<?php $pageTitle = 'Accueil'; $activeTab = 'home';
$hour = (int)date('H');
$firstName = explode(' ', $userName)[0];
if ($hour < 12) { $greeting = 'Bonjour'; } elseif ($hour < 18) { $greeting = 'Bon après-midi'; } else { $greeting = 'Bonsoir'; }
$total = $stats['total'] ?? $total ?? 0;
$resolved = $stats['resolved'] ?? $resolved ?? 0;
$active = $stats['active'] ?? $inProgress ?? 0;
$rate = $total > 0 ? round(($resolved / $total) * 100) : 0;
$levelNum = $userLevel['number'] ?? 1;
$levelName = $userLevel['name'] ?? 'Citoyen';
$levelIcon = $userLevel['icon'] ?? 'fa-star';
$levelPoints = $userLevel['points'] ?? 0;
$levelProgress = $userLevel['progress'] ?? 0;
$nextLevel = $userLevel['next_level'] ?? '';
$nextMin = $userLevel['next_min'] ?? 0;
$wfSteps = ['submitted','acknowledged','assigned','in_progress','validated'];
$wfLabels = ['Soumis','Accusé','Assigné','En cours','Validé'];
?>
<style>
@keyframes heroGradientShift { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
@keyframes heroFloatOrb { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-18px) scale(1.06)} }
@keyframes particleRise { 0%{transform:translateY(0) rotate(0deg);opacity:0} 8%{opacity:0.7} 92%{opacity:0.7} 100%{transform:translateY(-420px) rotate(720deg);opacity:0} }
@keyframes ringDraw { from{stroke-dashoffset:var(--ring-circ)} to{stroke-dashoffset:var(--ring-target)} }
@keyframes ringPulse { 0%,100%{filter:drop-shadow(0 0 4px var(--ring-glow))} 50%{filter:drop-shadow(0 0 12px var(--ring-glow))} }
@keyframes catBarGrow { from{transform:scaleX(0)} to{transform:scaleX(1)} }
@keyframes fabPulse { 0%,100%{box-shadow:0 4px 20px rgba(99,102,241,0.35)} 50%{box-shadow:0 4px 32px rgba(99,102,241,0.55), 0 0 0 8px rgba(99,102,241,0.08)} }
@keyframes fadeSlideUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
@keyframes emptyIconBounce { 0%,100%{transform:scale(1);box-shadow:0 0 0 0 var(--c-accent-glow)} 50%{transform:scale(1.08);box-shadow:0 0 30px 10px var(--c-accent-glow)} }

