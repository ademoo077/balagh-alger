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

<!-- ==================== 8. INSTALL APP BANNER ==================== -->
<div id="hInstallApp" class="h-install-app c-anim-slide c-delay-4">
    <div class="h-install-app-bg">
        <div class="h-install-app-orb h-install-app-orb-1"></div>
        <div class="h-install-app-orb h-install-app-orb-2"></div>
    </div>
    <div class="h-install-app-content">
        <div class="h-install-app-logo">
            <img src="/assets/img/icon-192.png" alt="Balagh" width="48" height="48">
        </div>
        <div class="h-install-app-text">
            <h4>Télécharger Balagh</h4>
            <p>Installez l'application sur votre écran d'accueil pour un accès rapide et des notifications push.</p>
        </div>
        <div class="h-install-app-features">
            <div class="h-install-app-feat">
                <i class="fas fa-bolt"></i>
                <span>Accès instantané</span>
            </div>
            <div class="h-install-app-feat">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </div>
            <div class="h-install-app-feat">
                <i class="fas fa-wifi-slash"></i>
                <span>Fonctionne hors-ligne</span>
            </div>
        </div>
        <div class="h-install-app-actions">
            <button id="hInstallBtn" class="h-install-app-btn">
                <i class="fas fa-download"></i> Installer l'application
            </button>
            <button id="hInstallDismiss" class="h-install-app-dismiss" title="Pas maintenant">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    </div>
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

    /* --- Install App Banner --- */
    (function() {
        var banner = document.getElementById('hInstallApp');
        var installBtn = document.getElementById('hInstallBtn');
        var dismissBtn = document.getElementById('hInstallDismiss');
        if (!banner || !installBtn || !dismissBtn) return;

        if (window.matchMedia('(display-mode: standalone)').matches ||
            localStorage.getItem('balagh-install-dismissed') ||
            localStorage.getItem('balagh-install-accepted')) {
            banner.style.display = 'none';
            return;
        }

        var deferredPrompt = null;

        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
        });

        installBtn.addEventListener('click', function() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function(result) {
                    if (result.outcome === 'accepted') {
                        installBtn.innerHTML = '<i class="fas fa-check"></i> Installé !';
                        installBtn.classList.add('installed');
                        localStorage.setItem('balagh-install-accepted', '1');
                        if (typeof CToast !== 'undefined') CToast.show('Balagh installé !', 'success');
                        setTimeout(function() { banner.style.display = 'none'; }, 2000);
                    }
                    deferredPrompt = null;
                });
            } else {
                var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                var isAndroid = /Android/.test(navigator.userAgent);
                var msg = '';
                if (isIOS) {
                    msg = 'Sur iPhone/iPad :\n\n1. Appuyez sur le bouton Partager ⬇️\n2. "Ajouter à l\'écran d\'accueil"\n3. Confirmez "Ajouter"';
                } else if (isAndroid) {
                    msg = 'Sur Android :\n\n1. Menu ⋮ → "Ajouter à l\'écran d\'accueil"\n2. Confirmez "Ajouter"';
                } else {
                    msg = 'Pour installer :\n\n• Chrome/Edge : icône + dans la barre d\'adresse\n• Firefox : Menu → Installer cette application\n• Safari : File → Ajouter au dossier Dock';
                }
                alert(msg);
            }
        });

        dismissBtn.addEventListener('click', function() {
            banner.style.display = 'none';
            localStorage.setItem('balagh-install-dismissed', '1');
        });

        window.addEventListener('appinstalled', function() {
            banner.style.display = 'none';
            deferredPrompt = null;
            localStorage.setItem('balagh-install-accepted', '1');
        });
    })();
});
</script>
