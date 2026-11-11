</main>
<?php $fq = menu_tree('footer_quick'); $fc = menu_tree('footer_care'); $pm = array_filter(explode(',', S('payment_methods'))); try { q('CREATE TABLE IF NOT EXISTS payment_logos (id INT UNSIGNED NOT NULL AUTO_INCREMENT, title VARCHAR(120) NOT NULL, image VARCHAR(255) NOT NULL, sort_order INT NOT NULL DEFAULT 0, status TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'); $payment_logos = rows('SELECT * FROM payment_logos WHERE status=1 ORDER BY sort_order ASC, id ASC'); } catch (Exception $e) { $payment_logos = []; } ?>
<footer class="footer"><div class="container fgrid">
  <div class="fb"><a href="<?= url() ?>" class="logo"><?= logo_html('dark') ?></a>
    <p><?= e(sx('about')) ?></p>
    <div class="social"><?php foreach (['facebook', 'youtube', 'instagram', 'linkedin', 'pinterest'] as $k) if (S($k) !== '') echo '<a href="' . e(S($k)) . '" target="_blank" rel="noopener" aria-label="' . $k . '">' . ic($k, 18) . '</a>'; ?></div></div>
  <div><h4><?= t('quick_links') ?></h4><?php foreach ($fq as $m) echo '<a href="' . url($m['href']) . '">' . e($m['label']) . '</a>'; ?></div>
  <div><h4><?= t('customer_care') ?></h4><?php foreach ($fc as $m) echo '<a href="' . url($m['href']) . '">' . e($m['label']) . '</a>'; ?></div>
  <div><h4><?= t('contact_us') ?></h4><p>📞 <?= e(S('phone')) ?></p><p>✉️ <?= e(S('email')) ?></p><p>📍 <?= e(sx('address')) ?></p></div>
  <div><h4><?= t('we_accept') ?></h4>
    <?php if ($payment_logos): ?>
      <div class="accept-payments"><?php foreach ($payment_logos as $logo) echo '<div class="accept-pay-item"><img class="accept-pay-logo" src="' . e(img($logo['image'])) . '" alt="' . e($logo['title']) . '"></div>'; ?></div>
    <?php else: ?>
      <div class="pay"><?php foreach ($pm as $m) echo '<span>' . e(t(trim($m))) . '</span>'; ?></div>
    <?php endif; ?>
  </div>
</div><div class="container copy"><span><?= e(S('copyright')) ?></span><span class="copy-r"><a href="<?= url('page.php?slug=privacy') ?>">Privacy Policy</a><a href="<?= url('page.php?slug=terms') ?>">Terms &amp; Conditions</a><button class="dm" data-theme-toggle>🌙 <span class="dm-l"><?= LANG === 'bn' ? 'ডার্ক মোড' : 'Dark Mode' ?></span></button></span></div></footer>

<aside class="drawer drawer-r" id="cartDrawer"><div class="drawer-h"><b>🛒 <span id="cdTitle"><?= t('your_cart') ?></span></b><button data-close aria-label="close">✕</button></div>
  <div class="cd-free" id="cdFree"></div><div class="drawer-b cd-body" id="cdBody"></div>
  <div class="cd-foot"><div class="srow"><span><?= t('subtotal') ?></span><b id="cdSub"><?= money(0) ?></b></div><div class="srow big"><span><?= t('total') ?></span><b id="cdTot"><?= money(0) ?></b></div>
    <div class="cd-btns"><a class="btn btn-ghost" href="<?= url('cart.php') ?>"><?= t('cart') ?></a><a class="btn" href="<?= url('checkout.php') ?>"><?= t('checkout') ?> →</a></div></div></aside>
<button class="cart-fab" data-open-cart aria-label="cart"><span class="cf-ic">🛍️<i class="cnt js-cnt"><?= cart_count() ?></i></span><span class="cf-t"><small><span class="js-cnt2"><?= cart_count() ?></span> <?= LANG === 'bn' ? 'পণ্য' : 'items' ?></small><b class="js-tot"><?= money(cart_totals()['subtotal']) ?></b></span></button>
<?php if (S('float_enabled') === '1'): ?>
<div class="float-contact" id="fc"><div class="fc-list">
  <?php if (S('float_whatsapp')): ?><a class="fc-wa" target="_blank" rel="noopener" href="https://wa.me/<?= e(preg_replace('/\D/', '', S('float_whatsapp'))) ?>" title="WhatsApp">💬</a><?php endif; ?>
  <?php if (S('float_messenger')): ?><a class="fc-ms" target="_blank" rel="noopener" href="<?= e(S('float_messenger')) ?>" title="Messenger">✉</a><?php endif; ?>
  <?php if (S('float_phone')): ?><a class="fc-ph" href="tel:<?= e(S('float_phone')) ?>" title="Call">📞</a><?php endif; ?>
  <?php if (S('float_email')): ?><a class="fc-em" href="mailto:<?= e(S('float_email')) ?>" title="Email">📧</a><?php endif; ?></div>
  <button class="fc-main" id="fcBtn" aria-label="contact">💬</button></div><?php endif; ?>
<a href="#" class="to-top" id="toTop" aria-label="top">↑</a><div class="toast" id="toast"></div>
<script src="<?= asset('js/app.js') ?>?v=5"></script>
</body></html>
