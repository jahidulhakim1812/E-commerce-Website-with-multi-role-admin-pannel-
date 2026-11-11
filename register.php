<?php
require_once 'includes/core.php';
if (user()) redirect('account.php');
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $n = trim($_POST['name'] ?? ''); $em = trim($_POST['email'] ?? ''); $pw = $_POST['password'] ?? '';
    if (!$n || !filter_var($em, FILTER_VALIDATE_EMAIL) || strlen($pw) < 6) $err = t('required') . ' (min 6)';
    elseif ($ex = row('SELECT * FROM users WHERE email=?', [$em])) { if ($ex['is_guest'] && $ex['role'] === 'customer') { q('UPDATE users SET name=?, phone=IF(?<>\'\',?,phone), password=?, is_guest=0 WHERE id=?', [$n, trim($_POST['phone'] ?? ''), trim($_POST['phone'] ?? ''), password_hash($pw, PASSWORD_DEFAULT), $ex['id']]); login_user(row('SELECT * FROM users WHERE id=?', [$ex['id']])); redirect('account.php'); } else $err = t('email_exists'); }
    else { q('INSERT INTO users(name,email,phone,password,role) VALUES(?,?,?,?,?)', [$n, $em, trim($_POST['phone'] ?? ''), password_hash($pw, PASSWORD_DEFAULT), 'customer']); login_user(row('SELECT * FROM users WHERE email=?', [$em])); $back = $_SESSION['back'] ?? ''; unset($_SESSION['back']); redirect($back ?: 'account.php'); }
}
$page_title = t('register'); require 'includes/header.php'; ?>
<div class="container sec"><div class="auth card-flat card3d"><div class="auth-logo"><?= logo_html('header') ?></div><h1><?= t('register_title') ?></h1><?php if ($err) echo '<div class="alert err">' . e($err) . '</div>'; ?>
<form method="post" class="form"><?= csrf_field() ?><label><?= t('name') ?><input name="name" required value="<?= e($_POST['name'] ?? '') ?>"></label><label><?= t('email') ?><input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label><label><?= t('phone') ?><input name="phone" value="<?= e($_POST['phone'] ?? '') ?>"></label><label><?= t('password') ?><input type="password" name="password" required minlength="6"></label><button class="btn btn-block"><?= t('register') ?></button></form>
<p class="center"><?= t('have_account') ?> <a href="<?= url('login.php') ?>"><?= t('sign_in') ?></a></p></div></div>
<?php require 'includes/footer.php';
