<?php
require '_layout.php'; admin_start('Sliders & Banners', 'content');
crud(['table' => 'sliders', 'title' => 'Hero Slider', 'single' => 'Slide', 'order' => 'sort_order, id', 'defaults' => ['status' => 1, 'link' => 'shop.php'],
 'fields' => [
  ['name' => 'image', 'label' => 'Slide Picture (full image shown with centered buttons)', 'type' => 'image', 'list' => true],
  ['name' => 'btn_en', 'label' => 'Main Button (English)', 'half' => true], ['name' => 'btn_bn', 'label' => 'Main Button (বাংলা)', 'half' => true], ['name' => 'link', 'label' => 'Main Button Link', 'half' => true],
  ['name' => 'btn2_en', 'label' => 'Second Button (English)', 'half' => true], ['name' => 'btn2_bn', 'label' => 'Second Button (বাংলা)', 'half' => true], ['name' => 'link2', 'label' => 'Second Button Link', 'half' => true],
  ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'half' => true, 'list' => true], ['name' => 'status', 'label' => 'Visible', 'type' => 'checkbox', 'list' => true],
 ]]);
admin_end();
