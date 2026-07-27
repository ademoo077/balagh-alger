<?php $pageTitle = 'FAQ — Landing Page'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-circle-question me-2" style="color:#8b5cf6;"></i> FAQ</h4>
    <div class="d-flex gap-2">
        <a href="/admin/landing" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
        <a href="/admin/landing/faq/create" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Ajouter</a>
    </div>
</div>

<?php if (empty($items)): ?>
<div class="alert alert-info"><i class="fas fa-info-circle me-1"></i> Aucune question FAQ.</div>
<?php else: ?>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Question (FR)</th>
                    <th>Réponse (FR, aperçu)</th>
                    <th style="width:80px;">Statut</th>
                    <th style="width:180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td class="text-muted"><?= $item['sort_order'] ?></td>
                    <td><strong class="small"><?= htmlspecialchars(mb_strimwidth($item['question_fr'], 0, 50, '...')) ?></strong></td>
                    <td><small class="text-muted"><?= htmlspecialchars(mb_strimwidth($item['answer_fr'], 0, 60, '...')) ?></small></td>
                    <td><span class="badge bg-<?= $item['is_active'] ? 'success' : 'secondary' ?>"><?= $item['is_active'] ? 'Actif' : 'Inactif' ?></span></td>
                    <td>
                        <a href="/admin/landing/faq/<?= $item['id'] ?>/edit" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="/admin/landing/faq/<?= $item['id'] ?>/toggle" style="display:inline;">
                            <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                            <button class="btn btn-sm btn-outline-<?= $item['is_active'] ? 'warning' : 'success' ?> me-1" data-confirm="<?= __('ui.confirm_toggle') ?>"><i class="fas fa-<?= $item['is_active'] ? 'eye-slash' : 'eye' ?>"></i></button>
                        </form>
                        <form method="POST" action="/admin/landing/faq/<?= $item['id'] ?>/delete" style="display:inline;">
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
<?php endif; ?>
