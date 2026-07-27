<?php $pageTitle = __('categories.edit_category') . ' — ' . htmlspecialchars($category['name']); ?>
<h4 class="mb-4"><i class="fas fa-tag me-2 text-primary"></i> <?= __('categories.edit_category') ?></h4>
<div class="row"><div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="/categories/<?= $category['id'] ?>">
    <input type="hidden" name="_token" value="<?= $csrfToken ?>">

    <div class="row mb-3">
        <div class="col-md-8">
            <label class="form-label fw-bold"><?= __('categories.name') ?> *</label>
            <input type="text" class="form-control" name="name" id="catName" required value="<?= htmlspecialchars($category['name']) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">Slug</label>
            <input type="text" class="form-control" name="slug" id="catSlug" value="<?= htmlspecialchars($category['slug'] ?? '') ?>" placeholder="Auto-généré si vide">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold"><?= __('categories.description') ?></label>
        <textarea class="form-control" name="description" rows="2"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold"><?= __('categories.icon') ?></label>
            <div class="input-group">
                <span class="input-group-text" id="iconPreview"><i class="<?= htmlspecialchars($category['icon'] ?? 'fas fa-exclamation-triangle') ?>"></i></span>
                <input type="text" class="form-control" name="icon" id="catIcon" value="<?= htmlspecialchars($category['icon'] ?? 'fas fa-exclamation-triangle') ?>">
            </div>
            <small class="text-muted">Classe Font Awesome, ex: <code>fas fa-water</code>, <code>fas fa-bolt</code></small>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold"><?= __('categories.color') ?></label>
            <div class="input-group">
                <input type="color" class="form-control form-control-color" name="color" id="catColor" value="<?= htmlspecialchars($category['color'] ?? '#3a7bd5') ?>">
                <input type="text" class="form-control" id="catColorText" value="<?= htmlspecialchars($category['color'] ?? '#3a7bd5') ?>" style="max-width:120px;">
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label fw-bold"><?= __('categories.deadline') ?> *</label>
            <div class="input-group">
                <input type="number" class="form-control" name="deadline_days" id="catDeadline" value="<?= (int)($category['deadline_days'] ?? 7) ?>" min="1" max="365">
                <span class="input-group-text"><?= __('categories.days') ?></span>
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold"><?= __('categories.sort_order') ?></label>
            <input type="number" class="form-control" name="sort_order" value="<?= (int)($category['sort_order'] ?? 0) ?>" min="0">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="catActive" <?= !empty($category['is_active']) ? 'checked' : '' ?>>
                <label class="form-check-label fw-bold" for="catActive"><?= __('categories.active') ?></label>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> <?= __('categories.save') ?></button>
        <a href="/categories" class="btn btn-outline-secondary"><?= __('categories.cancel') ?></a>
    </div>
</form>
</div></div></div></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var nameInput = document.getElementById('catName');
    var slugInput = document.getElementById('catSlug');
    var iconInput = document.getElementById('catIcon');
    var iconPreview = document.getElementById('iconPreview');
    var colorInput = document.getElementById('catColor');
    var colorText = document.getElementById('catColorText');

    if (!slugInput.value) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.dataset.manual) {
                slugInput.value = this.value
                    .toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
            }
        });
    }

    slugInput.addEventListener('input', function() {
        this.dataset.manual = this.value.length > 0 ? '1' : '';
    });

    iconInput.addEventListener('input', function() {
        iconPreview.innerHTML = '<i class="' + this.value + '"></i>';
    });

    colorInput.addEventListener('input', function() {
        colorText.value = this.value;
    });
    colorText.addEventListener('input', function() {
        if (/^#[0-9a-fA-F]{6}$/.test(this.value)) {
            colorInput.value = this.value;
        }
    });
});
</script>