.h-hero {
    position: relative; border-radius: 24px; overflow: hidden; margin-bottom: 20px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 25%, #06b6d4 50%, #10b981 75%, #6366f1 100%);
    background-size: 300% 300%; animation: heroGradientShift 8s ease infinite;
}
.h-hero::before {
    content: ''; position: absolute; inset: 0; pointer-events: none;
    background: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.12) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255,255,255,0.08) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(0,0,0,0.05) 0%, transparent 60%);
}
.h-hero::after {
    content: ''; position: absolute; top: -60%; right: -20%;
    width: 500px; height: 500px; border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
    animation: heroFloatOrb 6s ease-in-out infinite; pointer-events: none;
}
.h-particles { position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
.h-particles span {
    position: absolute; bottom: -20px; border-radius: 50%;
    background: rgba(255,255,255,0.07); animation: particleRise linear infinite;
}
.h-particles span:nth-child(1) { left: 10%; width: 6px; height: 6px; animation-duration: 12s; animation-delay: 0s; }
.h-particles span:nth-child(2) { left: 30%; width: 4px; height: 4px; animation-duration: 15s; animation-delay: 2s; }
.h-particles span:nth-child(3) { left: 55%; width: 8px; height: 8px; animation-duration: 10s; animation-delay: 4s; }
.h-particles span:nth-child(4) { left: 75%; width: 5px; height: 5px; animation-duration: 14s; animation-delay: 1s; }
.h-particles span:nth-child(5) { left: 90%; width: 7px; height: 7px; animation-duration: 11s; animation-delay: 3s; }
.h-hero-content { position: relative; z-index: 1; padding: 28px 22px 0; color: #fff; }
.h-hero .h-greeting { font-size: 1.7rem; font-weight: 800; margin-bottom: 2px; text-shadow: 0 2px 10px rgba(0,0,0,0.15); line-height: 1.2; }
.h-hero .h-greeting-name { background: linear-gradient(90deg, #fff 0%, rgba(255,255,255,0.75) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.h-hero .h-subtitle { opacity: 0.85; font-size: 0.88rem; margin-bottom: 16px; max-width: 400px; }
.h-level-pill {
    display: inline-flex; align-items: center; gap: 8px; padding: 7px 16px;
    border-radius: 50px; background: rgba(255,255,255,0.15); backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.25); font-size: 0.78rem; font-weight: 600;
    margin-bottom: 18px;
}
.h-level-pill i { font-size: 0.85rem; }
.h-level-pill .h-lp-pts { opacity: 0.75; font-weight: 500; }
.h-hero-stats {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px;
    background: rgba(255,255,255,0.12); border-radius: 16px 16px 0 0; overflow: hidden;
    margin: 0 -22px; padding: 0;
}
.h-hero-stat {
    background: rgba(255,255,255,0.06); backdrop-filter: blur(8px);
    padding: 14px 10px; text-align: center;
}
.h-hero-stat-val { font-size: 1.35rem; font-weight: 800; line-height: 1.1; }
.h-hero-stat-lbl { font-size: 0.68rem; opacity: 0.7; margin-top: 3px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }

.h-rings { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
.h-ring-card {
    background: var(--c-card); border: 1px solid var(--c-card-border);
    border-radius: 16px; padding: 18px 14px; text-align: center;
    position: relative; overflow: hidden;
}
.h-ring-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--ring-color-from), var(--ring-color-to));
    border-radius: 16px 16px 0 0;
}
.h-ring-card .h-ring-svg { display: block; margin: 0 auto 10px; }
.h-ring-card .h-ring-svg circle.ring-track { fill: none; stroke: var(--c-surface); stroke-width: 6; }
.h-ring-card .h-ring-svg circle.ring-fill {
    fill: none; stroke-width: 6; stroke-linecap: round;
    transform: rotate(-90deg); transform-origin: center;
    transition: stroke-dashoffset 1.2s cubic-bezier(0.22,1,0.36,1);
}
.h-ring-card .h-ring-label { font-size: 0.75rem; color: var(--c-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.h-ring-card .h-ring-title { font-size: 0.82rem; font-weight: 700; color: var(--c-text-primary); margin-top: 2px; }

.h-quick { display: grid; grid-template-columns: repeat(4,1fr); gap: 10px; margin-bottom: 20px; }
.h-quick-btn {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    padding: 14px 8px; border-radius: 14px;
    background: var(--c-card); border: 1px solid var(--c-card-border);
    text-decoration: none; color: inherit; cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
}
.h-quick-btn:active { transform: scale(0.94); }
.h-quick-btn:hover { transform: translateY(-3px); box-shadow: var(--c-shadow-md); }
.h-quick-icon {
    width: 44px; height: 44px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; position: relative;
}
.h-quick-label { font-size: 0.72rem; font-weight: 600; color: var(--c-text-secondary); }
.h-quick-badge {
    position: absolute; top: -3px; right: -3px;
    width: 16px; height: 16px; border-radius: 50%;
    background: var(--c-red); color: #fff; font-size: 0.55rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid var(--c-bg);
}

.h-cat-bar-wrap { margin-bottom: 20px; }
.h-cat-bar {
    display: flex; height: 10px; border-radius: 6px; overflow: hidden;
    background: var(--c-surface); margin-bottom: 12px;
}
.h-cat-bar-seg { transition: width 0.8s cubic-bezier(0.22,1,0.36,1); transform-origin: left; animation: catBarGrow 0.8s cubic-bezier(0.22,1,0.36,1) both; }
.h-cat-legend { display: flex; flex-wrap: wrap; gap: 10px 16px; }
.h-cat-legend-item { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: var(--c-text-secondary); }
.h-cat-legend-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.h-cat-legend-cnt { font-weight: 700; color: var(--c-text-primary); }

.h-timeline { margin-bottom: 20px; }
.h-timeline-scroll {
    display: flex; gap: 10px; overflow-x: auto; padding: 4px 0 8px;
    scrollbar-width: none;
}
.h-timeline-scroll::-webkit-scrollbar { display: none; }
.h-tl-card {
    flex-shrink: 0; width: 140px;
    background: var(--c-card); border: 1px solid var(--c-card-border);
    border-radius: 12px; padding: 12px; position: relative;
    overflow: hidden; transition: all 0.2s;
}
.h-tl-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: var(--tl-color, var(--c-accent)); }
.h-tl-card:hover { transform: translateY(-2px); box-shadow: var(--c-shadow-sm); }
.h-tl-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.68rem; margin-bottom: 8px; }
.h-tl-reason { font-size: 0.72rem; font-weight: 600; color: var(--c-text-primary); line-height: 1.3; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.h-tl-pts { font-size: 0.72rem; font-weight: 800; color: var(--c-green); }
.h-tl-time { font-size: 0.65rem; color: var(--c-text-muted); margin-top: 4px; }

.h-map-wrap { position: relative; margin: 0 -16px 20px; }
#homeMap { width: 100%; height: 55vh; min-height: 280px; }
.h-map-filters {
    position: absolute; top: 12px; left: 12px; right: 12px; z-index: 500;
}
.h-map-filters .c-chips {
    background: rgba(10,14,26,0.8); backdrop-filter: blur(12px);
    border-radius: 20px; padding: 6px 10px;
    border: 1px solid var(--c-glass-border);
}
.h-map-legend {
    position: absolute; bottom: 12px; left: 12px; right: 12px; z-index: 500;
    display: flex; gap: 6px; flex-wrap: wrap;
}
.h-map-legend-item {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 10px; border-radius: 20px;
    background: rgba(10,14,26,0.85); backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.08);
    font-size: 0.68rem; font-weight: 500; color: var(--c-text-secondary);
}
.h-map-legend-dot { width: 8px; height: 8px; border-radius: 50%; }

.h-report-item {
    position: relative; border-radius: 16px; background: var(--c-card);
    border: 1px solid var(--c-card-border); padding: 16px; margin-bottom: 12px;
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    text-decoration: none; display: block; color: inherit; overflow: hidden;
}
.h-report-item::before {
    content: ''; position: absolute; top: 0; left: 0; bottom: 0; width: 4px;
    border-radius: 4px 0 0 4px; background: var(--rp-color, var(--c-accent));
    transition: width 0.3s ease;
}
.h-report-item:hover { transform: translateY(-3px); box-shadow: var(--c-shadow-lg); border-color: transparent; }
.h-report-item:hover::before { width: 6px; }
.h-rp-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 8px; }
.h-rp-code { font-family: var(--font-mono, 'JetBrains Mono', monospace); font-weight: 800; font-size: 0.82rem; letter-spacing: 0.02em; }
.h-rp-title { font-size: 0.9rem; font-weight: 600; color: var(--c-text-primary); line-height: 1.4; margin-top: 3px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.h-rp-meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin: 8px 0; }
.h-rp-meta span { display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; color: var(--c-text-muted); }
.h-rp-meta i { font-size: 0.65rem; }
.h-rp-cat-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.9rem; flex-shrink: 0; position: relative;
}
.h-rp-cat-icon::after { content: ''; position: absolute; inset: -3px; border-radius: 13px; border: 2px solid; opacity: 0.2; }
.h-rp-right { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0; }
.h-rp-wf { margin-top: 12px; }
.h-rp-wf-bar { display: flex; gap: 3px; height: 4px; border-radius: 2px; overflow: hidden; background: var(--c-surface); }
.h-rp-wf-seg { flex: 1; border-radius: 2px; transition: background 0.5s ease; }
.h-rp-wf-labels { display: flex; justify-content: space-between; margin-top: 4px; }
.h-rp-wf-labels span { font-size: 0.58rem; color: var(--c-text-muted); font-weight: 500; }

