<?php
require_once 'includes/core.php';
if (user()) redirect(is_staff() ? 'admin/' : 'account.php');

$email = trim($_SESSION['password_reset_email'] ?? '');
$codeSent = !empty($_SESSION['password_reset_code_sent']);
$err = '';
$message = '';

if (!$email || !$codeSent) {
    redirect('forgot_password.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $code = preg_replace('/[^0-9]/', '', trim($_POST['verification_code'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!preg_match('/^[0-9]{6}$/', $code)) {
        $err = 'Enter the 6-digit verification code.';
    } elseif (strlen($password) < 6) {
        $err = t('password_minimum');
    } elseif ($password !== $confirm) {
        $err = t('passwords_mismatch');
    } else {
        $account = row('SELECT id FROM users WHERE email=? AND status=1', [$email]);
        $request = $account
            ? row('SELECT id, user_id FROM password_reset_requests WHERE user_id=? AND code_hash=? AND expires_at>NOW() AND code_hash IS NOT NULL', [$account['id'], hash('sha256', $code)])
            : null;

        if (!$request) {
            $err = 'The verification code is invalid or has expired.';
        } else {
            db()->beginTransaction();
            $locked = row('SELECT id, user_id FROM password_reset_requests WHERE id=? AND code_hash=? AND expires_at>NOW() AND user_id IS NOT NULL FOR UPDATE', [$request['id'], hash('sha256', $code)]);
            if (!$locked) {
                db()->rollBack();
                $err = 'The verification code is invalid or has expired.';
            } else {
                q('UPDATE users SET password=? WHERE id=? AND status=1', [password_hash($password, PASSWORD_DEFAULT), $locked['user_id']]);
                q('UPDATE password_reset_requests SET expires_at=NOW() WHERE user_id=? AND expires_at>NOW()', [$locked['user_id']]);
                db()->commit();
                unset($_SESSION['password_reset_email'], $_SESSION['password_reset_code_sent'], $_SESSION['password_reset_verified_request']);
                $message = t('password_reset_success');
            }
        }
    }
}

$page_title = 'Verify reset code';
$auth_page = true;
require 'includes/header.php';
?>
<div class="container sec auth-page">
    <div class="auth card-flat auth-single">
        <div class="auth-content">
            <div class="auth-logo"><?= logo_html('header') ?></div>
            <?php if ($message): ?>
                <h1>Password reset successful</h1>
                <div class="alert ok"><?= e($message) ?></div>
                <p class="auth-register"><a href="<?= url('login.php') ?>">Return to sign in</a></p>
            <?php else: ?>
                <h1>Verify and reset password</h1>
                <p class="auth-intro">Enter the 6-digit code sent to <?= e($email) ?>, then choose a new password. The code expires in 1 hour.</p>
                <?php if ($err) echo '<div class="alert err">' . e($err) . '</div>'; ?>
                <form method="post" class="form auth-form">
                    <?= csrf_field() ?>
                    <label>Verification code
                        <input type="text" name="verification_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
                    </label>
                    <label><?= t('new_password') ?>
                        <input type="password" name="password" autocomplete="new-password" minlength="6" required>
                    </label>
                    <label><?= t('confirm_password') ?>
                        <input type="password" name="confirm_password" autocomplete="new-password" minlength="6" required>
                    </label>
                    <button class="btn btn-block" type="submit">Verify code and reset password</button>
                </form>
            <?php endif; ?>
            <p class="auth-register"><a href="<?= url('login.php') ?>"><?= t('back_to_login') ?></a></p>
        </div>
    </div>
</div>
<?php require 'includes/footer.php';
