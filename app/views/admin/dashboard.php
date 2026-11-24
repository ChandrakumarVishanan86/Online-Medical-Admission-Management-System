<h4>Admissions Dashboard</h4><div class="row g-3 mb-4">
<?php foreach(['applicants'=>'Registered applicants','submitted'=>'Submitted applications','pending'=>'Pending review','shortlisted'=>'Shortlisted / interview','accepted'=>'Accepted'] as $k=>$l): ?>
<div class="col-md"><div class="card text-center shadow-sm"><div class="card-body"><div class="display-6"><?= e($m[$k]) ?></div><div class="small text-muted"><?= $l ?></div></div></div></div><?php endforeach; ?></div>
<table class="table bg-white shadow-sm"><tr><th>Program</th><th>Intake</th><th>Seats left</th><th>Deadline</th></tr>
<?php foreach($progs as $p): ?><tr><td><?= e($p['name']) ?></td><td><?= e($p['seats']) ?></td><td><?= e($p['seats_left']) ?></td><td><?= e($p['deadline']) ?></td></tr><?php endforeach; ?></table>
