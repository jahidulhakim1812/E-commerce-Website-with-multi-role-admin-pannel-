<?php
require_once 'includes/core.php';
$done = $_GET['done'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $coupon = !empty($_SESSION['coupon']) ? find_coupon($_SESSION['coupon']) : null;
    $name = trim($_POST['name'] ?? ''); $phone = trim($_POST['phone'] ?? ''); $addr = trim($_POST['address'] ?? ''); $city = trim($_POST['city'] ?? ''); $email = trim($_POST['email'] ?? '');
    $zone = in_array($_POST['zone'] ?? '', ['inside', 'outside']) ? $_POST['zone'] : '';
    $pm = in_array($_POST['payment'] ?? '', explode(',', S('payment_methods'))) ? $_POST['payment'] : 'cod';
    $t = cart_totals($coupon, $zone);
    if (!$t['lines']) redirect('cart.php');
    cv_touch(['name' => $name, 'phone' => $phone, 'email' => $email, 'address' => $addr]);
    if (!$name || !$phone || !$addr || ($email && !filter_var($email, FILTER_VALIDATE_EMAIL))) flash(t('required'), 'err');
    elseif (!$zone) flash(t('zone_required'), 'err');
    else {
        $pdo = db(); $pdo->beginTransaction();
        try {
            foreach ($t['lines'] as $l) {
                $r = $l['v'] ? row('SELECT stock FROM product_variants WHERE id=? FOR UPDATE', [$l['v']['id']]) : row('SELECT stock FROM products WHERE id=? FOR UPDATE', [$l['p']['id']]);
                if (!$r || $r['stock'] < $l['qty']) throw new Exception(t('stock_short', $l['p']['name_en'] . ($l['v'] ? ' (' . $l['v']['label_en'] . ')' : '')));
            }
            $uid = ensure_customer($name, $phone, $email, $addr, $city);
            $no = 'DN' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
            q('INSERT INTO orders(order_no,user_id,name,phone,email,address,city,subtotal,shipping,discount,total,coupon,delivery_zone,payment_method,note) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [$no, $uid, $name, $phone, $email, $addr, $city, $t['subtotal'], $t['shipping'], $t['discount'], $t['total'], $coupon['code'] ?? null, $zone, $pm, trim($_POST['note'] ?? '')]);
            $oid = (int)$pdo->lastInsertId();
            foreach ($t['lines'] as $l) {
                $p = $l['p']; $v = $l['v'];
                q('INSERT INTO order_items(order_id,product_id,variant_id,variant,name,price,cost,qty,image) VALUES(?,?,?,?,?,?,?,?,?)', [$oid, $p['id'], $v['id'] ?? 0, $v['label_en'] ?? '', $p['name_en'], $l['price'], $l['cost'], $l['qty'], $p['image']]);
                if ($v) q('UPDATE product_variants SET stock=stock-? WHERE id=?', [$l['qty'], $v['id']]); else q('UPDATE products SET stock=stock-? WHERE id=?', [$l['qty'], $p['id']]);
                q('UPDATE products SET sold=sold+? WHERE id=?', [$l['qty'], $p['id']]);
            }
            foreach (array_unique(array_map(fn($l) => (int)$l['p']['id'], array_filter($t['lines'], fn($l) => $l['v']))) as $pid) sync_product_from_variants($pid);
            if ($coupon) q('UPDATE coupons SET used=used+1 WHERE id=?', [$coupon['id']]);
            q('INSERT INTO order_log(order_id,status,note) VALUES(?,?,?)', [$oid, 'pending', 'Order placed']);
            $pdo->commit();
            cv_complete($oid, $uid);
            unset($_SESSION['cart'], $_SESSION['coupon']);
            $mailSent = notify_order($oid);
            $smsSent = send_order_sms($oid);
            if (!$mailSent && $email) flash(S('mail_enabled') === '1' ? 'Your order was placed, but the invoice email could not be sent. Please contact us if you need a copy.' : 'Your order was placed, but invoice emails are disabled. Please contact us if you need a copy.', 'err');
            if (S('sms_enabled') === '1' && !$smsSent) flash('Your order was placed, but the SMS confirmation could not be sent. Please contact us if you need a copy of the confirmation.', 'err');
            redirect('checkout.php?done=' . $no);
        } catch (Exception $ex) { if ($pdo->inTransaction()) $pdo->rollBack(); flash($ex->getMessage(), 'err'); }
    }
}
$page_title = t('checkout'); require 'includes/header.php';
if ($done): $o = row('SELECT * FROM orders WHERE order_no=?', [$done]); ?>
<div class="container sec center"><div class="success card-flat"><div class="big-ic">🎉</div><h1><?= t('order_placed') ?></h1><p><?= t('order_thanks') ?></p>
 <p><?= t('order_number') ?>: <b class="mono"><?= e($done) ?></b></p><div class="btn-row center"><a class="btn" href="<?= url('track.php?order=' . e($done) . '&phone=' . urlencode($o['phone'] ?? '')) ?>"><?= t('track_order') ?></a><a class="btn btn-ghost" href="<?= url('shop.php') ?>"><?= t('continue_shopping') ?></a></div></div></div>
<?php require 'includes/footer.php'; exit; endif;
$coupon = !empty($_SESSION['coupon']) ? find_coupon($_SESSION['coupon']) : null; $zone = in_array($_POST['zone'] ?? '', ['inside', 'outside']) ? $_POST['zone'] : ''; $t = cart_totals($coupon, $zone);
if (!$t['lines']) redirect('cart.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') cv_touch();
$u = user(); $v = fn($k, $d = '') => e($_POST[$k] ?? ($u[$k] ?? $d)); $after = $t['subtotal'] - $t['discount']; $free = (float)S('free_shipping_min', 0); $isFree = $free > 0 && $after >= $free;
foreach (flash() as $f) echo '<div class="container"><div class="alert ' . $f[0] . '">' . e($f[1]) . '</div></div>';
?><div class="container sec"><h1 class="page-title"><?= t('checkout') ?></h1>
<form method="post" class="cart-wrap" id="coForm"><?= csrf_field() ?>
 <div class="card-flat form"><h3><?= t('delivery_info') ?></h3>
  <div class="two"><label><?= t('name') ?> *<input name="name" required value="<?= $v('name') ?>"></label><label><?= t('phone') ?> *<input name="phone" required value="<?= $v('phone') ?>"></label></div>
  <label><?= t('email') ?><input type="email" name="email" value="<?= $v('email') ?>"></label>
  <div class="two"><label><?= t('address') ?> *<input name="address" required value="<?= $v('address') ?>"></label><label><?= t('city') ?><input name="city" value="<?= $v('city') ?>"></label></div>
  <h3><?= t('delivery_area') ?> *</h3><div class="zones">
   <label class="zone"><input type="radio" name="zone" value="inside" required <?= $zone === 'inside' ? 'checked' : '' ?>><span><b><?= t('inside_dhaka') ?></b><small><?= $isFree ? t('delivery_free') : money(S('shipping_inside', 60)) ?></small></span></label>
   <label class="zone"><input type="radio" name="zone" value="outside" <?= $zone === 'outside' ? 'checked' : '' ?>><span><b><?= t('outside_dhaka') ?></b><small><?= $isFree ? t('delivery_free') : money(S('shipping_outside', 120)) ?></small></span></label></div>
  <h3><?= t('payment_method') ?></h3><div class="pm"><?php foreach (array_filter(explode(',', S('payment_methods'))) as $i => $m): ?><label class="pm-o"><input type="radio" name="payment" value="<?= e($m) ?>" <?= $i === 0 ? 'checked' : '' ?>> <?= t(trim($m)) ?></label><?php endforeach; ?></div>
  <small class="muted"><?= e(sx('payment_note')) ?></small>
  <label><?= t('order_note') ?><textarea name="note" rows="3"><?= $v('note') ?></textarea></label><small class="muted">🔒 <?= t('save_note') ?></small></div>
 <aside class="card-flat sum" id="coSum" data-sub="<?= $t['subtotal'] ?>" data-disc="<?= $t['discount'] ?>" data-in="<?= (float)S('shipping_inside', 60) ?>" data-out="<?= (float)S('shipping_outside', 120) ?>" data-free="<?= $free ?>">
  <h3><?= t('items') ?></h3><?php foreach ($t['lines'] as $l) echo '<div class="srow"><span>' . e(tr($l['p'], 'name')) . ($l['label'] ? ' <span class="vtag">' . e($l['label']) . '</span>' : '') . ' × ' . $l['qty'] . '</span><b>' . money($l['line']) . '</b></div>'; ?>
  <hr><div class="srow"><span><?= t('subtotal') ?></span><b><?= money($t['subtotal']) ?></b></div><div class="srow"><span><?= t('discount') ?></span><b>-<?= money($t['discount']) ?></b></div>
  <div class="srow"><span><?= t('shipping') ?></span><b id="coShip"><?= $zone ? ($t['shipping'] ? money($t['shipping']) : t('free')) : '—' ?></b></div>
  <div class="srow big"><span><?= t('total') ?></span><b id="coTot"><?= $zone ? money($t['total']) : money($t['total']) . ' + ' . t('shipping') ?></b></div>
  <button class="btn btn-block"><?= t('place_order') ?></button></aside></form></div>
<?php require 'includes/footer.php';
