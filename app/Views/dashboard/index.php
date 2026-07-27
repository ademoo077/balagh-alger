<?php $pageTitle = __('dashboard.title'); ?>
<?php $isRtl = \App\Helpers\I18n::isRtl(); ?>
<?php $isChefUnite = ($primaryRole ?? '') === 'chef_unite'; ?>
<?php $isChefSection = ($primaryRole ?? '') === 'chef_section'; ?>
<?php $isAgent = ($primaryRole ?? '') === 'intervenant'; ?>

<!-- Page Header -->
<div class="page-header animate-fade-in-up">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4>
                <i class="fas fa-th-large me-2 text-accent"></i>
                <?php if ($isChefUnite): ?>
                    <?= __('dashboard.daira_dashboard') ?>
                <?php elseif ($isChefSection): ?>
                    <?= __('dashboard.section_dashboard') ?>
                <?php else: ?>
                    <?= __('dashboard.title') ?>
                <?php endif; ?>
            </h4>
            <small class="text-secondary"><?= $periodLabel ?? 'Tout' ?> — <?= \App\Helpers\Session::get('daira_name', __('ui.wilaya_alger')) ?></small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="period-filter d-none d-sm-block">
                <div class="btn-group" role="group">
                    <a href="?period=" class="btn btn-sm btn-outline-secondary <?= ($period ?? '') === '' ? 'active' : '' ?>"><?= __('ui.period_all') ?></a>
                    <a href="?period=today" class="btn btn-sm btn-outline-secondary <?= ($period ?? '') === 'today' ? 'active' : '' ?>"><?= __('ui.period_day') ?></a>
                    <a href="?period=week" class="btn btn-sm btn-outline-secondary <?= ($period ?? '') === 'week' ? 'active' : '' ?>"><?= __('ui.period_week') ?></a>
                    <a href="?period=month" class="btn btn-sm btn-outline-secondary <?= ($period ?? '') === 'month' ? 'active' : '' ?>"><?= __('ui.period_month') ?></a>
                    <a href="?period=quarter" class="btn btn-sm btn-outline-secondary <?= ($period ?? '') === 'quarter' ? 'active' : '' ?>"><?= __('ui.period_quarter') ?></a>
                    <a href="?period=year" class="btn btn-sm btn-outline-secondary <?= ($period ?? '') === 'year' ? 'active' : '' ?>"><?= __('ui.period_year') ?></a>
                </div>
            </div>
            <div class="text-end d-none d-sm-block">
                <div class="text-secondary" style="font-size:0.75rem;"><i class="fas fa-map-marker-alt me-1"></i><?= \App\Helpers\Session::get('daira_name', __('ui.wilaya_alger')) ?></div>
                <div id="live-clock" class="font-mono" style="font-size:1.2rem;font-weight:700;font-variant-numeric:tabular-nums;"></div>
            </div>
            <?php if (\App\Helpers\Rbac::has('reports.export')): ?>
            <a href="/reports/export-monthly" class="btn btn-sm btn-primary" target="_blank">
                <i class="fas fa-file-pdf me-1"></i> <?= __('ui.monthly_report') ?>
            </a>
            <?php endif; ?>
            <a href="/reports/create" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i><?= __('ui.new_report') ?></a>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card accent animate-fade-in-up stagger-1">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:var(--accent-surface);color:var(--accent);">
                        <i class="fas fa-flag"></i>
                    </div>
                    <div class="ms-3">
                        <h3 data-count-up="<?= $total ?>"><?= number_format($total) ?></h3>
                        <div class="stat-label"><?= __('dashboard.total_reports') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card amber animate-fade-in-up stagger-2">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:var(--amber-surface);color:var(--amber);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="ms-3">
                        <h3 data-count-up="<?= $pending ?>"><?= number_format($pending) ?></h3>
                        <div class="stat-label"><?= __('dashboard.pending') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card cyan animate-fade-in-up stagger-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:var(--cyan-surface);color:var(--cyan);">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div class="ms-3">
                        <h3 data-count-up="<?= $inProgress ?>"><?= number_format($inProgress) ?></h3>
                        <div class="stat-label"><?= __('dashboard.in_progress') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card green animate-fade-in-up stagger-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:var(--green-surface);color:var(--green);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ms-3">
                        <h3 data-count-up="<?= $resolved ?>"><?= number_format($resolved) ?></h3>
                        <div class="stat-label"><?= __('dashboard.resolved') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary KPIs -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-mini animate-fade-in-up stagger-1">
            <div class="kpi-icon" style="background:var(--red-surface);color:var(--red);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div class="kpi-value text-red" data-count-up="<?= $urgent ?>"><?= number_format($urgent) ?></div>
                <div class="kpi-label"><?= __('dashboard.urgent') ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-mini animate-fade-in-up stagger-2">
            <div class="kpi-icon" style="background:var(--accent-surface);color:var(--accent);">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <div class="kpi-value text-accent" data-count-up="<?= $today ?>"><?= number_format($today) ?></div>
                <div class="kpi-label"><?= __('dashboard.today') ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-mini animate-fade-in-up stagger-3">
            <div class="kpi-icon" style="background:var(--green-surface);color:var(--green);">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <div class="kpi-value text-green"><?= $total > 0 ? round(($resolved / $total) * 100) : 0 ?>%</div>
                <div class="kpi-label"><?= __('dashboard.resolution_rate') ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="kpi-mini animate-fade-in-up stagger-4">
            <div class="kpi-icon" style="background:var(--cyan-surface);color:var(--cyan);">
                <i class="fas fa-map-location-dot"></i>
            </div>
            <div>
                <div class="kpi-value text-cyan" data-count-up="<?= count($mapData) ?>"><?= number_format(count($mapData)) ?></div>
                <div class="kpi-label"><?= __('dashboard.geolocated') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Overdue KPI -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card animate-fade-in-up" style="border-inline-start:3px solid var(--red);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:var(--red-surface);color:var(--red);">
                        <i class="fas fa-hourglass-end"></i>
                    </div>
                    <div class="ms-3">
                        <h3 style="color:var(--red);"><?= number_format($overdue ?? 0) ?></h3>
                        <div class="stat-label"><?= __('dashboard.overdue') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Commune Stats (chef_section only) -->
