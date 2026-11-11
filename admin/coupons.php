<?php
require '_layout.php'; admin_start('Coupons', 'products');
crud(['table' => 'coupons', 'title' => 'Discount Coupons', 'single' => 'Coupon', 'defaults' => ['status' => 1, 'type' => 'percent'],
 'fields' => [
  ['name' => 'code', 'label' => 'Coupon Code', 'req' => true, 'half' => true, 'list' => true], ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'opts' => ['percent' => 'Percentage %', 'fixed' => 'Fixed amount'], 'half' => true, 'list' => true],
  ['name' => 'value', 'label' => 'Value', 'type' => 'number', 'half' => true, 'list' => true], ['name' => 'min_order', 'label' => 'Minimum order amount', 'type' => 'number', 'half' => true],
  ['name' => 'max_uses', 'label' => 'Max uses (0 = unlimited)', 'type' => 'number', 'half' => true], ['name' => 'expires_at', 'label' => 'Expiry date', 'type' => 'date', 'half' => true, 'list' => true],
  ['name' => 'used', 'label' => 'Used', 'noform' => true, 'list' => true], ['name' => 'status', 'label' => 'Active', 'type' => 'checkbox', 'list' => true],
 ], 'after' => function ($id, $new, $d) { q('UPDATE coupons SET code=UPPER(code) WHERE id=?', [$id]); if ($new && !empty($d['status']) && S('notify_offers') === '1') { $c = row('SELECT * FROM coupons WHERE id=?', [$id]); $v = $c['type'] === 'percent' ? (float)$c['value'] . '% OFF' : money($c['value']) . ' OFF'; notify_create('offer', '🎁 New offer: ' . $v . ' with code ' . $c['code'], 'Use code ' . $c['code'] . ' at checkout' . ($c['min_order'] > 0 ? ' on orders over ' . money($c['min_order']) : '') . ($c['expires_at'] ? ' - valid until ' . date('d M Y', strtotime($c['expires_at'])) : '') . '.', 'shop.php?offer=1', '🎁 নতুন অফার: কোড ' . $c['code'], '', ''); } }]);
admin_end();
