<?php $pageTitle = isset($item) ? 'Éditer Témoignage' : 'Ajouter un Témoignage'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-comment-dots me-2 text-success"></i> <?= $pageTitle ?></h4>
    <a href="/admin/landing/testimonials" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:700px;">
    <div class="card-body p-4">
        <form method="POST" action="<?= isset($item) ? '/admin/landing/testimonials/' . $item['id'] : '/admin/landing/testimonials/store' ?>">
            <input type="hidden" name="_token" value="<?= $csrfToken ?>">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Texte FR *</label><textarea name="text_fr" class="form-control" rows="3" required><?= htmlspecialchars($item['text_fr'] ?? '') ?></textarea></div>
                <div class="col-md-6"><label class="form-label">Texte AR *</label><textarea name="text_ar" class="form-control" rows="3" dir="rtl" required><?= htmlspecialchars($item['text_ar'] ?? '') ?></textarea></div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-4"><label class="form-label">Nom auteur</label><input type="text" name="author_name" class="form-control" value="<?= htmlspecialchars($item['author_name'] ?? '') ?>" required></div>
                <div class="col-md-4"><label class="form-label">Ville/Rôle</label><input type="text" name="author_role" class="form-control" value="<?= htmlspecialchars($item['author_role'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Note (1-5)</label><input type="number" name="rating" class="form-control" value="<?= $item['rating'] ?? 5 ?>" min="1" max="5"></div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-4"><label class="form-label">Couleur avatar</label>
                    <select name="avatar_gradient" class="form-select">
                        <option value="var(--gradient-accent)" <?= ($item['avatar_gradient'] ?? '') === 'var(--gradient-accent)' ? 'selected' : '' ?>>Bleu→Cyan</option>
                        <option value="var(--gradient-cool)" <?= ($item['avatar_gradient'] ?? '') === 'var(--gradient-cool)' ? 'selected' : '' ?>>Vert→Cyan</option>
                        <option value="var(--gradient-purple)" <?= ($item['avatar_gradient'] ?? '') === 'var(--gradient-purple)' ? 'selected' : '' ?>>Violet→Rose</option>
                        <option value="var(--gradient-warm)" <?= ($item['avatar_gradient'] ?? '') === 'var(--gradient-warm)' ? 'selected' : '' ?>>Orange→Rouge</option>
                    </select>
                </div>
                <div class="col-md-4"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="<?= $item['sort_order'] ?? 0 ?>" min="0"></div>
                <div class="col-md-4"><label class="form-label">&nbsp;</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>><label class="form-check-label">Actif</label></div></div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer</button></div>
        </form>
    </div>
</div>
