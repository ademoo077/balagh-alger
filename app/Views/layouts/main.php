<?php
\App\Helpers\I18n::init();
$currentLang = \App\Helpers\I18n::getLang();
$isRtl = \App\Helpers\I18n::isRtl();
$dir = \App\Helpers\I18n::getDir();
$csrfToken = $csrfToken ?? (\App\Helpers\Session::get('csrf_token') ?? \App\Helpers\Csrf::generate());
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $dir ?>" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? __('app.name') ?> — <?= __('app.name') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6366f1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/assets/img/icon-192.png">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta name="csrf-token" content="<?= $csrfToken ?>">
    <script>
    (function(){var t=localStorage.getItem('balagh-theme');if(t){document.documentElement.setAttribute('data-bs-theme',t);}})();
    </script>
    <script>
    window.__translations = <?= file_get_contents(__DIR__ . '/../../../lang/' . $currentLang . '.json') ?>;
    window.__lang = '<?= $currentLang ?>';
    </script>
    <?php if ($isRtl): ?>
    <style>
        /* Sidebar: move to right */
        .sidebar { border-right: none; border-left: 1px solid var(--border); left: auto; right: 0; }
        .sidebar-brand { flex-direction: row-reverse; }
        .sidebar-nav .nav-link { flex-direction: row-reverse; justify-content: flex-start; }
        .sidebar-nav .nav-link i { margin-left: 0; margin-right: 0; }
        .sidebar-nav .nav-link .badge { margin-left: 0 !important; margin-right: auto !important; }
        .sidebar-nav .nav-section { text-align: right; padding-right: 0; padding-left: 24px; }
        .sidebar .nav-link.active::before { left: auto; right: 0; border-radius: 3px 0 0 3px; }
        .sidebar-footer .nav-link { flex-direction: row-reverse; }
        .sidebar-footer .nav-link i { margin-left: 0; margin-right: 0; }

        /* Main content: swap margin */
        #page-content-wrapper { margin-left: 0; margin-right: 260px; }
        .sidebar.collapsed ~ #page-content-wrapper { margin-right: 0 !important; }

        /* Navbar: reverse direction */
        .top-navbar { flex-direction: row-reverse; }
        .toggle-btn { margin-left: auto; margin-right: 0; }
        .navbar-actions { margin-left: 0; margin-right: auto; flex-direction: row-reverse; }
        .navbar-actions .dropdown-menu { left: 0; right: auto; }
        .search-box { margin-left: 0; margin-right: auto; }

        /* Content: RTL direction */
        .content-area { direction: rtl; }
        .user-info { text-align: right; }
        .badge { margin-right: 0; margin-left: 8px; }
        .alert .btn-close { margin-left: auto; margin-right: 0; }
        .dropdown-menu { text-align: right; }
        .form-floating > label { transform-origin: right; }
        .table th, .table td { text-align: right; }
        .text-start { text-align: right !important; }
        .text-end { text-align: left !important; }

        /* Bootstrap utility swaps */
        .me-1 { margin-right: 0.25rem !important; margin-left: 0 !important; }
        .me-2 { margin-right: 0.5rem !important; margin-left: 0 !important; }
        .me-3 { margin-right: 1rem !important; margin-left: 0 !important; }
        .ms-1 { margin-left: 0.25rem !important; margin-right: 0 !important; }
        .ms-2 { margin-left: 0.5rem !important; margin-right: 0 !important; }
        .ms-3 { margin-left: 1rem !important; margin-right: 0 !important; }
        .ps-1 { padding-right: 0.25rem !important; padding-left: 0 !important; }
        .ps-2 { padding-right: 0.5rem !important; padding-left: 0 !important; }
        .ps-3 { padding-right: 1rem !important; padding-left: 0 !important; }
        .pe-1 { padding-left: 0.25rem !important; padding-right: 0 !important; }
        .pe-2 { padding-left: 0.5rem !important; padding-right: 0 !important; }
        .pe-3 { padding-left: 1rem !important; padding-right: 0 !important; }

        /* Wizard steps RTL */
        .wizard-steps { flex-direction: row-reverse; }
        .wizard-connector { direction: ltr; }
        .wizard-step .step-label { text-align: right; }

        /* Content area flex adjustments */
        .page-header { flex-direction: row-reverse; }
        .page-header .btn { flex-direction: row-reverse; }
        .card-header { flex-direction: row-reverse; }
        .d-flex.justify-content-between { flex-direction: row-reverse; }
        .d-flex.justify-content-end { flex-direction: row-reverse; }
        .d-flex.justify-content-start { flex-direction: row-reverse; }

        /* Alerts */
        .alert .btn-close { margin-left: auto; margin-right: 0; }

        @media (max-width: 768px) {
            #page-content-wrapper { margin-right: 0 !important; }
        }

        /* Mobile sidebar hide direction */
        .sidebar.collapsed { transform: translateX(100%); }

        /* Search icon RTL */
        .search-box .search-icon { left: auto; right: 11px; }

        /* Wizard step connector RTL */
        .wizard-step { border-inline-start: none; }
        .wizard-step.active { border-inline-start: 3px solid var(--accent); }
        .wizard-connector { margin-inline-start: 12px; }
        .wizard-step.active::before { inset-inline-start: -50%; }
    </style>
    <?php endif; ?>
