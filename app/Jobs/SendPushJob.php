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

        foreach ($subs as $sub) {
            $payload = json_encode([
                'title' => $this->title,
                'body' => $this->body,
                'url' => $this->url,
            ]);

            $ch = curl_init($sub['endpoint']);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
            ]);
            curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            // Remove stale subscriptions
            if ($code === 410 || $code === 404) {
                $db->prepare("DELETE FROM push_subscriptions WHERE endpoint = ?")->execute([$sub['endpoint']]);
            }

            if ($error) {
                error_log("[Push Error] user={$this->userId}: {$error}");
            }
        }
    }

    public function queue(): string {
        return 'push';
    }

    public function maxTries(): int {
        return 2;
    }
}
