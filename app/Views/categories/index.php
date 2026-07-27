<?php $pageTitle = __('categories.title'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-tags me-2 text-primary"></i> <?= __('categories.title') ?></h4>
    <?php if ($canManage): ?>
    <a href="/categories/create" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> <?= __('categories.add') ?></a>
    <?php endif; ?>
</div>

<?php if (empty($categories)): ?>
<div class="alert alert-info"><i class="fas fa-info-circle me-1"></i> <?= __('categories.no_categories') ?></div>
<?php else: ?>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:50px;"><?= __('categories.icon') ?></th>
                    <th><?= __('categories.name') ?></th>
                    <th>Slug</th>
                    <th style="width:80px;"><?= __('categories.deadline') ?></th>
                    <th style="width:100px;"><?= __('categories.reports') ?></th>
                    <th style="width:90px;"><?= __('categories.status') ?></th>
                    <th style="width:120px;"><?= __('categories.actions') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td class="text-muted"><?= $c['sort_order'] ?></td>
                    <td>
                        <div style="width:36px;height:36px;border-radius:8px;background:<?= htmlspecialchars($c['color'] ?? '#ccc') ?>20;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="<?= htmlspecialchars($c['icon'] ?? 'fas fa-tag') ?>" style="color:<?= htmlspecialchars($c['color'] ?? '#3a7bd5') ?>;font-size:16px;"></i>
                        </div>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($c['name']) ?></strong>
                        <?php if (!empty($c['description'])): ?>
                            <br><small class="text-muted"><?= htmlspecialchars(mb_strimwidth($c['description'], 0, 60, '...')) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><code><?= htmlspecialchars($c['slug']) ?></code></td>
                    <td><span class="badge bg-info text-dark"><i class="fas fa-clock me-1"></i><?= (int)($c['deadline_days'] ?? 7) ?> <?= __('categories.days') ?></span></td>
                    <td class="text-center"><span class="badge bg-secondary"><?= (int)($c['report_count'] ?? 0) ?></span></td>
                    <td>
                        <?php if (!empty($c['is_active'])): ?>
                            <span class="badge bg-success"><?= __('categories.active') ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><?= __('categories.inactive') ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <?php if ($canManage): ?>
                            <a href="/categories/<?= $c['id'] ?>/edit" class="btn btn-sm btn-outline-warning" title="<?= __('categories.edit') ?>"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="/categories/<?= $c['id'] ?>/toggle" class="d-inline">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken ?? \App\Helpers\Csrf::generate()) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-<?= !empty($c['is_active']) ? 'danger' : 'success' ?>" title="<?= !empty($c['is_active']) ? __('categories.deactivate') : __('categories.activate') ?>" data-confirm="<?= __('ui.confirm_toggle') ?>">
                                    <i class="fas fa-<?= !empty($c['is_active']) ? 'ban' : 'check' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" action="/categories/<?= $c['id'] ?>/delete" class="d-inline">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken ?? \App\Helpers\Csrf::generate()) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="<?= __('categories.delete') ?>" data-confirm="<?= __('categories.confirm_delete') ?>"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>
