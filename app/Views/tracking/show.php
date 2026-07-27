<?php
\App\Helpers\I18n::init();
$lang = \App\Helpers\I18n::getLang();
$isRtl = \App\Helpers\I18n::isRtl();
$dir = \App\Helpers\I18n::getDir();

$statusLabels = [
    'submitted' => [__('statuses.submitted'), 'info', 'fas fa-paper-plane'],
    'acknowledged' => [__('statuses.acknowledged'), 'primary', 'fas fa-check-double'],
    'assigned' => [__('statuses.assigned'), 'warning', 'fas fa-user-check'],
    'in_progress' => [__('statuses.in_progress'), 'primary', 'fas fa-hard-hat'],
    'pending_review' => [__('statuses.pending_review'), 'secondary', 'fas fa-clipboard-check'],
    'pending_unite' => [__('statuses.pending_unite'), 'secondary', 'fas fa-building'],
    'validated' => [__('statuses.validated'), 'success', 'fas fa-check-circle'],
    'resolved' => [__('statuses.resolved'), 'success', 'fas fa-check-circle'],
    'closed' => [__('statuses.closed'), 'dark', 'fas fa-lock'],
    'rejected' => [__('statuses.rejected'), 'danger', 'fas fa-times-circle'],
];
$sInfo = $statusLabels[$report['status']] ?? [__('statuses.unknown'), 'secondary', 'fas fa-question'];

$wf = $report['workflow_step'] ?? 0;
$wfPercent = min(100, round(($wf / 8) * 100));

