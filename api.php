<?php
require_once 'includes/core.php';
$act = $_REQUEST['a'] ?? '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false], 405);
csrf_check();
function cur_coupon() { return !empty($_SESSION['coupon']) ? find_coupon($_SESSION['coupon']) : null; }
function cart_json($extra = []) { $t = cart_totals(cur_coupon()); return array_merge(['ok' => true, 'count' => cart_count(), 'subtotal' => $t['subtotal'], 'total' => $t['total'], 'totf' => money($t['total'])], $extra); }
function valid_key($k) { return preg_match('/^\d+:\d+$/', (string)$k) ? $k : null; }
switch ($act) {
case 'add':
    $id = (int)($_POST['id'] ?? 0); $vid = (int)($_POST['vid'] ?? 0); $qty = max(1, (int)($_POST['qty'] ?? 1));
    $p = row('SELECT id,stock FROM products WHERE id=? AND status=1', [$id]); if (!$p) json_out(['ok' => false, 'msg' => t('out_of_stock')]);
    $vs = variants_of($id); $stock = (int)$p['stock'];
    if ($vs) { $v = null; foreach ($vs as $x) if ((int)$x['id'] === $vid) $v = $x; if (!$v) $v = $vs[0]; /* nothing chosen -> lowest quantity is selected automatically */ $vid = (int)$v['id']; $stock = (int)$v['stock']; } else $vid = 0;
    if ($stock < 1) json_out(['ok' => false, 'msg' => t('out_of_stock')]);
    $k = $id . ':' . $vid; $cart = cart(); $_SESSION['cart'] = $cart; $_SESSION['cart'][$k] = min($stock, ($cart[$k] ?? 0) + $qty);
    json_out(cart_json(['msg' => t('added_cart')]));
case 'set':
    $k = valid_key($_POST['key'] ?? ''); if (!$k) json_out(['ok' => false], 400); $qty = (int)($_POST['qty'] ?? 0); $_SESSION['cart'] = cart();
    if ($qty < 1) unset($_SESSION['cart'][$k]); else $_SESSION['cart'][$k] = $qty;
    $t = cart_totals(cur_coupon()); $lines = []; foreach ($t['lines'] as $l) $lines[$l['key']] = money($l['line']);
    json_out(cart_json(['lines' => $lines, 'sub' => money($t['subtotal']), 'disc' => money($t['discount']), 'tot' => money($t['total'])]));
case 'mini':
    $t = cart_totals(cur_coupon()); $lines = [];
    foreach ($t['lines'] as $l) { $p = $l['p']; $lines[] = ['key' => $l['key'], 'name' => tr($p, 'name'), 'label' => $l['label'], 'url' => url('product.php?slug=' . $p['slug']), 'img' => has_img($p['image']) ? img($p['image']) : '', 'icon' => cat_icon($p['category_id']), 'price' => money($l['price']), 'qty' => $l['qty'], 'max' => (int)$l['stock'], 'line' => money($l['line'])]; }
    $free = (float)S('free_shipping_min', 0); $after = $t['subtotal'] - $t['discount']; $need = $free > 0 ? max(0, $free - $after) : 0;
    json_out(['ok' => true, 'count' => cart_count(), 'lines' => $lines, 'sub' => money($t['subtotal']), 'total' => money($t['total']), 'free' => $free > 0, 'need' => $need > 0 ? money($need) : '', 'pct' => $free > 0 ? (int)min(100, round($after / $free * 100)) : 0]);
case 'notifs':
    $rs = rows("SELECT * FROM notifications WHERE status=1 AND created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) ORDER BY id DESC LIMIT 15"); $items = []; $max = 0;
    foreach ($rs as $n) { $max = max($max, (int)$n['id']); $items[] = ['id' => (int)$n['id'], 'type' => $n['type'], 'title' => tr($n, 'title'), 'body' => tr($n, 'body'), 'url' => $n['link'] ? (preg_match('#^https?://#', $n['link']) ? $n['link'] : url($n['link'])) : '', 'img' => has_img($n['image']) ? img($n['image']) : '', 'date' => date('d M, h:i A', strtotime($n['created_at']))]; }
    json_out(['ok' => true, 'items' => $items, 'max' => $max]);
case 'push-subscribe':
    if (S('push_enabled') !== '1' || !S('push_public_key')) json_out(['ok' => false, 'msg' => 'Browser push is not enabled on this website.'], 403);
    $subscription = json_decode((string)($_POST['subscription'] ?? ''), true);
    if (!push_subscribe($subscription)) json_out(['ok' => false, 'msg' => 'Could not save this browser notification subscription.'], 400);
    json_out(['ok' => true]);
case 'push-unsubscribe':
    $subscription = json_decode((string)($_POST['subscription'] ?? ''), true);
    $endpoint = is_array($subscription) ? ($subscription['endpoint'] ?? '') : '';
    if (!push_unsubscribe($endpoint)) json_out(['ok' => false, 'msg' => 'Could not remove this browser notification subscription.'], 400);
    json_out(['ok' => true]);
case 'wish':
    $id = (int)$_POST['id']; $w = $_SESSION['wish'] ?? []; $on = !in_array($id, $w); $_SESSION['wish'] = $on ? array_merge($w, [$id]) : array_values(array_diff($w, [$id])); json_out(['ok' => true, 'on' => $on]);
case 'newsletter':
    $em = strtolower(trim($_POST['email'] ?? '')); if (!filter_var($em, FILTER_VALIDATE_EMAIL)) json_out(['ok' => false, 'msg' => LANG === 'bn' ? 'সঠিক ইমেইল দিন' : 'Please enter a valid email']);
    q("INSERT INTO newsletter(email,token,status,source) VALUES(?,?,1,'site') ON DUPLICATE KEY UPDATE status=1", [$em, substr(bin2hex(random_bytes(12)), 0, 24)]); json_out(['ok' => true, 'msg' => t('subscribed')]);
case 'coupon':
    $c = find_coupon($_POST['code'] ?? ''); $_SESSION['coupon'] = $c ? $c['code'] : null;
    if (!$c) json_out(['ok' => false, 'msg' => t('coupon_invalid')]);
    $t = cart_totals($c); json_out(['ok' => true, 'msg' => t('coupon_ok'), 'disc' => money($t['discount']), 'tot' => money($t['total'])]);
case 'cv':
    cv_touch(['name' => $_POST['name'] ?? '', 'phone' => $_POST['phone'] ?? '', 'email' => $_POST['email'] ?? '', 'address' => $_POST['address'] ?? '']); json_out(['ok' => true]);
}
json_out(['ok' => false], 400);
