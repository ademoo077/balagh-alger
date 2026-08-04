<?php $pageTitle = __('dashboard.agent_missions'); ?>

<!-- Hero -->
<div class="agent-hero animate-fade-in-up">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h3 style="font-weight:800;"><?= __('dashboard.my_missions') ?></h3>
            <p class="mb-2" style="opacity:0.85;"><?= __('dashboard.agent_welcome', ['name' => \App\Helpers\Session::get('first_name', '')]) ?></p>
            <div class="d-flex gap-2 flex-wrap">
                <span class="stat-pill"><i class="fas fa-clipboard-list me-1"></i> <?= $total ?> <?= __('dashboard.total_missions') ?></span>
                <span class="stat-pill"><i class="fas fa-spinner me-1"></i> <?= $inProgress ?> <?= __('dashboard.in_progress') ?></span>
                <span class="stat-pill"><i class="fas fa-check-circle me-1"></i> <?= $completed ?> <?= __('dashboard.resolved') ?></span>
                <span class="stat-pill"><i class="fas fa-hourglass-half me-1"></i> <?= $pendingReview ?> <?= __('dashboard.pending_review') ?></span>
            </div>
        </div>
        <div class="text-end">
            <div class="text-white-50" style="font-size:0.75rem;"><i class="fas fa-map-marker-alt me-1"></i><?= \App\Helpers\Session::get('daira_name', '') ?></div>
            <div id="live-clock" class="font-mono" style="font-size:1.2rem;font-weight:700;color:rgba(255,255,255,0.9);font-variant-numeric:tabular-nums;"></div>
        </div>
    </div>
</div>

<?php if (empty($missions)): ?>
<div class="card animate-fade-in-up">
    <div class="card-body text-center py-5">
        <div class="empty-state py-3">
            <i class="fas fa-clipboard-check d-block mb-2" style="font-size:2.5rem;color:var(--accent);"></i>
            <p class="text-muted mb-0"><?= __('dashboard.no_missions') ?></p>
        </div>
    </div>
</div>
<?php else: ?>

<!-- Mission Cards -->
<div class="row g-3">
<?php foreach ($missions as $i => $m):
    $workflowStep = (int)($m['workflow_step'] ?? 0);
    $deadlineAt = $m['deadline_at'] ?? null;
    $deadlineDays = (int)($m['deadline_days'] ?? 7);
    $created = $m['created_at'];
    $isOverdue = $deadlineAt && strtotime($deadlineAt) < time() && !in_array($m['status'], ['resolved','validated','closed']);
    $statusClass = match($m['status']) {
        'in_progress' => 'cyan',
        'resolved','validated' => 'green',
        'pending_review' => 'amber',
        'closed' => 'secondary',
        default => 'accent',
    };
