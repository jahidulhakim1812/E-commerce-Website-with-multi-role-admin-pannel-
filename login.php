<?php
require_once 'includes/core.php';
if (user()) redirect(is_staff() ? 'admin/' : 'account.php');
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (login_locked()) $err = LANG === 'bn' ? 'অনেকবার ভুল চেষ্টা। ১৫ মিনিট পরে আবার চেষ্টা করুন।' : 'Too many failed attempts. Please try again in 15 minutes.';
    else { $u = row('SELECT * FROM users WHERE email=? AND status=1', [trim($_POST['email'] ?? '')]);
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) { login_ok(); login_user($u); $b = $_SESSION['back'] ?? ''; unset($_SESSION['back']); redirect(in_array($u['role'], ['admin', 'manager']) ? 'admin/' : ($b ?: 'account.php')); }
    login_fail(); $err = t('invalid_login'); }
}
$page_title = t('sign_in'); $auth_page = true; require 'includes/header.php'; ?>
<div class="container sec auth-page">
<div class="auth auth-login card-flat">
    <div class="auth-brand">
        <div class="auth-logo"><?= logo_html('header') ?></div>
        <span class="auth-kicker"><?= e(S('site_name')) ?></span>
        <h2><?= t('auth_welcome') ?></h2>
        <p><?= t('auth_welcome_text') ?></p>
        <span class="auth-decoration" aria-hidden="true"></span>
    </div>
    <div class="auth-content">
        <h1><?= t('login_title') ?></h1>
        <p class="auth-intro"><?= t('auth_signin_text') ?></p>
        <?php if ($err) echo '<div class="alert err">' . e($err) . '</div>'; ?>
        <form method="post" class="form auth-form">
            <?= csrf_field() ?>
            <label><?= t('email') ?><input type="email" name="email" autocomplete="email" required autofocus value="<?= e($_POST['email'] ?? '') ?>"></label>
            <label><?= t('password') ?><input type="password" name="password" autocomplete="current-password" required></label>
            <a class="auth-forgot" href="<?= url('forgot_password.php') ?>"><?= t('forgot_password') ?></a>
            <button class="btn btn-block"><?= t('sign_in') ?></button>
        </form>
        <p class="auth-register"><?= t('no_account') ?> <a href="<?= url('register.php') ?>"><?= t('register') ?></a></p>
    </div>
</div>
</div>
<?php require 'includes/footer.php';
