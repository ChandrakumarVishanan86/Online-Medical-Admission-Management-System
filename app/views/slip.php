<div class="card card-body shadow-sm"><h3 class="text-center"><?= e(cfg('app_name')) ?></h3><h5 class="text-center mb-4">Application Acknowledgment Slip</h5>
<table class="table table-sm"><tr><th>Reference</th><td><?= e($a['ref_no']) ?></td><th>Status</th><td><?= badge($a['status']) ?></td></tr>
<tr><th>Applicant</th><td><?= e($pf['name']) ?></td><th>NIC</th><td><?= e($pf['nic']) ?></td></tr><tr><th>Program</th><td><?= e($a['pname']) ?> (<?= e($a['code']) ?>)</td><th>Submitted</th><td><?= e($a['submitted_at']) ?></td></tr>
<tr><th>Marks (Bio/Chem/Phys)</th><td><?= e($pf['bio_marks'].' / '.$pf['chem_marks'].' / '.$pf['phys_marks']) ?></td><th>Merit score</th><td><?= e($a['merit_score']) ?></td></tr></table>
<p class="small text-muted">This slip acknowledges receipt only and is not an offer of admission.</p><button onclick="print()" class="btn btn-primary no-print">Print</button></div>
