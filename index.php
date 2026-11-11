<?php
require_once __DIR__ . "/includes/core.php";
$page_title = S('site_name') . ' - ' . sx('tagline');
require 'includes/header.php';
foreach (rows('SELECT * FROM sections WHERE status=1 ORDER BY sort_order,id') as $s) render_section($s);
require 'includes/footer.php';
