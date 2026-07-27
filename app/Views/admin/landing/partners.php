<?php $pageTitle = 'Partenaires — Landing Page'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-handshake me-2 text-primary"></i> Partenaires</h4>
    <div class="d-flex gap-2">
        <a href="/admin/landing" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-1"></i> Ajouter</button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:50px;">#</th>
                    <th style="width:60px;">Icône</th>
                    <th>Nom</th>
                    <th style="width:80px;">Couleur</th>
                    <th style="width:80px;">Statut</th>
                    <th style="width:180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($partners as $p): ?>
                <tr>
                    <td class="text-muted"><?= $p['sort_order'] ?></td>
                    <td>
                        <div style="width:36px;height:36px;border-radius:8px;background:<?= htmlspecialchars($p['color']) ?>20;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="<?= htmlspecialchars($p['icon']) ?>" style="color:<?= htmlspecialchars($p['color']) ?>;font-size:16px;"></i>
                        </div>
                    </td>
                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                    <td><code class="small"><?= htmlspecialchars($p['color']) ?></code></td>
                    <td>
                        <?php if ($p['is_active']): ?>
                            <span class="badge bg-success">Actif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/admin/landing/partners/<?= $p['id'] ?>/edit" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="/admin/landing/partners/<?= $p['id'] ?>/toggle" style="display:inline;">
                            <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                            <button class="btn btn-sm btn-outline-<?= $p['is_active'] ? 'warning' : 'success' ?> me-1" title="<?= $p['is_active'] ? 'Désactiver' : 'Activer' ?>" data-confirm="<?= __('ui.confirm_toggle') ?>"><i class="fas fa-<?= $p['is_active'] ? 'eye-slash' : 'eye' ?>"></i></button>
                        </form>
                        <form method="POST" action="/admin/landing/partners/<?= $p['id'] ?>/delete" style="display:inline;">
                            <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                            <button class="btn btn-sm btn-outline-danger" data-confirm="<?= __('ui.confirm_delete') ?>"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="/admin/landing/partners/store">
        <input type="hidden" name="_token" value="<?= $csrfToken ?>">
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-plus me-2"></i>Ajouter un partenaire</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Nom *</label><input type="text" name="name" class="form-control" required></div>
            <div class="row g-3">
                <div class="col-6"><label class="form-label">Icône FA</label><input type="text" name="icon" class="form-control" value="fas fa-building" placeholder="fas fa-building"></div>
                <div class="col-6"><label class="form-label">Couleur</label><input type="text" name="color" class="form-control" value="var(--primary-light)" placeholder="var(--primary-light)"></div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-6"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="0" min="0"></div>
                <div class="col-6"><label class="form-label">&nbsp;</label><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked><label class="form-check-label">Actif</label></div></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Ajouter</button></div>
    </form>
</div></div></div>
