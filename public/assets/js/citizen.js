/* ============================================
   Balagh Alger — Citizen Interface JS
   ============================================ */

(function() {
    'use strict';

    // Haptic feedback helper
    function vibrate(ms) {
        if (navigator.vibrate) navigator.vibrate(ms || 15);
    }

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
        var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        if (sunIcon) sunIcon.style.display = isDark ? 'inline' : 'none';
        if (moonIcon) moonIcon.style.display = isDark ? 'none' : 'inline';
    }
    if (themeBtn) {
        updateThemeIcons();
        themeBtn.addEventListener('click', function() {
            vibrate();
            var current = document.documentElement.getAttribute('data-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('balagh-theme', next);
            updateThemeIcons();
        });
    }
    var langBtn = document.getElementById('cLangToggle');
    if (langBtn) {
        langBtn.addEventListener('click', function() {
            vibrate();
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

    // Back to top
    var backToTop = document.getElementById('cBackToTop');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            backToTop.classList.toggle('visible', window.scrollY > 400);
        });
        backToTop.addEventListener('click', function() {
            vibrate();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Haptic feedback on bottom nav taps
    document.querySelectorAll('.c-nav-item').forEach(function(el) {
        el.addEventListener('click', function() { vibrate(10); });
    });

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
                var markers = (typeof L.markerClusterGroup === 'function') ? L.markerClusterGroup({
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
                }) : L.layerGroup();
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

    // Offline Manager
    window.OfflineManager = {
        bar: null,
        badge: null,
        isOffline: false,
        pendingCount: 0,

        init: function() {
            this.bar = document.getElementById('offlineBar');
            this.badge = document.getElementById('pendingBadge');
            if (this.bar) {
                this.bar.addEventListener('click', function() {
                    if (navigator.onLine) window.OfflineManager.hideBar();
                    else if (window.OfflineManager.pendingCount > 0) {
                        window.OfflineManager.triggerSync();
                    }
                });
            }
            this.checkStatus();
            window.addEventListener('online', function() { window.OfflineManager.onOnline(); });
            window.addEventListener('offline', function() { window.OfflineManager.onOffline(); });
            if (navigator.connection) {
                navigator.connection.addEventListener('change', function() {
                    if (navigator.onLine) window.OfflineManager.onOnline();
                    else window.OfflineManager.onOffline();
                });
            }
            this.pollPending();
            this.listenForMessages();
        },

        checkStatus: function() {
            if (!navigator.onLine) this.showBar('Vous êtes hors ligne', 'offline');
            else this.hideBar();
        },

        onOffline: function() {
            this.isOffline = true;
            this.showBar('Vous êtes hors ligne', 'offline');
        },

        onOnline: function() {
            this.isOffline = false;
            if (this.pendingCount > 0) {
                this.showBar('Envoi des signalements en attente...', 'syncing');
                this.triggerSync();
            } else {
                this.showBar('Connexion rétablie', 'online');
                var self = this;
                setTimeout(function() { self.hideBar(); }, 2000);
            }
        },

        showBar: function(msg, type) {
            if (!this.bar) return;
            this.bar.className = 'c-offline-bar ' + (type || '');
            var icon = type === 'offline' ? 'fa-wifi-slash' : type === 'syncing' ? 'fa-sync fa-spin' : 'fa-check-circle';
            var countHtml = this.pendingCount > 0 && type !== 'online' ? ' <strong>(' + this.pendingCount + ')</strong>' : '';
            this.bar.innerHTML = '<i class="fas ' + icon + '"></i> <span>' + msg + countHtml + '</span>';
            this.bar.style.display = 'flex';
            document.body.classList.add('has-offline-bar');
        },

        hideBar: function() {
            if (!this.bar) return;
            this.bar.style.display = 'none';
            document.body.classList.remove('has-offline-bar');
        },

        pollPending: function() {
            this.queryPendingCount();
            setInterval(function() { window.OfflineManager.queryPendingCount(); }, 10000);
        },

        queryPendingCount: function() {
            if (!navigator.serviceWorker || !navigator.serviceWorker.controller) return;
            navigator.serviceWorker.controller.postMessage({ type: 'GET_PENDING_COUNT' });
        },

        triggerSync: function() {
            if (navigator.serviceWorker && navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage({ type: 'SYNC_NOW' });
            }
        },

        listenForMessages: function() {
            if (!navigator.serviceWorker) return;
            navigator.serviceWorker.addEventListener('message', function(event) {
                if (!event.data) return;
                switch (event.data.type) {
                    case 'PENDING_COUNT':
                        window.OfflineManager.pendingCount = event.data.count;
                        window.OfflineManager.updateBadge();
                        break;
                    case 'SYNC_COMPLETE':
                        window.OfflineManager.pendingCount = event.data.remaining || 0;
                        window.OfflineManager.updateBadge();
                        if (event.data.synced > 0) {
                            var msg = event.data.synced + ' signalement(s) envoyé(s)';
                            if (event.data.remaining > 0) msg += '. ' + event.data.remaining + ' restant(s).';
                            else msg += ' avec succès !';
                            window.OfflineManager.showBar(msg, 'online');
                            setTimeout(function() {
                                if (navigator.onLine) window.OfflineManager.hideBar();
                            }, 3000);
                            if (typeof CToast !== 'undefined') {
                                CToast.show(msg, 'success');
                            }
                        } else {
                            if (navigator.onLine) window.OfflineManager.hideBar();
                        }
                        break;
                    case 'CACHED_REPORTS':
                        if (window.OfflineManager._cachedReportsCallback) {
                            window.OfflineManager._cachedReportsCallback(event.data.reports);
                            window.OfflineManager._cachedReportsCallback = null;
                        }
                        break;
                }
            });
        },

        updateBadge: function() {
            if (!this.badge) return;
            if (this.pendingCount > 0) {
                this.badge.textContent = this.pendingCount;
                this.badge.style.display = 'flex';
            } else {
                this.badge.style.display = 'none';
            }
        },

        getCachedReports: function(callback) {
            this._cachedReportsCallback = callback;
            if (navigator.serviceWorker && navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage({ type: 'GET_CACHED_REPORTS' });
            }
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

    // ============ SPLASH SCREEN ============
    window.SplashScreen = {
        el: null,
        init: function() {
            this.el = document.getElementById('splashScreen');
            if (!this.el) return;
            if (sessionStorage.getItem('balagh-splash-hidden')) {
                this.el.style.display = 'none';
                return;
            }
            var self = this;
            window.addEventListener('load', function() {
                setTimeout(function() { self.hide(); }, 600);
            });
            setTimeout(function() { self.hide(); }, 3000);
        },
        hide: function() {
            if (!this.el) return;
            this.el.classList.add('hidden');
            sessionStorage.setItem('balagh-splash-hidden', '1');
            var self = this;
            setTimeout(function() { if (self.el) self.el.style.display = 'none'; }, 400);
        }
    };

    // ============ PAGE TRANSITIONS ============
    window.PageTransitions = {
        init: function() {
            var main = document.getElementById('cMain');
            if (!main) return;
            main.classList.add('c-page-enter');
            var self = this;
            document.querySelectorAll('a:not([target]):not([download]):not([href^="#"]):not([href^="javascript"]):not([href^="tel"]):not([href^="mailto"])').forEach(function(a) {
                var href = a.getAttribute('href');
                if (!href || href.startsWith('http') || href.startsWith('//')) return;
                a.addEventListener('click', function(e) {
                    if (e.metaKey || e.ctrlKey || e.shiftKey) return;
                    e.preventDefault();
                    self.navigate(href);
                });
            });
        },
        navigate: function(url) {
            var main = document.getElementById('cMain');
            if (!main) { window.location.href = url; return; }
            main.classList.remove('c-page-enter');
            main.classList.add('c-page-leave');
            var self = this;
            setTimeout(function() { window.location.href = url; }, 200);
        }
    };

    // ============ SKELETON LOADER ============
    window.SkeletonLoader = {
        show: function(container, count) {
            var el = typeof container === 'string' ? document.querySelector(container) : container;
            if (!el) return;
            var html = '';
            var n = count || 3;
            for (var i = 0; i < n; i++) {
                html += '<div class="c-skeleton-card">' +
                    '<div class="c-skeleton c-skeleton-heading"></div>' +
                    '<div class="c-skeleton c-skeleton-text"></div>' +
                    '<div class="c-skeleton c-skeleton-text" style="width:75%"></div>' +
                    '</div>';
            }
            el._skeletonOrig = el.innerHTML;
            el.innerHTML = html;
        },
        hide: function(container) {
            var el = typeof container === 'string' ? document.querySelector(container) : container;
            if (!el || !el._skeletonOrig) return;
            el.innerHTML = el._skeletonOrig;
            delete el._skeletonOrig;
        }
    };

    // ============ PULL-TO-REFRESH ============
    window.PullToRefresh = {
        enabled: true,
        startY: 0,
        pulling: false,
        ptrEl: null,
        threshold: 80,
        callback: null,
        init: function(opts) {
            this.callback = (opts && opts.onRefresh) || function() { location.reload(); };
            this.ptrEl = document.getElementById('cPtr') || this.createEl();
            var self = this;
            document.addEventListener('touchstart', function(e) {
                if (!self.enabled || window.scrollY > 0) return;
                self.startY = e.touches[0].clientY;
                self.pulling = false;
            }, { passive: true });
            document.addEventListener('touchmove', function(e) {
                if (!self.enabled || window.scrollY > 0) return;
                var dy = e.touches[0].clientY - self.startY;
                if (dy > 0) {
                    self.pulling = true;
                    if (dy > self.threshold) {
                        self.ptrEl.classList.add('release');
                    } else {
                        self.ptrEl.classList.remove('release');
                    }
                    if (dy > 100) dy = 100;
                    self.ptrEl.style.transform = 'translateY(' + (dy * 0.4) + 'px)';
                    self.ptrEl.classList.add('visible');
                }
            }, { passive: true });
            document.addEventListener('touchend', function(e) {
                if (!self.pulling) return;
                self.pulling = false;
                self.ptrEl.classList.remove('visible', 'release');
                self.ptrEl.style.transform = '';
                if (e.changedTouches[0].clientY - self.startY > self.threshold) {
                    self.ptrEl.classList.add('loading');
                    if (self.callback) self.callback();
                    setTimeout(function() { self.ptrEl.classList.remove('loading'); }, 1000);
                }
            }, { passive: true });
        },
        createEl: function() {
            var el = document.createElement('div');
            el.id = 'cPtr';
            el.className = 'c-ptr';
            el.innerHTML = '<div class="c-ptr-content"><i class="fas fa-arrow-down c-ptr-icon"></i> <span>' + (window.__translations?.common?.pull_to_refresh || 'Tirez pour actualiser') + '</span></div>';
            document.body.prepend(el);
            return el;
        }
    };

    // ============ APP BADGE ============
    window.AppBadge = {
        el: null,
        init: function() {
            this.el = document.createElement('div');
            this.el.className = 'c-app-badge';
            this.el.id = 'appBadge';
            document.body.appendChild(this.el);
        },
        set: function(count) {
            if (!this.el) return;
            if (count > 0) {
                this.el.textContent = count > 99 ? '99+' : count;
                this.el.classList.add('visible');
            } else {
                this.el.classList.remove('visible');
            }
        },
        clear: function() { this.set(0); }
    };

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        initScrollAnimations();
        animateCounters();
        initLightbox();
        window.OfflineManager.init();
        window.SplashScreen.init();
        window.PageTransitions.init();
        window.PullToRefresh.init();
        window.AppBadge.init();

        // Sync app badge with notification count
        var badgeEl = document.querySelector('.notif-badge');
        if (badgeEl) {
            window.AppBadge.set(parseInt(badgeEl.textContent, 10) || 0);
        }
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

    /* ============================================
       Chatbot — Citizen Assistant
       ============================================ */
    var CBot = {
        panel: null,
        fab: null,
        messages: null,
        quickEl: null,
        overlay: null,
        opened: false,
        greeted: false,
        topics: null,

        init: function() {
            this.panel = document.getElementById('chatPanel');
            this.fab = document.getElementById('chatFab');
            this.messages = document.getElementById('chatMessages');
            this.quickEl = document.getElementById('chatQuickActions');
            this.overlay = document.getElementById('chatOverlay');
            if (!this.panel || !this.fab) return;

            this.buildTopics();
            var self = this;

            this.fab.addEventListener('click', function() { vibrate(); self.toggle(); });
            document.getElementById('chatClose').addEventListener('click', function() { self.close(); });
            if (this.overlay) {
                this.overlay.addEventListener('click', function() { self.close(); });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && self.opened) self.close();
            });
        },

        buildTopics: function() {
            var t = (window.__translations && window.__translations.chatbot) || {};
            this.topics = {
                welcome: {
                    text: t.welcome || 'Bienvenue ! Je suis l\'assistant Balagh Alger. Comment puis-je vous aider ?',
                    quick: [
                        { label: t.q_how || 'Comment signaler ?', topic: 'how_to_report' },
                        { label: t.q_track || 'Suivre un signalement', topic: 'track' },
                        { label: t.q_statuses || 'Les statuts', topic: 'statuses' },
                        { label: t.q_categories || 'Catégories', topic: 'categories' },
                        { label: t.q_account || 'Mon compte', topic: 'account' }
                    ]
                },
                how_to_report: {
                    text: (t.how_intro || 'Créer un signalement est simple ! Voici les étapes :') +
                        '<br><br>' +
                        '<span class="step-num">1</span> <strong>' + (t.how_s1 || 'Photo') + '</strong> — ' + (t.how_s1d || 'Prenez une photo du problème.') + '<br><br>' +
                        '<span class="step-num">2</span> <strong>' + (t.how_s2 || 'Catégorie') + '</strong> — ' + (t.how_s2d || 'Choisissez la catégorie et décrivez le problème.') + '<br><br>' +
                        '<span class="step-num">3</span> <strong>' + (t.how_s3 || 'Localisation') + '</strong> — ' + (t.how_s3d || 'Géolocalisez le problème sur la carte.') + '<br><br>' +
                        '<span class="step-num">4</span> <strong>' + (t.how_s4 || 'Envoyer') + '</strong> — ' + (t.how_s4d || 'Validez et c\'est envoyé !') + '<br><br>' +
                        '<strong>' + (t.how_hint || ' appuyez sur le bouton + en bas pour commencer.') + '</strong>',
                    quick: [
                        { label: t.q_track || 'Suivre un signalement', topic: 'track' },
                        { label: t.q_categories || 'Catégories', topic: 'categories' },
                        { label: t.q_back || '← Retour', topic: 'welcome' }
                    ]
                },
                track: {
                    text: t.track_text || 'Après avoir soumis un signalement, vous recevez un <strong>code de suivi</strong> unique (ex: BA-2026-000001).<br><br>Vous pouvez le consulter depuis :<br>• <strong>Mes Signalements</strong> dans votre profil<br>• La page publique <strong>/suivi</strong> (sans compte)<br><br>Vous êtes notifié à chaque étape du traitement.',
                    quick: [
                        { label: t.q_statuses || 'Les statuts', topic: 'statuses' },
                        { label: t.q_how || 'Comment signaler ?', topic: 'how_to_report' },
                        { label: t.q_back || '← Retour', topic: 'welcome' }
                    ]
                },
                statuses: {
                    text: t.statuses_intro || 'Voici les étapes de traitement d\'un signalement :',
                    text2: '<div style="display:flex;flex-direction:column;gap:6px;margin-top:8px;">' +
                        '<div style="display:flex;align-items:center;gap:8px;"><span style="width:8px;height:8px;border-radius:50%;background:var(--c-amber);flex-shrink:0;"></span><span style="font-size:0.82rem;"><strong>' + (t.st_submitted || 'Soumis') + '</strong> — ' + (t.st_submitted_d || 'Votre signalement est enregistré.') + '</span></div>' +
                        '<div style="display:flex;align-items:center;gap:8px;"><span style="width:8px;height:8px;border-radius:50%;background:var(--c-accent);flex-shrink:0;"></span><span style="font-size:0.82rem;"><strong>' + (t.st_acknowledged || 'Pris en compte') + '</strong> — ' + (t.st_acknowledged_d || 'L\'administration a pris connaissance.') + '</span></div>' +
                        '<div style="display:flex;align-items:center;gap:8px;"><span style="width:8px;height:8px;border-radius:50%;background:var(--c-cyan);flex-shrink:0;"></span><span style="font-size:0.82rem;"><strong>' + (t.st_in_progress || 'En cours') + '</strong> — ' + (t.st_in_progress_d || 'Un agent intervient sur le terrain.') + '</span></div>' +
                        '<div style="display:flex;align-items:center;gap:8px;"><span style="width:8px;height:8px;border-radius:50%;background:var(--c-amber);flex-shrink:0;"></span><span style="font-size:0.82rem;"><strong>' + (t.st_pending || 'À vérifier') + '</strong> — ' + (t.st_pending_d || 'Les travaux sont en attente de validation.') + '</span></div>' +
                        '<div style="display:flex;align-items:center;gap:8px;"><span style="width:8px;height:8px;border-radius:50%;background:var(--c-green);flex-shrink:0;"></span><span style="font-size:0.82rem;"><strong>' + (t.st_resolved || 'Résolu') + '</strong> — ' + (t.st_resolved_d || 'Le problème a été corrigé.') + '</span></div>' +
                        '</div>',
                    quick: [
                        { label: t.q_how || 'Comment signaler ?', topic: 'how_to_report' },
                        { label: t.q_track || 'Suivre', topic: 'track' },
                        { label: t.q_back || '← Retour', topic: 'welcome' }
                    ]
                },
                categories: {
                    text: t.cat_intro || 'Vous pouvez signaler ce type de problème :',
                    text2: '<div style="display:flex;flex-direction:column;gap:6px;margin-top:8px;">' +
                        '<div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;"><i class="fas fa-road" style="color:var(--c-accent);width:20px;text-align:center;"></i><strong>' + (t.cat_road || 'Voirie & Routes') + '</strong> — ' + (t.cat_road_d || 'Nids-de-poule, trottoirs cassés') + '</div>' +
                        '<div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;"><i class="fas fa-droplet" style="color:var(--c-cyan);width:20px;text-align:center;"></i><strong>' + (t.cat_water || 'Eau & Assainissement') + '</strong> — ' + (t.cat_water_d || 'Fuites, canalisations') + '</div>' +
                        '<div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;"><i class="fas fa-bolt" style="color:var(--c-amber);width:20px;text-align:center;"></i><strong>' + (t.cat_elec || 'Électricité & Gaz') + '</strong> — ' + (t.cat_elec_d || 'Pannes, câbles dangereux') + '</div>' +
                        '<div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;"><i class="fas fa-trash" style="color:var(--c-red);width:20px;text-align:center;"></i><strong>' + (t.cat_trash || 'Propreté') + '</strong> — ' + (t.cat_trash_d || 'Déchets, décharges sauvages') + '</div>' +
                        '<div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;"><i class="fas fa-lightbulb" style="color:var(--c-green);width:20px;text-align:center;"></i><strong>' + (t.cat_light || 'Éclairage Public') + '</strong> — ' + (t.cat_light_d || 'Lampadaires en panne') + '</div>' +
                        '<div style="display:flex;align-items:center;gap:8px;font-size:0.82rem;"><i class="fas fa-tree" style="color:var(--c-green);width:20px;text-align:center;"></i><strong>' + (t.cat_green || 'Espaces Verts') + '</strong> — ' + (t.cat_green_d || 'Arbres tombés, parcs dégradés') + '</div>' +
                        '</div>',
                    quick: [
                        { label: t.q_how || 'Comment signaler ?', topic: 'how_to_report' },
                        { label: t.q_track || 'Suivre', topic: 'track' },
                        { label: t.q_back || '← Retour', topic: 'welcome' }
                    ]
                },
                account: {
                    text: t.account_text || 'Un <strong>compte gratuit</strong> est nécessaire pour signaler et suivre vos demandes.<br><br>• Cliquez sur <strong>"Créer un compte"</strong> sur la page de connexion<br>• Remplissez votre nom et email<br>• Vous pouvez ensuite vous connecter depuis n\'importe quelle page.<br><br>Votre compte vous permet de recevoir des <strong>notifications</strong> et de suivre vos signalements.',
                    quick: [
                        { label: t.q_how || 'Comment signaler ?', topic: 'how_to_report' },
                        { label: t.q_notif || 'Notifications', topic: 'notifications' },
                        { label: t.q_back || '← Retour', topic: 'welcome' }
                    ]
                },
                notifications: {
                    text: t.notif_text || 'Vous recevez des <strong>notifications</strong> automatiques à chaque étape :<br><br>• Quand votre signalement est <strong>pris en compte</strong><br>• Quand un agent <strong>intervient</strong> sur le terrain<br>• Quand le problème est <strong>résolu</strong><br><br>Consultez-les via l\'icône <i class="fas fa-bell"></i> dans le menu.',
                    quick: [
                        { label: t.q_track || 'Suivre', topic: 'track' },
                        { label: t.q_statuses || 'Les statuts', topic: 'statuses' },
                        { label: t.q_back || '← Retour', topic: 'welcome' }
                    ]
                },
                contact: {
                    text: t.contact_text || 'Pour toute <strong>question</strong> ou <strong>urgence</strong> :<br><br>• Utilisez ce chat pour comprendre la plateforme<br>• Contactez la <strong>Wilaya d\'Alger</strong> pour les urgences<br>• Vos signalements sont traités par les organismes compétents (SEAAL, Sonelgaz, DTP, etc.)',
                    quick: [
                        { label: t.q_how || 'Comment signaler ?', topic: 'how_to_report' },
                        { label: t.q_back || '← Retour', topic: 'welcome' }
                    ]
                }
            };
        },

        toggle: function() {
            if (this.opened) this.close(); else this.open();
        },

        open: function() {
            this.opened = true;
            this.panel.classList.add('open');
            this.fab.classList.add('open');
            if (this.overlay) this.overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
            this.fab.querySelector('i').className = 'fas fa-times';

            if (!this.greeted) {
                this.greeted = true;
                var self = this;
                setTimeout(function() { self.showTopic('welcome'); }, 300);
            }
        },

        close: function() {
            this.opened = false;
            this.panel.classList.remove('open');
            this.fab.classList.remove('open');
            if (this.overlay) this.overlay.classList.remove('open');
            document.body.style.overflow = '';
            this.fab.querySelector('i').className = 'fas fa-comment-dots';
        },

        showTopic: function(topicId) {
            var topic = this.topics[topicId];
            if (!topic) return;
            var self = this;

            self.addTyping();
            setTimeout(function() {
                self.removeTyping();
                self.addMessage(topic.text, 'bot');
                if (topic.text2) {
                    setTimeout(function() { self.addMessage(topic.text2, 'bot'); }, 200);
                }
                if (topic.quick) {
                    setTimeout(function() { self.showQuickReplies(topic.quick); }, topic.text2 ? 400 : 200);
                }
            }, 600);
        },

        addMessage: function(html, type) {
            var isRtl = document.documentElement.dir === 'rtl' || document.documentElement.getAttribute('dir') === 'rtl';
            var msg = document.createElement('div');
            msg.className = 'c-chat-msg ' + type;

            var avatar = document.createElement('div');
            avatar.className = 'c-chat-msg-avatar';
            avatar.innerHTML = type === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>';

            var bubble = document.createElement('div');
            bubble.className = 'c-chat-msg-bubble';
            bubble.innerHTML = html;

            msg.appendChild(avatar);
            msg.appendChild(bubble);
            this.messages.appendChild(msg);
            this.scrollToBottom();
        },

        addTyping: function() {
            var t = document.createElement('div');
            t.className = 'c-chat-typing';
            t.id = 'chatTyping';
            t.innerHTML = '<div class="c-chat-typing-dot"></div><div class="c-chat-typing-dot"></div><div class="c-chat-typing-dot"></div>';
            this.messages.appendChild(t);
            this.scrollToBottom();
        },

        removeTyping: function() {
            var t = document.getElementById('chatTyping');
            if (t) t.remove();
        },

        showQuickReplies: function(replies) {
            this.quickEl.innerHTML = '';
            var self = this;
            replies.forEach(function(r) {
                var btn = document.createElement('button');
                btn.className = 'c-chat-chip';
                btn.textContent = r.label;
                btn.addEventListener('click', function() {
                    self.addMessage(r.label, 'user');
                    self.quickEl.innerHTML = '';
                    self.showTopic(r.topic);
                });
                self.quickEl.appendChild(btn);
            });
        },

        scrollToBottom: function() {
            var el = this.messages;
            el.scrollTop = el.scrollHeight;
        }
    };

    // Initialize chatbot on load
    document.addEventListener('DOMContentLoaded', function() {
        CBot.init();

        // data-confirm handler (native confirm, no SweetAlert2)
        document.querySelectorAll('[data-confirm]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                var msg = this.dataset.confirm || 'Êtes-vous sûr ? Cette action est irréversible.';
                if (!confirm(msg)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
            });
        });
    });

})();
