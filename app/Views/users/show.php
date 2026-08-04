<?php $pageTitle = $user['first_name'] . ' ' . $user['last_name']; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.usr-show-hero{position:relative;border-radius:0 0 32px 32px;padding:50px 0 35px;margin:-1.5rem -15px 2rem;text-align:center;overflow:hidden}
.usr-show-hero.role-admin_central{background:linear-gradient(135deg,#ef4444 0%,#dc2626 50%,#b91c1c 100%)}
.usr-show-hero.role-resp_central,.usr-show-hero.role-admin_local{background:linear-gradient(135deg,#f59e0b 0%,#f97316 50%,#ea580c 100%)}
.usr-show-hero.role-chef_unite{background:linear-gradient(135deg,#8b5cf6 0%,#7c3aed 50%,#6d28d9 100%)}
.usr-show-hero.role-chef_section{background:linear-gradient(135deg,#667eea 0%,#764ba2 50%,#5b21b6 100%)}
.usr-show-hero.role-intervenant{background:linear-gradient(135deg,#22c55e 0%,#16a34a 50%,#15803d 100%)}
.usr-show-hero.role-citizen{background:linear-gradient(135deg,#64748b 0%,#475569 50%,#334155 100%)}
.usr-show-hero::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle at 30% 50%,rgba(255,255,255,0.08) 0%,transparent 50%);animation:usrShowShine 10s ease-in-out infinite}
@keyframes usrShowShine{0%,100%{transform:translateX(-20%) rotate(0deg)}50%{transform:translateX(20%) rotate(3deg)}}
.usr-show-particles{position:absolute;inset:0;overflow:hidden;pointer-events:none}
.usr-show-particle{position:absolute;width:4px;height:4px;background:rgba(255,255,255,0.25);border-radius:50%;animation:usrShowParticle 7s ease-in-out infinite}
.usr-show-particle:nth-child(1){left:8%;top:25%;animation-delay:0s}
.usr-show-particle:nth-child(2){left:25%;top:65%;animation-delay:1.8s;width:5px;height:5px}
.usr-show-particle:nth-child(3){left:55%;top:20%;animation-delay:3.2s}
.usr-show-particle:nth-child(4){left:75%;top:55%;animation-delay:4.5s;width:6px;height:6px}
.usr-show-particle:nth-child(5){left:92%;top:35%;animation-delay:2.2s}
@keyframes usrShowParticle{0%,100%{transform:translateY(0) scale(1);opacity:0.25}50%{transform:translateY(-18px) scale(1.4);opacity:0.6}}
.usr-show-hero-content{position:relative;z-index:2}
.usr-show-avatar-wrap{position:relative;width:110px;height:110px;margin:0 auto 1rem}
.usr-show-avatar-ring{position:absolute;inset:-4px;border-radius:50%;border:2px solid rgba(255,255,255,0.3);animation:usrShowRing 3s ease-in-out infinite}
@keyframes usrShowRing{0%,100%{transform:scale(1);opacity:0.3}50%{transform:scale(1.04);opacity:0.7}}
.usr-show-avatar{width:110px;height:110px;border-radius:50%;background:rgba(255,255,255,0.2);backdrop-filter:blur(10px);border:3px solid rgba(255,255,255,0.4);display:flex;align-items:center;justify-content:center;font-size:2.8rem;font-weight:700;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.15);box-shadow:0 8px 32px rgba(0,0,0,0.15)}
.usr-show-name{font-size:1.6rem;font-weight:700;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:0.3rem}
.usr-show-email{font-size:0.85rem;color:rgba(255,255,255,0.85);margin-bottom:0.8rem}
.usr-show-badges{display:flex;align-items:center;justify-content:center;gap:0.6rem;flex-wrap:wrap}
.usr-show-status{display:inline-flex;align-items:center;gap:0.4rem;padding:0.3rem 0.8rem;border-radius:20px;font-size:0.75rem;font-weight:600;backdrop-filter:blur(10px)}
.usr-show-status.active{background:rgba(34,197,94,0.25);color:#fff;border:1px solid rgba(34,197,94,0.4)}
.usr-show-status.inactive{background:rgba(148,163,184,0.25);color:#fff;border:1px solid rgba(148,163,184,0.4)}
.usr-show-status.suspended{background:rgba(239,68,68,0.25);color:#fff;border:1px solid rgba(239,68,68,0.4)}
.usr-show-status.pending{background:rgba(245,158,11,0.25);color:#fff;border:1px solid rgba(245,158,11,0.4)}
.usr-show-role{display:inline-flex;align-items:center;gap:0.4rem;padding:0.3rem 0.8rem;border-radius:20px;background:rgba(255,255,255,0.2);color:#fff;font-size:0.75rem;font-weight:600;border:1px solid rgba(255,255,255,0.3)}
.usr-show-actions{position:absolute;top:1rem;right:1rem;display:flex;gap:0.5rem;z-index:3}
.usr-show-action{padding:0.4rem 0.8rem;border-radius:10px;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all 0.2s;text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;border:1.5px solid rgba(255,255,255,0.3);color:#fff;background:rgba(255,255,255,0.15);backdrop-filter:blur(10px)}
.usr-show-action:hover{background:rgba(255,255,255,0.25);border-color:rgba(255,255,255,0.5);transform:translateY(-1px)}
.usr-show-action.back{border-color:rgba(255,255,255,0.2);background:rgba(255,255,255,0.1)}

.usr-show-stat-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;margin-bottom:2rem}
.usr-show-stat{background:var(--bg-card);border-radius:14px;padding:1rem;text-align:center;border:1px solid var(--border);position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s}
.usr-show-stat:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,0.08)}
.usr-show-stat::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:14px 14px 0 0}
.usr-show-stat:nth-child(1)::before{background:linear-gradient(90deg,#667eea,#764ba2)}
.usr-show-stat:nth-child(2)::before{background:linear-gradient(90deg,#22c55e,#16a34a)}
.usr-show-stat:nth-child(3)::before{background:linear-gradient(90deg,#f59e0b,#f97316)}
.usr-show-stat-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 0.5rem;font-size:0.85rem}
.usr-show-stat:nth-child(1) .usr-show-stat-icon{background:linear-gradient(135deg,#667eea22,#764ba222);color:#667eea}
.usr-show-stat:nth-child(2) .usr-show-stat-icon{background:linear-gradient(135deg,#22c55e22,#16a34a22);color:#22c55e}
.usr-show-stat:nth-child(3) .usr-show-stat-icon{background:linear-gradient(135deg,#f59e0b22,#f9731622);color:#f59e0b}
.usr-show-stat-value{font-size:1.4rem;font-weight:700;color:var(--text-primary);line-height:1}
.usr-show-stat-label{font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-top:0.2rem}

.usr-show-glass{background:var(--bg-card);border:1px solid var(--border);border-radius:16px;overflow:hidden;transition:box-shadow 0.2s;margin-bottom:1.2rem}
.usr-show-glass:hover{box-shadow:0 8px 32px rgba(0,0,0,0.06)}
.usr-show-glass-header{padding:1rem 1.2rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem}
.usr-show-glass-header h6{margin:0;font-weight:600;font-size:0.9rem;color:var(--text-primary)}
.usr-show-glass-header i{color:var(--accent);font-size:0.85rem}
.usr-show-glass-body{padding:1.2rem}

.usr-show-info-item{display:flex;align-items:center;gap:0.8rem;padding:0.7rem 0.8rem;border-radius:12px;transition:background 0.2s;margin-bottom:0.3rem}
.usr-show-info-item:hover{background:var(--bg-elevated)}
.usr-show-info-icon{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.75rem;flex-shrink:0}
.usr-show-info-icon.email{background:linear-gradient(135deg,#667eea22,#764ba222);color:#667eea}
.usr-show-info-icon.phone{background:linear-gradient(135deg,#4facfe22,#00f2fe22);color:#4facfe}
.usr-show-info-icon.role{background:linear-gradient(135deg,#f093fb22,#f5576c22);color:#f5576c}
.usr-show-info-icon.org{background:linear-gradient(135deg,#f59e0b22,#f9731622);color:#f59e0b}
.usr-show-info-icon.daira{background:linear-gradient(135deg,#8b5cf622,#a78bfa22);color:#8b5cf6}
.usr-show-info-icon.calendar{background:linear-gradient(135deg,#22c55e22,#16a34a22);color:#22c55e}
.usr-show-info-icon.login{background:linear-gradient(135deg,#ef444422,#dc262622);color:#ef4444}
.usr-show-info-text{flex:1;min-width:0}
.usr-show-info-label{font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.1rem}
.usr-show-info-value{font-size:0.85rem;color:var(--text-primary);font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

.usr-progress-row{display:flex;align-items:center;gap:0.8rem;margin-bottom:0.6rem}
.usr-progress-label{min-width:110px;font-size:0.78rem;color:var(--text-secondary);font-weight:500}
.usr-progress-bar-wrap{flex:1;height:22px;border-radius:11px;background:var(--bg-elevated);overflow:hidden;position:relative}
.usr-progress-bar{height:100%;border-radius:11px;transition:width 1s ease-out;position:relative}
.usr-progress-bar::after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15));border-radius:11px}
.usr-progress-count{min-width:36px;text-align:right;font-size:0.82rem;font-weight:700;color:var(--text-primary)}

.usr-activity-item{display:flex;gap:0.8rem;padding:0.7rem 0;border-bottom:1px solid var(--border);transition:background 0.2s}
.usr-activity-item:last-child{border-bottom:none}
.usr-activity-item:hover{background:var(--bg-elevated);border-radius:8px;padding-left:0.5rem;padding-right:0.5rem}
.usr-activity-dot{width:8px;height:8px;border-radius:50%;background:var(--accent);margin-top:0.4rem;flex-shrink:0}
.usr-activity-content{flex:1}
.usr-activity-code{display:inline-flex;align-items:center;gap:0.3rem;padding:0.2rem 0.5rem;border-radius:6px;background:var(--accent-surface);color:var(--accent);font-size:0.72rem;font-weight:600;margin-bottom:0.2rem}
.usr-activity-title{font-size:0.82rem;color:var(--text-primary);margin-bottom:0.15rem}
.usr-activity-time{font-size:0.7rem;color:var(--text-muted)}

@media(max-width:768px){
    .usr-show-hero{border-radius:0 0 24px 24px;padding:40px 0 28px}
    .usr-show-hero-title{font-size:1.3rem}
    .usr-show-actions{position:static;justify-content:center;margin-top:1rem}
    .usr-show-stat-row{grid-template-columns:repeat(2,1fr)}
}
</style>

<?php
$avatarInitial = strtoupper(substr($user['first_name'], 0, 1));
$primaryRole = $primaryRole ?? 'citizen';
$roleLabels = [
    'admin_central' => 'Admin Central', 'resp_central' => 'Resp. Central',
    'admin_local' => 'Admin Local', 'chef_unite' => "Chef d'Unité",
    'chef_section' => 'Chef de Section', 'intervenant' => 'Intervenant', 'citizen' => 'Citoyen'
];
$statusLabels = ['active' => __('users.active'), 'inactive' => __('users.inactive'), 'suspended' => __('users.suspended'), 'pending' => __('users.pending')];
?>

<div class="usr-show-hero role-<?= $primaryRole ?>">
    <div class="usr-show-particles">
        <div class="usr-show-particle"></div>
        <div class="usr-show-particle"></div>
        <div class="usr-show-particle"></div>
        <div class="usr-show-particle"></div>
        <div class="usr-show-particle"></div>
    </div>
    <div class="usr-show-actions">
        <?php if ($canEdit): ?>
        <a href="/users/<?= $user['id'] ?>/edit" class="usr-show-action"><i class="fas fa-pen"></i> <?= __('common.edit') ?></a>
        <?php endif; ?>
        <a href="/users" class="usr-show-action back"><i class="fas fa-arrow-left"></i> <?= __('common.back') ?></a>
    </div>
    <div class="usr-show-hero-content">
        <div class="usr-show-avatar-wrap">
            <div class="usr-show-avatar-ring"></div>
            <div class="usr-show-avatar"><?= $avatarInitial ?></div>
        </div>
        <div class="usr-show-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
        <div class="usr-show-email"><?= htmlspecialchars($user['email']) ?></div>
        <div class="usr-show-badges">
            <span class="usr-show-status <?= $user['status'] ?>">
                <span style="width:6px;height:6px;border-radius:50%;background:currentColor;"></span>
                <?= $statusLabels[$user['status']] ?? ucfirst($user['status']) ?>
            </span>
            <span class="usr-show-role"><i class="fas fa-shield-halved"></i> <?= $roleLabels[$primaryRole] ?? ucfirst(str_replace('_', ' ', $primaryRole)) ?></span>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="usr-show-stat-row">
    <?php
    $statItems = [];
    if (in_array($primaryRole, ['admin_central', 'resp_central', 'admin_local'])) {
        $pendingOrg = 0;
        foreach (($stats['orgByStatus'] ?? []) as $row) {
            if (in_array($row['status'], ['submitted','acknowledged','assigned'])) $pendingOrg += $row['cnt'];
        }
        $statItems = [
            ['icon' => 'fas fa-flag', 'value' => $stats['orgTotal'] ?? 0, 'label' => $primaryRole === 'admin_central' ? __('users.total_reports') : __('users.org_reports')],
            ['icon' => 'fas fa-share-nodes', 'value' => $stats['assigned'] ?? 0, 'label' => __('users.assigned_by_you')],
            ['icon' => 'fas fa-clock', 'value' => $pendingOrg, 'label' => __('users.pending_count')],
        ];
    } elseif ($primaryRole === 'chef_unite') {
        $pendingDaira = 0;
        foreach (($stats['dairaByStatus'] ?? []) as $row) {
            if (in_array($row['status'], ['submitted','acknowledged','assigned'])) $pendingDaira += $row['cnt'];
        }
        $statItems = [
            ['icon' => 'fas fa-map-marker-alt', 'value' => $stats['dairaTotal'] ?? 0, 'label' => __('users.daira_reports')],
            ['icon' => 'fas fa-link', 'value' => $stats['assigned'] ?? 0, 'label' => __('users.assigned_directly')],
            ['icon' => 'fas fa-clock', 'value' => $pendingDaira, 'label' => __('users.pending_count')],
        ];
    } elseif ($primaryRole === 'chef_section') {
        $pendingSection = 0;
        foreach (($stats['sectionByStatus'] ?? []) as $row) {
            if (in_array($row['status'], ['submitted','acknowledged','assigned'])) $pendingSection += $row['cnt'];
        }
        $statItems = [
            ['icon' => 'fas fa-city', 'value' => $stats['sectionTotal'] ?? 0, 'label' => __('users.section_reports')],
            ['icon' => 'fas fa-link', 'value' => $stats['assigned'] ?? 0, 'label' => __('users.assigned_directly')],
            ['icon' => 'fas fa-clock', 'value' => $pendingSection, 'label' => __('users.pending_count')],
        ];
    } elseif ($primaryRole === 'intervenant') {
        $completedInterv = 0;
        foreach (($stats['interventionByStatus'] ?? []) as $row) {
            if (in_array($row['status'], ['completed','validated'])) $completedInterv += $row['cnt'];
        }
        $statItems = [
            ['icon' => 'fas fa-wrench', 'value' => $stats['interventions'] ?? 0, 'label' => __('users.interventions')],
            ['icon' => 'fas fa-link', 'value' => $stats['assigned'] ?? 0, 'label' => __('users.assigned_directly')],
            ['icon' => 'fas fa-check-circle', 'value' => $completedInterv, 'label' => __('users.completed')],
        ];
    } else {
        $statItems = [
            ['icon' => 'fas fa-link', 'value' => $stats['assigned'] ?? 0, 'label' => __('users.assigned_reports')],
        ];
    }
    ?>
    <?php foreach ($statItems as $si): ?>
    <div class="usr-show-stat">
        <div class="usr-show-stat-icon"><i class="<?= $si['icon'] ?>"></i></div>
        <div class="usr-show-stat-value"><?= number_format($si['value']) ?></div>
        <div class="usr-show-stat-label"><?= $si['label'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <!-- Left: Info -->
    <div class="col-lg-4">
        <div class="usr-show-glass">
            <div class="usr-show-glass-header">
                <i class="fas fa-circle-info"></i>
                <h6><?= __('users.info_card') ?></h6>
            </div>
            <div class="usr-show-glass-body" style="padding:0.8rem;">
                <div class="usr-show-info-item">
                    <div class="usr-show-info-icon email"><i class="fas fa-envelope"></i></div>
                    <div class="usr-show-info-text">
                        <div class="usr-show-info-label">Email</div>
                        <div class="usr-show-info-value"><?= htmlspecialchars($user['email']) ?></div>
                    </div>
                </div>
                <div class="usr-show-info-item">
                    <div class="usr-show-info-icon phone"><i class="fas fa-phone"></i></div>
                    <div class="usr-show-info-text">
                        <div class="usr-show-info-label">Téléphone</div>
                        <div class="usr-show-info-value"><?= htmlspecialchars($user['phone'] ?: '—') ?></div>
                    </div>
                </div>
                <div class="usr-show-info-item">
                    <div class="usr-show-info-icon role"><i class="fas fa-shield-halved"></i></div>
                    <div class="usr-show-info-text">
                        <div class="usr-show-info-label">Rôle(s)</div>
                        <div class="usr-show-info-value"><?= htmlspecialchars($user['role_labels'] ?? $user['roles'] ?? '—') ?></div>
                    </div>
                </div>
                <div class="usr-show-info-item">
                    <div class="usr-show-info-icon org"><i class="fas fa-building"></i></div>
                    <div class="usr-show-info-text">
                        <div class="usr-show-info-label">Organisation</div>
                        <div class="usr-show-info-value"><?= htmlspecialchars($user['org_name'] ?? '—') ?></div>
                    </div>
                </div>
                <div class="usr-show-info-item">
                    <div class="usr-show-info-icon daira"><i class="fas fa-map-location-dot"></i></div>
                    <div class="usr-show-info-text">
                        <div class="usr-show-info-label">Daïra</div>
                        <div class="usr-show-info-value"><?= htmlspecialchars($user['daira_name'] ?? '—') ?></div>
                    </div>
                </div>
                <div class="usr-show-info-item">
                    <div class="usr-show-info-icon calendar"><i class="fas fa-calendar-check"></i></div>
                    <div class="usr-show-info-text">
                        <div class="usr-show-info-label">Créé le</div>
                        <div class="usr-show-info-value"><?= date('d/m/Y', strtotime($user['created_at'])) ?></div>
                    </div>
                </div>
                <div class="usr-show-info-item">
                    <div class="usr-show-info-icon login"><i class="fas fa-right-to-bracket"></i></div>
                    <div class="usr-show-info-text">
                        <div class="usr-show-info-label">Dernière connexion</div>
                        <div class="usr-show-info-value"><?= $user['last_login_at'] ? \App\Helpers\Helper::timeAgo($user['last_login_at']) : __('users.never') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity -->
        <?php
        $db = \App\Helpers\Database::getConnection();
        $actQ = $db->prepare("SELECT r.tracking_code, r.title, r.status, a.created_at 
            FROM audit_logs a 
            JOIN reports r ON a.model_id = r.id 
            WHERE a.user_id = ? AND a.model = 'Report' AND r.deleted_at IS NULL
            ORDER BY a.created_at DESC LIMIT 5");
        $actQ->execute([$user['id']]);
        $recentActivity = $actQ->fetchAll();
        ?>
        <?php if (!empty($recentActivity)): ?>
        <div class="usr-show-glass">
            <div class="usr-show-glass-header">
                <i class="fas fa-clock-rotate-left"></i>
                <h6><?= __('users.recent_activity') ?></h6>
            </div>
            <div class="usr-show-glass-body" style="padding:0.8rem;">
                <?php foreach ($recentActivity as $act): ?>
                <div class="usr-activity-item">
                    <div class="usr-activity-dot"></div>
                    <div class="usr-activity-content">
                        <div class="usr-activity-code"><i class="fas fa-hashtag" style="font-size:0.6rem;"></i> <?= $act['tracking_code'] ?></div>
                        <div class="usr-activity-title"><?= htmlspecialchars(mb_substr($act['title'], 0, 50)) ?></div>
                        <div class="usr-activity-time"><i class="fas fa-clock me-1"></i><?= \App\Helpers\Helper::timeAgo($act['created_at']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right: Stats Breakdown -->
    <div class="col-lg-8">
        <?php if (in_array($primaryRole, ['admin_central', 'resp_central', 'admin_local']) && !empty($stats['orgByStatus'])): ?>
        <div class="usr-show-glass">
            <div class="usr-show-glass-header">
                <i class="fas fa-chart-bar"></i>
                <h6><?= __('users.status_breakdown') ?></h6>
            </div>
            <div class="usr-show-glass-body">
                <?php
                $statusBadgeColors = [
                    'submitted' => ['#94a3b8', 'rgba(148,163,184,0.15)'],
                    'acknowledged' => ['#0891b2', 'rgba(8,145,178,0.15)'],
                    'assigned' => ['#6366f1', 'rgba(99,102,241,0.15)'],
                    'in_progress' => ['#f59e0b', 'rgba(245,158,11,0.15)'],
                    'pending_review' => ['#8b5cf6', 'rgba(139,92,246,0.15)'],
                    'pending_unite' => ['#a78bfa', 'rgba(167,139,250,0.15)'],
                    'validated' => ['#22c55e', 'rgba(34,197,94,0.15)'],
                    'resolved' => ['#16a34a', 'rgba(22,163,74,0.15)'],
                    'closed' => ['#475569', 'rgba(71,85,105,0.15)'],
                    'rejected' => ['#ef4444', 'rgba(239,68,68,0.15)'],
                ];
                $maxCnt = max(array_column($stats['orgByStatus'], 'cnt'));
                ?>
                <?php foreach ($stats['orgByStatus'] as $row): ?>
                <?php
                $sLabel = __('statuses.' . $row['status']) ?? $row['status'];
                $sColor = $statusBadgeColors[$row['status']] ?? ['#64748b', 'rgba(100,116,139,0.15)'];
                $pct = $maxCnt > 0 ? round(($row['cnt'] / $maxCnt) * 100) : 0;
                ?>
                <div class="usr-progress-row">
                    <div class="usr-progress-label">
                        <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:<?= $sColor[0] ?>;margin-right:0.4rem;"></span>
                        <?= $sLabel ?>
                    </div>
                    <div class="usr-progress-bar-wrap">
                        <div class="usr-progress-bar" style="width:<?= $pct ?>%;background:<?= $sColor[0] ?>;"></div>
                    </div>
                    <div class="usr-progress-count"><?= $row['cnt'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($primaryRole === 'chef_unite'): ?>
            <?php if (!empty($stats['communeStats'])): ?>
            <div class="usr-show-glass">
                <div class="usr-show-glass-header">
                    <i class="fas fa-city"></i>
                    <h6><?= __('users.commune_stats') ?></h6>
                </div>
                <div class="usr-show-glass-body">
                    <?php
                    $maxCs = max(array_map(function($cs) { return (int)$cs['report_count']; }, $stats['communeStats']));
                    ?>
                    <?php foreach ($stats['communeStats'] as $cs): ?>
                    <?php
                    $total = (int) $cs['report_count'];
                    $resolved = (int) $cs['resolved_count'];
                    $taux = $total > 0 ? round(($resolved / $total) * 100) : 0;
                    $tauxColor = $taux >= 70 ? '#22c55e' : ($taux >= 40 ? '#f59e0b' : '#ef4444');
                    $barPct = $maxCs > 0 ? round(($total / $maxCs) * 100) : 0;
                    ?>
                    <div class="usr-progress-row">
                        <div class="usr-progress-label">
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--accent);margin-right:0.4rem;"></span>
                            <?= htmlspecialchars($cs['commune_name'] ?? '—') ?>
                        </div>
                        <div class="usr-progress-bar-wrap">
                            <div class="usr-progress-bar" style="width:<?= $barPct ?>%;background:linear-gradient(90deg,var(--accent),var(--accent-light));"></div>
                        </div>
                        <div class="usr-progress-count" style="min-width:80px;text-align:right;">
                            <?= $total ?> · <span style="color:<?= $tauxColor ?>;font-size:0.75rem;"><?= $taux ?>%</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($stats['dairaByStatus'])): ?>
            <div class="usr-show-glass">
                <div class="usr-show-glass-header">
                    <i class="fas fa-chart-bar"></i>
                    <h6><?= __('users.daira_status_breakdown') ?></h6>
                </div>
                <div class="usr-show-glass-body">
                    <?php
                    $maxCnt = max(array_column($stats['dairaByStatus'], 'cnt'));
                    foreach ($stats['dairaByStatus'] as $row):
                        $sLabel = __('statuses.' . $row['status']) ?? $row['status'];
                        $sColor = $statusBadgeColors[$row['status']] ?? ['#64748b', 'rgba(100,116,139,0.15)'];
                        $pct = $maxCnt > 0 ? round(($row['cnt'] / $maxCnt) * 100) : 0;
                    ?>
                    <div class="usr-progress-row">
                        <div class="usr-progress-label">
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:<?= $sColor[0] ?>;margin-right:0.4rem;"></span>
                            <?= $sLabel ?>
                        </div>
                        <div class="usr-progress-bar-wrap">
                            <div class="usr-progress-bar" style="width:<?= $pct ?>%;background:<?= $sColor[0] ?>;"></div>
                        </div>
                        <div class="usr-progress-count"><?= $row['cnt'] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($primaryRole === 'chef_section' && !empty($stats['sectionByStatus'])): ?>
        <div class="usr-show-glass">
            <div class="usr-show-glass-header">
                <i class="fas fa-chart-bar"></i>
                <h6><?= __('users.status_breakdown') ?></h6>
            </div>
            <div class="usr-show-glass-body">
                <?php
                $maxCnt = max(array_column($stats['sectionByStatus'], 'cnt'));
                foreach ($stats['sectionByStatus'] as $row):
                    $sLabel = __('statuses.' . $row['status']) ?? $row['status'];
                    $sColor = $statusBadgeColors[$row['status']] ?? ['#64748b', 'rgba(100,116,139,0.15)'];
                    $pct = $maxCnt > 0 ? round(($row['cnt'] / $maxCnt) * 100) : 0;
                ?>
                <div class="usr-progress-row">
                    <div class="usr-progress-label">
                        <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:<?= $sColor[0] ?>;margin-right:0.4rem;"></span>
                        <?= $sLabel ?>
                    </div>
                    <div class="usr-progress-bar-wrap">
                        <div class="usr-progress-bar" style="width:<?= $pct ?>%;background:<?= $sColor[0] ?>;"></div>
                    </div>
                    <div class="usr-progress-count"><?= $row['cnt'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($primaryRole === 'intervenant' && !empty($stats['interventionByStatus'])): ?>
        <div class="usr-show-glass">
            <div class="usr-show-glass-header">
                <i class="fas fa-chart-bar"></i>
                <h6><?= __('users.intervention_status') ?></h6>
            </div>
            <div class="usr-show-glass-body">
                <?php
                $intBadgeColors = [
                    'assigned' => ['#6366f1', 'rgba(99,102,241,0.15)'],
                    'in_progress' => ['#f59e0b', 'rgba(245,158,11,0.15)'],
                    'completed' => ['#0891b2', 'rgba(8,145,178,0.15)'],
                    'validated' => ['#22c55e', 'rgba(34,197,94,0.15)'],
                    'rejected' => ['#ef4444', 'rgba(239,68,68,0.15)'],
                ];
                $maxCnt = max(array_column($stats['interventionByStatus'], 'cnt'));
                foreach ($stats['interventionByStatus'] as $row):
                    $sLabel = __('users.intervention_' . $row['status']) ?? $row['status'];
                    $sColor = $intBadgeColors[$row['status']] ?? ['#64748b', 'rgba(100,116,139,0.15)'];
                    $pct = $maxCnt > 0 ? round(($row['cnt'] / $maxCnt) * 100) : 0;
                ?>
                <div class="usr-progress-row">
                    <div class="usr-progress-label">
                        <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:<?= $sColor[0] ?>;margin-right:0.4rem;"></span>
                        <?= $sLabel ?>
                    </div>
                    <div class="usr-progress-bar-wrap">
                        <div class="usr-progress-bar" style="width:<?= $pct ?>%;background:<?= $sColor[0] ?>;"></div>
                    </div>
                    <div class="usr-progress-count"><?= $row['cnt'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($primaryRole === 'citizen'): ?>
        <div class="usr-show-glass">
            <div class="usr-show-glass-body text-center" style="padding:2rem;">
                <div style="width:60px;height:60px;border-radius:50%;background:var(--accent-surface);color:var(--accent);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.5rem;">
                    <i class="fas fa-user"></i>
                </div>
                <div style="font-size:1.4rem;font-weight:700;color:var(--text-primary);"><?= number_format($stats['assigned'] ?? 0) ?></div>
                <div style="font-size:0.82rem;color:var(--text-muted);"><?= __('users.assigned_reports') ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
