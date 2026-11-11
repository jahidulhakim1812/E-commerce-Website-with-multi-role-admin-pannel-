<?php
require '_layout.php'; admin_start('Trust Badges', 'content');
echo '<div class="alert ok">These appear in the <b>Why Choose DryNutri?</b> strip on the home page.</div>';
crud(['table' => 'features', 'title' => 'Why Choose – Trust Badges', 'single' => 'Badge', 'order' => 'sort_order, id', 'defaults' => ['status' => 1, 'icon' => 'sprout'],
 'fields' => [
  ['name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'opts' => fn() => array_combine($k = ['sprout','leaf','award','shield','truck','headphones','heart','star','gift','package','percent','clock','lock','zap','check-circle','tag','credit-card','phone','globe'], array_map('ucfirst', $k)), 'half' => true, 'list' => true, 'fmt' => fn($r) => icon_or_emoji($r['icon'], 22) . ' ' . e($r['icon'])], ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'half' => true, 'list' => true],
  ['name' => 'title_en', 'label' => 'Title (English)', 'half' => true, 'list' => true], ['name' => 'title_bn', 'label' => 'Title (বাংলা)', 'half' => true],
  ['name' => 'sub_en', 'label' => 'Second line (English)', 'half' => true, 'list' => true], ['name' => 'sub_bn', 'label' => 'Second line (বাংলা)', 'half' => true],
  ['name' => 'status', 'label' => 'Visible', 'type' => 'checkbox', 'list' => true],
 ]]);
admin_end();
