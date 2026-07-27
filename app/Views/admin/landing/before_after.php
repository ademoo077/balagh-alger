<?php $pageTitle = 'Avant / Après — Landing Page'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-arrows-left-right me-2 text-warning"></i> Avant / Après</h4>
    <div class="d-flex gap-2">
        <a href="/admin/landing" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
        <a href="/admin/landing/before-after/create" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Ajouter</a>
    </div>
</div>

<?php if (empty($items)): ?>
<div class="alert alert-info"><i class="fas fa-info-circle me-1"></i> Aucun élément avant/après.</div>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($items as $item): ?>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="row g-0">
                <div class="col-6" style="height:120px;overflow:hidden;background:#f0f0f0;">
                    <img src="<?= htmlspecialchars($item['before_image']) ?>" alt="Avant" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                    <span class="badge bg-dark" style="position:absolute;top:5px;left:5px;font-size:0.6rem;">Avant</span>
                </div>
                <div class="col-6" style="height:120px;overflow:hidden;background:#f0f0f0;position:relative;">
                    <img src="<?= htmlspecialchars($item['after_image']) ?>" alt="Après" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                    <span class="badge bg-success" style="position:absolute;top:5px;right:5px;font-size:0.6rem;">Après</span>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong class="small"><?= htmlspecialchars($item['title_fr']) ?></strong>
                        <br><small class="text-muted"><?= htmlspecialchars($item['desc_fr']) ?></small>
                    </div>
                    <span class="badge bg-<?= $item['is_active'] ? 'success' : 'secondary' ?>"><?= $item['is_active'] ? 'Actif' : 'Inactif' ?></span>
                </div>
                <div class="mt-2 d-flex gap-1">
                    <a href="/admin/landing/before-after/<?= $item['id'] ?>/edit" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="/admin/landing/before-after/<?= $item['id'] ?>/toggle" style="display:inline;">
                        <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                        <button class="btn btn-sm btn-outline-<?= $item['is_active'] ? 'warning' : 'success' ?>" data-confirm="<?= __('ui.confirm_toggle') ?>"><i class="fas fa-<?= $item['is_active'] ? 'eye-slash' : 'eye' ?>"></i></button>
                    </form>
                    <form method="POST" action="/admin/landing/before-after/<?= $item['id'] ?>/delete" style="display:inline;">
                        <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                        <button class="btn btn-sm btn-outline-danger" data-confirm="<?= __('ui.confirm_delete') ?>"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
