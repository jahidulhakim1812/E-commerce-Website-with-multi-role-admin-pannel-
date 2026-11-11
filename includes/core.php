<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/icons.php';
$composerAutoload = ROOT_PATH . '/vendor/autoload.php';
if (file_exists($composerAutoload)) require_once $composerAutoload;
if (!headers_sent()) { header('X-Content-Type-Options: nosniff'); header('X-Frame-Options: SAMEORIGIN'); header('Referrer-Policy: strict-origin-when-cross-origin'); header('Permissions-Policy: camera=(), microphone=(), geolocation=()'); }
if (session_status() === PHP_SESSION_NONE) { session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax']); session_start(); }

/* ---------- Database ---------- */
function db() { static $p; if (!$p) { $p = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]); } return $p; }
function q($sql, $a = []) { $s = db()->prepare($sql); $s->execute($a); return $s; }
function rows($sql, $a = []) { return q($sql, $a)->fetchAll(); }
function row($sql, $a = []) { $r = q($sql, $a)->fetch(); return $r ?: null; }
function val($sql, $a = []) { $r = q($sql, $a)->fetchColumn(); return $r === false ? null : $r; }

/* ---------- Base URL (works in any folder) ---------- */
function base_url() { static $b; if ($b === null) { $dr = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ''; $rp = realpath(ROOT_PATH); $b = str_replace('\\', '/', substr($rp, strlen($dr))); $b = rtrim($b, '/'); } return $b; }
function url($p = '') { return base_url() . '/' . ltrim($p, '/'); }
function full_url($p = '') { $s = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'; return $s . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url($p); }
function asset($p) { return url('assets/' . $p); }
function img($f) { return ($f && file_exists(UPLOAD_DIR . $f)) ? url('uploads/' . $f) : asset('img/placeholder.svg'); }
function redirect($p) {
    if (preg_match('#^(https?:)?//#', $p) || ($p !== '' && $p[0] === '/')) { header('Location: ' . $p); exit; }
    $file = strtok($p, '?#');
    if (defined('IS_ADMIN_AREA') && strpos($file, '/') === false && file_exists(ROOT_PATH . '/admin/' . $file)) $p = 'admin/' . $p;
    header('Location: ' . url($p)); exit;
}

/* ---------- Settings ---------- */
function S($k, $d = '') { static $c; if ($c === null) { $c = []; try { foreach (rows('SELECT skey,sval FROM settings') as $r) $c[$r['skey']] = $r['sval']; } catch (Exception $e) {} } return $c[$k] ?? $d; }
function set_setting($k, $v) { q('INSERT INTO settings(skey,sval) VALUES(?,?) ON DUPLICATE KEY UPDATE sval=VALUES(sval)', [$k, $v]); }

/* ---------- Language ---------- */
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'bn'])) { setcookie('lang', $_GET['lang'], time() + 86400 * 365, '/'); $_COOKIE['lang'] = $_GET['lang']; }
define('LANG', (($_COOKIE['lang'] ?? 'en') === 'bn') ? 'bn' : 'en');
function t($k, ...$a) { global $STR; $s = $STR[LANG][$k] ?? $STR['en'][$k] ?? $k; return $a ? vsprintf($s, $a) : $s; }
function tr($r, $f) { if (LANG === 'bn' && !empty($r[$f . '_bn'])) return $r[$f . '_bn']; return $r[$f . '_en'] ?? ''; }
function lines($k) { return array_values(array_filter(array_map('trim', preg_split('/\R/', sx($k))))); }
function sx($k) { if (LANG === 'bn' && S($k . '_bn') !== '') return S($k . '_bn'); return S($k . '_en'); }
function lang_url($l) { $q = $_GET; $q['lang'] = $l; return strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query($q); }

