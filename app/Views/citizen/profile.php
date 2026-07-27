<?php $pageTitle = 'Profil'; $activeTab = 'profile';
$userId = \App\Helpers\Session::getUserId();
$level = \App\Helpers\Gamification::getLevel($userId);
$badges = \App\Helpers\Badge::getUserBadges($userId);
$badgeDefs = \App\Helpers\Badge::getDefinitions();
$stats = \App\Helpers\Badge::getUserStats($userId);
$recentActivity = \App\Helpers\Gamification::getRecentActivity($userId, 10);
$user = $this->getUser();
?>

<!-- Hero -->
<div class="profile-hero">
    <div class="profile-avatar-wrap">
        <div class="profile-avatar"><?= strtoupper(substr(\App\Helpers\Session::getUserName(), 0, 1)) ?></div>
    </div>
    <div class="profile-name"><?= htmlspecialchars(\App\Helpers\Session::getUserName()) ?></div>
    <div class="profile-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
    <div class="profile-level-badge">
        <i class="fas <?= $level['icon'] ?>"></i>
        Niveau <?= $level['number'] ?> — <?= $level['name'] ?>
    </div>
</div>

<!-- Level progress -->
<div class="c-card c-anim-slide c-delay-1">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
        <span style="font-weight:700;font-size:0.88rem;"><?= $level['points'] ?? 0 ?> points</span>
        <?php if ($level['next_min']): ?>
        <span style="font-size:0.75rem;color:var(--c-text-muted);">Niveau <?= ($level['number'] ?? 1) + 1 ?> : <?= $level['next_min'] ?> pts</span>
        <?php endif; ?>
    </div>
    <div class="c-level-bar">
        <div class="c-level-fill" style="width:<?= $level['progress'] ?>%;"></div>
    </div>
</div>

<!-- Stats -->
<div class="c-stats-row c-anim-fade c-delay-2">
    <div class="c-stat cyan">
        <div class="c-stat-value"><?= $stats['total'] ?></div>
        <div class="c-stat-label">Signalements</div>
    </div>
    <div class="c-stat green">
        <div class="c-stat-value"><?= $stats['resolved'] ?></div>
        <div class="c-stat-label">Résolus</div>
    </div>
    <div class="c-stat amber">
        <div class="c-stat-value"><?= $stats['active'] ?></div>
        <div class="c-stat-label">En cours</div>
    </div>
    <div class="c-stat accent">
        <div class="c-stat-value"><?= $stats['rate'] ?>%</div>
        <div class="c-stat-label">Taux</div>
    </div>
</div>

<!-- Badges -->
<div class="c-section-title c-anim-fade c-delay-3">
    <h6><i class="fas fa-medal"></i> Badges</h6>
</div>
<div class="c-anim-fade c-delay-3" style="margin-bottom:20px;">
<?php if (empty($badges)): ?>
<div class="c-card-flat" style="text-align:center;padding:20px;">
    <i class="fas fa-award" style="font-size:1.5rem;color:var(--c-text-muted);opacity:0.5;"></i>
    <p style="font-size:0.82rem;color:var(--c-text-muted);margin:8px 0 0;">Pas encore de badges. Continuez à signaler !</p>
</div>
<?php else: ?>
<?php foreach ($badges as $b): ?>
<div class="c-badge-card">
    <div class="c-badge-icon" style="background:<?= $b['badge_color'] ?>;">
        <i class="fas <?= $b['badge_icon'] ?>"></i>
    </div>
    <div class="c-badge-info">
        <div class="c-badge-name"><?= htmlspecialchars($b['badge_name']) ?></div>
        <div class="c-badge-desc">Obtenu le <?= (new DateTime($b['earned_at']))->format('d/m/Y') ?></div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>

<!-- Locked badges -->
<div class="c-section-title c-anim-fade c-delay-4">
    <h6><i class="fas fa-lock"></i> Badges à débloquer</h6>
</div>
<div class="c-anim-fade c-delay-4" style="margin-bottom:20px;">
<?php
$earnedKeys = array_column($badges, 'badge_key');
foreach ($badgeDefs as $key => $def):
    if (in_array($key, $earnedKeys)) continue;
?>
<div class="c-badge-card locked">
    <div class="c-badge-icon" style="background:<?= $def['color'] ?>;">
        <i class="fas <?= $def['icon'] ?>"></i>
    </div>
    <div class="c-badge-info">
        <div class="c-badge-name"><?= htmlspecialchars($def['name']) ?></div>
        <div class="c-badge-desc"><?= htmlspecialchars($def['desc']) ?></div>
    </div>
</div>
<?php endforeach; ?>
</div>

<!-- Recent activity -->
<div class="c-section-title c-anim-fade c-delay-5">
    <h6><i class="fas fa-clock-rotate-left"></i> Activité récente</h6>
</div>
<div class="c-anim-fade c-delay-5">
<?php if (empty($recentActivity)): ?>
<div class="c-card-flat" style="text-align:center;padding:16px;">
    <p style="font-size:0.82rem;color:var(--c-text-muted);">Aucune activité récente</p>
