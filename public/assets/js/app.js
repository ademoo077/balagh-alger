/* ============================================
   BALAGH ALGER — Application JS v3.0
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {

    // ============ THEME TOGGLE ============
    var html = document.documentElement;
    var themeToggle = document.getElementById('themeToggle');
    var savedTheme = localStorage.getItem('balagh-theme') || 'dark';
    html.setAttribute('data-bs-theme', savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            var current = html.getAttribute('data-bs-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            localStorage.setItem('balagh-theme', next);
        });
    }

    // ============ SIDEBAR TOGGLE ============
    var toggleBtn = document.getElementById('toggleSidebar');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('show');
                document.body.classList.toggle('sidebar-open');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            if (sidebar) sidebar.classList.remove('show');
            document.body.classList.remove('sidebar-open');
        });
    }

    // ============ CSRF ============
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.content : '';
    if (typeof $ !== 'undefined') {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });
    }

    // ============ AUTO-DISMISS ALERTS ============
    setTimeout(function() {
        document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        });
    }, 5000);

    // ============ SWEETALERT2 CONFIRM ============
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            var form = this.closest('form');
            var theme = html.getAttribute('data-bs-theme');
            Swal.fire({
                title: typeof I18n !== 'undefined' ? I18n.t('ui.are_you_sure') : 'Êtes-vous sûr ?',
                text: this.dataset.confirm || (typeof I18n !== 'undefined' ? I18n.t('ui.irreversible_action') : 'Cette action est irréversible.'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: theme === 'dark' ? '#374151' : '#9ca3af',
                confirmButtonText: typeof I18n !== 'undefined' ? I18n.t('ui.confirm') : 'Confirmer',
                cancelButtonText: typeof I18n !== 'undefined' ? I18n.t('ui.cancel') : 'Annuler',
                background: theme === 'dark' ? '#1a2235' : '#ffffff',
                color: theme === 'dark' ? '#f1f5f9' : '#111827',
                buttonsStyling: false,
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-outline-secondary' }
            }).then(function(result) {
                if (result.isConfirmed && form) form.submit();
            });
        });
    });

    // ============ GLOBAL SEARCH ============
    var globalSearch = document.getElementById('globalSearch');
    if (globalSearch) {
        var searchTimeout;
        var resultsContainer = null;

        globalSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            var q = this.value;

            if (!resultsContainer) {
                resultsContainer = document.createElement('div');
                resultsContainer.className = 'search-results';
                resultsContainer.style.cssText = 'position:absolute;top:100%;left:0;right:0;margin-top:4px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);box-shadow:var(--shadow-xl);max-height:320px;overflow-y:auto;z-index:1060;display:none;padding:4px;';
                globalSearch.parentElement.appendChild(resultsContainer);
            }

            if (q.length >= 2) {
                searchTimeout = setTimeout(function() {
                    fetch('/api/reports/search?q=' + encodeURIComponent(q))
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            if (data.length === 0) {
                                resultsContainer.innerHTML = '<div style="padding:16px;text-align:center;color:var(--text-muted);font-size:0.82rem;">' + (typeof I18n !== 'undefined' ? I18n.t('reports.no_results') : 'Aucun résultat') + '</div>';
                            } else {
                                resultsContainer.innerHTML = data.map(function(r) {
                                    var isUser = r.result_type === 'user';
                                    var icon = isUser ? 'fas fa-user' : 'fas fa-flag';
                                    var url = isUser ? '/users/' + r.id : '/reports/' + r.id;
                                    var subtitle = isUser ? (r.category_name || '') : (r.tracking_code + ' · ' + (r.title || ''));
                                    return '<a href="' + url + '" style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:var(--radius-xs);text-decoration:none;color:var(--text-primary);transition:background 0.15s;" onmouseover="this.style.background=\'var(--accent-surface)\'" onmouseout="this.style.background=\'transparent\'">' +
                                        '<i class="' + icon + '" style="color:var(--accent);font-size:0.78rem;"></i>' +
                                        '<div><div style="font-weight:600;font-size:0.82rem;">' + r.title + '</div><div style="font-size:0.75rem;color:var(--text-muted);">' + subtitle + '</div></div>' +
                                        '</a>';
                                }).join('');
                            }
                            resultsContainer.style.display = 'block';
                        });
                }, 250);
            } else {
                resultsContainer.style.display = 'none';
            }
        });

        document.addEventListener('click', function(e) {
            if (resultsContainer && !resultsContainer.contains(e.target) && e.target !== globalSearch) {
                resultsContainer.style.display = 'none';
            }
        });
    }

    // ============ SMOOTH SCROLL TO TOP ============
    document.querySelectorAll('[data-scroll-top]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // ============ COPY TO CLIPBOARD ============
    document.querySelectorAll('[data-copy]').forEach(function(el) {
        el.addEventListener('click', function() {
            var theme = html.getAttribute('data-bs-theme');
            navigator.clipboard.writeText(this.dataset.copy);
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: typeof I18n !== 'undefined' ? I18n.t('ui.copied') : 'Copié !',
                showConfirmButton: false,
                timer: 1500,
                background: theme === 'dark' ? '#1a2235' : '#ffffff',
                color: theme === 'dark' ? '#f1f5f9' : '#111827'
            });
        });
    });

    // ============ TOOLTIPS ============
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

    // ============ SMOOTH CARD ENTRANCE ============
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.card').forEach(function(card) {
            if (!card.closest('.animate-fade-in-up') && !card.closest('.animate-fade-in') && !card.closest('.animate-slide-in-left')) {
                card.style.opacity = '0';
                card.style.transform = 'translateY(12px)';
                card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                observer.observe(card);
            }
        });
    }

    // ============ NUMBER FORMATTING ============
    document.querySelectorAll('.format-number').forEach(function(el) {
        var num = parseInt(el.textContent);
        if (!isNaN(num)) el.textContent = num.toLocaleString('fr-FR');
    });

    // ============ FORM LOADING STATES ============
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function() {
            if (form.dataset.noLoading) return;
            var btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.dataset.loading) {
                btn.dataset.loading = '1';
                btn.dataset.originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>';
            }
        });
    });

    // ============ TOP LOADING BAR ============
    (function() {
        var bar = document.createElement('div');
        bar.style.cssText = 'position:fixed;top:0;left:0;height:3px;background:linear-gradient(90deg,var(--accent),var(--cyan));z-index:9999;width:0;transition:width 0.3s ease;pointer-events:none;';
        document.body.appendChild(bar);
        document.querySelectorAll('a[href]').forEach(function(a) {
            a.addEventListener('click', function() {
                var h = this.getAttribute('href');
                if (!h || h === '#' || h.startsWith('javascript:') || this.target === '_blank' || this.closest('form')) return;
                bar.style.width = '60%';
                setTimeout(function() { bar.style.width = '85%'; }, 150);
            });
        });
        window.addEventListener('load', function() {
            if (bar.style.width !== '0px' && bar.style.width !== '0') {
                bar.style.width = '100%';
                setTimeout(function() { bar.style.width = '0'; }, 300);
            }
        });
    })();

    // ============ PRINT ============
    window.printReport = function(url) {
        window.open(url, '_blank', 'width=800,height=600');
    };

    // ============ WIZARD SYSTEM ============
    window.BalaghWizard = {
        currentStep: 0,
        totalSteps: 0,
        init: function() {
            this.totalSteps = document.querySelectorAll('.wizard-content').length;
            this.showStep(0);
            this.bindNavButtons();
            this.bindIndicatorClicks();
            this.updateProgress();
        },
        showStep: function(n) {
            this.currentStep = n;
            document.querySelectorAll('.wizard-content').forEach(function(el, i) {
                el.classList.toggle('active', i === n);
            });
            document.querySelectorAll('.wizard-step').forEach(function(el, i) {
                el.classList.toggle('active', i === n);
                el.classList.toggle('completed', i < n);
            });
            document.querySelectorAll('.wizard-connector').forEach(function(el, i) {
                el.classList.toggle('completed', i < n);
            });
            this.updateProgress();
        },
        nextStep: function() {
            if (this.validateStep(this.currentStep) && this.currentStep < this.totalSteps - 1) {
                this.showStep(this.currentStep + 1);
                this.scrollToTop();
            }
        },
        prevStep: function() {
            if (this.currentStep > 0) {
                this.showStep(this.currentStep - 1);
                this.scrollToTop();
            }
        },
        goToStep: function(n) {
            if (n >= 0 && n < this.totalSteps) {
                this.showStep(n);
            }
        },
        validateStep: function(n) {
            var content = document.querySelectorAll('.wizard-content')[n];
            if (!content) return true;
            var required = content.querySelectorAll('[required]');
            var valid = true;
            required.forEach(function(el) {
                if (!el.value || !el.value.trim()) {
                    el.style.borderColor = 'var(--red)';
                    el.addEventListener('input', function() { this.style.borderColor = ''; }, { once: true });
                    valid = false;
                }
            });
            if (!valid) {
                var theme = html.getAttribute('data-bs-theme');
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Veuillez remplir les champs obligatoires',
                    showConfirmButton: false,
                    timer: 2000,
                    background: theme === 'dark' ? '#1a2235' : '#ffffff',
                    color: theme === 'dark' ? '#f1f5f9' : '#111827'
                });
            }
            return valid;
        },
        bindNavButtons: function() {
            var self = this;
            document.querySelectorAll('[data-wizard-next]').forEach(function(btn) {
                btn.addEventListener('click', function(e) { e.preventDefault(); self.nextStep(); });
            });
            document.querySelectorAll('[data-wizard-prev]').forEach(function(btn) {
                btn.addEventListener('click', function(e) { e.preventDefault(); self.prevStep(); });
            });
        },
        bindIndicatorClicks: function() {
            var self = this;
            document.querySelectorAll('.wizard-step').forEach(function(step, i) {
                step.addEventListener('click', function() { self.goToStep(i); });
            });
        },
        updateProgress: function() {
            var pct = this.totalSteps > 1 ? ((this.currentStep) / (this.totalSteps - 1)) * 100 : 0;
            var bar = document.querySelector('.progress-thin .progress-bar');
            if (bar) bar.style.width = pct + '%';
        },
        scrollToTop: function() {
            var el = document.querySelector('.wizard-steps');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    // ============ SCROLL REVEAL ============
    if ('IntersectionObserver' in window) {
        var revealObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal-on-scroll').forEach(function(el) {
            revealObserver.observe(el);
        });
    }

    // ============ ANIMATED COUNTERS ============
    if ('IntersectionObserver' in window) {
        var counterObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        document.querySelectorAll('[data-count-up]').forEach(function(el) {
            counterObserver.observe(el);
        });
    }
    function animateCounter(el) {
        var target = parseInt(el.getAttribute('data-count-up'));
        if (isNaN(target) || target === 0) return;
        var duration = 900;
        var startTime = null;
        var startVal = 0;
        function step(ts) {
            if (!startTime) startTime = ts;
            var progress = Math.min((ts - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target).toLocaleString('fr-FR');
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target.toLocaleString('fr-FR');
        }
        requestAnimationFrame(step);
    }

    // ============ LIGHTBOX ============
    (function() {
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

        function openLightbox(index) {
            items = [];
            document.querySelectorAll('.show-gallery-item, .ba-lightbox-trigger').forEach(function(el) {
                var link = el.querySelector('a[href]');
                if (link && /\.(jpg|jpeg|png|gif|webp|svg)/i.test(link.href)) {
                    items.push({ src: link.href, caption: (el.querySelector('.gallery-overlay span') || el.querySelector('img') || {}).alt || '' });
                }
            });
            if (items.length === 0) return;
            currentIdx = index < items.length ? index : 0;
            showImage();
            lb.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function showImage() {
            if (!items[currentIdx]) return;
            img.src = items[currentIdx].src;
            img.alt = items[currentIdx].caption;
            caption.textContent = items[currentIdx].caption || '';
            counter.textContent = (currentIdx + 1) + ' / ' + items.length;
            lb.querySelector('.balagh-lightbox-prev').style.display = items.length > 1 ? 'flex' : 'none';
            lb.querySelector('.balagh-lightbox-next').style.display = items.length > 1 ? 'flex' : 'none';
        }

        function closeLightbox() {
            lb.classList.remove('active');
            document.body.style.overflow = '';
        }

        lb.querySelector('.balagh-lightbox-close').addEventListener('click', closeLightbox);
        img.addEventListener('click', closeLightbox);
        lb.addEventListener('click', function(e) { if (e.target === lb) closeLightbox(); });
        lb.querySelector('.balagh-lightbox-prev').addEventListener('click', function(e) { e.stopPropagation(); currentIdx = (currentIdx - 1 + items.length) % items.length; showImage(); });
        lb.querySelector('.balagh-lightbox-next').addEventListener('click', function(e) { e.stopPropagation(); currentIdx = (currentIdx + 1) % items.length; showImage(); });

        document.addEventListener('keydown', function(e) {
            if (!lb.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            else if (e.key === 'ArrowLeft') { currentIdx = (currentIdx - 1 + items.length) % items.length; showImage(); }
            else if (e.key === 'ArrowRight') { currentIdx = (currentIdx + 1) % items.length; showImage(); }
        });

        document.addEventListener('click', function(e) {
            var galleryItem = e.target.closest('.show-gallery-item');
            if (galleryItem && !galleryItem.querySelector('video')) {
                e.preventDefault();
                var allItems = Array.from(galleryItem.parentElement.querySelectorAll('.show-gallery-item'));
                var idx = allItems.indexOf(galleryItem);
                var galleryItems = [];
                allItems.forEach(function(el) {
                    var link = el.querySelector('a[href]');
                    if (link && /\.(jpg|jpeg|png|gif|webp|svg)/i.test(link.href)) {
                        galleryItems.push(el);
                    }
                });
                var realIdx = galleryItems.indexOf(galleryItem);
                openLightbox(realIdx >= 0 ? realIdx : 0);
            }
        });

        window.BalaghLightbox = { open: openLightbox, close: closeLightbox };
    })();

    // ============ NOTIFICATION DROPDOWN ============
    (function() {
        var toggle = document.getElementById('notifDropdownToggle');
        var dropdown = document.getElementById('notifDropdown');
        var list = document.getElementById('notifDropdownList');
        var badge = document.getElementById('notifBadge');
        if (!toggle || !dropdown) return;

        var isOpen = false;
        var notifTypes = {
            'report_assigned': { icon: 'fas fa-user-check', color: 'var(--accent)' },
            'report_status': { icon: 'fas fa-exchange-alt', color: 'var(--cyan)' },
            'report_comment': { icon: 'fas fa-comment', color: 'var(--amber)' },
            'report_created': { icon: 'fas fa-plus-circle', color: 'var(--green)' },
            'default': { icon: 'fas fa-bell', color: 'var(--accent)' }
        };

        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            isOpen = !isOpen;
            dropdown.classList.toggle('show', isOpen);
            if (isOpen) loadNotifications();
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== toggle && !toggle.contains(e.target)) {
                isOpen = false;
                dropdown.classList.remove('show');
            }
        });

        function loadNotifications() {
            fetch('/api/notifications/count', { credentials: 'same-origin' })
                .then(function(r) { return r.ok ? r.json() : null; })
                .then(function(data) {
                    if (data) updateBadge(data.count || 0);
                });
            fetch('/notifications?ajax=1', { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.ok ? r.text() : null; })
                .then(function(html) {
                    if (html && list) {
                        list.innerHTML = html;
                        bindNotifClicks();
                    }
                })
                .catch(function() {});
        }

        function bindNotifClicks() {
            list.querySelectorAll('.notif-dropdown-item[data-id]').forEach(function(item) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    var id = this.getAttribute('data-id');
                    var url = this.getAttribute('href');
                    fetch('/notifications/' + id + '/read', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: '_token=' + encodeURIComponent(csrfToken)
                    }).then(function() {
                        if (url) window.location.href = url;
                    });
                });
            });
        }

        function updateBadge(count) {
            if (!badge) return;
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        function pollNotifications() {
            fetch('/api/notifications/count', { credentials: 'same-origin' })
                .then(function(r) { return r.ok ? r.json() : null; })
                .then(function(data) {
                    if (data) updateBadge(data.count || 0);
                })
                .catch(function() {});
        }
        pollNotifications();
        setInterval(pollNotifications, 30000);
    })();

    // ============ THEME CUSTOMIZER ============
    (function() {
        var toggleBtn = document.getElementById('themeCustomizerToggle');
        var panel = document.getElementById('themeCustomizerPanel');
        if (!toggleBtn || !panel) return;

        var accentColors = [
            { name: 'Indigo', value: '#6366f1', hover: '#4f46e5', surface: 'rgba(99,102,241,0.1)' },
            { name: 'Blue', value: '#3b82f6', hover: '#2563eb', surface: 'rgba(59,130,246,0.1)' },
            { name: 'Cyan', value: '#0891b2', hover: '#0e7490', surface: 'rgba(8,145,178,0.1)' },
            { name: 'Emerald', value: '#10b981', hover: '#059669', surface: 'rgba(16,185,129,0.1)' },
            { name: 'Teal', value: '#14b8a6', hover: '#0d9488', surface: 'rgba(20,184,166,0.1)' },
            { name: 'Amber', value: '#f59e0b', hover: '#d97706', surface: 'rgba(245,158,11,0.1)' },
            { name: 'Orange', value: '#f97316', hover: '#ea580c', surface: 'rgba(249,115,22,0.1)' },
            { name: 'Rose', value: '#f43f5e', hover: '#e11d48', surface: 'rgba(244,63,94,0.1)' },
            { name: 'Purple', value: '#a855f7', hover: '#9333ea', surface: 'rgba(168,85,247,0.1)' },
            { name: 'Slate', value: '#64748b', hover: '#475569', surface: 'rgba(100,116,139,0.1)' }
        ];

        var savedAccent = localStorage.getItem('balagh-accent') || '#6366f1';
        applyAccent(savedAccent);

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            panel.classList.toggle('show');
        });
        document.addEventListener('click', function(e) {
            if (!panel.contains(e.target) && e.target !== toggleBtn) panel.classList.remove('show');
        });

        var grid = panel.querySelector('.theme-color-grid');
        if (grid) {
            accentColors.forEach(function(c) {
                var swatch = document.createElement('button');
                swatch.className = 'theme-color-swatch' + (savedAccent === c.value ? ' active' : '');
                swatch.style.background = c.value;
                swatch.title = c.name;
                swatch.setAttribute('data-color', c.value);
                swatch.addEventListener('click', function() {
                    grid.querySelectorAll('.theme-color-swatch').forEach(function(s) { s.classList.remove('active'); });
                    swatch.classList.add('active');
                    applyAccent(c.value);
                    localStorage.setItem('balagh-accent', c.value);
                });
                grid.appendChild(swatch);
            });
        }

        var resetBtn = panel.querySelector('.theme-reset-btn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                applyAccent('#6366f1');
                localStorage.removeItem('balagh-accent');
                grid.querySelectorAll('.theme-color-swatch').forEach(function(s) {
                    s.classList.toggle('active', s.getAttribute('data-color') === '#6366f1');
                });
            });
        }

        function applyAccent(hex) {
            var r = parseInt(hex.slice(1,3), 16);
            var g = parseInt(hex.slice(3,5), 16);
            var b = parseInt(hex.slice(5,7), 16);
            document.documentElement.style.setProperty('--accent', hex);
            document.documentElement.style.setProperty('--accent-hover', hex);
            document.documentElement.style.setProperty('--accent-light', hex + 'cc');
            document.documentElement.style.setProperty('--accent-surface', 'rgba(' + r + ',' + g + ',' + b + ',0.1)');
            document.documentElement.style.setProperty('--accent-glow', 'rgba(' + r + ',' + g + ',' + b + ',0.15)');
            document.documentElement.style.setProperty('--bs-primary', hex);
            var toggle = document.getElementById('themeCustomizerToggle');
            if (toggle) toggle.style.background = hex;
        }
    })();

    // ============ COMMAND PALETTE (Ctrl+K) ============
    (function() {
        var overlay = document.getElementById('cmdPaletteOverlay');
        if (!overlay) return;
        var input = document.getElementById('cmdPaletteInput');
        var results = document.getElementById('cmdPaletteResults');
        var items = [];
        var activeIndex = -1;

        var pages = [
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.dashboard') : 'Dashboard', icon: 'fas fa-th-large', url: '/dashboard', hint: typeof I18n !== 'undefined' ? I18n.t('nav.dashboard_hint') : 'Tableau de bord' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.reports') : 'Signalements', icon: 'fas fa-flag', url: '/reports', hint: typeof I18n !== 'undefined' ? I18n.t('nav.reports_hint') : 'Liste des signalements' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.new_report') : 'Nouveau signalement', icon: 'fas fa-plus-circle', url: '/reports/create', hint: typeof I18n !== 'undefined' ? I18n.t('nav.new_report_hint') : 'Créer un signalement' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.interventions') : 'Interventions', icon: 'fas fa-tools', url: '/interventions', hint: typeof I18n !== 'undefined' ? I18n.t('nav.interventions_hint') : 'Suivi des interventions' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.users') : 'Utilisateurs', icon: 'fas fa-users', url: '/users', hint: typeof I18n !== 'undefined' ? I18n.t('nav.users_hint') : 'Gestion des utilisateurs' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.organizations') : 'Organisations', icon: 'fas fa-building', url: '/organizations', hint: typeof I18n !== 'undefined' ? I18n.t('nav.organizations_hint') : 'Gestion des organisations' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.categories') : 'Catégories', icon: 'fas fa-tags', url: '/categories', hint: typeof I18n !== 'undefined' ? I18n.t('nav.categories_hint') : 'Gestion des catégories' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.notifications') : 'Notifications', icon: 'fas fa-bell', url: '/notifications', hint: typeof I18n !== 'undefined' ? I18n.t('nav.notifications_hint') : 'Centre de notifications' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.profile') : 'Profil', icon: 'fas fa-user-circle', url: '/profile', hint: typeof I18n !== 'undefined' ? I18n.t('nav.profile_hint') : 'Mon profil' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.settings') : 'Paramètres', icon: 'fas fa-cog', url: '/settings', hint: typeof I18n !== 'undefined' ? I18n.t('nav.settings_hint') : 'Paramètres du système' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.public_tracking') : 'Suivi public', icon: 'fas fa-search-location', url: '/suivi', hint: typeof I18n !== 'undefined' ? I18n.t('nav.public_tracking_hint') : 'Suivre un signalement' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.audit') : 'Audit', icon: 'fas fa-shield-alt', url: '/audit', hint: typeof I18n !== 'undefined' ? I18n.t('nav.audit_hint') : 'Journal d\'audit' },
            { label: typeof I18n !== 'undefined' ? I18n.t('nav.monthly_report') : 'Rapport mensuel', icon: 'fas fa-file-pdf', url: '/reports/export-monthly', hint: typeof I18n !== 'undefined' ? I18n.t('nav.monthly_report_hint') : 'PDF mensuel' }
        ];

        function openPalette() {
            overlay.classList.add('active');
            input.value = '';
            input.focus();
            renderResults('');
            activeIndex = -1;
        }
        function closePalette() {
            overlay.classList.remove('active');
            input.value = '';
        }
        function renderResults(query) {
            var q = query.toLowerCase().trim();
            var filtered = q ? pages.filter(function(p) {
                return p.label.toLowerCase().indexOf(q) !== -1 || p.hint.toLowerCase().indexOf(q) !== -1;
            }) : pages;
            activeIndex = filtered.length > 0 ? 0 : -1;
            results.innerHTML = filtered.map(function(p, i) {
                return '<a href="' + p.url + '" class="cmd-palette-item' + (i === 0 ? ' active' : '') + '" data-index="' + i + '">' +
                    '<i class="' + p.icon + '"></i>' +
                    '<div><div class="cmd-label">' + p.label + '</div><div class="cmd-hint">' + p.hint + '</div></div>' +
                    '</a>';
            }).join('');
        }
        function navigate(dir) {
            var allItems = results.querySelectorAll('.cmd-palette-item');
            if (allItems.length === 0) return;
            allItems.forEach(function(el) { el.classList.remove('active'); });
            activeIndex = (activeIndex + dir + allItems.length) % allItems.length;
            allItems[activeIndex].classList.add('active');
            allItems[activeIndex].scrollIntoView({ block: 'nearest' });
        }

        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closePalette();
        });
        input.addEventListener('input', function() { renderResults(this.value); });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePalette();
            else if (e.key === 'ArrowDown') { e.preventDefault(); navigate(1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); navigate(-1); }
            else if (e.key === 'Enter') {
                e.preventDefault();
                var active = results.querySelector('.cmd-palette-item.active');
                if (active) window.location.href = active.getAttribute('href');
            }
        });

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                if (overlay.classList.contains('active')) closePalette();
                else openPalette();
            }
        });
    })();
});
