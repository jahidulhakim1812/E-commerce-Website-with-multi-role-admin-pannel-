<?php
function flag_badge($p) { $m = ['best' => ['t' => 'best_selling', 'c' => 'b-best'], 'new' => ['t' => 'new_arrival', 'c' => 'b-new'], 'organic' => ['t' => 'organic', 'c' => 'b-org']]; return isset($m[$p['flag']]) ? '<span class="badge ' . $m[$p['flag']]['c'] . '">' . t($m[$p['flag']]['t']) . '</span>' : ''; }
function pimg_html($p, $big = false) { return has_img($p['image']) ? '<img loading="lazy" src="' . img($p['image']) . '" alt="' . e(tr($p, 'name')) . '">' : '<span class="ph3d' . ($big ? ' big' : '') . '">' . cat_icon($p['category_id']) . '</span>'; }
function product_card($p) {
    $wl = in_array($p['id'], $_SESSION['wish'] ?? []);
    $pct = ($p['old_price'] > $p['price']) ? round(100 - $p['price'] / $p['old_price'] * 100) : 0;
    ob_start(); ?>
    <div class="pcard card3d">
      <?= flag_badge($p) ?>
      <button class="wish <?= $wl ? 'on' : '' ?>" data-wish="<?= $p['id'] ?>" aria-label="wishlist">♥</button>
      <a href="<?= url('product.php?slug=' . e($p['slug'])) ?>" class="pimg"><?= pimg_html($p) ?></a>
      <div class="pbody">
        <a href="<?= url('product.php?slug=' . e($p['slug'])) ?>" class="pname"><?= e(tr($p, 'name')) ?></a>
        <?php if ($p['rating_count']): ?><div class="rate"><span class="star">★</span> <b><?= number_format($p['rating'], 1) ?></b> <small>(<?= $p['rating_count'] ?> <?= LANG === 'bn' ? 'রিভিউ' : 'reviews' ?>)</small></div><?php endif; ?>
        <div class="price"><b><?= money($p['price']) ?></b><?php if ($p['unit']): ?><small class="pu">/ <?= e($p['unit']) ?></small><?php endif; ?> <?php if ($pct): ?><s><?= money($p['old_price']) ?></s><?php endif; ?></div>
        <?php if ($p['stock'] > 0): ?><button class="btn btn-block" data-add="<?= $p['id'] ?>">🛒 <?= t('add_to_cart') ?></button>
        <?php else: ?><button class="btn btn-block" disabled><?= t('out_of_stock') ?></button><?php endif; ?>
      </div>
    </div>
    <?php return ob_get_clean();
}
function section_head($s, $viewall = true) { echo '<div class="sec-head"><h2 class="sec-title"><span class="leaf">🌿</span>' . e(tr($s, 'title')) . '<span class="leaf r">🌿</span></h2>' . (tr($s, 'subtitle') ? '<p class="muted">' . e(tr($s, 'subtitle')) . '</p>' : '') . ($viewall && $s['link'] && $s['type'] === 'products' ? '<a class="btn btn-sm btn-view" href="' . url($s['link']) . '">' . e(tr($s, 'btn') ?: t('view_all')) . ' →</a>' : '') . '</div>'; }
function section_products_query($s) {
    $src = $s['source']; $lim = max(1, (int)$s['limit_n']); $w = 'status=1'; $o = 'id DESC';
    if ($src === 'featured') $w .= ' AND is_featured=1';
    elseif ($src === 'best') { $w .= " AND (flag='best' OR sold>0)"; $o = "(flag='best') DESC, sold DESC, id DESC"; }
    elseif ($src === 'new') $w .= " AND flag='new'";
    elseif ($src === 'organic') $w .= " AND flag='organic'";
    elseif ($src === 'popular') $o = 'sold DESC';
    elseif ($src === 'recent' || $src === 'latest') $o = 'created_at DESC, id DESC';
    elseif ($src === 'all') $o = 'id ASC';
    elseif (strpos($src, 'cat:') === 0) $w .= ' AND category_id IN (' . implode(',', category_ids_with_children((int)substr($src, 4))) . ')';
    return rows("SELECT * FROM products WHERE $w ORDER BY $o LIMIT $lim");
}
function hs_open($auto = 1) { return '<div class="hs" data-auto="' . $auto . '"><button class="hs-btn prev" aria-label="prev">‹</button><div class="hs-track">'; }
function hs_close() { return '</div><button class="hs-btn next" aria-label="next">›</button></div>'; }
function render_section($s) {
    switch ($s['type']) {
    case 'hero':
        $sl = rows('SELECT * FROM sliders WHERE status=1 ORDER BY sort_order,id'); if (!$sl) return; $imageSlides = count(array_filter($sl, fn($slide) => has_img($slide['image']))) > 0; $imageFiles = array_values(array_filter($sl, fn($slide) => has_img($slide['image']))); $firstImageFile = $imageSlides ? $imageFiles[0]['image'] : ''; $imageExt = strtolower(pathinfo($firstImageFile, PATHINFO_EXTENSION)); $firstImage = in_array($imageExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']) ? getimagesize(UPLOAD_DIR . $firstImageFile) : false; $imageRatio = $firstImage ? (int)$firstImage[0] . ' / ' . (int)$firstImage[1] : '3 / 1'; $sets = [['🥭','🍊','🍌','🍍','🍓','🍇','🍑','🥝','🍋'], ['🥭','🥭','🍑','🍊','🍊','🥭','🍋','🍑','🍍'], ['🍍','🥝','🍓','🍇','🍊','🍋','🍌','🍑','🥭']]; ?>
        <section class="hero container"><div class="slides<?= $imageSlides ? ' slides-images' : '' ?>"<?= $imageSlides ? ' style="aspect-ratio:' . $imageRatio . '"' : '' ?>>
          <?php foreach ($sl as $i => $x): $hasImg = has_img($x['image']); $full = $hasImg; ?>
          <?php if ($full): ?>
          <div class="slide slide-full <?= $i === 0 ? 'active' : '' ?>">
            <img class="sf-img" src="<?= img($x['image']) ?>" alt="">
            <div class="slide-actions"><?php if (tr($x, 'btn')): ?><a class="btn btn-dark3d" href="<?= url($x['link']) ?>">🛒 <?= e(tr($x, 'btn')) ?> →</a><?php endif; ?><?php if (tr($x, 'btn2')): ?><a class="btn btn-outline" href="<?= url($x['link2']) ?>"><?= e(tr($x, 'btn2')) ?> <span class="circ">▶</span></a><?php endif; ?></div>
          </div>
          <?php else: ?>
          <div class="slide <?= $i === 0 ? 'active' : '' ?>">
            <div class="slide-actions"><?php if (tr($x, 'btn')): ?><a class="btn btn-dark3d" href="<?= url($x['link']) ?>">🛒 <?= e(tr($x, 'btn')) ?> →</a><?php endif; ?><?php if (tr($x, 'btn2')): ?><a class="btn btn-outline" href="<?= url($x['link2']) ?>"><?= e(tr($x, 'btn2')) ?> <span class="circ">▶</span></a><?php endif; ?></div>
            <div class="slide-vis"><?php if ($hasImg): ?><img class="slide-img" src="<?= img($x['image']) ?>" alt="<?= e(tr($x, 'title')) ?>"><?php else: ?>
              <div class="bowl3d" aria-hidden="true"><div class="items"><?php foreach ($sets[$i % 3] as $em) echo '<span>' . $em . '</span>'; ?></div><div class="bowl"></div><div class="seal"><b>100%</b><small>ORGANIC</small></div><span class="lf l1">🌿</span><span class="lf l2">🍃</span><span class="lf l3">🌿</span></div><?php endif; ?></div>
          </div><?php endif; endforeach; ?>
          <?php if (count($sl) > 1): ?><button class="arrow prev" aria-label="prev">‹</button><button class="arrow next" aria-label="next">›</button><div class="dots"><?php foreach ($sl as $i => $x) echo '<i class="' . ($i ? '' : 'on') . '"></i>'; ?></div><?php endif; ?>
        </div></section><?php break;
    case 'categories':
        $cs = rows('SELECT * FROM categories WHERE status=1 AND parent_id=0 AND show_home=1 ORDER BY sort_order,id LIMIT ' . max(1, (int)$s['limit_n'])); if (!$cs) return; ?>
        <section class="container sec"><?php section_head($s, false); echo hs_open(1); foreach ($cs as $c): ?>
          <a class="catc card3d" href="<?= url('shop.php?cat=' . e($c['slug'])) ?>"><span class="catc-img"><?= has_img($c['image']) ? '<img loading="lazy" src="' . img($c['image']) . '" alt="">' : '<span class="ph3d">' . e($c['icon'] ?: '🥜') . '</span>' ?></span><b><?= e(tr($c, 'name')) ?></b><i>→</i></a><?php endforeach; echo hs_close(); ?></section><?php break;
    case 'products':
        $ps = section_products_query($s); if (!$ps) return; ?>
        <section class="container sec"><?php section_head($s);
        if (($s['layout'] ?? 'grid') !== 'grid') { echo hs_open(0); foreach ($ps as $p) echo '<div class="hs-item">' . product_card($p) . '</div>'; echo hs_close(); }
        else { echo '<div class="grid">'; foreach ($ps as $p) echo product_card($p); echo '</div>'; } ?></section><?php break;
    case 'banner':
        if (!has_img($s['image2'] ?? '')) return;
        $offerImage = getimagesize(UPLOAD_DIR . $s['image2']); $offerRatio = $offerImage ? (int)$offerImage[0] . ' / ' . (int)$offerImage[1] : '3 / 1'; ?>
        <section class="container sec"><div class="offer-banner" style="aspect-ratio:<?= $offerRatio ?>">
          <img class="offer-banner-image" src="<?= img($s['image2']) ?>" alt="">
          <?php if (tr($s, 'btn')): ?><a class="btn btn-accent offer-banner-button" href="<?= url($s['link'] ?: 'shop.php') ?>"><?= e(tr($s, 'btn')) ?> →</a><?php endif; ?>
        </div></section><?php break;
    case 'features':
        $fs = rows('SELECT * FROM features WHERE status=1 ORDER BY sort_order,id'); if (!$fs) return; ?>
        <section class="container sec"><?php section_head($s, false) ?><div class="features"><?php foreach ($fs as $f): ?><div class="feat"><span class="fi"><?= icon_or_emoji($f['icon']) ?></span><b><?= e(tr($f, 'title')) ?></b><small><?= e(tr($f, 'sub')) ?></small></div><?php endforeach; ?></div></section><?php break;
    case 'testimonials':
        $ts = rows('SELECT * FROM testimonials WHERE status=1 ORDER BY sort_order,id LIMIT ' . max(1, (int)$s['limit_n'])); if (!$ts) return; ?>
        <section class="container sec"><?php section_head($s, false); echo hs_open(1); foreach ($ts as $x): ?>
          <div class="tcard card3d"><div class="av"><?= has_img($x['image']) ? '<img src="' . img($x['image']) . '" alt="">' : e(mb_substr($x['name'], 0, 1)) ?></div><div><p>“<?= e(tr($x, 'body')) ?>”</p><b>— <?= e($x['name']) ?></b><div class="star"><?= stars($x['rating']) ?></div></div></div><?php endforeach; echo hs_close(); ?></section><?php break;
    case 'newsletter': ?>
        <section class="container sec"><form class="news" id="newsForm"><div class="news-ic">✈️</div><div class="news-t"><h3><?= e(tr($s, 'title')) ?></h3><p><?= e(tr($s, 'subtitle')) ?></p></div>
          <div class="news-f"><input type="email" name="email" required placeholder="<?= t('enter_email') ?>"><button class="btn btn-accent"><?= e(tr($s, 'btn') ?: t('subscribe')) ?></button></div></form></section><?php break;
    }
}

function order_timeline($o) {
    $steps = ['pending' => '🕒', 'confirmed' => '✅', 'processing' => '📦', 'shipped' => '🚚', 'delivered' => '🏠'];
    $log = []; foreach (rows('SELECT status,created_at FROM order_log WHERE order_id=? ORDER BY id', [$o['id']]) as $l) $log[$l['status']] = $l['created_at'];
    if ($o['status'] === 'cancelled') return '<div class="alert err">❌ ' . t('s_cancelled') . '</div>';
    $idx = array_search($o['status'], array_keys($steps)); $h = '<div class="tl">';
    $i = 0; foreach ($steps as $k => $ic) { $h .= '<div class="tl-s ' . ($i <= $idx ? 'done' : '') . ($i === $idx ? ' now' : '') . '"><span class="tl-ic">' . $ic . '</span><b>' . status_label($k) . '</b><small>' . (isset($log[$k]) ? date('d M, h:i A', strtotime($log[$k])) : '') . '</small></div>'; $i++; }
    return $h . '</div>';
}
function order_detail_html($o) {
    $its = rows('SELECT * FROM order_items WHERE order_id=?', [$o['id']]);
    $h = '<div class="card-flat"><h3>' . t('order_timeline') . '</h3>' . order_timeline($o) . '</div><div class="card-flat"><h3>#' . e($o['order_no']) . ' <span class="pill s-' . $o['status'] . '">' . status_label($o['status']) . '</span></h3><div class="table-wrap"><table class="tbl"><tr><th>' . t('items') . '</th><th>' . t('amount') . '</th></tr>';
    foreach ($its as $i) $h .= '<tr><td>' . (has_img($i['image']) ? '<img class="ti" src="' . img($i['image']) . '" alt=""> ' : '') . e($i['name']) . ($i['variant'] ? ' <span class="vtag">' . e($i['variant']) . '</span>' : '') . ' × ' . $i['qty'] . '</td><td>' . money($i['price'] * $i['qty']) . '</td></tr>';
    $h .= '<tr><td>' . t('subtotal') . '</td><td>' . money($o['subtotal']) . '</td></tr><tr><td>' . t('shipping') . '</td><td>' . money($o['shipping']) . '</td></tr>' . ($o['discount'] > 0 ? '<tr><td>' . t('discount') . '</td><td>-' . money($o['discount']) . '</td></tr>' : '') . '<tr><td><b>' . t('total') . '</b></td><td><b>' . money($o['total']) . '</b></td></tr></table></div><p class="muted">' . e($o['name']) . ' · ' . e($o['phone']) . ' · ' . e($o['address']) . ', ' . e($o['city']) . '</p></div>';
    return $h;
}

function hero_title($t) { $t = str_replace(["\\n", "\r\n"], "\n", (string)$t); return nl2br(e($t)); }
