<?php
// DryNutri configuration. The web installer (install.php) writes config.local.php for you.
if (file_exists(__DIR__ . '/config.local.php')) { require __DIR__ . '/config.local.php'; }
defined('DB_HOST') or define('DB_HOST', 'localhost');
defined('DB_NAME') or define('DB_NAME', 'drynutri');
defined('DB_USER') or define('DB_USER', 'root');
defined('DB_PASS') or define('DB_PASS', '');
define('ROOT_PATH', __DIR__);
define('UPLOAD_DIR', __DIR__ . '/uploads/');
