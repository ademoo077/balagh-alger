<?php
/**
 * SLA Deadline Alert Cron Script
 * Run: php /var/www/balagh-alger/cron/sla_alerts.php
 * Add to crontab: 0 9 * * * php /var/www/balagh-alger/cron/sla_alerts.php
 */
$root = dirname(__DIR__);
require_once $root . '/vendor/autoload.php';
require_once $root . '/app/Config/paths.php';
require_once APP_PATH . '/Helpers/Session.php';
require_once APP_PATH . '/Helpers/Database.php';
require_once APP_PATH . '/Helpers/Notification.php';

$db = Database::getConnection();

$alertsSent = 0;

// J-2 alerts (deadline in 2 days)
$stmt = $db->query("
    SELECT r.id, r.tracking_code, r.title, r.assigned_to, r.citizen_id,
           c.name as category_name, c.deadline_days,
           DATEDIFF(DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY), CURDATE()) as days_left
    FROM reports r
    JOIN categories c ON r.category_id = c.id
    WHERE r.status IN ('assigned', 'in_progress')
      AND r.deleted_at IS NULL
      AND DATEDIFF(DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY), CURDATE()) = 2
      AND NOT EXISTS (SELECT 1 FROM sla_alerts WHERE report_id = r.id AND alert_type = 'j2')
");

$reports = $stmt->fetchAll();
foreach ($reports as $r) {
    if ($r['assigned_to']) {
        Notification::create($r['assigned_to'], 'sla_alert',
            '⏰ Deadline J-2',
            "Le signalement \"{$r['title']}\" ({$r['tracking_code']}) arrive à échéance dans 2 jours.",
            ['report_id' => $r['id']]
        );
    }
    $db->prepare("INSERT IGNORE INTO sla_alerts (report_id, alert_type) VALUES (?, 'j2')")->execute([$r['id']]);
    $alertsSent++;
}

// J-1 alerts (deadline tomorrow)
$stmt = $db->query("
    SELECT r.id, r.tracking_code, r.title, r.assigned_to, r.citizen_id,
           c.name as category_name, c.deadline_days,
           DATEDIFF(DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY), CURDATE()) as days_left
    FROM reports r
    JOIN categories c ON r.category_id = c.id
    WHERE r.status IN ('assigned', 'in_progress')
      AND r.deleted_at IS NULL
      AND DATEDIFF(DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY), CURDATE()) = 1
      AND NOT EXISTS (SELECT 1 FROM sla_alerts WHERE report_id = r.id AND alert_type = 'j1')
");

$reports = $stmt->fetchAll();
foreach ($reports as $r) {
    if ($r['assigned_to']) {
        Notification::create($r['assigned_to'], 'sla_alert',
            '🔴 Deadline J-1 URGENT',
            "Le signalement \"{$r['title']}\" ({$r['tracking_code']}) arrive à échéance DEMAIN.",
            ['report_id' => $r['id']]
        );
    }
    $db->prepare("INSERT IGNORE INTO sla_alerts (report_id, alert_type) VALUES (?, 'j1')")->execute([$r['id']]);
    $alertsSent++;
}

// Overdue alerts (deadline passed)
$stmt = $db->query("
    SELECT r.id, r.tracking_code, r.title, r.assigned_to, r.citizen_id,
           c.name as category_name, c.deadline_days,
           DATEDIFF(CURDATE(), DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY)) as days_overdue
    FROM reports r
    JOIN categories c ON r.category_id = c.id
    WHERE r.status IN ('assigned', 'in_progress')
      AND r.deleted_at IS NULL
      AND DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY) < CURDATE()
      AND NOT EXISTS (SELECT 1 FROM sla_alerts WHERE report_id = r.id AND alert_type = 'overdue')
");

$reports = $stmt->fetchAll();
foreach ($reports as $r) {
    if ($r['assigned_to']) {
        Notification::create($r['assigned_to'], 'sla_overdue',
            '🚨 EN RETARD — ' . $r['days_overdue'] . ' jour(s)',
            "Le signalement \"{$r['title']}\" ({$r['tracking_code']}) est en retard de {$r['days_overdue']} jour(s).",
            ['report_id' => $r['id']]
        );
    }
    $db->prepare("INSERT IGNORE INTO sla_alerts (report_id, alert_type) VALUES (?, 'overdue')")->execute([$r['id']]);
    $alertsSent++;
}

echo date('Y-m-d H:i:s') . " — SLA Cron: {$alertsSent} alertes envoyées\n";
