<?php
require_once 'includes/core.php';
$p = row('SELECT * FROM products WHERE slug=? AND status=1', [$_GET['slug'] ?? '']);
if (!$p) { http_response_code(404); $page_title = t('page_not_found'); require 'includes/header.php'; echo '<div class="container sec center"><h1>404</h1><a class="btn" href="' . url() . '">' . t('back_home') . '</a></div>'; require 'includes/footer.php'; exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review'])) {
    csrf_check(); $u = user();
    if ($u && trim($_POST['body'] ?? '')) { q('INSERT INTO reviews(product_id,user_id,name,rating,body,status) VALUES(?,?,?,?,?,0)', [$p['id'], $u['id'], $u['name'], max(1, min(5, (int)$_POST['rating'])), trim($_POST['body'])]); flash(t('review_pending')); }
    redirect('product.php?slug=' . $p['slug'] . '#reviews');
}
$vars = variants_of($p['id']); $def = null;
foreach ($vars as $v) if ($v['stock'] > 0) { $def = $v; break; }   /* lowest quantity that is in stock is pre-selected */
if (!$def && $vars) $def = $vars[0];
$price = $def ? (float)$def['price'] : (float)$p['price']; $old = $def ? (float)$def['old_price'] : (float)$p['old_price']; $stock = $def ? (int)$def['stock'] : (int)$p['stock'];
$page_title = tr($p, 'name'); $meta_desc = tr($p, 'short'); $og_image = has_img($p['image']) ? full_url('uploads/' . $p['image']) : null;
require 'includes/header.php';
$gal = array_values(array_filter(array_merge([$p['image']], array_filter(explode(',', (string)$p['gallery'])))));
if (!$gal) $gal = [''];
$galUrls = array_values(array_filter(array_map(fn($g) => has_img($g) ? img($g) : '', $gal)));
$revs = rows('SELECT * FROM reviews WHERE product_id=? AND status=1 ORDER BY id DESC', [$p['id']]);
$rel = rows('SELECT * FROM products WHERE status=1 AND category_id=? AND id<>? ORDER BY RAND() LIMIT 5', [$p['category_id'], $p['id']]);
if (count($rel) < 4) $rel = rows('SELECT * FROM products WHERE status=1 AND id<>? ORDER BY RAND() LIMIT 5', [$p['id']]);
$pct = $old > $price ? round(100 - $price / $old * 100) : 0;
$wa = 'https://wa.me/' . preg_replace('/\D/', '', S('float_whatsapp')) . '?text=' . rawurlencode('Hello, I want to order: ' . $p['name_en'] . ($def ? ' (' . $def['label_en'] . ')' : '') . ' ' . full_url('product.php?slug=' . $p['slug']));
$pv = array_map(fn($v) => ['id' => (int)$v['id'], 'label' => tr($v, 'label'), 'price' => (float)$v['price'], 'old' => (float)$v['old_price'], 'stock' => (int)$v['stock'], 'pct' => variant_pct($v)], $vars);
foreach (flash() as $f) echo '<div class="container"><div class="alert ' . $f[0] . '">' . e($f[1]) . '</div></div>';
?>
<div class="container"><div class="crumb"><a href="<?= url() ?>"><?= t('home') ?></a> › <a href="<?= url('shop.php') ?>"><?= t('all_products') ?></a> › <?= e(tr($p, 'name')) ?></div>
<div class="pd card-flat"><div class="gal"><?php if (count($galUrls) > 1): ?><div class="thumbs"><?php foreach ($galUrls as $i => $g): ?><img class="<?= $i ? '' : 'on' ?>" src="<?= $g ?>" alt="" data-full="<?= $g ?>" data-i="<?= $i ?>"><?php endforeach; ?></div><?php endif; ?>
  <div class="gal-main" id="galMain"><?= flag_badge($p) ?><?php if ($galUrls): ?><div class="zoom-wrap" id="zoomWrap"><img id="mainImg" src="<?= $galUrls[0] ?>" alt="<?= e(tr($p, 'name')) ?>"></div><button type="button" class="zoom-btn" id="zoomBtn" aria-label="zoom">🔍 <span><?= LANG === 'bn' ? 'জুম' : 'Zoom' ?></span></button><?php else: echo pimg_html($p, true); endif; ?></div></div>
 <div class="pd-info"><h1><?= e(tr($p, 'name')) ?></h1>
  <?php if ($p['rating_count']): ?><div class="rate"><span class="star"><?= stars($p['rating']) ?></span> <?= number_format($p['rating'], 1) ?> (<?= $p['rating_count'] ?>)</div><?php endif; ?>
  <div class="pd-price"><b id="pdPrice"><?= money($price) ?></b> <s id="pdOld" <?= $pct ? '' : 'hidden' ?>><?= money($old) ?></s> <em id="pdPct" <?= $pct ? '' : 'hidden' ?>><?= t('save_pct', $pct) ?></em></div>
  <p class="muted"><?= e(tr($p, 'short')) ?></p>
  <?php if ($vars): ?><div class="vopt"><b><?= t('choose_option') ?>:</b><div class="vlist" id="vlist"><?php foreach ($vars as $v): $vp = variant_pct($v); ?>
    <button type="button" class="vchip <?= ($def && $v['id'] == $def['id']) ? 'on' : '' ?> <?= $v['stock'] < 1 ? 'off' : '' ?>" data-vid="<?= $v['id'] ?>" <?= $v['stock'] < 1 ? 'disabled title="' . e(t('no_stock_opt')) . '"' : '' ?>><span><?= e(tr($v, 'label')) ?></span><?php if ($vp): ?><em><?= $vp ?>% OFF</em><?php endif; ?></button><?php endforeach; ?></div></div><?php endif; ?>
  <p id="pdStock"><?= $stock > 0 ? '<span class="ok-t">● ' . t('in_stock') . ($stock <= (int)S('low_stock', 10) ? ' — ' . t('only_left', $stock) : '') . '</span>' : '<span class="bad-t">● ' . t('out_of_stock') . '</span>' ?></p>
  <?php if ($stock > 0): ?><div class="qty-row"><label><?= t('quantity') ?>:</label><div class="qty"><button type="button" data-q="-1">−</button><input id="qty" type="number" value="1" min="1" max="<?= $stock ?>"><button type="button" data-q="1">+</button></div></div>
  <div class="btn-row"><button class="btn" id="addBtn" data-pid="<?= $p['id'] ?>">🛒 <?= t('add_to_cart') ?></button><button class="btn btn-dark" id="buyBtn" data-pid="<?= $p['id'] ?>"><?= t('buy_now') ?></button></div><?php endif; ?>
  <div class="btn-row"><?php if (S('whatsapp_order') === '1'): ?><a class="btn btn-wa" target="_blank" rel="noopener" href="<?= e($wa) ?>">💬 <?= t('order_whatsapp') ?></a><?php endif; ?><a class="btn btn-call" href="tel:<?= e(S('phone')) ?>">📞 <?= t('call_order') ?></a></div>
  <?php if ($p['brand']): ?><div class="brand"><?= t('brand') ?>: <b><?= e($p['brand']) ?></b></div><?php endif; ?></div></div>