<?php if ($isChefSection && !empty($communeStats)): ?>
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card animate-fade-in-up">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0"><i class="fas fa-city me-2 text-accent"></i><?= __('dashboard.commune_stats') ?></h6>
                <span class="badge bg-accent" style="background:var(--accent);color:#fff;"><?= count($communeStats) ?> <?= __('dashboard.communes_assigned') ?></span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($communeStats as $cs): ?>
                    <div class="col-xl-4 col-md-6">
                        <div class="card h-100" style="border:1px solid var(--border);border-radius:12px;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.08)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:36px;height:36px;border-radius:10px;background:var(--accent-surface);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:0.85rem;">
                                            <i class="fas fa-city"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="font-size:0.9rem;"><?= htmlspecialchars($cs['commune_name']) ?></div>
                                            <div class="text-muted" style="font-size:0.7rem;"><?= number_format($cs['total']) ?> <?= __('dashboard.total_reports') ?></div>
                                        </div>
                                    </div>
                                    <?php
                                    $rate = $cs['total'] > 0 ? round(($cs['resolved'] / $cs['total']) * 100) : 0;
                                    $rateColor = ($rate >= 70) ? 'var(--green)' : (($rate >= 40) ? 'var(--amber)' : 'var(--red)');
                                    ?>
                                    <div style="width:42px;height:42px;position:relative;">
                                        <svg viewBox="0 0 36 36" style="width:100%;height:100%;transform:rotate(-90deg);">
                                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="var(--border)" stroke-width="3"/>
                                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="<?= $rateColor ?>" stroke-width="3" stroke-dasharray="<?= $rate * 0.974 ?> 100" stroke-linecap="round" style="transition:stroke-dasharray 1s ease-out;"/>
                                        </svg>
                                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:700;color:<?= $rateColor ?>;"><?= $rate ?>%</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <div style="flex:1;min-width:60px;padding:0.4rem 0.5rem;border-radius:8px;background:var(--amber-surface);text-align:center;">
                                        <div style="font-size:0.95rem;font-weight:700;color:var(--amber);"><?= $cs['pending'] ?></div>
                                        <div style="font-size:0.62rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.3px;"><?= __('dashboard.pending') ?></div>
                                    </div>
                                    <div style="flex:1;min-width:60px;padding:0.4rem 0.5rem;border-radius:8px;background:var(--cyan-surface);text-align:center;">
                                        <div style="font-size:0.95rem;font-weight:700;color:var(--cyan);"><?= $cs['in_progress'] ?></div>
                                        <div style="font-size:0.62rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.3px;"><?= __('dashboard.in_progress') ?></div>
                                    </div>
                                    <div style="flex:1;min-width:60px;padding:0.4rem 0.5rem;border-radius:8px;background:var(--green-surface);text-align:center;">
                                        <div style="font-size:0.95rem;font-weight:700;color:var(--green);"><?= $cs['resolved'] ?></div>
                                        <div style="font-size:0.62rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.3px;"><?= __('dashboard.resolved') ?></div>
                                    </div>
                                </div>
                                <?php if ($cs['overdue'] > 0 || $cs['urgent'] > 0): ?>
                                <div class="d-flex gap-2 mt-2">
                                    <?php if ($cs['urgent'] > 0): ?>
                                    <div style="padding:0.25rem 0.5rem;border-radius:6px;background:var(--red-surface);font-size:0.68rem;color:var(--red);font-weight:600;">
                                        <i class="fas fa-exclamation-triangle me-1"></i><?= $cs['urgent'] ?> urgent<?= $cs['urgent'] > 1 ? 's' : '' ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($cs['overdue'] > 0): ?>
                                    <div style="padding:0.25rem 0.5rem;border-radius:6px;background:var(--red-surface);font-size:0.68rem;color:var(--red);font-weight:600;">
                                        <i class="fas fa-clock me-1"></i><?= $cs['overdue'] ?> en retard
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($cs['total'] > 0): ?>
                                <div class="mt-2" style="height:3px;border-radius:2px;background:var(--border);overflow:hidden;">
                                    <div style="height:100%;width:<?= $rate ?>%;background:<?= $rateColor ?>;border-radius:2px;transition:width 1s ease-out;"></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-xl-4">
        <div class="card h-100 animate-fade-in-up">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-accent"></i><?= __('dashboard.by_category') ?></h6>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" height="260"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100 animate-fade-in-up">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2 text-cyan"></i>
                    <?php if (($isChefUnite || $isChefSection) && !empty($byCommune)): ?>
                        <?= __('dashboard.by_commune') ?>
                    <?php else: ?>
                        <?= __('dashboard.by_daira') ?>
                    <?php endif; ?>
                </h6>
            </div>
            <div class="card-body">
                <?php if (($isChefUnite || $isChefSection) && !empty($byCommune)): ?>
                    <canvas id="communeChart" height="260"></canvas>
                <?php else: ?>
                    <canvas id="dairaChart" height="260"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100 animate-fade-in-up">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-amber"></i><?= __('dashboard.by_priority') ?></h6>
            </div>
            <div class="card-body">
                <canvas id="priorityChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Subcategory Chart -->
