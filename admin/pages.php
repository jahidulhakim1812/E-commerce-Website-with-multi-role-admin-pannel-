<?php
require '_layout.php'; admin_start('Pages', 'content');
crud(['table' => 'pages', 'title' => 'Pages (About, Policies, FAQ...)', 'single' => 'Page', 'defaults' => ['status' => 1],
 'fields' => [
  ['name' => 'slug', 'label' => 'Slug (URL: page.php?slug=...)', 'req' => true, 'half' => true, 'list' => true, 'slug_from' => 'title_en'], ['name' => 'title_en', 'label' => 'Title (English)', 'req' => true, 'half' => true, 'list' => true],
  ['name' => 'title_bn', 'label' => 'Title (বাংলা)'], ['name' => 'body_en', 'label' => 'Content (English, HTML allowed)', 'type' => 'textarea', 'rows' => 8], ['name' => 'body_bn', 'label' => 'Content (বাংলা, HTML allowed)', 'type' => 'textarea', 'rows' => 8],
  ['name' => 'status', 'label' => 'Published', 'type' => 'checkbox', 'list' => true],
 ]]);
admin_end();
