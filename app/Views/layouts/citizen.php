<?php
\App\Helpers\I18n::init();
$currentLang = \App\Helpers\I18n::getLang();
$isRtl = \App\Helpers\I18n::isRtl();
$dir = \App\Helpers\I18n::getDir();
$csrfToken = $csrfToken ?? (\App\Helpers\Session::get('csrf_token') ?? \App\Helpers\Csrf::generate());
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $dir ?>" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= $pageTitle ?? 'Balagh Alger' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700;800&family=Noto+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="/assets/css/citizen.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6366f1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/assets/img/icon-192.png">
    <meta name="csrf-token" content="<?= $csrfToken ?>">
    <script>
    (function(){var t=localStorage.getItem('balagh-theme');if(t){document.documentElement.setAttribute('data-bs-theme',t);}})();
    </script>
    <script>
    window.__translations = <?= file_get_contents(__DIR__ . '/../../../lang/' . $currentLang . '.json') ?>;
    window.__lang = '<?= $currentLang ?>';
    </script>
</head>
<body>

    <!-- Offline Banner -->
    <div id="offlineBanner" class="c-offline-banner">
        <i class="fas fa-wifi-slash" style="margin-right:6px;"></i> Vous êtes hors ligne — les envois seront retardés
    </div>

    <!-- Header -->
    <header class="c-header">
        <a href="/home" class="c-header-brand">
            <i class="fas fa-bullhorn"></i>
            <span>Balagh</span>
        </a>
        <div class="c-header-search">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="cSearch" placeholder="<?= __('reports.search_placeholder') ?>" autocomplete="off">
        </div>
        <div class="c-header-actions">
            <button class="c-header-btn" id="cThemeToggle" title="Thème">
                <i class="fas fa-sun" id="cThemeIconSun"></i>
                <i class="fas fa-moon" id="cThemeIconMoon" style="display:none;"></i>
            </button>
            <button class="c-header-btn" id="cLangToggle" title="Langue">
                <i class="fas fa-globe"></i>
            </button>
            <a href="/notifications" class="c-header-btn" title="Notifications" style="text-decoration:none;color:inherit;">
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
            <span><?= $msg ?></span>
        </div>
        <?php endif; ?>
        <?php if ($msg = \App\Helpers\Session::getFlash('error')): ?>
        <div class="c-alert c-alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= $msg ?></span>
        </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <!-- Bottom Navigation -->
    <nav class="c-bottom-nav" id="cBottomNav">
        <a href="/home" class="c-nav-item <?= ($activeTab ?? '') === 'home' ? 'active' : '' ?>">
            <i class="fas fa-map-location-dot"></i>
            <span><?= __('nav.home') ?></span>
        </a>
        <a href="/reports" class="c-nav-item <?= ($activeTab ?? '') === 'reports' ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list"></i>
            <span><?= __('nav.reports') ?></span>
            <?php if (($unreadCount ?? 0) > 0): ?>
            <span class="c-nav-badge"></span>
            <?php endif; ?>
        </a>
        <a href="/reports/create" class="c-nav-item center-btn" title="Signaler un problème">
            <i class="fas fa-plus"></i>
        </a>
        <a href="/feed" class="c-nav-item <?= ($activeTab ?? '') === 'feed' ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span><?= __('nav.feed') ?></span>
        </a>
        <a href="/my-profile" class="c-nav-item <?= ($activeTab ?? '') === 'profile' ? 'active' : '' ?>">
            <i class="fas fa-user"></i>
            <span><?= __('nav.profile') ?></span>
        </a>
    </nav>

    <!-- Toast -->
    <div class="c-toast" id="cToast"></div>

    <!-- Chatbot Overlay -->
    <div class="c-chat-overlay" id="chatOverlay"></div>

    <!-- Chatbot FAB -->
    <button class="c-chat-fab" id="chatFab" title="<?= __('chatbot.title') ?>">
        <i class="fas fa-comment-dots"></i>
    </button>

    <!-- Chatbot Panel -->
    <div class="c-chat-panel" id="chatPanel">
        <div class="c-chat-header">
            <div class="c-chat-header-info">
                <div class="c-chat-avatar"><i class="fas fa-robot"></i></div>
                <div>
                    <div class="c-chat-title"><?= __('chatbot.title') ?></div>
                    <div class="c-chat-status"><?= __('chatbot.online') ?></div>
                </div>
            </div>
            <button class="c-chat-close" id="chatClose"><i class="fas fa-times"></i></button>
        </div>
        <div class="c-chat-messages" id="chatMessages"></div>
        <div class="c-chat-quick" id="chatQuickActions"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/assets/js/i18n.js"></script>
    <script src="/assets/js/citizen.js"></script>
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').then(function(reg) {
            window.__swReg = reg;
            // Listen for sync complete messages
            navigator.serviceWorker.addEventListener('message', function(event) {
                if (event.data && event.data.type === 'SYNC_COMPLETE') {
                    if (typeof CToast !== 'undefined') {
                        CToast.show('Vos signalements hors ligne ont été envoyés !', 'success');
                    }
                }
            });
        }).catch(function(e) {});
    }
    // Offline/online banner
    (function() {
        var banner = document.getElementById('offlineBanner');
        if (!banner) return;
        function update() {
            banner.style.display = navigator.onLine ? 'none' : 'block';
            document.body.style.paddingTop = navigator.onLine ? '' : 'calc(var(--c-header-height) + 36px)';
        }
        window.addEventListener('online', update);
        window.addEventListener('offline', update);
        update();
    })();
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
</body>
</html>
