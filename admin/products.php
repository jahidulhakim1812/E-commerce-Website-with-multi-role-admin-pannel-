<?php
require '_layout.php'; admin_start('Products', 'products');
$cats = fn() => [0 => '— Select —'] + array_column(rows('SELECT id,name_en FROM categories ORDER BY parent_id,sort_order,id'), 'name_en', 'id');
crud(['table' => 'products', 'title' => 'Products', 'single' => 'Product', 'search' => ['name_en', 'name_bn', 'brand'], 'defaults' => ['status' => 1, 'unit' => '', 'brand' => 'DryNutri'],

 'after' => function ($id, $new, $d) { sync_variants_from_post($id); if ($new && !empty($d['status']) && S('notify_products') === '1') { $p = row('SELECT * FROM products WHERE id=?', [$id]); if ($p) notify_create('product', '🆕 New arrival: ' . $p['name_en'], ($p['short_en'] ?: 'Now available at ' . S('site_name')) . ' - ' . money($p['price']), 'product.php?slug=' . $p['slug'], $p['name_bn'] ? '🆕 নতুন পণ্য: ' . $p['name_bn'] : '', $p['short_bn'] ?? '', $p['image'] ?? ''); } },
 'fields' => [
  ['name' => 'image', 'label' => 'Main Image', 'type' => 'image', 'list' => true],
  ['name' => 'name_en', 'label' => 'Name (English)', 'req' => true, 'half' => true, 'list' => true],
  ['name' => 'name_bn', 'label' => 'Name (বাংলা)', 'half' => true],
  ['name' => 'slug', 'label' => 'URL Slug', 'half' => true, 'slug_from' => 'name_en', 'help' => 'Auto-generated if left empty'],
  ['name' => 'category_id', 'label' => 'Category / Sub-category', 'type' => 'select', 'opts' => $cats, 'half' => true, 'list' => true],
  ['name' => 'price', 'label' => 'Sale Price (auto = lowest weight option if options are set)', 'type' => 'number', 'half' => true, 'list' => true, 'money' => true],
  ['name' => 'old_price', 'label' => 'Old Price (for discount)', 'type' => 'number', 'half' => true],
  ['name' => 'cost_price', 'label' => 'Cost Price (for profit)', 'type' => 'number', 'half' => true],
  ['name' => 'stock', 'label' => 'Stock Quantity', 'type' => 'number', 'half' => true, 'list' => true],
  ['name' => 'unit', 'label' => 'Unit / Weight (e.g. 500g)', 'half' => true],
  ['name' => 'brand', 'label' => 'Brand', 'half' => true],
  ['name' => 'flag', 'label' => 'Product Flag', 'type' => 'select', 'opts' => ['' => 'None', 'best' => 'Best Selling', 'new' => 'New Arrival', 'organic' => 'Organic'], 'half' => true, 'list' => true],
  ['name' => 'short_en', 'label' => 'Short Description (English)', 'half' => true], ['name' => 'short_bn', 'label' => 'Short Description (বাংলা)', 'half' => true],
  ['name' => 'desc_en', 'label' => 'Full Description (English)', 'type' => 'textarea', 'rows' => 6], ['name' => 'desc_bn', 'label' => 'Full Description (বাংলা)', 'type' => 'textarea', 'rows' => 6],
  ['name' => 'variants', 'type' => 'custom', 'render' => fn($r, $id) => variant_editor_html($id)],
  ['name' => 'gallery', 'label' => 'Additional Product Images (select multiple)', 'type' => 'images', 'help' => 'Choose several image files at once. They appear as thumbnails in the product image gallery.'],
  ['name' => 'is_featured', 'label' => 'Fan Favorite / Featured', 'type' => 'checkbox', 'hint' => 'Show in "Fan Favorites" section', 'half' => true, 'list' => true],
  ['name' => 'status', 'label' => 'Visibility', 'type' => 'checkbox', 'hint' => 'Visible on website', 'half' => true],
 ]]);
admin_end();
