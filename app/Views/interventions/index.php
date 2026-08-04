<?php $pageTitle = __('interventions.title'); ?>
<div class="page-header animate-fade-in-up">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4><i class="fas fa-hard-hat me-2 text-amber"></i> <?= __('interventions.title') ?></h4>
            <small class="text-secondary"><?= number_format($total) ?> <?= __('interventions.in_treatment') ?></small>
        </div>
        <div class="d-flex gap-2">
            <span class="badge" style="background:var(--accent-surface);color:var(--accent);">
                <?= count($activeFilters) ?> <?= __('interventions.active_filters') ?>
            </span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4 animate-fade-in-up">
    <div class="card-header d-flex justify-content-between align-items-center" style="cursor:pointer;" onclick="document.getElementById('filterBody').classList.toggle('d-none');this.querySelector('.fa-chevron-down,.fa-chevron-up').classList.toggle('fa-chevron-down');this.querySelector('.fa-chevron-down,.fa-chevron-up').classList.toggle('fa-chevron-up');">
        <h6 class="mb-0"><i class="fas fa-filter me-2 text-accent"></i><?= __('interventions.search_filters') ?></h6>
        <i class="fas fa-chevron-up text-muted"></i>
    </div>
    <div class="card-body" id="filterBody">
        <form method="GET" action="/interventions" id="filterForm">
            <!-- Row 1: Search + Status + Priority -->
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <label class="form-label" style="font-size:0.78rem;"><?= __('interventions.search_placeholder') ?></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="q" placeholder="<?= __('interventions.search_placeholder') ?>" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:0.78rem;"><?= __('common.status') ?></label>
                    <select class="form-select form-select-sm" name="status">
                        <option value=""><?= __('interventions.all') ?></option>
                        <option value="acknowledged" <?= ($_GET['status'] ?? '') === 'acknowledged' ? 'selected' : '' ?>><?= __('interventions.pending') ?></option>
                        <option value="assigned" <?= ($_GET['status'] ?? '') === 'assigned' ? 'selected' : '' ?>><?= __('interventions.assigned') ?></option>
                        <option value="in_progress" <?= ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>><?= __('statuses.in_progress') ?></option>
                        <option value="resolved" <?= ($_GET['status'] ?? '') === 'resolved' ? 'selected' : '' ?>><?= __('interventions.completed_label') ?></option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:0.78rem;"><?= __('reports.priority') ?></label>
                    <select class="form-select form-select-sm" name="priority">
                        <option value=""><?= __('interventions.all') ?></option>
                        <option value="urgent" <?= ($_GET['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>><?= __('priorities.urgent') ?></option>
                        <option value="high" <?= ($_GET['priority'] ?? '') === 'high' ? 'selected' : '' ?>><?= __('priorities.high') ?></option>
                        <option value="medium" <?= ($_GET['priority'] ?? '') === 'medium' ? 'selected' : '' ?>><?= __('priorities.medium') ?></option>
                        <option value="low" <?= ($_GET['priority'] ?? '') === 'low' ? 'selected' : '' ?>><?= __('priorities.low') ?></option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size:0.78rem;"><?= __('interventions.assignment') ?></label>
                    <select class="form-select form-select-sm" name="assigned">
                        <option value=""><?= __('interventions.all') ?></option>
                        <option value="assigned" <?= ($_GET['assigned'] ?? '') === 'assigned' ? 'selected' : '' ?>><?= __('interventions.assigned_label') ?></option>
                        <option value="unassigned" <?= ($_GET['assigned'] ?? '') === 'unassigned' ? 'selected' : '' ?>><?= __('interventions.unassigned') ?></option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1 align-items-end">
                    <button class="btn btn-sm btn-primary flex-fill"><i class="fas fa-filter me-1"></i><?= __('interventions.filter') ?></button>
                    <a href="/interventions" class="btn btn-sm btn-outline-secondary flex-fill"><i class="fas fa-times me-1"></i><?= __('common.reset') ?></a>
                </div>
            </div>

            <!-- Row 2: Category + Daira + Commune + Organization -->
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label" style="font-size:0.78rem;"><?= __('interventions.category') ?></label>
                    <select class="form-select form-select-sm" name="category_id" onchange="this.form.submit()">
                        <option value=""><?= __('interventions.all_categories') ?></option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($_GET['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:0.78rem;"><?= __('interventions.daira') ?></label>
                    <select class="form-select form-select-sm" name="daira_id" onchange="this.form.submit()">
                        <option value=""><?= __('interventions.all_dairas') ?></option>
                        <?php foreach ($dairas as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($_GET['daira_id'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:0.78rem;"><?= __('interventions.commune') ?></label>
                    <select class="form-select form-select-sm" name="commune_id">
                        <option value=""><?= __('interventions.all_communes') ?></option>
                        <?php foreach ($communes as $co): ?>
                        <option value="<?= $co['id'] ?>" <?= ($_GET['commune_id'] ?? '') == $co['id'] ? 'selected' : '' ?>><?= htmlspecialchars($co['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:0.78rem;"><?= __('interventions.organization') ?></label>
                    <select class="form-select form-select-sm" name="organization_id">
                        <option value=""><?= __('interventions.all_orgs') ?></option>
                        <?php foreach ($organizations as $o): ?>
                        <option value="<?= $o['id'] ?>" <?= ($_GET['organization_id'] ?? '') == $o['id'] ? 'selected' : '' ?>><?= htmlspecialchars($o['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Row 3: Date range -->
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label" style="font-size:0.78rem;"><?= __('interventions.date_from') ?></label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:0.78rem;"><?= __('interventions.date_to') ?></label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
                </div>
                <?php if (!empty($activeFilters)): ?>
                <div class="col-md-6 d-flex align-items-end gap-1 flex-wrap">
                    <small class="text-muted me-1" style="font-size:0.72rem;"><?= __('interventions.active_filters') ?>:</small>
                    <?php
                    $filterLabels = [
                        'status' => __('common.status'),
                        'priority' => __('reports.priority'),
                        'category_id' => __('interventions.category'),
                        'daira_id' => __('interventions.daira'),
                        'commune_id' => __('interventions.commune'),
                        'organization_id' => __('interventions.organization'),
                        'assigned' => __('interventions.assignment'),
                        'date_from' => __('interventions.date_from'),
                        'date_to' => __('interventions.date_to'),
                        'q' => __('interventions.search_placeholder'),
                    ];
                    foreach ($activeFilters as $fk => $fv):
                        if ($fk === 'q'): ?>
                            <span class="badge" style="background:var(--accent-surface);color:var(--accent);font-size:0.68rem;"><?= htmlspecialchars($filterLabels[$fk] ?? $fk) ?>: "<?= htmlspecialchars($fv) ?>" <a href="/interventions?<?= http_build_query(array_filter($_GET, fn($k) => $k !== $fk, ARRAY_FILTER_USE_KEY)) ?>" class="ms-1" style="color:inherit;text-decoration:none;">&times;</a></span>
                        <?php elseif (in_array($fk, ['date_from','date_to'])): ?>
                            <span class="badge" style="background:var(--cyan-surface);color:var(--cyan);font-size:0.68rem;"><?= htmlspecialchars($filterLabels[$fk] ?? $fk) ?>: <?= htmlspecialchars($fv) ?> <a href="/interventions?<?= http_build_query(array_filter($_GET, fn($k) => $k !== $fk, ARRAY_FILTER_USE_KEY)) ?>" class="ms-1" style="color:inherit;text-decoration:none;">&times;</a></span>
                        <?php else: ?>
                            <span class="badge" style="background:var(--green-surface);color:var(--green);font-size:0.68rem;"><?= htmlspecialchars($filterLabels[$fk] ?? $fk) ?> <a href="/interventions?<?= http_build_query(array_filter($_GET, fn($k) => $k !== $fk, ARRAY_FILTER_USE_KEY)) ?>" class="ms-1" style="color:inherit;text-decoration:none;">&times;</a></span>
                        <?php endif;
                    endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
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