</div>
<?php else: ?>
<?php foreach ($recentActivity as $act): ?>
<div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--c-card-border);">
    <div class="activity-icon" style="background:<?= $act['points'] > 0 ? 'var(--c-green-surface)' : 'var(--c-red-surface)' ?>;color:<?= $act['points'] > 0 ? 'var(--c-green)' : 'var(--c-red)' ?>;">
        <i class="fas fa-<?= $act['points'] > 0 ? 'plus' : 'minus' ?>"></i>
    </div>
    <div style="flex:1;">
        <div style="font-size:0.82rem;font-weight:500;"><?= htmlspecialchars($act['reason']) ?></div>
        <div style="font-size:0.7rem;color:var(--c-text-muted);"><?= \App\Helpers\Helper::timeAgo($act['created_at']) ?></div>
    </div>
    <div style="font-weight:700;font-size:0.85rem;color:<?= $act['points'] > 0 ? 'var(--c-green)' : 'var(--c-red)' ?>;">
        <?= $act['points'] > 0 ? '+' : '' ?><?= $act['points'] ?> pts
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>

<!-- Virtual ID Card -->

<div class="idcard-section c-anim-fade">
    <div class="c-section-title">
        <h6><i class="fas fa-id-card"></i> Carte d'identité virtuelle</h6>
    </div>
    <div class="idcard-wrapper">
        <div class="idcard" id="idCard">
            <div class="idcard-front">
                <div class="idcard-micro"></div>
                <div class="idcard-holo"></div>
                <div class="idcard-stripe"></div>
                <div class="idcard-header">
                    <img src="/assets/img/wilaya-logo.png" alt="Wilaya" class="idcard-logo">
                    <div class="idcard-header-text">
                        <div class="idcard-header-title">République Algérienne Démocratique et Populaire</div>
                        <div class="idcard-header-sub">Wilaya d'Alger — Balagh Alger</div>
                    </div>
                </div>
                <div class="idcard-body">
                    <div class="idcard-photo-wrap">
                        <?php $hasAvatar = !empty($user['avatar']) && $user['avatar'] !== 'default.png'; ?>
                        <?php if ($hasAvatar): ?>
                        <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Photo" class="idcard-photo">
                        <?php else: ?>
                        <div class="idcard-photo-letter"><?= strtoupper(substr($user['first_name'] ?? '?', 0, 1)) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="idcard-info">
                        <div class="idcard-name"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></div>
                        <div class="idcard-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
                        <div class="idcard-details">
                            <div class="idcard-detail"><i class="fas fa-shield-halved"></i> <strong>Citoyen</strong></div>
                            <?php if (!empty($user['phone'])): ?>
                            <div class="idcard-detail"><i class="fas fa-phone"></i> <?= htmlspecialchars($user['phone']) ?></div>
                            <?php endif; ?>
                            <div class="idcard-detail"><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($user['created_at'] ?? 'now')) ?></div>
                        </div>
                        <div class="idcard-role-badge"><i class="fas fa-id-badge"></i> Badge N° <?= str_pad($user['id'] ?? 0, 6, '0', STR_PAD_LEFT) ?></div>
                    </div>
                </div>
                <div class="idcard-qr">
                    <div id="idCardQr"></div>
                    <div class="idcard-qr-label">IDENTIFIANT</div>
                </div>
                <div class="idcard-serial">BA-<?= str_pad($user['id'] ?? 0, 6, '0', STR_PAD_LEFT) ?>-<?= date('Y') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Links -->
<div style="margin-top:20px;" class="c-anim-fade">
    <a href="/leaderboard" class="c-btn c-btn-outline c-btn-block c-profile-link">
        <i class="fas fa-trophy"></i> Classement
    </a>
    <a href="/badges" class="c-btn c-btn-outline c-btn-block c-profile-link">
        <i class="fas fa-award"></i> Badges & Récompenses
    </a>
    <a href="/citizen/map" class="c-btn c-btn-outline c-btn-block c-profile-link">
        <i class="fas fa-map-location-dot"></i> Carte des signalements
    </a>
    <a href="/before-after" class="c-btn c-btn-outline c-btn-block c-profile-link">
        <i class="fas fa-images"></i> Avant / Après
    </a>
    <a href="/my-profile/edit" class="c-btn c-btn-outline c-btn-block c-profile-link">
        <i class="fas fa-user-pen"></i> <?= __('common.edit_profile') ?>
    </a>
    <form method="POST" action="/logout" style="margin-top:12px;">
        <input type="hidden" name="_token" value="<?= \App\Helpers\Session::get('csrf_token') ?? \App\Helpers\Csrf::generate() ?>">
        <button type="submit" class="c-btn c-btn-block" style="background:var(--c-red-surface);color:var(--c-red);border:none;">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var qrContainer = document.getElementById('idCardQr');
    if (qrContainer) {
        new QRCode(qrContainer, {
            text: window.location.origin + '/suivi/BA-<?= str_pad($user['id'] ?? 0, 6, '0', STR_PAD_LEFT) ?>',
            width: 64,
            height: 64,
            colorDark: '#ffffff',
            colorLight: '#1e3a5f',
            correctLevel: QRCode.CorrectLevel.M
        });
    }
});
</script>
