<?php
namespace App\Helpers;

use App\Helpers\Database;

class AuditLog {
    public static function log(string $action, string $model, ?int $modelId = null, $oldValues = null, $newValues = null): void {
        $userId = Session::getUserId();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        Database::getConnection()->prepare(
            "INSERT INTO audit_logs (user_id, action, model, model_id, old_values, new_values, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        )->execute([
            $userId,
            $action,
            $model,
            $modelId,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
            $ip,
            $ua,
        ]);
    }
}
