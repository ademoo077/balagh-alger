<?php $pageTitle = __('nav.my_reports'); ?>

<!-- ==================== HERO ==================== -->
<div class="citizen-hero-outer animate-fade-in-up">
    <div class="hero-particles">
        <?php for ($i = 0; $i < 12; $i++): ?>
        <div class="particle" style="
            left: <?= rand(5, 95) ?>%;
            width: <?= rand(4, 10) ?>px; height: <?= rand(4, 10) ?>px;
            animation-duration: <?= rand(8, 18) ?>s;
            animation-delay: <?= rand(0, 8) ?>s;
            bottom: -20px;
        "></div>
        <?php endfor; ?>
    </div>
    <div class="citizen-hero">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="hero-greeting">
                    <?= __('dashboard.greeting') ?>, <?= htmlspecialchars(\App\Helpers\Session::getUserName()) ?> 👋
                </div>
                <div class="hero-subtitle"><?= __('dashboard.hero_subtitle') ?></div>
                <a href="/reports/create" class="hero-cta">
                    <i class="fas fa-plus-circle"></i> <?= __('dashboard.new_report') ?>
                </a>
            </div>
            <div class="col-md-5 mt-4 mt-md-0 d-flex justify-content-md-end">
                <!-- Resolution Ring -->
                <?php $resolutionRate = $total > 0 ? round(($resolved / $total) * 100) : 0; ?>
                <div class="progress-ring" data-progress="<?= $resolutionRate ?>">
                    <svg width="64" height="64">
                        <circle class="ring-bg" cx="32" cy="32" r="28" fill="none" stroke-width="5"/>
                        <circle class="ring-fill" cx="32" cy="32" r="28" fill="none" stroke-width="5"
                            stroke="rgba(255,255,255,0.9)"
                            stroke-dasharray="175.93"
                            stroke-dashoffset="175.93"/>
                    </svg>
                    <div class="ring-text" style="color:white;"><?= $resolutionRate ?>%</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== KPI CARDS ==================== -->
<div class="citizen-kpi-grid">
    <div class="citizen-kpi animate-fade-in-up stagger-1">
        <div class="kpi-icon" style="background:var(--accent-surface);color:var(--accent);">
            <i class="fas fa-flag"></i>
        </div>
        <div class="kpi-number" style="color:var(--accent);" data-count-up="<?= $total ?>"><?= number_format($total) ?></div>
        <div class="kpi-label"><?= __('dashboard.total_reports') ?></div>
        <div class="kpi-bar" style="background:var(--accent);"></div>
    </div>
    <div class="citizen-kpi animate-fade-in-up stagger-2">
        <div class="kpi-icon" style="background:var(--amber-surface);color:var(--amber);">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="kpi-number" style="color:var(--amber);" data-count-up="<?= $pending ?>"><?= number_format($pending) ?></div>
        <div class="kpi-label"><?= __('dashboard.pending') ?></div>
        <div class="kpi-bar" style="background:var(--amber);"></div>
    </div>
    <div class="citizen-kpi animate-fade-in-up stagger-3">
        <div class="kpi-icon" style="background:rgba(99,102,241,0.08);color:var(--accent-light);">
            <i class="fas fa-spinner"></i>
        </div>
        <div class="kpi-number" style="color:var(--accent-light);" data-count-up="<?= $inProgress ?>"><?= number_format($inProgress) ?></div>
        <div class="kpi-label"><?= __('dashboard.in_progress') ?></div>
        <div class="kpi-bar" style="background:var(--accent-light);"></div>
    </div>
    <div class="citizen-kpi animate-fade-in-up stagger-4">
        <div class="kpi-icon" style="background:var(--green-surface);color:var(--green);">
            <i class="fas fa-check-double"></i>
        </div>
        <div class="kpi-number" style="color:var(--green);" data-count-up="<?= $resolved ?>"><?= number_format($resolved) ?></div>
        <div class="kpi-label"><?= __('dashboard.resolved') ?></div>
        <div class="kpi-bar" style="background:var(--green);"></div>
    </div>
    <?php if ($avgRating > 0): ?>
    <div class="citizen-kpi animate-fade-in-up stagger-4">
        <div class="kpi-icon" style="background:rgba(251,191,36,0.08);color:#f59e0b;">
            <i class="fas fa-star"></i>
        </div>
        <div class="kpi-number" style="color:#f59e0b;"><?= $avgRating ?></div>
        <div class="kpi-label"><?= __('dashboard.avg_rating') ?></div>
        <div class="kpi-bar" style="background:#f59e0b;"></div>
    </div>
    <?php endif; ?>
