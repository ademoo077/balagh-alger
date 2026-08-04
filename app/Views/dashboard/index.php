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
            <div class="d-flex align-items-center gap-2 d-none d-sm-flex">
                <input type="date" id="dateFrom" class="form-control form-control-sm" style="width:auto;" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                <span class="text-muted">→</span>
                <input type="date" id="dateTo" class="form-control form-control-sm" style="width:auto;" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
                <button class="btn btn-sm btn-primary" onclick="applyDateFilter()" title="<?= __('ui.apply') ?>"><i class="fas fa-filter"></i></button>
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
            <button class="btn btn-sm btn-outline-success" onclick="exportDashboardCSV()" title="Exporter CSV">
                <i class="fas fa-file-csv me-1"></i> CSV
            </button>
            <a href="/reports/create" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i><?= __('ui.new_report') ?></a>
        </div>
    </div>
</div>

<!-- Drill-down Filter -->
<div id="drilldownChip" class="mb-3" style="display:none;">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-accent" style="background:var(--accent);color:#fff;padding:6px 12px;border-radius:10px;font-size:0.78rem;">
            <i class="fas fa-filter me-1"></i>Filtre : <span id="drilldownLabel"></span>
        </span>
        <button class="btn btn-sm btn-outline-secondary" style="border-radius:8px;padding:2px 10px;font-size:0.72rem;" onclick="clearDrilldown()">
            <i class="fas fa-times"></i> Effacer
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <?php
    function periodArrow(int $current, int $prev): string {
        if ($prev == 0 && $current == 0) return '';
        $diff = $prev == 0 ? 100 : round((($current - $prev) / $prev) * 100);
        if ($diff > 0) return '<span class="period-arrow up"><i class="fas fa-arrow-up"></i> +' . $diff . '%</span>';
        if ($diff < 0) return '<span class="period-arrow down"><i class="fas fa-arrow-down"></i> ' . $diff . '%</span>';
        return '<span class="period-arrow stable"><i class="fas fa-minus"></i> 0%</span>';
    }
    ?>
    <div class="col-xl-3 col-sm-6">
        <div class="card stat-card accent animate-fade-in-up stagger-1">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon" style="background:var(--accent-surface);color:var(--accent);">
                        <i class="fas fa-flag"></i>
                    </div>
                    <div class="ms-3">
                        <div class="d-flex align-items-center gap-2">
                            <h3 data-count-up="<?= $total ?>"><?= number_format($total) ?></h3>
                            <?= periodArrow($total, $prevTotal ?? 0) ?>
                        </div>
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
                        <div class="d-flex align-items-center gap-2">
                            <h3 data-count-up="<?= $pending ?>"><?= number_format($pending) ?></h3>
                            <?= periodArrow($pending, $prevPending ?? 0) ?>
                        </div>
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
                        <div class="d-flex align-items-center gap-2">
                            <h3 data-count-up="<?= $inProgress ?>"><?= number_format($inProgress) ?></h3>
                            <?= periodArrow($inProgress, $prevInProgress ?? 0) ?>
                        </div>
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
                        <div class="d-flex align-items-center gap-2">
                            <h3 data-count-up="<?= $resolved ?>"><?= number_format($resolved) ?></h3>
                            <?= periodArrow($resolved, $prevResolved ?? 0) ?>
                        </div>
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

