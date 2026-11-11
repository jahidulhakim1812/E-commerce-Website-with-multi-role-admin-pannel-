<?php
require '_layout.php'; admin_start('Checkout Visited', 'orders');
function wa_num($p) { $d = preg_replace('/\D/', '', (string)$p); if (strlen($d) === 11 && $d[0] === '0') return '88' . $d; return $d; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $id = (int)($_POST['id'] ?? 0);
    if (isset($_POST['contacted'])) { q('UPDATE checkout_visits SET contacted=1-contacted WHERE id=?', [$id]); flash('Updated.'); }
    elseif (isset($_POST['del'])) { q('DELETE FROM checkout_visits WHERE id=?', [$id]); flash('Deleted.'); }
    elseif (isset($_POST['clear_old'])) { q("DELETE FROM checkout_visits WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)"); flash('Records older than 90 days were removed.'); }
    redirect('checkout_visits.php' . (!empty($_POST['back']) ? '?' . $_POST['back'] : ''));
}
$ST = "CASE WHEN status='ordered' THEN 'ordered' WHEN updated_at < DATE_SUB(NOW(), INTERVAL 30 MINUTE) THEN 'abandoned' ELSE 'active' END";
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-29 days')); $to = $_GET['to'] ?? date('Y-m-d'); $st = $_GET['st'] ?? ''; $qv = trim($_GET['q'] ?? ''); $ph = !empty($_GET['phone']);
$w = ['DATE(created_at) BETWEEN ? AND ?']; $a = [$from, $to];
if (in_array($st, ['active', 'abandoned', 'ordered'])) { $w[] = "($ST)=?"; $a[] = $st; }
if ($qv !== '') { $w[] = '(name LIKE ? OR phone LIKE ? OR email LIKE ? OR ip LIKE ?)'; array_push($a, "%$qv%", "%$qv%", "%$qv%", "%$qv%"); }
if ($ph) $w[] = "phone<>''"; $W = implode(' AND ', $w);
if (isset($_GET['export'])) { ob_end_clean(); header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename=checkout-visited-' . date('Ymd') . '.csv'); $f = fopen('php://output', 'w'); fputcsv($f, ['Started', 'Last activity', 'Name', 'Account ID', 'Phone', 'Email', 'Address', 'IP', 'Device', 'Items', 'Cart total', 'Status']); foreach (rows("SELECT *, ($ST) st FROM checkout_visits WHERE $W ORDER BY updated_at DESC", $a) as $r) fputcsv($f, [$r['created_at'], $r['updated_at'], $r['name'], $r['user_id'] ?: 'guest', $r['phone'], $r['email'], $r['address'], $r['ip'], $r['device'], $r['items_count'], $r['cart_total'], $r['st']]); exit; }
$k = fn($sql, $args = []) => (int)val($sql, $args);
$all = $k("SELECT COUNT(*) FROM checkout_visits WHERE DATE(created_at) BETWEEN ? AND ?", [$from, $to]); $ab = $k("SELECT COUNT(*) FROM checkout_visits WHERE DATE(created_at) BETWEEN ? AND ? AND ($ST)='abandoned'", [$from, $to]);
$abp = $k("SELECT COUNT(*) FROM checkout_visits WHERE DATE(created_at) BETWEEN ? AND ? AND ($ST)='abandoned' AND phone<>''", [$from, $to]); $od = $k("SELECT COUNT(*) FROM checkout_visits WHERE DATE(created_at) BETWEEN ? AND ? AND status='ordered'", [$from, $to]);
$lost = (float)val("SELECT COALESCE(SUM(cart_total),0) FROM checkout_visits WHERE DATE(created_at) BETWEEN ? AND ? AND ($ST)='abandoned'", [$from, $to]);
echo '<div class="cards"><div class="kpi c3d b"><span class="ki">🛒</span><div><small>Checkout visits</small><b>' . $all . '</b></div></div><div class="kpi c3d r"><span class="ki">⚠</span><div><small>Abandoned (no order)</small><b>' . $ab . '</b></div></div><div class="kpi c3d o"><span class="ki">📞</span><div><small>Abandoned with phone</small><b>' . $abp . '</b></div></div><div class="kpi c3d g"><span class="ki">✅</span><div><small>Ordered · cart value at risk ' . money($lost) . '</small><b>' . $od . '</b></div></div></div>';
echo '<div class="alert ok">When someone opens the checkout page, their visitor ID, IP, cart and any name / phone / email they type are saved here — even if they never place the order. Use <b>WhatsApp</b> or <b>Call</b> to follow up. An entry turns <b>Abandoned</b> after 30 minutes without an order.</div>';
$per = 25; $pg = max(1, (int)($_GET['page'] ?? 1)); $n = $k("SELECT COUNT(*) FROM checkout_visits WHERE $W", $a); $rs = rows("SELECT *, ($ST) st FROM checkout_visits WHERE $W ORDER BY updated_at DESC LIMIT $per OFFSET " . (($pg - 1) * $per), $a); $pages = max(1, ceil($n / $per));
echo '<div class="panel"><div class="panel-h"><h3>Checkout visitors <small>(' . $n . ')</small></h3><form class="filt"><span>From</span><input type="date" name="from" value="' . e($from) . '"><span>To</span><input type="date" name="to" value="' . e($to) . '"><select name="st"><option value="">All status</option>'; foreach (['abandoned' => 'Abandoned', 'active' => 'Still on checkout', 'ordered' => 'Ordered'] as $kk => $l) echo '<option value="' . $kk . '" ' . ($st === $kk ? 'selected' : '') . '>' . $l . '</option>'; echo '</select><input name="q" value="' . e($qv) . '" placeholder="Name / phone / email / IP"><label class="chk"><input type="checkbox" name="phone" value="1" ' . ($ph ? 'checked' : '') . '> Phone captured</label><button class="abtn sm">Filter</button><a class="abtn sm ghost" href="checkout_visits.php">Reset</a><a class="abtn sm ghost" href="?' . e(http_build_query(array_merge($_GET, ['export' => 1]))) . '">⬇ CSV</a></form></div>';
echo '<div class="table-wrap"><table class="atbl"><tr><th>Last activity</th><th>Visitor</th><th>Contact</th><th>IP / device</th><th>Cart</th><th>Status</th><th>Actions</th></tr>';
$back = e(http_build_query($_GET));
foreach ($rs as $r) {
    $items = json_decode($r['cart_json'] ?: '[]', true) ?: []; $cart = ''; foreach ($items as $i) $cart .= e($i['n']) . ($i['v'] ? ' <b>(' . e($i['v']) . ')</b>' : '') . ' × ' . (int)$i['q'] . '<br>';
    $who = $r['user_id'] ? 'Account #' . (int)$r['user_id'] : 'Guest · ' . substr($r['sid'], 0, 6);
    $wa = $r['phone'] ? 'https://wa.me/' . wa_num($r['phone']) . '?text=' . rawurlencode('Hello ' . ($r['name'] ?: '') . ', you left items worth ' . money($r['cart_total']) . ' in your ' . S('site_name') . ' cart. Can we help you complete the order?') : '';
    echo '<tr><td>' . date('d M h:i A', strtotime($r['updated_at'])) . '<div class="cvcart">started ' . date('d M h:i A', strtotime($r['created_at'])) . '</div></td><td><b>' . e($r['name'] ?: 'Unknown visitor') . '</b><div class="cvcart">' . $who . '</div></td>';
    echo '<td>' . ($r['phone'] ? '<a href="tel:' . e($r['phone']) . '"><b>' . e($r['phone']) . '</b></a>' : '<span class="muted">no phone yet</span>') . ($r['email'] ? '<div class="cvcart">' . e($r['email']) . '</div>' : '') . ($r['address'] ? '<div class="cvcart">' . e($r['address']) . '</div>' : '') . '</td>';
    echo '<td class="mono">' . e($r['ip']) . '<div class="cvcart">' . e($r['device']) . '</div></td><td><b>' . money($r['cart_total']) . '</b> · ' . (int)$r['items_count'] . ' item(s)<div class="cvcart">' . $cart . '</div></td>';
    echo '<td><span class="pill s-' . $r['st'] . '">' . ucfirst($r['st']) . '</span>' . ($r['order_id'] ? '<div class="cvcart"><a href="orders.php?view=' . (int)$r['order_id'] . '">order #' . (int)$r['order_id'] . '</a></div>' : '') . ($r['contacted'] ? '<div class="cvcart">✅ contacted</div>' : '') . '</td><td class="acts">';
    if ($wa) echo '<a class="abtn sm" style="background:#25d366" target="_blank" rel="noopener" href="' . e($wa) . '">💬 WhatsApp</a>';
    if ($r['phone']) echo '<a class="abtn sm ghost" href="tel:' . e($r['phone']) . '">📞 Call</a>';
    echo '<form method="post">' . csrf_field() . '<input type="hidden" name="id" value="' . $r['id'] . '"><input type="hidden" name="back" value="' . $back . '"><button class="abtn sm ghost" name="contacted" value="1">' . ($r['contacted'] ? 'Unmark' : 'Mark contacted') . '</button></form><form method="post" onsubmit="return confirm(\'Delete this record?\')">' . csrf_field() . '<input type="hidden" name="id" value="' . $r['id'] . '"><input type="hidden" name="back" value="' . $back . '"><button class="abtn sm danger" name="del" value="1">🗑</button></form></td></tr>';
}
if (!$rs) echo '<tr><td colspan="7" class="empty">No checkout visits in this period.</td></tr>';
echo '</table></div>'; if ($pages > 1) { echo '<div class="pager">'; for ($i = 1; $i <= $pages; $i++) echo '<a class="' . ($i == $pg ? 'on' : '') . '" href="?' . e(http_build_query(array_merge($_GET, ['page' => $i]))) . '">' . $i . '</a>'; echo '</div>'; }
echo '<form method="post" style="margin-top:12px" onsubmit="return confirm(\'Remove records older than 90 days?\')">' . csrf_field() . '<button class="abtn sm ghost" name="clear_old" value="1">Clear records older than 90 days</button></form></div>';
admin_end();
