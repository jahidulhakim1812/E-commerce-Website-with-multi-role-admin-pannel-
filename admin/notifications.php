<?php
require '_layout.php'; admin_start('Notifications', 'content');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['compose']) || isset($_POST['resend']))) {
    csrf_check();
    if (isset($_POST['resend'])) { $n = (int)$_POST['resend']; $c = notify_email($n); flash(S('mail_enabled') === '1' ? "Email sent to $c people." : 'Turn on email in Settings → Email (SMTP) first.', S('mail_enabled') === '1' ? 'ok' : 'err'); redirect('notifications.php'); }
    $te = trim($_POST['title_en'] ?? ''); if ($te === '') { flash('Title is required.', 'err'); redirect('notifications.php'); }
    $type = in_array($_POST['type'] ?? '', ['message', 'offer', 'product']) ? $_POST['type'] : 'message';
    $img = upload_image('image'); $mail = !empty($_POST['email']);
    $id = notify_create($type, $te, trim($_POST['body_en'] ?? ''), trim($_POST['link'] ?? ''), trim($_POST['title_bn'] ?? ''), trim($_POST['body_bn'] ?? ''), $img, false);
    $sent = 0; if ($mail) { if (S('mail_enabled') === '1') $sent = notify_email($id); }
    flash('Notification published on the website bell' . ($mail ? (S('mail_enabled') === '1' ? " and emailed to $sent subscribers & customers." : '. Email NOT sent – enable SMTP in Settings first.') : '.'), ($mail && S('mail_enabled') !== '1') ? 'err' : 'ok');
    redirect('notifications.php');
}
$rc = count(notify_recipients()); $subs = (int)val('SELECT COUNT(*) FROM newsletter'); $cus = (int)val("SELECT COUNT(*) FROM users WHERE role='customer'");
echo '<div class="cards three"><div class="mini c3d"><small>Newsletter subscribers</small><b>' . $subs . '</b></div><div class="mini c3d"><small>Registered customers</small><b>' . $cus . '</b></div><div class="mini c3d"><small>Email recipients (unique)</small><b>' . $rc . '</b></div></div>';
echo '<div class="alert ok">Every visitor sees notifications in the 🔔 bell. Visitors who opt in under Settings → Browser Push can receive updates when they are away, without sharing contact details. Subscribers &amp; registered customers can also get an <b>email</b>. New products and coupons are announced <b>automatically</b> (change in Settings → Notifications).</div>';
echo '<div class="panel"><div class="panel-h"><h3>📢 Send a custom message / offer</h3></div><form method="post" enctype="multipart/form-data" class="aform">' . csrf_field() . '<input type="hidden" name="compose" value="1"><div class="fgrid2">
<div class="fld half"><label>Type</label><select name="type"><option value="message">📢 Custom message</option><option value="offer">🎁 Offer / discount</option><option value="product">🆕 New product</option></select></div>
<div class="fld half"><label>Link (optional) e.g. shop.php?offer=1</label><input type="text" name="link" placeholder="shop.php"></div>
<div class="fld half"><label>Title (English) *</label><input type="text" name="title_en" required></div><div class="fld half"><label>Title (বাংলা)</label><input type="text" name="title_bn"></div>
<div class="fld half"><label>Message (English)</label><textarea name="body_en" rows="3"></textarea></div><div class="fld half"><label>Message (বাংলা)</label><textarea name="body_bn" rows="3"></textarea></div>
<div class="fld half"><label>Picture (optional)</label><input type="file" name="image" accept="image/*"></div>
<div class="fld half"><label>Email</label><label class="sw"><input type="checkbox" name="email" value="1"><span></span> Also email all subscribers &amp; customers (' . $rc . ')</label></div></div>
<div class="form-actions"><button class="abtn">🚀 Publish now</button></div></form></div>';
crud(['table' => 'notifications', 'title' => 'Notification history', 'single' => 'Notification', 'can_add' => false, 'search' => ['title_en', 'body_en'],
 'actions' => fn($r) => '<form method="post" style="display:inline">' . csrf_field() . '<input type="hidden" name="resend" value="' . $r['id'] . '"><button class="abtn sm ghost" title="Email subscribers & customers">✉️ Email</button></form>',
 'fields' => [
  ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'opts' => ['message' => 'Message', 'offer' => 'Offer', 'product' => 'New product'], 'half' => true, 'list' => true],
  ['name' => 'link', 'label' => 'Link', 'half' => true], ['name' => 'title_en', 'label' => 'Title (English)', 'half' => true, 'list' => true, 'req' => true], ['name' => 'title_bn', 'label' => 'Title (বাংলা)', 'half' => true],
  ['name' => 'body_en', 'label' => 'Message (English)', 'type' => 'textarea', 'rows' => 3], ['name' => 'body_bn', 'label' => 'Message (বাংলা)', 'type' => 'textarea', 'rows' => 3],
  ['name' => 'image', 'label' => 'Picture', 'type' => 'image'], ['name' => 'status', 'label' => 'Visible in bell', 'type' => 'checkbox', 'list' => true],
  ['name' => 'emailed', 'label' => 'Emailed to', 'noform' => true, 'list' => true, 'fmt' => fn($r) => (int)$r['emailed'] . ' people'],
  ['name' => 'created_at', 'label' => 'Sent', 'noform' => true, 'list' => true, 'fmt' => fn($r) => date('d M Y h:i A', strtotime($r['created_at']))],
 ]]);
admin_end();
