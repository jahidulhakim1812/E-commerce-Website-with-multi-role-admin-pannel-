<?php
require '_layout.php'; admin_start('Customer Profiles', 'customers');

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $customer = row("SELECT * FROM users WHERE id=? AND role='customer'", [$id]);
    if (!$customer) { echo '<div class="panel"><p>Customer not found.</p><a class="abtn ghost" href="customer_profiles.php">← Back to customers</a></div>'; admin_end(); exit; }
    $orders = rows('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC', [$id]);
    $orderCount = count($orders);
    $spent = (float)val("SELECT COALESCE(SUM(total),0) FROM orders WHERE user_id=? AND status IN " . REV_STATUS, [$id]);
    echo '<div class="panel"><div class="panel-h"><h3>' . e($customer['name']) . '</h3><a class="abtn ghost" href="customer_profiles.php">← All Customers</a></div>';
    echo '<div class="grid2"><div><p><b>Email:</b> ' . (preg_match('/@guest\\.local$/', $customer['email']) ? '—' : e($customer['email'])) . '</p><p><b>Phone:</b> ' . e($customer['phone'] ?: '—') . '</p><p><b>Address:</b> ' . e(trim(($customer['address'] ?: '') . ', ' . ($customer['city'] ?: ''), ', ')) . '</p><p><b>Account type:</b> ' . ($customer['is_guest'] ? 'Guest checkout' : 'Registered customer') . '</p><p><b>Account status:</b> ' . ($customer['status'] ? 'Active' : 'Inactive') . '</p><p><b>Customer since:</b> ' . date('d M Y', strtotime($customer['created_at'])) . '</p></div>';
    echo '<div><p><b>Total orders:</b> ' . $orderCount . '</p><p><b>Completed-order total:</b> ' . money($spent) . '</p><p><b>Last login:</b> ' . ($customer['last_login'] ? date('d M Y h:i A', strtotime($customer['last_login'])) : 'Never') . '</p><p><a class="abtn sm" href="customers.php?a=edit&id=' . (int)$customer['id'] . '">Edit Customer</a></p></div></div></div>';
    echo '<div class="panel"><h3>Purchase History</h3><div class="table-wrap"><table class="atbl"><tr><th>Order</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr>';
    foreach ($orders as $order) echo '<tr><td class="mono">' . e($order['order_no']) . '</td><td>' . date('d M Y h:i A', strtotime($order['created_at'])) . '</td><td>' . money($order['total']) . '</td><td>' . e(strtoupper($order['payment_method'])) . ' · ' . e($order['payment_status']) . '</td><td><span class="pill s-' . e($order['status']) . '">' . e(ucfirst($order['status'])) . '</span></td><td><a class="abtn sm" href="orders.php?view=' . (int)$order['id'] . '">View Order</a></td></tr>';
    if (!$orders) echo '<tr><td colspan="6" class="empty">No orders found for this customer.</td></tr>';
    echo '</table></div></div>';
    admin_end(); exit;
}

$search = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$where = "role='customer'";
$args = [];
if ($search !== '') { $where .= ' AND (name LIKE ? OR phone LIKE ?)'; $args = ["%$search%", "%$search%"]; }
$total = (int)val("SELECT COUNT(*) FROM users WHERE $where", $args);
$customers = rows("SELECT id,name,email,phone,city,is_guest,created_at FROM users WHERE $where ORDER BY id DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage), $args);
echo '<div class="panel"><div class="panel-h"><h3>Customer Profiles <small>(' . $total . ')</small></h3><form class="filt" method="get"><input name="q" value="' . e($search) . '" placeholder="Search customer name or phone"><button class="abtn sm">Search</button><a class="abtn sm ghost" href="customer_profiles.php">Clear</a></form></div><div class="table-wrap"><table class="atbl"><tr><th>Name</th><th>Phone</th><th>Email</th><th>City</th><th>Joined</th><th></th></tr>';
foreach ($customers as $customer) echo '<tr><td>' . e($customer['name']) . ($customer['is_guest'] ? ' <span class="guesttag">from order</span>' : '') . '</td><td>' . e($customer['phone'] ?: '—') . '</td><td>' . (preg_match('/@guest\\.local$/', $customer['email']) ? '—' : e($customer['email'])) . '</td><td>' . e($customer['city'] ?: '—') . '</td><td>' . date('d M Y', strtotime($customer['created_at'])) . '</td><td><a class="abtn sm" href="customer_profiles.php?id=' . (int)$customer['id'] . '">View Profile</a></td></tr>';
if (!$customers) echo '<tr><td colspan="6" class="empty">No customers found.</td></tr>';
echo '</table></div>';
$pages = max(1, (int)ceil($total / $perPage));
if ($pages > 1) { echo '<div class="pager">'; for ($i = 1; $i <= $pages; $i++) echo '<a class="' . ($i === $page ? 'on' : '') . '" href="?' . http_build_query(['q' => $search, 'page' => $i]) . '">' . $i . '</a>'; echo '</div>'; }
echo '</div>';
admin_end();
