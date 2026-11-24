<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e(cfg('app_name')) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>.brand{background:#0b4f6c}.hero{background:linear-gradient(135deg,#0b4f6c,#20a4a4);color:#fff}@media print{.no-print{display:none!important}}</style></head>
<body class="bg-light"><nav class="navbar navbar-expand-md navbar-dark brand no-print"><div class="container">
<a class="navbar-brand fw-semibold" href="<?= url('') ?>">&#9877; <?= e(cfg('app_name')) ?></a>
<div class="ms-auto d-flex gap-2 align-items-center text-white">
<a class="btn btn-sm btn-outline-light" href="<?= url('programs') ?>">Programs</a>
<?php if(user()): if(is_staff()): ?><a class="btn btn-sm btn-outline-light" href="<?= url('admin') ?>">Dashboard</a><a class="btn btn-sm btn-outline-light" href="<?= url('admin/applications') ?>">Applications</a><a class="btn btn-sm btn-outline-light" href="<?= url('admin/programs') ?>">Manage Programs</a>
<?php else: ?><a class="btn btn-sm btn-outline-light" href="<?= url('profile') ?>">Profile</a><a class="btn btn-sm btn-outline-light" href="<?= url('applications') ?>">My Applications</a><?php endif; ?>
<form method="post" action="<?= url('logout') ?>" class="m-0"><?= csrf_field() ?><button class="btn btn-sm btn-light"><?= e(user()['name']) ?> &middot; Logout</button></form>
<?php else: ?><a class="btn btn-sm btn-outline-light" href="<?= url('login') ?>">Login</a><a class="btn btn-sm btn-light" href="<?= url('register') ?>">Apply Now</a><?php endif; ?>
</div></div></nav>
<main class="container py-4">
<?php if($m=flash('ok')): ?><div class="alert alert-success"><?= e($m) ?></div><?php endif; ?>
<?php if($m=flash('err')): ?><div class="alert alert-danger"><?= e($m) ?></div><?php endif; ?>
<?= $content ?></main></body></html>