</head>
<body class="<?= $isRtl ? 'rtl' : '' ?>">
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon"><i class="fas fa-bullhorn"></i></div>
                <span class="brand-text"><?= __('app.name') ?></span>
            </div>
            <nav class="sidebar-nav">
                <?php if (\App\Helpers\Session::isAuthenticated()): ?>
                <?php
                $rbacSidebar = \App\Helpers\Rbac::class;
                $isCitizenSidebar = $rbacSidebar::isRole('citizen');
                $isStaffSidebar = $rbacSidebar::isStaff();
                ?>
                <?php if ($isCitizenSidebar): ?>
                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : '' ?>" href="/dashboard">
                    <i class="fas fa-house-signal"></i>
                    <span><?= __('nav.my_tracking') ?></span>
                </a>
                <div class="nav-section"><?= __('nav.my_reports') ?></div>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/reports') && !str_starts_with($_SERVER['REQUEST_URI'], '/reports/create') ? 'active' : '' ?>" href="/reports">
                    <i class="fas fa-clipboard-list"></i>
                    <span><?= __('nav.track_my_reports') ?></span>
                </a>
                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/reports/create' ? 'active' : '' ?>" href="/reports/create">
                    <i class="fas fa-circle-plus"></i>
                    <span><?= __('nav.report_problem') ?></span>
                </a>
                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/impact' ? 'active' : '' ?>" href="/impact">
                    <i class="fas fa-rocket"></i>
                    <span>Mon Impact</span>
                </a>
                <div class="nav-section"><?= __('nav.help') ?></div>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/notifications') ? 'active' : '' ?>" href="/notifications">
                    <i class="fas fa-bell"></i>
                    <span><?= __('nav.notifications') ?></span>
                    <?php $unread = \App\Helpers\Notification::getUnreadCount(\App\Helpers\Session::getUserId()); if ($unread > 0): ?>
                        <span class="badge" style="background:var(--amber-surface);color:var(--amber);"><?= $unread ?></span>
                    <?php endif; ?>
                </a>
                <?php else: ?>
                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : '' ?>" href="/dashboard">
                    <i class="fas fa-th-large"></i>
                    <span><?= __('nav.dashboard') ?></span>
                </a>
                <div class="nav-section"><?= __('nav.reports') ?></div>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/reports') ? 'active' : '' ?>" href="/reports">
                    <i class="fas fa-flag"></i>
                    <span><?= __('nav.all_reports') ?></span>
                    <?php if ($rbacSidebar::minLevel(3)):
                    $db = \App\Helpers\Database::getConnection();
                    $pending = $db->query("SELECT COUNT(*) FROM reports WHERE status IN ('submitted','acknowledged') AND deleted_at IS NULL")->fetchColumn();
                    if ($pending > 0): ?>
                        <span class="badge" style="background:var(--red-surface);color:var(--red);"><?= $pending ?></span>
                    <?php endif; endif; ?>
                </a>
                <?php if ($rbacSidebar::minLevel(3)): ?>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/interventions') ? 'active' : '' ?>" href="/interventions">
                    <i class="fas fa-tools"></i>
                    <span><?= __('nav.interventions') ?></span>
                </a>
                <?php endif; ?>
                <?php if ($rbacSidebar::hasAny(['users.view', 'organizations.view'])): ?>
                <div class="nav-section"><?= __('nav.management') ?></div>
                <?php if ($rbacSidebar::has('users.view')): ?>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/users') ? 'active' : '' ?>" href="/users">
                    <i class="fas fa-users"></i>
                    <span><?= __('nav.users') ?></span>
                </a>
                <?php endif; ?>
                <?php if ($rbacSidebar::has('organizations.view')): ?>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/organizations') ? 'active' : '' ?>" href="/organizations">
                    <i class="fas fa-building"></i>
                    <span><?= __('nav.organizations') ?></span>
                </a>
                <?php endif; ?>
                <?php endif; ?>
                <?php if ($rbacSidebar::has('dairas.view')): ?>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/dairas') ? 'active' : '' ?>" href="/dairas">
                    <i class="fas fa-map-marked-alt"></i>
                    <span><?= __('nav.dairas') ?></span>
                </a>
                <?php endif; ?>
                <?php if ($rbacSidebar::has('categories.view')): ?>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/categories') ? 'active' : '' ?>" href="/categories">
                    <i class="fas fa-tags"></i>
                    <span><?= __('nav.categories') ?></span>
                </a>
                <?php endif; ?>
                <?php if ($rbacSidebar::isRole('admin_central', 'resp_central', 'admin_local', 'chef_unite')): ?>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/section-communes') ? 'active' : '' ?>" href="/section-communes">
                    <i class="fas fa-map-pin"></i>
                    <span><?= __('nav.section_communes') ?></span>
                </a>
                <?php endif; ?>
                <div class="nav-section"><?= __('nav.system') ?></div>
                <?php if ($rbacSidebar::has('landing.manage')): ?>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/landing') ? 'active' : '' ?>" href="/admin/landing">
                    <i class="fas fa-globe"></i>
                    <span>Landing Page</span>
                </a>
                <?php endif; ?>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/notifications') ? 'active' : '' ?>" href="/notifications">
                    <i class="fas fa-bell"></i>
                    <span><?= __('nav.notifications') ?></span>
                    <?php $unread = \App\Helpers\Notification::getUnreadCount(\App\Helpers\Session::getUserId()); if ($unread > 0): ?>
                        <span class="badge" style="background:var(--amber-surface);color:var(--amber);"><?= $unread ?></span>
                    <?php endif; ?>
                </a>
                <?php if ($rbacSidebar::canViewAudit()): ?>
                <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/audit' ? 'active' : '' ?>" href="/audit">
                    <i class="fas fa-history"></i>
                    <span><?= __('nav.audit') ?></span>
                </a>
                <?php endif; ?>
                <?php if ($rbacSidebar::canManageSettings()): ?>
                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/settings') ? 'active' : '' ?>" href="/settings">
                    <i class="fas fa-cog"></i>
                    <span><?= __('nav.settings') ?></span>
                </a>
                <?php endif; ?>
                <?php endif; ?>
                <?php endif; ?>
            </nav>
            <div class="sidebar-footer">
                <form method="POST" action="/logout" class="d-inline">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <button type="submit" class="nav-link text-decoration-none" style="color:var(--text-muted);background:none;border:none;cursor:pointer;width:100%;text-align:<?php echo $isRtl ? 'right' : 'left'; ?>;" data-confirm="<?= __('ui.confirm_logout') ?>">
                        <i class="fas fa-sign-out-alt"></i>
                        <span><?= __('nav.logout') ?></span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Page Content -->
        <div id="page-content-wrapper" class="w-100">
            <!-- Top Navbar -->
            <header class="top-navbar">
                <button class="toggle-btn" id="toggleSidebar" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-box d-none d-md-flex">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="<?= __('nav.search') ?>" id="globalSearch">
                </div>
                <div class="navbar-actions">
                    <!-- Language Switcher -->
                    <div class="dropdown me-2">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" data-bs-toggle="dropdown" style="border-radius:50px;padding:4px 12px;font-size:0.82rem;">
                            <span><?= $currentLang === 'ar' ? '🇩🇿' : '🇫🇷' ?></span>
                            <span class="d-none d-md-inline"><?= $currentLang === 'ar' ? 'العربية' : 'FR' ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width:140px;">
                            <li><a class="dropdown-item <?= $currentLang === 'fr' ? 'active' : '' ?>" href="#" onclick="I18n.setLang('fr');return false;">
                                <span class="me-1">🇫🇷</span> Français
                                <?php if ($currentLang === 'fr'): ?><i class="fas fa-check ms-auto text-success"></i><?php endif; ?>
                            </a></li>
                            <li><a class="dropdown-item <?= $currentLang === 'ar' ? 'active' : '' ?>" href="#" onclick="I18n.setLang('ar');return false;">
                                <span class="me-1">🇩🇿</span> العربية
                                <?php if ($currentLang === 'ar'): ?><i class="fas fa-check ms-auto text-success"></i><?php endif; ?>
                            </a></li>
                        </ul>
                    </div>
                    <!-- Theme Toggle -->
                    <button class="theme-toggle" id="themeToggle" title="<?= __('nav.change_theme') ?>">
                        <i class="fas fa-sun icon-sun"></i>
                        <i class="fas fa-moon icon-moon"></i>
                    </button>
                    <?php if (\App\Helpers\Session::isAuthenticated()): ?>
                    <div style="position:relative;">
                        <button class="icon-btn" id="notifDropdownToggle" style="position:relative;background:none;border:none;cursor:pointer;padding:6px;color:var(--text-secondary);">
                            <i class="fas fa-bell" style="font-size:1rem;"></i>
                            <span id="notifBadge" class="nav-notif-badge" style="display:<?= ($unread ?? 0) > 0 ? 'flex' : 'none' ?>;"><?= $unread ?? 0 ?></span>
                        </button>
                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-dropdown-header">
                                <h6><i class="fas fa-bell me-1" style="color:var(--accent);"></i> <?= __('nav.notifications') ?></h6>
                                <form method="POST" action="/notifications/read-all" class="d-inline"><input type="hidden" name="_token" value="<?= $csrfToken ?>"><button type="submit" class="btn btn-link p-0" style="font-size:0.72rem;font-weight:600;color:var(--accent);text-decoration:none;"><?= __('notifications.mark_all_read') ?></button></form>
                            </div>
                            <div class="notif-dropdown-list" id="notifDropdownList">
                                <div class="notif-dropdown-empty"><i class="fas fa-bell-slash d-block mb-2" style="font-size:1.2rem;opacity:0.3;"></i><?= __('notifications.none') ?></div>
                            </div>
                            <div class="notif-dropdown-footer">
                                <a href="/notifications"><i class="fas fa-list-ul me-1"></i><?= __('nav.notifications') ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="#" class="user-menu dropdown-toggle" data-bs-toggle="dropdown">
                            <?php $navAvatar = \App\Helpers\Session::getAvatar(); ?>
                            <?php if ($navAvatar): ?>
                            <img src="<?= htmlspecialchars($navAvatar) ?>" alt="Avatar" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--accent);">
                            <?php else: ?>
                            <div class="user-avatar">
                                <?= strtoupper(substr(\App\Helpers\Session::getUserName(), 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <div class="user-info d-none d-md-block">
                                <span class="user-name"><?= \App\Helpers\Session::getUserName() ?></span>
                                <?php $primaryRole = \App\Helpers\Rbac::getPrimaryRole(); ?>
                                <?php if ($primaryRole): ?>
                                <small class="text-muted d-block" style="font-size:0.7rem;line-height:1;">
                                    <?= ucfirst(str_replace('_', ' ', $primaryRole)) ?>
                                </small>
                                <?php endif; ?>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile"><i class="fas fa-user me-2"></i> <?= __('nav.my_profile') ?></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form method="POST" action="/logout" class="d-inline"><input type="hidden" name="_token" value="<?= $csrfToken ?>"><button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start" data-confirm="<?= __('ui.confirm_logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> <?= __('nav.logout') ?></button></form></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="content-area">
                <?php if ($msg = \App\Helpers\Session::getFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show animate-fade-in" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                <?php if ($msg = \App\Helpers\Session::getFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show animate-fade-in" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Main Content -->
                <?= $content ?>
            </div>

            <!-- Footer -->
            <footer class="app-footer">
                <i class="fas fa-shield-alt me-1"></i> <?= date('Y') ?> <?= __('app.name') ?> &mdash; <?= __('app.subtitle') ?> &mdash; <?= __('app.wilaya') ?> <?= __('app.version') ?>
            </footer>
        </div>
    </div>

    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Command Palette -->
<div id="cmdPaletteOverlay" class="cmd-palette-overlay">
    <div class="cmd-palette">
        <input type="text" id="cmdPaletteInput" class="cmd-palette-input" placeholder="<?= __('cmd_palette.placeholder') ?>" autocomplete="off">
        <div id="cmdPaletteResults" class="cmd-palette-results"></div>
        <div class="cmd-palette-footer">
            <span><kbd>&uarr;</kbd><kbd>&darr;</kbd> naviguer</span>
            <span><kbd>Entr</kbd> ouvrir</span>
            <span><kbd>Echap</kbd> fermer</span>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/assets/js/i18n.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').then(function(reg) {
            window.__swReg = reg;
        }).catch(function(e) { console.log('SW error:', e); });
    }

    window.BalaghPush = {
        vapidPublicKey: '<?= defined('VAPID_PUBLIC') ? VAPID_PUBLIC : '' ?>',
        subscribe: function() {
            if (!('Notification' in window) || !window.__swReg) return Promise.reject('no support');
            return Notification.requestPermission().then(function(perm) {
                if (perm !== 'granted') return null;
                return window.__swReg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: window.BalaghPush.urlBase64ToUint8Array(window.BalaghPush.vapidPublicKey)
                }).then(function(sub) {
                    fetch('/api/push/subscribe', {
                        method: 'POST',
                        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
                        body: JSON.stringify({endpoint: sub.endpoint, keys: {p256dh: btoa(String.fromCharCode.apply(null, new Uint8Array(sub.getKey('p256dh')))), auth: btoa(String.fromCharCode.apply(null, new Uint8Array(sub.getKey('auth'))))}})
                    });
                    return sub;
                });
            });
        },
        urlBase64ToUint8Array: function(b64) {
            var s = atob(b64.replace(/-/g,'+').replace(/_/g,'/'));
            var a = new Uint8Array(s.length);
            for (var i=0;i<s.length;i++) a[i]=s.charCodeAt(i);
            return a;
        }
    };
    </script>

    <!-- Theme Customizer -->
    <button class="theme-customizer-toggle" id="themeCustomizerToggle" title="Personnaliser le thème">
        <i class="fas fa-palette"></i>
    </button>
    <div class="theme-customizer-panel" id="themeCustomizerPanel">
        <h6><i class="fas fa-palette me-1" style="color:var(--accent);"></i> <?= __('nav.change_theme') ?></h6>
        <div class="theme-section-label">Couleur d'accent</div>
        <div class="theme-color-grid"></div>
        <button class="theme-reset-btn"><i class="fas fa-rotate-left me-1"></i> Réinitialiser</button>
    </div>
</body>
</html>
