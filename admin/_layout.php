<?php
require_once __DIR__ . '/../includes/core.php'; require_once __DIR__ . '/../includes/crud.php';
define('IS_ADMIN_AREA', 1);
function admin_menu() { return [
 'Main' => [['Dashboard', 'index.php', '', 'dashboard'], ['Orders', 'orders.php', 'orders', 'bag'], ['Return Requests', 'returns.php', 'orders', 'refresh'], ['Checkout Visited', 'checkout_visits.php', 'orders', 'cart'], ['Products', 'products.php', 'products', 'package'], ['Categories', 'categories.php', 'categories', 'layers'], ['Customers', 'customers.php', 'customers', 'users', [['Customer List', 'customers.php'], ['Customer Profile', 'customer_profiles.php']]], ['Subscribers', 'subscribers.php', 'customers', 'mail'], ['Managers & Roles', 'managers.php', 'users', 'shield'], ['Visitor Analytics', 'visitors.php', 'visitors', 'activity', [['Visitor by IP Address', 'visitors.php?view=ip'], ['Recent Visit', 'visitors.php?view=recent'], ['Top Page', 'visitors.php?view=pages']]]],
 'Website Builder' => [['Header & Menu', 'menu.php', 'content', 'list'], ['Homepage Sections', 'sections.php', 'content', 'sliders'], ['Product Stories & Videos', 'videos.php', 'content', 'video'], ['Sliders & Banners', 'sliders.php', 'content', 'image'], ['Offer Banners', 'offers.php', 'content', 'tag'], ['Trust Badges', 'features.php', 'content', 'award'], ['Payment Logos', 'payment_logos.php', 'content', 'credit-card'], ['Pages', 'pages.php', 'content', 'file-text'], ['Testimonials', 'testimonials.php', 'content', 'message-square']],
 'Sales & Insight' => [['Notifications', 'notifications.php', 'content', 'bell'], ['Coupons', 'coupons.php', 'products', 'tag'], ['Reviews', 'reviews.php', 'products', 'star'], ['Messages', 'messages.php', 'customers', 'mail'], ['Finance', 'finance.php', 'finance', 'wallet'], ['Reports', 'reports.php', 'reports', 'bar-chart'], ['Export Center', 'export_center.php', '', 'download']],
 'System' => [['Settings', 'settings.php', 'settings', 'settings']]]; }
