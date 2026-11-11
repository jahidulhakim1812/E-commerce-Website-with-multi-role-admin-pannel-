<?php
require_once __DIR__ . '/core.php'; require_once __DIR__ . '/ui.php';
ob_start('render_icons');
track_visit();
$u = user(); $nav = menu_tree('header'); $pt = $page_title ?? S('site_name');
$tb = array_filter(array_map('trim', explode('|', sx('topbar')))); $tbi = ['🚚', '🌿', '🎧', '⭐'];
$curp = basename(strtok($_SERVER['REQUEST_URI'], '?')); $curq = $_SERVER['REQUEST_URI'];
function nav_active($href) { global $curp, $curq; $h = basename(strtok($href, '?')); if ($h === 'index.php' || $h === '') return $curp === 'index.php' || $curp === basename(base_url()) || $curp === ''; if (strpos($href, '?') !== false) return strpos($curq, substr($href, strpos($href, '?'))) !== false && $curp === $h; return $curp === $h && strpos($curq, '?') === false; }
?><!DOCTYPE html>
<html lang="<?= LANG ?>" data-theme="light">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= e($pt) ?> | <?= e(S('site_name')) ?></title>
<meta name="description" content="<?= e($meta_desc ?? sx('about')) ?>"><meta name="csrf" content="<?= csrf() ?>"><meta name="theme-color" content="<?= e(S('primary_color', '#1f5c3a')) ?>">
<link rel="icon" href="<?= favicon_url() ?>"><link rel="apple-touch-icon" href="<?= asset('img/apple-touch-icon.png') ?>">
<meta property="og:type" content="website"><meta property="og:site_name" content="<?= e(S('site_name')) ?>"><meta property="og:title" content="<?= e($pt) ?>"><meta property="og:description" content="<?= e($meta_desc ?? sx('about')) ?>"><meta property="og:image" content="<?= e($og_image ?? logo_url()) ?>">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=13">
<style>:root{--green:<?= e(S('primary_color', '#1f5c3a')) ?>;--accent:<?= e(S('accent_color', '#e8923a')) ?>}</style>
<script>(function(){var t=localStorage.getItem('theme');if(!t&&window.matchMedia('(prefers-color-scheme: dark)').matches)t='dark';document.documentElement.setAttribute('data-theme',t||'light')})();window.IC=<?= json_encode(['x' => ic('x'), 'cart' => ic('cart', 44), 'truck' => ic('truck'), 'tag' => ic('tag'), 'gift' => ic('gift'), 'megaphone' => ic('megaphone'), 'prev' => ic('chevron-left', 28), 'next' => ic('chevron-right', 28)]) ?>;window.BASE="<?= base_url() ?>";window.PUSH_PUBLIC_KEY=<?= json_encode(S('push_public_key')) ?>;window.PUSH_ENABLED=<?= json_encode(S('push_enabled') === '1') ?>;window.CURS=<?= json_encode(S('currency', '৳')) ?>;window.T={freeW:<?= json_encode(t('free')) ?>,shipW:<?= json_encode(t('shipping')) ?>,nonew:"<?= e(t('no_notifs')) ?>",nprod:"<?= e(t('n_product')) ?>",noffer:"<?= e(t('n_offer')) ?>",nmsg:"<?= e(t('n_message')) ?>",enablePush:"<?= e(t('enable_alerts')) ?>",disablePush:"<?= e(t('disable_alerts')) ?>",added:"<?= e(t('added_cart')) ?>",cart:"<?= e(t('your_cart')) ?>",empty:"<?= e(t('cart_empty')) ?>",free:"<?= e(LANG === 'bn' ? 'ফ্রি ডেলিভারি পেতে আর' : 'Add') ?>",free2:"<?= e(LANG === 'bn' ? 'এর পণ্য যোগ করুন' : 'more for free delivery') ?>",free3:"<?= e(t('free_delivery')) ?>"};</script>
</head>
<body class="lang-<?= LANG ?><?= !empty($auth_page) ? ' auth-flow' : '' ?>">
<div class="topbar"><div class="container topbar-in">
  <div class="tb-items"><?php $i = 0; foreach ($tb as $x): ?><span class="tb-i"><i><?= $tbi[$i++ % 4] ?></i><?= e($x) ?></span><?php endforeach; ?></div>
  <div class="tb-r"><button class="theme-toggle" data-theme-toggle aria-label="theme"><span class="sun">☀️</span><span class="moon">🌙</span></button>
  <span class="langsw"><a class="<?= LANG === 'en' ? 'on' : '' ?>" href="<?= e(lang_url('en')) ?>">🇬🇧 English</a><a class="<?= LANG === 'bn' ? 'on' : '' ?>" href="<?= e(lang_url('bn')) ?>">বাংলা</a></span></div></div></div>
