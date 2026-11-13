<?php
require '_layout.php'; admin_start('Homepage Sections', 'content');
echo '<div class="alert ok">Build your home page: add as many <b>Product Grid</b> sections as you want – <i>Fan Favorites</i>, <i>Best Selling</i>, <i>Recent Uploads</i>, <i>All Products</i> or any category – choose a grid or an auto-scrolling slider, then drag them into order using <b>Position</b>. Upload category-linked clips in <b>Website Builder → Product Stories & Videos</b>. Manage the promotional picture block from <b>Website Builder → Offer Banners</b>.</div>';
$src = ['featured' => 'Fan Favorites (products marked Featured)', 'best' => 'Best Selling (flag + most sold)', 'recent' => 'Recent Uploads (newest first)', 'all' => 'All Products', 'popular' => 'Most sold', 'new' => 'New Arrival flag', 'organic' => 'Organic flag'];
foreach (rows('SELECT id,name_en FROM categories ORDER BY id') as $c) $src['cat:' . $c['id']] = 'Category: ' . $c['name_en'];
crud(['table' => 'sections', 'title' => 'Homepage Sections', 'single' => 'Section', 'order' => 'sort_order, id', 'defaults' => ['status' => 1, 'type' => 'products', 'source' => 'featured', 'limit_n' => 8, 'layout' => 'grid'],
 'fields' => [
  ['name' => 'type', 'label' => 'Section Type', 'type' => 'select', 'opts' => ['hero' => 'Hero Slider (edit in Sliders)', 'categories' => 'Shop by Category (auto-scroll)', 'products' => 'Product Section', 'videos' => 'Product Stories & Videos (manage videos in Product Stories & Videos)', 'banner' => 'Offer Banner (manage in Offer Banners)', 'features' => 'Why Choose strip (edit in Trust Badges)', 'testimonials' => 'Testimonials', 'newsletter' => 'Newsletter'], 'half' => true, 'list' => true],
  ['name' => 'sort_order', 'label' => 'Position (lower = higher on page)', 'type' => 'number', 'half' => true, 'list' => true],
  ['name' => 'title_en', 'label' => 'Title (English)', 'half' => true, 'list' => true], ['name' => 'title_bn', 'label' => 'Title (বাংলা)', 'half' => true],
  ['name' => 'subtitle_en', 'label' => 'Subtitle (English)', 'half' => true], ['name' => 'subtitle_bn', 'label' => 'Subtitle (বাংলা)', 'half' => true],
  ['name' => 'source', 'label' => 'Products to show (Product Section)', 'type' => 'select', 'opts' => $src, 'half' => true, 'list' => true], ['name' => 'limit_n', 'label' => 'How many items', 'type' => 'number', 'half' => true],
  ['name' => 'layout', 'label' => 'Layout', 'type' => 'select', 'opts' => ['grid' => 'Grid', 'slider' => 'Scrolling slider'], 'half' => true, 'list' => true],
  ['name' => 'badge_en', 'label' => 'Round badge (Banner) e.g. UP TO|30%|OFF', 'half' => true], ['name' => 'badge_bn', 'label' => 'Round badge (বাংলা)', 'half' => true],
  ['name' => 'image', 'label' => 'Banner SIDE picture (shown on the right instead of fruit icons)', 'type' => 'image', 'list' => true], ['name' => 'image2', 'label' => 'Banner BACKGROUND picture (optional, full width)', 'type' => 'image'], ['name' => 'link', 'label' => 'Link / "View All" URL e.g. shop.php?featured=1', 'half' => true],
  ['name' => 'btn_en', 'label' => 'Button Text (English)', 'half' => true], ['name' => 'btn_bn', 'label' => 'Button Text (বাংলা)', 'half' => true],
  ['name' => 'status', 'label' => 'Visible', 'type' => 'checkbox', 'list' => true],
 ]]);
admin_end();
