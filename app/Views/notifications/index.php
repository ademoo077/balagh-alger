<?php $pageTitle = __('notifications.title'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-bell me-2 text-primary"></i> <?= __('notifications.title') ?></h4>
    <form method="POST" action="/notifications/read-all"><input type="hidden" name="_token" value="<?= $csrfToken ?>"><button class="btn btn-sm btn-outline-secondary" data-confirm="<?= __('ui.mark_all_read') ?>"><i class="fas fa-check-double me-1"></i> <?= __('notifications.mark_all_read') ?></button></form>
</div>
<div class="card border-0 shadow-sm">
    <div class="list-group list-group-flush">
    <?php if (empty($notifications)): ?>
        <div class="list-group-item bg-transparent text-center text-muted py-5"><?= __('notifications.none') ?></div>
    <?php else: foreach ($notifications as $n): ?>
        <form method="POST" action="/notifications/<?= $n['id'] ?>/read" class="list-group-item list-group-item-action <?= $n['is_read'] ? 'bg-transparent' : 'bg-primary bg-opacity-10' ?> border-0" style="cursor:pointer;">
            <input type="hidden" name="_token" value="<?= $csrfToken ?>">
            <button type="submit" class="btn btn-link text-decoration-none text-start w-100 p-0" style="color:inherit;">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-1"><?= htmlspecialchars($n['title']) ?></h6>
                        <p class="mb-1 text-muted"><?= htmlspecialchars($n['message']) ?></p>
                        <?php if (!empty($n['data']) && isset(json_decode($n['data'], true)['report_id'])): ?>
                        <a href="/reports/<?= json_decode($n['data'], true)['report_id'] ?>" style="font-size:0.78rem;color:var(--primary);text-decoration:none;font-weight:600;" onclick="event.stopPropagation();">
                            <i class="fas fa-external-link-alt me-1"></i><?= __('notifications.view_report') ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted"><?= \App\Helpers\Helper::timeAgo($n['created_at']) ?></small>
                </div>
            </button>
        </form>
    <?php endforeach; endif; ?>
    </div>
</div>
