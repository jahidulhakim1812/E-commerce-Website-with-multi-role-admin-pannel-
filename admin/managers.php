<?php
require '_layout.php'; admin_start('Managers & Roles', 'users');
ensure_manager_action_code();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_save'])) {
 csrf_check();
 $id = (int)($_POST['id'] ?? 0);
 $existingCode = $id ? val("SELECT manager_action_code_hash FROM users WHERE id=? AND role IN ('admin','manager')", [$id]) : null;
 $codeValue = $_POST['manager_action_code'] ?? '';
 $code = is_string($codeValue) ? $codeValue : '';
 if (($_POST['role'] ?? '') === 'manager' && (($code === '' && !$existingCode) || ($code !== '' && (strlen($code) < 8 || strlen($code) > 72)))) {
  $_SESSION['old'] = $_POST; unset($_SESSION['old']['manager_action_code'], $_SESSION['old']['password']);
  flash($code === '' ? 'Set an action code of at least 8 characters for every manager.' : 'The action code must be 8–72 characters.', 'err');
  redirect('managers.php?a=' . ($id ? 'edit&id=' . $id : 'add'));
 }
}
crud(['table' => 'users', 'title' => 'Managers & Admins', 'single' => 'Staff Member', 'where' => "role IN ('admin','manager')", 'search' => ['name', 'email'], 'defaults' => ['status' => 1, 'role' => 'manager', 'perms' => ''],
 'can_delete' => fn($r) => $r['id'] != user()['id'],
 'after' => function ($id, $new, $d) {
  $code = $_POST['manager_action_code'] ?? '';
  if (($d['role'] ?? '') === 'manager' && is_string($code) && $code !== '') q('UPDATE users SET manager_action_code_hash=? WHERE id=?', [password_hash($code, PASSWORD_DEFAULT), $id]);
 },
 'fields' => [
  ['name' => 'name', 'label' => 'Full Name', 'req' => true, 'half' => true, 'list' => true], ['name' => 'email', 'label' => 'Email (login)', 'req' => true, 'half' => true, 'list' => true],
  ['name' => 'phone', 'label' => 'Phone', 'half' => true], ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'half' => true],
  ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'opts' => ['manager' => 'Manager (limited access)', 'admin' => 'Admin (full access)'], 'half' => true, 'list' => true],
  ['name' => 'status', 'label' => 'Account Active', 'type' => 'checkbox', 'half' => true, 'list' => true],
  ['name' => 'perms', 'label' => 'Manager Menu Permissions', 'type' => 'checks', 'opts' => ['products' => 'Products, Coupons & Reviews', 'categories' => 'Categories', 'orders' => 'Orders, Return Requests & Checkout Visits', 'customers' => 'Customers, Import, Profiles, Subscribers, Messages & Live Chat', 'content' => 'Header/Menu, Sections, Videos, Sliders, Offers, Badges, Payment Logos, Pages, Testimonials & Notifications', 'finance' => 'Finance', 'visitors' => 'Visitor Analytics', 'reports' => 'Reports', 'exports' => 'Export Center', 'settings' => 'Settings'], 'help' => 'Select every menu area this manager may access. Dashboard and profile stay available; Managers & Roles is admin-only.'],
  ['name' => 'manager_action_code_field', 'type' => 'custom', 'render' => fn($r, $id) => '<label>Manager Action Code</label><input type="password" name="manager_action_code" minlength="8" maxlength="72" autocomplete="new-password" placeholder="' . ($id ? 'Leave blank to keep current code' : 'Set an 8–72 character code') . '"><small>Required for managers. They must enter this code to add, update, or delete products, reviews, and customers.</small>'],
 ]]);
admin_end();
