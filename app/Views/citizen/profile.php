<?php $pageTitle = 'Profil'; $activeTab = 'profile';
$userId = \App\Helpers\Session::getUserId();
$level = \App\Helpers\Gamification::getLevel($userId);
$badges = \App\Helpers\Badge::getUserBadges($userId);
$badgeDefs = \App\Helpers\Badge::getDefinitions();
$stats = \App\Helpers\Badge::getUserStats($userId);
$recentActivity = \App\Helpers\Gamification::getRecentActivity($userId, 10);
$user = $this->getUser();
?>
<style>
.profile-hero { background: linear-gradient(135deg, var(--c-accent), #8b5cf6, #06b6d4); border-radius: 0 0 24px 24px; padding: 24px 16px; margin: -12px -16px 16px; text-align: center; position: relative; overflow: hidden; }
.profile-hero::before { content:''; position:absolute; inset:0; background: radial-gradient(circle at 30% 20%, rgba(255,255,255,0.15), transparent 50%); }
.profile-avatar-wrap { position: relative; z-index: 1; display: inline-block; margin-bottom: 10px; }
.profile-avatar { width: 80px; height: 80px; border-radius: 50%; border: 4px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; color: #fff; }
.profile-name { position: relative; z-index: 1; font-weight: 800; font-size: 1.15rem; color: #fff; }
.profile-email { position: relative; z-index: 1; font-size: 0.78rem; color: rgba(255,255,255,0.7); margin-top: 2px; }
.profile-level-badge { position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; background: rgba(255,255,255,0.15); color: #fff; font-size: 0.78rem; font-weight: 600; margin-top: 10px; backdrop-filter: blur(8px); }
.activity-icon { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0; }
</style>

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
<style>
.idcard-section{margin-top:20px;margin-bottom:20px;}
.idcard-wrapper{perspective:1200px;margin:0 auto;max-width:580px}
.idcard{position:relative;width:100%;aspect-ratio:1.586/1;border-radius:14px;overflow:hidden;box-shadow:0 12px 48px rgba(0,0,0,0.3),0 2px 8px rgba(0,0,0,0.15);transition:transform 0.4s;transform-style:preserve-3d}
.idcard:hover{transform:rotateY(3deg) rotateX(1deg) scale(1.01)}
.idcard-front{position:absolute;inset:0;background:linear-gradient(165deg,#1e3a5f 0%,#0f2744 40%,#1a2f4a 100%);border-radius:14px;z-index:2}
.idcard-holo{position:absolute;top:0;right:0;width:120px;height:120px;background:radial-gradient(circle at 70% 30%,rgba(255,215,0,0.15),rgba(255,215,0,0.05) 40%,transparent 70%);pointer-events:none;z-index:10}
.idcard-holo::after{content:'';position:absolute;inset:0;background:repeating-linear-gradient(135deg,transparent,transparent 2px,rgba(255,255,255,0.02) 2px,rgba(255,255,255,0.02) 4px);z-index:1}
.idcard-stripe{position:absolute;bottom:0;left:0;right:0;height:6px;background:linear-gradient(90deg,#c0392b,#e74c3c,#f39c12,#27ae60,#2980b9,#8e44ad);z-index:10}
.idcard-header{display:flex;align-items:center;gap:0.8rem;padding:0.8rem 1rem;border-bottom:1px solid rgba(255,255,255,0.08);position:relative;z-index:5}
.idcard-logo{width:48px;height:48px;border-radius:50%;object-fit:contain;background:rgba(255,255,255,0.1);padding:3px;border:1.5px solid rgba(255,255,255,0.15)}
.idcard-header-text{flex:1}
.idcard-header-title{font-size:0.82rem;font-weight:700;color:#fff;letter-spacing:0.5px;text-transform:uppercase}
.idcard-header-sub{font-size:0.6rem;color:rgba(255,255,255,0.55);letter-spacing:1px;text-transform:uppercase}
.idcard-body{display:flex;gap:1rem;padding:0.8rem 1rem;position:relative;z-index:5}
.idcard-photo-wrap{flex-shrink:0;width:90px;height:110px;border-radius:8px;overflow:hidden;border:2px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center}
.idcard-photo{width:100%;height:100%;object-fit:cover}
.idcard-photo-letter{font-size:2.2rem;font-weight:700;color:rgba(255,255,255,0.6)}
.idcard-info{flex:1;display:flex;flex-direction:column;justify-content:center;gap:0.25rem;min-width:0}
.idcard-name{font-size:1.05rem;font-weight:700;color:#fff;letter-spacing:0.3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.idcard-email{font-size:0.68rem;color:rgba(255,255,255,0.5);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.idcard-details{display:flex;flex-direction:column;gap:0.2rem;margin-top:0.3rem}
.idcard-detail{display:flex;align-items:center;gap:0.4rem;font-size:0.65rem;color:rgba(255,255,255,0.6)}
.idcard-detail i{width:12px;font-size:0.6rem;color:rgba(255,255,255,0.35)}
.idcard-detail strong{color:rgba(255,255,255,0.85);font-weight:600}
.idcard-role-badge{display:inline-flex;align-items:center;gap:0.25rem;padding:0.15rem 0.5rem;border-radius:4px;background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.75);font-size:0.6rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-top:0.3rem;width:fit-content;border:1px solid rgba(255,255,255,0.08)}
.idcard-qr{position:absolute;bottom:0.8rem;right:1rem;z-index:5}
.idcard-qr canvas{border-radius:4px;background:#fff;padding:3px}
.idcard-qr-label{font-size:0.5rem;color:rgba(255,255,255,0.35);text-align:center;margin-top:2px;letter-spacing:0.5px}
.idcard-serial{position:absolute;bottom:0.8rem;left:1rem;font-size:0.55rem;color:rgba(255,255,255,0.25);letter-spacing:1.5px;font-family:'Courier New',monospace;z-index:5}
.idcard-micro{position:absolute;top:0;left:0;right:0;height:40px;background:repeating-linear-gradient(0deg,transparent,transparent 1px,rgba(255,255,255,0.015) 1px,rgba(255,255,255,0.015) 2px);z-index:1;pointer-events:none}
@media(max-width:576px){
    .idcard{border-radius:10px}
    .idcard-header{padding:0.6rem 0.8rem}
    .idcard-logo{width:38px;height:38px}
    .idcard-header-title{font-size:0.72rem}
    .idcard-body{padding:0.6rem 0.8rem;gap:0.7rem}
    .idcard-photo-wrap{width:72px;height:90px}
    .idcard-name{font-size:0.9rem}
    .idcard-qr{bottom:0.5rem;right:0.7rem}
    .idcard-qr canvas{width:55px!important;height:55px!important}
}
</style>

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
    <a href="/leaderboard" class="c-btn c-btn-outline c-btn-block" style="margin-bottom:8px;text-decoration:none;color:inherit;">
        <i class="fas fa-trophy"></i> Classement
    </a>
    <a href="/badges" class="c-btn c-btn-outline c-btn-block" style="margin-bottom:8px;text-decoration:none;color:inherit;">
        <i class="fas fa-award"></i> Badges & Récompenses
    </a>
    <a href="/citizen/map" class="c-btn c-btn-outline c-btn-block" style="margin-bottom:8px;text-decoration:none;color:inherit;">
        <i class="fas fa-map-location-dot"></i> Carte des signalements
    </a>
    <a href="/before-after" class="c-btn c-btn-outline c-btn-block" style="margin-bottom:8px;text-decoration:none;color:inherit;">
        <i class="fas fa-images"></i> Avant / Après
    </a>
    <a href="/my-profile/edit" class="c-btn c-btn-outline c-btn-block" style="margin-bottom:8px;text-decoration:none;color:inherit;">
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
