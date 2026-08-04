<?php
namespace App\Jobs;

use App\Helpers\Database;

class SendPushJob extends Job {
    private int $userId;
    private string $title;
    private string $body;
    private string $url;

    public function __construct(int $userId, string $title, string $body, string $url = '/notifications') {
        $this->userId = $userId;
        $this->title = $title;
        $this->body = $body;
        $this->url = $url;
    }

    public function handle(): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT endpoint, p256dh_key, auth_key FROM push_subscriptions WHERE user_id = ?");
        $stmt->execute([$this->userId]);
        $subs = $stmt->fetchAll();
        if (empty($subs)) return;

        $config = require __DIR__ . '/../Config/push.php';
        $webPush = new \Minishlink\WebPush\WebPush([
            'VAPID' => [
                'subject' => $config['vapid_subject'],
                'publicKey' => $config['vapid_public_key'],
                'privateKey' => $config['vapid_private_key'],
            ],
        ]);

        $payload = json_encode([
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
        ]);

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
            error_log("[Push Error] user={$this->userId}: " . ($resp ? $resp->getStatusCode() : 'timeout'));
        }
    }

    public function queue(): string {
        return 'push';
    }

    public function maxTries(): int {
        return 2;
    }
}
