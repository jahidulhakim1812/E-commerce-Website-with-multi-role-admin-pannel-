<?php
require '_layout.php'; admin_start('My Profile');
$u = user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (isset($_POST['profile'])) {
        $name = trim($_POST['name'] ?? ''); $em = strtolower(trim($_POST['email'] ?? '')); $err = '';
        if ($name === '') $err = 'Name is required.'; elseif (!filter_var($em, FILTER_VALIDATE_EMAIL)) $err = 'Enter a valid email address.'; elseif (val('SELECT id FROM users WHERE email=? AND id<>?', [$em, $u['id']])) $err = 'That email is already used by another account.';
        if ($err) flash($err, 'err');
        else { $av = !empty($_POST['rm_avatar']) ? '' : upload_image('avatar', $u['avatar'] ?? ''); q('UPDATE users SET name=?,email=?,phone=?,avatar=? WHERE id=?', [$name, $em, trim($_POST['phone'] ?? ''), $av, $u['id']]); flash('Profile updated.'); }
    }
    if (isset($_POST['pass'])) {
        if (!password_verify($_POST['cur'] ?? '', $u['password'])) flash('Current password is incorrect.', 'err');
        elseif (strlen($_POST['new'] ?? '') < 6) flash('New password must be at least 6 characters.', 'err');
        elseif (($_POST['new'] ?? '') !== ($_POST['new2'] ?? '')) flash('The two new passwords do not match.', 'err');
        else { q('UPDATE users SET password=? WHERE id=?', [password_hash($_POST['new'], PASSWORD_DEFAULT), $u['id']]); flash('Password changed successfully.'); }
    }
    redirect('profile.php');
}
$orders = $u['role'] === 'admin' ? (int)val('SELECT COUNT(*) FROM orders') : (int)val('SELECT COUNT(*) FROM orders');
echo '<div class="profile-grid"><div class="panel profile-card"><div class="bigav">' . (has_img($u['avatar'] ?? '') ? '<img src="' . img($u['avatar']) . '" alt="">' : e(mb_substr($u['name'], 0, 1))) . '</div><h3>' . e($u['name']) . '</h3><span class="pill s-confirmed">' . ($u['role'] === 'admin' ? 'Administrator' : 'Manager') . '</span>
<dl><dt>Email</dt><dd>' . e($u['email']) . '</dd><dt>Phone</dt><dd>' . e($u['phone'] ?: '—') . '</dd><dt>Member since</dt><dd>' . date('d M Y', strtotime($u['created_at'])) . '</dd><dt>Last login</dt><dd>' . ($u['last_login'] ? date('d M Y, h:i A', strtotime($u['last_login'])) : '—') . '</dd></dl><p style="margin-top:16px"><a class="abtn ghost" href="../logout.php">⏻ Logout</a></p></div><div>';
echo '<div class="panel"><div class="panel-h"><h3>✎ Edit profile</h3></div><form method="post" enctype="multipart/form-data" class="aform">' . csrf_field() . '<input type="hidden" name="profile" value="1"><div class="fgrid2">
<div class="fld half"><label>Full name *</label><input type="text" name="name" required value="' . e($u['name']) . '"></div><div class="fld half"><label>Email (login) *</label><input type="text" name="email" required value="' . e($u['email']) . '"></div>
<div class="fld half"><label>Phone</label><input type="text" name="phone" value="' . e($u['phone']) . '"></div>
<div class="fld half"><label>Profile photo</label>' . (has_img($u['avatar'] ?? '') ? '<div class="prev"><img src="' . img($u['avatar']) . '" alt=""><label><input type="checkbox" name="rm_avatar" value="1"> Remove photo</label></div>' : '') . '<input type="file" name="avatar" accept="image/*"></div></div>
<div class="form-actions"><button class="abtn">💾 Save profile</button></div></form></div>';
echo '<div class="panel"><div class="panel-h"><h3>🔒 Change password</h3></div><form method="post" class="aform">' . csrf_field() . '<input type="hidden" name="pass" value="1"><div class="fgrid2">
<div class="fld full"><label>Current password</label><input type="password" name="cur" required autocomplete="current-password"></div><div class="fld half"><label>New password (min 6)</label><input type="password" name="new" required minlength="6" autocomplete="new-password"></div><div class="fld half"><label>Repeat new password</label><input type="password" name="new2" required minlength="6" autocomplete="new-password"></div></div>
<div class="form-actions"><button class="abtn">Update password</button></div></form></div></div></div>';
admin_end();
