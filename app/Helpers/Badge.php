<?php
namespace App\Helpers;

class Badge {
    private static array $definitions = [
        'first_report' => ['name' => 'Premier signalement', 'icon' => 'fa-star', 'color' => '#f59e0b', 'desc' => 'Votre premier signalement'],
        'report_5' => ['name' => '5 signalements', 'icon' => 'fa-fire', 'color' => '#ef4444', 'desc' => '5 signalements soumis'],
        'report_10' => ['name' => '10 signalements', 'icon' => 'fa-bolt', 'color' => '#8b5cf6', 'desc' => '10 signalements soumis'],
        'report_25' => ['name' => '25 signalements', 'icon' => 'fa-gem', 'color' => '#06b6d4', 'desc' => '25 signalements soumis'],
        'report_50' => ['name' => 'Citoyen actif', 'icon' => 'fa-shield-halved', 'color' => '#10b981', 'desc' => '50 signalements soumis'],
        'first_resolved' => ['name' => 'Résolution', 'icon' => 'fa-check-circle', 'color' => '#22c55e', 'desc' => 'Votre premier signalement résolu'],
        'resolved_5' => ['name' => 'Impact local', 'icon' => 'fa-award', 'color' => '#3b82f6', 'desc' => '5 signalements résolus'],
        'resolved_10' => ['name' => 'Héros citoyen', 'icon' => 'fa-medal', 'color' => '#f59e0b', 'desc' => '10 signalements résolus'],
        'speedster' => ['name' => 'Rapide', 'icon' => 'fa-stopwatch', 'color' => '#06b6d4', 'desc' => 'Signalement résolu en moins de 24h'],
        'commenter' => ['name' => 'Engagé', 'icon' => 'fa-comments', 'color' => '#8b5cf6', 'desc' => '5 commentaires sur vos signalements'],
    ];

    public static function getDefinitions(): array {
        return self::$definitions;
    }

    public static function award(int $userId, string $badgeKey): bool {
        if (!isset(self::$definitions[$badgeKey])) return false;
        $def = self::$definitions[$badgeKey];
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT IGNORE INTO user_badges (user_id, badge_key, badge_name, badge_icon, badge_color) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$userId, $badgeKey, $def['name'], $def['icon'], $def['color']]);
        if ($stmt->rowCount() > 0) {
            Notification::create($userId, 'badge', 'Badge obtenu !', 'Vous avez gagné le badge "' . $def['name'] . '"', ['badge' => $badgeKey]);
            return true;
        }
        return false;
    }

    public static function getUserBadges(int $userId): array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM user_badges WHERE user_id = ? ORDER BY earned_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function checkAndAward(int $userId): array {
        $earned = [];
        $db = Database::getConnection();
        $countStmt = $db->prepare("SELECT COUNT(*) FROM reports WHERE citizen_id = ? AND deleted_at IS NULL");
        $countStmt->execute([$userId]);
        $count = (int) $countStmt->fetchColumn();
        $stmt = $db->prepare("SELECT COUNT(*) FROM reports WHERE citizen_id = ? AND status = 'resolved' AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        $resolved = (int)$stmt->fetchColumn();

        if ($count >= 1 && self::award($userId, 'first_report')) $earned[] = 'first_report';
        if ($count >= 5 && self::award($userId, 'report_5')) $earned[] = 'report_5';
        if ($count >= 10 && self::award($userId, 'report_10')) $earned[] = 'report_10';
        if ($count >= 25 && self::award($userId, 'report_25')) $earned[] = 'report_25';
        if ($count >= 50 && self::award($userId, 'report_50')) $earned[] = 'report_50';
        if ($resolved >= 1 && self::award($userId, 'first_resolved')) $earned[] = 'first_resolved';
        if ($resolved >= 5 && self::award($userId, 'resolved_5')) $earned[] = 'resolved_5';
        if ($resolved >= 10 && self::award($userId, 'resolved_10')) $earned[] = 'resolved_10';

        return $earned;
    }

    public static function getUserStats(int $userId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM reports WHERE citizen_id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        $total = (int)$stmt->fetchColumn();
        $stmt = $db->prepare("SELECT COUNT(*) FROM reports WHERE citizen_id = ? AND status = 'resolved' AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        $resolved = (int)$stmt->fetchColumn();
        $stmt = $db->prepare("SELECT COUNT(*) FROM reports WHERE citizen_id = ? AND status IN ('submitted','acknowledged','assigned','in_progress') AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        $active = (int)$stmt->fetchColumn();
        return ['total' => $total, 'resolved' => $resolved, 'active' => $active, 'rate' => $total > 0 ? round(($resolved / $total) * 100) : 0];
    }
}
