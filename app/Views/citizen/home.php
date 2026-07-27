<?php $pageTitle = 'Accueil'; $activeTab = 'home'; ?>
<style>
.home-map-wrap { position: relative; margin: -12px -16px 16px; }
#homeMap { width: 100%; height: 55vh; min-height: 280px; border-radius: 0; }
.home-map-filters { position: absolute; top: 12px; left: 12px; right: 12px; z-index: 500; }
.home-map-filters .c-chips { background: rgba(10,14,26,0.8); backdrop-filter: blur(12px); border-radius: 20px; padding: 6px 10px; border: 1px solid var(--c-glass-border); }
.home-quick-btn { position: fixed; bottom: calc(var(--c-nav-height) + 20px); right: 20px; width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, var(--c-accent), #8b5cf6); color: #fff; border: none; font-size: 1.3rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px var(--c-accent-glow); z-index: 600; cursor: pointer; transition: transform 0.2s; }
.home-quick-btn:active { transform: scale(0.9); }
.home-legend { display: flex; gap: 12px; padding: 8px 16px; overflow-x: auto; scrollbar-width: none; }
.home-legend::-webkit-scrollbar { display: none; }
.home-legend-item { display: flex; align-items: center; gap: 5px; font-size: 0.7rem; color: var(--c-text-muted); white-space: nowrap; }
.home-legend-dot { width: 8px; height: 8px; border-radius: 50%; }
</style>

<!-- KPIs -->
<div class="c-stats-row c-anim-fade">
    <div class="c-stat cyan">
        <div class="c-stat-value" data-count="<?= $total ?>">0</div>
        <div class="c-stat-label">Total</div>
    </div>
    <div class="c-stat accent">
        <div class="c-stat-value" data-count="<?= $inProgress ?>">0</div>
        <div class="c-stat-label">En cours</div>
    </div>
    <div class="c-stat green">
        <div class="c-stat-value" data-count="<?= $resolved ?>">0</div>
        <div class="c-stat-label">Résolus</div>
    </div>
    <div class="c-stat amber">
        <div class="c-stat-value" data-count="<?= $stats['points'] ?? 0 ?>">0</div>
        <div class="c-stat-label">Points</div>
    </div>
</div>

<!-- Map -->
<div class="home-map-wrap c-anim-slide c-delay-1">
    <div id="homeMap"></div>
    <div class="home-map-filters">
        <div class="c-chips" id="homeCatFilter">
            <button class="c-chip active" data-cat="all"><i class="fas fa-layer-group"></i>Tout</button>
            <?php foreach ($categories as $cat): ?>
            <button class="c-chip" data-cat="<?= $cat['id'] ?>" data-color="<?= $cat['color'] ?>">
                <i class="fas <?= $cat['icon'] ?>"></i><?= $cat['name'] ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="home-legend c-anim-fade c-delay-2">
    <div class="home-legend-item"><div class="home-legend-dot" style="background:#f59e0b;"></div>En attente</div>
    <div class="home-legend-item"><div class="home-legend-dot" style="background:#06b6d4;"></div>En cours</div>
    <div class="home-legend-item"><div class="home-legend-dot" style="background:#22c55e;"></div>Résolu</div>
    <div class="home-legend-item"><div class="home-legend-dot" style="background:#6366f1;"></div>Autre</div>
</div>

<!-- Nearby reports -->
<div class="c-section-title c-anim-fade c-delay-3" style="margin-top:16px;">
    <h6><i class="fas fa-clock-rotate-left"></i> Signalements récents</h6>
    <a href="/reports">Voir tout →</a>
</div>

<?php if (empty($recentReports)): ?>
<div class="c-empty c-anim-fade c-delay-4">
    <i class="fas fa-inbox"></i>
    <h5>Aucun signalement</h5>
    <p>Soyez le premier à signaler un problème dans votre quartier !</p>
    <a href="/reports/create" class="c-btn c-btn-primary">
        <i class="fas fa-plus"></i> Signaler maintenant
    </a>
</div>
<?php else: ?>
<div id="recentList" class="c-anim-fade c-delay-4">
    <?php foreach ($recentReports as $i => $r):
        $statusClass = ($r['status'] === 'resolved' || $r['status'] === 'validated') ? 'resolved' :
                       (($r['status'] === 'submitted') ? 'submitted' :
                       (($r['status'] === 'in_progress') ? 'in_progress' : 'submitted'));
        $statusLabel = ['submitted'=>'Soumis','acknowledged'=>'Pris en compte','assigned'=>'Assigné','in_progress'=>'En cours',
            'pending_review'=>'En revue','pending_unite'=>'Valid. unite','validated'=>'Validé','resolved'=>'Résolu','closed'=>'Fermé'];
    ?>
    <a href="/reports/<?= $r['id'] ?>" class="c-report-card" style="animation-delay:<?= $i * 0.04 ?>s;text-decoration:none;color:inherit;">
        <div class="c-report-icon" style="background:<?= $r['category_color'] ?? 'var(--c-accent)' ?>20;color:<?= $r['category_color'] ?? 'var(--c-accent)' ?>;">
            <i class="fas <?= $r['category_icon'] ?? 'fa-flag' ?>"></i>
        </div>
        <div class="c-report-body">
            <div class="c-report-title"><?= htmlspecialchars($r['title']) ?></div>
            <div class="c-report-meta">
                <span><i class="fas fa-location-dot"></i> <?= htmlspecialchars($r['commune_name']) ?></span>
                <span>&middot;</span>
                <span><i class="fas fa-clock"></i> <?= \App\Helpers\Helper::timeAgo($r['created_at']) ?></span>
            </div>
        </div>
        <div class="c-report-right">
            <span class="c-badge <?= $statusClass ?>"><?= $statusLabel[$r['status']] ?? $r['status'] ?></span>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- FAB -->
<a href="/reports/create" class="home-quick-btn" title="Signaler">
    <i class="fas fa-plus"></i>
</a>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var mapData = <?= json_encode(array_map(function($r) {
        return ['id'=>$r['id'],'title'=>$r['title'],'status'=>$r['status'],'latitude'=>$r['latitude'],'longitude'=>$r['longitude'],'category_name'=>$r['category_name'],'category_icon'=>$r['category_icon'],'category_color'=>$r['category_color'],'commune_name'=>$r['commune_name']];
    }, $mapReports)) ?>;

    var map = CMap.init('homeMap', mapData);

    // Category filter
    var filterChips = document.querySelectorAll('#homeCatFilter .c-chip');
    filterChips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            filterChips.forEach(function(c) { c.classList.remove('active'); });
            this.classList.add('active');
            var catId = this.getAttribute('data-cat');
            // Re-init map with filtered data
            var filtered = catId === 'all' ? mapData : mapData.filter(function(r) {
                return String(r.category_id || '') === catId || String(r.category_name || '') === catId;
            });
            map.remove();
            map = CMap.init('homeMap', filtered);
        });
    });
});
</script>
