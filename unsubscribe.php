<?php
require_once 'includes/core.php'; $t = preg_replace('/[^a-f0-9]/', '', $_GET['t'] ?? ''); $ok = false;
if ($t !== '') { $ok = q('UPDATE newsletter SET status=0 WHERE token=?', [$t])->rowCount() > 0 || (bool)val('SELECT id FROM newsletter WHERE token=? AND status=0', [$t]); }
$page_title = 'Unsubscribe'; require 'includes/header.php'; ?>
<div class="container sec center"><div class="success card-flat"><div class="big-ic"><?= $ok ? '✅' : '⚠' ?></div><h2><?= $ok ? t('unsub_done') : t('unsub_bad') ?></h2><a class="btn" href="<?= url() ?>"><?= t('back_home') ?></a></div></div>
<?php require 'includes/footer.php';
