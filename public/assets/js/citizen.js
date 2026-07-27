/* ============================================
   Balagh Alger — Citizen Interface JS
   ============================================ */

(function() {
    'use strict';

    // Toast notification
    window.CToast = {
        show: function(msg, type) {
            var t = document.getElementById('cToast');
            if (!t) return;
            t.textContent = msg;
            t.className = 'c-toast ' + (type || '') + ' show';
            clearTimeout(t._timer);
            t._timer = setTimeout(function() { t.className = 'c-toast'; }, 3000);
        }
    };

    // Theme toggle
    var themeBtn = document.getElementById('cThemeToggle');
    var sunIcon = document.getElementById('cThemeIconSun');
    var moonIcon = document.getElementById('cThemeIconMoon');
    function updateThemeIcons() {
        var isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        if (sunIcon) sunIcon.style.display = isDark ? 'inline' : 'none';
        if (moonIcon) moonIcon.style.display = isDark ? 'none' : 'inline';
    }
    if (themeBtn) {
        updateThemeIcons();
        themeBtn.addEventListener('click', function() {
            var current = document.documentElement.getAttribute('data-bs-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('balagh-theme', next);
            updateThemeIcons();
        });
    }

    // Language toggle
    var langBtn = document.getElementById('cLangToggle');
    if (langBtn) {
        langBtn.addEventListener('click', function() {
            var cur = document.documentElement.lang === 'ar' ? 'fr' : 'ar';
            if (window.I18n && window.I18n.setLang) {
                window.I18n.setLang(cur);
            } else {
                fetch('/api/set-lang', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
                    body: JSON.stringify({lang: cur})
                }).then(function() { location.reload(); });
            }
        });
    }

    // Search
    var searchInput = document.getElementById('cSearch');
    if (searchInput) {
        var searchTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            var q = this.value.trim();
            if (q.length < 3) return;
            searchTimer = setTimeout(function() {
                window.location.href = '/reports?search=' + encodeURIComponent(q);
            }, 600);
        });
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var q = this.value.trim();
                if (q) window.location.href = '/reports?search=' + encodeURIComponent(q);
            }
        });
    }

    // Scroll animations
    function initScrollAnimations() {
        var els = document.querySelectorAll('[data-c-anim]');
        if (!els.length) return;
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('c-anim-fade');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        els.forEach(function(el) { observer.observe(el); });
    }

    // Animate counters
    function animateCounters() {
        var counters = document.querySelectorAll('[data-count]');
        counters.forEach(function(el) {
            var target = parseInt(el.getAttribute('data-count'), 10);
            var current = 0;
            var step = Math.max(1, Math.ceil(target / 30));
            var timer = setInterval(function() {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = current;
            }, 30);
        });
    }

    // Map initialization helper
    window.CMap = {
        init: function(containerId, reports, options) {
            var map = L.map(containerId, {
                zoomControl: false,
                attributionControl: false
            }).setView([36.7538, 3.0588], 12);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19
            }).addTo(map);

            L.control.zoom({ position: 'topright' }).addTo(map);

            if (reports && reports.length) {
                var markers = L.markerClusterGroup && L.markerClusterGroup() || L.layerGroup();
                reports.forEach(function(r) {
                    if (!r.latitude || !r.longitude) return;
                    var color = r.status === 'resolved' || r.status === 'validated' ? '#22c55e' :
                                r.status === 'submitted' ? '#f59e0b' :
                                r.status === 'in_progress' ? '#06b6d4' : '#6366f1';
                    var icon = L.divIcon({
                        className: '',
                        html: '<div style="width:28px;height:28px;border-radius:50%;background:' + color + ';border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;"><i class="fas ' + (r.category_icon || 'fa-flag') + '" style="font-size:10px;color:#fff;"></i></div>',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    });
                    var marker = L.marker([r.latitude, r.longitude], {icon: icon});
                    marker.bindPopup(
                        '<div style="font-family:Inter,sans-serif;min-width:180px;">' +
                        '<div style="font-weight:700;font-size:0.85rem;margin-bottom:4px;">' + (r.title || '') + '</div>' +
                        '<div style="font-size:0.75rem;color:#666;margin-bottom:6px;">' + (r.category_name || '') + ' &middot; ' + (r.commune_name || '') + '</div>' +
                        '<a href="/reports/' + r.id + '" style="color:#6366f1;font-size:0.8rem;font-weight:600;text-decoration:none;">' + (typeof I18n !== 'undefined' ? I18n.t('common.view') : 'Voir le détail') + ' →</a>' +
                        '</div>'
                    );
                    markers.addLayer(marker);
                });
                map.addLayer(markers);
            }

            return map;
        }
    };

    // GPS helper
    window.CGeo = {
        getCurrent: function(callback) {
            if (!navigator.geolocation) { callback(null); return; }
            navigator.geolocation.getCurrentPosition(
                function(pos) { callback({lat: pos.coords.latitude, lng: pos.coords.longitude}); },
                function() { callback(null); },
                {enableHighAccuracy: true, timeout: 8000}
            );
        }
    };

    // Voice input helper
    window.CVoice = {
        available: function() {
            return 'webkitSpeechRecognition' in window || 'SpeechRecognition' in window;
        },
        start: function(callback) {
            if (!this.available()) { callback(null, 'not supported'); return; }
            var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
            var rec = new SR();
            rec.lang = document.documentElement.lang === 'ar' ? 'ar-DZ' : 'fr-FR';
            rec.interimResults = false;
            rec.maxAlternatives = 1;
            rec.onresult = function(e) { callback(e.results[0][0].transcript); };
            rec.onerror = function(e) { callback(null, e.error); };
            rec.onend = function() {};
            rec.start();
        }
    };

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        initScrollAnimations();
        animateCounters();
        initLightbox();
    });

    // Lightbox for citizen gallery images
    function initLightbox() {
        var lb = document.createElement('div');
        lb.className = 'balagh-lightbox';
        lb.innerHTML = '<button class="balagh-lightbox-close" title="Fermer"><i class="fas fa-times"></i></button>' +
            '<button class="balagh-lightbox-nav balagh-lightbox-prev" title="Précédent"><i class="fas fa-chevron-left"></i></button>' +
            '<button class="balagh-lightbox-nav balagh-lightbox-next" title="Suivant"><i class="fas fa-chevron-right"></i></button>' +
            '<div class="balagh-lightbox-caption"></div>' +
            '<div class="balagh-lightbox-counter"></div>' +
            '<img class="balagh-lightbox-img" src="" alt="">';
        document.body.appendChild(lb);

        var img = lb.querySelector('.balagh-lightbox-img');
        var counter = lb.querySelector('.balagh-lightbox-counter');
        var caption = lb.querySelector('.balagh-lightbox-caption');
        var items = [];
        var currentIdx = 0;

        function showImage() {
            if (!items[currentIdx]) return;
            img.src = items[currentIdx].src;
            caption.textContent = items[currentIdx].caption || '';
            counter.textContent = (currentIdx + 1) + ' / ' + items.length;
            lb.querySelector('.balagh-lightbox-prev').style.display = items.length > 1 ? 'flex' : 'none';
            lb.querySelector('.balagh-lightbox-next').style.display = items.length > 1 ? 'flex' : 'none';
        }

        function openLb(idx) {
            items = [];
            document.querySelectorAll('.show-gallery-item, .c-gallery-item, .ba-card').forEach(function(el) {
                var a = el.querySelector('a[href]');
                if (a && /\.(jpg|jpeg|png|gif|webp|svg)/i.test(a.href)) {
                    items.push({ src: a.href, caption: (el.querySelector('.gallery-overlay span') || el.querySelector('img') || {}).alt || '' });
                }
            });
            if (!items.length) return;
            currentIdx = idx < items.length ? idx : 0;
            showImage();
            lb.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLb() { lb.classList.remove('active'); document.body.style.overflow = ''; }

        lb.querySelector('.balagh-lightbox-close').addEventListener('click', closeLb);
        img.addEventListener('click', closeLb);
        lb.addEventListener('click', function(e) { if (e.target === lb) closeLb(); });
        lb.querySelector('.balagh-lightbox-prev').addEventListener('click', function(e) { e.stopPropagation(); currentIdx = (currentIdx - 1 + items.length) % items.length; showImage(); });
        lb.querySelector('.balagh-lightbox-next').addEventListener('click', function(e) { e.stopPropagation(); currentIdx = (currentIdx + 1) % items.length; showImage(); });
        document.addEventListener('keydown', function(e) {
            if (!lb.classList.contains('active')) return;
            if (e.key === 'Escape') closeLb();
            else if (e.key === 'ArrowLeft') { currentIdx = (currentIdx - 1 + items.length) % items.length; showImage(); }
            else if (e.key === 'ArrowRight') { currentIdx = (currentIdx + 1) % items.length; showImage(); }
        });

        document.addEventListener('click', function(e) {
            var item = e.target.closest('.show-gallery-item, .c-gallery-item, .ba-card');
            if (item && !item.querySelector('video')) {
                e.preventDefault();
                var siblings = Array.from(item.parentElement.children).filter(function(el) {
                    return el.querySelector('a[href]') && !el.querySelector('video') && /\.(jpg|jpeg|png|gif|webp|svg)/i.test(el.querySelector('a[href]').href);
                });
                var idx = siblings.indexOf(item);
                openLb(idx >= 0 ? idx : 0);
            }
        });
    }

})();
