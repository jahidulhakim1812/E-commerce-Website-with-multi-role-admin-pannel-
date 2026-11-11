<?php
require '_layout.php';
header('Content-Type: application/json; charset=utf-8');

function notification_response($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

if (!is_staff()) notification_response(['error' => 'Please sign in again to view notifications.'], 401);
ensure_admin_notification_reads();
$staff = user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $type = $_POST['type'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($id < 1 || !in_array($type, ['order', 'message'], true)) notification_response(['error' => 'Invalid notification.'], 400);

    if ($type === 'order') {
        if (!can('orders')) notification_response(['error' => 'You do not have permission to view orders.'], 403);
        if (!val('SELECT id FROM orders WHERE id=?', [$id])) notification_response(['error' => 'This order is no longer available.'], 404);
        q('INSERT INTO admin_notification_reads(staff_id,item_type,item_id) VALUES(?,?,?) ON DUPLICATE KEY UPDATE read_at=VALUES(read_at)', [$staff['id'], 'order', $id]);
    } else {
        if (!can('customers')) notification_response(['error' => 'You do not have permission to view messages.'], 403);
        $updated = q('UPDATE contact_messages SET is_read=1 WHERE id=? AND is_read=0', [$id])->rowCount();
        if (!$updated && !val('SELECT id FROM contact_messages WHERE id=?', [$id])) notification_response(['error' => 'This message is no longer available.'], 404);
    }

    notification_response(['ok' => true]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') notification_response(['error' => 'Unsupported request method.'], 405);

$items = [];
if (can('orders')) {
    foreach (rows("SELECT o.id,o.order_no,o.name,o.total,o.created_at FROM orders o LEFT JOIN admin_notification_reads r ON r.staff_id=? AND r.item_type='order' AND r.item_id=o.id WHERE o.status='pending' AND r.item_id IS NULL ORDER BY o.id DESC LIMIT 20", [$staff['id']]) as $order) {
        $items[] = [
            'type' => 'order',
            'id' => (int)$order['id'],
            'title' => 'New order #' . $order['order_no'],
            'detail' => trim($order['name'] . ' · ' . money($order['total'])),
            'created_at' => date('d M, h:i A', strtotime($order['created_at'])),
            'sort_at' => strtotime($order['created_at']),
            'href' => 'orders.php?view=' . (int)$order['id'],
        ];
    }
}
if (can('customers')) {
    foreach (rows('SELECT id,name,email,created_at FROM contact_messages WHERE is_read=0 ORDER BY id DESC LIMIT 20') as $message) {
        $items[] = [
            'type' => 'message',
            'id' => (int)$message['id'],
            'title' => 'New contact message',
            'detail' => trim($message['name'] . ($message['email'] ? ' · ' . $message['email'] : '')),
            'created_at' => date('d M, h:i A', strtotime($message['created_at'])),
            'sort_at' => strtotime($message['created_at']),
            'href' => 'messages.php?a=edit&id=' . (int)$message['id'],
        ];
    }
}

usort($items, fn($a, $b) => $b['sort_at'] <=> $a['sort_at']);
$items = array_slice($items, 0, 20);
foreach ($items as &$item) unset($item['sort_at']);
unset($item);
notification_response(['items' => $items]);
