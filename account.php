<?php
require_once 'includes/core.php'; require_login(); $u = user();
if ($u['role'] !== 'customer') { /* staff may still view their own customer page */ }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (isset($_POST['profile'])) { $av = !empty($_POST['rm_avatar']) ? '' : upload_image('avatar', $u['avatar'] ?? ''); q('UPDATE users SET name=?,phone=?,address=?,city=?,avatar=? WHERE id=?', [trim($_POST['name']), trim($_POST['phone']), trim($_POST['address']), trim($_POST['city']), $av, $u['id']]); flash(t('saved')); }
    if (isset($_POST['pass'])) { if (!password_verify($_POST['cur'] ?? '', $u['password'])) flash(t('wrong_password'), 'err'); elseif (strlen($_POST['new'] ?? '') < 6) flash(t('required'), 'err'); else { q('UPDATE users SET password=? WHERE id=?', [password_hash($_POST['new'], PASSWORD_DEFAULT), $u['id']]); flash(t('saved')); } }
    redirect('account.php?tab=profile');
}
$tab = $_GET['tab'] ?? 'orders'; $page_title = t('my_account'); require 'includes/header.php';
$orders = rows('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC', [$u['id']]);
$spent = array_sum(array_map(fn($o) => $o['status'] !== 'cancelled' ? $o['total'] : 0, $orders));
?><div class="container sec"><div class="acc-head"><?= user_avatar($u) ?><div><h1 class="page-title" style="margin:0"><?= e($u['name']) ?></h1><span class="muted"><?= e($u['email']) ?></span></div></div>
<?php foreach (flash() as $f) echo '<div class="alert ' . $f[0] . '">' . e($f[1]) . '</div>'; ?>
<div class="stats3"><div class="stat card3d"><span>📦</span><b><?= count($orders) ?></b><small><?= t('my_orders') ?></small></div><div class="stat card3d"><span>💰</span><b><?= money($spent) ?></b><small><?= t('total') ?></small></div><div class="stat card3d"><span>🚚</span><b><?= count(array_filter($orders, fn($o) => in_array($o['status'], ['confirmed', 'processing', 'shipped']))) ?></b><small><?= t('s_shipped') ?></small></div></div>
<div class="tab-h acc"><a class="<?= $tab === 'orders' ? 'on' : '' ?>" href="?tab=orders"><?= t('my_orders') ?></a><a class="<?= $tab === 'profile' ? 'on' : '' ?>" href="?tab=profile"><?= t('profile') ?></a><a href="<?= url('return.php') ?>"><?= t('return_title') ?></a></div>
<?php if ($tab === 'orders') {
  if (!empty($_GET['view'])) { $o = row('SELECT * FROM orders WHERE order_no=? AND user_id=?', [$_GET['view'], $u['id']]); if ($o) echo order_detail_html($o); echo '<a class="btn btn-ghost" href="?tab=orders">←</a>'; }
  elseif (!$orders) { echo '<p class="empty card-flat">' . t('cart_empty') . '</p>'; }
  else { ?>
  <div class="card-flat table-wrap"><table class="tbl"><tr><th><?= t('order_no') ?></th><th><?= t('date') ?></th><th><?= t('total') ?></th><th><?= t('status') ?></th><th></th></tr>
  <?php foreach ($orders as $o) echo '<tr><td class="mono">' . e($o['order_no']) . '</td><td>' . date('d M Y', strtotime($o['created_at'])) . '</td><td>' . money($o['total']) . '</td><td><span class="pill s-' . $o['status'] . '">' . status_label($o['status']) . '</span></td><td><a class="btn btn-sm" href="?tab=orders&view=' . e($o['order_no']) . '">' . t('track') . '</a> <a class="btn btn-sm btn-ghost" target="_blank" href="' . url('invoice.php?no=' . e($o['order_no'])) . '">' . t('invoice') . '</a></td></tr>'; ?></table></div><?php }
} else { ?><div class="two"><form method="post" enctype="multipart/form-data" class="card-flat form"><?= csrf_field() ?><input type="hidden" name="profile" value="1"><h3><?= t('profile') ?></h3><div class="avatar-up"><?= user_avatar($u) ?><div><b><?= t('profile_photo') ?></b><input type="file" name="avatar" accept="image/*"><?php if (has_img($u['avatar'] ?? '')): ?><label class="chk"><input type="checkbox" name="rm_avatar" value="1"> <?= t('remove') ?></label><?php endif; ?></div></div><label><?= t('name') ?><input name="name" required value="<?= e($u['name']) ?>"></label><label><?= t('email') ?><input value="<?= e($u['email']) ?>" disabled></label><label><?= t('phone') ?><input name="phone" value="<?= e($u['phone']) ?>"></label><label><?= t('address') ?><input name="address" value="<?= e($u['address']) ?>"></label><label><?= t('city') ?><input name="city" value="<?= e($u['city']) ?>"></label><button class="btn"><?= t('save') ?></button></form>
 <form method="post" class="card-flat form"><?= csrf_field() ?><input type="hidden" name="pass" value="1"><h3><?= t('password') ?></h3><label><?= t('current_password') ?><input type="password" name="cur" required></label><label><?= t('new_password') ?><input type="password" name="new" required minlength="6"></label><button class="btn"><?= t('save') ?></button></form></div><?php } ?></div>
<?php require 'includes/footer.php';
