<?php $pageTitle = 'Classement'; $activeTab = 'leaderboard';
$leaderboard = \App\Helpers\Gamification::getLeaderboard(20);
$userLevel = \App\Helpers\Gamification::getLevel(\App\Helpers\Session::getUserId());
?>
<div class="c-section-title c-anim-fade">
    <h6><i class="fas fa-trophy"></i> Classement</h6>
</div>

<!-- User level card -->
<div class="c-card c-anim-slide c-delay-1" style="margin-bottom:20px;">
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

<!-- Podium -->
<?php $top3 = array_slice($leaderboard, 0, 3); ?>
<?php if (count($top3) >= 1): ?>
<div class="c-podium c-anim-slide c-delay-2">
    <?php for ($i = 1; $i <= 3; $i++): $u = $top3[$i - 1] ?? null; ?>
    <div class="c-podium-item">
        <div class="c-podium-rank" style="color:<?= $i === 1 ? '#f59e0b' : ($i === 2 ? '#94a3b8' : '#cd7f32') ?>;"><?= $i ?></div>
        <div class="c-podium-avatar"><?= strtoupper(substr($u['first_name'] ?? '?', 0, 1)) ?></div>
        <div class="c-podium-name"><?= htmlspecialchars(($u['first_name'] ?? '?') . ' ' . ($u['last_name'] ?? '')) ?></div>
        <div class="c-podium-points"><?= $u['total_points'] ?? 0 ?> pts</div>
    </div>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Full list -->
<div id="leaderboardList">
<?php foreach ($leaderboard as $i => $u):
    $rank = $i + 1;
    $isMe = $u['id'] == \App\Helpers\Session::getUserId();
?>
<div class="c-leader-row c-anim-fade" style="animation-delay:<?= ($i + 3) * 0.04 ?>s;<?= $isMe ? 'border-color:var(--c-accent);background:var(--c-accent-surface);' : '' ?>">
    <div class="c-leader-rank"><?= $rank ?></div>
    <div class="c-leader-avatar"><?= strtoupper(substr($u['first_name'] ?? '?', 0, 1)) ?></div>
    <div class="c-leader-info">
        <div class="c-leader-name"><?= htmlspecialchars(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?><?= $isMe ? ' <span style="font-size:0.7rem;color:var(--c-accent);">(vous)</span>' : '' ?></div>
        <div class="c-leader-level"><?= $u['report_count'] ?? 0 ?> signalements</div>
    </div>
    <div class="c-leader-pts"><?= $u['total_points'] ?> pts</div>
</div>
<?php endforeach; ?>
</div>

<?php if (empty($leaderboard)): ?>
<div class="c-empty c-anim-fade">
    <svg class="c-empty-svg" viewBox="0 0 140 120" fill="none">
        <circle class="c-sv-circle" cx="70" cy="60" r="50"/>
        <path class="c-sv-icon c-sv-float" d="M48 76l8-26 10 20 12-30 10 36" stroke="var(--c-amber)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        <circle cx="48" cy="76" r="4" fill="var(--c-amber)" opacity="0.5"/>
        <circle cx="88" cy="50" r="4" fill="var(--c-accent)" opacity="0.5"/>
        <circle class="c-sv-dot" cx="36" cy="38" r="4"/>
        <circle class="c-sv-dot" cx="106" cy="42" r="3"/>
    </svg>
    <h5>Aucun participant</h5>
    <p>Soyez le premier à gagner des points !</p>
</div>
<?php endif; ?>
