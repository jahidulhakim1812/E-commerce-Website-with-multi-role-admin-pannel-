<?php
require '_layout.php'; admin_start('Messages', 'customers');
echo '<div class="tabs-a"><a class="on" href="messages.php">Contact Messages</a></div>';
crud(['table' => 'contact_messages', 'title' => 'Contact Messages', 'single' => 'Message', 'can_add' => false, 'search' => ['name', 'email', 'message'], 'fields' => [
  ['name' => 'name', 'label' => 'Name', 'half' => true, 'list' => true], ['name' => 'email', 'label' => 'Email', 'half' => true, 'list' => true], ['name' => 'phone', 'label' => 'Phone', 'half' => true, 'list' => true],
  ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'list' => true], ['name' => 'is_read', 'label' => 'Marked as read', 'type' => 'checkbox']]]);
$n = rows('SELECT email,created_at FROM newsletter ORDER BY id DESC LIMIT 50');
echo '<div class="panel"><div class="panel-h"><h3>Newsletter Subscribers (' . (int)val('SELECT COUNT(*) FROM newsletter') . ')</h3></div><div class="chips">'; foreach ($n as $x) echo '<span class="chip">' . e($x['email']) . '</span>'; if (!$n) echo '<span class="muted">No subscribers yet</span>'; echo '</div></div>';
admin_end();
