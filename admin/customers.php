<?php
require '_layout.php'; admin_start('Customers', 'customers');
crud(['table' => 'users', 'title' => 'Customers', 'single' => 'Customer', 'where' => "role='customer'", 'fixed' => ['role' => 'customer'], 'search' => ['name', 'email', 'phone'], 'defaults' => ['status' => 1],
 'fields' => [
  ['name' => 'avatar', 'label' => 'Photo', 'type' => 'image', 'list' => true],
  ['name' => 'name', 'label' => 'Full Name', 'req' => true, 'half' => true, 'list' => true, 'fmt' => fn($r) => e($r['name']) . ($r['is_guest'] ? '<span class="guesttag">from order</span>' : '')], ['name' => 'email', 'label' => 'Email', 'req' => true, 'half' => true, 'list' => true, 'fmt' => fn($r) => preg_match('/@guest\\.local$/', $r['email']) ? '—' : e($r['email'])],
  ['name' => 'phone', 'label' => 'Phone', 'half' => true, 'list' => true], ['name' => 'city', 'label' => 'City', 'half' => true],
  ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'rows' => 2], ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'half' => true],
  ['name' => 'status', 'label' => 'Account Active', 'type' => 'checkbox', 'half' => true, 'list' => true],
  ['name' => 'orders', 'label' => 'Orders', 'noform' => true, 'list' => true, 'fmt' => fn($r) => (int)val('SELECT COUNT(*) FROM orders WHERE user_id=?', [$r['id']]) . ' · ' . money(val("SELECT COALESCE(SUM(total),0) FROM orders WHERE user_id=? AND status IN " . REV_STATUS, [$r['id']]))],
  ['name' => 'created_at', 'label' => 'Joined', 'noform' => true, 'list' => true, 'fmt' => fn($r) => date('d M Y', strtotime($r['created_at']))],
 ]]);
admin_end();
