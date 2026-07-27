<?php if (empty($notifications)): ?>
<div class="notif-dropdown-empty"><i class="fas fa-bell-slash d-block mb-2" style="font-size:1.2rem;opacity:0.3;"></i><?= __('notifications.none') ?></div>
<?php else: foreach ($notifications as $n):
    $nData = json_decode($n['data'], true) ?? [];
    $reportId = $nData['report_id'] ?? null;
    $nUrl = $reportId ? '/reports/' . $reportId : null;
    $typeIcons = [
        'report_assigned' => ['fas fa-user-check', 'var(--accent-surface)', 'var(--accent)'],
        'report_status_change' => ['fas fa-exchange-alt', 'var(--cyan-surface)', 'var(--cyan)'],
        'report_comment' => ['fas fa-comment', 'var(--amber-surface)', 'var(--amber)'],
        'report_created' => ['fas fa-plus-circle', 'var(--green-surface)', 'var(--green)'],
        'report_resolved' => ['fas fa-check-circle', 'var(--green-surface)', 'var(--green)'],
    ];
    [$nIcon, $nBg, $nColor] = $typeIcons[$n['type']] ?? ['fas fa-bell', 'var(--accent-surface)', 'var(--accent)'];
?>
<a href="<?= $nUrl ?: '#' ?>" class="notif-dropdown-item <?= $n['is_read'] ? '' : 'unread' ?>" data-id="<?= $n['id'] ?>">
    <div class="notif-icon" style="background:<?= $nBg ?>;color:<?= $nColor ?>;"><i class="<?= $nIcon ?>"></i></div>
    <div class="notif-text">
        <div class="notif-title"><?= htmlspecialchars($n['title']) ?></div>
        <div class="notif-msg"><?= htmlspecialchars($n['message']) ?></div>
    </div>
    <div class="notif-time"><?= \App\Helpers\Helper::timeAgo($n['created_at']) ?></div>
</a>
<?php endforeach; endif; ?>
