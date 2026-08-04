<?php
\App\Helpers\I18n::init();
$currentLang = \App\Helpers\I18n::getLang();
$isRtl = \App\Helpers\I18n::isRtl();
$dir = \App\Helpers\I18n::getDir();
$csrfToken = $csrfToken ?? (\App\Helpers\Session::get('csrf_token') ?? \App\Helpers\Csrf::generate());
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $dir ?>" data-theme="dark" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= $pageTitle ?? 'Balagh Alger' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700;800&family=Noto+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all';this.onload=null;">
    <noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></noscript>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" rel="stylesheet">
    <link href="/assets/css/citizen.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/assets/img/icon-192.png" type="image/png">
    <meta name="theme-color" content="#6366f1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Balagh">
    <link rel="apple-touch-icon" href="/assets/img/icon-192.png">
    <link rel="apple-touch-startup-image" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" href="/assets/img/icon-512.png">
    <link rel="apple-touch-startup-image" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" href="/assets/img/icon-512.png">
    <link rel="apple-touch-startup-image" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)" href="/assets/img/icon-512.png">
    <link rel="apple-touch-startup-image" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" href="/assets/img/icon-512.png">
    <link rel="apple-touch-startup-image" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)" href="/assets/img/icon-512.png">
    <link rel="apple-touch-startup-image" media="(device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3)" href="/assets/img/icon-512.png">
    <link rel="apple-touch-startup-image" media="(device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3)" href="/assets/img/icon-512.png">
    <meta name="csrf-token" content="<?= $csrfToken ?>">
    <style>
        :root {
            --sat: env(safe-area-inset-top, 0px);
            --sar: env(safe-area-inset-right, 0px);
            --sab: env(safe-area-inset-bottom, 0px);
            --sal: env(safe-area-inset-left, 0px);
        }
        body { padding-top: calc(var(--c-header-height) + var(--sat)); padding-bottom: calc(var(--c-nav-height) + 16px + var(--sab)); }
        .c-header { top: var(--sat); }
        .c-bottom-nav { padding-bottom: calc(8px + var(--sab)); }
    </style>
    <script>
    (function(){
        var t=localStorage.getItem('balagh-theme');
        if(t){document.documentElement.setAttribute('data-theme',t);document.documentElement.setAttribute('data-bs-theme',t);}
        else if(window.matchMedia('(prefers-color-scheme:light)').matches){
            document.documentElement.setAttribute('data-theme','light');
            document.documentElement.setAttribute('data-bs-theme','light');
        }
        window.matchMedia('(prefers-color-scheme:light)').addEventListener('change',function(e){
            if(!localStorage.getItem('balagh-theme')){
                var v=e.matches?'light':'dark';
                document.documentElement.setAttribute('data-theme',v);
                document.documentElement.setAttribute('data-bs-theme',v);
            }
        });
    })();
    </script>
    <script>
    window.__translations = <?= file_get_contents(__DIR__ . '/../../../lang/' . $currentLang . '.json') ?>;
    window.__lang = '<?= $currentLang ?>';
    </script>
