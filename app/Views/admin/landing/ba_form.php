<?php $pageTitle = isset($item) ? 'Éditer Avant/Après' : 'Ajouter Avant/Après'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-arrows-left-right me-2 text-warning"></i> <?= $pageTitle ?></h4>
    <a href="/admin/landing/before-after" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:800px;">
    <div class="card-body p-4">
        <form method="POST" action="<?= isset($item) ? '/admin/landing/before-after/' . $item['id'] : '/admin/landing/before-after/store' ?>" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= $csrfToken ?>">
            <h6 class="mb-3">Image AVANT</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Upload fichier</label>
                    <input type="file" name="before_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>
                <div class="col-md-6">
                    <label class="form-label">— ou URL —</label>
                    <input type="url" name="before_image_url" class="form-control" value="<?= htmlspecialchars($item['before_image'] ?? '') ?>" placeholder="https://...">
                </div>
            </div>
            <?php if (!empty($item['before_image'])): ?>
            <div class="mb-4"><img src="<?= htmlspecialchars($item['before_image']) ?>" alt="Avant" style="max-height:120px;border-radius:8px;border:2px solid #ddd;"></div>
            <?php endif; ?>

            <h6 class="mb-3">Image APRÈS</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Upload fichier</label>
                    <input type="file" name="after_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>
                <div class="col-md-6">
                    <label class="form-label">— ou URL —</label>
                    <input type="url" name="after_image_url" class="form-control" value="<?= htmlspecialchars($item['after_image'] ?? '') ?>" placeholder="https://...">
                </div>
            </div>
            <?php if (!empty($item['after_image'])): ?>
            <div class="mb-4"><img src="<?= htmlspecialchars($item['after_image']) ?>" alt="Après" style="max-height:120px;border-radius:8px;border:2px solid #28a745;"></div>
            <?php endif; ?>

            <hr>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Titre FR</label><input type="text" name="title_fr" class="form-control" value="<?= htmlspecialchars($item['title_fr'] ?? '') ?>" required></div>
                <div class="col-md-6"><label class="form-label">Titre AR</label><input type="text" name="title_ar" class="form-control" dir="rtl" value="<?= htmlspecialchars($item['title_ar'] ?? '') ?>"></div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6"><label class="form-label">Description FR</label><textarea name="desc_fr" class="form-control" rows="2" required><?= htmlspecialchars($item['desc_fr'] ?? '') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Description AR</label><textarea name="desc_ar" class="form-control" rows="2" dir="rtl"><?= htmlspecialchars($item['desc_ar'] ?? '') ?></textarea></div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-4"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="<?= $item['sort_order'] ?? 0 ?>" min="0"></div>
                <div class="col-md-4"><label class="form-label">&nbsp;</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>><label class="form-check-label">Actif</label></div></div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer</button></div>
        </form>
    </div>
</div>
