<?php $pageTitle = 'Badges & Récompenses'; $activeTab = 'badges'; ?>

<div class="c-section-title c-anim-fade">
    <h6><i class="fas fa-award"></i> Badges & Récompenses</h6>
</div>

<!-- Stats Grid -->
<div class="c-stats-row c-anim-slide c-delay-1">
    <div class="c-stat accent">
        <div class="c-stat-value"><?= $userStats['total'] ?></div>
        <div class="c-stat-label">Signalements</div>
    </div>
    <div class="c-stat green">
        <div class="c-stat-value"><?= $userStats['resolved'] ?></div>
        <div class="c-stat-label">Résolus</div>
    </div>
    <div class="c-stat cyan">
        <div class="c-stat-value"><?= $userStats['active'] ?></div>
        <div class="c-stat-label">En cours</div>
    </div>
    <div class="c-stat amber">
        <div class="c-stat-value"><?= $userStats['rate'] ?>%</div>
        <div class="c-stat-label">Taux</div>
    </div>
</div>

<!-- Level Card -->
<div class="c-card c-anim-slide c-delay-2" style="margin-bottom:16px;">
    <div style="display:flex;align-items:center;gap:14px;">
        <div class="c-feed-avatar" style="width:52px;height:52px;font-size:1.2rem;">
            <?= strtoupper(substr(\App\Helpers\Session::getUserName(), 0, 1)) ?>
        </div>
        <div style="flex:1;">
            <div style="font-weight:700;font-size:1rem;"><?= htmlspecialchars(\App\Helpers\Session::getUserName()) ?></div>
            <div style="font-size:0.8rem;color:var(--c-accent);display:flex;align-items:center;gap:6px;">
                <i class="fas <?= $userLevel['icon'] ?>"></i> Niveau <?= $userLevel['number'] ?? 1 ?> — <?= $userLevel['name'] ?? 'Citoyen' ?>
            </div>
            <div class="c-level-bar" style="margin-top:6px;">
                <div class="c-level-fill" style="width:<?= $userLevel['progress'] ?>%;"></div>
            </div>
            <div style="font-size:0.7rem;color:var(--c-text-muted);"><?= $userLevel['points'] ?? 0 ?> points<?php if ($userLevel['next_min']): ?> / <?= $userLevel['next_min'] ?> pour <?= $userLevel['next_level'] ?><?php endif; ?></div>
        </div>
    </div>
</div>

<!-- Badges Collection -->
<div class="c-section-title c-anim-fade" style="margin-top:8px;">
    <h6><i class="fas fa-medal"></i> Collection de badges</h6>
    <span style="font-size:0.75rem;color:var(--c-text-muted);"><?= count($earnedKeys) ?>/<?= count($allBadges) ?> obtenus</span>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(155px,1fr));gap:10px;margin-bottom:20px;">
<?php foreach ($allBadges as $key => $def):
    $earned = in_array($key, $earnedKeys);
    $earnedAt = null;
    if ($earned) {
        foreach ($userBadges as $ub) {
            if ($ub['badge_key'] === $key) { $earnedAt = $ub['earned_at']; break; }
        }
    }
?>
<div class="c-card c-anim-fade <?= $earned ? '' : 'locked' ?>" style="padding:14px;text-align:center;<?= !$earned ? 'opacity:0.4;filter:grayscale(0.7);' : '' ?>">
    <div style="width:48px;height:48px;border-radius:14px;background:<?= $earned ? $def['color'] : '#374151' ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:1.2rem;color:#fff;">
        <i class="fas <?= $def['icon'] ?>"></i>
    </div>
    <div style="font-weight:700;font-size:0.82rem;margin-bottom:3px;"><?= htmlspecialchars($def['name']) ?></div>
    <div style="font-size:0.7rem;color:var(--c-text-muted);line-height:1.3;"><?= htmlspecialchars($def['desc']) ?></div>
    <?php if ($earned && $earnedAt): ?>
    <div style="font-size:0.65rem;color:var(--c-green);margin-top:6px;font-weight:600;">
        <i class="fas fa-check-circle"></i> <?= (new \DateTime($earnedAt))->format('d/m/Y') ?>
    </div>
    <?php elseif (!$earned): ?>
    <div style="font-size:0.65rem;color:var(--c-text-muted);margin-top:6px;font-weight:500;">
        <i class="fas fa-lock"></i> Non obtenu
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>
</div>

<!-- Recent Activity -->
<?php if (!empty($recentActivity)): ?>
<div class="c-section-title c-anim-fade">
    <h6><i class="fas fa-clock-rotate-left"></i> Activité récente</h6>
</div>

<div class="c-card c-anim-slide" style="padding:0;">
<?php
$reasonIcons = [
    'report_created' => ['fa-flag', 'var(--c-accent)'],
    'report_resolved' => ['fa-check-circle', 'var(--c-green)'],
    'post_created' => ['fa-pen-to-square', 'var(--c-cyan)'],
    'comment_created' => ['fa-comment', 'var(--c-purple)'],
    'like_received' => ['fa-heart', 'var(--c-red)'],
    'daily_login' => ['fa-right-to-bracket', 'var(--c-amber)'],
    'first_report' => ['fa-star', 'var(--c-amber)'],
];
$reasonLabels = [
    'report_created' => 'Signalement créé',
    'report_resolved' => 'Signalement résolu',
    'post_created' => 'Publication créée',
    'comment_created' => 'Commentaire',
    'like_received' => 'Like reçu',
    'daily_login' => 'Connexion quotidienne',
    'first_report' => 'Premier signalement',
];
foreach ($recentActivity as $i => $act):
    $icon = $reasonIcons[$act['reason']] ?? ['fa-circle', 'var(--c-text-muted)'];
    $label = $reasonLabels[$act['reason']] ?? $act['reason'];
?>
<div style="display:flex;align-items:center;gap:12px;padding:12px 14px;<?= $i > 0 ? 'border-top:1px solid var(--c-card-border);' : '' ?>">
    <div style="width:32px;height:32px;border-radius:8px;background:<?= $icon[1] ?>20;color:<?= $icon[1] ?>;display:flex;align-items:center;justify-content:center;font-size:0.75rem;flex-shrink:0;">
        <i class="fas <?= $icon[0] ?>"></i>
    </div>
    <div style="flex:1;min-width:0;">
        <div style="font-size:0.82rem;font-weight:600;"><?= htmlspecialchars($label) ?></div>
        <div style="font-size:0.7rem;color:var(--c-text-muted);"><?= (new \DateTime($act['created_at'])) instanceof \DateTime ? (new \DateTime($act['created_at']))->format('d/m/Y H:i') : $act['created_at'] ?></div>
    </div>
    <div style="font-size:0.85rem;font-weight:700;color:var(--c-accent);">+<?= $act['points'] ?></div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
