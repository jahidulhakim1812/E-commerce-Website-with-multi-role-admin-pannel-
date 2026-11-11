<?php
require '_layout.php'; admin_start('Managers & Roles', 'users');
crud(['table' => 'users', 'title' => 'Managers & Admins', 'single' => 'Staff Member', 'where' => "role IN ('admin','manager')", 'search' => ['name', 'email'], 'defaults' => ['status' => 1, 'role' => 'manager', 'perms' => 'products,categories,orders,customers'],
 'can_delete' => fn($r) => $r['id'] != user()['id'],
 'fields' => [
  ['name' => 'name', 'label' => 'Full Name', 'req' => true, 'half' => true, 'list' => true], ['name' => 'email', 'label' => 'Email (login)', 'req' => true, 'half' => true, 'list' => true],
  ['name' => 'phone', 'label' => 'Phone', 'half' => true], ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'half' => true],
  ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'opts' => ['manager' => 'Manager (limited access)', 'admin' => 'Admin (full access)'], 'half' => true, 'list' => true],
  ['name' => 'status', 'label' => 'Account Active', 'type' => 'checkbox', 'half' => true, 'list' => true],
  ['name' => 'perms', 'label' => 'Manager Permissions', 'type' => 'checks', 'opts' => ['products' => 'Products, Coupons & Reviews', 'categories' => 'Categories', 'orders' => 'Orders', 'customers' => 'Customers & Messages', 'content' => 'Menu, Sections, Sliders, Pages', 'finance' => 'Finance & Expenses', 'visitors' => 'Visitor Analytics', 'reports' => 'Reports', 'settings' => 'Settings'], 'help' => 'Only applies to the Manager role. Admins always have full access.'],
 ]]);
admin_end();
