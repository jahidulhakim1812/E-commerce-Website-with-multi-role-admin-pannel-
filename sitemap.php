<?php
require_once 'includes/core.php'; header('Content-Type: application/xml; charset=utf-8');
$u = [[full_url(), '1.0'], [full_url('shop.php'), '0.9']];
foreach (rows('SELECT slug FROM categories WHERE status=1') as $c) $u[] = [full_url('shop.php?cat=' . $c['slug']), '0.7'];
foreach (rows('SELECT slug,created_at FROM products WHERE status=1') as $p) $u[] = [full_url('product.php?slug=' . $p['slug']), '0.8'];
foreach (rows('SELECT slug FROM pages WHERE status=1') as $p) $u[] = [full_url('page.php?slug=' . $p['slug']), '0.4'];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($u as $x) echo '<url><loc>' . htmlspecialchars($x[0]) . '</loc><priority>' . $x[1] . "</priority></url>\n";
echo '</urlset>';
