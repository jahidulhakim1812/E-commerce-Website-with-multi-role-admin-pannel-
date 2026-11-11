<?php
require_once __DIR__ . "/includes/core.php";
$page_title = t('your_cart'); require 'includes/header.php';
$coupon = !empty($_SESSION['coupon']) ? find_coupon($_SESSION['coupon']) : null; $t = cart_totals($coupon);
?><div class="container sec"><h1 class="page-title">🛒 <?= t('your_cart') ?></h1>
<?php if (!$t['lines']): ?><div class="empty card-flat"><p><?= t('cart_empty') ?></p><a class="btn" href="<?= url('shop.php') ?>"><?= t('continue_shopping') ?></a></div>
<?php else: ?><div class="cart-wrap"><div class="card-flat"><?php foreach ($t['lines'] as $l): $p = $l['p']; ?>
  <div class="cline" data-key="<?= e($l['key']) ?>"><?= has_img($p['image']) ? '<img src="' . img($p['image']) . '" alt="">' : '<span class="ph3d sm">' . cat_icon($p['category_id']) . '</span>' ?><div class="ci"><a href="<?= url('product.php?slug=' . e($p['slug'])) ?>"><b><?= e(tr($p, 'name')) ?></b></a><?php if ($l['label']): ?> <span class="vtag"><?= e($l['label']) ?></span><?php endif; ?><div class="muted"><?= money($l['price']) ?></div></div>
   <div class="qty"><button type="button" data-cq="-1">−</button><input type="number" value="<?= $l['qty'] ?>" min="1" max="<?= $l['stock'] ?>" data-cqin><button type="button" data-cq="1">+</button></div>
   <b class="lt"><?= money($l['line']) ?></b><button class="x" data-crm title="<?= t('remove') ?>">✕</button></div><?php endforeach; ?></div>
 <aside class="card-flat sum"><h3><?= t('total') ?></h3>
  <div class="coupon"><input id="cpn" placeholder="<?= t('coupon') ?>" value="<?= e($_SESSION['coupon'] ?? '') ?>"><button class="btn btn-sm" id="cpnBtn"><?= t('apply_coupon') ?></button></div><small id="cpnMsg"></small>
  <div class="srow"><span><?= t('subtotal') ?></span><b id="sSub"><?= money($t['subtotal']) ?></b></div>
  <div class="srow"><span><?= t('discount') ?></span><b id="sDisc">-<?= money($t['discount']) ?></b></div>
  <div class="srow big"><span><?= t('total') ?></span><b id="sTot"><?= money($t['total']) ?></b></div>
  <p class="muted dnote">🚚 <?= t('delivery_note') ?></p>
  <a class="btn btn-block" href="<?= url('checkout.php') ?>"><?= t('proceed_checkout') ?></a></aside></div><?php endif; ?></div>
<?php require 'includes/footer.php';
