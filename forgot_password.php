<?php
require_once 'includes/core.php';
if (user()) redirect(is_staff() ? 'admin/' : 'account.php');

db()->exec("CREATE TABLE IF NOT EXISTS password_reset_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    token_hash CHAR(64) NULL,
    request_ip VARCHAR(45) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NULL,
    KEY token_hash (token_hash),
    KEY request_ip_created (request_ip, created_at),
    KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$token = trim($_GET['token'] ?? '');
$reset = $token !== '' && preg_match('/^[a-f0-9]{64}$/i', $token);
$err = '';
$message = '';
$resetRecord = null;

if ($reset) {
    $resetRecord = row('SELECT id, user_id FROM password_reset_requests WHERE token_hash=? AND expires_at>NOW() AND user_id IS NOT NULL', [hash('sha256', $token)]);
    if (!$resetRecord) $err = t('reset_link_invalid');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (isset($_POST['reset_token'])) {
        $token = trim($_POST['reset_token']);
        $resetRecord = preg_match('/^[a-f0-9]{64}$/i', $token)
            ? row('SELECT id, user_id FROM password_reset_requests WHERE token_hash=? AND expires_at>NOW() AND user_id IS NOT NULL', [hash('sha256', $token)])
            : null;
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!$resetRecord) $err = t('reset_link_invalid');
        elseif (strlen($password) < 6) $err = t('password_minimum');
        elseif ($password !== $confirm) $err = t('passwords_mismatch');
        else {
            db()->beginTransaction();
            $locked = row('SELECT id, user_id FROM password_reset_requests WHERE id=? AND token_hash=? AND expires_at>NOW() AND user_id IS NOT NULL FOR UPDATE', [$resetRecord['id'], hash('sha256', $token)]);
            if (!$locked) {
                db()->rollBack();
                $err = t('reset_link_invalid');
            } else {
                q('UPDATE users SET password=? WHERE id=? AND status=1', [password_hash($password, PASSWORD_DEFAULT), $locked['user_id']]);
                q('UPDATE password_reset_requests SET expires_at=NOW() WHERE user_id=? AND expires_at>NOW()', [$locked['user_id']]);
                db()->commit();
                $message = t('password_reset_success');
                $resetRecord = null;
            }
        }
    } else {
        $email = trim($_POST['email'] ?? '');
        $ip = client_ip();
        $recent = (int)row('SELECT COUNT(*) AS total FROM password_reset_requests WHERE request_ip=? AND created_at>=DATE_SUB(NOW(), INTERVAL 1 HOUR)', [$ip])['total'];
        if ($recent < 5) {
            q('DELETE FROM password_reset_requests WHERE created_at<DATE_SUB(NOW(), INTERVAL 1 DAY)');
            $account = filter_var($email, FILTER_VALIDATE_EMAIL)
                ? row('SELECT id, email FROM users WHERE email=? AND status=1', [$email])
                : null;
            $rawToken = $account ? bin2hex(random_bytes(32)) : null;
            if ($account) q('UPDATE password_reset_requests SET expires_at=NOW() WHERE user_id=? AND expires_at>NOW()', [$account['id']]);
            if ($account) {
                q('INSERT INTO password_reset_requests(user_id, token_hash, request_ip, expires_at) VALUES(?,?,?,DATE_ADD(NOW(), INTERVAL 1 HOUR))', [$account['id'], hash('sha256', $rawToken), $ip]);
            } else {
                q('INSERT INTO password_reset_requests(user_id, token_hash, request_ip, expires_at) VALUES(NULL,NULL,?,NULL)', [$ip]);
            }
            if ($account) {
                $siteOrigin = rtrim(trim(S('site_url')), '/');
                $siteParts = $siteOrigin !== '' ? parse_url($siteOrigin) : false;
                $validSiteOrigin = $siteParts
                    && in_array(strtolower($siteParts['scheme'] ?? ''), ['http', 'https'], true)
                    && !empty($siteParts['host'])
                    && empty($siteParts['user'])
                    && empty($siteParts['pass'])
                    && empty($siteParts['query'])
                    && empty($siteParts['fragment'])
                    && empty($siteParts['path']);
                if (!$validSiteOrigin) {
                    q('UPDATE password_reset_requests SET expires_at=NOW() WHERE token_hash=?', [hash('sha256', $rawToken)]);
                    error_log('Password reset email was not sent; configure the canonical website URL in Admin > Settings > General.');
                } else {
                    $resetUrl = $siteOrigin . url('forgot_password.php?token=' . rawurlencode($rawToken));
                    $html = '<h2>' . e(t('password_reset_email_title')) . '</h2><p>' . e(t('password_reset_email_text')) . '</p><p><a href="' . e($resetUrl) . '">' . e(t('reset_password')) . '</a></p><p>' . e(t('reset_link_expiry')) . '</p>';
                    if (!send_mail($account['email'], S('site_name') . ' - ' . t('reset_password'), $html)) {
                        q('UPDATE password_reset_requests SET expires_at=NOW() WHERE token_hash=?', [hash('sha256', $rawToken)]);
                        error_log('Password reset email was not delivered; check Admin > Settings > Email (SMTP).');
                    }
                }
            }
        }
        $message = t('reset_request_sent');
    }
}

$page_title = t('forgot_password');
$auth_page = true;
require 'includes/header.php'; ?>
<div class="container sec auth-page">
    <div class="auth card-flat auth-single">
        <div class="auth-content">
            <div class="auth-logo"><?= logo_html('header') ?></div>
            <?php if ($resetRecord): ?>
                <h1><?= t('reset_password') ?></h1>
                <p class="auth-intro"><?= t('choose_new_password') ?></p>
                <?php if ($err) echo '<div class="alert err">' . e($err) . '</div>'; ?>
                <form method="post" class="form auth-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="reset_token" value="<?= e($token) ?>">
                    <label><?= t('new_password') ?><input type="password" name="password" autocomplete="new-password" minlength="6" required></label>
                    <label><?= t('confirm_password') ?><input type="password" name="confirm_password" autocomplete="new-password" minlength="6" required></label>
                    <button class="btn btn-block"><?= t('reset_password') ?></button>
                </form>
            <?php else: ?>
                <h1><?= t('forgot_password') ?></h1>
                <p class="auth-intro"><?= t('forgot_password_text') ?></p>
                <?php if ($err) echo '<div class="alert err">' . e($err) . '</div>'; ?>
                <?php if ($message) echo '<div class="alert ok">' . e($message) . '</div>'; ?>
                <?php if (!$message): ?>
                    <form method="post" class="form auth-form">
                        <?= csrf_field() ?>
                        <label><?= t('email') ?><input type="email" name="email" autocomplete="email" required autofocus></label>
                        <button class="btn btn-block"><?= t('send_reset_link') ?></button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
            <p class="auth-register"><a href="<?= url('login.php') ?>"><?= t('back_to_login') ?></a></p>
        </div>
    </div>
</div>
<?php require 'includes/footer.php';
