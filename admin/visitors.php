<?php
require '_layout.php'; admin_start('Visitor Analytics', 'visitors');
$view = in_array($_GET['view'] ?? '', ['ip', 'recent', 'pages'], true) ? $_GET['view'] : 'overview';
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-29 days')); $to = $_GET['to'] ?? date('Y-m-d'); $ip = trim($_GET['ip'] ?? ''); $dev = $_GET['device'] ?? '';
$w = 'visit_date BETWEEN ? AND ?'; $a = [$from, $to]; if ($ip !== '') { $w .= ' AND ip LIKE ?'; $a[] = "%$ip%"; } if (in_array($dev, ['desktop', 'mobile', 'tablet'])) { $w .= ' AND device=?'; $a[] = $dev; }
if (isset($_GET['export'])) { ob_end_clean(); header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename=visitors-' . date('Ymd') . '.csv'); $f = fopen('php://output', 'w'); fputcsv($f, ['Time', 'IP', 'Page', 'Device', 'Referrer']); foreach (rows("SELECT * FROM visitors WHERE $w ORDER BY id DESC LIMIT 20000", $a) as $r) fputcsv($f, [$r['created_at'], $r['ip'], $r['page'], $r['device'], $r['referrer']]); exit; }
$views = (int)val("SELECT COUNT(*) FROM visitors WHERE $w", $a); $uniq = (int)val("SELECT COUNT(DISTINCT ip) FROM visitors WHERE $w", $a);
$tv = (int)val('SELECT COUNT(*) FROM visitors WHERE visit_date=CURDATE()'); $tu = (int)val('SELECT COUNT(DISTINCT ip) FROM visitors WHERE visit_date=CURDATE()'); $allu = (int)val('SELECT COUNT(DISTINCT ip) FROM visitors');
$daily = rows("SELECT visit_date d, COUNT(*) v, COUNT(DISTINCT ip) u FROM visitors WHERE $w GROUP BY d ORDER BY d", $a); $dm = []; foreach ($daily as $r) $dm[$r['d']] = $r;
$labels = $vv = $uu = []; for ($t = strtotime($from); $t <= strtotime($to) && count($labels) < 400; $t += 86400) { $d = date('Y-m-d', $t); $labels[] = date('d M', $t); $vv[] = (int)($dm[$d]['v'] ?? 0); $uu[] = (int)($dm[$d]['u'] ?? 0); }
$pagesTop = rows("SELECT page, COUNT(*) c FROM visitors WHERE $w GROUP BY page ORDER BY c DESC LIMIT 8", $a);
$byIp = rows("SELECT ip, COUNT(*) c, MIN(created_at) f, MAX(created_at) l, MAX(device) dv FROM visitors WHERE $w GROUP BY ip ORDER BY l DESC LIMIT 25", $a);
$pg = max(1, (int)($_GET['page'] ?? 1)); $per = 20; $recent = rows("SELECT * FROM visitors WHERE $w ORDER BY id DESC LIMIT $per OFFSET " . (($pg - 1) * $per), $a); $pages = max(1, ceil($views / $per));
echo '<form class="filt panel-h"><input type="hidden" name="view" value="' . e($view) . '"><span>From</span><input type="date" name="from" value="' . e($from) . '"><span>To</span><input type="date" name="to" value="' . e($to) . '"><input name="ip" value="' . e($ip) . '" placeholder="Filter by IP address"><select name="device"><option value="">All devices</option>'; foreach (['desktop', 'mobile', 'tablet'] as $d) echo '<option ' . ($dev === $d ? 'selected' : '') . '>' . $d . '</option>'; echo '</select><button class="abtn sm">Filter</button><a class="abtn sm ghost" href="visitors.php?view=' . e($view) . '">Reset</a><a class="abtn sm ghost" href="?' . e(http_build_query(array_merge($_GET, ['export' => 1]))) . '">⬇ CSV</a></form>';
if ($view === 'overview') {
    echo '<div class="cards"><div class="kpi c3d p"><span class="ki">👁</span><div><small>Unique Visitors (IP)</small><b>' . $uniq . '</b></div></div><div class="kpi c3d b"><span class="ki">📄</span><div><small>Page Views</small><b>' . $views . '</b></div></div><div class="kpi c3d g"><span class="ki">📅</span><div><small>Today (unique / views)</small><b>' . $tu . ' / ' . $tv . '</b></div></div><div class="kpi c3d o"><span class="ki">🌍</span><div><small>All-time Unique IPs</small><b>' . $allu . '</b></div></div></div>';
    echo '<div class="panel"><div class="panel-h"><h3>Traffic Trend</h3></div><div class="chart-box"><canvas id="vc"></canvas></div></div>';
}
if ($view === 'overview' || $view === 'pages') {
    echo '<div class="panel"><h3>Top Pages</h3><table class="atbl">'; foreach ($pagesTop as $r) echo '<tr><td>' . e($r['page']) . '</td><td class="r"><b>' . $r['c'] . '</b></td></tr>'; if (!$pagesTop) echo '<tr><td colspan="2" class="empty">No page visits for this filter.</td></tr>'; echo '</table></div>';
}
if ($view === 'overview' || $view === 'ip') {
    echo '<div class="panel"><h3>Visitors by IP Address</h3><div class="table-wrap"><table class="atbl"><tr><th>IP</th><th>Visits</th><th>Device</th><th>Last Seen</th></tr>'; foreach ($byIp as $r) echo '<tr><td class="mono"><a href="visitors.php?view=ip&amp;ip=' . urlencode($r['ip']) . '">' . e($r['ip']) . '</a></td><td>' . $r['c'] . '</td><td>' . e($r['dv']) . '</td><td>' . date('d M h:i A', strtotime($r['l'])) . '</td></tr>'; if (!$byIp) echo '<tr><td colspan="4" class="empty">No visitors for this filter.</td></tr>'; echo '</table></div></div>';
}
if ($view === 'overview' || $view === 'recent') {
    echo '<div class="panel"><div class="panel-h"><h3>Recent Visits</h3></div><div class="table-wrap"><table class="atbl"><tr><th>Time</th><th>IP Address</th><th>Page</th><th>Device</th><th>Referrer</th></tr>'; foreach ($recent as $r) echo '<tr><td>' . date('d M Y h:i A', strtotime($r['created_at'])) . '</td><td class="mono">' . e($r['ip']) . '</td><td>' . e($r['page']) . '</td><td>' . e($r['device']) . '</td><td>' . e(mb_strimwidth($r['referrer'] ?: '— direct', 0, 40, '…')) . '</td></tr>'; if (!$recent) echo '<tr><td colspan="5" class="empty">No visits recorded for this filter.</td></tr>'; echo '</table></div>';
    if ($pages > 1) { echo '<div class="pager">'; for ($i = max(1, $pg - 4); $i <= min($pages, $pg + 4); $i++) echo '<a class="' . ($i == $pg ? 'on' : '') . '" href="?' . e(http_build_query(array_merge($_GET, ['page' => $i]))) . '">' . $i . '</a>'; echo '</div>'; } echo '</div>';
}
if ($view === 'overview') {
?><script>var mc=getComputedStyle(document.documentElement).getPropertyValue('--muted');new Chart(document.getElementById('vc'),{type:'line',data:{labels:<?= json_encode($labels) ?>,datasets:[{label:'Page views',data:<?= json_encode($vv) ?>,borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,.12)',fill:true,tension:.3},{label:'Unique visitors',data:<?= json_encode($uu) ?>,borderColor:'#2b9d5c',tension:.3}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{labels:{color:mc}}},scales:{x:{ticks:{color:mc,maxTicksLimit:10}},y:{ticks:{color:mc},beginAtZero:true}}}});</script>
<?php }
admin_end();
