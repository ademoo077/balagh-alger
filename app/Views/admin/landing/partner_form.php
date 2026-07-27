<?php $pageTitle = 'Éditer Partenaire'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i> Éditer Partenaire</h4>
    <a href="/admin/landing/partners" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px;">
    <div class="card-body p-4">
        <form method="POST" action="/admin/landing/partners/<?= $partner['id'] ?>">
            <input type="hidden" name="_token" value="<?= $csrfToken ?>">
            <div class="mb-3"><label class="form-label">Nom *</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($partner['name']) ?>" required></div>
            <div class="row g-3">
                <div class="col-6"><label class="form-label">Icône FA</label><input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($partner['icon']) ?>"></div>
                <div class="col-6"><label class="form-label">Couleur</label><input type="text" name="color" class="form-control" value="<?= htmlspecialchars($partner['color']) ?>"></div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-6"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="<?= $partner['sort_order'] ?>" min="0"></div>
                <div class="col-6"><label class="form-label">&nbsp;</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" <?= $partner['is_active'] ? 'checked' : '' ?>><label class="form-check-label">Actif</label></div></div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer</button></div>
        </form>
    </div>
</div>
