<?php
namespace App\Jobs;

use App\Helpers\Database;

class SlaAlertJob extends Job {

    public function handle(): void {
        $db = Database::getConnection();

        $alertsSent = 0;

        // J-2 alerts
        $stmt = $db->query("
            SELECT r.id, r.tracking_code, r.title, r.assigned_to,
                   c.name as category_name, c.deadline_days,
                   DATEDIFF(DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY), CURDATE()) as days_left
            FROM reports r
            JOIN categories c ON r.category_id = c.id
            WHERE r.status IN ('assigned', 'in_progress')
              AND r.deleted_at IS NULL
              AND DATEDIFF(DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY), CURDATE()) = 2
              AND NOT EXISTS (SELECT 1 FROM sla_alerts WHERE report_id = r.id AND alert_type = 'j2')
        ");
        foreach ($stmt->fetchAll() as $r) {
            if ($r['assigned_to']) {
                $db->prepare(
                    "INSERT INTO notifications (user_id, type, title, message, data) VALUES (?, ?, ?, ?, ?)"
                )->execute([
                    $r['assigned_to'], 'sla_alert',
                    '⏰ Deadline J-2',
                    "Le signalement \"{$r['title']}\" ({$r['tracking_code']}) arrive à échéance dans 2 jours.",
                    json_encode(['report_id' => $r['id']])
                ]);
            }
            $db->prepare("INSERT IGNORE INTO sla_alerts (report_id, alert_type) VALUES (?, 'j2')")->execute([$r['id']]);
            $alertsSent++;
        }

        // J-1 alerts
        $stmt = $db->query("
            SELECT r.id, r.tracking_code, r.title, r.assigned_to,
                   c.deadline_days,
                   DATEDIFF(DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY), CURDATE()) as days_left
            FROM reports r
            JOIN categories c ON r.category_id = c.id
            WHERE r.status IN ('assigned', 'in_progress')
              AND r.deleted_at IS NULL
              AND DATEDIFF(DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY), CURDATE()) = 1
              AND NOT EXISTS (SELECT 1 FROM sla_alerts WHERE report_id = r.id AND alert_type = 'j1')
        ");
        foreach ($stmt->fetchAll() as $r) {
            if ($r['assigned_to']) {
                $db->prepare(
                    "INSERT INTO notifications (user_id, type, title, message, data) VALUES (?, ?, ?, ?, ?)"
                )->execute([
                    $r['assigned_to'], 'sla_alert',
                    '🔴 Deadline J-1 URGENT',
                    "Le signalement \"{$r['title']}\" ({$r['tracking_code']}) arrive à échéance DEMAIN.",
                    json_encode(['report_id' => $r['id']])
                ]);
            }
            $db->prepare("INSERT IGNORE INTO sla_alerts (report_id, alert_type) VALUES (?, 'j1')")->execute([$r['id']]);
            $alertsSent++;
        }

        // Overdue alerts
        $stmt = $db->query("
            SELECT r.id, r.tracking_code, r.title, r.assigned_to,
                   c.deadline_days,
                   DATEDIFF(CURDATE(), DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY)) as days_overdue
            FROM reports r
            JOIN categories c ON r.category_id = c.id
            WHERE r.status IN ('assigned', 'in_progress')
              AND r.deleted_at IS NULL
              AND DATE_ADD(r.created_at, INTERVAL c.deadline_days DAY) < CURDATE()
              AND NOT EXISTS (SELECT 1 FROM sla_alerts WHERE report_id = r.id AND alert_type = 'overdue')
        ");
        foreach ($stmt->fetchAll() as $r) {
            if ($r['assigned_to']) {
                $db->prepare(
                    "INSERT INTO notifications (user_id, type, title, message, data) VALUES (?, ?, ?, ?, ?)"
                )->execute([
                    $r['assigned_to'], 'sla_overdue',
                    '🚨 EN RETARD — ' . $r['days_overdue'] . ' jour(s)',
                    "Le signalement \"{$r['title']}\" ({$r['tracking_code']}) est en retard de {$r['days_overdue']} jour(s).",
                    json_encode(['report_id' => $r['id']])
                ]);
            }
            $db->prepare("INSERT IGNORE INTO sla_alerts (report_id, alert_type) VALUES (?, 'overdue')")->execute([$r['id']]);
            $alertsSent++;
        }

        if ($alertsSent > 0) {
            error_log("[SLA Job] {$alertsSent} alertes envoyées");
        }
    }

    public function queue(): string {
        return 'low';
    }

    public function maxTries(): int {
        return 1;
    }
}