?>
<div class="col-lg-6 col-xl-4">
    <div class="card mission-card animate-fade-in-up stagger-<?= ($i % 4) + 1 ?>">
        <!-- Photos -->
        <?php if ($m['before_photo'] || $m['after_photo']): ?>
        <div class="mission-photos">
            <div class="mission-photo">
                <?php if ($m['before_photo']): ?>
                    <img src="/uploads/reports/<?= htmlspecialchars($m['before_photo']) ?>" alt="Avant" loading="lazy">
                    <span class="photo-label"><?= __('interventions.before') ?></span>
                <?php else: ?>
                    <div class="photo-placeholder"><i class="fas fa-camera"></i></div>
                    <span class="photo-label"><?= __('interventions.before') ?></span>
                <?php endif; ?>
            </div>
            <div class="mission-photo">
                <?php if ($m['after_photo']): ?>
                    <img src="/uploads/interventions/<?= $m['id'] ?>/<?= htmlspecialchars($m['after_photo']) ?>" alt="Après" loading="lazy">
                    <span class="photo-label"><?= __('interventions.after') ?></span>
                <?php else: ?>
                    <div class="photo-placeholder"><i class="fas fa-camera"></i></div>
                    <span class="photo-label"><?= __('interventions.after') ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card-body">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <a href="/reports/<?= $m['id'] ?>" class="text-decoration-none" style="font-weight:700;font-size:0.95rem;color:var(--text);">
                        <?= $m['tracking_code'] ?>
                    </a>
                    <div style="font-size:0.85rem;color:var(--text);line-height:1.4;margin-top:2px;">
                        <?= \App\Helpers\Helper::sanitize(mb_substr($m['title'], 0, 60)) ?><?= mb_strlen($m['title']) > 60 ? '…' : '' ?>
                    </div>
                </div>
                <?= \App\Helpers\Helper::getStatusBadge($m['status']) ?>
            </div>

            <!-- Category & Priority -->
            <div class="d-flex gap-2 flex-wrap mb-2">
                <span class="badge rounded-pill" style="background:<?= ($m['category_color'] ?? '#6366f1') ?>18;color:<?= $m['category_color'] ?? '#6366f1' ?>;font-size:0.75rem;">
                    <i class="<?= $m['category_icon'] ?? 'fas fa-tag' ?> me-1"></i><?= $m['category_name'] ?>
                </span>
                <?= \App\Helpers\Helper::getPriorityBadge($m['priority']) ?>
            </div>

            <!-- Location -->
            <div class="d-flex gap-2 text-muted mb-2" style="font-size:0.8rem;">
                <span><i class="fas fa-map-pin me-1"></i><?= $m['commune_name'] ?>, <?= $m['daira_name'] ?></span>
            </div>

            <!-- Deadline -->
            <div class="d-flex align-items-center gap-2 mb-2" style="font-size:0.8rem;">
                <?= \App\Helpers\DeadlineHelper::renderBadge($m['created_at'], $deadlineDays, $m['status']) ?>
                <?php if ($isOverdue): ?>
                    <span class="badge bg-danger rounded-pill"><i class="fas fa-exclamation-triangle me-1"></i><?= __('dashboard.overdue') ?></span>
                <?php endif; ?>
            </div>

            <!-- Workflow Progress -->
            <div class="workflow-mini">
                <div class="workflow-step <?= $workflowStep >= 0 ? 'done' : '' ?>"><span>1</span></div>
                <div class="workflow-line <?= $workflowStep >= 1 ? 'done' : '' ?>"></div>
                <div class="workflow-step <?= $workflowStep >= 1 ? 'done' : '' ?> <?= $workflowStep === 2 ? 'active' : '' ?>"><span>2</span></div>
                <div class="workflow-line <?= $workflowStep >= 2 ? 'done' : '' ?>"></div>
                <div class="workflow-step <?= $workflowStep >= 2 ? 'done' : '' ?> <?= $workflowStep === 3 ? 'active' : '' ?>"><span>3</span></div>
                <div class="workflow-line <?= $workflowStep >= 3 ? 'done' : '' ?>"></div>
                <div class="workflow-step <?= $workflowStep >= 4 ? 'done' : '' ?> <?= $workflowStep === 4 ? 'active' : '' ?>"><span>4</span></div>
            </div>

            <!-- Actions -->
            <div class="d-flex gap-2 mt-3">
                <a href="/reports/<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
                    <i class="fas fa-eye me-1"></i><?= __('common.view') ?>
                </a>
                <?php if ($m['status'] === 'in_progress'): ?>
                <a href="/reports/<?= $m['id'] ?>?action=complete" class="btn btn-sm btn-success flex-fill" onclick="return confirm('<?= __('dashboard.confirm_complete') ?>')">
                    <i class="fas fa-check me-1"></i><?= __('dashboard.complete') ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- Timeline -->
