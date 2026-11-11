<?php
require '_layout.php'; admin_start('Header & Menu', 'content');
$loc = ['header' => 'Header Menu Bar', 'footer_quick' => 'Footer – Quick Links', 'footer_care' => 'Footer – Customer Care'];
$cur = isset($loc[$_GET['loc'] ?? '']) ? $_GET['loc'] : 'header';
echo '<div class="tabs-a">'; foreach ($loc as $k => $l) echo '<a class="' . ($cur === $k ? 'on' : '') . '" href="?loc=' . $k . '">' . $l . '</a>'; echo '</div>';
echo '<div class="alert ok">Choose any <b>category or sub-category</b> to place in the menu, or add a custom link. Tick "Auto sub-menu" to show its sub-categories as a dropdown.</div>';
$cats = fn() => ['0' => '— (for custom link) —'] + array_column(rows('SELECT c.id, CONCAT(IF(c.parent_id>0,"   ↳ ",""),c.name_en) n FROM categories c ORDER BY IF(c.parent_id=0,c.id,c.parent_id), c.parent_id>0, c.sort_order'), 'n', 'id');
$parents = fn() => [0 => '— Top level —'] + array_column(rows("SELECT id,COALESCE(NULLIF(label_en,''),CONCAT('Category #',ref_id)) label_en FROM menu_items WHERE location=? AND parent_id=0", [$cur]), 'label_en', 'id');
crud(['table' => 'menu_items', 'title' => $loc[$cur], 'single' => 'Menu Item', 'where' => 'location=?', 'wargs' => [$cur], 'fixed' => ['location' => $cur], 'keep' => ['loc' => $cur], 'order' => 'parent_id, sort_order, id', 'defaults' => ['status' => 1, 'item_type' => 'link'],
 'fields' => [
  ['name' => 'item_type', 'label' => 'Item Type', 'type' => 'select', 'opts' => ['link' => 'Custom Link', 'category' => 'Category / Sub-category'], 'half' => true, 'list' => true],
  ['name' => 'ref_id', 'label' => 'Category (when type = Category)', 'type' => 'select', 'opts' => $cats, 'half' => true],
  ['name' => 'label_en', 'label' => 'Label (English)', 'half' => true, 'list' => true, 'help' => 'For categories, leave empty to use the category name'], ['name' => 'label_bn', 'label' => 'Label (বাংলা)', 'half' => true],
  ['name' => 'badge', 'label' => 'Badge (e.g. Hot, Sale)', 'half' => true, 'list' => true],
  ['name' => 'url', 'label' => 'URL (custom link, e.g. shop.php?offer=1 or page.php?slug=about)', 'list' => true],
  ['name' => 'parent_id', 'label' => 'Parent Menu Item (for dropdown)', 'type' => 'select', 'opts' => $parents, 'half' => true],
  ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'half' => true, 'list' => true],
  ['name' => 'auto_children', 'label' => 'Auto sub-menu', 'type' => 'checkbox', 'hint' => 'Show sub-categories as dropdown', 'half' => true],
  ['name' => 'status', 'label' => 'Visible', 'type' => 'checkbox', 'half' => true, 'list' => true],
 ]]);
admin_end();
