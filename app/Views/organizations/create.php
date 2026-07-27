<?php $pageTitle = __('organizations.new_org'); ?>
<h4 class="mb-4"><i class="fas fa-building me-2 text-primary"></i> <?= __('organizations.new_org') ?></h4>
<div class="row"><div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="/organizations/store">
    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
    <div class="row mb-3"><div class="col-md-6"><label class="form-label fw-bold"><?= __('organizations.name') ?> *</label><input type="text" class="form-control" name="name" required></div><div class="col-md-6"><label class="form-label fw-bold"><?= __('organizations.name_ar') ?></label><input type="text" class="form-control" name="name_ar" dir="rtl"></div></div>
    <div class="row mb-3"><div class="col-md-4"><label class="form-label fw-bold"><?= __('organizations.code') ?> *</label><input type="text" class="form-control" name="code" required></div><div class="col-md-8"><label class="form-label fw-bold"><?= __('organizations.website') ?></label><input type="url" class="form-control" name="website"></div></div>
    <div class="mb-3"><label class="form-label fw-bold"><?= __('organizations.address') ?></label><textarea class="form-control" name="address" rows="2"></textarea></div>
    <div class="row mb-3"><div class="col-md-6"><label class="form-label fw-bold"><?= __('organizations.phone') ?></label><input type="tel" class="form-control" name="phone"></div><div class="col-md-6"><label class="form-label fw-bold"><?= __('organizations.email') ?></label><input type="email" class="form-control" name="email"></div></div>
    <div class="mb-3"><label class="form-label fw-bold"><?= __('organizations.description') ?></label><textarea class="form-control" name="description" rows="3"></textarea></div>
    <div class="d-flex gap-2"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> <?= __('organizations.create') ?></button><a href="/organizations" class="btn btn-outline-secondary"><?= __('organizations.cancel') ?></a></div>
</form>
</div></div></div></div>