<div class="card mt-4 animate-fade-in-up">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-history me-2 text-accent"></i><?= __('dashboard.recent_activity') ?></h6>
    </div>
    <div class="card-body">
        <div class="timeline-list">
        <?php foreach (array_slice($missions, 0, 10) as $m): ?>
            <div class="timeline-item">
                <div class="timeline-icon" style="background:<?= match($m['status']) {
                    'in_progress' => 'var(--cyan-surface);color:var(--cyan)',
                    'resolved','validated' => 'var(--green-surface);color:var(--green)',
                    'pending_review' => 'var(--amber-surface);color:var(--amber)',
                    default => 'var(--accent-surface);color:var(--accent)',
                } ?>">
                    <i class="<?= match($m['status']) {
                        'in_progress' => 'fas fa-spinner',
                        'resolved','validated' => 'fas fa-check',
                        'pending_review' => 'fas fa-hourglass-half',
                        default => 'fas fa-clipboard',
                    } ?>"></i>
                </div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between">
                        <a href="/reports/<?= $m['id'] ?>" class="fw-bold text-decoration-none" style="color:var(--text);"><?= $m['tracking_code'] ?></a>
                        <small class="text-muted"><?= \App\Helpers\Helper::timeAgo($m['updated_at'] ?? $m['created_at']) ?></small>
                    </div>
                    <div style="font-size:0.85rem;color:var(--text);"><?= \App\Helpers\Helper::sanitize(mb_substr($m['title'], 0, 80)) ?></div>
                    <div class="d-flex gap-2 mt-1">
                        <?= \App\Helpers\Helper::getStatusBadge($m['status']) ?>
                        <?= \App\Helpers\Helper::getPriorityBadge($m['priority']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    .agent-hero {
        background: linear-gradient(135deg, #059669 0%, #10b981 50%, #06b6d4 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .agent-hero::after {
        content: '';
        position: absolute;
        top: -30%; right: -10%;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .stat-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.18); backdrop-filter: blur(4px);
        border-radius: 50px; padding: 6px 14px; font-size: 0.85rem; font-weight: 600;
        margin: 4px 4px 4px 0;
    }
    .mission-card {
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .mission-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .mission-photos {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2px;
        height: 140px;
    }
    .mission-photo {
        position: relative;
        overflow: hidden;
        background: var(--card-bg-alt, #f8fafc);
    }
    .mission-photo img {
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .mission-photo .photo-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted, #94a3b8);
        font-size: 1.5rem;
    }
    .mission-photo .photo-label {
        position: absolute;
        bottom: 4px; left: 4px;
        background: rgba(0,0,0,0.6);
        color: white;
        font-size: 0.65rem;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
    }
    .workflow-mini {
        display: flex;
        align-items: center;
        gap: 0;
        margin-top: 8px;
    }
    .workflow-mini .workflow-step {
        width: 20px; height: 20px;
        border-radius: 50%;
        background: var(--card-border, #e2e8f0);
        display: flex; align-items: center; justify-content: center;
        font-size: 0.6rem; font-weight: 700;
        color: var(--text-muted, #94a3b8);
        flex-shrink: 0;
    }
    .workflow-mini .workflow-step.done {
        background: #059669;
        color: white;
    }
    .workflow-mini .workflow-step.active {
        background: var(--accent);
        color: white;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
    }
    .workflow-mini .workflow-line {
        flex: 1;
        height: 2px;
        background: var(--card-border, #e2e8f0);
    }
    .workflow-mini .workflow-line.done {
        background: #059669;
    }
    .timeline-list { position: relative; padding-left: 36px; }
    .timeline-list .timeline-item {
        position: relative;
        padding-bottom: 16px;
    }
    .timeline-list .timeline-item:last-child { padding-bottom: 0; }
    .timeline-list .timeline-icon {
        position: absolute;
        left: -36px; top: 2px;
        width: 28px; height: 28px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.7rem;
    }
    .timeline-list .timeline-content {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e2e8f0);
        border-radius: 8px;
        padding: 10px 14px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var clockEl = document.getElementById('live-clock');
    if (clockEl) {
        function tick() {
            var isRtl = document.documentElement.getAttribute('dir') === 'rtl';
            clockEl.textContent = new Date().toLocaleTimeString(isRtl ? 'ar-DZ' : 'fr-DZ', { hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Africa/Algiers' });
        }
        tick();
        setInterval(tick, 1000);
    }
});
</script>
