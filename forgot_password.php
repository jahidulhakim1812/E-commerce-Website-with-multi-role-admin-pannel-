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
try { db()->exec("ALTER TABLE password_reset_requests ADD COLUMN IF NOT EXISTS code_hash CHAR(64) NULL AFTER token_hash"); } catch (Throwable $e) {
    try { db()->exec("ALTER TABLE password_reset_requests ADD COLUMN code_hash CHAR(64) NULL AFTER token_hash"); } catch (Throwable $e2) {}
}

$token = trim($_GET['token'] ?? '');
$reset = $token !== '' && preg_match('/^[a-f0-9]{64}$/i', $token);
$err = '';
$message = '';
$resetRecord = null;
if (empty($_SESSION['password_reset_email'])) $_SESSION['password_reset_email'] = '';

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
            if (!$locked) { db()->rollBack(); $err = t('reset_link_invalid'); }
            else {
                q('UPDATE users SET password=? WHERE id=? AND status=1', [password_hash($password, PASSWORD_DEFAULT), $locked['user_id']]);
                q('UPDATE password_reset_requests SET expires_at=NOW() WHERE user_id=? AND expires_at>NOW()', [$locked['user_id']]);
                db()->commit();
                $message = t('password_reset_success');
                $resetRecord = null;
            }
        }
    } elseif (isset($_POST['reset_password'])) {
        $requestId = (int)($_SESSION['password_reset_verified_request'] ?? 0);
        $resetRecord = $requestId
            ? row('SELECT id, user_id FROM password_reset_requests WHERE id=? AND expires_at>NOW() AND user_id IS NOT NULL', [$requestId])
            : null;
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (!$resetRecord) $err = t('reset_link_invalid');
        elseif (strlen($password) < 6) $err = t('password_minimum');
        elseif ($password !== $confirm) $err = t('passwords_mismatch');
        else {
            db()->beginTransaction();
            $locked = row('SELECT id, user_id FROM password_reset_requests WHERE id=? AND expires_at>NOW() AND user_id IS NOT NULL FOR UPDATE', [$resetRecord['id']]);
            if (!$locked) {
                db()->rollBack();
                $err = 'This verification code is invalid or has expired. Request a new code.';
            } else {
                q('UPDATE users SET password=? WHERE id=? AND status=1', [password_hash($password, PASSWORD_DEFAULT), $locked['user_id']]);
                q('UPDATE password_reset_requests SET expires_at=NOW() WHERE user_id=? AND expires_at>NOW()', [$locked['user_id']]);
                db()->commit();
                unset($_SESSION['password_reset_verified_request'], $_SESSION['password_reset_email']);
                $message = t('password_reset_success');
                $resetRecord = null;
            }
        }
    } elseif (isset($_POST['verify_code'])) {
        $email = trim($_POST['email'] ?? ($_SESSION['password_reset_email'] ?? ''));
        $code = preg_replace('/[^0-9]/', '', trim($_POST['verification_code'] ?? ''));
        $account = filter_var($email, FILTER_VALIDATE_EMAIL)
            ? row('SELECT id FROM users WHERE email=? AND status=1', [$email])
            : null;
        $verified = null;
        if ($account && preg_match('/^[0-9]{6}$/', $code)) {
            $verified = row('SELECT id FROM password_reset_requests WHERE user_id=? AND code_hash=? AND expires_at>NOW() AND code_hash IS NOT NULL', [$account['id'], hash('sha256', $code)]);
        }
        if (!$verified) {
            $err = 'The verification code is invalid or has expired.';
        } else {
            $_SESSION['password_reset_verified_request'] = (int)$verified['id'];
            $_SESSION['password_reset_email'] = $email;
        }
    } else {
        $email = trim($_POST['email'] ?? '');
        $ip = client_ip();
        $account = null;
        $recent = (int)row('SELECT COUNT(*) AS total FROM password_reset_requests WHERE request_ip=? AND created_at>=DATE_SUB(NOW(), INTERVAL 1 HOUR)', [$ip])['total'];
        if ($recent < 5) {
            q('DELETE FROM password_reset_requests WHERE created_at<DATE_SUB(NOW(), INTERVAL 1 DAY)');
            $account = filter_var($email, FILTER_VALIDATE_EMAIL)
                ? row('SELECT id, email FROM users WHERE email=? AND status=1', [$email])
                : null;
            $rawCode = $account ? str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT) : null;
            if ($account) q('UPDATE password_reset_requests SET expires_at=NOW() WHERE user_id=? AND expires_at>NOW()', [$account['id']]);
            if ($account) {
                q('INSERT INTO password_reset_requests(user_id, token_hash, code_hash, request_ip, expires_at) VALUES(?,?,?,?,DATE_ADD(NOW(), INTERVAL 1 HOUR))', [$account['id'], null, hash('sha256', $rawCode), $ip]);
                $_SESSION['password_reset_email'] = $account['email'];
                $_SESSION['password_reset_code_sent'] = true;
            } else {
                q('INSERT INTO password_reset_requests(user_id, token_hash, code_hash, request_ip, expires_at) VALUES(NULL,NULL,NULL,?,NULL)', [$ip]);
            }
            if ($account) {
                $html = '<h2>Password reset verification</h2><p>Use this 6-digit verification code to reset your password:</p><div style="font-size:28px;font-weight:bold;letter-spacing:8px">' . e($rawCode) . '</div><p>This code expires in 1 hour and can only be used once.</p>';
                if (!send_mail($account['email'], S('site_name') . ' - Password reset verification code', $html)) {
                    q('UPDATE password_reset_requests SET expires_at=NOW() WHERE code_hash=?', [hash('sha256', $rawCode)]);
                    unset($_SESSION['password_reset_email'], $_SESSION['password_reset_code_sent']);
                    $account = null;
                    error_log('Password reset verification email was not delivered; check Admin > Settings > Webmail.');
                } else {
                    redirect('forgot_password_verify.php');
                }
            }
        }
        $message = $account ? 'A 6-digit verification code has been sent to your email.' : t('reset_request_sent');
    }
}

