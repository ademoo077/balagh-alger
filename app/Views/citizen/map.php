<?php $pageTitle = 'Carte'; $activeTab = 'map';
$db = \App\Helpers\Database::getConnection();
$categories = $db->query("SELECT id, name, icon, color FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();
$stats = $db->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='submitted' THEN 1 ELSE 0 END) as submitted,
    SUM(CASE WHEN status='in_progress' OR status='assigned' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status='resolved' OR status='validated' THEN 1 ELSE 0 END) as resolved
    FROM reports WHERE deleted_at IS NULL AND latitude IS NOT NULL")->fetch();
?>
<style>
.map-filters-wrap {
    position: absolute; top: 0; left: 0; right: 0; z-index: 500;
    padding: 10px; pointer-events: none;
}
.map-filters-wrap > * { pointer-events: auto; }
.map-filter-bar {
    background: rgba(10,14,26,0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;
    padding: 8px 10px; max-height: calc(100dvh - var(--c-header-height) - var(--c-nav-height) - 80px);
    overflow-y: auto; scrollbar-width: none;
}
.map-filter-bar::-webkit-scrollbar { display: none; }
.map-filter-section { margin-bottom: 6px; }
.map-filter-label { font-size: 0.65rem; font-weight: 700; color: var(--c-text-muted); text-transform: uppercase; letter-spacing: 0.05em; padding: 0 4px 4px; }
.map-chips { display: flex; gap: 5px; overflow-x: auto; scrollbar-width: none; padding-bottom: 2px; }
.map-chips::-webkit-scrollbar { display: none; }
.map-chip {
    flex-shrink: 0; display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.04);
    color: var(--c-text-secondary); font-size: 0.72rem; font-weight: 500;
    cursor: pointer; transition: all 0.2s; white-space: nowrap;
    -webkit-tap-highlight-color: transparent;
}
.map-chip:active { transform: scale(0.95); }
.map-chip.active {
    background: var(--c-accent); border-color: var(--c-accent);
    color: #fff; box-shadow: 0 2px 10px rgba(99,102,241,0.3);
}
.map-chip .dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.map-filter-toggle {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    width: 100%; padding: 8px; border-radius: 12px;
    background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);
    color: var(--c-text-secondary); font-size: 0.75rem; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
}
.map-filter-toggle:active { transform: scale(0.97); }
.map-filter-toggle .count-badge {
    background: var(--c-accent); color: #fff; border-radius: 10px;
    padding: 1px 7px; font-size: 0.65rem; font-weight: 700;
}
.map-legend {
    position: absolute; bottom: 12px; left: 10px; right: 10px; z-index: 500;
    display: flex; gap: 6px; flex-wrap: wrap; align-items: center;
}
.map-legend-item {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 10px; border-radius: 20px;
    background: rgba(10,14,26,0.85); backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.08);
    font-size: 0.68rem; font-weight: 500; color: var(--c-text-secondary);
}
.map-legend-dot { width: 8px; height: 8px; border-radius: 50%; }
.map-stats-bar {
    position: absolute; bottom: 50px; left: 10px; right: 10px; z-index: 500;
    display: none; /* shown when filters active */
}
.map-stats-inner {
    display: flex; gap: 6px; justify-content: center;
    background: rgba(10,14,26,0.88); backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,0.08); border-radius: 14px;
    padding: 8px 12px;
}
.map-stat-pill {
    display: flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 10px;
    background: rgba(255,255,255,0.05);
    font-size: 0.7rem; font-weight: 600;
}
.map-stat-pill .num { font-weight: 800; }
.map-locate-btn {
    position: absolute; bottom: 90px; right: 12px; z-index: 500;
    width: 44px; height: 44px; border-radius: 50%;
    background: rgba(10,14,26,0.88); backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.1);
    color: var(--c-accent); font-size: 1.1rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.2s;
    box-shadow: 0 2px 12px rgba(0,0,0,0.2);
}
.map-locate-btn:active { transform: scale(0.9); background: var(--c-accent); color: #fff; }

/* Leaflet popup overrides */
.leaflet-popup-content-wrapper {
    background: var(--c-card) !important; color: var(--c-text) !important;
    border: 1px solid var(--c-card-border) !important;
    border-radius: 14px !important; box-shadow: var(--c-shadow-lg) !important;
    padding: 0 !important;
}
.leaflet-popup-content { margin: 0 !important; padding: 14px !important; min-width: 200px; }
.leaflet-popup-tip { background: var(--c-card) !important; border: 1px solid var(--c-card-border) !important; }
.leaflet-popup-close-button { color: var(--c-text-muted) !important; font-size: 18px !important; top: 8px !important; right: 8px !important; }
.popup-card {}
.popup-card .pop-title { font-weight: 700; font-size: 0.88rem; margin-bottom: 4px; line-height: 1.3; }
.popup-card .pop-meta { display: flex; align-items: center; gap: 6px; font-size: 0.72rem; color: var(--c-text-muted); margin-bottom: 8px; flex-wrap: wrap; }
.popup-card .pop-badge { display: inline-flex; align-items: center; gap: 3px; padding: 2px 8px; border-radius: 8px; font-size: 0.65rem; font-weight: 600; }
.popup-card .pop-cat { display: inline-flex; align-items: center; gap: 4px; font-size: 0.72rem; color: var(--c-text-secondary); }
.popup-card .pop-link {
    display: inline-flex; align-items: center; gap: 4px; margin-top: 8px;
    padding: 6px 14px; border-radius: 10px;
    background: var(--c-accent); color: #fff;
    font-size: 0.75rem; font-weight: 600; text-decoration: none;
    transition: all 0.2s;
}
.popup-card .pop-link:active { transform: scale(0.95); }
</style>

<div class="c-container-full" style="margin:-12px -16px;position:relative;">
    <div id="fullMap" style="width:100%;height:calc(100dvh - var(--c-header-height) - var(--c-nav-height));"></div>

    <!-- Filters -->
    <div class="map-filters-wrap">
        <div class="map-filter-bar" id="mapFilterBar">
            <!-- Toggle button -->
            <div class="map-filter-toggle" id="mapFilterToggle">
                <i class="fas fa-sliders"></i>
                <span>Filtres</span>
                <span class="count-badge" id="filterCountBadge" style="display:none;">0</span>
                <i class="fas fa-chevron-down" id="filterChevron" style="font-size:0.6rem;margin-left:auto;transition:transform 0.2s;"></i>
            </div>

            <!-- Filter panels (collapsible) -->
            <div id="mapFilterPanels" style="display:none;margin-top:8px;">
                <!-- Categories -->
                <div class="map-filter-section">
                    <div class="map-filter-label">Catégorie</div>
                    <div class="map-chips" id="mapCatFilter">
                        <button class="map-chip active" data-cat="all"><i class="fas fa-layer-group"></i> Tout</button>
                        <?php foreach ($categories as $cat): ?>
                        <button class="map-chip" data-cat="<?= $cat['id'] ?>">
                            <span class="dot" style="background:<?= $cat['color'] ?>;"></span>
                            <?= htmlspecialchars($cat['name']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Status -->
                <div class="map-filter-section">
                    <div class="map-filter-label">Statut</div>
                    <div class="map-chips" id="mapStatusFilter">
                        <button class="map-chip active" data-status="all">Tous</button>
                        <button class="map-chip" data-status="submitted"><span class="dot" style="background:#f59e0b;"></span> Soumis</button>
                        <button class="map-chip" data-status="in_progress"><span class="dot" style="background:#06b6d4;"></span> En cours</button>
                        <button class="map-chip" data-status="resolved"><span class="dot" style="background:#22c55e;"></span> Résolu</button>
                        <button class="map-chip" data-status="rejected"><span class="dot" style="background:#ef4444;"></span> Rejeté</button>
                    </div>
                </div>

                <!-- Date -->
                <div class="map-filter-section">
                    <div class="map-filter-label">Période</div>
                    <div class="map-chips" id="mapDateFilter">
                        <button class="map-chip active" data-date="all"><i class="fas fa-calendar"></i> Tout</button>
                        <button class="map-chip" data-date="today"><i class="fas fa-clock"></i> Aujourd'hui</button>
                        <button class="map-chip" data-date="week"><i class="fas fa-calendar-week"></i> 7 jours</button>
                        <button class="map-chip" data-date="month"><i class="fas fa-calendar-alt"></i> 30 jours</button>
                    </div>
                </div>

                <!-- Reset -->
                <button class="map-chip" id="mapResetFilters" style="width:100%;justify-content:center;margin-top:4px;color:var(--c-red);">
                    <i class="fas fa-rotate-left"></i> Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="map-legend">
        <span class="map-legend-item"><span class="map-legend-dot" style="background:#f59e0b;"></span> Soumis</span>
        <span class="map-legend-item"><span class="map-legend-dot" style="background:#06b6d4;"></span> En cours</span>
        <span class="map-legend-item"><span class="map-legend-dot" style="background:#22c55e;"></span> Résolu</span>
        <span class="map-legend-item"><span class="map-legend-dot" style="background:#ef4444;"></span> Rejeté</span>
    </div>

    <!-- Stats bar -->
    <div class="map-stats-bar" id="mapStatsBar">
        <div class="map-stats-inner">
            <div class="map-stat-pill" style="color:var(--c-text);"><span class="num" id="mapStatTotal"><?= $stats['total'] ?></span> total</div>
            <div class="map-stat-pill" style="color:#f59e0b;"><span class="num" id="mapStatSubmitted"><?= $stats['submitted'] ?></span> soumis</div>
            <div class="map-stat-pill" style="color:#06b6d4;"><span class="num" id="mapStatProgress"><?= $stats['in_progress'] ?></span> en cours</div>
            <div class="map-stat-pill" style="color:#22c55e;"><span class="num" id="mapStatResolved"><?= $stats['resolved'] ?></span> résolu</div>
        </div>
    </div>

    <!-- Locate button -->
    <button class="map-locate-btn" id="mapLocateBtn" title="Ma position">
        <i class="fas fa-crosshairs"></i>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Filter toggle
    var filterPanels = document.getElementById('mapFilterPanels');
    var filterChevron = document.getElementById('filterChevron');
    document.getElementById('mapFilterToggle').addEventListener('click', function() {
        var open = filterPanels.style.display !== 'none';
        filterPanels.style.display = open ? 'none' : 'block';
        filterChevron.style.transform = open ? '' : 'rotate(180deg)';
    });

    fetch('/api/reports/map').then(function(r) { return r.json(); }).then(function(data) {
        var allReports = data.reports || data;

        // Init map
        var map = L.map('fullMap', { zoomControl: false, attributionControl: false }).setView([36.7538, 3.0588], 12);
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
                html: '<div style="width:30px;height:30px;border-radius:50%;background:' + color + ';border:3px solid rgba(255,255,255,0.9);box-shadow:0 2px 10px ' + color + '66;display:flex;align-items:center;justify-content:center;transition:transform 0.2s;">' +
                    '<i class="fas ' + (r.category_icon || 'fa-flag') + '" style="font-size:11px;color:#fff;"></i></div>',
                iconSize: [30, 30], iconAnchor: [15, 15]
            });
            var marker = L.marker([r.latitude, r.longitude], { icon: icon });

            var catName = r.category_name || '';
            var catColor = r.category_color || '#6366f1';
            var commune = r.commune_name || '';
            var sl = statusLabels[r.status] || r.status;
            var sc = getStatusColor(r);
            var date = r.created_at ? new Date(r.created_at).toLocaleDateString('fr-FR') : '';

            var popupHtml = '<div class="popup-card">' +
                '<div class="pop-title">' + (r.title || 'Signalement #' + r.id) + '</div>' +
                '<div class="pop-meta">' +
                    '<span class="pop-cat"><i class="fas ' + (r.category_icon || 'fa-flag') + '" style="color:' + catColor + ';"></i> ' + catName + '</span>' +
                    (commune ? '<span>&middot; ' + commune + '</span>' : '') +
                    (date ? '<span>&middot; ' + date + '</span>' : '') +
                '</div>' +
                '<span class="pop-badge" style="background:' + sc + '22;color:' + sc + ';">' + sl + '</span>' +
                '<br><a href="/reports/' + r.id + '" class="pop-link"><i class="fas fa-arrow-right"></i> Voir le détail</a>' +
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
            // Update stats
            document.getElementById('mapStatTotal').textContent = reports.length;
            var counts = { submitted: 0, in_progress: 0, resolved: 0 };
            reports.forEach(function(r) {
                if (r.status === 'submitted' || r.status === 'acknowledged') counts.submitted++;
                else if (r.status === 'in_progress' || r.status === 'assigned') counts.in_progress++;
                else if (r.status === 'resolved' || r.status === 'validated') counts.resolved++;
            });
            document.getElementById('mapStatSubmitted').textContent = counts.submitted;
            document.getElementById('mapStatProgress').textContent = counts.in_progress;
            document.getElementById('mapStatResolved').textContent = counts.resolved;
        }

        // Initial render
        renderMarkers(allReports);

        // Filters
        var activeCat = 'all', activeStatus = 'all', activeDate = 'all';

        function filterReports() {
            var now = Date.now();
            return allReports.filter(function(r) {
                if (activeCat !== 'all' && String(r.category_id) !== activeCat) return false;
                if (activeStatus !== 'all' && r.status !== activeStatus) return false;
                if (activeDate !== 'all' && r.created_at) {
                    var diff = now - new Date(r.created_at).getTime();
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
            var badge = document.getElementById('filterCountBadge');
            var statsBar = document.getElementById('mapStatsBar');
            if (count > 0) {
                badge.style.display = 'inline';
                badge.textContent = count;
                statsBar.style.display = 'block';
            } else {
                badge.style.display = 'none';
                statsBar.style.display = 'none';
            }
        }

        function applyFilters() {
            var filtered = filterReports();
            renderMarkers(filtered);
            updateFilterCount();
        }

        function setupChips(containerId, key) {
            document.querySelectorAll('#' + containerId + ' .map-chip').forEach(function(chip) {
                chip.addEventListener('click', function() {
                    document.querySelectorAll('#' + containerId + ' .map-chip').forEach(function(c) { c.classList.remove('active'); });
                    this.classList.add('active');
                    if (key === 'cat') activeCat = this.getAttribute('data-cat');
                    else if (key === 'status') activeStatus = this.getAttribute('data-status');
                    else if (key === 'date') activeDate = this.getAttribute('data-date');
                    applyFilters();
                });
            });
        }

        setupChips('mapCatFilter', 'cat');
        setupChips('mapStatusFilter', 'status');
        setupChips('mapDateFilter', 'date');

        // Reset
        document.getElementById('mapResetFilters').addEventListener('click', function() {
            activeCat = 'all'; activeStatus = 'all'; activeDate = 'all';
            document.querySelectorAll('.map-chip').forEach(function(c) { c.classList.remove('active'); });
            document.querySelectorAll('.map-chip[data-cat="all"], .map-chip[data-status="all"], .map-chip[data-date="all"]').forEach(function(c) { c.classList.add('active'); });
            applyFilters();
        });

        // Locate button
        document.getElementById('mapLocateBtn').addEventListener('click', function() {
            if (!navigator.geolocation) return;
            var btn = this;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            navigator.geolocation.getCurrentPosition(function(pos) {
                map.setView([pos.coords.latitude, pos.coords.longitude], 15);
                // Add user marker
                var userIcon = L.divIcon({
                    className: '',
                    html: '<div style="width:16px;height:16px;border-radius:50%;background:#6366f1;border:3px solid #fff;box-shadow:0 0 0 3px rgba(99,102,241,0.3),0 2px 8px rgba(0,0,0,0.3);"></div>',
                    iconSize: [16, 16], iconAnchor: [8, 8]
                });
                L.marker([pos.coords.latitude, pos.coords.longitude], { icon: userIcon }).addTo(map)
                    .bindPopup('<div style="font-weight:600;font-size:0.82rem;"><i class="fas fa-location-dot" style="color:var(--c-accent);"></i> Votre position</div>');
                btn.innerHTML = '<i class="fas fa-crosshairs"></i>';
            }, function() {
                btn.innerHTML = '<i class="fas fa-crosshairs"></i>';
            }, { enableHighAccuracy: true, timeout: 8000 });
        });
    });
});
</script>
