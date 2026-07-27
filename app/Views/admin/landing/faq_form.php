<?php $pageTitle = isset($item) ? 'Éditer Question FAQ' : 'Ajouter une Question FAQ'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-circle-question me-2" style="color:#8b5cf6;"></i> <?= $pageTitle ?></h4>
    <a href="/admin/landing/faq" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:700px;">
    <div class="card-body p-4">
        <form method="POST" action="<?= isset($item) ? '/admin/landing/faq/' . $item['id'] : '/admin/landing/faq/store' ?>">
            <input type="hidden" name="_token" value="<?= $csrfToken ?>">
            <div class="mb-3"><label class="form-label">Question FR *</label><input type="text" name="question_fr" class="form-control" value="<?= htmlspecialchars($item['question_fr'] ?? '') ?>" required></div>
            <div class="mb-3"><label class="form-label">Question AR</label><input type="text" name="question_ar" class="form-control" dir="rtl" value="<?= htmlspecialchars($item['question_ar'] ?? '') ?>"></div>
            <div class="mb-3"><label class="form-label">Réponse FR *</label><textarea name="answer_fr" class="form-control" rows="4" required><?= htmlspecialchars($item['answer_fr'] ?? '') ?></textarea></div>
            <div class="mb-3"><label class="form-label">Réponse AR</label><textarea name="answer_ar" class="form-control" rows="4" dir="rtl"><?= htmlspecialchars($item['answer_ar'] ?? '') ?></textarea></div>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="<?= $item['sort_order'] ?? 0 ?>" min="0"></div>
                <div class="col-md-4"><label class="form-label">&nbsp;</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>><label class="form-check-label">Actif</label></div></div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer</button></div>
        </form>
    </div>
</div>
