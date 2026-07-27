<?php $pageTitle = $reports_title = $isCitizen ?? false ? __('reports.my_reports') : __('reports.title'); ?>
<?php if ($isCitizen ?? false): ?>
<?php
    $filterStatuses = ['submitted','acknowledged','assigned','in_progress','pending_review','pending_unite','validated','resolved','closed','rejected'];
    $activeStatus = $_GET['status'] ?? '';
    $activeSearch = htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8');
    $activeCatId = $_GET['category_id'] ?? '';
    $activeSubcatId = $_GET['subcategory_id'] ?? '';
    $hasFilters = !empty($_GET);
?>

<!-- ========== REPORTS HEADER ========== -->
<div class="c-reports-hero c-anim-fade">
    <div class="c-rh-top">
        <div class="c-rh-info">
            <h5 class="c-rh-title"><i class="fas fa-clipboard-list"></i><?= $pageTitle ?></h5>
            <span class="c-rh-count"><?= number_format($total) ?> <?= __('reports.found') ?></span>
        </div>
        <a href="/reports/create" class="c-btn c-btn-primary c-rh-cta">
            <i class="fas fa-plus"></i>
            <span><?= __('reports.new') ?></span>
        </a>
    </div>
</div>

<!-- ========== SEARCH BAR ========== -->
<form method="GET" action="/reports" id="citizenFilterForm" class="c-reports-search-form c-anim-fade c-delay-1">
    <div class="c-rs-search">
        <i class="fas fa-search c-rs-search-icon"></i>
        <input type="text" name="search" id="citizenFilterSearch" class="c-rs-input" placeholder="<?= __('reports.search_placeholder') ?>" value="<?= $activeSearch ?>" autocomplete="off">
        <kbd class="c-rs-kbd">/</kbd>
    </div>

    <!-- Hidden fields to preserve filters on search submit -->
    <?php if ($activeStatus): ?><input type="hidden" name="status" value="<?= htmlspecialchars($activeStatus) ?>"><?php endif; ?>
    <?php if ($activeCatId): ?><input type="hidden" name="category_id" value="<?= htmlspecialchars($activeCatId) ?>"><?php endif; ?>
    <?php if ($activeSubcatId): ?><input type="hidden" name="subcategory_id" value="<?= htmlspecialchars($activeSubcatId) ?>"><?php endif; ?>
</form>

