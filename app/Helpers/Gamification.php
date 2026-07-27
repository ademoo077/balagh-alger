<?php
namespace App\Helpers;

class Gamification {

    const POINTS = [
        'report_created' => 10,
        'report_resolved' => 25,
        'post_created' => 5,
        'comment_created' => 2,
        'like_received' => 1,
        'daily_login' => 1,
        'first_report' => 50,
    ];

    const LEVELS = [
        1 => ['name' => 'Citoyen', 'min' => 0, 'icon' => 'fa-user'],
        2 => ['name' => 'Citoyen actif', 'min' => 50, 'icon' => 'fa-user-check'],
        3 => ['name' => 'Observateur', 'min' => 150, 'icon' => 'fa-eye'],
        4 => ['name' => 'Gardien', 'min' => 350, 'icon' => 'fa-shield'],
        5 => ['name' => 'Protecteur', 'min' => 600, 'icon' => 'fa-shield-halved'],
        6 => ['name' => 'Héros', 'min' => 1000, 'icon' => 'fa-mask'],
        7 => ['name' => 'Légende', 'min' => 2000, 'icon' => 'fa-crown'],
    ];

    public static function addPoints(int $userId, string $reason, ?int $refId = null, ?string $refType = null): void {
        $points = self::POINTS[$reason] ?? 0;
        if ($points <= 0) return;

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO citizen_points (user_id, points, reason, reference_id, reference_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $points, $reason, $refId, $refType]);
    }

    public static function getTotalPoints(int $userId): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COALESCE(SUM(points), 0) FROM citizen_points WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function getLevel(int $userId): array {
        $points = self::getTotalPoints($userId);
        $level = self::LEVELS[1];
        $nextLevel = null;
        $prevPoints = 0;

        foreach (self::LEVELS as $num => $l) {
            if ($points >= $l['min']) {
                $level = array_merge($l, ['number' => $num, 'points' => $points]);
                $prevPoints = $l['min'];
            } else {
                if (!$nextLevel) $nextLevel = $l;
                break;
            }
        }

        $progress = 0;
        if ($nextLevel && ($nextLevel['min'] - $prevPoints) > 0) {
            $progress = round((($points - $prevPoints) / ($nextLevel['min'] - $prevPoints)) * 100);
        } elseif (!$nextLevel) {
            $progress = 100;
        }

        return array_merge($level, [
            'progress' => min(100, $progress),
            'next_level' => $nextLevel ? $nextLevel['name'] : null,
            'next_min' => $nextLevel ? $nextLevel['min'] : null,
        ]);
    }

    public static function getLeaderboard(int $limit = 20): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.id, u.first_name, u.last_name, u.avatar,
                   COALESCE(SUM(cp.points), 0) as total_points,
                   (SELECT COUNT(*) FROM reports r WHERE r.citizen_id = u.id AND r.deleted_at IS NULL) as report_count
            FROM users u
            LEFT JOIN citizen_points cp ON u.id = cp.user_id
            WHERE u.deleted_at IS NULL AND EXISTS (SELECT 1 FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = u.id AND r.name = 'citizen')
            GROUP BY u.id
            ORDER BY total_points DESC
            LIMIT {$limit}
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getRecentActivity(int $userId, int $limit = 10): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM citizen_points WHERE user_id = ? ORDER BY created_at DESC LIMIT {$limit}");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