</div>

<?php if (empty($myReports)): ?>
<!-- ==================== EMPTY STATE ==================== -->
<div class="card border-0 shadow-sm animate-fade-in-up" style="border-radius:16px;">
    <div class="citizen-empty">
        <div class="empty-icon">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <h5 style="font-weight:700;margin-bottom:0.5rem;"><?= __('dashboard.no_reports_yet') ?></h5>
        <p class="text-muted mb-3" style="max-width:400px;margin:0 auto;"><?= __('dashboard.empty_description') ?></p>
        <a href="/reports/create" class="btn btn-primary btn-lg" style="border-radius:50px;padding:10px 28px;font-weight:700;">
            <i class="fas fa-plus me-1"></i> <?= __('dashboard.create_report') ?>
        </a>
    </div>
</div>

<?php else: ?>

<!-- ==================== MY REPORTS ==================== -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0" style="font-weight:800;">
        <i class="fas fa-list-check me-2 text-accent"></i> <?= __('nav.my_reports') ?>
    </h5>
    <a href="/reports/create" class="btn btn-sm btn-primary" style="border-radius:50px;">
        <i class="fas fa-plus me-1"></i> <?= __('common.new') ?>
    </a>
</div>

<?php foreach ($myReports as $ri => $r):
    $wf = $r['workflow_step'] ?? 0;
    $wfPercent = min(100, round(($wf / 8) * 100));
    $catColor = $r['category_color'] ?? '#6366f1';
    $statusColors = [
        'submitted'       => ['#f59e0b', 'fas fa-paper-plane'],
        'acknowledged'    => ['#0891b2', 'fas fa-check-double'],
        'assigned'        => ['#6366f1', 'fas fa-user-check'],
        'in_progress'     => ['#3b82f6', 'fas fa-hard-hat'],
        'pending_review'  => ['#d97706', 'fas fa-clipboard-check'],
        'pending_unite'   => ['#8b5cf6', 'fas fa-building'],
        'validated'       => ['#059669', 'fas fa-check-circle'],
        'resolved'        => ['#10b981', 'fas fa-check-circle'],
        'closed'          => ['#6b7280', 'fas fa-lock'],
        'rejected'        => ['#dc2626', 'fas fa-times-circle'],
    ];
    [$statusColor, $statusIcon] = $statusColors[$r['status']] ?? ['#6b7280', 'fas fa-question'];
?>
<a href="/reports/<?= $r['id'] ?>" class="citizen-report-item animate-fade-in-up stagger-<?= ($ri % 4) + 1 ?>" style="--accent-color:<?= $catColor ?>;">
    <div style="position:absolute;top:0;left:0;bottom:0;width:4px;border-radius:4px 0 0 4px;background:<?= $catColor ?>;"></div>

    <div class="ri-header">
        <div class="d-flex align-items-center gap-3">
            <div class="cat-icon-ring" style="background:<?= $catColor ?>12;color:<?= $catColor ?>;">
                <i class="<?= $r['category_icon'] ?? 'fas fa-exclamation-triangle' ?>"></i>
            </div>
            <div>
                <div class="ri-code" style="color:<?= $catColor ?>;"><?= $r['tracking_code'] ?></div>
                <div class="ri-title"><?= htmlspecialchars(mb_strimwidth($r['title'], 0, 70, '…')) ?></div>
            </div>
        </div>
        <div class="ri-right">
            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:6px;font-size:0.7rem;font-weight:700;background:<?= $statusColor ?>18;color:<?= $statusColor ?>;">
                <i class="<?= $statusIcon ?>" style="font-size:0.6rem;"></i>
                <?= \App\Helpers\Helper::getStatusBadge($r['status']) ?>
            </span>
            <small class="text-muted" style="font-size:0.7rem;"><?= \App\Helpers\Helper::timeAgo($r['created_at']) ?></small>
        </div>
    </div>

    <div class="ri-meta">
        <span><i class="fas fa-map-marker-alt" style="color:<?= $catColor ?>;"></i><?= $r['commune_name'] ?>, <?= $r['daira_name'] ?></span>
        <?php if ($r['org_name']): ?>
        <span><i class="fas fa-building" style="color:var(--text-muted);"></i><?= $r['org_name'] ?></span>
        <?php endif; ?>
    </div>

    <div class="ri-tags">
        <?= \App\Helpers\Helper::getPriorityBadge($r['priority']) ?>
        <?= \App\Helpers\DeadlineHelper::renderBadge($r['created_at'], (int)($r['deadline_days'] ?? 7), $r['status']) ?>
        <?php if ($r['assigned_first_name']): ?>
        <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;color:var(--text-muted);background:var(--bg-elevated);padding:2px 8px;border-radius:6px;">
            <i class="fas fa-user"></i><?= $r['assigned_first_name'] ?>
        </span>
        <?php endif; ?>
    </div>

    <div class="ri-workflow">
        <div class="wf-bar">
            <?php for ($i = 1; $i <= 8; $i++): ?>
                <div class="wf-seg" style="background:<?= $i < $wf ? $catColor : ($i == $wf ? 'var(--accent)' : 'var(--bg-elevated)') ?>;"></div>
            <?php endfor; ?>
        </div>
        <div class="ri-workflow-labels">
            <span><?= __('dashboard.reported') ?></span>
            <span><?= $wf >= 8 ? __('statuses.closed') : __('dashboard.in_progress') ?></span>
        </div>
    </div>
