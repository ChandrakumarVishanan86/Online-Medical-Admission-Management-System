<div class="hero rounded p-4 mb-4"><h2>Medical &amp; Health Science Programs</h2><p class="mb-0">Browse programs, check eligibility and apply online.</p></div>
<div class="row g-3"><?php foreach($rows as $r): ?><div class="col-md-6"><div class="card h-100 shadow-sm"><div class="card-body">
<h5><?= e($r['name']) ?> <small class="text-muted">(<?= e($r['code']) ?>)</small></h5><p><?= e($r['description']) ?></p>
<ul class="small mb-3"><li>Eligibility: average &ge; <?= e($r['min_avg']) ?>, each of Bio/Chem/Physics &ge; <?= e($r['min_each']) ?></li>
<li>Intake: <?= e($r['seats']) ?> &middot; Seats left: <strong><?= e($r['seats_left']) ?></strong></li><li>Deadline: <?= e($r['deadline']) ?></li></ul>
<?php if(!user()): ?><a class="btn btn-primary btn-sm" href="<?= url('register') ?>">Register to apply</a>
<?php elseif(user()['role']==='applicant'): if(isset($mine[$r['id']])): ?><a class="btn btn-outline-primary btn-sm" href="<?= url('applications/'.$mine[$r['id']]) ?>">View my application</a>
<?php else: ?><form method="post" action="<?= url('apply/'.$r['id']) ?>"><?= csrf_field() ?><button class="btn btn-primary btn-sm">Start application</button></form><?php endif; endif; ?>
</div></div></div><?php endforeach; ?></div>