$page_title = t('forgot_password');
$auth_page = true;
$verifiedRequestId = (int)($_SESSION['password_reset_verified_request'] ?? 0);
$codeEmail = $_SESSION['password_reset_email'] ?? '';
$codeWindow = $codeEmail !== '' && !$verifiedRequestId;
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
            <?php elseif ($verifiedRequestId): ?>
                <h1><?= t('reset_password') ?></h1>
                <p class="auth-intro"><?= t('choose_new_password') ?></p>
                <?php if ($err) echo '<div class="alert err">' . e($err) . '</div>'; ?>
                <form method="post" class="form auth-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="reset_password" value="1">
                    <label><?= t('new_password') ?><input type="password" name="password" autocomplete="new-password" minlength="6" required></label>
                    <label><?= t('confirm_password') ?><input type="password" name="confirm_password" autocomplete="new-password" minlength="6" required></label>
                    <button class="btn btn-block"><?= t('reset_password') ?></button>
                </form>
            <?php elseif ($codeEmail): ?>
                <h1>Enter verification code</h1>
                <p class="auth-intro">Enter the 6-digit code sent to <?= e($codeEmail) ?>. The code expires in 1 hour.</p>
                <?php if ($err) echo '<div class="alert err">' . e($err) . '</div>'; ?>
                <?php if ($message) echo '<div class="alert ok">' . e($message) . '</div>'; ?>
                <form method="post" class="form auth-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="email" value="<?= e($codeEmail) ?>">
                    <label>Verification code<input type="text" name="verification_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus></label>
                    <button class="btn btn-block" name="verify_code" value="1">Verify code</button>
                </form>
            <?php else: ?>
                <h1><?= t('forgot_password') ?></h1>
                <p class="auth-intro">Enter the email address linked to your account. We will send a 6-digit verification code.</p>
                <?php if ($err) echo '<div class="alert err">' . e($err) . '</div>'; ?>
                <?php if ($message) echo '<div class="alert ok">' . e($message) . '</div>'; ?>
                <?php if (!$message): ?>
                    <form method="post" class="form auth-form">
                        <?= csrf_field() ?>
                        <label><?= t('email') ?><input type="email" name="email" autocomplete="email" required autofocus></label>
                        <button class="btn btn-block">Send verification code</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
            <p class="auth-register"><a href="<?= url('login.php') ?>"><?= t('back_to_login') ?></a></p>
        </div>
    </div>
</div>
<?php require 'includes/footer.php';
