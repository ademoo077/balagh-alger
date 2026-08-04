<?php
namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Helpers\Database;
use App\Helpers\Session;

class PushController extends Controller {
    public function subscribe(): void {
        $this->auth();
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['endpoint'])) {
            $this->json(['error' => 'Invalid subscription'], 400);
            return;
        }
        $db = Database::getConnection();
        $userId = Session::getUserId();
        $stmt = $db->prepare("INSERT INTO push_subscriptions (user_id, endpoint, p256dh_key, auth_key) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $data['endpoint'], $data['keys']['p256dh'] ?? '', $data['keys']['auth'] ?? '']);
        $this->json(['ok' => true]);
    }

    public function unsubscribe(): void {
        $this->auth();
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['endpoint'])) { $this->json(['error' => 'Invalid'], 400); return; }
        $db = Database::getConnection();
        $db->prepare("DELETE FROM push_subscriptions WHERE user_id = ? AND endpoint = ?")->execute([Session::getUserId(), $data['endpoint']]);
        $this->json(['ok' => true]);
    }

    public static function sendPush(int $userId, string $title, string $body, string $url = '/notifications'): void {
        if (\App\Helpers\Queue::isAvailable()) {
            \App\Helpers\Queue::dispatch(\App\Jobs\SendPushJob::class, [
                'userId' => $userId, 'title' => $title, 'body' => $body, 'url' => $url,
            ]);
            return;
        }
        self::sendPushSync($userId, $title, $body, $url);
    }

    public static function sendPushSync(int $userId, string $title, string $body, string $url = '/notifications'): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT endpoint, p256dh_key, auth_key FROM push_subscriptions WHERE user_id = ?");
        $stmt->execute([$userId]);
        $subs = $stmt->fetchAll();
        if (empty($subs)) return;

        $config = require __DIR__ . '/../../Config/push.php';
        $webPush = new \Minishlink\WebPush\WebPush([
            'VAPID' => [
                'subject' => $config['vapid_subject'],
                'publicKey' => $config['vapid_public_key'],
                'privateKey' => $config['vapid_private_key'],
            ],
        ]);

        $payload = json_encode(['title' => $title, 'body' => $body, 'url' => $url]);

        foreach ($subs as $sub) {
            $webPush->queueNotification(
                new \Minishlink\WebPush\Notification(
                    $sub['endpoint'],
                    $payload,
                    $sub['p256dh_key'] ?: null,
                    $sub['auth_key'] ?: null
                )
            );
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) continue;
            $endpoint = $report->getEndpoint();
            $resp = $report->getResponse();
            if ($resp && ($resp->getStatusCode() === 410 || $resp->getStatusCode() === 404)) {
                $db->prepare("DELETE FROM push_subscriptions WHERE endpoint = ?")->execute([$endpoint]);
            }
            error_log("[Push Error] user={$userId}: " . ($resp ? $resp->getStatusCode() : 'timeout'));
        }
    }
}
