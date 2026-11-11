<?php
require_once 'includes/core.php'; $sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_check(); if (trim($_POST['message'] ?? '') && trim($_POST['name'] ?? '')) { q('INSERT INTO contact_messages(name,email,phone,message) VALUES(?,?,?,?)', [trim($_POST['name']), trim($_POST['email'] ?? ''), trim($_POST['phone'] ?? ''), trim($_POST['message'])]); @send_mail(S('admin_email'), 'New contact message from ' . $_POST['name'], nl2br(e($_POST['message'])) . '<br>' . e($_POST['email'] ?? '') . ' ' . e($_POST['phone'] ?? '')); $sent = true; } }
$page_title = t('contact_us'); require 'includes/header.php'; ?>
<div class="container sec"><h1 class="page-title"><?= t('contact_us') ?></h1><div class="two">
<div class="card-flat"><p>📞 <?= e(S('phone')) ?></p><p>✉️ <?= e(S('email')) ?></p><p>📍 <?= e(sx('address')) ?></p></div>
<form method="post" class="card-flat form"><?= csrf_field() ?><?php if ($sent) echo '<div class="alert ok">' . t('message_sent') . '</div>'; ?><label><?= t('name') ?> *<input name="name" required></label><div class="two"><label><?= t('email') ?><input type="email" name="email"></label><label><?= t('phone') ?><input name="phone"></label></div><label><?= t('message') ?> *<textarea name="message" rows="5" required></textarea></label><button class="btn"><?= t('send_message') ?></button></form></div></div>
<?php require 'includes/footer.php';
