<?php
require_once __DIR__ . "/includes/core.php";
$page_title = t('all_products');
require 'includes/header.php';
$w = ['p.status=1']; $a = [];
$catSlug = $_GET['cat'] ?? ''; $cur = null;
if ($catSlug) { $cur = row('SELECT * FROM categories WHERE slug=?', [$catSlug]); if ($cur) { $w[] = 'p.category_id IN (' . implode(',', category_ids_with_children($cur['id'])) . ')'; $page_title = tr($cur, 'name'); } }
if (!empty($_GET['q'])) { $w[] = '(p.name_en LIKE ? OR p.name_bn LIKE ? OR p.brand LIKE ?)'; $l = '%' . $_GET['q'] . '%'; array_push($a, $l, $l, $l); }
$maxP = (int)val('SELECT CEIL(MAX(price)) FROM products WHERE status=1') ?: 1000;
$pmin = max(0, (int)($_GET['min'] ?? 0)); $pmax = (int)($_GET['max'] ?? $maxP);
if ($pmin > 0) { $w[] = 'p.price>=?'; $a[] = $pmin; } if ($pmax < $maxP) { $w[] = 'p.price<=?'; $a[] = $pmax; }
$fl = array_intersect((array)($_GET['flag'] ?? []), ['best', 'new', 'organic']); if (isset($_GET['flag']) && is_string($_GET['flag'])) $fl = array_intersect([$_GET['flag']], ['best', 'new', 'organic']);
if ($fl) $w[] = "p.flag IN ('" . implode("','", $fl) . "')";
if (!empty($_GET['featured'])) $w[] = 'p.is_featured=1';
if (!empty($_GET['offer'])) $w[] = 'p.old_price>p.price';
$sorts = ['' => 'p.id DESC', 'low' => 'p.price ASC', 'high' => 'p.price DESC', 'new' => 'p.created_at DESC', 'pop' => 'p.sold DESC'];
$sort = $_GET['sort'] ?? ''; $ob = $sorts[$sort] ?? $sorts[''];
$per = 12; $pg = max(1, (int)($_GET['page'] ?? 1));
$where = implode(' AND ', $w);
$total = (int)val("SELECT COUNT(*) FROM products p WHERE $where", $a);
$pages = max(1, ceil($total / $per)); $off = ($pg - 1) * $per;
$ps = rows("SELECT p.* FROM products p WHERE $where ORDER BY $ob LIMIT $per OFFSET $off", $a);
$tree = categories_tree();
function qs($over = []) { $q = array_merge($_GET, $over); unset($q['lang']); return '?' . http_build_query(array_filter($q, fn($v) => $v !== '' && $v !== null)); }
?>
<div class="container"><div class="crumb"><a href="<?= url() ?>"><?= t('home') ?></a> › <?= e($page_title) ?></div>
<h1 class="page-title"><?= e($page_title) ?> <small class="muted">(<?= $total ?>)</small></h1>
<div class="shop">
 <button class="btn btn-ghost filter-toggle" onclick="document.getElementById('filters').classList.toggle('open')">⚙ <?= t('filter') ?></button>
 <aside class="filters" id="filters"><form method="get">
  <?php if (!empty($_GET['q'])) echo '<input type="hidden" name="q" value="' . e($_GET['q']) . '">'; ?>
  <div class="fbox"><h5><?= t('category') ?></h5>
   <?php foreach ($tree as $c): ?><label class="chk"><input type="radio" name="cat" value="<?= e($c['slug']) ?>" <?= $catSlug === $c['slug'] ? 'checked' : '' ?> onchange="this.form.submit()"> <?= e(tr($c, 'name')) ?></label>
   <?php foreach ($c['children'] as $ch): ?><label class="chk sub"><input type="radio" name="cat" value="<?= e($ch['slug']) ?>" <?= $catSlug === $ch['slug'] ? 'checked' : '' ?> onchange="this.form.submit()"> <?= e(tr($ch, 'name')) ?></label><?php endforeach; endforeach; ?></div>
  <div class="fbox"><h5><?= t('price_range') ?></h5><div class="range"><input type="number" name="min" min="0" max="<?= $maxP ?>" value="<?= $pmin ?>"><span>–</span><input type="number" name="max" min="0" max="<?= $maxP ?>" value="<?= $pmax ?>"></div>
   <input type="range" min="0" max="<?= $maxP ?>" value="<?= $pmax ?>" oninput="this.form.max.value=this.value"></div>
  <div class="fbox"><h5><?= t('flag') ?></h5><?php foreach (['best' => 'best_selling', 'new' => 'new_arrival', 'organic' => 'organic'] as $k => $l): ?><label class="chk"><input type="checkbox" name="flag[]" value="<?= $k ?>" <?= in_array($k, $fl) ? 'checked' : '' ?>> <?= t($l) ?></label><?php endforeach; ?></div>
  <button class="btn btn-block"><?= t('apply') ?></button><a class="btn btn-ghost btn-block" style="margin-top:8px" href="<?= url('shop.php') ?>"><?= t('reset') ?></a></form></aside>
 <div class="shop-main">
  <div class="sortbar"><span><?= t('sort_by') ?>:</span><select onchange="location.href=this.value">
   <?php foreach (['' => 'default_sort', 'low' => 'price_low', 'high' => 'price_high', 'new' => 'newest', 'pop' => 'popular'] as $k => $l) echo '<option value="' . e(qs(['sort' => $k, 'page' => 1])) . '" ' . ($sort === $k ? 'selected' : '') . '>' . t($l) . '</option>'; ?></select></div>
  <?php if (!$ps): ?><p class="empty"><?= t('no_products') ?></p><?php else: ?><div class="grid g4"><?php foreach ($ps as $p) echo product_card($p); ?></div><?php endif; ?>
  <?php if ($pages > 1): ?><div class="pager"><?php for ($i = 1; $i <= $pages; $i++) echo '<a class="' . ($i == $pg ? 'on' : '') . '" href="' . e(qs(['page' => $i])) . '">' . $i . '</a>'; ?></div><?php endif; ?>
 </div></div></div>
<?php require 'includes/footer.php';
