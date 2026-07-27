<?php $pageTitle = 'Galerie Photos — Landing Page'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-images me-2 text-info"></i> Galerie Photos</h4>
    <div class="d-flex gap-2">
        <a href="/admin/landing" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Ajouter</button>
    </div>
</div>

<?php if (empty($images)): ?>
<div class="alert alert-info"><i class="fas fa-info-circle me-1"></i> Aucune image dans la galerie.</div>
<?php else: ?>
<div class="row g-3">
    <?php foreach ($images as $img): ?>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div style="height:180px;overflow:hidden;background:#f0f0f0;border-radius:8px 8px 0 0;">
                <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="<?= htmlspecialchars($img['alt_text']) ?>" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
            </div>
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted">#<?= $img['sort_order'] ?></small>
                        <?php if ($img['alt_text']): ?><br><small><?= htmlspecialchars($img['alt_text']) ?></small><?php endif; ?>
                    </div>
                    <span class="badge bg-<?= $img['is_active'] ? 'success' : 'secondary' ?>"><?= $img['is_active'] ? 'Actif' : 'Inactif' ?></span>
                </div>
                <div class="mt-2 d-flex gap-1">
                    <form method="POST" action="/admin/landing/gallery/<?= $img['id'] ?>/toggle" style="display:inline;">
                        <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                        <button class="btn btn-sm btn-outline-<?= $img['is_active'] ? 'warning' : 'success' ?>" data-confirm="<?= __('ui.confirm_toggle') ?>"><i class="fas fa-<?= $img['is_active'] ? 'eye-slash' : 'eye' ?>"></i></button>
                    </form>
                    <form method="POST" action="/admin/landing/gallery/<?= $img['id'] ?>/delete" style="display:inline;">
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="/admin/landing/gallery/store" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="<?= $csrfToken ?>">
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-upload me-2"></i>Ajouter une image</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Image (upload local, max 5MB)</label><input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp"></div>
            <div class="mb-3"><label class="form-label">— ou URL externe —</label><input type="url" name="image_url" class="form-control" placeholder="https://..."></div>
            <div class="row g-3">
                <div class="col-8"><label class="form-label">Texte alternatif</label><input type="text" name="alt_text" class="form-control" placeholder="Description de l'image"></div>
                <div class="col-4"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="0" min="0"></div>
            </div>
            <div class="form-check mt-3"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked><label class="form-check-label">Actif</label></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Ajouter</button></div>
    </form>
</div></div></div>
