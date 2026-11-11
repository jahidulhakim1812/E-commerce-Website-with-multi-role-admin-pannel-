<?php
require '_layout.php'; admin_start('Finance — Expenses, Profit & Loss', 'finance');
$tab = $_GET['tab'] ?? 'overview';
echo '<div class="tabs-a"><a class="' . ($tab === 'overview' ? 'on' : '') . '" href="?tab=overview">📊 Profit &amp; Loss</a><a class="' . ($tab === 'expenses' ? 'on' : '') . '" href="?tab=expenses">🧾 Expenses</a></div>';
if ($tab === 'expenses') {
    $cats = ['Marketing' => 'Marketing / Ads', 'Technology' => 'Technology / Hosting', 'Salary' => 'Salary', 'Rent' => 'Rent & Utilities', 'Packaging' => 'Packaging', 'Delivery' => 'Delivery / Courier', 'Inventory' => 'Inventory Purchase', 'Other' => 'Other'];
    crud(['table' => 'expenses', 'title' => 'Expenses', 'single' => 'Expense', 'keep' => ['tab' => 'expenses'], 'search' => ['title', 'category', 'note'], 'order' => 'expense_date DESC, id DESC', 'defaults' => ['expense_date' => date('Y-m-d')],
     'fields' => [['name' => 'title', 'label' => 'Expense Title', 'req' => true, 'half' => true, 'list' => true], ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'opts' => $cats, 'half' => true, 'list' => true], ['name' => 'amount', 'label' => 'Amount', 'type' => 'number', 'half' => true, 'list' => true, 'money' => true], ['name' => 'expense_date', 'label' => 'Date', 'type' => 'date', 'half' => true, 'list' => true], ['name' => 'note', 'label' => 'Note']]]);
    admin_end(); exit;
}
$from = $_GET['from'] ?? date('Y-m-01'); $to = $_GET['to'] ?? date('Y-m-d');
$R = " o.status IN " . REV_STATUS . " AND DATE(o.created_at) BETWEEN ? AND ? ";
$rev = (float)val("SELECT COALESCE(SUM(o.total),0) FROM orders o WHERE $R", [$from, $to]); $ship = (float)val("SELECT COALESCE(SUM(o.shipping),0) FROM orders o WHERE $R", [$from, $to]);
$cogs = (float)val("SELECT COALESCE(SUM(oi.cost*oi.qty),0) FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE $R", [$from, $to]);
$exp = (float)val('SELECT COALESCE(SUM(amount),0) FROM expenses WHERE expense_date BETWEEN ? AND ?', [$from, $to]); $gross = $rev - $cogs; $net = $gross - $exp;
$byCat = rows('SELECT category,SUM(amount) s FROM expenses WHERE expense_date BETWEEN ? AND ? GROUP BY category ORDER BY s DESC', [$from, $to]);
$dailyProfit = rows("SELECT s.day,s.rev,COALESCE(c.cogs,0) cogs FROM
 (SELECT DATE(o.created_at) day,SUM(o.total) rev FROM orders o WHERE o.status IN " . REV_STATUS . " AND DATE(o.created_at) BETWEEN ? AND ? GROUP BY DATE(o.created_at)) s
 LEFT JOIN (SELECT DATE(o.created_at) day,SUM(oi.cost*oi.qty) cogs FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE o.status IN " . REV_STATUS . " AND DATE(o.created_at) BETWEEN ? AND ? GROUP BY DATE(o.created_at)) c ON c.day=s.day
 ORDER BY s.day", [$from, $to, $from, $to]);
$dailyExpenses = rows('SELECT expense_date day,SUM(amount) amount FROM expenses WHERE expense_date BETWEEN ? AND ? GROUP BY expense_date ORDER BY expense_date', [$from, $to]);
$dailyProfitByDate = array_column($dailyProfit, null, 'day');
$dailyExpensesByDate = array_column($dailyExpenses, 'amount', 'day');
$chartDates = array_values(array_unique(array_merge(array_keys($dailyProfitByDate), array_keys($dailyExpensesByDate))));
sort($chartDates, SORT_STRING);
$grossProfitSeries = $netProfitSeries = $expenseSeries = [];
foreach ($chartDates as $day) {
    $sales = $dailyProfitByDate[$day] ?? ['rev' => 0, 'cogs' => 0];
    $grossForDay = (float)$sales['rev'] - (float)$sales['cogs'];
    $expenseForDay = (float)($dailyExpensesByDate[$day] ?? 0);
    $grossProfitSeries[] = $grossForDay;
    $netProfitSeries[] = $grossForDay - $expenseForDay;
    $expenseSeries[] = $expenseForDay;
}
$m = rows("SELECT DATE_FORMAT(o.created_at,'%Y-%m') ym, SUM(o.total) rev, (SELECT COALESCE(SUM(oi.cost*oi.qty),0) FROM order_items oi JOIN orders o2 ON o2.id=oi.order_id WHERE o2.status IN " . REV_STATUS . " AND DATE_FORMAT(o2.created_at,'%Y-%m')=DATE_FORMAT(o.created_at,'%Y-%m')) cogs FROM orders o WHERE o.status IN " . REV_STATUS . " GROUP BY ym ORDER BY ym DESC LIMIT 12");
$em = array_column(rows("SELECT DATE_FORMAT(expense_date,'%Y-%m') ym, SUM(amount) s FROM expenses GROUP BY ym"), 's', 'ym');
echo '<form class="filt panel-h"><span>From</span><input type="date" name="from" value="' . e($from) . '"><span>To</span><input type="date" name="to" value="' . e($to) . '"><button class="abtn sm">Apply</button></form>';
echo '<div class="cards"><div class="kpi c3d g"><span class="ki">💵</span><div><small>Revenue (incl. shipping)</small><b>' . money($rev) . '</b></div></div><div class="kpi c3d o"><span class="ki">📦</span><div><small>Cost of Goods Sold</small><b>' . money($cogs) . '</b></div></div><div class="kpi c3d b"><span class="ki">🧾</span><div><small>Operating Expenses</small><b>' . money($exp) . '</b></div></div><div class="kpi c3d ' . ($net >= 0 ? 'g' : 'r') . '"><span class="ki">' . ($net >= 0 ? '📈' : '📉') . '</span><div><small>' . ($net >= 0 ? 'NET PROFIT' : 'NET LOSS') . '</small><b>' . money(abs($net)) . '</b></div></div></div>';
echo '<div class="grid2"><div class="panel"><h3>Summary</h3><table class="atbl"><tr><td>Total Revenue</td><td class="r">' . money($rev) . '</td></tr><tr><td>− Cost of Goods Sold</td><td class="r">' . money($cogs) . '</td></tr><tr><td><b>Gross Profit</b></td><td class="r"><b>' . money($gross) . '</b></td></tr><tr><td>− Expenses</td><td class="r">' . money($exp) . '</td></tr><tr class="' . ($net >= 0 ? 'profit' : 'loss') . '"><td><b>' . ($net >= 0 ? 'Net Profit' : 'Net Loss') . '</b></td><td class="r"><b>' . money($net) . '</b></td></tr><tr><td class="muted">Profit margin</td><td class="r muted">' . ($rev ? round($net / $rev * 100, 1) : 0) . '%</td></tr></table><p class="muted">Only confirmed/processing/shipped/delivered orders count as revenue. Cancelled and pending orders are excluded.</p></div>
<div class="panel"><h3>Expenses by Category</h3><div class="chart-box sm"><canvas id="ec"></canvas></div></div></div>';
echo '<div class="grid2 finance-charts"><div class="panel"><div class="panel-h"><h3>Profit Trend</h3><span class="muted">Gross and net profit by day</span></div><div class="chart-box"><canvas id="profitChart"></canvas></div></div>
<div class="panel"><div class="panel-h"><h3>Expense Trend</h3><span class="muted">Operating expenses by day</span></div><div class="chart-box"><canvas id="expenseChart"></canvas></div></div></div>';
echo '<div class="panel"><h3>Monthly Profit &amp; Loss</h3><div class="table-wrap"><table class="atbl"><tr><th>Month</th><th>Revenue</th><th>COGS</th><th>Expenses</th><th>Net</th></tr>'; foreach ($m as $r) { $n = $r['rev'] - $r['cogs'] - ($em[$r['ym']] ?? 0); echo '<tr><td>' . e($r['ym']) . '</td><td>' . money($r['rev']) . '</td><td>' . money($r['cogs']) . '</td><td>' . money($em[$r['ym']] ?? 0) . '</td><td class="' . ($n >= 0 ? 'ok-t' : 'bad-t') . '"><b>' . ($n >= 0 ? '+' : '−') . money(abs($n)) . '</b></td></tr>'; } if (!$m) echo '<tr><td colspan="5" class="empty">No sales yet.</td></tr>'; echo '</table></div></div>';
?><script>
var financeText=getComputedStyle(document.documentElement).getPropertyValue('--muted'),financeGrid=getComputedStyle(document.documentElement).getPropertyValue('--line');
new Chart(document.getElementById('ec'),{type:'doughnut',data:{labels:<?= json_encode(array_column($byCat, 'category')) ?>,datasets:[{data:<?= json_encode(array_map('floatval', array_column($byCat, 's'))) ?>,backgroundColor:['#2b9d5c','#e8923a','#3b82f6','#8b5cf6','#06b6d4','#ef4444','#eab308','#64748b'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,cutout:'60%',plugins:{legend:{position:'right',labels:{color:financeText,usePointStyle:true}}}}});
new Chart(document.getElementById('profitChart'),{type:'line',data:{labels:<?= json_encode($chartDates) ?>,datasets:[{label:'Gross profit',data:<?= json_encode($grossProfitSeries) ?>,borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,.10)',fill:true,tension:.32,pointRadius:2,borderWidth:2},{label:'Net profit',data:<?= json_encode($netProfitSeries) ?>,borderColor:'#2b9d5c',backgroundColor:'rgba(43,157,92,.14)',fill:true,tension:.32,pointRadius:2,borderWidth:2}]},options:{responsive:true,maintainAspectRatio:false,interaction:{mode:'index',intersect:false},plugins:{legend:{labels:{color:financeText,usePointStyle:true}}},scales:{x:{grid:{display:false},ticks:{color:financeText,maxTicksLimit:8}},y:{grid:{color:financeGrid},ticks:{color:financeText},beginAtZero:false}}}});
new Chart(document.getElementById('expenseChart'),{type:'bar',data:{labels:<?= json_encode($chartDates) ?>,datasets:[{label:'Expenses',data:<?= json_encode($expenseSeries) ?>,backgroundColor:'rgba(232,146,58,.72)',borderColor:'#e8923a',borderWidth:1,borderRadius:6,maxBarThickness:28}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{color:financeText,maxTicksLimit:8}},y:{grid:{color:financeGrid},ticks:{color:financeText},beginAtZero:true}}}});
</script>
<?php admin_end();