/* ---------- Helpers ---------- */
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n) { return S('currency', '৳') . number_format((float)$n, 0); }
function slugify($s) { $s = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $s), '-')); return $s ?: 'item-' . substr(md5(uniqid()), 0, 6); }
function flash($m = null, $t = 'ok') { if ($m !== null) { $_SESSION['flash'][] = [$t, $m]; return; } $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }
function csrf() { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_field() { return '<input type="hidden" name="_csrf" value="' . csrf() . '">'; }
function csrf_check() { $t = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF'] ?? ''); if (!hash_equals(csrf(), $t)) { http_response_code(419); die('Security token expired. Please go back and refresh the page.'); } }
function json_out($d, $c = 200) { http_response_code($c); header('Content-Type: application/json'); echo json_encode($d); exit; }
function client_ip() { foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $k) { if (!empty($_SERVER[$k])) { $ip = trim(explode(',', $_SERVER[$k])[0]); if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip; } } return '0.0.0.0'; }
function stars($r) { $r = round((float)$r); return str_repeat('★', $r) . str_repeat('☆', 5 - $r); }
function status_label($s) { return t('s_' . $s); }
const ORDER_STATUSES = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
const REV_STATUS = "('confirmed','processing','shipped','delivered')";

function upload_image($field, $old = '') {
    if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return $old;
    return save_upload($_FILES[$field]['tmp_name'], $_FILES[$field]['name'], $_FILES[$field]['size']) ?: $old;
}
function save_upload($tmp, $name, $size) {
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']) || $size > 5 * 1024 * 1024) return null;
    if ($ext !== 'svg' && !@getimagesize($tmp)) return null;
    if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);
    $f = date('ymd') . '-' . bin2hex(random_bytes(5)) . '.' . $ext;
    return move_uploaded_file($tmp, UPLOAD_DIR . $f) ? $f : null;
}
function ensure_return_requests() {
    static $ready = false;
    if ($ready) return;
    q("CREATE TABLE IF NOT EXISTS return_requests (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id INT NOT NULL,
        order_id INT NOT NULL,
        order_item_id INT NOT NULL,
        product_name VARCHAR(200) NOT NULL,
        product_variant VARCHAR(80) DEFAULT NULL,
        product_image VARCHAR(255) DEFAULT NULL,
        proof_image VARCHAR(255) NOT NULL,
        reason TEXT NOT NULL,
        status ENUM('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
        admin_note TEXT DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY order_id (order_id),
        KEY status (status),
        KEY order_item_id (order_item_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ready = true;
}

/* ---------- Auth ---------- */
function user() { static $u = false; if ($u === false) { $u = null; if (!empty($_SESSION['uid'])) { $u = row('SELECT * FROM users WHERE id=? AND status=1', [$_SESSION['uid']]); if (!$u) unset($_SESSION['uid']); } } return $u; }
function login_user($u) { session_regenerate_id(true); $_SESSION['uid'] = $u['id']; q('UPDATE users SET last_login=NOW() WHERE id=?', [$u['id']]); }
function require_login() { if (!user()) { $_SESSION['back'] = $_SERVER['REQUEST_URI']; redirect('login.php'); } }
function is_staff() { $u = user(); return $u && in_array($u['role'], ['admin', 'manager']); }
function can($perm) { $u = user(); if (!$u) return false; if ($u['role'] === 'admin') return true; if ($u['role'] !== 'manager') return false; return in_array($perm, array_filter(explode(',', $u['perms']))); }

/* ---------- Visitors ---------- */
function track_visit() {
    if (S('visitor_tracking') !== '1' || is_staff()) return;
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if ($ua === '' || preg_match('/bot|crawl|spider|slurp|curl|wget|monitor|facebookexternalhit/i', $ua)) return;
    $dev = preg_match('/tablet|ipad/i', $ua) ? 'tablet' : (preg_match('/mobile|android|iphone/i', $ua) ? 'mobile' : 'desktop');
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    q('INSERT INTO visitors(ip,page,referrer,user_agent,device,visit_date) VALUES(?,?,?,?,?,CURDATE())', [client_ip(), substr($_SERVER['REQUEST_URI'] ?? '', 0, 250), substr($ref, 0, 250), substr($ua, 0, 250), $dev]);
}

/* ---------- Weight options (variants) ---------- */
function variants_of($pid) { static $c = []; $pid = (int)$pid; if (!isset($c[$pid])) { try { $c[$pid] = rows('SELECT * FROM product_variants WHERE product_id=? AND status=1 ORDER BY grams ASC, id ASC', [$pid]); } catch (Exception $e) { $c[$pid] = []; } } return $c[$pid]; }
function variant_pct($v) { return ($v['old_price'] > $v['price']) ? (int)round(100 - $v['price'] / $v['old_price'] * 100) : 0; }
function sync_product_from_variants($pid) {
    $vs = rows('SELECT * FROM product_variants WHERE product_id=? AND status=1 ORDER BY grams ASC, id ASC', [$pid]); if (!$vs) return; $d = $vs[0]; $stock = array_sum(array_column($vs, 'stock'));
    q('UPDATE products SET price=?, old_price=?, cost_price=?, unit=?, stock=? WHERE id=?', [$d['price'], $d['old_price'], $d['cost_price'], $d['label_en'], $stock, $pid]);
}

/* ---------- Cart (keys are "productId:variantId") + Wishlist ---------- */
function cart() { $c = []; foreach (($_SESSION['cart'] ?? []) as $k => $q) { $k = (string)$k; if (strpos($k, ':') === false) $k .= ':0'; $c[$k] = ($c[$k] ?? 0) + (int)$q; } return $c; }
function cart_count() { return array_sum(cart()); }
function cart_lines() {
    $c = cart(); if (!$c) return [];
    $pids = []; $vids = [];
    foreach ($c as $k => $q) { [$p, $v] = array_map('intval', explode(':', $k)); $pids[$p] = 1; if ($v) $vids[$v] = 1; }
    $ps = []; foreach (rows('SELECT * FROM products WHERE status=1 AND id IN (' . implode(',', array_keys($pids)) . ')') as $r) $ps[$r['id']] = $r;
    $vs = []; if ($vids) foreach (rows('SELECT * FROM product_variants WHERE status=1 AND id IN (' . implode(',', array_keys($vids)) . ')') as $r) $vs[$r['id']] = $r;
    $out = [];
    foreach ($c as $k => $q) {
        [$pid, $vid] = array_map('intval', explode(':', $k)); $p = $ps[$pid] ?? null; if (!$p) continue;
        $v = $vid ? ($vs[$vid] ?? null) : null; if ($vid && (!$v || $v['product_id'] != $pid)) continue;
        $price = $v ? (float)$v['price'] : (float)$p['price']; $stock = $v ? (int)$v['stock'] : (int)$p['stock']; $qty = min((int)$q, max(1, $stock));
        $out[] = ['key' => $k, 'p' => $p, 'v' => $v, 'qty' => $qty, 'price' => $price, 'cost' => $v ? (float)$v['cost_price'] : (float)$p['cost_price'], 'stock' => $stock, 'label' => $v ? tr($v, 'label') : '', 'line' => $qty * $price];
    }
    return $out;
}
function delivery_fee($zone, $afterDiscount) {
    if (!in_array($zone, ['inside', 'outside'])) return 0;
    $free = (float)S('free_shipping_min', 0); if ($free > 0 && $afterDiscount >= $free) return 0;
    return (float)($zone === 'inside' ? S('shipping_inside', 60) : S('shipping_outside', 120));
}
/* Delivery is NOT added automatically: it is chosen by the customer at checkout (inside / outside Dhaka). */
function cart_totals($coupon = null, $zone = null) {
    $lines = cart_lines(); $sub = array_sum(array_column($lines, 'line')); $disc = 0;
    if ($coupon && $sub >= $coupon['min_order']) $disc = $coupon['type'] === 'percent' ? round($sub * $coupon['value'] / 100) : min($sub, $coupon['value']);
    $ship = $lines ? delivery_fee($zone, $sub - $disc) : 0;
    return ['lines' => $lines, 'subtotal' => $sub, 'discount' => $disc, 'shipping' => $ship, 'total' => $sub - $disc + $ship];
}
function find_coupon($code) {
    $c = row('SELECT * FROM coupons WHERE code=? AND status=1', [strtoupper(trim($code))]);
    if (!$c) return null;
    if ($c['expires_at'] && $c['expires_at'] < date('Y-m-d')) return null;
    if ($c['max_uses'] && $c['used'] >= $c['max_uses']) return null;
    return $c;
}

/* ---------- Catalog ---------- */
function categories_tree() { $all = rows('SELECT * FROM categories WHERE status=1 ORDER BY sort_order,id'); $t = []; foreach ($all as $c) { if (!$c['parent_id']) { $c['children'] = []; $t[$c['id']] = $c; } } foreach ($all as $c) if ($c['parent_id'] && isset($t[$c['parent_id']])) $t[$c['parent_id']]['children'][] = $c; return $t; }
function category_ids_with_children($id) { $ids = [(int)$id]; foreach (rows('SELECT id FROM categories WHERE parent_id=?', [$id]) as $r) $ids[] = (int)$r['id']; return $ids; }
function update_rating($pid) { $r = row("SELECT AVG(rating) a, COUNT(*) c FROM reviews WHERE product_id=? AND status=1", [$pid]); q('UPDATE products SET rating=?, rating_count=? WHERE id=?', [round($r['a'] ?? 0, 2), (int)$r['c'], $pid]); }
function menu_tree($loc) {
    $items = rows('SELECT * FROM menu_items WHERE location=? AND status=1 ORDER BY sort_order,id', [$loc]);
    $cats = []; foreach (rows('SELECT * FROM categories WHERE status=1') as $c) $cats[$c['id']] = $c;
    $out = [];
    foreach ($items as &$it) {
        if ($it['item_type'] === 'category') { $c = $cats[$it['ref_id']] ?? null; if (!$c) continue; $it['href'] = 'shop.php?cat=' . $c['slug']; $it['label'] = $it['label_en'] ? tr($it, 'label') : tr($c, 'name'); }
        else { $it['href'] = $it['url'] ?: '#'; $it['label'] = tr($it, 'label'); }
        $it['children'] = [];
        if ($it['item_type'] === 'category' && $it['auto_children']) foreach ($cats as $c) if ($c['parent_id'] == $it['ref_id']) $it['children'][] = ['href' => 'shop.php?cat=' . $c['slug'], 'label' => tr($c, 'name'), 'children' => []];
    } unset($it);
    $byId = []; foreach ($items as $i => &$it) $byId[$it['id']] = &$it; unset($it);
    foreach ($items as $it) { if (!isset($it['href'])) continue; if ($it['parent_id'] && isset($byId[$it['parent_id']])) $byId[$it['parent_id']]['children'][] = $it; elseif (!$it['parent_id']) $out[] = $byId[$it['id']]; }
    return $out;
}

/* ---------- Mail (built-in SMTP client - credentials come from Admin > Settings) ---------- */
function send_mail($to, $subject, $html) {
    if (S('mail_enabled') !== '1' || !$to) return false;
    $host = S('smtp_host'); $port = (int)S('smtp_port', 587); $sec = S('smtp_secure', 'tls'); $user = S('smtp_user'); $pass = S('smtp_pass');
    $from = S('mail_from') ?: $user; $fname = S('mail_from_name', S('site_name'));
    $err = '';
    try {
        $f = @stream_socket_client(($sec === 'ssl' ? 'ssl://' : '') . $host . ':' . $port, $en, $es, 15);
        if (!$f) throw new Exception("Connect failed: $es");
        stream_set_timeout($f, 15);
        $rd = function () use ($f) { $d = ''; while (($l = fgets($f, 515)) !== false) { $d .= $l; if (substr($l, 3, 1) === ' ') break; } return $d; };
        $wr = function ($c) use ($f, $rd) { fwrite($f, $c . "\r\n"); return $rd(); };
        $rd(); $wr('EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
        if ($sec === 'tls') { $wr('STARTTLS'); if (!stream_socket_enable_crypto($f, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) throw new Exception('TLS failed'); $wr('EHLO localhost'); }
        if ($user) { $wr('AUTH LOGIN'); $wr(base64_encode($user)); $r = $wr(base64_encode($pass)); if (substr($r, 0, 3) !== '235') throw new Exception('SMTP login failed: ' . trim($r)); }
        $wr("MAIL FROM:<$from>"); $r = $wr("RCPT TO:<$to>"); if (substr($r, 0, 1) !== '2') throw new Exception('Recipient rejected');
        $wr('DATA');
        $h = "From: =?UTF-8?B?" . base64_encode($fname) . "?= <$from>\r\nTo: <$to>\r\nSubject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\nDate: " . date('r') . "\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
        $r = $wr($h . chunk_split(base64_encode('<div style="font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #dbe7cf;border-radius:12px;padding:24px"><p style="margin:0 0 18px"><img src="' . logo_url() . '" alt="' . e(S('site_name')) . '" style="height:54px"></p>' . $html . '</div>')) . "\r\n.");
        if (substr($r, 0, 3) !== '250') throw new Exception('Send failed: ' . trim($r));
        $wr('QUIT'); fclose($f);
    } catch (Exception $ex) { $err = $ex->getMessage(); }
    q('INSERT INTO mail_log(to_email,subject,status,error) VALUES(?,?,?,?)', [$to, mb_substr($subject, 0, 250), $err ? 'failed' : 'sent', mb_substr($err, 0, 250)]);
    return !$err;
}
function sms_normalize_phone($phone) {
    $digits = preg_replace('/\D+/', '', (string)$phone);
    if (preg_match('/^01[3-9][0-9]{8}$/', $digits)) $digits = '880' . substr($digits, 1);
    return $digits;
}
function send_sms($phone, $message, &$error = '') {
    $error = '';
    if (S('sms_enabled') !== '1') { $error = 'SMS notifications are disabled.'; return false; }
    $phone = sms_normalize_phone($phone);
    if (!preg_match('/^8801[3-9][0-9]{8}$/', $phone)) { $error = 'The customer phone number is not a valid Bangladesh mobile number.'; return false; }
    $endpoint = trim(S('sms_api_url'));
    $host = strtolower((string)parse_url($endpoint, PHP_URL_HOST));
    if (!$endpoint || stripos($endpoint, 'https://') !== 0 || !preg_match('/(^|\\.)sslwireless\\.com$/', $host)) { $error = 'Configure a valid HTTPS SSL Wireless API endpoint in Admin → Settings → SMS.'; return false; }
    $username = S('sms_api_user'); $password = S('sms_api_pass'); $senderId = S('sms_sender_id');
    if (!$username || !$password || !$senderId) { $error = 'Complete the SSL Wireless username, password and sender ID in Admin → Settings → SMS.'; return false; }
    if (!function_exists('curl_init')) { $error = 'PHP cURL is required to send SMS.'; return false; }
    $payload = http_build_query(['user' => $username, 'pass' => $password, 'sid' => $senderId, 'sms' => [['0' => $phone, '1' => $message]]]);
    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 8, CURLOPT_TIMEOUT => 15, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2, CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']]);
    $response = curl_exec($ch); $curlError = curl_error($ch); $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    if ($response === false || $curlError) $error = 'SSL Wireless request failed: ' . ($curlError ?: 'no response');
    elseif ($status < 200 || $status >= 300) $error = 'SSL Wireless returned HTTP ' . $status . '.';
    elseif (preg_match('/\\b(error|failed|failure|invalid|rejected)\\b/i', (string)$response)) $error = 'SSL Wireless rejected the SMS request.';
    if ($error !== '') { error_log('DryNutri SMS send failed for ' . $phone . ': ' . $error); return false; }
    return true;
}
function notify_order($oid) {
    $o = row('SELECT * FROM orders WHERE id=?', [$oid]); if (!$o) return false;
    $its = rows('SELECT * FROM order_items WHERE order_id=?', [$oid]);
    $list = '<p><b>Invoice:</b> ' . e($o['order_no']) . '<br><b>Date:</b> ' . date('d M Y h:i A', strtotime($o['created_at'])) . '<br><b>Delivery address:</b> ' . e($o['address']) . ', ' . e($o['city']) . '<br><b>Payment:</b> ' . e(strtoupper($o['payment_method'])) . ' (' . e($o['payment_status']) . ')</p>';
    $list .= '<table style="width:100%;border-collapse:collapse"><tr><th style="padding:6px;text-align:left">Item</th><th style="padding:6px;text-align:right">Amount</th></tr>'; foreach ($its as $i) $list .= '<tr><td style="padding:6px;border-bottom:1px solid #eee">' . e($i['name']) . ($i['variant'] ? ' (' . e($i['variant']) . ')' : '') . ' × ' . (int)$i['qty'] . '</td><td style="padding:6px;border-bottom:1px solid #eee;text-align:right">' . money($i['price'] * $i['qty']) . '</td></tr>';
    $list .= '<tr><td style="padding:6px;text-align:right">Subtotal</td><td style="padding:6px;text-align:right">' . money($o['subtotal']) . '</td></tr><tr><td style="padding:6px;text-align:right">Shipping</td><td style="padding:6px;text-align:right">' . money($o['shipping']) . '</td></tr><tr><td style="padding:6px;text-align:right">Discount</td><td style="padding:6px;text-align:right">-' . money($o['discount']) . '</td></tr><tr><td style="padding:6px;text-align:right"><b>Total</b></td><td style="padding:6px;text-align:right"><b>' . money($o['total']) . '</b></td></tr></table>';
    $rep = ['{name}' => e($o['name']), '{order_no}' => $o['order_no'], '{status}' => ucfirst($o['status']), '{total}' => money($o['total']), '{items}' => $list, '{track_url}' => full_url('track.php?order=' . $o['order_no']), '{site}' => S('site_name')];
    $sub = strtr(S('order_mail_subject'), $rep); $template = S('order_mail_body'); $body = strtr($template, $rep);
    if (strpos($template, '{items}') === false) $body .= '<hr><h3>Invoice details</h3>' . $list;
    $customerSent = false;
    if ($o['email'] && filter_var($o['email'], FILTER_VALIDATE_EMAIL)) $customerSent = send_mail($o['email'], $sub, $body);
    if (S('admin_email') && $o['status'] === 'pending') send_mail(S('admin_email'), '[New Order] ' . $o['order_no'] . ' - ' . money($o['total']), $body);
    return $customerSent;
}
function send_order_sms($oid) {
    if (S('sms_enabled') !== '1') return false;
    $o = row('SELECT order_no,name,phone,total,status FROM orders WHERE id=?', [$oid]);
    if (!$o) { error_log('DryNutri SMS send failed: order not found (' . (int)$oid . ').'); return false; }
    $template = S('order_sms_body', 'Hello {name}, we received order {order_no} ({status}). Total: {total}. Thank you for shopping with {site}.');
    $message = strtr($template, ['{name}' => $o['name'], '{order_no}' => $o['order_no'], '{total}' => money($o['total']), '{status}' => ucfirst($o['status']), '{site}' => S('site_name')]);
    $error = '';
    return send_sms($o['phone'], $message, $error);
}
function set_order_status($oid, $status, $note = '') {
    q('UPDATE orders SET status=? WHERE id=?', [$status, $oid]);
    q('INSERT INTO order_log(order_id,status,note) VALUES(?,?,?)', [$oid, $status, $note]);
    if ($status === 'delivered') q("UPDATE orders SET payment_status='paid' WHERE id=? AND payment_method='cod'", [$oid]);
}

function cat_icon($id) { static $m; if ($m === null) { $m = []; try { foreach (rows('SELECT id,icon FROM categories') as $r) $m[$r['id']] = $r['icon'] ?: '🥜'; } catch (Exception $e) {} } return $m[$id] ?? '🥜'; }
function has_img($f) { return $f && file_exists(UPLOAD_DIR . $f); }

/* ---------- Notifications (site bell + email to subscribers & customers) ---------- */
function notify_recipients($aud = 'all') {
    $out = [];
    try { q("UPDATE newsletter SET token=SUBSTR(MD5(CONCAT(id,email,RAND())),1,24) WHERE token IS NULL OR token=''"); } catch (Exception $e) {}
    if (in_array($aud, ['all', 'subscribers'])) foreach (rows('SELECT email,token FROM newsletter WHERE status=1') as $r) $out[strtolower($r['email'])] = ['email' => strtolower($r['email']), 'token' => $r['token']];
    if (in_array($aud, ['all', 'customers'])) foreach (rows("SELECT email FROM users WHERE role='customer' AND status=1 AND email<>'' AND email NOT LIKE '%@guest.local'") as $r) { $e = strtolower($r['email']); if (!isset($out[$e])) $out[$e] = ['email' => $e, 'token' => null]; }
    $out = array_filter($out, fn($x) => filter_var($x['email'], FILTER_VALIDATE_EMAIL));
    return array_slice(array_values($out), 0, 1000);
}
function notify_email($id, $aud = 'all') {
    if (S('mail_enabled') !== '1') return 0;
    $n = row('SELECT * FROM notifications WHERE id=?', [$id]); if (!$n) return 0;
    @set_time_limit(0); $sent = 0;
    $btn = $n['link'] ? '<p style="margin:22px 0"><a href="' . e(full_url($n['link'])) . '" style="background:#2f7d32;color:#fff;padding:12px 26px;border-radius:999px;text-decoration:none;font-weight:bold">View on ' . e(S('site_name')) . ' &rarr;</a></p>' : '';
    $img = has_img($n['image']) ? '<p><img src="' . e(full_url('uploads/' . $n['image'])) . '" alt="" style="max-width:100%;border-radius:12px"></p>' : '';
    $html = '<h2 style="color:#1f5a24;margin-top:0">' . e($n['title_en']) . '</h2>' . $img . '<p>' . nl2br(e($n['body_en'])) . '</p>' . ($n['title_bn'] || $n['body_bn'] ? '<hr style="border:0;border-top:1px solid #eee"><h3>' . e($n['title_bn']) . '</h3><p>' . nl2br(e($n['body_bn'])) . '</p>' : '') . $btn;
    foreach (notify_recipients($aud) as $r) {
        $foot = '<p style="color:#999;font-size:12px">You are receiving this because you subscribed or registered at ' . e(S('site_name')) . '.' . ($r['token'] ? ' <a href="' . e(full_url('unsubscribe.php?t=' . $r['token'])) . '" style="color:#999">Unsubscribe</a>' : '') . '</p>';
        if (send_mail($r['email'], $n['title_en'], $html . $foot)) $sent++;
    }
    q('UPDATE notifications SET emailed=? WHERE id=?', [$sent, $id]);
    return $sent;
}
function notify_create($type, $te, $be, $link = '', $tb = '', $bb = '', $image = '', $email = false, $aud = 'all') {
    q('INSERT INTO notifications(type,title_en,title_bn,body_en,body_bn,link,image) VALUES(?,?,?,?,?,?,?)', [$type, $te, $tb, $be, $bb, $link, $image]);
    $id = (int)db()->lastInsertId();
    send_push_notification($id);
    if ($email || S('notify_email_auto') === '1') notify_email($id, $aud);
    return $id;
}
function ensure_push_subscriptions_table() {
    q("CREATE TABLE IF NOT EXISTS push_subscriptions (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        endpoint_hash CHAR(64) NOT NULL UNIQUE,
        endpoint TEXT NOT NULL,
        public_key VARCHAR(255) NOT NULL,
        auth_token VARCHAR(255) NOT NULL,
        content_encoding VARCHAR(12) NOT NULL DEFAULT 'aes128gcm',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY(updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}
function push_subscribe($subscription) {
    if (S('push_enabled') !== '1' || !S('push_public_key')) return false;
    if (!is_array($subscription)) return false;
    $endpoint = trim((string)($subscription['endpoint'] ?? ''));
    $endpointHost = strtolower((string)parse_url($endpoint, PHP_URL_HOST));
    $knownPushHost = false;
    foreach (['fcm.googleapis.com', 'push.services.mozilla.com', 'push.apple.com', 'notify.windows.com'] as $allowedHost) {
        if ($endpointHost === $allowedHost || substr($endpointHost, -strlen('.' . $allowedHost)) === '.' . $allowedHost) { $knownPushHost = true; break; }
    }
    $keys = $subscription['keys'] ?? [];
    $publicKey = (string)($keys['p256dh'] ?? '');
    $authToken = (string)($keys['auth'] ?? '');
    if (strlen($endpoint) > 2048 || stripos($endpoint, 'https://') !== 0 || !$knownPushHost || filter_var($endpointHost, FILTER_VALIDATE_IP) || !preg_match('/^[A-Za-z0-9_-]{80,200}$/', $publicKey) || !preg_match('/^[A-Za-z0-9_-]{16,100}$/', $authToken)) return false;
    ensure_push_subscriptions_table();
    $hash = hash('sha256', $endpoint);
    q('INSERT INTO push_subscriptions(endpoint_hash,endpoint,public_key,auth_token,content_encoding) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE endpoint=VALUES(endpoint),public_key=VALUES(public_key),auth_token=VALUES(auth_token),content_encoding=VALUES(content_encoding),updated_at=NOW()', [$hash, $endpoint, $publicKey, $authToken, 'aes128gcm']);
    return true;
}
function push_unsubscribe($endpoint) {
    $endpoint = trim((string)$endpoint);
    if (!$endpoint || strlen($endpoint) > 2048) return false;
    ensure_push_subscriptions_table();
    q('DELETE FROM push_subscriptions WHERE endpoint_hash=?', [hash('sha256', $endpoint)]);
    return true;
}
function send_push_notification($notificationId) {
    if (S('push_enabled') !== '1' || !S('push_public_key') || !S('push_private_key')) return 0;
    if (!class_exists(\Minishlink\WebPush\WebPush::class)) { error_log('Browser push is enabled but Composer dependencies are missing. Run composer install.'); return 0; }
    try {
        ensure_push_subscriptions_table();
        $notification = row('SELECT * FROM notifications WHERE id=? AND status=1', [$notificationId]);
        if (!$notification) return 0;
        $subscriptions = rows('SELECT endpoint_hash,endpoint,public_key,auth_token,content_encoding FROM push_subscriptions');
        if (!$subscriptions) return 0;
        $siteUrl = rtrim(S('site_url'), '/');
        $link = trim((string)$notification['link']);
        $target = preg_match('#^https://#i', $link) ? $link : ($siteUrl ? $siteUrl . '/' . ltrim($link, '/') : full_url($link));
        $payload = json_encode([
            'title' => trim((string)$notification['title_en']) ?: S('site_name'),
            'body' => trim(strip_tags((string)$notification['body_en'])),
            'url' => $target,
            'icon' => has_img($notification['image']) ? full_url('uploads/' . $notification['image']) : logo_url(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($payload === false) { error_log('Browser push payload encoding failed for notification ' . (int)$notificationId . '.'); return 0; }
        $webPush = new \Minishlink\WebPush\WebPush(['VAPID' => [
            'subject' => $siteUrl ?: full_url(),
            'publicKey' => S('push_public_key'),
            'privateKey' => S('push_private_key'),
        ]], ['TTL' => 86400, 'urgency' => 'normal']);
        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(\Minishlink\WebPush\Subscription::create([
                'endpoint' => $subscription['endpoint'],
                'publicKey' => $subscription['public_key'],
                'authToken' => $subscription['auth_token'],
                'contentEncoding' => $subscription['content_encoding'],
            ]), $payload);
        }
        $sent = 0;
        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) { $sent++; continue; }
            $endpointHash = hash('sha256', $report->getEndpoint());
            if ($report->isSubscriptionExpired()) q('DELETE FROM push_subscriptions WHERE endpoint_hash=?', [$endpointHash]);
            error_log('Browser push failed (' . ($report->isSubscriptionExpired() ? 'expired subscription' : $report->getReason()) . ') for endpoint hash ' . $endpointHash . '.');
        }
        return $sent;
    } catch (\Throwable $error) {
        error_log('Browser push failed for notification ' . (int)$notificationId . ': ' . $error->getMessage());
        return 0;
    }
}

/* ---------- Logo (assets/img default = DryNutri logo; Settings can override) ---------- */
function logo_html($ctx = 'header', $full = false) {
    $name = e(S('site_name', 'DryNutri')); $custom = S('logo'); $dark = S('logo_dark');
    if ($ctx === 'dark') {
        if (has_img($dark)) $img = '<img class="logo-img" src="' . img($dark) . '" alt="' . $name . '">';
        elseif (has_img($custom)) $img = '<span class="logo-pill"><img class="logo-img" src="' . img($custom) . '" alt="' . $name . '"></span>';
        else $img = '<img class="logo-img" src="' . asset('img/logo-mark-light.png') . '" alt="' . $name . '">';
    } else $img = '<img class="logo-img" src="' . (has_img($custom) ? img($custom) : asset('img/logo-mark.png')) . '" alt="' . $name . '">';
    $tag = S('logo_tagline', '1') === '1' && sx('tagline') !== '' ? '<small class="logo-tag">' . e(sx('tagline')) . '</small>' : '';
    return '<span class="logo-lock">' . $img . $tag . '</span>';
}
function logo_url() { return has_img(S('logo')) ? full_url('uploads/' . S('logo')) : full_url('assets/img/logo.png'); }
function favicon_url() { return has_img(S('favicon')) ? img(S('favicon')) : asset('img/favicon.png'); }

/* ---------- Login throttle (5 wrong passwords => 15 min lock per IP) ---------- */
function login_locked() { try { $r = row('SELECT tries,locked_until FROM login_attempts WHERE ip=?', [client_ip()]); return $r && $r['locked_until'] && strtotime($r['locked_until']) > time(); } catch (Exception $e) { return false; } }
function login_fail() { try { q('INSERT INTO login_attempts(ip,tries) VALUES(?,1) ON DUPLICATE KEY UPDATE tries=tries+1, locked_until=IF(tries>=5, DATE_ADD(NOW(), INTERVAL 15 MINUTE), locked_until)', [client_ip()]); } catch (Exception $e) {} }
function login_ok() { try { q('DELETE FROM login_attempts WHERE ip=?', [client_ip()]); } catch (Exception $e) {} }


/* ---------- Customers created from orders (guest checkout becomes a customer record) ---------- */
function ensure_customer($name, $phone, $email, $address, $city) {
    $u = user();
    if ($u) { q("UPDATE users SET phone=IF(phone IS NULL OR phone='',?,phone), address=IF(address IS NULL OR address='',?,address), city=IF(city IS NULL OR city='',?,city) WHERE id=?", [$phone, $address, $city, $u['id']]); return (int)$u['id']; }
    $email = strtolower(trim($email)); $row = null;
    if ($email) $row = row("SELECT id FROM users WHERE email=? AND role='customer'", [$email]);
    if (!$row && $phone) $row = row("SELECT id FROM users WHERE phone=? AND role='customer' AND is_guest=1 ORDER BY id LIMIT 1", [$phone]);
    if ($row) { q("UPDATE users SET address=?, city=? WHERE id=?", [$address, $city, $row['id']]); return (int)$row['id']; }
    $em = $email ?: 'guest-' . substr(md5($phone . $name), 0, 12) . '@guest.local';
    if ($ex = row('SELECT id FROM users WHERE email=?', [$em])) return (int)$ex['id'];
    q("INSERT INTO users(name,email,phone,password,role,address,city,is_guest) VALUES(?,?,?,?,'customer',?,?,1)", [$name, $em, $phone, password_hash(bin2hex(random_bytes(12)), PASSWORD_DEFAULT), $address, $city]);
    return (int)db()->lastInsertId();
}
function user_avatar($u, $cls = 'av') { if (!$u) return ''; return has_img($u['avatar'] ?? '') ? '<span class="' . $cls . '"><img src="' . img($u['avatar']) . '" alt=""></span>' : '<span class="' . $cls . '">' . e(mb_substr($u['name'] ?: 'U', 0, 1)) . '</span>'; }

/* ---------- Checkout visited (abandoned checkout tracking) ---------- */
function cv_token($create = true) {
    if (!empty($_COOKIE['dn_cv']) && preg_match('/^[a-f0-9]{32}$/', $_COOKIE['dn_cv'])) return $_COOKIE['dn_cv'];
    if (!$create) return '';
    $t = bin2hex(random_bytes(16)); setcookie('dn_cv', $t, time() + 86400 * 30, '/'); $_COOKIE['dn_cv'] = $t; return $t;
}
function cv_touch($f = []) {
    try {
        $lines = cart_lines(); if (!$lines) return;
        $t = cv_token(); $u = user(); $items = []; $sub = 0; $cnt = 0;
        foreach ($lines as $l) { $items[] = ['n' => $l['p']['name_en'], 'v' => $l['v'] ? $l['v']['label_en'] : '', 'q' => $l['qty'], 'p' => $l['price']]; $sub += $l['line']; $cnt += $l['qty']; }
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? ''; $dev = preg_match('/tablet|ipad/i', $ua) ? 'tablet' : (preg_match('/mobile|android|iphone/i', $ua) ? 'mobile' : 'desktop');
        $name = trim($f['name'] ?? '') ?: ($u['name'] ?? ''); $phone = trim($f['phone'] ?? '') ?: ($u['phone'] ?? ''); $email = trim($f['email'] ?? '') ?: ($u['email'] ?? ''); $addr = trim($f['address'] ?? '') ?: ($u['address'] ?? '');
        if (preg_match('/@guest\.local$/', $email)) $email = '';
        q("INSERT INTO checkout_visits(sid,user_id,name,phone,email,address,ip,device,cart_json,cart_total,items_count) VALUES(?,?,?,?,?,?,?,?,?,?,?)
           ON DUPLICATE KEY UPDATE user_id=IF(VALUES(user_id)>0,VALUES(user_id),user_id), name=IF(VALUES(name)<>'',VALUES(name),name), phone=IF(VALUES(phone)<>'',VALUES(phone),phone), email=IF(VALUES(email)<>'',VALUES(email),email), address=IF(VALUES(address)<>'',VALUES(address),address), ip=VALUES(ip), device=VALUES(device), cart_json=VALUES(cart_json), cart_total=VALUES(cart_total), items_count=VALUES(items_count), status=IF(status='ordered','ordered',status)",
          [$t, $u['id'] ?? 0, mb_substr($name, 0, 120), mb_substr($phone, 0, 30), mb_substr($email, 0, 160), mb_substr($addr, 0, 500), client_ip(), $dev, json_encode($items, JSON_UNESCAPED_UNICODE), $sub, $cnt]);
    } catch (Exception $e) {}
}
function cv_complete($orderId, $userId) {
    try { $t = cv_token(false); if ($t) { q("UPDATE checkout_visits SET status='ordered', order_id=?, user_id=IF(?>0,?,user_id) WHERE sid=?", [$orderId, $userId, $userId, $t]); setcookie('dn_cv', '', time() - 3600, '/'); unset($_COOKIE['dn_cv']); } } catch (Exception $e) {}
}

/* ---------- Weight options editor helpers (admin) ---------- */
function parse_grams($label) { if (preg_match('/(\d+(?:\.\d+)?)\s*(kg|kilo|কেজি)?/iu', (string)$label, $m)) return (int)round((float)$m[1] * (!empty($m[2]) ? 1000 : 1)); return 0; }
function sync_variants_from_post($pid) {
    $labs = $_POST['v_label'] ?? []; if (!is_array($labs)) return; $keep = [];
    foreach ($labs as $i => $lab) {
        $lab = trim($lab); if ($lab === '') continue; $vid = (int)($_POST['v_id'][$i] ?? 0); $g = (int)($_POST['v_grams'][$i] ?? 0); if ($g <= 0) $g = parse_grams($lab);
        $d = [$lab, trim($_POST['v_label_bn'][$i] ?? ''), $g, (float)($_POST['v_price'][$i] ?? 0), (float)($_POST['v_old'][$i] ?? 0), (float)($_POST['v_cost'][$i] ?? 0), max(0, (int)($_POST['v_stock'][$i] ?? 0))];
        if ($vid && val('SELECT id FROM product_variants WHERE id=? AND product_id=?', [$vid, $pid])) { q('UPDATE product_variants SET label_en=?,label_bn=?,grams=?,price=?,old_price=?,cost_price=?,stock=? WHERE id=?', array_merge($d, [$vid])); $keep[] = $vid; }
        else { q('INSERT INTO product_variants(product_id,label_en,label_bn,grams,price,old_price,cost_price,stock) VALUES(?,?,?,?,?,?,?,?)', array_merge([$pid], $d)); $keep[] = (int)db()->lastInsertId(); }
    }
    if ($keep) q('DELETE FROM product_variants WHERE product_id=? AND id NOT IN (' . implode(',', $keep) . ')', [$pid]); else q('DELETE FROM product_variants WHERE product_id=?', [$pid]);
    sync_product_from_variants($pid);
}
function variant_editor_html($pid) {
    $rows = $pid ? rows('SELECT * FROM product_variants WHERE product_id=? ORDER BY grams,id', [$pid]) : [];
    $row = function ($v) { $g = fn($k, $d = '') => e($v[$k] ?? $d); return '<div class="vrow"><input type="hidden" name="v_id[]" value="' . $g('id', 0) . '"><input type="text" name="v_label[]" value="' . $g('label_en') . '" placeholder="e.g. 200 g"><input type="text" name="v_label_bn[]" value="' . $g('label_bn') . '" placeholder="২০০ গ্রাম"><input type="number" name="v_grams[]" value="' . $g('grams') . '" placeholder="200" min="0" step="1"><input type="number" name="v_price[]" value="' . $g('price') . '" placeholder="Price" min="0" step="any"><input type="number" name="v_old[]" value="' . $g('old_price') . '" placeholder="Old price" min="0" step="any"><input type="number" name="v_cost[]" value="' . $g('cost_price') . '" placeholder="Cost" min="0" step="any"><input type="number" name="v_stock[]" value="' . $g('stock') . '" placeholder="Stock" min="0" step="1"><button type="button" class="abtn sm danger vdel" title="Remove">🗑</button></div>'; };
    $h = '<label>Weight / size options</label><small class="muted">Customers must pick one on the product page (e.g. 200 g, 100 g, 50 g, 25 g). If they do not pick, the <b>lowest quantity</b> is selected automatically. Each option has its own price, old price (for the % OFF badge), cost and stock. Leave empty for a normal single-price product.</small>';
    $h .= '<div class="vhead"><span>Label (English)</span><span>Label (বাংলা)</span><span>Grams</span><span>Price</span><span>Old price</span><span>Cost</span><span>Stock</span><span></span></div><div class="vrows" id="vrows">';
    foreach ($rows as $v) $h .= $row($v);
    $h .= '</div><button type="button" class="abtn sm ghost" id="addV">＋ Add weight option</button><template id="vtpl">' . $row([]) . '</template>';
    $h .= '<script>(function(){var box=document.getElementById("vrows"),tpl=document.getElementById("vtpl");document.getElementById("addV").onclick=function(){box.appendChild(tpl.content.cloneNode(true))};box.addEventListener("click",function(e){var b=e.target.closest(".vdel");if(b)b.closest(".vrow").remove()})})()</script>';
    return $h;
}
