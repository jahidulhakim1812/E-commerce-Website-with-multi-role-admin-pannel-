<?php
require '_layout.php';
admin_start('Export Center');

$validDate = static function ($value) {
    if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) return false;
    [$year, $month, $day] = array_map('intval', explode('-', $value));
    return checkdate($month, $day, $year);
};
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$dateError = '';
if (!$validDate($from) || !$validDate($to) || $from > $to) {
    $dateError = 'Choose a valid date range (start date must not be after end date).';
    $from = date('Y-m-01');
    $to = date('Y-m-d');
}

$datasets = [
    'customers' => [
        'title' => 'Customer List', 'description' => 'Customer contact and account details.',
        'permission' => 'customers', 'date_column' => 'u.created_at',
        'headers' => ['Name', 'Email', 'Phone', 'City', 'Address', 'Account Status', 'Joined'],
        'query' => fn() => rows("SELECT u.name,u.email,u.phone,u.city,u.address,u.status,u.created_at FROM users u WHERE u.role='customer' AND DATE(u.created_at) BETWEEN ? AND ? ORDER BY u.id DESC", [$from, $to]),
        'values' => fn($r) => [$r['name'], preg_match('/@guest\.local$/', $r['email']) ? '' : $r['email'], $r['phone'], $r['city'], $r['address'], $r['status'] ? 'Active' : 'Inactive', $r['created_at']],
    ],
    'products' => [
        'title' => 'Product List & Inventory', 'description' => 'Product catalog, pricing, stock, and sales counts.',
        'permission' => 'products', 'date_column' => 'p.created_at',
        'headers' => ['Product', 'Category', 'SKU / Slug', 'Price', 'Old Price', 'Cost Price', 'Stock', 'Sold', 'Status', 'Created'],
        'query' => fn() => rows("SELECT p.name_en,COALESCE(c.name_en,'Uncategorized') category,p.slug,p.price,p.old_price,p.cost_price,p.stock,p.sold,p.status,p.created_at FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE DATE(p.created_at) BETWEEN ? AND ? ORDER BY p.id DESC", [$from, $to]),
        'values' => fn($r) => [$r['name_en'], $r['category'], $r['slug'], $r['price'], $r['old_price'], $r['cost_price'], $r['stock'], $r['sold'], $r['status'] ? 'Active' : 'Inactive', $r['created_at']],
    ],
    'subscribers' => [
        'title' => 'Subscriber List', 'description' => 'Newsletter email addresses, subscription status, and source.',
        'permission' => 'customers', 'date_column' => 'n.created_at',
        'headers' => ['Email', 'Status', 'Source', 'Subscribed'],
        'query' => fn() => rows('SELECT n.email,n.status,n.source,n.created_at FROM newsletter n WHERE DATE(n.created_at) BETWEEN ? AND ? ORDER BY n.id DESC', [$from, $to]),
        'values' => fn($r) => [$r['email'], $r['status'] ? 'Active' : 'Unsubscribed', $r['source'], $r['created_at']],
    ],
    'orders' => [
        'title' => 'Orders', 'description' => 'Order, customer, payment, and fulfillment details.',
        'permission' => 'orders', 'date_column' => 'o.created_at',
        'headers' => ['Order Number', 'Date', 'Customer', 'Phone', 'Email', 'City', 'Subtotal', 'Shipping', 'Discount', 'Total', 'Payment Method', 'Payment Status', 'Order Status'],
        'query' => fn() => rows('SELECT o.order_no,o.created_at,o.name,o.phone,o.email,o.city,o.subtotal,o.shipping,o.discount,o.total,o.payment_method,o.payment_status,o.status FROM orders o WHERE DATE(o.created_at) BETWEEN ? AND ? ORDER BY o.id DESC', [$from, $to]),
        'values' => fn($r) => array_values($r),
    ],
    'sales_daily' => [
        'title' => 'Daily Sales Report', 'description' => 'Daily order count and revenue from recognized sales.',
        'permission' => 'reports', 'date_column' => 'o.created_at',
        'headers' => ['Date', 'Orders', 'Revenue'],
        'query' => fn() => rows("SELECT DATE(o.created_at) sale_date,COUNT(*) orders,SUM(o.total) revenue FROM orders o WHERE o.status IN " . REV_STATUS . " AND DATE(o.created_at) BETWEEN ? AND ? GROUP BY DATE(o.created_at) ORDER BY sale_date", [$from, $to]),
        'values' => fn($r) => array_values($r),
    ],
    'sales_products' => [
        'title' => 'Product Sales Report', 'description' => 'Units sold, revenue, and product profit for the chosen period.',
        'permission' => 'reports', 'date_column' => 'o.created_at',
        'headers' => ['Product', 'Quantity Sold', 'Revenue', 'Profit'],
        'query' => fn() => rows("SELECT oi.name product,SUM(oi.qty) quantity,SUM(oi.qty*oi.price) revenue,SUM(oi.qty*(oi.price-oi.cost)) profit FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE o.status IN " . REV_STATUS . " AND DATE(o.created_at) BETWEEN ? AND ? GROUP BY oi.name ORDER BY revenue DESC", [$from, $to]),
        'values' => fn($r) => array_values($r),
    ],
    'profit_loss' => [
        'title' => 'Profit & Loss Summary', 'description' => 'Revenue, product costs, expenses, gross profit, and net result.',
        'permission' => 'finance', 'date_column' => '',
        'headers' => ['From', 'To', 'Revenue', 'Cost of Goods Sold', 'Expenses', 'Gross Profit', 'Net Profit / (Loss)'],
        'query' => function () use ($from, $to) {
            $revenue = (float)val("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN " . REV_STATUS . " AND DATE(created_at) BETWEEN ? AND ?", [$from, $to]);
            $cogs = (float)val("SELECT COALESCE(SUM(oi.cost*oi.qty),0) FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE o.status IN " . REV_STATUS . " AND DATE(o.created_at) BETWEEN ? AND ?", [$from, $to]);
            $expenses = (float)val('SELECT COALESCE(SUM(amount),0) FROM expenses WHERE expense_date BETWEEN ? AND ?', [$from, $to]);
            return [['from' => $from, 'to' => $to, 'revenue' => $revenue, 'cogs' => $cogs, 'expenses' => $expenses, 'gross' => $revenue - $cogs, 'net' => $revenue - $cogs - $expenses]];
        },
        'values' => fn($r) => array_values($r),
    ],
    'expenses' => [
        'title' => 'Expense List', 'description' => 'Recorded operating expenses for the chosen period.',
        'permission' => 'finance', 'date_column' => 'e.expense_date',
        'headers' => ['Date', 'Title', 'Category', 'Amount', 'Note'],
        'query' => fn() => rows('SELECT e.expense_date,e.title,e.category,e.amount,e.note FROM expenses e WHERE e.expense_date BETWEEN ? AND ? ORDER BY e.expense_date DESC,e.id DESC', [$from, $to]),
        'values' => fn($r) => array_values($r),
    ],
    'visitors' => [
        'title' => 'Visitor Analytics', 'description' => 'Visit time, IP address, page, device, and referral source.',
        'permission' => 'visitors', 'date_column' => 'v.visit_date',
        'headers' => ['Time', 'IP Address', 'Page', 'Device', 'Referrer'],
        'query' => fn() => rows('SELECT v.created_at,v.ip,v.page,v.device,v.referrer FROM visitors v WHERE v.visit_date BETWEEN ? AND ? ORDER BY v.id DESC', [$from, $to]),
        'values' => fn($r) => array_values($r),
    ],
];

