<?php
require '_layout.php'; admin_start('Categories', 'categories');
crud(['table' => 'categories', 'title' => 'Categories & Sub-categories', 'single' => 'Category', 'search' => ['name_en', 'name_bn'], 'order' => 'parent_id, sort_order, id', 'defaults' => ['status' => 1, 'show_home' => 1, 'icon' => '🥜'],
 'fields' => [
  ['name' => 'image', 'label' => 'Image', 'type' => 'image', 'list' => true], ['name' => 'icon', 'label' => 'Emoji icon (shown when there is no image)', 'half' => true, 'list' => true],
  ['name' => 'name_en', 'label' => 'Name (English)', 'req' => true, 'half' => true, 'list' => true], ['name' => 'name_bn', 'label' => 'Name (বাংলা)', 'half' => true],
  ['name' => 'slug', 'label' => 'URL Slug', 'half' => true, 'slug_from' => 'name_en'],
  ['name' => 'parent_id', 'label' => 'Parent Category', 'type' => 'select', 'opts' => fn() => [0 => '— Top level —'] + array_column(rows('SELECT id,name_en FROM categories WHERE parent_id=0 ORDER BY sort_order'), 'name_en', 'id'), 'half' => true, 'list' => true, 'help' => 'Choose a parent to make this a sub-category'],
  ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'half' => true, 'list' => true],
  ['name' => 'show_home', 'label' => 'Show on Home', 'type' => 'checkbox', 'hint' => 'Show in the auto-scrolling "Shop by Category"', 'half' => true], ['name' => 'status', 'label' => 'Status', 'type' => 'checkbox', 'half' => true, 'list' => true],
 ]]);
admin_end();
