<?php
require '_layout.php';
admin_start('Return Requests', 'orders');
ensure_return_requests();
$statuses = ['pending', 'approved', 'rejected', 'completed'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $idValue = $_POST['id'] ?? 0;
    $id = is_scalar($idValue) ? (int)$idValue : 0;
    $status = $_POST['status'] ?? '';
    $noteValue = $_POST['admin_note'] ?? '';
    $note = is_string($noteValue) ? trim($noteValue) : '';
    if (!$id || !is_string($status) || !in_array($status, $statuses, true) || strlen($note) > 5000) {
        flash('Invalid return request update.', 'err');
    } elseif (!row('SELECT id FROM return_requests WHERE id=?', [$id])) {
        flash('Return request not found.', 'err');
    } else {
        q('UPDATE return_requests SET status=?,admin_note=? WHERE id=?', [$status, $note ?: null, $id]);
        flash('Return request updated.');
    }
    redirect('returns.php?view=' . $id);
}
if (isset($_GET['view'])) {
    $request = row('SELECT rr.*,o.order_no,o.name customer_name,o.phone customer_phone,o.email customer_email,u.email account_email
        FROM return_requests rr
        JOIN orders o ON o.id=rr.order_id
        LEFT JOIN users u ON u.id=rr.user_id
        WHERE rr.id=?', [(int)$_GET['view']]);
    if (!$request) { echo '<div class="panel"><p>Return request not found.</p><a class="abtn ghost" href="returns.php">← All Requests</a></div>'; admin_end(); exit; }
    echo '<div class="panel-h"><h3>Return Request #' . (int)$request['id'] . ' <span class="pill s-' . e($request['status']) . '">' . ucfirst(e($request['status'])) . '</span></h3><a class="abtn ghost" href="returns.php">← All Requests</a></div>';
    echo '<div class="grid2"><div class="panel"><h3>Product and Customer</h3><p><b>' . e($request['product_name']) . '</b>' . ($request['product_variant'] ? ' · ' . e($request['product_variant']) : '') . '<br>Order: <a href="orders.php?view=' . (int)$request['order_id'] . '">' . e($request['order_no']) . '</a><br>Customer: ' . e($request['customer_name']) . '<br>Phone: ' . e($request['customer_phone'] ?: '—') . '<br>Email: ' . e($request['account_email']) . '<br>Requested: ' . date('d M Y h:i A', strtotime($request['created_at'])) . '</p><h4>Reason</h4><p>' . nl2br(e($request['reason'])) . '</p><h4>Customer photo</h4><a href="' . e(img($request['proof_image'])) . '" target="_blank" rel="noopener"><img class="return-proof-large" src="' . e(img($request['proof_image'])) . '" alt="Customer return photo"></a></div>';
    echo '<div class="panel"><h3>Review Request</h3><form method="post" class="aform">' . csrf_field() . '<input type="hidden" name="id" value="' . (int)$request['id'] . '"><div class="fld"><label>Status</label><select name="status">';
    foreach ($statuses as $status) echo '<option value="' . e($status) . '"' . ($request['status'] === $status ? ' selected' : '') . '>' . ucfirst(e($status)) . '</option>';
    echo '</select></div><div class="fld"><label>Admin note (visible to customer)</label><textarea name="admin_note" rows="5" maxlength="5000">' . e($request['admin_note']) . '</textarea></div><button class="abtn">Save Update</button></form></div></div>';
    admin_end(); exit;
}
$status = $_GET['status'] ?? '';
$search = trim($_GET['q'] ?? '');
$where = ['1=1']; $args = [];
if (in_array($status, $statuses, true)) { $where[] = 'rr.status=?'; $args[] = $status; }
if ($search !== '') { $where[] = '(rr.product_name LIKE ? OR rr.reason LIKE ? OR o.order_no LIKE ? OR u.name LIKE ? OR u.email LIKE ?)'; array_push($args, "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"); }
$whereSql = implode(' AND ', $where);
$perPage = 20; $page = max(1, (int)($_GET['page'] ?? 1));
$total = (int)val("SELECT COUNT(*) FROM return_requests rr JOIN orders o ON o.id=rr.order_id LEFT JOIN users u ON u.id=rr.user_id WHERE $whereSql", $args);
$requests = rows("SELECT rr.*,o.order_no,u.name customer_name FROM return_requests rr JOIN orders o ON o.id=rr.order_id LEFT JOIN users u ON u.id=rr.user_id WHERE $whereSql ORDER BY rr.id DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage), $args);
$counts = array_column(rows('SELECT status,COUNT(*) c FROM return_requests GROUP BY status'), 'c', 'status');
echo '<div class="tabs-a"><a class="' . (!$status ? 'on' : '') . '" href="returns.php">All (' . array_sum($counts) . ')</a>';
foreach ($statuses as $s) echo '<a class="' . ($status === $s ? 'on' : '') . '" href="?status=' . e($s) . '">' . ucfirst(e($s)) . ' (' . (int)($counts[$s] ?? 0) . ')</a>';
echo '</div><div class="panel"><div class="panel-h"><h3>Return Requests <small>(' . $total . ')</small></h3><form class="filt"><input type="hidden" name="status" value="' . e($status) . '"><input name="q" value="' . e($search) . '" placeholder="Product / order / customer"><button class="abtn sm">Search</button></form></div><div class="table-wrap"><table class="atbl"><tr><th>Request</th><th>Date</th><th>Order</th><th>Product</th><th>Customer</th><th>Status</th><th></th></tr>';
foreach ($requests as $request) echo '<tr><td>#' . (int)$request['id'] . '</td><td>' . date('d M Y', strtotime($request['created_at'])) . '</td><td class="mono">' . e($request['order_no']) . '</td><td>' . e($request['product_name']) . '</td><td>' . e($request['customer_name'] ?: 'Deleted customer') . '</td><td><span class="pill s-' . e($request['status']) . '">' . ucfirst(e($request['status'])) . '</span></td><td><a class="abtn sm" href="?view=' . (int)$request['id'] . '">Review</a></td></tr>';
if (!$requests) echo '<tr><td colspan="7" class="empty">No return requests found.</td></tr>';
echo '</table></div>';
$pages = max(1, (int)ceil($total / $perPage));
if ($pages > 1) { echo '<div class="pager">'; for ($i = 1; $i <= $pages; $i++) echo '<a class="' . ($i === $page ? 'on' : '') . '" href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '">' . $i . '</a>'; echo '</div>'; }
echo '</div>';
admin_end();
