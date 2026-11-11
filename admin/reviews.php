<?php
require '_layout.php'; admin_start('Reviews', 'products');
crud(['table' => 'reviews', 'title' => 'Product Reviews (approve to publish)', 'single' => 'Review', 'can_add' => false, 'search' => ['name', 'body'], 'defaults' => ['status' => 1],
 'fields' => [
  ['name' => 'product_id', 'label' => 'Product', 'type' => 'select', 'opts' => fn() => array_column(rows('SELECT id,name_en FROM products'), 'name_en', 'id'), 'list' => true], ['name' => 'name', 'label' => 'Reviewer', 'half' => true, 'list' => true], ['name' => 'rating', 'label' => 'Rating', 'type' => 'number', 'half' => true, 'list' => true],
  ['name' => 'body', 'label' => 'Review', 'type' => 'textarea', 'list' => true], ['name' => 'status', 'label' => 'Approved', 'type' => 'checkbox', 'hint' => 'Visible on product page', 'list' => true],
 ], 'after' => fn($id) => update_rating((int)val('SELECT product_id FROM reviews WHERE id=?', [$id])), 'on_delete' => fn($r) => update_rating((int)$r['product_id'])]);
admin_end();
