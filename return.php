<?php
require_once 'includes/core.php';
if (!user()) { $_SESSION['back'] = url('return.php'); redirect('login.php'); }
ensure_return_requests();
$u = user(); $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $itemValue = $_POST['order_item_id'] ?? '';
    $itemId = is_scalar($itemValue) ? (int)$itemValue : 0;
    $reasonValue = $_POST['reason'] ?? '';
    $reason = is_string($reasonValue) ? trim($reasonValue) : '';
    $item = row("SELECT oi.id,oi.order_id,oi.name,oi.variant,oi.image
        FROM order_items oi JOIN orders o ON o.id=oi.order_id
        WHERE oi.id=? AND o.user_id=? AND o.status<>'cancelled'", [$itemId, $u['id']]);
    $hasOpenRequest = $item ? row("SELECT id FROM return_requests WHERE order_item_id=? AND status<>'rejected' LIMIT 1", [$itemId]) : null;
    $file = $_FILES['proof_image'] ?? null;
    if (!$item) $err = t('return_invalid_item');
    elseif ($hasOpenRequest) $err = t('return_already_requested');
    elseif ($reason === '' || strlen($reason) > 5000) $err = t('return_reason_required');
    elseif (!is_array($file) || !isset($file['error'], $file['name'], $file['tmp_name'], $file['size']) || !is_string($file['name']) || !is_string($file['tmp_name']) || !is_int($file['size']) || $file['error'] !== UPLOAD_ERR_OK) $err = t('return_image_required');
    else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $image = @getimagesize($file['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) || !$image || !in_array($image['mime'] ?? '', $allowedTypes, true) || $file['size'] > 5 * 1024 * 1024) {
            $err = t('return_image_invalid');
        } else {
            $proof = save_upload($file['tmp_name'], $file['name'], $file['size']);
            if (!$proof) $err = t('return_image_invalid');
            else {
                q('INSERT INTO return_requests(user_id,order_id,order_item_id,product_name,product_variant,product_image,proof_image,reason) VALUES(?,?,?,?,?,?,?,?)', [
                    $u['id'], $item['order_id'], $item['id'], $item['name'] ?: t('product'), $item['variant'], $item['image'], $proof, $reason
                ]);
                flash(t('return_submitted'));
                redirect('return.php');
            }
        }
    }
}
$items = rows("SELECT oi.id,oi.order_id,oi.name,oi.variant,o.order_no,o.created_at
    FROM order_items oi JOIN orders o ON o.id=oi.order_id
    WHERE o.user_id=? AND o.status<>'cancelled'
    AND NOT EXISTS (SELECT 1 FROM return_requests rr WHERE rr.order_item_id=oi.id AND rr.status<>'rejected')
    ORDER BY o.id DESC,oi.id DESC", [$u['id']]);
$requests = rows('SELECT rr.*,o.order_no FROM return_requests rr JOIN orders o ON o.id=rr.order_id WHERE rr.user_id=? ORDER BY rr.id DESC', [$u['id']]);
$page_title = t('return_title'); require 'includes/header.php';
?>
<div class="container sec">
 <div class="crumb"><a href="<?= url('account.php') ?>"><?= t('my_account') ?></a> › <?= t('return_title') ?></div>
 <h1 class="page-title"><?= t('return_title') ?></h1>
 <p class="muted"><?= t('return_intro') ?></p>
 <?php foreach (flash() as $f) echo '<div class="alert ' . e($f[0]) . '">' . e($f[1]) . '</div>'; ?>
 <?php if ($err): ?><div class="alert err"><?= e($err) ?></div><?php endif; ?>
 <form method="post" enctype="multipart/form-data" class="card-flat form return-form">
  <?= csrf_field() ?>
  <h2><?= t('return_request') ?></h2>
  <?php if ($items): ?>
   <label><?= t('return_product') ?>
    <select name="order_item_id" required>
     <option value=""><?= t('choose_option') ?></option>
     <?php foreach ($items as $item): ?><option value="<?= (int)$item['id'] ?>" <?= (int)($_POST['order_item_id'] ?? 0) === (int)$item['id'] ? 'selected' : '' ?>><?= e($item['order_no'] . ' · ' . $item['name'] . ($item['variant'] ? ' (' . $item['variant'] . ')' : '')) ?></option><?php endforeach; ?>
    </select>
   </label>
   <label><?= t('return_reason') ?><textarea name="reason" rows="5" maxlength="5000" required><?= e($_POST['reason'] ?? '') ?></textarea></label>
   <label><?= t('return_product_image') ?><input type="file" name="proof_image" accept="image/jpeg,image/png,image/gif,image/webp" required><small class="muted"><?= t('return_image_help') ?></small></label>
   <button class="btn"><?= t('return_submit') ?></button>
  <?php else: ?><p class="muted"><?= t('return_no_items') ?></p><?php endif; ?>
 </form>
 <h2 class="sec-title"><?= t('return_history') ?></h2>
 <?php if ($requests): ?><div class="card-flat table-wrap"><table class="tbl"><tr><th><?= t('order_no') ?></th><th><?= t('return_product') ?></th><th><?= t('date') ?></th><th><?= t('status') ?></th><th><?= t('return_product_image') ?></th><th><?= t('return_admin_note') ?></th></tr>
  <?php foreach ($requests as $request): ?><tr><td class="mono"><?= e($request['order_no']) ?></td><td><?= e($request['product_name']) ?><?= $request['product_variant'] ? ' · ' . e($request['product_variant']) : '' ?></td><td><?= date('d M Y', strtotime($request['created_at'])) ?></td><td><span class="pill s-<?= e($request['status']) ?>"><?= e(t('return_status_' . $request['status'])) ?></span></td><td><a href="<?= e(img($request['proof_image'])) ?>" target="_blank" rel="noopener"><img class="return-proof-thumb" src="<?= e(img($request['proof_image'])) ?>" alt="<?= e(t('return_product_image')) ?>"></a></td><td><?= e($request['admin_note'] ?: '—') ?></td></tr><?php endforeach; ?>
 </table></div><?php else: ?><p class="empty card-flat"><?= t('return_no_requests') ?></p><?php endif; ?>
</div>
<?php require 'includes/footer.php';