.h-empty { text-align: center; padding: 48px 24px; }
.h-empty-icon {
    width: 88px; height: 88px; border-radius: 50%;
    background: linear-gradient(135deg, var(--c-accent-surface), var(--c-cyan-surface));
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; font-size: 2.2rem; color: var(--c-accent);
    animation: emptyIconBounce 3s ease-in-out infinite;
}
.h-empty h5 { font-weight: 700; margin-bottom: 6px; }
.h-empty p { font-size: 0.85rem; color: var(--c-text-muted); margin-bottom: 20px; line-height: 1.5; }

.h-fab {
    position: fixed; bottom: calc(var(--c-nav-height) + 20px); right: 20px;
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg, var(--c-accent), #8b5cf6);
    color: #fff; border: none; font-size: 1.3rem;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 20px var(--c-accent-glow); z-index: 600;
    cursor: pointer; transition: transform 0.2s;
    animation: fabPulse 3s ease-in-out infinite;
    text-decoration: none;
}
.h-fab:active { transform: scale(0.9); }

.h-section-hdr { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.h-section-hdr h6 { font-weight: 700; font-size: 0.92rem; margin: 0; display: flex; align-items: center; gap: 8px; }
.h-section-hdr h6 i { color: var(--c-accent); font-size: 0.85rem; }
.h-section-hdr a { font-size: 0.78rem; color: var(--c-accent); text-decoration: none; font-weight: 500; }

@media (max-width: 480px) {
    .h-hero .h-greeting { font-size: 1.35rem; }
    .h-hero-stats .h-hero-stat-val { font-size: 1.15rem; }
    .h-quick-icon { width: 40px; height: 40px; font-size: 1rem; }
    .h-tl-card { width: 125px; padding: 10px; }
}
</style>

<!-- ==================== 1. HERO ==================== -->
<div class="h-hero c-anim-fade">
    <div class="h-particles">
        <span></span><span></span><span></span><span></span><span></span>
    </div>
    <div class="h-hero-content">
        <div class="h-greeting"><?= $greeting ?>, <span class="h-greeting-name"><?= htmlspecialchars($firstName) ?></span></div>
        <div class="h-subtitle">Voici le résumé de votre activité citoyenne.</div>
        <div class="h-level-pill">
            <i class="fas <?= $levelIcon ?>"></i>
            <span>Niv. <?= $levelNum ?> — <?= htmlspecialchars($levelName) ?></span>
            <span class="h-lp-pts"><?= $levelPoints ?> pts</span>
        </div>
        <div class="h-hero-stats">
            <div class="h-hero-stat">
                <div class="h-hero-stat-val" data-count="<?= $total ?>">0</div>
                <div class="h-hero-stat-lbl">Total</div>
            </div>
            <div class="h-hero-stat">
                <div class="h-hero-stat-val" data-count="<?= $resolved ?>">0</div>
                <div class="h-hero-stat-lbl">Résolu</div>
            </div>
            <div class="h-hero-stat">
                <div class="h-hero-stat-val" data-count="<?= $levelPoints ?>">0</div>
                <div class="h-hero-stat-lbl">Points</div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== 2. RINGS ==================== -->
<div class="h-rings c-anim-slide c-delay-1">
    <!-- Resolution Ring -->
    <div class="h-ring-card" style="--ring-color-from:#22c55e;--ring-color-to:#06b6d4;--ring-glow:rgba(34,197,94,0.3);">
        <?php
        $rR = 36; $rC = 2 * M_PI * $rR; $rOff = $rC - ($rate / 100) * $rC;
        ?>
        <svg class="h-ring-svg" width="88" height="88" viewBox="0 0 88 88">
            <defs>
                <linearGradient id="gradRes" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#22c55e"/>
                    <stop offset="100%" stop-color="#06b6d4"/>
                </linearGradient>
            </defs>
            <circle class="ring-track" cx="44" cy="44" r="<?= $rR ?>"/>
            <circle class="ring-fill" cx="44" cy="44" r="<?= $rR ?>"
                stroke="url(#gradRes)"
                stroke-dasharray="<?= $rC ?>"
                stroke-dashoffset="<?= $rC ?>"
                data-target="<?= $rOff ?>"
                style="--ring-circ:<?= $rC ?>;--ring-target:<?= $rOff ?>;"/>
        </svg>
        <div class="h-ring-label">Taux de résolution</div>
        <div class="h-ring-title"><?= $rate ?>%</div>
    </div>
    <!-- Level Progress Ring -->
    <div class="h-ring-card" style="--ring-color-from:#6366f1;--ring-color-to:#a855f7;--ring-glow:rgba(99,102,241,0.3);">
        <?php
        $lR = 36; $lC = 2 * M_PI * $lR; $lOff = $lC - ($levelProgress / 100) * $lC;
        ?>
        <svg class="h-ring-svg" width="88" height="88" viewBox="0 0 88 88">
            <defs>
                <linearGradient id="gradLvl" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#6366f1"/>
                    <stop offset="100%" stop-color="#a855f7"/>
                </linearGradient>
            </defs>
            <circle class="ring-track" cx="44" cy="44" r="<?= $lR ?>"/>
            <circle class="ring-fill" cx="44" cy="44" r="<?= $lR ?>"
                stroke="url(#gradLvl)"
                stroke-dasharray="<?= $lC ?>"
                stroke-dashoffset="<?= $lC ?>"
                data-target="<?= $lOff ?>"
                style="--ring-circ:<?= $lC ?>;--ring-target:<?= $lOff ?>;"/>
        </svg>
        <div class="h-ring-label">Progression niveau</div>
        <div class="h-ring-title"><?= $levelProgress ?>%</div>
    </div>
</div>

<!-- ==================== 3. QUICK ACTIONS ==================== -->
<div class="h-quick c-anim-slide c-delay-2">
    <a href="/reports/create" class="h-quick-btn">
        <div class="h-quick-icon" style="background:var(--c-accent-surface);color:var(--c-accent);">
            <i class="fas fa-plus"></i>
        </div>
        <span class="h-quick-label">Signaler</span>
    </a>
    <a href="/map" class="h-quick-btn">
        <div class="h-quick-icon" style="background:var(--c-cyan-surface);color:var(--c-cyan);">
            <i class="fas fa-map-location-dot"></i>
        </div>
        <span class="h-quick-label">Carte</span>
    </a>
    <a href="/feed" class="h-quick-btn">
        <div class="h-quick-icon" style="background:var(--c-green-surface);color:var(--c-green);">
            <i class="fas fa-users"></i>
            <span class="h-quick-badge" id="hFeedBadge" style="display:none;">1</span>
        </div>
        <span class="h-quick-label">Communauté</span>
    </a>
    <a href="/leaderboard" class="h-quick-btn">
        <div class="h-quick-icon" style="background:var(--c-amber-surface);color:var(--c-amber);">
            <i class="fas fa-trophy"></i>
        </div>
        <span class="h-quick-label">Classement</span>
    </a>
</div>

<!-- ==================== 4. CATEGORY BREAKDOWN ==================== -->
<?php if (!empty($catBreakdown)): ?>
<div class="h-cat-bar-wrap c-anim-fade c-delay-3">
    <div class="h-section-hdr">
        <h6><i class="fas fa-chart-bar"></i> Répartition par catégorie</h6>
    </div>
    <div class="h-cat-bar" id="hCatBar">
        <?php foreach ($catBreakdown as $cb): ?>
        <div class="h-cat-bar-seg" style="width:0%;background:<?= $cb['color'] ?>;" data-pct="<?= $total > 0 ? round(($cb['cnt'] / $total) * 100, 1) : 0 ?>"></div>
        <?php endforeach; ?>
    </div>
    <div class="h-cat-legend">
        <?php foreach ($catBreakdown as $cb): ?>
        <div class="h-cat-legend-item">
            <span class="h-cat-legend-dot" style="background:<?= $cb['color'] ?>;"></span>
            <span><?= htmlspecialchars($cb['name']) ?></span>
            <span class="h-cat-legend-cnt"><?= $cb['cnt'] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ==================== 5. RECENT ACTIVITY TIMELINE ==================== -->
<?php if (!empty($recentActivity)): ?>
<div class="h-timeline c-anim-fade c-delay-3">
    <div class="h-section-hdr">
        <h6><i class="fas fa-clock-rotate-left"></i> Activité récente</h6>
    </div>
    <div class="h-timeline-scroll">
        <?php
        $actMap = [
            'report_created'  => ['icon'=>'fa-flag','color'=>'#f59e0b','label'=>'Créé'],
            'report_resolved' => ['icon'=>'fa-check','color'=>'#22c55e','label'=>'Résolu'],
            'comment_created' => ['icon'=>'fa-comment','color'=>'#a855f7','label'=>'Commenté'],
            'like_received'   => ['icon'=>'fa-heart','color'=>'#ef4444','label'=>'Like reçu'],
            'daily_login'     => ['icon'=>'fa-right-to-bracket','color'=>'#06b6d4','label'=>'Connexion'],
        ];
        foreach (array_slice($recentActivity, 0, 10) as $act):
            $am = $actMap[$act['reason']] ?? ['icon'=>'fa-circle','color'=>'#6366f1','label'=>$act['reason']];
        ?>
        <div class="h-tl-card" style="--tl-color:<?= $am['color'] ?>;">
            <div class="h-tl-icon" style="background:<?= $am['color'] ?>20;color:<?= $am['color'] ?>;">
                <i class="fas <?= $am['icon'] ?>"></i>
            </div>
            <div class="h-tl-reason"><?= $am['label'] ?></div>
            <?php if (!empty($act['points'])): ?>
            <div class="h-tl-pts">+<?= $act['points'] ?> pts</div>
            <?php endif; ?>
            <div class="h-tl-time"><?= \App\Helpers\Helper::timeAgo($act['created_at']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- ==================== 6. MAP ==================== -->
<div class="h-map-wrap c-anim-slide c-delay-4">
    <div id="homeMap"></div>
    <div class="h-map-filters">
        <div class="c-chips" id="homeCatFilter">
            <button class="c-chip active" data-cat="all"><i class="fas fa-layer-group"></i>Tout</button>
            <?php foreach ($categories as $cat): ?>
            <button class="c-chip" data-cat="<?= $cat['id'] ?>" data-color="<?= $cat['color'] ?>">
                <i class="fas <?= $cat['icon'] ?>"></i><?= htmlspecialchars($cat['name']) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="h-map-legend">
        <span class="h-map-legend-item"><span class="h-map-legend-dot" style="background:#f59e0b;"></span> Soumis</span>
        <span class="h-map-legend-item"><span class="h-map-legend-dot" style="background:#06b6d4;"></span> En cours</span>
        <span class="h-map-legend-item"><span class="h-map-legend-dot" style="background:#22c55e;"></span> Résolu</span>
        <span class="h-map-legend-item"><span class="h-map-legend-dot" style="background:#ef4444;"></span> Rejeté</span>
    </div>
</div>

<!-- ==================== 7. RECENT REPORTS ==================== -->
<div class="h-section-hdr c-anim-fade c-delay-5" style="margin-top:4px;">
    <h6><i class="fas fa-file-lines"></i> Signalements récents</h6>
    <a href="/reports">Voir tout →</a>
</div>

<?php if (empty($recentReports)): ?>
<!-- ==================== 8. EMPTY STATE ==================== -->
<div class="h-empty c-anim-fade c-delay-5">
    <div class="h-empty-icon"><i class="fas fa-inbox"></i></div>
    <h5>Aucun signalement</h5>
    <p>Soyez le premier à signaler un problème dans votre quartier et gagnez des points !</p>
    <a href="/reports/create" class="c-btn c-btn-primary" style="border-radius:14px;">
        <i class="fas fa-plus"></i> Signaler maintenant
    </a>
</div>
<?php else: ?>
<div id="recentList" class="c-anim-fade c-delay-5">
    <?php foreach ($recentReports as $i => $r):
        $statusLabel = [
            'submitted'=>'Soumis','acknowledged'=>'Accusé','assigned'=>'Assigné','in_progress'=>'En cours',
            'pending_review'=>'En revue','pending_unite'=>'Valid. unite','validated'=>'Validé','resolved'=>'Résolu','closed'=>'Fermé','rejected'=>'Rejeté'
        ];
        $statusClass = $r['status'];
        $catColor = $r['category_color'] ?? 'var(--c-accent)';
        $wfIdx = array_search($r['status'], $wfSteps);
        if ($wfIdx === false) $wfIdx = -1;
        $wfCompleted = $wfIdx + 1;
        $wfTotal = count($wfSteps);
    ?>
    <a href="/reports/<?= $r['id'] ?>" class="h-report-item" style="--rp-color:<?= $catColor ?>;animation-delay:<?= $i * 0.05 ?>s;text-decoration:none;color:inherit;">
        <div class="h-rp-top">
            <div style="flex:1;min-width:0;">
                <div class="h-rp-code" style="background:linear-gradient(135deg,<?= $catColor ?>,color-mix(in srgb,<?= $catColor ?> 70%,white));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                    <?= htmlspecialchars($r['tracking_code'] ?? '#'.$r['id']) ?>
                </div>
                <div class="h-rp-title"><?= htmlspecialchars($r['title']) ?></div>
            </div>
            <div class="h-rp-right">
                <div class="h-rp-cat-icon" style="background:<?= $catColor ?>15;color:<?= $catColor ?>;">
                    <i class="fas <?= $r['category_icon'] ?? 'fa-flag' ?>"></i>
                    <span style="position:absolute;inset:-3px;border-radius:13px;border:2px solid;opacity:0.2;border-color:<?= $catColor ?>;"></span>
                </div>
                <span class="c-badge <?= $statusClass ?>" style="font-size:0.65rem;padding:3px 10px;border-radius:8px;font-weight:700;">
                    <?= $statusLabel[$r['status']] ?? $r['status'] ?>
                </span>
            </div>
        </div>
        <div class="h-rp-meta">
            <span><i class="fas fa-location-dot" style="color:<?= $catColor ?>;opacity:0.7;"></i> <?= htmlspecialchars($r['commune_name'] ?? '') ?></span>
            <span><i class="fas fa-folder" style="color:<?= $catColor ?>;opacity:0.7;"></i> <?= htmlspecialchars($r['category_name'] ?? '') ?></span>
            <span><i class="fas fa-clock" style="color:<?= $catColor ?>;opacity:0.7;"></i> <?= \App\Helpers\Helper::timeAgo($r['created_at']) ?></span>
        </div>
        <div class="h-rp-wf">
            <div class="h-rp-wf-bar">
                <?php for ($s = 0; $s < $wfTotal; $s++): ?>
                <div class="h-rp-wf-seg" style="background:<?= $s < $wfCompleted ? $catColor : '' ?>;opacity:<?= $s < $wfCompleted ? '1' : '0.15' ?>;"></div>
                <?php endfor; ?>
            </div>
            <div class="h-rp-wf-labels">
                <span><?= $wfLabels[0] ?></span>
                <span><?= $wfLabels[$wfTotal - 1] ?></span>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ==================== 9. FAB ==================== -->
<a href="/reports/create" class="h-fab" title="Signaler un problème">
    <i class="fas fa-plus"></i>
</a>

<!-- ==================== 10. JAVASCRIPT ==================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    /* --- SVG Ring Animation --- */
    var rings = document.querySelectorAll('.ring-fill');
    setTimeout(function() {
        rings.forEach(function(ring) {
            var target = ring.getAttribute('data-target');
            if (target !== null) {
                ring.style.strokeDashoffset = target;
            }
        });
    }, 200);

    /* --- Category Bar Animation --- */
    var catSegs = document.querySelectorAll('.h-cat-bar-seg');
    setTimeout(function() {
        catSegs.forEach(function(seg) {
            var pct = parseFloat(seg.getAttribute('data-pct')) || 0;
            seg.style.width = pct + '%';
        });
    }, 400);

    /* --- Map with Layer Groups (no destroy/reinit) --- */
    var mapData = <?= json_encode(array_map(function($r) {
        return [
            'id' => $r['id'],
            'title' => $r['title'],
            'status' => $r['status'],
            'latitude' => $r['latitude'],
            'longitude' => $r['longitude'],
            'category_id' => $r['category_id'] ?? '',
            'category_name' => $r['category_name'] ?? '',
            'category_icon' => $r['category_icon'] ?? 'fa-flag',
            'category_color' => $r['category_color'] ?? '#6366f1',
            'commune_name' => $r['commune_name'] ?? ''
        ];
    }, $mapReports)) ?>;

    var statusColors = {
        submitted: '#f59e0b', acknowledged: '#6366f1', assigned: '#06b6d4',
        in_progress: '#06b6d4', pending_review: '#f59e0b', pending_unite: '#f59e0b',
        validated: '#22c55e', resolved: '#22c55e', closed: '#64748b', rejected: '#ef4444'
    };
    var statusLabels = {
        submitted: 'Soumis', acknowledged: 'Accusé', assigned: 'Assigné',
        in_progress: 'En cours', pending_review: 'En revue', pending_unite: 'En revue',
        validated: 'Validé', resolved: 'Résolu', closed: 'Fermé', rejected: 'Rejeté'
    };

    var map = L.map('homeMap', { zoomControl: false, attributionControl: false }).setView([36.7538, 3.0588], 12);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);
    L.control.zoom({ position: 'topright' }).addTo(map);
    var markersLayer = L.layerGroup().addTo(map);

    function getStatusColor(r) {
        return statusColors[r.status] || '#6366f1';
    }

    function createMarker(r) {
        if (!r.latitude || !r.longitude) return null;
        var color = getStatusColor(r);
        var icon = L.divIcon({
            className: '',
            html: '<div style="width:30px;height:30px;border-radius:50%;background:' + color + ';border:3px solid rgba(255,255,255,0.9);box-shadow:0 2px 10px ' + color + '66;display:flex;align-items:center;justify-content:center;">' +
                '<i class="fas ' + (r.category_icon || 'fa-flag') + '" style="font-size:11px;color:#fff;"></i></div>',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });
        var marker = L.marker([r.latitude, r.longitude], { icon: icon });
        var catColor = r.category_color || '#6366f1';
        var sl = statusLabels[r.status] || r.status;
        var sc = getStatusColor(r);
        var popupHtml = '<div style="font-family:Inter,sans-serif;min-width:180px;">' +
            '<div style="font-weight:700;font-size:0.85rem;margin-bottom:4px;">' + (r.title || 'Signalement #' + r.id) + '</div>' +
            '<div style="font-size:0.75rem;color:var(--c-text-muted);margin-bottom:6px;">' +
                '<i class="fas ' + (r.category_icon || 'fa-flag') + '" style="color:' + catColor + ';"></i> ' + (r.category_name || '') +
                (r.commune_name ? ' &middot; ' + r.commune_name : '') +
            '</div>' +
            '<span style="display:inline-block;padding:2px 8px;border-radius:8px;font-size:0.65rem;font-weight:600;background:' + sc + '22;color:' + sc + ';">' + sl + '</span>' +
            '<br><a href="/reports/' + r.id + '" style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;padding:6px 14px;border-radius:10px;background:var(--c-accent);color:#fff;font-size:0.75rem;font-weight:600;text-decoration:none;"><i class="fas fa-arrow-right"></i> Voir</a>' +
            '</div>';
        marker.bindPopup(popupHtml, { maxWidth: 280, closeButton: true });
        return marker;
    }

    function renderMarkers(reports) {
        markersLayer.clearLayers();
        reports.forEach(function(r) {
            var m = createMarker(r);
            if (m) markersLayer.addLayer(m);
        });
    }

    renderMarkers(mapData);

    /* Category filter with layer groups */
    var filterChips = document.querySelectorAll('#homeCatFilter .c-chip');
    filterChips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            filterChips.forEach(function(c) { c.classList.remove('active'); });
            this.classList.add('active');
            var catId = this.getAttribute('data-cat');
            var filtered = catId === 'all' ? mapData : mapData.filter(function(r) {
                return String(r.category_id) === catId;
            });
            renderMarkers(filtered);
        });
    });
});
</script>
