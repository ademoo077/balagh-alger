<?php $pageTitle = 'Carte'; $activeTab = '';
$db = \App\Helpers\Database::getConnection();
$categories = $db->query("SELECT id, name, icon, color FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();
?>
<div class="c-container-full" style="margin:-12px -16px;position:relative;">
    <div id="fullMap" style="width:100%;height:calc(100dvh - var(--c-header-height) - var(--c-nav-height));"></div>
    <div class="c-map-filters" style="position:absolute;top:12px;left:12px;right:12px;z-index:500;">
        <div class="c-chips" id="mapCatFilter" style="background:rgba(10,14,26,0.85);backdrop-filter:blur(12px);border-radius:20px;padding:6px 10px;border:1px solid var(--c-glass-border);">
            <button class="c-chip active" data-cat="all"><i class="fas fa-layer-group"></i>Tout</button>
            <?php foreach ($categories as $cat): ?>
            <button class="c-chip" data-cat="<?= $cat['id'] ?>">
                <i class="fas <?= $cat['icon'] ?>"></i><?= $cat['name'] ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Status legend -->
    <div style="position:absolute;bottom:12px;left:12px;right:12px;z-index:500;display:flex;gap:8px;flex-wrap:wrap;">
        <span class="c-badge submitted" style="font-size:0.65rem;">Soumis</span>
        <span class="c-badge in_progress" style="font-size:0.65rem;">En cours</span>
        <span class="c-badge resolved" style="font-size:0.65rem;">Résolu</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/reports/map').then(function(r) { return r.json(); }).then(function(data) {
        var reports = data.reports || data;
        var map = CMap.init('fullMap', reports);
        document.querySelectorAll('#mapCatFilter .c-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                document.querySelectorAll('#mapCatFilter .c-chip').forEach(function(c) { c.classList.remove('active'); });
                this.classList.add('active');
                var catId = this.getAttribute('data-cat');
                var filtered = catId === 'all' ? reports : reports.filter(function(r) { return String(r.category_id || r.category_name) === catId; });
                map.remove();
                map = CMap.init('fullMap', filtered);
            });
        });
    });
});
</script>
