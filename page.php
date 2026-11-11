<?php
require_once 'includes/core.php'; $pg = row('SELECT * FROM pages WHERE slug=? AND status=1', [$_GET['slug'] ?? '']);
if (!$pg) http_response_code(404); $page_title = $pg ? tr($pg, 'title') : t('page_not_found'); require 'includes/header.php'; ?>
<div class="container sec"><div class="card-flat rich"><?php if ($pg): ?><h1><?= e(tr($pg, 'title')) ?></h1><?= LANG === 'bn' && $pg['body_bn'] ? $pg['body_bn'] : $pg['body_en'] ?><?php else: ?><h1>404</h1><a class="btn" href="<?= url() ?>"><?= t('back_home') ?></a><?php endif; ?></div></div>
<?php require 'includes/footer.php';
