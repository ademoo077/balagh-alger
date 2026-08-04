<?php $pageTitle = 'Carte'; $activeTab = 'map';
$isRtl = ($currentLang ?? 'fr') === 'ar';
?>
<div class="c-container-full" style="margin:-12px -16px;position:relative;">
    <div id="fullMap" style="width:100%;height:calc(100dvh - var(--c-header-height) - var(--c-nav-height));"></div>

    <!-- Loading overlay -->
    <div class="map-loading" id="mapLoading">
        <i class="fas fa-spinner fa-spin"></i> Chargement...
    </div>
    <div class="map-error" id="mapError" style="display:none;">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Erreur de chargement de la carte</span>
        <button id="mapRetryBtn" class="map-chip" style="margin-top:6px;"><i class="fas fa-rotate-left"></i> Réessayer</button>
    </div>

    <!-- Filters -->
    <div class="map-filters-wrap">
        <div class="map-filter-bar" id="mapFilterBar">
            <div class="map-filter-toggle" id="mapFilterToggle">
                <i class="fas fa-sliders"></i>
                <span>Filtres</span>
                <span class="count-badge" id="filterCountBadge" style="display:none;">0</span>
                <i class="fas fa-chevron-down" id="filterChevron" style="font-size:0.6rem;margin-left:auto;transition:transform 0.2s;"></i>
            </div>
            <div id="mapFilterPanels" style="display:none;margin-top:8px;">
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
                <div class="map-filter-section">
                    <div class="map-filter-label">Période</div>
                    <div class="map-chips" id="mapDateFilter">
                        <button class="map-chip active" data-date="all"><i class="fas fa-calendar"></i> Tout</button>
                        <button class="map-chip" data-date="today"><i class="fas fa-clock"></i> Aujourd'hui</button>
                        <button class="map-chip" data-date="week"><i class="fas fa-calendar-week"></i> 7 jours</button>
                        <button class="map-chip" data-date="month"><i class="fas fa-calendar-alt"></i> 30 jours</button>
                    </div>
                </div>
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

    <!-- Stats bar (always visible) -->
    <div class="map-stats-bar" id="mapStatsBar">
        <div class="map-stats-inner">
            <div class="map-stat-pill" style="color:var(--c-text);"><span class="num" id="mapStatTotal">0</span> total</div>
            <div class="map-stat-pill" style="color:#f59e0b;"><span class="num" id="mapStatSubmitted">0</span> soumis</div>
            <div class="map-stat-pill" style="color:#06b6d4;"><span class="num" id="mapStatProgress">0</span> en cours</div>
            <div class="map-stat-pill" style="color:#22c55e;"><span class="num" id="mapStatResolved">0</span> résolu</div>
        </div>
    </div>

    <!-- Search -->
    <div class="map-search-box" id="mapSearchBox">
        <i class="fas fa-search map-search-icon"></i>
        <input type="text" id="mapSearchInput" placeholder="<?= $isRtl ? 'البحث عن عنوان...' : 'Chercher une adresse...' ?>" autocomplete="off">
        <div class="map-search-results" id="mapSearchResults"></div>
    </div>

    <button class="map-locate-btn" id="mapLocateBtn" title="Ma position">
        <i class="fas fa-crosshairs"></i>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var isRtl = <?= json_encode($isRtl) ?>;
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

    var loadingEl = document.getElementById('mapLoading');
    var errorEl = document.getElementById('mapError');
    var filterPanels = document.getElementById('mapFilterPanels');
    var filterChevron = document.getElementById('filterChevron');
    var statsBar = document.getElementById('mapStatsBar');

    // Tracked markers (locate + search) - only one of each at a time
    var locateMarker = null;
    var searchMarker = null;

    document.getElementById('mapFilterToggle').addEventListener('click', function() {
        var open = filterPanels.style.display !== 'none';
        filterPanels.style.display = open ? 'none' : 'block';
        filterChevron.style.transform = open ? '' : 'rotate(180deg)';
    });

    function loadMapData() {
        loadingEl.style.display = 'flex';
        errorEl.style.display = 'none';

        fetch('/api/reports/map').then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        }).then(function(data) {
            loadingEl.style.display = 'none';
            var allReports = Array.isArray(data) ? data : (data.reports || []);
            if (!allReports.length) {
                // Show empty state on map
                emptyMap();
                return;
            }
            initMap(allReports);
        }).catch(function(err) {
            loadingEl.style.display = 'none';
            errorEl.style.display = 'flex';
            console.error('Map load error:', err);
        });
    }

    function emptyMap() {
        statsBar.style.display = 'flex';
        document.getElementById('mapStatTotal').textContent = '0';
        document.getElementById('mapStatSubmitted').textContent = '0';
        document.getElementById('mapStatProgress').textContent = '0';
        document.getElementById('mapStatResolved').textContent = '0';
    }

    var mapCenter = <?= json_encode($mapCenter ?? [36.7538, 3.0588]) ?>;
    var map, markersLayer, allReports;

    function initMap(reports) {
        allReports = reports;

        map = L.map('fullMap', { zoomControl: false, attributionControl: false }).setView(mapCenter, 12);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);
        L.control.zoom({ position: 'topright' }).addTo(map);

        markersLayer = (typeof L.markerClusterGroup === 'function') ? L.markerClusterGroup({
            chunkedLoading: true,
            maxClusterRadius: 50,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            iconCreateFunction: function(cluster) {
                var count = cluster.getChildCount();
                var size = count < 10 ? 'small' : count < 50 ? 'medium' : 'large';
                return L.divIcon({
                    className: 'balagh-cluster balagh-cluster-' + size,
                    html: '<div><span>' + count + '</span></div>',
                    iconSize: L.point(40, 40)
                });
            }
        }).addTo(map) : L.layerGroup().addTo(map);

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
            var dirAttr = isRtl ? ' dir="rtl"' : '';

            marker.bindPopup(
                '<div class="popup-card"' + dirAttr + '>' +
                '<div class="pop-title">' + (r.title || 'Signalement #' + r.id) + '</div>' +
                '<div class="pop-meta">' +
                    '<span class="pop-cat"><i class="fas ' + (r.category_icon || 'fa-flag') + '" style="color:' + catColor + ';"></i> ' + catName + '</span>' +
                    (commune ? '<span>&middot; ' + commune + '</span>' : '') +
                    (date ? '<span>&middot; ' + date + '</span>' : '') +
                '</div>' +
                '<span class="pop-badge" style="background:' + sc + '22;color:' + sc + ';">' + sl + '</span>' +
                '<br><a href="/reports/' + r.id + '" class="pop-link"><i class="fas fa-arrow-'+ (isRtl ? 'left' : 'right') + '"></i> ' + (isRtl ? 'عرض التفاصيل' : 'Voir le détail') + '</a>' +
                '</div>',
                { maxWidth: 280, closeButton: true }
            );
            return marker;
        }

        function renderMarkers(reports) {
            markersLayer.clearLayers();
            reports.forEach(function(r) {
                var m = createMarker(r);
                if (m) markersLayer.addLayer(m);
            });
            // Always update stats
            statsBar.style.display = 'flex';
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

        renderMarkers(allReports);

        // ===== Filters =====
        var activeCat = 'all', activeStatus = 'all', activeDate = 'all';
        var filterCountBadge = document.getElementById('filterCountBadge');

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
            filterCountBadge.style.display = count > 0 ? 'inline' : 'none';
            filterCountBadge.textContent = count;
        }

        function applyFilters() {
            renderMarkers(filterReports());
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

        document.getElementById('mapResetFilters').addEventListener('click', function() {
            activeCat = 'all'; activeStatus = 'all'; activeDate = 'all';
            document.querySelectorAll('.map-chip').forEach(function(c) { c.classList.remove('active'); });
            document.querySelectorAll('.map-chip[data-cat="all"], .map-chip[data-status="all"], .map-chip[data-date="all"]').forEach(function(c) { c.classList.add('active'); });
            applyFilters();
        });

        // ===== Search =====
        var searchInput = document.getElementById('mapSearchInput');
        var searchResults = document.getElementById('mapSearchResults');
        var searchTimer;

        function makeUserMarkerIcon() {
            return L.divIcon({
                className: '',
                html: '<div style="width:16px;height:16px;border-radius:50%;background:#6366f1;border:3px solid #fff;box-shadow:0 0 0 3px rgba(99,102,241,0.3),0 2px 8px rgba(0,0,0,0.3);"></div>',
                iconSize: [16, 16], iconAnchor: [8, 8]
            });
        }

        function placeSearchMarker(lat, lon, label) {
            if (searchMarker) map.removeLayer(searchMarker);
            searchMarker = L.marker([lat, lon], { icon: makeUserMarkerIcon() }).addTo(map)
                .bindPopup('<div style="font-weight:600;font-size:0.82rem;">' + label + '</div>');
        }

        searchInput.addEventListener('input', function() {
            var q = this.value.trim();
            if (q.length < 3) { searchResults.style.display = 'none'; return; }
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=5&countrycodes=dz')
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        searchResults.innerHTML = '';
                        if (!data.length) {
                            searchResults.innerHTML = '<div class="map-search-result" style="color:var(--c-text-muted);cursor:default;">' + (isRtl ? 'لا توجد نتائج' : 'Aucun résultat') + '</div>';
                            searchResults.style.display = 'block';
                            return;
                        }
                        data.forEach(function(r) {
                            var div = document.createElement('div');
                            div.className = 'map-search-result';
                            div.innerHTML = '<i class="fas fa-location-dot"></i> ' + r.display_name.substring(0, 60);
                            div.addEventListener('click', function() {
                                map.setView([r.lat, r.lon], 15);
                                placeSearchMarker(r.lat, r.lon, r.display_name.substring(0, 80));
                                searchResults.style.display = 'none';
                                searchInput.value = r.display_name.substring(0, 60);
                            });
                            searchResults.appendChild(div);
                        });
                        searchResults.style.display = 'block';
                    }).catch(function() {});
            }, 400);
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#mapSearchBox')) searchResults.style.display = 'none';
        });

        // ===== Locate =====
        document.getElementById('mapLocateBtn').addEventListener('click', function() {
            if (!navigator.geolocation) return;
            var btn = this;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            navigator.geolocation.getCurrentPosition(function(pos) {
                map.setView([pos.coords.latitude, pos.coords.longitude], 15);
                if (locateMarker) map.removeLayer(locateMarker);
                locateMarker = L.marker([pos.coords.latitude, pos.coords.longitude], { icon: makeUserMarkerIcon() }).addTo(map)
                    .bindPopup('<div style="font-weight:600;font-size:0.82rem;"><i class="fas fa-location-dot" style="color:var(--c-accent);"></i> ' + (isRtl ? 'موقعك' : 'Votre position') + '</div>');
                btn.innerHTML = '<i class="fas fa-crosshairs"></i>';
            }, function() {
                btn.innerHTML = '<i class="fas fa-crosshairs"></i>';
            }, { enableHighAccuracy: true, timeout: 8000 });
        });
    }

    // Load on init
    loadMapData();

    // Retry button
    document.getElementById('mapRetryBtn').addEventListener('click', function() {
        loadMapData();
    });
});
</script>
