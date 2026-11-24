<div class="d-flex justify-content-between"><h4><?= e($a['pname']) ?> <?= badge($a['status']) ?></h4>
<?php if($a['status']!=='draft'): ?><a class="btn btn-outline-secondary btn-sm" href="<?= url('applications/'.$a['id'].'/slip') ?>">Admission slip</a><?php endif; ?></div>
<?php if($a['ref_no']): ?><p>Reference: <strong><?= e($a['ref_no']) ?></strong> &middot; Merit score: <strong><?= e($a['merit_score']) ?></strong></p><?php endif; ?>
<div class="row g-3"><div class="col-md-7"><div class="card card-body shadow-sm"><h6>Documents</h6>
<ul><?php foreach($docs as $d): ?><li><?= e($types[$d['doc_type']]) ?>: <?= e($d['original_name']) ?> <?= $d['verified']?'<span class="badge text-bg-success">Verified</span>':'' ?></li><?php endforeach; ?><?php if(!$docs): ?><li class="text-muted">None uploaded.</li><?php endif; ?></ul>
<?php if($a['status']==='draft'): ?><form method="post" enctype="multipart/form-data" action="<?= url('applications/'.$a['id'].'/upload') ?>" class="row g-2"><?= csrf_field() ?>
<div class="col-md-4"><select name="doc_type" class="form-select"><?php foreach($types as $k=>$v): ?><option value="<?= $k ?>"><?= e($v) ?></option><?php endforeach; ?></select></div>
<div class="col-md-5"><input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required></div><div class="col-md-3"><button class="btn btn-secondary w-100">Upload</button></div></form>
<hr><?php foreach($problems as $x): ?><div class="text-danger small"><?= e($x) ?></div><?php endforeach; ?>
<form method="post" action="<?= url('applications/'.$a['id'].'/submit') ?>" class="mt-2"><?= csrf_field() ?><button class="btn btn-primary" <?= $problems?'disabled':'' ?>>Submit application</button>
<span class="small text-muted ms-2">ID proof and exam results are required.</span></form><?php endif; ?></div></div>
<div class="col-md-5"><div class="card card-body shadow-sm"><h6>Status history</h6><ul class="small mb-0"><?php foreach($logs as $l): ?><li><?= e($l['created_at']) ?> &ndash; <?= badge($l['new_status']) ?> <?= e($l['note']) ?></li><?php endforeach; ?></ul></div></div></div>
