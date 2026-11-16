<?php
require_once 'includes/core.php';
$u = user();
if ($u) audit_activity('logout', 'Signed out', 'user', (int)$u['id'], $u);
$_SESSION = []; session_destroy(); redirect('index.php');
