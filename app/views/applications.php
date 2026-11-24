<h4>My Applications</h4><table class="table bg-white shadow-sm"><tr><th>Ref</th><th>Program</th><th>Status</th><th></th></tr>
<?php foreach($apps as $a): ?><tr><td><?= e($a['ref_no']?:'-') ?></td><td><?= e($a['pname']) ?></td><td><?= badge($a['status']) ?></td><td><a href="<?= url('applications/'.$a['id']) ?>">Open</a></td></tr><?php endforeach; ?>
<?php if(!$apps): ?><tr><td colspan="4">No applications yet. <a href="<?= url('programs') ?>">Browse programs</a>.</td></tr><?php endif; ?></table>