<!-- Top 5 Widgets -->
<?php if (!empty($topCategories)): ?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100 animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-fire me-2 text-red"></i><?= __('dashboard.by_category') ?> — Top 5</h6></div>
            <div class="card-body p-0">
                <?php foreach ($topCategories as $i => $tc): ?>
                <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-bottom:1px solid var(--border);">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge" style="background:<?= ['var(--red-surface),color:var(--red)','var(--amber-surface),color:var(--amber)','var(--accent-surface),color:var(--accent)','var(--cyan-surface),color:var(--cyan)','var(--green-surface),color:var(--green)'][$i] ?>;min-width:24px;"><?= $i+1 ?></span>
                        <span style="font-size:0.85rem;"><?= htmlspecialchars($tc['name']) ?></span>
                    </div>
                    <span class="fw-bold" style="color:var(--accent);"><?= $tc['count'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-cyan"></i><?= __('dashboard.commune_ranking') ?> — Top 5</h6></div>
            <div class="card-body p-0">
                <?php foreach ($topCommunes as $i => $tco): ?>
                <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-bottom:1px solid var(--border);">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge" style="background:<?= ['var(--red-surface),color:var(--red)','var(--amber-surface),color:var(--amber)','var(--accent-surface),color:var(--accent)','var(--cyan-surface),color:var(--cyan)','var(--green-surface),color:var(--green)'][$i] ?>;min-width:24px;"><?= $i+1 ?></span>
                        <span style="font-size:0.85rem;"><?= htmlspecialchars($tco['name']) ?></span>
                    </div>
                    <span class="fw-bold" style="color:var(--accent);"><?= $tco['count'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-amber"></i><?= __('dashboard.urgent_reports') ?></h6></div>
            <div class="card-body p-0">
                <?php if (!empty($topUrgent)): ?>
                <?php foreach ($topUrgent as $tu): ?>
                <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-bottom:1px solid var(--border);">
                    <div>
                        <a href="/reports/<?= htmlspecialchars($tu['tracking_code']) ?>" class="text-decoration-none fw-bold" style="color:var(--accent);font-size:0.82rem;"><?= htmlspecialchars($tu['tracking_code']) ?></a>
                        <div style="font-size:0.78rem;color:var(--text-muted);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($tu['title']) ?></div>
                    </div>
                    <span class="badge" style="background:var(--red-surface);color:var(--red);font-size:0.7rem;"><?= htmlspecialchars($tu['priority']) ?></span>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center py-3 text-muted" style="font-size:0.85rem;"><i class="fas fa-check-circle me-1 text-green"></i><?= __('dashboard.no_urgent') ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Charts — draggable -->
<div id="charts-container">
<!-- Charts Row -->
<div class="row g-3 mb-4 chart-draggable" data-id="main-charts">
    <div class="col-xl-4">
        <div class="card h-100 animate-fade-in-up">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-accent"></i><?= __('dashboard.by_category') ?></h6>
                <button class="chart-fullscreen-btn" title="Plein écran"><i class="fas fa-expand"></i></button>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-amber"></i><?= __('dashboard.by_priority') ?></h6>
                <button class="chart-fullscreen-btn" title="Plein écran"><i class="fas fa-expand"></i></button>
            </div>
            <div class="card-body">
                <canvas id="priorityChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Subcategory Chart -->
<?php if (!empty($bySubcategory)): ?>
<div class="row g-3 mb-4 chart-draggable" data-id="subcategory">
    <div class="col-12">
        <div class="card animate-fade-in-up">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-layer-group me-2 text-purple"></i><?= __('dashboard.by_subcategory') ?></h6>
                <button class="chart-fullscreen-btn" title="Plein écran"><i class="fas fa-expand"></i></button>
            </div>
            <div class="card-body">
                <canvas id="subcategoryChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Commune Ranking -->
<div class="row g-3 mb-4 chart-draggable" data-id="commune-ranking">
    <div class="col-xl-6">
        <div class="card animate-fade-in-up">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-ranking-star me-2 text-amber"></i><?= __('dashboard.commune_ranking') ?></h6>
                <button class="chart-fullscreen-btn" title="Plein écran"><i class="fas fa-expand"></i></button>
            </div>
            <div class="card-body">
                <canvas id="communeRankChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card animate-fade-in-up">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2 text-cyan"></i><?= __('dashboard.charge_prediction') ?></h6>
                <button class="chart-fullscreen-btn" title="Plein écran"><i class="fas fa-expand"></i></button>
            </div>
            <div class="card-body">
                <canvas id="chargeTrendChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Evolution Charts -->
<div class="row g-3 mb-4 chart-draggable" data-id="evolution">
    <div class="col-xl-6">
        <div class="card animate-fade-in-up">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-chart-area me-2 text-accent"></i><?= __('dashboard.monthly_evolution') ?></h6>
                <button class="chart-fullscreen-btn" title="Plein écran"><i class="fas fa-expand"></i></button>
            </div>
            <div class="card-body">
                <canvas id="evolutionChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card animate-fade-in-up">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-layer-group me-2 text-purple"></i><?= __('dashboard.status_evolution') ?></h6>
                <button class="chart-fullscreen-btn" title="Plein écran"><i class="fas fa-expand"></i></button>
            </div>
            <div class="card-body">
                <canvas id="statusEvolutionChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Activity Heatmap -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card animate-fade-in-up">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-fire me-2 text-red"></i><?= __('dashboard.activity_heatmap') ?></h6>
                <button class="chart-fullscreen-btn" title="Plein écran"><i class="fas fa-expand"></i></button>
            </div>
            <div class="card-body">
                <div id="heatmapGrid" class="heatmap-grid"></div>
                <div class="heatmap-legend mt-2">
                    <span style="font-size:0.7rem;color:var(--text-muted);"><?= __('ui.heatmap_low') ?></span>
                    <span class="heatmap-legend-box" style="background:var(--card-bg);border:1px solid var(--border);"></span>
                    <span class="heatmap-legend-box" style="background:rgba(99,102,241,0.2);"></span>
                    <span class="heatmap-legend-box" style="background:rgba(99,102,241,0.45);"></span>
                    <span class="heatmap-legend-box" style="background:rgba(99,102,241,0.7);"></span>
                    <span class="heatmap-legend-box" style="background:rgba(99,102,241,1);"></span>
                    <span style="font-size:0.7rem;color:var(--text-muted);"><?= __('ui.heatmap_high') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
</div><!-- /charts-container -->

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
                    <table id="recentReportsTable" class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th><?= __('common.code') ?></th>
                                <th><?= __('common.title') ?></th>
                                <th><?= __('common.category') ?></th>
                                <th><?= __('common.daira') ?></th>
                                <th><?= __('common.priority') ?></th>
                                <th><?= __('ui.status') ?></th>
                                <th><?= __('ui.deadline') ?></th>
                                <th><?= __('common.date') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($recentReports)): foreach ($recentReports as $r): ?>
                            <tr>
                                <td><a href="/reports/<?= $r['id'] ?>"><?= $r['tracking_code'] ?></a></td>
                                <td class="truncate" style="max-width:180px;"><?= \App\Helpers\Helper::sanitize($r['title']) ?></td>
                                <td><span class="badge" style="background:<?= ($r['category_color'] ?? '#6366f1') ?>18;color:<?= $r['category_color'] ?? '#6366f1' ?>;"><?= $r['category_name'] ?></span></td>
                                <td class="text-secondary"><?= $r['daira_name'] ?></td>
                                <td><?= \App\Helpers\Helper::getPriorityBadge($r['priority']) ?></td>
                                <td><?= \App\Helpers\Helper::getStatusBadge($r['status']) ?></td>
                                <td><small><?= \App\Helpers\DeadlineHelper::renderBadge($r['created_at'], (int)($r['deadline_days'] ?? 7), $r['status']) ?></small></td>
                                <td class="text-muted"><small><?= \App\Helpers\Helper::timeAgo($r['created_at']) ?></small></td>
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

    var prioColors = { low: '#059669', medium: '#d97706', high: '#dc2626', urgent: '#b91c1c' };
    var prioLabels = { low: '<?= __('ui.priority_low') ?>', medium: '<?= __('ui.priority_medium') ?>', high: '<?= __('ui.priority_high') ?>', urgent: '<?= __('ui.priority_urgent') ?>' };

    // Commune Ranking Chart — loading skeleton
    var communeRankCard = document.getElementById('communeRankChart');
    if (communeRankCard) communeRankCard.closest('.card-body').classList.add('chart-loading');
    fetch('/api/commune-ranking').then(function(r){if(!r.ok)throw new Error(r.status);return r.json()}).then(function(data){
        if(communeRankCard) communeRankCard.closest('.card-body').classList.add('chart-loading-loaded');
        if(!data.length||!communeRankCard)return;
        new Chart(communeRankCard,{
            type:'bar',
            data:{labels:data.map(function(d){return d.commune_name}),datasets:[
                {label:'<?= __('dashboard.commune_this_month') ?>',data:data.map(function(d){return d.month_count}),backgroundColor:'rgba(99,102,241,0.7)',borderRadius:6,borderSkipped:false},
                {label:'<?= __('dashboard.resolution_rate_pct') ?>',data:data.map(function(d){return d.resolution_rate}),backgroundColor:'rgba(34,197,94,0.5)',borderRadius:6,borderSkipped:false}
            ]},
            options:Object.assign({},chartDefaults,{indexAxis:'y',plugins:{legend:{position:'bottom',labels:{color:textColor,font:{size:10}}}},scales:{x:{ticks:{color:textColor,font:{size:10}},grid:{color:gridColor}},y:{ticks:{color:textColor,font:{size:10}},grid:{display:false}}}})
        });
    }).catch(function(err){
        console.error('Commune ranking error:',err);
        if(communeRankCard){
            communeRankCard.closest('.card-body').classList.add('chart-loading-loaded');
            communeRankCard.closest('.card-body').innerHTML='<div class="text-center text-muted py-4"><i class="fas fa-exclamation-triangle mb-2 d-block"></i><?= __("common.error_loading") ?></div>';
        }
    });

    // Charge Trend Prediction
    var byMonth = <?= json_encode($byMonth ?? []) ?>;
    if(byMonth.length>0&&document.getElementById('chargeTrendChart')){
        var labels=byMonth.map(function(d){return d.month;});
        var vals=byMonth.map(function(d){return d.count});
        var n=vals.length;
        var sumX=0,sumY=0,sumXY=0,sumX2=0;
        for(var i=0;i<n;i++){sumX+=i;sumY+=vals[i];sumXY+=i*vals[i];sumX2+=i*i;}
        var m=(n*sumXY-sumX*sumY)/(n*sumX2-sumX*sumX)||0;
        var b=(sumY-m*sumX)/n;
        var pred=vals.map(function(_,i){return Math.max(0,Math.round(m*(n+i)+b));});
        var predLabels=labels.map(function(_,i){
            var d=new Date();d.setMonth(d.getMonth()+i-n+1+(n));
            return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0');
        });
        var allLabels=labels.concat(predLabels.slice(n>1?1:0));
        var predLine=vals.slice(0,-1).concat(pred);
        new Chart(document.getElementById('chargeTrendChart'),{
            type:'line',
            data:{labels:allLabels,datasets:[
                {label:'Réel',data:vals.concat(Array(pred.length).fill(null)),borderColor:'#6366f1',backgroundColor:'rgba(99,102,241,0.08)',fill:true,tension:0.4,pointRadius:4,pointBackgroundColor:'#6366f1'},
                {label:'<?= __('dashboard.prediction') ?>',data:predLine,borderColor:'#f59e0b',borderDash:[5,5],backgroundColor:'transparent',tension:0.4,pointRadius:3,pointBackgroundColor:'#f59e0b'}
            ]},
            options:Object.assign({},chartDefaults,{plugins:{legend:{position:'bottom',labels:{color:textColor,font:{size:10}}}},scales:{x:{ticks:{color:textColor,font:{size:10}},grid:{display:false}},y:{ticks:{color:textColor,font:{size:10}},grid:{color:gridColor},beginAtZero:true}}})
        });
    }

    // Monthly Evolution Chart (line: total + resolved)
    var byMonthEvo = <?= json_encode($byMonthEvolution ?? []) ?>;
    if(byMonthEvo.length>0&&document.getElementById('evolutionChart')){
        new Chart(document.getElementById('evolutionChart'),{
            type:'line',
            data:{labels:byMonthEvo.map(function(d){return d.month;}),datasets:[
                {label:'<?= __('dashboard.total_reports') ?>',data:byMonthEvo.map(function(d){return parseInt(d.total);}),borderColor:'#6366f1',backgroundColor:'rgba(99,102,241,0.1)',fill:true,tension:0.4,pointRadius:4,pointBackgroundColor:'#6366f1'},
                {label:'<?= __('dashboard.resolved') ?>',data:byMonthEvo.map(function(d){return parseInt(d.resolved);}),borderColor:'#22c55e',backgroundColor:'rgba(34,197,94,0.1)',fill:true,tension:0.4,pointRadius:4,pointBackgroundColor:'#22c55e'},
                {label:'<?= __('dashboard.urgent') ?>',data:byMonthEvo.map(function(d){return parseInt(d.urgent);}),borderColor:'#ef4444',backgroundColor:'transparent',tension:0.4,pointRadius:3,pointBackgroundColor:'#ef4444',borderDash:[4,4]}
            ]},
            options:Object.assign({},chartDefaults,{plugins:{legend:{position:'bottom',labels:{color:textColor,font:{size:10}}}},scales:{x:{ticks:{color:textColor,font:{size:10}},grid:{display:false}},y:{ticks:{color:textColor,font:{size:10}},grid:{color:gridColor},beginAtZero:true}}})
        });
    }

    // Status Evolution Stacked Area Chart
    if(byMonthEvo.length>0&&document.getElementById('statusEvolutionChart')){
        new Chart(document.getElementById('statusEvolutionChart'),{
            type:'line',
            data:{labels:byMonthEvo.map(function(d){return d.month;}),datasets:[
                {label:'<?= __('statuses.submitted') ?>',data:byMonthEvo.map(function(d){return parseInt(d.submitted);}),borderColor:'#94a3b8',backgroundColor:'rgba(148,163,184,0.3)',fill:true,tension:0.4,pointRadius:3},
                {label:'<?= __('statuses.in_progress') ?>',data:byMonthEvo.map(function(d){return parseInt(d.in_progress);}),borderColor:'#06b6d4',backgroundColor:'rgba(6,182,212,0.3)',fill:true,tension:0.4,pointRadius:3},
                {label:'<?= __('dashboard.resolved') ?>',data:byMonthEvo.map(function(d){return parseInt(d.resolved);}),borderColor:'#22c55e',backgroundColor:'rgba(34,197,94,0.3)',fill:true,tension:0.4,pointRadius:3}
            ]},
            options:Object.assign({},chartDefaults,{plugins:{legend:{position:'bottom',labels:{color:textColor,font:{size:10}}}},scales:{x:{stacked:true,ticks:{color:textColor,font:{size:10}},grid:{display:false}},y:{stacked:true,ticks:{color:textColor,font:{size:10}},grid:{color:gridColor},beginAtZero:true}}})
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
                    .then(function(resp) { if(!resp.ok)throw new Error(resp.status); return resp.json(); })
                    .then(function(data) {
                        var points = data.map(function(r) {
                            var intensity = r.priority === 'urgent' ? 1.0 : r.priority === 'high' ? 0.7 : r.priority === 'medium' ? 0.5 : 0.3;
                            return [r.latitude, r.longitude, intensity];
                        });
                        heatLayer = L.heatLayer(points, { radius: 25, blur: 15, maxZoom: 17, max: 1.0, gradient: { 0.2: '#3b82f6', 0.4: '#06b6d4', 0.6: '#22c55e', 0.8: '#eab308', 1.0: '#ef4444' } });
                        heatLayer.addTo(dashMap);
                        btn.classList.remove('btn-outline-danger');
                        btn.classList.add('btn-danger');
                    }).catch(function(err) {
                        console.error('Heatmap error:', err);
                        Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de charger la heatmap', timer: 2000, showConfirmButton: false });
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

    // Date range filter
    window.applyDateFilter = function() {
        var from = document.getElementById('dateFrom').value;
        var to = document.getElementById('dateTo').value;
        var params = [];
        if (from) params.push('date_from=' + from);
        if (to) params.push('date_to=' + to);
        window.location.href = '/dashboard' + (params.length ? '?' + params.join('&') : '');
    };

    // Live Clock
    var clockEl = document.getElementById('live-clock');
    if (clockEl) {
        function tick() {
            clockEl.textContent = new Date().toLocaleTimeString('<?= $isRtl ? "ar-DZ" : "fr-DZ" ?>', { hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Africa/Algiers' });
        }
        tick();
        setInterval(tick, 1000);
    }

    // ============================================================
    // FEATURE 3: EXPORT CSV
    // ============================================================
    window.exportDashboardCSV = function() {
        var lines = [];
        lines.push(['BALAGH ALGER - Dashboard Export'].join(','));
        lines.push(['Genere le ' + new Date().toLocaleString('fr-DZ')].join(','));
        lines.push([]);
        lines.push(['=== KPIs ==='].join(','));
        lines.push(['Total', <?= $total ?>].join(','));
        lines.push(['En attente', <?= $pending ?>].join(','));
        lines.push(['En cours', <?= $inProgress ?>].join(','));
        lines.push(['Resolu', <?= $resolved ?>].join(','));
        lines.push(['Urgent', <?= $urgent ?>].join(','));
        lines.push(['En retard', <?= $overdue ?? 0 ?>].join(','));
        lines.push([]);
        lines.push(['=== Par Categorie ===', 'Count'].join(','));
        var catData = <?= json_encode($byCategory) ?>;
        catData.forEach(function(c) { lines.push(['"' + c.name.replace(/"/g, '""') + '"', c.count].join(',')); });
        lines.push([]);
        lines.push(['=== Par Sous-categorie ===', 'Categorie', 'Count'].join(','));
        var subData = <?= json_encode($bySubcategory ?? []) ?>;
        subData.forEach(function(s) { lines.push(['"' + s.subcat_name.replace(/"/g, '""') + '"', '"' + s.cat_name.replace(/"/g, '""') + '"', s.count].join(',')); });
        lines.push([]);
        lines.push(['=== Par Priorite ===', 'Count'].join(','));
        var prioData2 = <?= json_encode($byPriority) ?>;
        prioData2.forEach(function(p) { lines.push([p.priority, p.count].join(',')); });
        lines.push([]);
        lines.push(['=== Par Mois ===', 'Mois', 'Count'].join(','));
        var mData = <?= json_encode($byMonth ?? []) ?>;
        mData.forEach(function(m) { lines.push([m.month, m.count].join(',')); });
        lines.push([]);
        lines.push(['=== Par Daira ===', 'Count'].join(','));
        var dData = <?= json_encode($byDaira ?? []) ?>;
        dData.forEach(function(d) { lines.push(['"' + d.name.replace(/"/g, '""') + '"', d.count].join(',')); });
        var csv = lines.join('\n');
        var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url; a.download = 'dashboard-balagh-' + new Date().toISOString().slice(0,10) + '.csv';
        document.body.appendChild(a); a.click(); document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    // ============================================================
    // FEATURE 4: ACTIVITY HEATMAP
    // ============================================================
    var heatData = <?= json_encode($activityHeatmap ?? []) ?>;
    var dayLabels = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
    var heatGrid = document.getElementById('heatmapGrid');
    if (heatGrid && heatData.length > 0) {
        var maxCount = Math.max.apply(null, heatData.map(function(d) { return d.count; }));
        var heatMap = {};
        heatData.forEach(function(d) { heatMap[d.day + '_' + d.hour] = d.count; });
        var html = '<div class="heatmap-label"></div>';
        for (var h = 0; h < 24; h++) {
            html += '<div class="heatmap-hour-label">' + (h % 3 === 0 ? h + 'h' : '') + '</div>';
        }
        for (var day = 1; day <= 7; day++) {
            html += '<div class="heatmap-label">' + dayLabels[day - 1] + '</div>';
            for (var hour = 0; hour < 24; hour++) {
                var cnt = heatMap[day + '_' + hour] || 0;
                var intensity = maxCount > 0 ? cnt / maxCount : 0;
                var alpha = intensity === 0 ? 0 : Math.max(0.15, intensity);
                var bg = 'rgba(99,102,241,' + alpha.toFixed(2) + ')';
                var tooltip = dayLabels[day - 1] + ' ' + hour + 'h : ' + cnt + ' action' + (cnt !== 1 ? 's' : '');
                html += '<div class="heatmap-cell" style="background:' + bg + ';" data-tooltip="' + tooltip + '"></div>';
            }
        }
        heatGrid.innerHTML = html;
    } else if (heatGrid) {
        heatGrid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:1rem;color:var(--text-muted);font-size:0.82rem;"><?= addslashes(__('dashboard.no_activity_data')) ?></div>';
    }

    // ============================================================
    // FEATURE 1: DRILL-DOWN (Category click → filter charts)
    // ============================================================
    var allCatData = <?= json_encode($byCategory) ?>;
    var allSubData = <?= json_encode($bySubcategory ?? []) ?>;
    var allPrioData = <?= json_encode($byPriority) ?>;
    var allRecentData = <?= json_encode($recentReports) ?>;
    var activeDrilldown = null;

    window.clearDrilldown = function() {
        activeDrilldown = null;
        document.getElementById('drilldownChip').style.display = 'none';
        if (window._catChart) { window._catChart.destroy(); window._catChart = null; }
        if (window._subChart) { window._subChart.destroy(); window._subChart = null; }
        if (window._prioChart) { window._prioChart.destroy(); window._prioChart = null; }
        if (window._dairaChart) { window._dairaChart.destroy(); window._dairaChart = null; }
        renderCatChart(allCatData);
        renderSubChart(allSubData);
        renderPrioChart(allPrioData);
    };

    window.applyDrilldown = function(catName) {
        activeDrilldown = catName;
        document.getElementById('drilldownChip').style.display = 'block';
        document.getElementById('drilldownLabel').textContent = catName;
        var filteredSub = allSubData.filter(function(s) { return s.cat_name === catName; });
        var filteredPrio = [];
        var prioMap = {};
        allRecentData.forEach(function(r) {
            if (r.category_name === catName) {
                var p = r.priority || 'medium';
                prioMap[p] = (prioMap[p] || 0) + 1;
            }
        });
        Object.keys(prioMap).forEach(function(k) { filteredPrio.push({ priority: k, count: prioMap[k] }); });
        var filteredDaira = [];
        var dairaMap = {};
        allRecentData.forEach(function(r) {
            if (r.category_name === catName && r.daira_name) {
                dairaMap[r.daira_name] = (dairaMap[r.daira_name] || 0) + 1;
            }
        });
        Object.keys(dairaMap).forEach(function(k) { filteredDaira.push({ name: k, count: dairaMap[k] }); });
        if (window._catChart) { window._catChart.destroy(); window._catChart = null; }
        if (window._subChart) { window._subChart.destroy(); window._subChart = null; }
        if (window._prioChart) { window._prioChart.destroy(); window._prioChart = null; }
        if (window._dairaChart) { window._dairaChart.destroy(); window._dairaChart = null; }
        renderSubChart(filteredSub);
        renderPrioChart(filteredPrio);
        renderDairaChart(filteredDaira);
    };

    function renderCatChart(data) {
        var el = document.getElementById('categoryChart');
        if (!el || data.length === 0) return;
        window._catChart = new Chart(el, {
            type: 'doughnut',
            data: { labels: data.map(function(d) { return d.name; }), datasets: [{ data: data.map(function(d) { return d.count; }), backgroundColor: data.map(function(d) { return d.color || '#6366f1'; }), borderWidth: 0, hoverOffset: 6 }] },
            options: Object.assign({}, chartDefaults, { cutout: '68%', onClick: function(e, els) { if (els.length > 0) { var idx = els[0].index; applyDrilldown(data[idx].name); } }, plugins: { legend: { position: 'bottom', labels: { color: textColor, padding: 10, font: { size: 11 }, usePointStyle: true, pointStyleWidth: 8 } } } })
        });
    }

    function renderSubChart(data) {
        var el = document.getElementById('subcategoryChart');
        if (!el || data.length === 0) return;
        window._subChart = new Chart(el, {
            type: 'bar',
            data: { labels: data.map(function(d) { return d.subcat_name; }), datasets: [{ data: data.map(function(d) { return d.count; }), backgroundColor: data.map(function(d) { return d.color || '#6366f1'; }), borderRadius: 6, borderSkipped: false }] },
            options: Object.assign({}, chartDefaults, { indexAxis: 'y', plugins: { legend: { display: false }, tooltip: { callbacks: { afterLabel: function(ctx) { return data[ctx.dataIndex].cat_name; } } } }, scales: { x: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor }, beginAtZero: true }, y: { ticks: { color: textColor, font: { size: 11 } }, grid: { display: false } } } })
        });
    }

    function renderPrioChart(data) {
        var el = document.getElementById('priorityChart');
        if (!el || data.length === 0) return;
        window._prioChart = new Chart(el, {
            type: 'doughnut',
            data: { labels: data.map(function(d) { return prioLabels[d.priority] || d.priority; }), datasets: [{ data: data.map(function(d) { return d.count; }), backgroundColor: data.map(function(d) { return prioColors[d.priority] || '#64748b'; }), borderWidth: 0, hoverOffset: 6 }] },
            options: Object.assign({}, chartDefaults, { cutout: '68%', plugins: { legend: { position: 'bottom', labels: { color: textColor, padding: 10, font: { size: 11 }, usePointStyle: true, pointStyleWidth: 8 } } } })
        });
    }

    function renderDairaChart(data) {
        var el = document.getElementById('dairaChart');
        if (!el || data.length === 0) return;
        window._dairaChart = new Chart(el, {
            type: 'bar',
            data: { labels: data.map(function(d) { return d.name; }), datasets: [{ data: data.map(function(d) { return d.count; }), backgroundColor: 'rgba(34,211,238,0.6)', borderRadius: 6, borderSkipped: false }] },
            options: Object.assign({}, chartDefaults, { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor } }, y: { ticks: { color: textColor, font: { size: 11 } }, grid: { display: false } } } })
        });
    }

    // Init original charts with drill-down capability
    if (allCatData.length > 0) renderCatChart(allCatData);
    if (allSubData.length > 0) renderSubChart(allSubData);
    if (allPrioData.length > 0) renderPrioChart(allPrioData);

    // DataTables on recent reports
    if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
        jQuery('#recentReportsTable').DataTable({
            pageLength: 10,
            order: [[7, 'desc']],
            language: { search: 'Rechercher :', lengthMenu: 'Afficher _MENU_', info: '_START_ à _END_ sur _TOTAL_', paginate: { previous: 'Préc', next: 'Suiv' }, zeroRecords: 'Aucun signalement' },
            dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [2, 5] }
            ]
        });
    }
});
</script>
