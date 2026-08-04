<?php $pageTitle = 'Notifications'; $activeTab = 'notifications'; ?>
<style>
.notif-empty { text-align: center; padding: 48px 24px; }
.notif-empty i { font-size: 3rem; color: var(--c-text-muted); opacity: 0.3; margin-bottom: 12px; }
.notif-empty h5 { font-weight: 700; margin-bottom: 4px; }
.notif-empty p { font-size: 0.85rem; color: var(--c-text-muted); }
.notif-item {
    display: flex; gap: 12px; padding: 14px; margin-bottom: 8px;
    background: var(--c-card); border: 1px solid var(--c-card-border);
    border-radius: var(--c-radius); transition: all 0.2s;
    text-decoration: none; color: inherit;
}
.notif-item:active { transform: scale(0.98); }
.notif-item.unread { border-left: 3px solid var(--c-accent); background: var(--c-accent-surface); }
.notif-icon {
    width: 40px; height: 40px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.9rem; flex-shrink: 0;
}
.notif-body { flex: 1; min-width: 0; }
.notif-title { font-weight: 600; font-size: 0.85rem; margin-bottom: 2px; }
.notif-msg { font-size: 0.78rem; color: var(--c-text-muted); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.notif-time { font-size: 0.68rem; color: var(--c-text-muted); white-space: nowrap; flex-shrink: 0; }
.notif-link {
    display: inline-flex; align-items: center; gap: 4px; margin-top: 6px;
    font-size: 0.75rem; color: var(--c-accent); font-weight: 600;
    text-decoration: none;
}
.notif-link:hover { text-decoration: underline; }
</style>

<div class="c-section-title c-anim-fade">
    <h6><i class="fas fa-bell"></i> Notifications</h6>
    <div style="display:flex;gap:8px;">
        <?php if (!empty($notifications)): ?>
        <form method="POST" action="/notifications/read-all" style="margin:0;">
            <input type="hidden" name="_token" value="<?= $csrfToken ?>">
            <button type="submit" class="c-btn c-btn-outline c-btn-sm" style="font-size:0.72rem;padding:6px 12px;">
                <i class="fas fa-check-double"></i> Tout marquer lu
            </button>
        </form>
        <?php endif; ?>
        <button class="c-btn c-btn-outline c-btn-sm" id="pushToggle" style="font-size:0.72rem;padding:6px 12px;display:none;">
            <i class="fas fa-bell-slash" id="pushToggleIcon"></i>
            <span id="pushToggleLabel">Push</span>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('pushToggle');
    var icon = document.getElementById('pushToggleIcon');
    var label = document.getElementById('pushToggleLabel');
    if (!btn || !window.BalaghPush) return;

    function updateUI() {
        if (window.BalaghPush.enabled) {
            btn.style.background = 'var(--c-accent-surface)';
            btn.style.color = 'var(--c-accent)';
            btn.style.borderColor = 'var(--c-accent)';
            icon.className = 'fas fa-bell';
            label.textContent = 'Activées';
        } else {
            btn.style.background = '';
            btn.style.color = '';
            btn.style.borderColor = '';
            icon.className = 'fas fa-bell-slash';
            label.textContent = 'Désactivées';
        }
    }

    function initPushToggle() {
        if (window.__swReg) {
            window.__swReg.pushManager.getSubscription().then(function(sub) {
                window.BalaghPush.enabled = !!sub;
                btn.style.display = 'inline-flex';
                updateUI();
                return;
            });
        } else {
            var stored = localStorage.getItem('balagh-push');
            if (stored === '1') window.BalaghPush.enabled = true;
            if (stored !== null) btn.style.display = 'inline-flex';
        }
        updateUI();
    }

    initPushToggle();
    // Retry if SW hasn't registered yet
    if (!window.__swReg) {
        var iv = setInterval(function() {
            if (window.__swReg) {
                clearInterval(iv);
                window.__swReg.pushManager.getSubscription().then(function(sub) {
                    window.BalaghPush.enabled = !!sub;
                    updateUI();
                });
            }
        }, 300);
    }

    btn.addEventListener('click', function() {
        if (window.BalaghPush.enabled) {
            window.BalaghPush.unsubscribe().then(function() { updateUI(); });
        } else {
            window.BalaghPush.subscribe().then(function() { updateUI(); }).catch(function() {});
        }
    });
});
</script>

<?php
$typeIcons = [
    'report_assigned' => ['fas fa-user-check', 'var(--c-accent-surface)', 'var(--c-accent)'],
    'report_status_change' => ['fas fa-exchange-alt', 'var(--c-cyan-surface)', 'var(--c-cyan)'],
    'report_comment' => ['fas fa-comment', 'var(--c-amber-surface)', 'var(--c-amber)'],
    'report_created' => ['fas fa-plus-circle', 'var(--c-green-surface)', 'var(--c-green)'],
    'report_resolved' => ['fas fa-check-circle', 'var(--c-green-surface)', 'var(--c-green)'],
    'badge' => ['fas fa-award', 'var(--c-purple-surface)', 'var(--c-purple)'],
];

if (empty($notifications)): ?>
<div class="notif-empty c-anim-fade">
    <i class="fas fa-bell-slash d-block"></i>
    <h5>Aucune notification</h5>
    <p>Vous serez notifié des mises à jour de vos signalements.</p>
</div>
<?php else: ?>
<div id="notifList">
<?php foreach ($notifications as $i => $n):
    $nData = json_decode($n['data'], true) ?? [];
    $reportId = $nData['report_id'] ?? null;
    $nUrl = $reportId ? '/reports/' . $reportId : '#';
    $icon = $typeIcons[$n['type']] ?? ['fas fa-bell', 'var(--c-accent-surface)', 'var(--c-accent)'];
?>
<div class="notif-item <?= $n['is_read'] ? '' : 'unread' ?> c-anim-fade" style="animation-delay:<?= $i * 0.03 ?>s;">
    <div class="notif-icon" style="background:<?= $icon[1] ?>;color:<?= $icon[2] ?>;">
        <i class="<?= $icon[0] ?>"></i>
    </div>
    <div class="notif-body">
        <div class="notif-title"><?= htmlspecialchars($n['title']) ?></div>
        <div class="notif-msg"><?= htmlspecialchars($n['message']) ?></div>
        <?php if ($reportId): ?>
        <a href="<?= $nUrl ?>" class="notif-link"><i class="fas fa-arrow-right"></i> Voir le signalement</a>
        <?php endif; ?>
    </div>
    <div class="notif-time"><?= \App\Helpers\Helper::timeAgo($n['created_at']) ?></div>
</div>
<?php endforeach; ?>
</div>

<?php
// Mark all as read on page view (via AJAX)
$unreadIds = array_column(array_filter($notifications, function($n) { return !$n['is_read']; }), 'id');
if (!empty($unreadIds)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ids = <?= json_encode($unreadIds) ?>;
    var token = <?= json_encode($csrfToken) ?>;
    ids.forEach(function(id) {
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': token},
            body: '_token=' + encodeURIComponent(token)
        }).catch(function() {});
    });
    // Update unread badge in header
    var badge = document.querySelector('.c-header-btn .notif-badge');
    if (badge) badge.style.display = 'none';
});
</script>
<?php endif; ?>
<?php endif; ?>