<header class="header"><div class="container head-in">
  <button class="ham" id="menuBtn" aria-label="menu"><span></span><span></span><span></span></button>
  <a href="<?= url() ?>" class="logo"><?= logo_html('header') ?></a>
  <form class="search" action="<?= url('shop.php') ?>"><input name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="<?= t('search_ph') ?>" autocomplete="off"><button aria-label="search">🔍</button></form>
  <div class="head-actions">
    <div class="dd"><a href="<?= $u ? url($u['role'] === 'customer' ? 'account.php' : 'admin/') : url('login.php') ?>" class="ha"><span><?= ($u && has_img($u['avatar'] ?? '')) ? '<img class="ha-av" src="' . img($u['avatar']) . '" alt="">' : '👤' ?></span><small><?= $u ? e(explode(' ', $u['name'])[0]) : t('account') ?> ▾</small></a>
      <div class="dd-menu"><?php if ($u): ?><?php if ($u['role'] !== 'customer'): ?><a href="<?= url('admin/') ?>"><?= $u['role'] === 'admin' ? 'Admin Panel' : 'Manager Panel' ?></a><?php endif; ?><a href="<?= url('account.php') ?>"><?= t('my_orders') ?></a><a href="<?= url('account.php?tab=profile') ?>"><?= t('profile') ?></a><a href="<?= url('track.php') ?>"><?= t('track_order') ?></a><a href="<?= url('logout.php') ?>"><?= t('logout') ?></a>
      <?php else: ?><a href="<?= url('login.php') ?>"><?= t('sign_in') ?></a><a href="<?= url('register.php') ?>"><?= t('register') ?></a><a href="<?= url('track.php') ?>"><?= t('track_order') ?></a><?php endif; ?></div></div>
    <div class="nf"><button class="ha" id="bellBtn" aria-label="<?= t('notifications') ?>"><span>🔔<i class="cnt" id="nfCnt" hidden>0</i></span><small><?= t('alerts') ?></small></button>
      <div class="nf-pop" id="nfPop"><div class="nf-h"><b><?= t('notifications') ?></b><button type="button" id="nfAll"><?= t('mark_read') ?></button></div><div class="nf-list" id="nfList"></div>
        <div class="nf-f"><button type="button" class="btn btn-sm btn-ghost" id="nfPerm" hidden>🔔 <?= t('enable_alerts') ?></button><small id="nfPushHelp" hidden><?= t('push_help') ?></small>
          <form id="nfForm"><input type="email" name="email" required placeholder="<?= t('get_email') ?>"><button class="btn btn-sm"><?= t('subscribe') ?></button></form></div></div></div>
    <a href="<?= url('wishlist.php') ?>" class="ha ha-w"><span>♡</span><small><?= t('wishlist') ?></small></a>
    <button class="ha" data-open-cart aria-label="cart"><span>🛒<i class="cnt js-cnt"><?= cart_count() ?></i></span><small><?= t('cart') ?></small></button>
  </div></div></header>
<nav class="nav"><div class="container"><ul class="menu"><?php foreach ($nav as $m): $act = nav_active($m['href']); ?>
  <li class="<?= $m['children'] ? 'has' : '' ?>"><a class="<?= $act ? 'on' : '' ?>" href="<?= url($m['href']) ?>"><?= ($m['href'] === 'index.php') ? '🏠 ' : '' ?><?= e($m['label']) ?><?= !empty($m['badge']) ? '<em class="mb">' . e($m['badge']) . '</em>' : '' ?><?= $m['children'] ? ' <i class="car">⌄</i>' : '' ?></a><?php if ($m['children']): ?><ul class="sub"><?php foreach ($m['children'] as $c): ?><li><a href="<?= url($c['href']) ?>"><?= e($c['label']) ?></a></li><?php endforeach; ?></ul><?php endif; ?></li><?php endforeach; ?></ul></div></nav>
<div class="overlay" id="overlay"></div>
<aside class="drawer drawer-l" id="menuDrawer"><div class="drawer-h"><a href="<?= url() ?>" class="dl"><?= logo_html('dark') ?></a><button data-close aria-label="close">✕</button></div>
  <div class="drawer-b">
    <div class="d-user"><?php if ($u): ?><?= user_avatar($u) ?><div><b><?= e($u['name']) ?></b><small><a href="<?= url($u['role'] === 'customer' ? 'account.php' : 'admin/') ?>"><?= t('my_account') ?></a> · <a href="<?= url('logout.php') ?>"><?= t('logout') ?></a></small></div>
      <?php else: ?><a class="btn btn-sm" href="<?= url('login.php') ?>"><?= t('sign_in') ?></a><a class="btn btn-sm btn-ghost" href="<?= url('register.php') ?>"><?= t('register') ?></a><?php endif; ?></div>
    <div class="d-nav"><?php foreach ($nav as $m): ?><div class="d-item <?= $m['children'] ? 'has' : '' ?>"><a href="<?= url($m['href']) ?>"><?= e($m['label']) ?><?= !empty($m['badge']) ? '<em class="mb">' . e($m['badge']) . '</em>' : '' ?></a><?php if ($m['children']): ?><button class="d-tg" aria-label="expand">⌄</button><div class="d-sub"><?php foreach ($m['children'] as $c) echo '<a href="' . url($c['href']) . '">' . e($c['label']) . '</a>'; ?></div><?php endif; ?></div><?php endforeach; ?></div>
    <div class="d-links"><a href="<?= url('wishlist.php') ?>">♡ <?= t('wishlist') ?></a><a href="<?= url('track.php') ?>">📦 <?= t('track_order') ?></a><a href="<?= url('contact.php') ?>">✉️ <?= t('contact') ?></a><a href="<?= url('page.php?slug=faq') ?>">❓ <?= t('help') ?></a></div>
    <div class="drawer-ctl"><button class="btn btn-ghost btn-sm" data-theme-toggle>🌓 <?= t('theme') ?></button><a class="btn btn-ghost btn-sm" href="<?= e(lang_url(LANG === 'en' ? 'bn' : 'en')) ?>">🌐 <?= LANG === 'en' ? 'বাংলা' : 'English' ?></a></div>
    <div class="d-contact"><p>📞 <?= e(S('phone')) ?></p><p>✉️ <?= e(S('email')) ?></p></div>
  </div></aside>
<main>