</a>
<?php endforeach; ?>

<!-- ==================== CHARTS ==================== -->
<div class="row g-3 mb-4">
    <?php if (!empty($byMonth)): ?>
    <div class="col-md-6">
        <div class="chart-card-citizen animate-fade-in-up">
            <div class="chart-header">
                <div style="width:28px;height:28px;border-radius:8px;background:var(--accent-surface);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-chart-line" style="color:var(--accent);font-size:0.75rem;"></i>
                </div>
                <h6><?= __('ui.chart_monthly') ?></h6>
            </div>
            <div class="chart-body">
                <canvas id="citizenMonthChart" height="180"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if (!empty($byCategory)): ?>
    <div class="col-md-6">
        <div class="chart-card-citizen animate-fade-in-up">
            <div class="chart-header">
                <div style="width:28px;height:28px;border-radius:8px;background:var(--cyan-surface);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-chart-pie" style="color:var(--cyan);font-size:0.75rem;"></i>
                </div>
                <h6><?= __('ui.chart_by_category') ?></h6>
            </div>
            <div class="chart-body">
                <canvas id="citizenCatChart" height="180"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (!empty($bySubcategory)): ?>
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="chart-card-citizen animate-fade-in-up">
            <div class="chart-header">
                <div style="width:28px;height:28px;border-radius:8px;background:rgba(124,58,237,0.08);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-layer-group" style="color:var(--purple);font-size:0.75rem;"></i>
                </div>
                <h6><?= __('dashboard.by_subcategory') ?></h6>
            </div>
            <div class="chart-body">
                <canvas id="citizenSubcatChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ==================== RECENT ACTIVITY ==================== -->
