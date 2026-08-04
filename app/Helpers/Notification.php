<?php
namespace App\Helpers;

use App\Helpers\Database;
use App\Helpers\Queue;

class Notification {
    public static function create(int $userId, string $type, string $title, string $message, $data = null): void {
        Database::getConnection()->prepare(
            "INSERT INTO notifications (user_id, type, title, message, data) VALUES (?, ?, ?, ?, ?)"
        )->execute([$userId, $type, $title, $message, $data ? json_encode($data) : null]);
    }

    public static function dispatch(int $userId, string $type, string $title, string $message, $data = null, bool $push = true): void {
        if (Queue::isAvailable()) {
            Queue::dispatch(\App\Jobs\SendNotificationJob::class, [
                'userId' => $userId, 'type' => $type, 'title' => $title,
                'message' => $message, 'data' => $data, 'push' => $push,
            ]);
        } else {
            self::create($userId, $type, $title, $message, $data);
            if ($push) {
                self::sendPushNow($userId, $title, $message);
            }
        }
    }

    public static function sendPushNow(int $userId, string $title, string $body, string $url = '/notifications'): void {
        \App\Controllers\Api\PushController::sendPush($userId, $title, $body, $url);
    }

    public static function getUnreadCount(int $userId): int {
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function getRecent(int $userId, int $limit = 10): array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT {$limit}");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function markAsRead(int $notificationId, int $userId): void {
        Database::getConnection()->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND user_id = ?")->execute([$notificationId, $userId]);
    }

    public static function markAllAsRead(int $userId): void {
        Database::getConnection()->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0")->execute([$userId]);
    }
}
