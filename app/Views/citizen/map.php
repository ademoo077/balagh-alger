<?php $pageTitle = 'Carte'; $activeTab = '';
$db = \App\Helpers\Database::getConnection();
$categories = $db->query("SELECT id, name, icon, color FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();
?>
<div class="c-container-full" style="margin:-12px -16px;position:relative;">
    <div id="fullMap" style="width:100%;height:calc(100dvh - var(--c-header-height) - var(--c-nav-height));"></div>

    <!-- Filters Panel -->
    <div class="c-map-filters" style="position:absolute;top:12px;left:12px;right:12px;z-index:500;">
        <!-- Category chips -->
        <div class="c-chips" id="mapCatFilter" style="background:rgba(10,14,26,0.85);backdrop-filter:blur(12px);border-radius:20px;padding:6px 10px;border:1px solid var(--c-glass-border);margin-bottom:8px;">
            <button class="c-chip active" data-cat="all"><i class="fas fa-layer-group"></i>Tout</button>
            <?php foreach ($categories as $cat): ?>
            <button class="c-chip" data-cat="<?= $cat['id'] ?>">
                <i class="fas <?= $cat['icon'] ?>"></i><?= $cat['name'] ?>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Status + Date filters row -->
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <!-- Status filter -->
            <div class="c-chips" id="mapStatusFilter" style="background:rgba(10,14,26,0.85);backdrop-filter:blur(12px);border-radius:20px;padding:6px 10px;border:1px solid var(--c-glass-border);">
                <button class="c-chip active" data-status="all"><i class="fas fa-circle-dot"></i>Tous</button>
                <button class="c-chip" data-status="submitted"><span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>Soumis</button>
                <button class="c-chip" data-status="in_progress"><span style="width:8px;height:8px;border-radius:50%;background:#06b6d4;display:inline-block;"></span>En cours</button>
                <button class="c-chip" data-status="resolved"><span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;"></span>Résolu</button>
                <button class="c-chip" data-status="rejected"><span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;"></span>Rejeté</button>
            </div>

            <!-- Date filter -->
            <div class="c-chips" id="mapDateFilter" style="background:rgba(10,14,26,0.85);backdrop-filter:blur(12px);border-radius:20px;padding:6px 10px;border:1px solid var(--c-glass-border);">
                <button class="c-chip active" data-date="all"><i class="fas fa-calendar"></i>Tout</button>
                <button class="c-chip" data-date="today"><i class="fas fa-clock"></i>Aujourd'hui</button>
                <button class="c-chip" data-date="week"><i class="fas fa-calendar-week"></i>7 jours</button>
                <button class="c-chip" data-date="month"><i class="fas fa-calendar-alt"></i>30 jours</button>
            </div>

            <!-- Active filter count -->
            <div id="mapFilterCount" style="display:none;background:var(--accent);color:#fff;border-radius:20px;padding:4px 12px;font-size:0.72rem;font-weight:700;align-self:center;cursor:pointer;" title="Réinitialiser les filtres">
                <i class="fas fa-filter me-1"></i><span id="mapFilterCountNum">0</span> filtres
            </div>
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
        var allReports = data.reports || data;
        var map = CMap.init('fullMap', allReports);
        var activeCat = 'all';
        var activeStatus = 'all';
        var activeDate = 'all';

        function getStatusFromTime(r) {
            if (r.status) return r.status;
            return 'submitted';
        }

        function filterReports() {
            var now = Date.now();
            return allReports.filter(function(r) {
                if (activeCat !== 'all' && String(r.category_id || r.category_name) !== activeCat) return false;
                if (activeStatus !== 'all' && getStatusFromTime(r) !== activeStatus) return false;
                if (activeDate !== 'all' && r.created_at) {
                    var created = new Date(r.created_at).getTime();
                    var diff = now - created;
                    if (activeDate === 'today' && diff > 86400000) return false;
                    if (activeDate === 'week' && diff > 604800000) return false;
                    if (activeDate === 'month' && diff > 2592000000) return false;
                }
                return true;
            });
        }

        function updateFilterCount() {
            var count = 0;
            if (activeCat !== 'all') count++;
            if (activeStatus !== 'all') count++;
            if (activeDate !== 'all') count++;
            var el = document.getElementById('mapFilterCount');
            var numEl = document.getElementById('mapFilterCountNum');
            if (count > 0) {
                el.style.display = 'inline-flex';
                numEl.textContent = count;
            } else {
                el.style.display = 'none';
            }
        }

        function refreshMap() {
            var filtered = filterReports();
            map.remove();
            map = CMap.init('fullMap', filtered);
            updateFilterCount();
        }

        document.querySelectorAll('#mapCatFilter .c-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                document.querySelectorAll('#mapCatFilter .c-chip').forEach(function(c) { c.classList.remove('active'); });
                this.classList.add('active');
                activeCat = this.getAttribute('data-cat');
                refreshMap();
            });
        });

        document.querySelectorAll('#mapStatusFilter .c-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                document.querySelectorAll('#mapStatusFilter .c-chip').forEach(function(c) { c.classList.remove('active'); });
                this.classList.add('active');
                activeStatus = this.getAttribute('data-status');
                refreshMap();
            });
        });

        document.querySelectorAll('#mapDateFilter .c-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                document.querySelectorAll('#mapDateFilter .c-chip').forEach(function(c) { c.classList.remove('active'); });
                this.classList.add('active');
                activeDate = this.getAttribute('data-date');
                refreshMap();
            });
        });

        document.getElementById('mapFilterCount').addEventListener('click', function() {
            activeCat = 'all';
            activeStatus = 'all';
            activeDate = 'all';
            document.querySelectorAll('.c-chip').forEach(function(c) { c.classList.remove('active'); });
            document.querySelectorAll('.c-chip[data-cat="all"], .c-chip[data-status="all"], .c-chip[data-date="all"]').forEach(function(c) { c.classList.add('active'); });
            refreshMap();
        });
    });
});
</script>