$deadlineStatus = \App\Helpers\DeadlineHelper::getStatus($report['created_at'], $catDays, $report['status']);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($report['tracking_code']) ?> — <?= __('app.name') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <style>
        :root, [data-bs-theme="light"] {
            --bg: #f8f9fc; --surface: #ffffff; --card-bg: #ffffff;
            --card-border: #e2e5ef; --text: #111827; --text-secondary: #4b5563; --text-muted: #9ca3af;
            --accent: #6366f1; --accent-light: #818cf8; --accent-surface: rgba(99,102,241,0.06);
            --cyan: #0891b2; --green: #059669; --amber: #d97706; --red: #dc2626;
            --shadow-sm: 0 1px 4px rgba(0,0,0,0.06); --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
        }
        [data-bs-theme="dark"] {
            --bg: #0a0e1a; --surface: #151c2c; --card-bg: rgba(21,28,44,0.9);
            --card-border: rgba(255,255,255,0.06); --text: #e2e8f0; --text-secondary: #94a3b8; --text-muted: #64748b;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.15); --shadow-md: 0 4px 12px rgba(0,0,0,0.2);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--bg); font-family: 'Inter', system-ui, sans-serif; color: var(--text);
            min-height: 100vh; padding-bottom: 40px;
        }
        body::before {
            content: ''; position: fixed; top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(99,102,241,0.04) 0%, transparent 50%);
            pointer-events: none; z-index: 0;
        }
        .container { max-width: 800px; position: relative; z-index: 1; padding: 16px; }

        .top-bar {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;
        }
        .top-bar .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text); }
        .top-bar .brand-icon {
            width: 36px; height: 36px; background: linear-gradient(135deg, var(--accent), #4f46e5);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            color: white; font-size: 0.85rem;
        }
        .top-bar .brand-name { font-weight: 800; font-size: 1rem; }
        .top-bar .actions { display: flex; gap: 8px; }
        .top-bar .actions button {
            width: 34px; height: 34px; border-radius: 8px;
            border: 1px solid var(--card-border); background: var(--card-bg);
            color: var(--text-muted); display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s ease; font-size: 0.82rem;
        }
        .top-bar .actions button:hover { border-color: var(--accent); color: var(--accent); }
        [data-bs-theme="dark"] .icon-sun-trk { display: none; }
        [data-bs-theme="light"] .icon-moon-trk { display: none; }

        .report-banner {
            background: linear-gradient(135deg, #D2122E 0%, #A30E24 50%, #8B0A1C 100%);
            border-radius: 16px; overflow: hidden; margin-bottom: 20px;
            box-shadow: 0 12px 40px rgba(210,18,46,0.3); position: relative;
        }
        .report-banner::after {
            content: ''; position: absolute; top: 0; right: 0;
            width: 280px; height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.07), transparent 70%);
            pointer-events: none;
        }
        .banner-stripe { height: 4px; background: linear-gradient(90deg, #006233 0%, #006233 33%, #fff 33%, #fff 66%, #D2122E 66%, #D2122E 100%); }
        .banner-body { padding: 20px; position: relative; z-index: 1; }
        .banner-label { font-size: 9px; font-family: 'JetBrains Mono', monospace; font-weight: 800; color: rgba(255,255,255,0.7); letter-spacing: 0.1em; text-transform: uppercase; }
        .banner-sub { font-size: 10px; color: rgba(255,255,255,0.55); font-weight: 600; }
        .banner-title { color: #fff; font-weight: 800; font-size: 1.15rem; margin: 8px 0; line-height: 1.3; }
        .banner-codes { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .code-badge { background: rgba(255,255,255,0.18); color: #fff; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 800; font-family: 'JetBrains Mono', monospace; }
        .banner-footer {
            background: rgba(0,0,0,0.25); backdrop-filter: blur(8px);
            padding: 8px 20px; display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 6px;
        }
        .banner-footer .info { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .banner-footer .info-item { display: flex; align-items: center; gap: 5px; color: rgba(255,255,255,0.85); font-size: 11px; font-weight: 700; }
        .banner-footer .info-item i { font-size: 11px; }

        .card-box {
            background: var(--card-bg); border: 1px solid var(--card-border);
            border-radius: 14px; overflow: hidden; margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
        }
        .card-header-bar {
            padding: 12px 16px; border-bottom: 1px solid var(--card-border);
            display: flex; align-items: center; gap: 8px;
            font-weight: 700; font-size: 0.88rem;
        }
        .card-header-bar i { color: var(--accent); font-size: 0.85rem; }
        .card-body-content { padding: 16px; }

        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .detail-item { display: flex; align-items: flex-start; gap: 10px; }
        .detail-item i { color: var(--text-muted); width: 16px; font-size: 0.78rem; margin-top: 3px; }
        .detail-item .label { font-size: 0.68rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .detail-item .value { font-size: 0.85rem; font-weight: 600; color: var(--text); }

        /* Workflow Progress */
        .workflow-section { margin-bottom: 16px; }
        .workflow-steps {
            display: flex; align-items: center; gap: 0; margin: 12px 0 8px; position: relative;
        }
        .workflow-steps .step {
            flex: 1; text-align: center; position: relative; z-index: 1;
        }
        .workflow-steps .step .dot {
            width: 28px; height: 28px; border-radius: 50%; margin: 0 auto 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem; font-weight: 700;
            background: var(--card-border); color: var(--text-muted);
            border: 2px solid var(--bg); transition: all 0.3s;
        }
        .workflow-steps .step.done .dot { background: var(--green); color: #fff; }
        .workflow-steps .step.active .dot {
            background: var(--accent); color: #fff;
            box-shadow: 0 0 0 4px rgba(99,102,241,0.2);
            animation: stepPulse 2s infinite;
        }
        .workflow-steps .step .step-label {
            font-size: 0.6rem; color: var(--text-muted); font-weight: 600;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .workflow-steps .step.done .step-label { color: var(--green); }
        .workflow-steps .step.active .step-label { color: var(--accent); }
        .workflow-bar-container {
            height: 4px; background: var(--card-border); border-radius: 2px;
            overflow: hidden; margin-top: 4px;
        }
        .workflow-bar-fill {
            height: 100%; border-radius: 2px; transition: width 0.5s ease;
        }
        @keyframes stepPulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(99,102,241,0.2); }
            50% { box-shadow: 0 0 0 8px rgba(99,102,241,0.1); }
        }

        /* Deadline */
        .deadline-bar {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px;
            border-radius: 10px; margin: 0 16px 16px;
        }
        .deadline-bar.success { background: rgba(5,150,105,0.08); border: 1px solid rgba(5,150,105,0.2); }
        .deadline-bar.warning { background: rgba(217,119,6,0.08); border: 1px solid rgba(217,119,6,0.2); }
        .deadline-bar.danger { background: rgba(220,38,38,0.08); border: 1px solid rgba(220,38,38,0.2); }
        .deadline-bar i { font-size: 1rem; }
        .deadline-bar.success i { color: var(--green); }
        .deadline-bar.warning i { color: var(--amber); }
        .deadline-bar.danger i { color: var(--red); }
        .deadline-bar .deadline-text { font-size: 0.82rem; font-weight: 600; }

        /* Timeline */
        .timeline { padding: 0; }
        .timeline-item {
            display: flex; gap: 14px; padding: 10px 0;
            border-bottom: 1px solid var(--card-border);
        }
        .timeline-item:last-child { border-bottom: none; }
        .timeline-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: var(--accent); margin-top: 5px; flex-shrink: 0;
        }
        .timeline-dot.green { background: var(--green); }
        .timeline-dot.amber { background: var(--amber); }
        .timeline-dot.red { background: var(--red); }
        .timeline-content .tl-title { font-size: 0.82rem; font-weight: 600; }
        .timeline-content .tl-meta { font-size: 0.72rem; color: var(--text-muted); margin-top: 2px; }
        .timeline-content .tl-detail { font-size: 0.78rem; color: var(--text-secondary); margin-top: 4px; }

        .empty-state { text-align: center; padding: 24px; color: var(--text-muted); }
        .empty-state i { font-size: 2rem; margin-bottom: 8px; display: block; opacity: 0.3; }
        .empty-state p { font-size: 0.82rem; }

        @keyframes fadeInUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .animate-in { animation: fadeInUp 0.4s ease-out both; }

        @media (max-width: 576px) {
            .detail-grid { grid-template-columns: 1fr; }
            .workflow-steps .step .step-label { font-size: 0.5rem; }
            .banner-body { padding: 14px; }
        }
    </style>
    <script>
    window.__translations = <?= file_get_contents(__DIR__ . '/../../../lang/' . $lang . '.json') ?>;
    window.__lang = '<?= $lang ?>';
    </script>
</head>
<body>
    <div class="container">
        <!-- Top Bar -->
        <div class="top-bar animate-in">
            <a href="/suivi" class="brand">
                <div class="brand-icon"><i class="fas fa-bullhorn"></i></div>
                <span class="brand-name"><?= __('app.name') ?></span>
            </a>
            <div class="actions">
                <button onclick="window.print()" title="<?= __('common.print') ?>"><i class="fas fa-print"></i></button>
                <button onclick="var h=document.documentElement,c=h.getAttribute('data-bs-theme'),n=c==='dark'?'light':'dark';h.setAttribute('data-bs-theme',n);localStorage.setItem('balagh-theme',n);">
                    <i class="fas fa-sun icon-sun-trk" style="font-size:0.82rem;"></i>
                    <i class="fas fa-moon icon-moon-trk" style="font-size:0.82rem;"></i>
                </button>
            </div>
        </div>

        <!-- Report Banner -->
        <div class="report-banner animate-in" style="animation-delay:0.05s;">
            <div class="banner-stripe"></div>
            <div class="banner-body">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div style="width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-shield-halved" style="color:#fff;font-size:14px;"></i>
                    </div>
                    <div>
                        <div class="banner-label"><?= __('app.wilaya') ?></div>
                        <div class="banner-sub"><?= __('tracking.public_view') ?></div>
                    </div>
                </div>
                <h4 class="banner-title"><?= htmlspecialchars($report['title']) ?></h4>
                <div class="banner-codes">
                    <span class="code-badge"><?= htmlspecialchars($report['tracking_code']) ?></span>
                    <span class="badge bg-<?= $sInfo[1] ?>-subtle text-<?= $sInfo[1] ?>" style="font-size:0.72rem;">
                        <i class="<?= $sInfo[2] ?> me-1"></i><?= $sInfo[0] ?>
                    </span>
                    <?= \App\Helpers\Helper::getPriorityBadge($report['priority']) ?>
                </div>
            </div>
            <div class="banner-footer">
                <div class="info">
                    <div class="info-item"><i class="fas fa-calendar-check" style="color:#059669;"></i><span><?= date('d/m/Y', strtotime($report['created_at'])) ?></span></div>
                    <div class="info-item"><i class="fas fa-clock" style="color:#d97706;"></i><span><?= date('H:i', strtotime($report['created_at'])) ?></span></div>
                </div>
            </div>
        </div>

        <!-- Deadline Status -->
        <?php if ($deadlineStatus['days_left'] !== null): ?>
        <div class="deadline-bar <?= $deadlineStatus['class'] ?> animate-in" style="animation-delay:0.1s;">
            <i class="fas <?= $deadlineStatus['is_late'] ? 'fa-exclamation-triangle' : ($deadlineStatus['class'] === 'warning' ? 'fa-clock' : 'fa-check-circle') ?>"></i>
            <div class="deadline-text"><?= $deadlineStatus['label'] ?></div>
        </div>
        <?php endif; ?>

        <!-- Workflow Progress -->
        <div class="card-box animate-in" style="animation-delay:0.12s;">
            <div class="card-header-bar">
                <i class="fas fa-route"></i> <?= __('tracking.workflow_progress') ?>
                <span class="ms-auto" style="font-size:0.72rem;color:var(--text-muted);"><?= $wf ?>/8 — <?= $wfPercent ?>%</span>
            </div>
            <div class="card-body-content">
                <div class="workflow-steps">
                    <?php
                    $steps = [
                        [__('interventions.workflow_created'), 'fas fa-paper-plane'],
                        [__('interventions.workflow_received'), 'fas fa-check-double'],
                        [__('interventions.workflow_chef_u'), 'fas fa-user-check'],
                        [__('interventions.workflow_chef_s'), 'fas fa-hard-hat'],
                        [__('interventions.workflow_agent'), 'fas fa-wrench'],
                        [__('interventions.workflow_verified'), 'fas fa-clipboard-check'],
                        [__('interventions.workflow_validated_u'), 'fas fa-building'],
                        [__('interventions.workflow_approved'), 'fas fa-check-circle'],
                    ];
                    foreach ($steps as $i => $step):
                        $num = $i + 1;
                        $cls = $num < $wf ? 'done' : ($num == $wf ? 'active' : '');
                    ?>
                    <div class="step <?= $cls ?>">
                        <div class="dot"><i class="<?= $step[1] ?>" style="font-size:0.6rem;"></i></div>
                        <div class="step-label"><?= $step[0] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="workflow-bar-container">
                    <div class="workflow-bar-fill" style="width:<?= $wfPercent ?>%;background:<?= $wf >= 8 ? 'var(--green)' : 'var(--accent)' ?>;"></div>
                </div>
            </div>
        </div>

        <!-- Report Details -->
        <div class="card-box animate-in" style="animation-delay:0.15s;">
            <div class="card-header-bar">
                <i class="fas fa-info-circle"></i> <?= __('reports.details') ?>
            </div>
            <div class="card-body-content">
                <p style="font-size:0.88rem;line-height:1.6;margin-bottom:16px;color:var(--text-secondary);"><?= nl2br(htmlspecialchars($report['description'])) ?></p>
                <div class="detail-grid">
                    <div class="detail-item">
                        <i class="fas fa-tag"></i>
                        <div><div class="label"><?= __('common.category') ?></div><div class="value"><?= htmlspecialchars($report['category_name'] ?? '-') ?></div></div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-map"></i>
                        <div><div class="label"><?= __('common.daira') ?></div><div class="value"><?= htmlspecialchars($report['daira_name'] ?? '-') ?></div></div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-city"></i>
                        <div><div class="label"><?= __('common.commune') ?></div><div class="value"><?= htmlspecialchars($report['commune_name'] ?? '-') ?></div></div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-location-dot"></i>
                        <div><div class="label"><?= __('common.address') ?></div><div class="value"><?= htmlspecialchars($report['address'] ?? '-') ?></div></div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-building"></i>
                        <div><div class="label"><?= __('common.organization') ?></div><div class="value"><?= htmlspecialchars($report['org_name'] ?? __('reports.not_assigned')) ?></div></div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-clock"></i>
                        <div><div class="label"><?= __('tracking.deadline') ?></div><div class="value"><?= date('d/m/Y', strtotime(\App\Helpers\DeadlineHelper::getDeadline($report['created_at'], $catDays))) ?></div></div>
                    </div>
                </div>

                <?php if ($report['latitude'] && $report['longitude']): ?>
                <div class="mt-3">
                    <div id="trackingMap" style="height:180px;border-radius:10px;overflow:hidden;"></div>
                    <div class="d-flex justify-content-between mt-1">
                        <small style="color:var(--text-muted);font-size:0.72rem;font-family:'JetBrains Mono',monospace;"><?= $report['latitude'] ?>, <?= $report['longitude'] ?></small>
                        <a href="https://www.google.com/maps?q=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>" target="_blank" style="color:var(--accent);font-size:0.78rem;text-decoration:none;font-weight:600;"><?= __('reports.google_maps') ?> <i class="fas fa-external-link-alt" style="font-size:0.65rem;"></i></a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Interventions -->
        <?php if (!empty($interventions)): ?>
        <div class="card-box animate-in" style="animation-delay:0.18s;">
            <div class="card-header-bar">
                <i class="fas fa-tools"></i> <?= __('interventions.title') ?>
                <span class="badge bg-primary ms-2" style="font-size:0.68rem;"><?= count($interventions) ?></span>
            </div>
            <div class="card-body-content">
                <?php foreach ($interventions as $iv): ?>
                <div style="padding:10px 0;border-bottom:1px solid var(--card-border);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-size:0.82rem;font-weight:600;">
                                <?= htmlspecialchars(($iv['first_name'] ?? '') . ' ' . ($iv['last_name'] ?? '')) ?>
                            </div>
                            <div style="font-size:0.72rem;color:var(--text-muted);"><?= \App\Helpers\Helper::timeAgo($iv['created_at']) ?></div>
                        </div>
                        <span class="badge bg-<?= $iv['status'] === 'completed' ? 'success' : ($iv['status'] === 'in_progress' ? 'primary' : 'secondary') ?>" style="font-size:0.65rem;">
                            <?= ucfirst(str_replace('_', ' ', $iv['status'] ?? '')) ?>
                        </span>
                    </div>
                    <?php if (!empty($iv['notes'])): ?>
                    <div style="font-size:0.78rem;color:var(--text-secondary);margin-top:4px;"><?= htmlspecialchars($iv['notes']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Timeline -->
        <div class="card-box animate-in" style="animation-delay:0.2s;">
            <div class="card-header-bar">
                <i class="fas fa-clock-rotate-left"></i> <?= __('interventions.timeline') ?>
                <span class="badge bg-secondary ms-2" style="font-size:0.68rem;"><?= count($history) ?></span>
            </div>
            <div class="card-body-content">
                <?php if (empty($history)): ?>
                <div class="empty-state">
                    <i class="fas fa-clock"></i>
                    <p><?= __('reports.no_history') ?></p>
                </div>
                <?php else: ?>
                <div class="timeline">
                    <?php foreach ($history as $h): ?>
                    <?php
                        $dotClass = '';
                        if (in_array($h['action'] ?? '', ['status_change'])) {
                            $newVal = $h['new_value'] ?? '';
                            if (in_array($newVal, ['resolved', 'validated'])) $dotClass = 'green';
                            elseif ($newVal === 'rejected') $dotClass = 'red';
                            elseif (in_array($newVal, ['in_progress', 'assigned'])) $dotClass = 'amber';
                        }
                    ?>
                    <div class="timeline-item">
                        <div class="timeline-dot <?= $dotClass ?>"></div>
                        <div class="timeline-content">
                            <div class="tl-title"><?= htmlspecialchars($h['action'] ?? '') ?></div>
                            <div class="tl-meta"><?= \App\Helpers\Helper::timeAgo($h['created_at']) ?></div>
                            <?php if (!empty($h['new_value'])): ?>
                            <div class="tl-detail"><?= htmlspecialchars(mb_substr($h['new_value'], 0, 150)) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 animate-in" style="animation-delay:0.25s;">
            <a href="/suivi" style="color:var(--accent-light);text-decoration:none;font-size:0.82rem;font-weight:600;">
                <i class="fas fa-arrow-left me-1"></i> <?= __('tracking.new_search') ?>
            </a>
            <div style="margin-top:8px;color:var(--text-muted);font-size:0.72rem;">
                <i class="fas fa-shield-alt me-1"></i> <?= __('app.wilaya') ?> &copy; <?= date('Y') ?>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($report['latitude'] && $report['longitude']): ?>
        var theme = document.documentElement.getAttribute('data-bs-theme');
        var tileUrl = theme === 'dark' ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
        var map = L.map('trackingMap').setView([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>], 15);
        L.tileLayer(tileUrl, { attribution: '&copy; OSM CARTO' }).addTo(map);
        L.marker([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>]).addTo(map).bindPopup('<?= htmlspecialchars($report['tracking_code']) ?>').openPopup();
        <?php endif; ?>
    });
    (function() {
        var saved = localStorage.getItem('balagh-theme');
        if (saved) document.documentElement.setAttribute('data-bs-theme', saved);
    })();
    </script>
</body>
</html>