<!-- ========== STATUS FILTER PILLS ========== -->
<div class="c-reports-filters c-anim-fade c-delay-2">
    <div class="c-rf-pills">
        <a href="/reports" class="c-rf-pill <?= $activeStatus === '' ? 'active' : '' ?>">
            <span><?= __('reports.all') ?></span>
        </a>
        <?php foreach ($filterStatuses as $fs): ?>
        <a href="/reports?status=<?= $fs ?><?= $activeSearch ? '&search=' . urlencode($_GET['search'] ?? '') : '' ?><?= $activeCatId ? '&category_id=' . $activeCatId : '' ?><?= $activeSubcatId ? '&subcategory_id=' . $activeSubcatId : '' ?>"
           class="c-rf-pill <?= $activeStatus === $fs ? 'active' : '' ?>"
           data-status-color="<?= $fs ?>">
            <span><?= __('statuses.' . $fs) ?></span>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Category & Subcategory Dropdowns -->
    <div class="c-rf-selects">
        <div class="c-rf-select-wrap">
            <i class="fas fa-tag c-rf-select-icon"></i>
            <select name="category_id" class="c-rf-select" id="citizenFilterCategory" onchange="loadCitizenSubcategories(this.value)">
                <option value=""><?= __('reports.all_categories') ?></option>
                <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $activeCatId == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="c-rf-select-wrap">
            <i class="fas fa-layer-group c-rf-select-icon"></i>
            <select name="subcategory_id" class="c-rf-select" id="citizenFilterSubcategory">
                <option value=""><?= __('reports.all_subcategories') ?></option>
            </select>
        </div>
        <button type="submit" form="citizenFilterForm" class="c-rf-apply" title="<?= __('common.filter') ?>">
            <i class="fas fa-sliders"></i>
        </button>
        <?php if ($hasFilters): ?>
        <a href="/reports" class="c-rf-reset" title="<?= __('common.reset') ?>">
            <i class="fas fa-xmark"></i>
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- ========== REPORT CARDS ========== -->
<div class="c-reports-list c-anim-fade c-delay-3">
    <?php if (empty($reports)): ?>
    <div class="c-empty-state">
        <div class="c-es-icon">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <h5 class="c-es-title"><?= __('reports.no_reports') ?></h5>
        <p class="c-es-desc"><?= __('reports.no_reports_citizen') ?></p>
        <a href="/reports/create" class="c-btn c-btn-primary c-btn-lg c-es-cta">
            <i class="fas fa-plus"></i>
            <?= __('reports.create_first') ?>
        </a>
    </div>
    <?php else: ?>
    <div class="c-reports-grid">
        <?php foreach ($reports as $ri => $r):
            $wf = $r['workflow_step'] ?? 0;
            $wfTotal = 8;
            $wfPct = $wfTotal > 0 ? round(($wf / $wfTotal) * 100) : 0;
            $catColor = $r['category_color'] ?? 'var(--c-accent)';
            $sLabel = __('statuses.' . $r['status']);
            $title = htmlspecialchars(mb_strimwidth($r['title'], 0, 60, '…'));
            $daira = htmlspecialchars($r['daira_name'] ?? '');
            $commune = htmlspecialchars($r['commune_name'] ?? '');
            $catName = htmlspecialchars($r['category_name'] ?? '');
            $catIcon = $r['category_icon'] ?? 'fa-flag';
            $trackingCode = htmlspecialchars($r['tracking_code']);
            $createdDate = date('d/m/Y', strtotime($r['created_at']));
            $createdTime = date('H:i', strtotime($r['created_at']));
            $isRtl = ($currentLang ?? 'fr') === 'ar';
        ?>
        <a href="/reports/<?= $r['id'] ?>" class="c-rc" style="--cat-color:<?= $catColor ?>;animation-delay:<?= ($ri % 8) * 0.05 ?>s;" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>">
            <!-- Accent bar -->
            <div class="c-rc-accent"></div>

            <!-- Card Header -->
            <div class="c-rc-head">
                <div class="c-rc-code-wrap">
                    <span class="c-rc-code" style="color:<?= $catColor ?>"><?= $trackingCode ?></span>
                </div>
                <span class="c-badge <?= $r['status'] ?>"><?= $sLabel ?></span>
            </div>

            <!-- Card Body -->
            <div class="c-rc-body">
                <p class="c-rc-title"><?= $title ?></p>
            </div>

            <!-- Card Meta -->
            <div class="c-rc-meta">
                <span class="c-rc-meta-item">
                    <i class="fas fa-location-dot"></i>
                    <?= $daira ?><?= $commune ? ', ' . $commune : '' ?>
                </span>
                <span class="c-rc-meta-item">
                    <i class="fas <?= $catIcon ?>"></i>
                    <?= $catName ?>
                </span>
            </div>

            <!-- Card Footer -->
            <div class="c-rc-foot">
                <div class="c-rc-date">
                    <i class="far fa-clock"></i>
                    <span><?= $createdDate ?></span>
                    <span class="c-rc-time"><?= $createdTime ?></span>
                </div>
                <div class="c-rc-progress">
                    <div class="c-rc-pbar">
                        <div class="c-rc-pbar-fill" style="width:<?= $wfPct ?>%;background:<?= $catColor ?>"></div>
                    </div>
                    <span class="c-rc-pstep"><?= $wf ?>/<?= $wfTotal ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php else: ?>
