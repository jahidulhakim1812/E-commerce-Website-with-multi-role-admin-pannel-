<?php
require '_layout.php';
admin_start('Payment Logos', 'content');
q("CREATE TABLE IF NOT EXISTS payment_logos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(120) NOT NULL,
    image VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
crud([
    'table' => 'payment_logos',
    'title' => 'Payment Logos',
    'single' => 'Payment Logo',
    'order' => 'sort_order ASC, id DESC',
    'defaults' => ['status' => 1, 'sort_order' => 0],
    'search' => ['title'],
    'fields' => [
        ['name' => 'title', 'label' => 'Logo title', 'type' => 'text', 'req' => true],
        ['name' => 'image', 'label' => 'Logo image', 'type' => 'image', 'req' => true],
        ['name' => 'sort_order', 'label' => 'Display order', 'type' => 'number', 'default' => 0],
        ['name' => 'status', 'label' => 'Active', 'type' => 'checkbox', 'default' => 1],
    ],
]);
admin_end();
