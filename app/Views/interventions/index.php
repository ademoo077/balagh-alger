<?php $pageTitle = __('interventions.title'); ?>
<div class="page-header animate-fade-in-up">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4><i class="fas fa-hard-hat me-2 text-amber"></i> <?= __('interventions.title') ?></h4>
            <small class="text-secondary"><?= number_format($total) ?> <?= __('interventions.in_treatment') ?></small>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4 animate-fade-in-up">
    <div class="card-body">
        <form method="GET" action="/interventions" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label"><?= __('common.status') ?></label>
                <select class="form-select form-select-sm" name="status">
                    <option value=""><?= __('interventions.all') ?></option>
                    <option value="acknowledged" <?= ($_GET['status'] ?? '') === 'acknowledged' ? 'selected' : '' ?>><?= __('interventions.pending') ?></option>
                    <option value="assigned" <?= ($_GET['status'] ?? '') === 'assigned' ? 'selected' : '' ?>><?= __('interventions.assigned') ?></option>
                    <option value="in_progress" <?= ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>><?= __('statuses.in_progress') ?></option>
                    <option value="resolved" <?= ($_GET['status'] ?? '') === 'resolved' ? 'selected' : '' ?>><?= __('interventions.completed_label') ?></option>
                </select>
            </div>
            <div class="col-md-1"><button class="btn btn-sm btn-primary w-100"><i class="fas fa-filter"></i></button></div>
            <div class="col-md-1"><a href="/interventions" class="btn btn-sm btn-outline-secondary w-100"><?= __('common.reset') ?></a></div>
        </form>
    </div>
</div>

<!-- Cards -->
<div class="row g-3">
    <?php if (empty($reports)): ?>
    <div class="col-12">
        <div class="card animate-fade-in-up">
            <div class="card-body">
                <div class="empty-state py-4">
                    <i class="fas fa-clipboard-check d-block mb-2"></i>
                    <h6><?= __('interventions.no_interventions') ?></h6>
                </div>
            </div>
        </div>
    </div>
    <?php else: foreach ($reports as $r): ?>
    <div class="col-md-6 col-xl-4">
        <a href="/interventions/<?= $r['id'] ?>" class="citizen-report-card animate-fade-in-up" style="border-inline-start:4px solid <?= match($r['status']) { 'in_progress' => 'var(--amber)', 'resolved' => 'var(--green)', 'acknowledged' => 'var(--accent-light)', default => 'var(--accent)' } ?>;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="tracking-code"><?= $r['tracking_code'] ?></div>
                    <div class="report-title"><?= \App\Helpers\Helper::sanitize(mb_strimwidth($r['title'], 0, 55, '...')) ?></div>
                </div>
                <?= \App\Helpers\Helper::getStatusBadge($r['status']) ?>
            </div>
            <div class="report-meta mt-2">
                <span><i class="fas fa-tag"></i><?= $r['category_name'] ?></span>
                <span><i class="fas fa-map-marker-alt"></i><?= $r['daira_name'] ?></span>
                <span><i class="fas fa-building"></i><?= $r['org_name'] ?? '-' ?></span>
            </div>
            <div class="mt-2" style="display:flex;justify-content:space-between;align-items:center;">
                <?= \App\Helpers\Helper::getPriorityBadge($r['priority']) ?>
                <small class="text-muted"><i class="fas fa-clock me-1"></i><?= \App\Helpers\Helper::timeAgo($r['created_at']) ?></small>
            </div>
            <?php if ($r['assigned_first_name']): ?>
            <div class="mt-2" style="font-size:0.72rem;color:var(--text-secondary);">
                <i class="fas fa-user-check me-1"></i><?= $r['assigned_first_name'] ?> <?= $r['assigned_last_name'] ?>
            </div>
            <?php endif; ?>
        </a>
    </div>
    <?php endforeach; endif; ?>
</div>

<?php if ($totalPages > 1): ?>
<nav class="mt-3">
    <ul class="pagination pagination-sm justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_filter($_GET, fn($k) => $k !== 'page', ARRAY_FILTER_USE_KEY)) ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
