<?php $pageTitle = __('users.title'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.usr-hero{position:relative;background:linear-gradient(135deg,#6366f1 0%,#8b5cf6 50%,#a78bfa 100%);border-radius:0 0 32px 32px;padding:40px 0 30px;margin:-1.5rem -15px 2rem;text-align:center;overflow:hidden}
.usr-hero::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle at 30% 50%,rgba(255,255,255,0.08) 0%,transparent 50%);animation:usrHeroShine 10s ease-in-out infinite}
@keyframes usrHeroShine{0%,100%{transform:translateX(-20%) rotate(0deg)}50%{transform:translateX(20%) rotate(3deg)}}
.usr-hero-particles{position:absolute;inset:0;overflow:hidden;pointer-events:none}
.usr-hero-particle{position:absolute;width:4px;height:4px;background:rgba(255,255,255,0.25);border-radius:50%;animation:usrParticle 7s ease-in-out infinite}
.usr-hero-particle:nth-child(1){left:8%;top:25%;animation-delay:0s}
.usr-hero-particle:nth-child(2){left:25%;top:65%;animation-delay:1.8s;width:5px;height:5px}
.usr-hero-particle:nth-child(3){left:55%;top:20%;animation-delay:3.2s}
.usr-hero-particle:nth-child(4){left:75%;top:55%;animation-delay:4.5s;width:6px;height:6px}
.usr-hero-particle:nth-child(5){left:92%;top:35%;animation-delay:2.2s}
@keyframes usrParticle{0%,100%{transform:translateY(0) scale(1);opacity:0.25}50%{transform:translateY(-18px) scale(1.4);opacity:0.6}}
.usr-hero-content{position:relative;z-index:2}
.usr-hero-icon{width:64px;height:64px;border-radius:18px;background:rgba(255,255,255,0.2);backdrop-filter:blur(10px);border:2px solid rgba(255,255,255,0.3);display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;color:#fff;margin-bottom:1rem;box-shadow:0 8px 32px rgba(0,0,0,0.15)}
.usr-hero-title{font-size:1.8rem;font-weight:700;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:0.3rem}
.usr-hero-sub{font-size:0.85rem;color:rgba(255,255,255,0.85)}

.usr-stat-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;margin-bottom:2rem}
.usr-stat-card{background:var(--bg-card);border-radius:14px;padding:1rem;text-align:center;border:1px solid var(--border);position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s}
.usr-stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,0.08)}
.usr-stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:14px 14px 0 0}
.usr-stat-card:nth-child(1)::before{background:linear-gradient(90deg,#667eea,#764ba2)}
.usr-stat-card:nth-child(2)::before{background:linear-gradient(90deg,#22c55e,#16a34a)}
.usr-stat-card:nth-child(3)::before{background:linear-gradient(90deg,#f59e0b,#f97316)}
.usr-stat-card:nth-child(4)::before{background:linear-gradient(90deg,#ef4444,#dc2626)}
.usr-stat-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 0.5rem;font-size:0.85rem}
.usr-stat-card:nth-child(1) .usr-stat-icon{background:linear-gradient(135deg,#667eea22,#764ba222);color:#667eea}
.usr-stat-card:nth-child(2) .usr-stat-icon{background:linear-gradient(135deg,#22c55e22,#16a34a22);color:#22c55e}
.usr-stat-card:nth-child(3) .usr-stat-icon{background:linear-gradient(135deg,#f59e0b22,#f9731622);color:#f59e0b}
.usr-stat-card:nth-child(4) .usr-stat-icon{background:linear-gradient(135deg,#ef444422,#dc262622);color:#ef4444}
.usr-stat-value{font-size:1.5rem;font-weight:700;color:var(--text-primary);line-height:1}
.usr-stat-label{font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-top:0.2rem}

.usr-filter-bar{background:var(--bg-card);border:1px solid var(--border);border-radius:14px;padding:1rem 1.2rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.8rem;flex-wrap:wrap;transition:box-shadow 0.2s}
.usr-filter-bar:focus-within{box-shadow:0 0 0 3px var(--accent-glow)}
.usr-filter-input{flex:1;min-width:180px;padding:0.55rem 0.8rem;border:1.5px solid var(--border);border-radius:10px;background:var(--bg-elevated);color:var(--text-primary);font-size:0.85rem;transition:border-color 0.2s,box-shadow 0.2s;outline:none}
.usr-filter-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.usr-filter-input::placeholder{color:var(--text-muted)}
.usr-filter-select{padding:0.55rem 0.8rem;border:1.5px solid var(--border);border-radius:10px;background:var(--bg-elevated);color:var(--text-primary);font-size:0.85rem;outline:none;cursor:pointer;min-width:120px;transition:border-color 0.2s}
.usr-filter-select:focus{border-color:var(--accent)}
.usr-filter-btn{padding:0.55rem 1rem;border:none;border-radius:10px;background:linear-gradient(135deg,var(--accent),#764ba2);color:#fff;font-weight:600;font-size:0.85rem;cursor:pointer;transition:transform 0.2s,box-shadow 0.2s;display:inline-flex;align-items:center;gap:0.4rem;white-space:nowrap}
.usr-filter-btn:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(99,102,241,0.3)}
.usr-filter-reset{padding:0.55rem 1rem;border:1.5px solid var(--border);border-radius:10px;background:transparent;color:var(--text-secondary);font-size:0.85rem;cursor:pointer;transition:border-color 0.2s;text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;white-space:nowrap}
.usr-filter-reset:hover{border-color:var(--accent);color:var(--accent)}

.usr-glass{background:var(--bg-card);border:1px solid var(--border);border-radius:16px;overflow:hidden;transition:box-shadow 0.2s}
.usr-glass:hover{box-shadow:0 8px 32px rgba(0,0,0,0.06)}
.usr-table{width:100%;border-collapse:collapse}
.usr-table thead{background:linear-gradient(135deg,rgba(99,102,241,0.08),rgba(139,92,246,0.08))}
.usr-table th{padding:0.8rem 1rem;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);border-bottom:1px solid var(--border);white-space:nowrap}
.usr-table td{padding:0.75rem 1rem;border-bottom:1px solid var(--border);vertical-align:middle;font-size:0.85rem;color:var(--text-primary)}
.usr-table tr:last-child td{border-bottom:none}
.usr-table tbody tr{transition:background 0.15s}
.usr-table tbody tr:hover{background:var(--accent-surface)}
.usr-user-cell{display:flex;align-items:center;gap:0.7rem}
.usr-user-avatar{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;color:#fff;flex-shrink:0}
.usr-user-avatar.av-1{background:linear-gradient(135deg,#667eea,#764ba2)}
.usr-user-avatar.av-2{background:linear-gradient(135deg,#f093fb,#f5576c)}
.usr-user-avatar.av-3{background:linear-gradient(135deg,#4facfe,#00f2fe)}
.usr-user-avatar.av-4{background:linear-gradient(135deg,#43e97b,#38f9d7)}
.usr-user-avatar.av-5{background:linear-gradient(135deg,#fa709a,#fee140)}
.usr-user-name{font-weight:600;color:var(--text-primary);text-decoration:none;transition:color 0.2s}
.usr-user-name:hover{color:var(--accent)}
.usr-user-email{font-size:0.75rem;color:var(--text-muted)}
.usr-role-badge{display:inline-flex;align-items:center;gap:0.3rem;padding:0.25rem 0.6rem;border-radius:8px;font-size:0.72rem;font-weight:600;white-space:nowrap}
.usr-role-badge.role-admin_central{background:linear-gradient(135deg,#ef444422,#dc262622);color:#ef4444}
.usr-role-badge.role-resp_central,.usr-role-badge.role-admin_local{background:linear-gradient(135deg,#f59e0b22,#f9731622);color:#f59e0b}
.usr-role-badge.role-chef_unite{background:linear-gradient(135deg,#8b5cf622,#a78bfa22);color:#8b5cf6}
.usr-role-badge.role-chef_section{background:linear-gradient(135deg,#667eea22,#764ba222);color:#667eea}
.usr-role-badge.role-intervenant{background:linear-gradient(135deg,#22c55e22,#16a34a22);color:#22c55e}
.usr-role-badge.role-citizen{background:linear-gradient(135deg,#64748b22,#47556922);color:#64748b}
.usr-status-dot{width:8px;height:8px;border-radius:50%;display:inline-block;margin-right:0.4rem}
.usr-status-dot.active{background:#22c55e;box-shadow:0 0 6px rgba(34,197,94,0.4)}
.usr-status-dot.inactive{background:#94a3b8}
.usr-status-dot.suspended{background:#ef4444;box-shadow:0 0 6px rgba(239,68,68,0.4)}
.usr-status-dot.pending{background:#f59e0b;box-shadow:0 0 6px rgba(245,158,11,0.4)}
.usr-action-btn{width:30px;height:30px;border-radius:8px;border:1.5px solid var(--border);background:transparent;color:var(--text-muted);display:inline-flex;align-items:center;justify-content:center;font-size:0.75rem;cursor:pointer;transition:all 0.2s;text-decoration:none}
.usr-action-btn:hover{border-color:var(--accent);color:var(--accent);background:var(--accent-surface)}
.usr-action-btn.edit:hover{border-color:#f59e0b;color:#f59e0b;background:rgba(245,158,11,0.08)}

.usr-mobile-list{display:none}
.usr-mobile-card{background:var(--bg-card);border:1px solid var(--border);border-radius:14px;padding:1rem;margin-bottom:0.8rem;transition:transform 0.2s,box-shadow 0.2s}
.usr-mobile-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,0.06)}
.usr-mobile-card-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:0.6rem}
.usr-mobile-card-body{display:grid;grid-template-columns:1fr 1fr;gap:0.4rem}
.usr-mobile-label{font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.3px}
.usr-mobile-value{font-size:0.82rem;color:var(--text-primary);font-weight:500}
.usr-mobile-footer{display:flex;gap:0.5rem;margin-top:0.8rem;padding-top:0.6rem;border-top:1px solid var(--border)}

@media(max-width:768px){
    .usr-hero{border-radius:0 0 24px 24px;padding:30px 0 24px}
    .usr-hero-title{font-size:1.4rem}
    .usr-filter-bar{flex-direction:column;gap:0.6rem}
    .usr-filter-input,.usr-filter-select{min-width:100%}
    .usr-table{display:none}
    .usr-mobile-list{display:block}
    .usr-stat-row{grid-template-columns:repeat(2,1fr)}
}
</style>

<div class="usr-hero">
    <div class="usr-hero-particles">
        <div class="usr-hero-particle"></div>
        <div class="usr-hero-particle"></div>
        <div class="usr-hero-particle"></div>
        <div class="usr-hero-particle"></div>
        <div class="usr-hero-particle"></div>
    </div>
    <div class="usr-hero-content">
        <div class="usr-hero-icon"><i class="fas fa-users"></i></div>
        <div class="usr-hero-title"><?= __('users.title') ?></div>
        <div class="usr-hero-sub"><?= count($users) ?> <?= __('users.total_users') ?></div>
    </div>
</div>

<?php
$totalUsers = count($users);
$activeCount = 0; $inactiveCount = 0; $suspendedCount = 0;
foreach ($users as $u) {
    if ($u['status'] === 'active') $activeCount++;
    elseif ($u['status'] === 'inactive') $inactiveCount++;
    elseif ($u['status'] === 'suspended') $suspendedCount++;
}
?>

<div class="usr-stat-row">
    <div class="usr-stat-card">
        <div class="usr-stat-icon"><i class="fas fa-users"></i></div>
        <div class="usr-stat-value"><?= $totalUsers ?></div>
        <div class="usr-stat-label"><?= __('users.total_users') ?></div>
    </div>
    <div class="usr-stat-card">
        <div class="usr-stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="usr-stat-value"><?= $activeCount ?></div>
        <div class="usr-stat-label"><?= __('users.active') ?></div>
    </div>
    <div class="usr-stat-card">
        <div class="usr-stat-icon"><i class="fas fa-pause-circle"></i></div>
        <div class="usr-stat-value"><?= $inactiveCount ?></div>
        <div class="usr-stat-label"><?= __('users.inactive') ?></div>
    </div>
    <div class="usr-stat-card">
        <div class="usr-stat-icon"><i class="fas fa-ban"></i></div>
        <div class="usr-stat-value"><?= $suspendedCount ?></div>
        <div class="usr-stat-label"><?= __('users.suspended') ?></div>
    </div>
</div>

<form method="GET" class="usr-filter-bar">
    <i class="fas fa-search" style="color:var(--text-muted);font-size:0.85rem;"></i>
    <input type="text" class="usr-filter-input" name="search" placeholder="<?= __('users.search_placeholder') ?>" value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <select class="usr-filter-select" name="role">
        <option value=""><?= __('users.filter_role') ?></option>
        <?php foreach ($roles as $r): ?>
        <option value="<?= $r['name'] ?>" <?= ($_GET['role'] ?? '') === $r['name'] ? 'selected' : '' ?>><?= $r['label'] ?></option>
        <?php endforeach; ?>
    </select>
    <select class="usr-filter-select" name="status">
        <option value=""><?= __('users.filter_status') ?></option>
        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>><?= __('users.active') ?></option>
        <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>><?= __('users.inactive') ?></option>
        <option value="suspended" <?= ($_GET['status'] ?? '') === 'suspended' ? 'selected' : '' ?>><?= __('users.suspended') ?></option>
    </select>
    <button type="submit" class="usr-filter-btn"><i class="fas fa-filter"></i> Filtrer</button>
    <a href="/users" class="usr-filter-reset"><i class="fas fa-times"></i> <?= __('common.reset') ?></a>
    <?php if ($canCreate ?? false): ?>
    <a href="/users/create" class="usr-filter-btn" style="margin-left:auto;"><i class="fas fa-plus"></i> <?= __('users.new_user') ?></a>
    <?php endif; ?>
</form>

<?php
$avatarColors = ['av-1','av-2','av-3','av-4','av-5'];
$statusColors = ['active' => '#22c55e', 'inactive' => '#94a3b8', 'suspended' => '#ef4444', 'pending' => '#f59e0b'];
$statusLabels = ['active' => __('users.active'), 'inactive' => __('users.inactive'), 'suspended' => __('users.suspended'), 'pending' => __('users.pending')];
?>

<div class="usr-glass">
    <table class="usr-table">
        <thead>
            <tr>
                <th><?= __('users.name') ?></th>
                <th><?= __('users.role') ?></th>
                <th><?= __('common.organization') ?></th>
                <th><?= __('common.status') ?></th>
                <th class="d-none d-lg-table-cell"><?= __('users.last_login') ?></th>
                <th style="width:80px;"><?= __('common.actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--text-muted);">
                <i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.4;"></i>
                <?= __('dashboard.no_reports') ?>
            </td></tr>
        <?php else: foreach ($users as $idx => $u): ?>
            <tr>
                <td>
                    <div class="usr-user-cell">
                        <div class="usr-user-avatar <?= $avatarColors[$idx % 5] ?>"><?= strtoupper(substr($u['first_name'], 0, 1)) ?></div>
                        <div>
                            <a href="/users/<?= $u['id'] ?>" class="usr-user-name"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></a>
                            <div class="usr-user-email"><?= htmlspecialchars($u['email']) ?></div>
                        </div>
                    </div>
                </td>
                <td>
                    <?php
                    $roleName = $u['role_names'] ?? 'citizen';
                    $roleArr = explode(',', $roleName);
                    $primaryRole = trim($roleArr[0]);
                    ?>
                    <span class="usr-role-badge role-<?= $primaryRole ?>"><i class="fas fa-shield-halved"></i> <?= $u['role_labels'] ?? '-' ?></span>
                </td>
                <td><small style="color:var(--text-secondary);"><?= htmlspecialchars($u['org_name'] ?? '-') ?></small></td>
                <td>
                    <span class="usr-status-dot <?= $u['status'] ?>"></span>
                    <small style="color:var(--text-secondary);"><?= $statusLabels[$u['status']] ?? $u['status'] ?></small>
                </td>
                <td class="d-none d-lg-table-cell"><small style="color:var(--text-muted);"><?= $u['last_login_at'] ? \App\Helpers\Helper::timeAgo($u['last_login_at']) : '—' ?></small></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/users/<?= $u['id'] ?>" class="usr-action-btn" title="<?= __('common.view') ?>"><i class="fas fa-eye"></i></a>
                        <a href="/users/<?= $u['id'] ?>/edit" class="usr-action-btn edit" title="<?= __('common.edit') ?>"><i class="fas fa-pen"></i></a>
                    </div>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>

    <?php if (!empty($users)): ?>
    <div class="usr-mobile-list">
        <?php foreach ($users as $idx => $u): ?>
        <div class="usr-mobile-card">
            <div class="usr-mobile-card-header">
                <div class="usr-user-cell">
                    <div class="usr-user-avatar <?= $avatarColors[$idx % 5] ?>"><?= strtoupper(substr($u['first_name'], 0, 1)) ?></div>
                    <a href="/users/<?= $u['id'] ?>" class="usr-user-name" style="font-size:0.9rem;"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></a>
                </div>
                <span class="usr-status-dot <?= $u['status'] ?>"></span>
            </div>
            <div class="usr-mobile-card-body">
                <div><div class="usr-mobile-label">Email</div><div class="usr-mobile-value"><?= htmlspecialchars($u['email']) ?></div></div>
                <div><div class="usr-mobile-label">Organisation</div><div class="usr-mobile-value"><?= htmlspecialchars($u['org_name'] ?? '-') ?></div></div>
                <div><div class="usr-mobile-label">Rôle</div><div class="usr-mobile-value"><?= $u['role_labels'] ?? '-' ?></div></div>
                <div><div class="usr-mobile-label">Statut</div><div class="usr-mobile-value"><?= $statusLabels[$u['status']] ?? $u['status'] ?></div></div>
            </div>
            <div class="usr-mobile-footer">
                <a href="/users/<?= $u['id'] ?>" class="usr-action-btn"><i class="fas fa-eye"></i></a>
                <a href="/users/<?= $u['id'] ?>/edit" class="usr-action-btn edit"><i class="fas fa-pen"></i></a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