<?php if (!empty($recentHistory)): ?>
<div class="chart-card-citizen animate-fade-in-up mb-4">
    <div class="chart-header" style="padding-bottom:0;">
        <div style="width:28px;height:28px;border-radius:8px;background:var(--green-surface);display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-clock-rotate-left" style="color:var(--green);font-size:0.75rem;"></i>
        </div>
        <h6><?= __('dashboard.recent_activity') ?></h6>
    </div>
    <div style="padding:1rem 1.25rem 1.25rem;">
        <div class="citizen-timeline">
            <?php foreach (array_slice($recentHistory, 0, 8) as $hi => $h): ?>
            <div class="tl-item">
                <div class="tl-dot" style="background:<?= ['#059669','#6366f1','#d97706','#0891b2','#8b5cf6'][$hi % 5] ?>;color:white;">
                    <i class="fas fa-<?= ['check','flag','clock','arrow-right','bell'][$hi % 5] ?>"></i>
                </div>
                <div class="tl-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="ri-code" style="font-size:0.75rem;color:var(--accent);"><?= $h['tracking_code'] ?></span>
                            <div style="font-size:0.82rem;font-weight:600;color:var(--text-primary);margin-top:2px;">
                                <?= htmlspecialchars($h['action']) ?>
                            </div>
                            <?php if ($h['new_value']): ?>
                            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;max-width:350px;">
                                <?= htmlspecialchars(mb_substr($h['new_value'], 0, 100)) ?><?= mb_strlen($h['new_value']) > 100 ? '…' : '' ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="tl-time"><?= \App\Helpers\Helper::timeAgo($h['created_at']) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<!-- ==================== SCRIPTS ==================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var theme = document.documentElement.getAttribute('data-bs-theme');
    var textColor = theme === 'dark' ? '#94a3b8' : '#6b7280';
    var gridColor = theme === 'dark' ? 'rgba(255,255,255,0.04)' : 'rgba(0,0,0,0.06)';
    var font = { family: "'Inter', 'Noto Sans Arabic', sans-serif" };

    // Animate progress ring
    var ring = document.querySelector('.progress-ring');
    if (ring) {
        var progress = parseInt(ring.dataset.progress) || 0;
        var circumference = 2 * Math.PI * 28;
        var fill = ring.querySelector('.ring-fill');
        setTimeout(function() {
            fill.style.strokeDashoffset = circumference - (progress / 100) * circumference;
        }, 500);
    }

    // Animate KPI counters
    document.querySelectorAll('[data-count-up]').forEach(function(el) {
        var target = parseInt(el.dataset.countUp) || 0;
        if (target === 0) return;
        var duration = 1200;
        var start = 0;
        var startTime = null;
        function step(ts) {
            if (!startTime) startTime = ts;
            var p = Math.min((ts - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.floor(eased * target).toLocaleString();
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = target.toLocaleString();
        }
        var obs = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) { requestAnimationFrame(step); obs.disconnect(); }
        });
        obs.observe(el);
    });

    // Charts
    var chartDefaults = { responsive: true, maintainAspectRatio: false };

    var monthData = <?= json_encode($byMonth ?? []) ?>;
    if (monthData.length > 0 && document.getElementById('citizenMonthChart')) {
        new Chart(document.getElementById('citizenMonthChart'), {
            type: 'line',
            data: {
                labels: monthData.map(function(d) { var p = d.month.split('-'); return p[1] + '/' + p[0].slice(2); }),
                datasets: [{
                    label: '<?= __('dashboard.total_reports') ?>',
                    data: monthData.map(function(d) { return d.count; }),
                    borderColor: '#6366f1',
                    backgroundColor: function(ctx) {
                        var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 180);
                        g.addColorStop(0, 'rgba(99,102,241,0.2)');
                        g.addColorStop(1, 'rgba(99,102,241,0)');
                        return g;
                    },
                    fill: true, tension: 0.4, borderWidth: 2.5, pointRadius: 4,
                    pointBackgroundColor: '#6366f1', pointBorderColor: '#fff', pointBorderWidth: 2,
                    pointHoverRadius: 7, pointHoverBorderWidth: 3
                }]
            },
            options: Object.assign({}, chartDefaults, {
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: textColor, font: { size: 11 } }, grid: { display: false } },
                    y: { ticks: { color: textColor, font: { size: 11 }, stepSize: 1 }, grid: { color: gridColor }, beginAtZero: true }
                }
            })
        });
    }

    var catData = <?= json_encode($byCategory ?? []) ?>;
    if (catData.length > 0 && document.getElementById('citizenCatChart')) {
        new Chart(document.getElementById('citizenCatChart'), {
            type: 'doughnut',
            data: {
                labels: catData.map(function(d) { return d.name; }),
                datasets: [{ data: catData.map(function(d) { return d.count; }), backgroundColor: catData.map(function(d) { return d.color || '#6366f1'; }), borderWidth: 0, hoverOffset: 8, borderRadius: 4 }]
            },
            options: Object.assign({}, chartDefaults, {
                cutout: '72%',
                plugins: { legend: { position: 'bottom', labels: { color: textColor, padding: 10, font: { size: 11, family: font.family }, usePointStyle: true, pointStyleWidth: 8, boxHeight: 7 } } }
            })
        });
    }

    var subcatData = <?= json_encode($bySubcategory ?? []) ?>;
    if (subcatData.length > 0 && document.getElementById('citizenSubcatChart')) {
        new Chart(document.getElementById('citizenSubcatChart'), {
            type: 'bar',
            data: {
                labels: subcatData.map(function(d) { return d.subcat_name; }),
                datasets: [{ data: subcatData.map(function(d) { return d.count; }), backgroundColor: subcatData.map(function(d) { return d.color || '#6366f1'; }), borderRadius: 8, borderSkipped: false, barThickness: 18 }]
            },
            options: Object.assign({}, chartDefaults, {
                indexAxis: 'y',
                plugins: { legend: { display: false }, tooltip: { callbacks: { afterLabel: function(ctx) { return subcatData[ctx.dataIndex].cat_name; } } } },
                scales: { x: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor }, beginAtZero: true }, y: { ticks: { color: textColor, font: { size: 11 } }, grid: { display: false } } }
            })
        });
    }
});
</script>
