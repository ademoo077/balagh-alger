<?php $pageTitle = 'Organismes'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-building me-2 text-primary"></i> Organismes</h4>
    <a href="/organizations/create" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Nouvel organisme</a>
</div>
<div class="row">
<?php foreach ($organizations as $o): ?>
<div class="col-xl-4 col-md-6 mb-4">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
            <h5 class="card-title mb-1"><?= $o['name'] ?></h5>
            <?php if ($o['name_ar']): ?><p class="text-muted mb-2" dir="rtl"><?= $o['name_ar'] ?></p><?php endif; ?>
            <div class="d-flex gap-3 mb-3">
                <span class="badge bg-primary"><?= $o['users_count'] ?> utilisateurs</span>
                <span class="badge bg-info"><?= $o['reports_count'] ?> signalements</span>
            </div>
            <?php if ($o['phone']): ?><p class="mb-1"><i class="fas fa-phone me-2 text-muted"></i><?= $o['phone'] ?></p><?php endif; ?>
            <?php if ($o['email']): ?><p class="mb-0"><i class="fas fa-envelope me-2 text-muted"></i><?= $o['email'] ?></p><?php endif; ?>
        </div>
        <div class="card-footer bg-transparent border-0">
            <a href="/organizations/<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i> Voir</a>
            <a href="/organizations/<?= $o['id'] ?>/edit" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit me-1"></i> Modifier</a>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
