<h4>My Profile</h4><?php foreach($errors as $x): ?><div class="alert alert-danger py-2"><?= e($x) ?></div><?php endforeach; ?>
<form method="post" action="<?= url('profile') ?>" class="card card-body shadow-sm row g-3 mx-0"><?= csrf_field() ?>
<?php $f=fn($n,$l,$t='text',$x='')=>'<div class="col-md-4"><label class="form-label">'.$l.'</label><input type="'.$t.'" name="'.$n.'" class="form-control" value="'.e($GLOBALS['p'][$n]??'').'" '.$x.' required></div>'; $GLOBALS['p']=$p;
echo $f('dob','Date of birth','date'), $f('phone','Phone'), $f('nic','NIC'); ?>
<div class="col-md-4"><label class="form-label">Gender</label><select name="gender" class="form-select" required><option value="">Select</option>
<?php foreach(['male','female','other'] as $g): ?><option value="<?= $g ?>" <?= ($p['gender']??'')===$g?'selected':'' ?>><?= ucfirst($g) ?></option><?php endforeach; ?></select></div>
<div class="col-md-8"><label class="form-label">Address</label><input name="address" class="form-control" value="<?= e($p['address']??'') ?>" required></div>
<?= $f('exam_year','A/L exam year','number'), $f('bio_marks','Biology marks','number','step="0.01" min="0" max="100"'), $f('chem_marks','Chemistry marks','number','step="0.01" min="0" max="100"'), $f('phys_marks','Physics marks','number','step="0.01" min="0" max="100"') ?>
<div class="col-12"><button class="btn btn-primary">Save profile</button></div></form>
