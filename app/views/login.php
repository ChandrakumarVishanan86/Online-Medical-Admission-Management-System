<div class="row justify-content-center"><div class="col-md-5"><div class="card shadow-sm"><div class="card-body p-4"><h4>Sign in</h4>
<?php foreach($errors as $x): ?><div class="alert alert-danger py-2"><?= e($x) ?></div><?php endforeach; ?>
<form method="post" action="<?= url('login') ?>"><?= csrf_field() ?>
<div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= e($old['email']??'') ?>" required></div>
<div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
<button class="btn btn-primary w-100">Login</button></form>
<p class="mt-3 mb-0 small">New applicant? <a href="<?= url('register') ?>">Create an account</a></p></div></div></div></div>
