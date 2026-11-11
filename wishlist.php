<?php
require_once __DIR__ . "/includes/core.php";
$page_title = t('wishlist'); require 'includes/header.php';
$ids = array_map('intval', $_SESSION['wish'] ?? []); $ps = $ids ? rows('SELECT * FROM products WHERE status=1 AND id IN (' . implode(',', $ids) . ')') : [];
?><div class="container sec"><h1 class="page-title">♡ <?= t('wishlist') ?></h1><?php if (!$ps) echo '<p class="empty card-flat">' . t('no_products') . '</p>'; else { echo '<div class="grid g4">'; foreach ($ps as $p) echo product_card($p); echo '</div>'; } ?></div>
<?php require 'includes/footer.php';
