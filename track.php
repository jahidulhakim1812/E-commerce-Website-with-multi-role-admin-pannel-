<?php
require_once __DIR__ . "/includes/core.php";
$page_title = t('track_title'); require 'includes/header.php';
$no = trim($_GET['order'] ?? ''); $ph = trim($_GET['phone'] ?? ''); $o = null; $miss = false;
if ($no) { $o = row('SELECT * FROM orders WHERE order_no=?', [$no]); $okUser = $o && user() && $o['user_id'] == user()['id']; if ($o && !$okUser && preg_replace('/\D/', '', $o['phone']) !== preg_replace('/\D/', '', $ph)) { $o = null; } $miss = !$o; }
?><div class="container sec"><h1 class="page-title">📦 <?= t('track_title') ?></h1>
<form class="card-flat form track-f"><input name="order" placeholder="<?= t('order_no') ?> (DN...)" value="<?= e($no) ?>" required><input name="phone" placeholder="<?= t('phone') ?>" value="<?= e($ph) ?>"><button class="btn"><?= t('track') ?></button></form>
<?php if ($miss) echo '<div class="alert err">' . t('not_found') . '</div>'; if ($o) echo order_detail_html($o); ?></div>
<?php require 'includes/footer.php';