<script>window.PV=<?= json_encode(['vars' => $pv, 'cur' => S('currency', '৳'), 'low' => (int)S('low_stock', 10), 'tx' => ['in' => t('in_stock'), 'out' => t('out_of_stock'), 'left' => t('only_left', 0), 'save' => t('save_pct', 0)], 'gal' => $galUrls], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
<div class="tabs card-flat"><div class="tab-h"><button class="on" data-tab="t1"><?= t('description') ?></button><button data-tab="t2"><?= t('reviews') ?> (<?= count($revs) ?>)</button></div>
 <div class="tab-c on" id="t1"><div class="rich"><?= nl2br(e(tr($p, 'desc'))) ?></div></div>
 <div class="tab-c" id="t2"><div id="reviews">
  <?php foreach ($revs as $r): ?><div class="rev"><b><?= e($r['name']) ?></b> <span class="star"><?= stars($r['rating']) ?></span><p><?= nl2br(e($r['body'])) ?></p><small class="muted"><?= date('d M Y', strtotime($r['created_at'])) ?></small></div><?php endforeach; if (!$revs) echo '<p class="muted">' . t('no_reviews') . '</p>'; ?>
  <h4><?= t('write_review') ?></h4>
  <?php if (user()): ?><form method="post" class="form"><?= csrf_field() ?><input type="hidden" name="review" value="1"><label><?= t('your_rating') ?><select name="rating"><?php for ($i = 5; $i >= 1; $i--) echo "<option value=$i>" . str_repeat('★', $i) . '</option>'; ?></select></label><textarea name="body" rows="4" required></textarea><button class="btn"><?= t('submit') ?></button></form>
  <?php else: ?><p><a href="<?= url('login.php') ?>"><?= t('login_to_review') ?></a></p><?php endif; ?></div></div></div>
<?php if ($rel): ?><section class="sec"><div class="sec-head"><h2 class="sec-title"><?= t('related') ?></h2></div><div class="grid g5"><?php foreach ($rel as $r) echo product_card($r); ?></div></section><?php endif; ?></div>
<script type="application/ld+json"><?= json_encode(['@context' => 'https://schema.org', '@type' => 'Product', 'name' => $p['name_en'], 'description' => $p['short_en'], 'image' => $og_image ?: logo_url(), 'brand' => ['@type' => 'Brand', 'name' => $p['brand'] ?: S('site_name')], 'offers' => ['@type' => 'Offer', 'priceCurrency' => 'BDT', 'price' => $price, 'availability' => $stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock', 'url' => full_url('product.php?slug=' . $p['slug'])]] + ($p['rating_count'] ? ['aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => (float)$p['rating'], 'reviewCount' => (int)$p['rating_count']]] : []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php require 'includes/footer.php';