<?php if (!empty($bySubcategory)): ?>
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card animate-fade-in-up">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-layer-group me-2 text-purple"></i><?= __('dashboard.by_subcategory') ?></h6>
            </div>
            <div class="card-body">
                <canvas id="subcategoryChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Commune Ranking -->
<div class="row g-3 mb-4">
    <div class="col-xl-6">
        <div class="card animate-fade-in-up">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-ranking-star me-2 text-amber"></i>Classement des communes</h6>
            </div>
            <div class="card-body">
                <canvas id="communeRankChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card animate-fade-in-up">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2 text-cyan"></i>Prévision charge</h6>
            </div>
            <div class="card-body">
                <canvas id="chargeTrendChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Map -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card animate-fade-in-up">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0"><i class="fas fa-map-marked-alt me-2 text-cyan"></i><?= __('dashboard.map_title') ?></h6>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <select id="mapFilterCategory" class="form-select form-select-sm" style="width:auto;" onchange="filterMapMarkers()">
                        <option value=""><?= __('reports.all_categories') ?></option>
                        <?php foreach ($byCategory as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="mapFilterStatus" class="form-select form-select-sm" style="width:auto;" onchange="filterMapMarkers()">
                        <option value=""><?= __('common.status') ?></option>
                        <option value="submitted"><?= __('statuses.submitted') ?></option>
                        <option value="in_progress"><?= __('statuses.in_progress') ?></option>
                        <option value="resolved"><?= __('statuses.resolved') ?></option>
                        <option value="closed"><?= __('statuses.closed') ?></option>
                    </select>
                    <button id="heatmapToggle" class="btn btn-sm btn-outline-danger" onclick="toggleHeatmap()"><i class="fas fa-fire me-1"></i> <?= __('dashboard.heatmap_toggle') ?></button>
                    <a href="#" onclick="dashMap.setView([36.7538, 3.0588], 11); return false;" class="btn btn-sm btn-outline-primary"><?= __('dashboard.recenter') ?></a>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="dashboardMap" style="height: 380px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Reports -->
<div class="row">
    <div class="col-12">
        <div class="card animate-fade-in-up">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-clock me-2 text-accent"></i><?= __('dashboard.recent_reports') ?></h6>
                <a href="/reports" class="btn btn-sm btn-outline-primary"><?= __('dashboard.see_all') ?></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th><?= __('common.code') ?></th>
                                <th><?= __('common.title') ?></th>
                                <th><?= __('common.category') ?></th>
                                <th class="d-none d-lg-table-cell"><?= __('common.daira') ?></th>
                                <th><?= __('common.priority') ?></th>
                                <th><?= __('ui.status') ?></th>
                                <th><?= __('ui.deadline') ?></th>
                                <th class="d-none d-md-table-cell"><?= __('common.date') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($recentReports)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-5">
                                <div class="empty-state py-3"><i class="fas fa-inbox d-block mb-2"></i><p><?= __('dashboard.no_reports') ?></p></div>
                            </td></tr>
                        <?php else: foreach ($recentReports as $r): ?>
                            <tr>
                                <td><a href="/reports/<?= $r['id'] ?>"><?= $r['tracking_code'] ?></a></td>
                                <td class="truncate" style="max-width:180px;"><?= \App\Helpers\Helper::sanitize($r['title']) ?></td>
                                <td><span class="badge" style="background:<?= ($r['category_color'] ?? '#6366f1') ?>18;color:<?= $r['category_color'] ?? '#6366f1' ?>;"><?= $r['category_name'] ?></span></td>
                                <td class="text-secondary d-none d-lg-table-cell"><?= $r['daira_name'] ?></td>
                                <td><?= \App\Helpers\Helper::getPriorityBadge($r['priority']) ?></td>
                                <td><?= \App\Helpers\Helper::getStatusBadge($r['status']) ?></td>
                                <td><small><?= \App\Helpers\DeadlineHelper::renderBadge($r['created_at'], (int)($r['deadline_days'] ?? 7), $r['status']) ?></small></td>
                                <td class="text-muted d-none d-md-table-cell"><small><?= \App\Helpers\Helper::timeAgo($r['created_at']) ?></small></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var theme = document.documentElement.getAttribute('data-bs-theme');
    var textColor = theme === 'dark' ? '#94a3b8' : '#6b7280';
    var gridColor = theme === 'dark' ? 'rgba(255,255,255,0.04)' : 'rgba(0,0,0,0.06)';

    var chartDefaults = { responsive: true, maintainAspectRatio: false };

    var catData = <?= json_encode($byCategory) ?>;
    if (catData.length > 0) {
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: catData.map(function(d) { return d.name; }),
                datasets: [{ data: catData.map(function(d) { return d.count; }), backgroundColor: catData.map(function(d) { return d.color || '#6366f1'; }), borderWidth: 0, hoverOffset: 6 }]
            },
            options: Object.assign({}, chartDefaults, { cutout: '68%', plugins: { legend: { position: 'bottom', labels: { color: textColor, padding: 10, font: { size: 11, family: '<?= $isRtl ? "Noto Sans Arabic, Inter" : "Inter" ?>' }, usePointStyle: true, pointStyleWidth: 8 } } } })
        });
    }

    var dairaData = <?= json_encode($byDaira ?? []) ?>;
    var communeData = <?= json_encode($byCommune ?? []) ?>;
    var chartData = communeData.length > 0 ? communeData : dairaData;
    var chartId = communeData.length > 0 ? 'communeChart' : 'dairaChart';

    if (chartData.length > 0 && document.getElementById(chartId)) {
        new Chart(document.getElementById(chartId), {
            type: 'bar',
            data: {
                labels: chartData.map(function(d) { return d.name; }),
                datasets: [{ data: chartData.map(function(d) { return d.count; }), backgroundColor: communeData.length > 0 ? 'rgba(99,102,241,0.6)' : 'rgba(34,211,238,0.6)', borderRadius: 6, borderSkipped: false }]
            },
            options: Object.assign({}, chartDefaults, { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor } }, y: { ticks: { color: textColor, font: { size: 11 } }, grid: { display: false } } } })
        });
    }

    var prioData = <?= json_encode($byPriority) ?>;
    var prioColors = { low: '#059669', medium: '#d97706', high: '#dc2626', urgent: '#b91c1c' };
    var prioLabels = { low: '<?= __('ui.priority_low') ?>', medium: '<?= __('ui.priority_medium') ?>', high: '<?= __('ui.priority_high') ?>', urgent: '<?= __('ui.priority_urgent') ?>' };
    if (prioData.length > 0) {
        new Chart(document.getElementById('priorityChart'), {
            type: 'doughnut',
            data: {
                labels: prioData.map(function(d) { return prioLabels[d.priority] || d.priority; }),
                datasets: [{ data: prioData.map(function(d) { return d.count; }), backgroundColor: prioData.map(function(d) { return prioColors[d.priority] || '#64748b'; }), borderWidth: 0, hoverOffset: 6 }]
            },
            options: Object.assign({}, chartDefaults, { cutout: '68%', plugins: { legend: { position: 'bottom', labels: { color: textColor, padding: 10, font: { size: 11, family: '<?= $isRtl ? "Noto Sans Arabic, Inter" : "Inter" ?>' }, usePointStyle: true, pointStyleWidth: 8 } } } })
        });
    }

    var subcatData = <?= json_encode($bySubcategory ?? []) ?>;
    if (subcatData.length > 0 && document.getElementById('subcategoryChart')) {
        new Chart(document.getElementById('subcategoryChart'), {
            type: 'bar',
            data: {
                labels: subcatData.map(function(d) { return d.subcat_name; }),
                datasets: [{ data: subcatData.map(function(d) { return d.count; }), backgroundColor: subcatData.map(function(d) { return d.color || '#6366f1'; }), borderRadius: 6, borderSkipped: false }]
            },
            options: Object.assign({}, chartDefaults, { indexAxis: 'y', plugins: { legend: { display: false }, tooltip: { callbacks: { afterLabel: function(ctx) { return subcatData[ctx.dataIndex].cat_name; } } } }, scales: { x: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor }, beginAtZero: true }, y: { ticks: { color: textColor, font: { size: 11 } }, grid: { display: false } } } })
        });
    }

    // Commune Ranking Chart
    fetch('/api/commune-ranking').then(function(r){return r.json()}).then(function(data){
        if(!data.length||!document.getElementById('communeRankChart'))return;
        new Chart(document.getElementById('communeRankChart'),{
            type:'bar',
            data:{labels:data.map(function(d){return d.commune_name}),datasets:[
                {label:'Ce mois',data:data.map(function(d){return d.month_count}),backgroundColor:'rgba(99,102,241,0.7)',borderRadius:6,borderSkipped:false},
                {label:'Taux résolution %',data:data.map(function(d){return d.resolution_rate}),backgroundColor:'rgba(34,197,94,0.5)',borderRadius:6,borderSkipped:false}
            ]},
            options:Object.assign({},chartDefaults,{indexAxis:'y',plugins:{legend:{position:'bottom',labels:{color:textColor,font:{size:10}}}},scales:{x:{ticks:{color:textColor,font:{size:10}},grid:{color:gridColor}},y:{ticks:{color:textColor,font:{size:10}},grid:{display:false}}}})
        });
    });

    // Charge Trend Prediction
    var byMonth = <?= json_encode($byMonth ?? []) ?>;
    if(byMonth.length>0&&document.getElementById('chargeTrendChart')){
        var labels=byMonth.map(function(d){return['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'][d.month-1]});
        var vals=byMonth.map(function(d){return d.count});
        var avg=vals.reduce(function(a,b){return a+b},0)/vals.length;
        var pred=vals.map(function(){return Math.round(avg*1.1)});
        new Chart(document.getElementById('chargeTrendChart'),{
            type:'line',
            data:{labels:labels,datasets:[
                {label:'Réel',data:vals,borderColor:'#6366f1',backgroundColor:'rgba(99,102,241,0.08)',fill:true,tension:0.4,pointRadius:4,pointBackgroundColor:'#6366f1'},
                {label:'Prévision',data:pred,borderColor:'#f59e0b',borderDash:[5,5],backgroundColor:'transparent',tension:0.4,pointRadius:3,pointBackgroundColor:'#f59e0b'}
            ]},
            options:Object.assign({},chartDefaults,{plugins:{legend:{position:'bottom',labels:{color:textColor,font:{size:10}}}},scales:{x:{ticks:{color:textColor,font:{size:10}},grid:{display:false}},y:{ticks:{color:textColor,font:{size:10}},grid:{color:gridColor},beginAtZero:true}}})
        });
    }

    var mapData = <?= json_encode($mapData) ?>;
    var isDark = theme === 'dark';
    var dashMap = L.map('dashboardMap').setView([36.7538, 3.0588], 11);
    var tileUrl = isDark ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
    L.tileLayer(tileUrl, { attribution: '&copy; OSM &copy; CARTO', maxZoom: 18 }).addTo(dashMap);
    window.dashMap = dashMap;

    var priorityColors = { low: '#059669', medium: '#d97706', high: '#dc2626', urgent: '#b91c1c' };
    var mapMarkers = [];
    mapData.forEach(function(r) {
        if (r.latitude && r.longitude) {
            var color = priorityColors[r.priority] || '#6366f1';
            var marker = L.circleMarker([r.latitude, r.longitude], { radius: 7, fillColor: color, color: isDark ? '#fff' : '#333', weight: 1.5, opacity: 1, fillOpacity: 0.85 });
            marker.bindPopup('<div style="font-family:Inter,sans-serif;font-size:12px;"><b>' + r.tracking_code + '</b><br>' + r.title + '<br><small style="color:' + textColor + ';">' + r.category_name + ' — ' + r.status + '</small><br><a href="/reports/' + r.id + '" style="color:#6366f1;"><?= __("dashboard.see_details") ?></a></div>');
            marker._data = r;
            mapMarkers.push(marker);
            marker.addTo(dashMap);
        }
    });

    window.filterMapMarkers = function() {
        var catVal = document.getElementById('mapFilterCategory').value;
        var statusVal = document.getElementById('mapFilterStatus').value;
        mapMarkers.forEach(function(m) {
            var r = m._data;
            var showCat = !catVal || r.category_name === catVal;
            var showStatus = !statusVal || r.status === statusVal;
            if (showCat && showStatus) {
                m.addTo(dashMap);
            } else {
                dashMap.removeLayer(m);
            }
        });
    };

    // Heatmap
    var heatLayer = null;
    var heatmapActive = false;
    var markersVisible = true;

    window.toggleHeatmap = function() {
        var btn = document.getElementById('heatmapToggle');
        if (!heatmapActive) {
            if (!heatLayer) {
                fetch('/api/reports/heatmap')
                    .then(function(resp) { return resp.json(); })
                    .then(function(data) {
                        var points = data.map(function(r) {
                            var intensity = r.priority === 'urgent' ? 1.0 : r.priority === 'high' ? 0.7 : r.priority === 'medium' ? 0.5 : 0.3;
                            return [r.latitude, r.longitude, intensity];
                        });
                        heatLayer = L.heatLayer(points, { radius: 25, blur: 15, maxZoom: 17, max: 1.0, gradient: { 0.2: '#3b82f6', 0.4: '#06b6d4', 0.6: '#22c55e', 0.8: '#eab308', 1.0: '#ef4444' } });
                        heatLayer.addTo(dashMap);
                        btn.classList.remove('btn-outline-danger');
                        btn.classList.add('btn-danger');
                    });
            } else {
                heatLayer.addTo(dashMap);
                btn.classList.remove('btn-outline-danger');
                btn.classList.add('btn-danger');
            }
            heatmapActive = true;
        } else {
            if (heatLayer) dashMap.removeLayer(heatLayer);
            heatmapActive = false;
            btn.classList.remove('btn-danger');
            btn.classList.add('btn-outline-danger');
        }
    };

    // Live Clock
    var clockEl = document.getElementById('live-clock');
    if (clockEl) {
        function tick() {
            clockEl.textContent = new Date().toLocaleTimeString($isRtl ? 'ar-DZ' : 'fr-DZ', { hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Africa/Algiers' });
        }
        tick();
        setInterval(tick, 1000);
    }
});
</script>
