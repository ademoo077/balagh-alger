<?php $pageTitle = 'Gestion des rôles'; ?>
<div class="d-flex align-items-center gap-3 mb-4">
    <h4 class="mb-0"><i class="fas fa-shield-alt me-2 text-primary"></i> Gestion des rôles &amp; permissions</h4>
    <a href="/settings" class="btn btn-sm btn-outline-secondary ms-auto"><i class="fas fa-arrow-left me-1"></i> Paramètres généraux</a>
</div>

<div class="d-flex gap-2 flex-wrap mb-3" id="rolePills" role="tablist">
    <?php foreach ($roles as $i => $role):
        $icons = [
            'admin_central' => 'fa-crown', 'admin_local' => 'fa-user-shield', 'resp_central' => 'fa-user-tie',
            'chef_unite' => 'fa-briefcase', 'chef_section' => 'fa-users', 'intervenant' => 'fa-hard-hat', 'citizen' => 'fa-user',
        ];
        $assigned = isset($rolePerms[$role['id']]) ? count($rolePerms[$role['id']]) : 0;
    ?>
    <button class="btn btn-sm <?= $i === 0 ? 'btn-primary' : 'btn-outline-secondary' ?> d-inline-flex align-items-center gap-1 px-3" id="pill-<?= $role['id'] ?>" data-bs-toggle="tab" data-bs-target="#role-<?= $role['id'] ?>" type="button" role="tab">
        <i class="fas <?= $icons[$role['name']] ?? 'fa-user' ?>"></i>
        <span><?= $role['label'] ?></span>
        <span class="badge <?= $i === 0 ? 'bg-white text-primary' : 'bg-secondary' ?> rounded-pill" style="font-size:0.6rem;"><?= $assigned ?>/<?= count($permissions) ?></span>
    </button>
    <?php endforeach; ?>
</div>

<div class="tab-content" id="roleTabsContent">
    <?php foreach ($roles as $i => $role):
        $assigned = isset($rolePerms[$role['id']]) ? array_map('intval', $rolePerms[$role['id']]) : [];
        $isAdminCentral = $role['name'] === 'admin_central';
    ?>
    <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="role-<?= $role['id'] ?>" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex flex-wrap align-items-center gap-2 py-3">
                <div class="d-flex align-items-center gap-2">
                    <strong><?= $role['label'] ?></strong>
                    <span class="badge bg-secondary" style="font-size:0.65rem;">Niv. <?= $role['level'] ?></span>
                    <?php if ($role['description']): ?>
                    <small class="text-muted d-none d-md-inline">— <?= $role['description'] ?></small>
                    <?php endif; ?>
                </div>
                <?php if ($isAdminCentral): ?>
                <span class="badge bg-warning text-dark px-3 py-2 ms-auto">
                    <i class="fas fa-key me-1"></i> Accès total (bypass code)
                </span>
                <?php else: ?>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <input type="text" class="form-control form-control-sm perm-filter" data-role="<?= $role['id'] ?>" placeholder="Filtrer permissions..." style="width:200px;font-size:0.8rem;">
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($isAdminCentral): ?>
                <div class="alert alert-info d-flex align-items-center gap-2 py-2 mb-4" style="font-size:0.85rem;">
                    <i class="fas fa-info-circle"></i>
                    <span>Le rôle <strong>Administrateur Central</strong> a accès à toutes les permissions via le code (<code>Rbac::has()</code> retourne <code>true</code>). Les permissions ci-dessous sont affichées à titre indicatif uniquement.</span>
                </div>
                <?php endif; ?>

                <form method="POST" action="/settings/roles/update" class="role-form" data-role="<?= $role['id'] ?>">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="role_id" value="<?= $role['id'] ?>">

                    <div class="row g-3">
                        <?php foreach ($modules as $module => $perms):
                            $moduleAssigned = count(array_intersect(array_map('intval', array_column($perms, 'id')), $assigned));
                        ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="p-3 rounded-3 border perm-module" style="border-color:var(--border);height:100%;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="text-uppercase fw-bold mb-0" style="font-size:0.7rem;letter-spacing:0.5px;color:var(--text-muted);">
                                        <i class="fas fa-folder me-1"></i> <?= $module ?>
                                    </h6>
                                    <?php if (!$isAdminCentral): ?>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-secondary module-select-all px-1 py-0" title="Tout sélectionner" style="font-size:0.6rem;line-height:1;" data-role="<?= $role['id'] ?>" data-module="<?= $module ?>">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary module-deselect-all px-1 py-0" title="Tout désélectionner" style="font-size:0.6rem;line-height:1;" data-role="<?= $role['id'] ?>" data-module="<?= $module ?>">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php foreach ($perms as $p): ?>
                                <div class="form-check form-switch mb-1 perm-item" data-perm-name="<?= strtolower($p['label'] . ' ' . $p['name']) ?>">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="perm-<?= $role['id'] ?>-<?= $p['id'] ?>"
                                        name="permissions[]" value="<?= $p['id'] ?>"
                                        <?= in_array((int)$p['id'], $assigned) ? 'checked' : '' ?>
                                        <?= $isAdminCentral ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="perm-<?= $role['id'] ?>-<?= $p['id'] ?>" style="font-size:0.8rem;cursor:pointer;">
                                        <?= $p['label'] ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                                <?php if (!$isAdminCentral && count($perms) > 0): ?>
                                <div class="mt-2 text-end">
                                    <small class="text-muted module-count" style="font-size:0.6rem;">
                                        <span class="module-checked" data-role="<?= $role['id'] ?>" data-module="<?= $module ?>"><?= $moduleAssigned ?></span>/<?= count($perms) ?>
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!$isAdminCentral): ?>
                    <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center gap-2">
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary select-all" data-role="<?= $role['id'] ?>">
                            <i class="fas fa-check-double me-1"></i> Tout
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary deselect-all" data-role="<?= $role['id'] ?>">
                            <i class="fas fa-times me-1"></i> Aucun
                        </button>
                        <small class="text-muted ms-auto">
                            <span class="checked-count fw-bold" data-role="<?= $role['id'] ?>"><?= count($assigned) ?></span>
                            / <?= count($permissions) ?> permissions
                        </small>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<style>
