<?php
require '_layout.php'; admin_start('Testimonials', 'content');
crud(['table' => 'testimonials', 'title' => 'Customer Testimonials', 'single' => 'Testimonial', 'order' => 'sort_order, id', 'defaults' => ['status' => 1, 'rating' => 5],
 'fields' => [
  ['name' => 'image', 'label' => 'Photo', 'type' => 'image', 'list' => true], ['name' => 'name', 'label' => 'Customer Name', 'req' => true, 'half' => true, 'list' => true], ['name' => 'rating', 'label' => 'Rating (1-5)', 'type' => 'number', 'half' => true, 'list' => true],
  ['name' => 'body_en', 'label' => 'Review (English)', 'type' => 'textarea', 'rows' => 3, 'list' => true], ['name' => 'body_bn', 'label' => 'Review (বাংলা)', 'type' => 'textarea', 'rows' => 3],
  ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'half' => true], ['name' => 'status', 'label' => 'Visible', 'type' => 'checkbox', 'half' => true, 'list' => true],
 ]]);
admin_end();