function ensure_admin_notification_reads() {
    q("CREATE TABLE IF NOT EXISTS admin_notification_reads (
        staff_id INT NOT NULL,
        item_type VARCHAR(20) NOT NULL,
        item_id INT NOT NULL,
        read_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (staff_id, item_type, item_id),
        KEY (read_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}
function admin_start($title, $perm = '') {
    if (!is_staff()) { $_SESSION['back'] = $_SERVER['REQUEST_URI']; redirect('login.php'); }
    if ($perm && !can($perm)) { http_response_code(403); die('<div style="font-family:sans-serif;padding:40px"><h2>🚫 Access denied</h2><p>You do not have permission for this section.</p><a href="index.php">← Dashboard</a></div>'); }
    ensure_admin_notification_reads();
    ob_start('render_icons'); $u = user(); $cur = basename($_SERVER['SCRIPT_NAME']); $unread = 0;
    if (can('orders')) $unread += (int)val("SELECT COUNT(*) FROM orders o LEFT JOIN admin_notification_reads r ON r.staff_id=? AND r.item_type='order' AND r.item_id=o.id WHERE o.status='pending' AND r.item_id IS NULL", [$u['id']]);
    if (can('customers')) $unread += (int)val("SELECT COUNT(*) FROM contact_messages WHERE is_read=0");
    $pend = (int)val("SELECT COUNT(*) FROM orders WHERE status='pending'"); ?>
<!DOCTYPE html><html lang="en" data-theme="light"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($title) ?> | <?= e(S('site_name')) ?> Admin</title>
<link rel="icon" href="<?= favicon_url() ?>"><link rel="stylesheet" href="<?= asset('css/admin.css') ?>?v=12"><style>:root{--green:<?= e(S('primary_color', '#1f5c3a')) ?>;--accent:<?= e(S('accent_color', '#e8923a')) ?>}</style>
<script>(function(){var t=localStorage.getItem('theme');document.documentElement.setAttribute('data-theme',t||'light');if(localStorage.getItem('sbc')==='1')document.documentElement.classList.add('sb-collapsed')})()</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script></head><body>
<aside class="sidebar" id="sidebar"><a class="s-logo" href="index.php"><?= logo_html('dark') ?><small class="role"><?= $u['role'] === 'admin' ? 'Admin Panel' : 'Manager Panel' ?></small></a>
<nav><?php foreach (admin_menu() as $grp => $items) { $vis = array_filter($items, fn($m) => !$m[2] || can($m[2])); if (!$vis) continue; echo '<div class="s-grp">' . e($grp) . '</div>'; foreach ($vis as $m) { $hasChildren = !empty($m[4]); $active = $cur === basename(strtok($m[1], '?')); if ($hasChildren) foreach ($m[4] as $child) if ($cur === basename(strtok($child[1], '?'))) $active = true; echo '<div class="nav-item"><a class="' . ($active ? 'on' : '') . '" href="' . $m[1] . '" title="' . e($m[0]) . '"><i>' . ic($m[3], 20) . '</i><span>' . e($m[0]) . '</span>' . ($m[1] === 'orders.php' && $pend ? '<em>' . $pend . '</em>' : '') . '</a>'; if ($hasChildren && $active) { echo '<div class="nav-sub">'; foreach ($m[4] as $child) { $childUrl = $child[1]; $childQuery = parse_url($childUrl, PHP_URL_QUERY); parse_str($childQuery ?: '', $childParams); $childActive = $cur === basename(strtok($childUrl, '?')) && ($_GET['view'] ?? '') === ($childParams['view'] ?? ''); echo '<a class="' . ($childActive ? 'on' : '') . '" href="' . e($childUrl) . '">' . e($child[0]) . '</a>'; } echo '</div>'; } echo '</div>'; } } ?></nav>
<div class="s-foot"><a href="../" target="_blank"><i>🌐</i><span>View Website</span></a><a href="../logout.php"><i>⏻</i><span>Logout</span></a></div></aside>
<div class="s-ov" id="sov"></div>
<div class="main"><header class="topbar"><button id="sbToggle" class="icon-btn" aria-label="toggle sidebar">☰</button><h2><?= e($title) ?></h2><div class="tb-right">
 <a class="abtn sm ghost vs" href="../" target="_blank">🌐 View Site</a><button class="icon-btn" data-theme-toggle title="Light / Dark">🌓</button><div class="admin-notif"><button class="icon-btn bell" type="button" data-admin-notif data-feed="notifications_api.php" data-csrf="<?= e(csrf()) ?>" aria-expanded="false" aria-controls="adminNotifPop" title="Notifications">🔔<em data-notif-count<?= $unread ? '' : ' hidden' ?>><?= $unread ?></em></button><div class="admin-notif-pop" id="adminNotifPop" hidden><div class="admin-notif-head"><b>Notifications</b><button type="button" data-notif-close aria-label="Close notifications">×</button></div><div class="admin-notif-list" data-notif-list><p class="admin-notif-empty">Click the bell to check for updates.</p></div></div></div>
 <div class="profile-dropdown"><button class="me" type="button" aria-expanded="false" aria-controls="profileMenu" title="Open account menu"><span class="avatar"><?= has_img($u['avatar'] ?? '') ? '<img src="' . img($u['avatar']) . '" alt="">' : e(mb_substr($u['name'], 0, 1)) ?></span><span class="me-info"><b><?= e($u['name']) ?></b><small><?= ucfirst($u['role']) ?></small></span><span class="me-caret" aria-hidden="true">⌄</span></button><div class="profile-menu" id="profileMenu" hidden><div class="profile-menu-head"><b><?= e($u['name']) ?></b><small><?= ucfirst($u['role']) ?></small></div><a href="profile.php"><span aria-hidden="true">⚙</span> Profile settings</a><a href="../logout.php" class="logout-link"><span aria-hidden="true">↪</span> Logout</a></div></div></div></header><div class="content">
<?php foreach (flash() as $f) echo '<div class="alert ' . $f[0] . '">' . e($f[1]) . '</div>';
}
function admin_end() { ?></div></div><script src="<?= asset('js/admin.js') ?>?v=5"></script></body></html><?php ob_end_flush(); }
function range_dates($r) { $to = date('Y-m-d'); $map = ['7d' => '-6 days', '30d' => '-29 days', '1m' => 'first day of this month', '1y' => '-364 days']; return [date('Y-m-d', strtotime($map[$r] ?? $map['30d'])), $to]; }
function pct_change($now, $prev) { if (!$prev) return $now ? 100 : 0; return round(($now - $prev) / $prev * 100, 1); }