.perm-item {
    transition: opacity .15s;
}
.perm-item.perm-hidden {
    opacity: .25;
    pointer-events: none;
}
.role-form .btn-sm {
    transition: all .15s;
}
.perm-module {
    transition: border-color .2s;
}
</style>

<script>
document.querySelectorAll('.perm-filter').forEach(function(inp) {
    inp.addEventListener('input', function() {
        var roleId = this.dataset.role;
        var q = this.value.toLowerCase().trim();
        document.querySelectorAll('#role-' + roleId + ' .perm-item').forEach(function(el) {
            el.classList.toggle('perm-hidden', q !== '' && !el.dataset.permName.includes(q));
        });
    });
});

document.querySelectorAll('.select-all').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var roleId = this.dataset.role;
        document.querySelectorAll('#role-' + roleId + ' .form-check-input:not(:disabled)').forEach(function(cb) {
            cb.checked = true;
        });
        refreshCounts(roleId);
    });
});
document.querySelectorAll('.deselect-all').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var roleId = this.dataset.role;
        document.querySelectorAll('#role-' + roleId + ' .form-check-input:not(:disabled)').forEach(function(cb) {
            cb.checked = false;
        });
        refreshCounts(roleId);
    });
});
document.querySelectorAll('.module-select-all').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var roleId = this.dataset.role;
        var module = this.dataset.module;
        var moduleDiv = this.closest('.perm-module');
        moduleDiv.querySelectorAll('.form-check-input:not(:disabled)').forEach(function(cb) { cb.checked = true; });
        refreshCounts(roleId);
    });
});
document.querySelectorAll('.module-deselect-all').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var roleId = this.dataset.role;
        var module = this.dataset.module;
        var moduleDiv = this.closest('.perm-module');
        moduleDiv.querySelectorAll('.form-check-input:not(:disabled)').forEach(function(cb) { cb.checked = false; });
        refreshCounts(roleId);
    });
});
document.querySelectorAll('.form-check-input').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var roleId = this.closest('.tab-pane').id.replace('role-', '');
        refreshCounts(roleId);
    });
});
function refreshCounts(roleId) {
    var pane = document.getElementById('role-' + roleId);
    if (!pane) return;
    var totalRole = pane.querySelectorAll('.form-check-input:not(:disabled)').length;
    var checkedRole = pane.querySelectorAll('.form-check-input:checked:not(:disabled)').length;
    var el = document.querySelector('.checked-count[data-role="' + roleId + '"]');
    if (el) el.textContent = checkedRole;

    pane.querySelectorAll('.perm-module').forEach(function(mod) {
        var checked = mod.querySelectorAll('.form-check-input:checked:not(:disabled)').length;
        var total = mod.querySelectorAll('.form-check-input:not(:disabled)').length;
        mod.querySelectorAll('.module-checked').forEach(function(e) {
            if (e.dataset.role === roleId) e.textContent = checked;
        });
        var badge = document.querySelector('#pill-' + roleId + ' .badge');
        if (badge) badge.textContent = checkedRole + '/' + totalRole;
    });
}

document.querySelectorAll('.role-form').forEach(function(form) {
    form.addEventListener('submit', function() {
        var btn = this.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Enregistrement...';
        }
    });
});
</script>