<!-- Admin/Staff View -->
<div class="card mb-4 animate-fade-in-up">
    <div class="card-body">
        <form method="GET" action="/reports" class="row g-2 align-items-end filter-form">
            <div class="col-md-2">
                <label class="form-label"><?= __('reports.search') ?></label>
                <input type="text" class="form-control form-control-sm" name="search" placeholder="<?= __('reports.search_placeholder') ?>" value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label"><?= __('common.status') ?></label>
                <select class="form-select form-select-sm" name="status">
                    <option value=""><?= __('reports.all') ?></option>
                    <option value="submitted" <?= ($_GET['status'] ?? '') === 'submitted' ? 'selected' : '' ?>><?= __('statuses.submitted') ?></option>
                    <option value="acknowledged" <?= ($_GET['status'] ?? '') === 'acknowledged' ? 'selected' : '' ?>><?= __('statuses.acknowledged') ?></option>
                    <option value="assigned" <?= ($_GET['status'] ?? '') === 'assigned' ? 'selected' : '' ?>><?= __('statuses.assigned') ?></option>
                    <option value="in_progress" <?= ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>><?= __('statuses.in_progress') ?></option>
                    <option value="resolved" <?= ($_GET['status'] ?? '') === 'resolved' ? 'selected' : '' ?>><?= __('statuses.resolved') ?></option>
                    <option value="closed" <?= ($_GET['status'] ?? '') === 'closed' ? 'selected' : '' ?>><?= __('statuses.closed') ?></option>
                    <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>><?= __('statuses.rejected') ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label"><?= __('common.priority') ?></label>
                <select class="form-select form-select-sm" name="priority">
                    <option value=""><?= __('reports.all_priorities') ?></option>
                    <option value="low" <?= ($_GET['priority'] ?? '') === 'low' ? 'selected' : '' ?>><?= __('priorities.low') ?></option>
                    <option value="medium" <?= ($_GET['priority'] ?? '') === 'medium' ? 'selected' : '' ?>><?= __('priorities.medium') ?></option>
                    <option value="high" <?= ($_GET['priority'] ?? '') === 'high' ? 'selected' : '' ?>><?= __('priorities.high') ?></option>
                    <option value="urgent" <?= ($_GET['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>><?= __('priorities.urgent') ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label"><?= __('common.category') ?></label>
                <select class="form-select form-select-sm" name="category_id" id="filterCategorySelect" onchange="loadFilterSubcategories(this.value)">
                    <option value=""><?= __('reports.all_categories') ?></option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($_GET['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label"><?= __('common.subcategory') ?></label>
                <select class="form-select form-select-sm" name="subcategory_id" id="filterSubcategorySelect">
                    <option value=""><?= __('reports.all_subcategories') ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label"><?= __('common.daira') ?></label>
                <select class="form-select form-select-sm" name="daira_id">
                    <option value=""><?= __('reports.all_dairas') ?></option>
                    <?php foreach ($dairas as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($_GET['daira_id'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= $d['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">Du</label>
                <input type="date" class="form-control form-control-sm" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
            </div>
            <div class="col-md-1">
                <label class="form-label">Au</label>
                <input type="date" class="form-control form-control-sm" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-primary"><i class="fas fa-filter"></i></button></div>
            <div class="col-auto"><a href="/reports" class="btn btn-sm btn-outline-secondary"><?= __('common.reset') ?></a></div>
        </form>
    </div>
</div>

<div class="card animate-fade-in-up">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="reportsTable">
                <thead>
                    <tr><th><?= __('common.code') ?></th><th><?= __('common.title') ?></th><th><?= __('common.category') ?></th><th class="d-none d-lg-table-cell"><?= __('common.daira') ?></th><th class="d-none d-xl-table-cell"><?= __('common.commune') ?></th><th class="d-none d-xl-table-cell"><?= __('common.organization') ?></th><th><?= __('common.priority') ?></th><th><?= __('common.status') ?></th><th><?= __('deadline.deadline') ?></th><th class="d-none d-md-table-cell"><?= __('common.date') ?></th></tr>
                </thead>
                <tbody>
                <?php if (empty($reports)): ?>
                    <tr><td colspan="10" class="text-center text-muted py-5"><div class="empty-state py-3"><i class="fas fa-inbox d-block mb-2"></i><p><?= __('reports.no_reports_staff') ?></p></div></td></tr>
                <?php else: foreach ($reports as $r): ?>
                    <tr>
                        <td><a href="/reports/<?= $r['id'] ?>"><?= $r['tracking_code'] ?></a></td>
                        <td class="truncate" style="max-width:160px;"><?= \App\Helpers\Helper::sanitize(mb_strimwidth($r['title'], 0, 40, '...')) ?></td>
                        <td><span class="badge" style="background:var(--accent-surface);color:var(--accent);"><?= $r['category_name'] ?></span></td>
                        <td class="text-secondary d-none d-lg-table-cell"><?= $r['daira_name'] ?></td>
                        <td class="text-secondary d-none d-xl-table-cell"><?= $r['commune_name'] ?></td>
                        <td class="text-secondary d-none d-xl-table-cell"><?= $r['org_name'] ?? '-' ?></td>
                        <td><?= \App\Helpers\Helper::getPriorityBadge($r['priority']) ?></td>
                        <td><?= \App\Helpers\Helper::getStatusBadge($r['status']) ?></td>
                        <td><small><?= \App\Helpers\DeadlineHelper::renderBadge($r['created_at'], (int)($r['deadline_days'] ?? 7), $r['status']) ?></small></td>
                        <td class="text-muted d-none d-md-table-cell"><small><?= \App\Helpers\Helper::timeAgo($r['created_at']) ?></small></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($reports)): ?>
        <div class="mobile-card-list d-md-none">
            <?php foreach ($reports as $r): ?>
            <div class="mobile-card-item">
                <div class="mc-header">
                    <a href="/reports/<?= $r['id'] ?>" class="fw-bold text-decoration-none"><?= \App\Helpers\Helper::sanitize($r['tracking_code']) ?></a>
                    <?= \App\Helpers\Helper::getStatusBadge($r['status']) ?>
                </div>
                <div class="mc-body">
                    <div class="mc-row"><span class="mc-label"><?= __('common.title') ?></span><span><?= \App\Helpers\Helper::sanitize(mb_strimwidth($r['title'], 0, 35, '...')) ?></span></div>
                    <div class="mc-row"><span class="mc-label"><?= __('common.category') ?></span><span class="badge" style="background:var(--accent-surface);color:var(--accent);"><?= $r['category_name'] ?></span></div>
                    <div class="mc-row"><span class="mc-label"><?= __('common.priority') ?></span><span><?= \App\Helpers\Helper::getPriorityBadge($r['priority']) ?></span></div>
                    <div class="mc-row"><span class="mc-label"><?= __('common.daira') ?></span><span><?= $r['daira_name'] ?></span></div>
                    <div class="mc-row"><span class="mc-label"><?= __('deadline.deadline') ?></span><span><?= \App\Helpers\DeadlineHelper::renderBadge($r['created_at'], (int)($r['deadline_days'] ?? 7), $r['status']) ?></span></div>
                </div>
                <div class="mc-footer"><a href="/reports/<?= $r['id'] ?>" class="btn btn-sm btn-primary"><?= __('common.view') ?></a></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php if ($totalPages > 1): ?>
<div class="c-pagination c-anim-fade c-delay-4">
    <?php if ($page > 1): ?>
    <a href="?page=<?= $page - 1 ?>&<?= http_build_query(array_filter($_GET, fn($k) => $k !== 'page', ARRAY_FILTER_USE_KEY)) ?>" class="c-page-btn">
        <i class="fas fa-chevron-left"></i>
    </a>
    <?php endif; ?>
    <?php
    $range = 2;
    $start = max(1, $page - $range);
    $end = min($totalPages, $page + $range);
    if ($start > 1): ?>
    <a href="?page=1&<?= http_build_query(array_filter($_GET, fn($k) => $k !== 'page', ARRAY_FILTER_USE_KEY)) ?>" class="c-page-btn">1</a>
    <?php if ($start > 2): ?><span class="c-page-dots">…</span><?php endif; ?>
    <?php endif; ?>
    <?php for ($i = $start; $i <= $end; $i++): ?>
    <a href="?page=<?= $i ?>&<?= http_build_query(array_filter($_GET, fn($k) => $k !== 'page', ARRAY_FILTER_USE_KEY)) ?>" class="c-page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($end < $totalPages): ?>
    <?php if ($end < $totalPages - 1): ?><span class="c-page-dots">…</span><?php endif; ?>
    <a href="?page=<?= $totalPages ?>&<?= http_build_query(array_filter($_GET, fn($k) => $k !== 'page', ARRAY_FILTER_USE_KEY)) ?>" class="c-page-btn"><?= $totalPages ?></a>
    <?php endif; ?>
    <?php if ($page < $totalPages): ?>
    <a href="?page=<?= $page + 1 ?>&<?= http_build_query(array_filter($_GET, fn($k) => $k !== 'page', ARRAY_FILTER_USE_KEY)) ?>" class="c-page-btn">
        <i class="fas fa-chevron-right"></i>
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<script>
function loadFilterSubcategories(catId) {
    var sel = document.getElementById('filterSubcategorySelect');
    sel.innerHTML = '<option value="">...</option>';
    if (!catId) { sel.innerHTML = '<option value=""><?= __('reports.all_subcategories') ?></option>'; return; }
    fetch('/api/subcategories/' + catId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            sel.innerHTML = '<option value=""><?= __('reports.all_subcategories') ?></option>';
            data.forEach(function(s) {
                var opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.name;
                if ('<?= htmlspecialchars($_GET["subcategory_id"] ?? "", ENT_QUOTES, 'UTF-8') ?>' == s.id) opt.selected = true;
                sel.appendChild(opt);
            });
        });
}
document.addEventListener('DOMContentLoaded', function() {
    var filterCat = document.getElementById('filterCategorySelect');
    if (filterCat) {
        var catId = filterCat.value;
        if (catId) loadFilterSubcategories(catId);
    }
    var citizenCat = document.getElementById('citizenFilterCategory');
    if (citizenCat && citizenCat.value) loadCitizenSubcategories(citizenCat.value);
});
function loadCitizenSubcategories(catId) {
    var sel = document.getElementById('citizenFilterSubcategory');
    sel.innerHTML = '<option value="">...</option>';
    if (!catId) { sel.innerHTML = '<option value=""><?= __('reports.all_subcategories') ?></option>'; return; }
    fetch('/api/subcategories/' + catId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            sel.innerHTML = '<option value=""><?= __('reports.all_subcategories') ?></option>';
            data.forEach(function(s) {
                var opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.name;
                if ('<?= htmlspecialchars($_GET["subcategory_id"] ?? "", ENT_QUOTES, 'UTF-8') ?>' == s.id) opt.selected = true;
                sel.appendChild(opt);
            });
        });
}
</script>