</head>
<body>

    <!-- Splash Screen -->
    <div id="splashScreen" class="c-splash">
        <div class="c-splash-spinner"><div class="c-splash-dot"></div><div class="c-splash-dot"></div><div class="c-splash-dot"></div></div>
    </div>

    <!-- Offline Bar (géré par OfflineManager) -->
    <div id="offlineBar" class="c-offline-bar" style="display:none;"></div>

    <!-- Install PWA Banner (Android/Chrome) -->
    <div id="installBanner" class="c-install-banner" style="display:none;" role="alert">
        <div class="c-install-banner-inner">
            <div class="c-install-banner-icon"><i class="fas fa-bullhorn"></i></div>
            <div class="c-install-banner-text">
                <strong>Installer Balagh</strong>
                <span>Ajoutez à votre écran d'accueil</span>
            </div>
            <button class="c-install-banner-btn" id="installBtn" aria-label="Installer l'application">Installer</button>
            <button class="c-install-banner-close" id="installDismiss" aria-label="Fermer"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <!-- iOS Install Banner (beforeinstallprompt not supported on iOS) -->
    <div id="iosInstallBanner" class="c-install-banner" style="display:none;" role="alert">
        <div class="c-install-banner-inner">
            <div class="c-install-banner-icon"><i class="fab fa-apple"></i></div>
            <div class="c-install-banner-text">
                <strong>Installer Balagh</strong>
                <span>Appuyez sur <i class="fas fa-share-from-square"></i> &gt; Ajouter à l'écran d'accueil</span>
            </div>
            <button class="c-install-banner-close" id="iosInstallDismiss" aria-label="Fermer"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <!-- Header -->
    <header class="c-header" role="banner">
        <a href="/home" class="c-header-brand" aria-label="Accueil Balagh">
            <i class="fas fa-bullhorn"></i>
            <span>Balagh</span>
        </a>
        <div class="c-header-search" role="search">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="cSearch" placeholder="<?= __('reports.search_placeholder') ?>" autocomplete="off" aria-label="<?= __('reports.search_placeholder') ?>">
        </div>
        <div class="c-header-actions">
            <button class="c-header-btn" id="cThemeToggle" title="Thème" aria-label="Basculer le thème">
                <i class="fas fa-sun" id="cThemeIconSun"></i>
                <i class="fas fa-moon" id="cThemeIconMoon" style="display:none;"></i>
            </button>
            <button class="c-header-btn" id="cLangToggle" title="Langue" aria-label="Changer la langue">
                <i class="fas fa-globe"></i>
            </button>
            <a href="/notifications" class="c-header-btn" title="Notifications" style="text-decoration:none;color:inherit;" aria-label="Notifications">
                <i class="fas fa-bell"></i>
                <?php $unread = \App\Helpers\Notification::getUnreadCount(\App\Helpers\Session::getUserId()); ?>
                <?php if ($unread > 0): ?>
                <span class="notif-badge"><?= $unread > 9 ? '9+' : $unread ?></span>
                <?php endif; ?>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="c-container <?= $containerClass ?? '' ?>" id="cMain">
        <?php if ($msg = \App\Helpers\Session::getFlash('success')): ?>
        <div class="c-alert c-alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?= htmlspecialchars($msg) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($msg = \App\Helpers\Session::getFlash('error')): ?>
        <div class="c-alert c-alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= htmlspecialchars($msg) ?></span>
        </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <!-- Back to top -->
    <button class="c-back-to-top" id="cBackToTop" title="Retour en haut" aria-label="Retour en haut de page">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Bottom Navigation -->
    <nav class="c-bottom-nav" id="cBottomNav" role="navigation" aria-label="Navigation principale">
        <a href="/home" class="c-nav-item <?= ($activeTab ?? '') === 'home' ? 'active' : '' ?>" aria-label="Accueil">
            <i class="fas fa-house"></i>
            <span><?= __('nav.home') ?></span>
        </a>
        <a href="/reports" class="c-nav-item <?= ($activeTab ?? '') === 'reports' ? 'active' : '' ?>" aria-label="Mes signalements">
            <i class="fas fa-clipboard-list"></i>
            <span><?= __('nav.reports') ?></span>
            <span class="c-nav-badge-count" id="pendingBadge" style="display:none;"></span>
        </a>
        <a href="/citizen/map" class="c-nav-item <?= ($activeTab ?? '') === 'map' ? 'active' : '' ?>" aria-label="Carte">
            <i class="fas fa-map-location-dot"></i>
            <span><?= __('nav.map') ?></span>
        </a>
        <a href="/reports/create" class="c-nav-item center-btn" title="Signaler un problème" aria-label="Nouveau signalement">
            <i class="fas fa-plus"></i>
        </a>
        <a href="/feed" class="c-nav-item <?= ($activeTab ?? '') === 'feed' ? 'active' : '' ?>" aria-label="Communauté">
            <i class="fas fa-users"></i>
            <span><?= __('nav.feed') ?></span>
        </a>
        <a href="/my-profile" class="c-nav-item <?= ($activeTab ?? '') === 'profile' ? 'active' : '' ?>" aria-label="Profil">
            <i class="fas fa-user"></i>
            <span><?= __('nav.profile') ?></span>
        </a>
    </nav>

    <!-- Toast (live region for screen readers) -->
    <div class="c-toast" id="cToast" role="alert" aria-live="polite"></div>

    <!-- Chatbot Overlay -->
    <div class="c-chat-overlay" id="chatOverlay"></div>

    <!-- Chatbot FAB -->
    <button class="c-chat-fab" id="chatFab" title="<?= __('chatbot.title') ?>" aria-label="<?= __('chatbot.title') ?>">
        <i class="fas fa-comment-dots"></i>
    </button>

    <!-- Chatbot Panel -->
    <div class="c-chat-panel" id="chatPanel" role="dialog" aria-label="<?= __('chatbot.title') ?>">
        <div class="c-chat-header">
            <div class="c-chat-header-info">
                <div class="c-chat-avatar"><i class="fas fa-robot"></i></div>
                <div>
                    <div class="c-chat-title"><?= __('chatbot.title') ?></div>
                    <div class="c-chat-status"><?= __('chatbot.online') ?></div>
                </div>
            </div>
            <button class="c-chat-close" id="chatClose" aria-label="Fermer le chat"><i class="fas fa-times"></i></button>
        </div>
        <div class="c-chat-messages" id="chatMessages" role="log" aria-live="polite"></div>
        <div class="c-chat-quick" id="chatQuickActions"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="/assets/js/i18n.js"></script>
    <script src="/assets/js/citizen.js"></script>
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').then(function(reg) {
            window.__swReg = reg;
            window.BalaghPush.autoSubscribe();
        }).catch(function() {});
    }
    window.BalaghPush = {
        vapidPublicKey: '<?= (require __DIR__ . '/../../Config/push.php')['vapid_public_key'] ?>',
        enabled: false,
        subscribe: function() {
            if (!('Notification' in window) || !window.__swReg) return Promise.reject('no support');
            return Notification.requestPermission().then(function(perm) {
                if (perm !== 'granted') return null;
                return window.__swReg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: window.BalaghPush.urlBase64ToUint8Array(window.BalaghPush.vapidPublicKey)
                }).then(function(sub) {
                    var p256dh = sub.getKey('p256dh');
                    var auth = sub.getKey('auth');
                    fetch('/api/push/subscribe', {
                        method: 'POST',
                        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
                        body: JSON.stringify({
                            endpoint: sub.endpoint,
                            keys: {
                                p256dh: btoa(String.fromCharCode.apply(null, new Uint8Array(p256dh))),
                                auth: btoa(String.fromCharCode.apply(null, new Uint8Array(auth)))
                            }
                        })
                    });
                    window.BalaghPush.enabled = true;
                    localStorage.setItem('balagh-push', '1');
                    return sub;
                });
            });
        },
        unsubscribe: function() {
            if (!window.__swReg) return Promise.resolve();
            return window.__swReg.pushManager.getSubscription().then(function(sub) {
                if (!sub) return;
                fetch('/api/push/unsubscribe', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
                    body: JSON.stringify({endpoint: sub.endpoint})
                });
                sub.unsubscribe();
                window.BalaghPush.enabled = false;
                localStorage.setItem('balagh-push', '0');
            });
        },
        urlBase64ToUint8Array: function(b64) {
            var s = atob(b64.replace(/-/g,'+').replace(/_/g,'/'));
            var a = new Uint8Array(s.length);
            for (var i=0;i<s.length;i++) a[i]=s.charCodeAt(i);
            return a;
        },
        autoSubscribe: function() {
            if (!('Notification' in window) || !window.__swReg) return;
            if (localStorage.getItem('balagh-push') === '1') {
                window.__swReg.pushManager.getSubscription().then(function(sub) {
                    if (sub) { window.BalaghPush.enabled = true; return; }
                    window.BalaghPush.subscribe().catch(function() {});
                });
            }
        }
    };

    // ============ PWA INSTALL BANNER ============
    (function() {
        if (window.matchMedia('(display-mode: standalone)').matches) return;
        var banner = document.getElementById('installBanner');
        var iosBanner = document.getElementById('iosInstallBanner');

        // Android: beforeinstallprompt
        if (!localStorage.getItem('balagh-install-dismissed')) {
            var deferredPrompt = null;
            if (banner) {
                var installBtn = document.getElementById('installBtn');
                var dismissBtn = document.getElementById('installDismiss');
                if (installBtn && dismissBtn) {
                    window.addEventListener('beforeinstallprompt', function(e) {
                        e.preventDefault();
                        deferredPrompt = e;
                        banner.style.display = 'block';
                    });
                    installBtn.addEventListener('click', function() {
                        if (!deferredPrompt) return;
                        deferredPrompt.prompt();
                        deferredPrompt.userChoice.then(function(result) {
                            if (result.outcome === 'accepted') {
                                banner.style.display = 'none';
                                if (typeof CToast !== 'undefined') CToast.show('Balagh installé !', 'success');
                            }
                            deferredPrompt = null;
                        });
                    });
                    dismissBtn.addEventListener('click', function() {
                        banner.style.display = 'none';
                        localStorage.setItem('balagh-install-dismissed', '1');
                    });
                    window.addEventListener('appinstalled', function() {
                        banner.style.display = 'none';
                        deferredPrompt = null;
                    });
                }
            }
        }

        // iOS: custom banner (no beforeinstallprompt on iOS)
        if (iosBanner && /iPad|iPhone|iPod/.test(navigator.userAgent) && !navigator.standalone) {
            if (!localStorage.getItem('balagh-ios-install-dismissed')) {
                setTimeout(function() { iosBanner.style.display = 'block'; }, 3000);
                document.getElementById('iosInstallDismiss').addEventListener('click', function() {
                    iosBanner.style.display = 'none';
                    localStorage.setItem('balagh-ios-install-dismissed', '1');
                });
            }
        }
    })();

    </script>
</body>
</html>
