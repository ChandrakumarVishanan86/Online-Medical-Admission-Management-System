<div class="row justify-content-center"><div class="col-md-5"><div class="card shadow-sm"><div class="card-body p-4"><h4>Create applicant account</h4>
<?php foreach($errors as $x): ?><div class="alert alert-danger py-2"><?= e($x) ?></div><?php endforeach; ?>
<form method="post" action="<?= url('register') ?>"><?= csrf_field() ?>
<div class="mb-3"><label class="form-label">Full name</label><input name="name" class="form-control" value="<?= e($old['name']??'') ?>" required></div>
<div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($old['email']??'') ?>" required></div>
<div class="mb-3"><label class="form-label">Password (min 8)</label><input type="password" name="password" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Confirm password</label><input type="password" name="password2" class="form-control" required></div>
<button class="btn btn-primary w-100">Register</button></form></div></div></div></div>
