<?php
namespace App\Jobs;

use App\Helpers\Database;
use App\Helpers\Queue;

class SendNotificationJob extends Job {
    private int $userId;
    private string $type;
    private string $title;
    private string $message;
    private ?array $data;
    private bool $push;

    public function __construct(int $userId, string $type, string $title, string $message, ?array $data = null, bool $push = true) {
        $this->userId = $userId;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->data = $data;
        $this->push = $push;
    }

    public function handle(): void {
        $db = Database::getConnection();

        // Create in-app notification
        $db->prepare(
            "INSERT INTO notifications (user_id, type, title, message, data) VALUES (?, ?, ?, ?, ?)"
        )->execute([$this->userId, $this->type, $this->title, $this->message, $this->data ? json_encode($this->data) : null]);

        // Dispatch push notification
        if ($this->push) {
            Queue::dispatch(SendPushJob::class, [
                'user_id' => $this->userId,
                'title' => $this->title,
                'body' => $this->message,
                'url' => $this->data['url'] ?? '/notifications',
            ], 'push');
        }
    }

    public function queue(): string {
        return 'default';
    }
}
