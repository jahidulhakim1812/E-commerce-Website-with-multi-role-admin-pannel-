<?php
require '_layout.php';
if (is_staff() && user()['role'] !== 'admin') { http_response_code(403); die('Activity log access is restricted to administrators.'); }
admin_start('Activity Log', 'users');

$fromInput = isset($_GET['from']) && is_string($_GET['from']) ? $_GET['from'] : '';
$toInput = isset($_GET['to']) && is_string($_GET['to']) ? $_GET['to'] : '';
$role = isset($_GET['role']) && is_string($_GET['role']) ? $_GET['role'] : '';
$action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '';
$search = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
$dateErrors = [];
$parseDateTime = function ($value, $endOfMinute = false) {
    if ($value === '') return '';
    $date = DateTime::createFromFormat('!Y-m-d\TH:i', $value);
    return $date && $date->format('Y-m-d\TH:i') === $value ? $date->format('Y-m-d H:i') . ($endOfMinute ? ':59' : ':00') : null;
};
$from = $parseDateTime($fromInput);
$to = $parseDateTime($toInput, true);
if ($from === null) { $dateErrors[] = 'Start date and time is invalid.'; $from = ''; }
if ($to === null) { $dateErrors[] = 'End date and time is invalid.'; $to = ''; }
if ($from !== '' && $to !== '' && $from > $to) { $dateErrors[] = 'Start date and time must be before the end date and time.'; $from = ''; $to = ''; }
$roles = ['admin', 'manager', 'customer', 'guest'];
$actions = array_column(rows('SELECT DISTINCT action_type FROM activity_logs ORDER BY action_type'), 'action_type');
$where = ['1=1']; $args = [];
if (in_array($role, $roles, true)) { $where[] = 'actor_role=?'; $args[] = $role; }
if (in_array($action, $actions, true)) { $where[] = 'action_type=?'; $args[] = $action; }
if ($from !== '') { $where[] = 'created_at>=?'; $args[] = $from; }
if ($to !== '') { $where[] = 'created_at<=?'; $args[] = $to; }
if ($search !== '') {
    $where[] = '(actor_name LIKE ? OR description LIKE ? OR ip LIKE ?)';
    array_push($args, "%$search%", "%$search%", "%$search%");
}
$whereSql = implode(' AND ', $where);
$perPage = 50; $page = max(1, (int)($_GET['page'] ?? 1));
$total = (int)val("SELECT COUNT(*) FROM activity_logs WHERE $whereSql", $args);
$pages = max(1, (int)ceil($total / $perPage)); $page = min($page, $pages);
$logs = rows("SELECT * FROM activity_logs WHERE $whereSql ORDER BY id DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage), $args);

if ($dateErrors) echo '<div class="alert err">' . e(implode(' ', $dateErrors)) . '</div>';
echo '<div class="panel"><div class="panel-h"><h3>Activity Log <small>(' . $total . ')</small></h3><small class="muted">Staff page access and changes, plus customer account, order, review, and return activity.</small></div><form class="filt" method="get"><input type="datetime-local" name="from" aria-label="From date and time" value="' . e($fromInput) . '"><input type="datetime-local" name="to" aria-label="To date and time" value="' . e($toInput) . '"><select name="role"><option value="">All roles</option>';
foreach ($roles as $item) echo '<option value="' . e($item) . '"' . ($role === $item ? ' selected' : '') . '>' . ucfirst(e($item)) . '</option>';
echo '</select><select name="action"><option value="">All activity types</option>';
foreach ($actions as $item) echo '<option value="' . e($item) . '"' . ($action === $item ? ' selected' : '') . '>' . ucfirst(str_replace('_', ' ', e($item))) . '</option>';
echo '</select><input name="q" value="' . e($search) . '" placeholder="Actor / details / IP"><button class="abtn sm">Filter</button><a class="abtn sm ghost" href="activity_log.php">Clear</a></form><div class="table-wrap"><table class="atbl"><tr><th>Date &amp; Time</th><th>Actor</th><th>Role</th><th>Activity</th><th>Details</th><th>IP Address</th></tr>';
foreach ($logs as $log) {
    $target = $log['target_type'] ? ' · ' . $log['target_type'] . ($log['target_id'] ? ' #' . $log['target_id'] : '') : '';
    echo '<tr><td>' . date('d M Y h:i:s A', strtotime($log['created_at'])) . '</td><td>' . e($log['actor_name']) . ($log['actor_user_id'] ? ' <small class="muted">#' . (int)$log['actor_user_id'] . '</small>' : '') . '</td><td><span class="pill">' . e(ucfirst($log['actor_role'])) . '</span></td><td>' . e(ucfirst(str_replace('_', ' ', $log['action_type']))) . '</td><td>' . e($log['description'] . $target) . '</td><td class="mono">' . e($log['ip'] ?: '—') . '</td></tr>';
}
if (!$logs) echo '<tr><td colspan="6" class="empty">No activity found for these filters.</td></tr>';
echo '</table></div>';
if ($pages > 1) {
    echo '<div class="pager">';
    for ($i = 1; $i <= $pages; $i++) echo '<a class="' . ($i === $page ? 'on' : '') . '" href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '">' . $i . '</a>';
    echo '</div>';
}
echo '</div>';
admin_end();
