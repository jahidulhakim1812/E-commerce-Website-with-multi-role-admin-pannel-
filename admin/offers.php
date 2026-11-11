<?php
require '_layout.php'; admin_start('Offer Banners', 'content');
echo '<div class="alert ok">Upload the complete offer artwork and set the offer button label and destination. The homepage shows the full image without extra heading or description text.</div>';
crud(['table' => 'sections', 'title' => 'Offer Banners', 'single' => 'Offer Banner', 'order' => 'sort_order, id', 'where' => "type='banner'", 'fixed' => ['type' => 'banner'],
 'defaults' => ['image2' => '', 'btn_en' => 'Shop the Offer', 'btn_bn' => 'অফার দেখুন', 'link' => 'shop.php', 'sort_order' => 4, 'status' => 1],
 'fields' => [
  ['name' => 'image2', 'label' => 'Full Offer Image', 'type' => 'image', 'list' => true],
  ['name' => 'btn_en', 'label' => 'Offer Button (English)', 'half' => true],
  ['name' => 'btn_bn', 'label' => 'Offer Button (বাংলা)', 'half' => true],
  ['name' => 'link', 'label' => 'Offer Button Link', 'half' => true],
  ['name' => 'sort_order', 'label' => 'Position (lower = higher on page)', 'type' => 'number', 'half' => true, 'list' => true],
  ['name' => 'status', 'label' => 'Visible', 'type' => 'checkbox', 'list' => true],
 ]]);
admin_end();