$available = array_filter($datasets, fn($dataset) => can($dataset['permission']));
$datasetKey = $_GET['dataset'] ?? '';
$format = $_GET['format'] ?? '';
if (isset($_GET['download'])) {
    if ($dateError) {
        http_response_code(400);
        echo '<div class="panel"><h3>Invalid date range</h3><p>Please choose valid start and end dates.</p></div>';
        admin_end();
        exit;
    }
    if (!isset($available[$datasetKey])) {
        http_response_code(403);
        echo '<div class="panel"><h3>Export unavailable</h3><p>You do not have permission to export this data.</p></div>';
        admin_end();
        exit;
    }
    if (!in_array($format, ['csv', 'doc'], true)) {
        http_response_code(400);
        echo '<div class="panel"><h3>Unsupported format</h3><p>Choose CSV or Word document.</p></div>';
        admin_end();
        exit;
    }

    $dataset = $available[$datasetKey];
    $rows = $dataset['query']();
    $fileBase = 'drynutri-' . $datasetKey . '-' . date('Ymd');
    ob_end_clean();
    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $fileBase . '.csv"');
        $output = fopen('php://output', 'w');
        if ($output === false) throw new RuntimeException('Unable to open export output.');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, $dataset['headers']);
        foreach ($rows as $record) {
            $values = ($dataset['values'])($record);
            $values = array_map(static function ($value) {
                if (!is_numeric($value) && preg_match('/^[\s]*[=+\-@]/u', (string)$value)) return "'" . $value;
                return $value;
            }, $values);
            fputcsv($output, $values);
        }
        fclose($output);
        exit;
    }

    header('Content-Type: application/msword; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $fileBase . '.doc"');
    echo "\xEF\xBB\xBF<!doctype html><html><head><meta charset=\"UTF-8\"><style>body{font-family:Arial,sans-serif;color:#222}h1{color:#245c38}p{color:#555}table{border-collapse:collapse;width:100%}th,td{border:1px solid #bbb;padding:7px;text-align:left}th{background:#e8f2e7}</style></head><body>";
    echo '<h1>' . e($dataset['title']) . '</h1><p>' . e($from) . ' to ' . e($to) . ' · ' . count($rows) . ' records</p><table><thead><tr>';
    foreach ($dataset['headers'] as $header) echo '<th>' . e($header) . '</th>';
    echo '</tr></thead><tbody>';
    foreach ($rows as $record) {
        echo '<tr>';
        foreach (($dataset['values'])($record) as $value) echo '<td>' . nl2br(e($value)) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></body></html>';
    exit;
}

?>
<div class="export-center">
    <section class="welcome">
        <div class="w-txt">
            <h1>Export Center</h1>
            <p>Download your reports and lists as CSV or Word documents.</p>
        </div>
        <form class="range" method="get">
            <label for="export-from">From</label><input id="export-from" type="date" name="from" value="<?= e($from) ?>">
            <label for="export-to">To</label><input id="export-to" type="date" name="to" value="<?= e($to) ?>">
            <button class="abtn sm" type="submit">Apply dates</button>
        </form>
    </section>
    <?php if ($dateError): ?><div class="alert err"><?= e($dateError) ?></div><?php endif; ?>
    <p class="export-note">The selected date range applies to each export. Customer and subscriber exports contain personal information; share downloaded files carefully.</p>
    <?php if (!$available): ?>
        <div class="panel"><h3>No exports available</h3><p class="muted">Your staff account does not currently have permission to export data.</p></div>
    <?php else: ?>
        <div class="export-grid">
            <?php foreach ($available as $key => $dataset): ?>
                <article class="panel export-card">
                    <div class="export-icon"><?= $key === 'customers' ? '♙' : ($key === 'products' ? '▣' : ($key === 'subscribers' ? '✉' : ($key === 'visitors' ? '◉' : ($key === 'orders' ? '▤' : '▥')))) ?></div>
                    <div class="export-card-body">
                        <h2><?= e($dataset['title']) ?></h2>
                        <p><?= e($dataset['description']) ?></p>
                        <div class="export-actions">
                            <a class="abtn sm" href="?<?= e(http_build_query(['download' => 1, 'dataset' => $key, 'format' => 'csv', 'from' => $from, 'to' => $to])) ?>">Download CSV</a>
                            <a class="abtn sm ghost" href="?<?= e(http_build_query(['download' => 1, 'dataset' => $key, 'format' => 'doc', 'from' => $from, 'to' => $to])) ?>">Word document</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php admin_end(); ?>
