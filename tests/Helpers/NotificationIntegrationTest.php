<?php
namespace Tests\Helpers;

use App\Helpers\Notification;
use Tests\DatabaseTestCase;

class NotificationIntegrationTest extends DatabaseTestCase
{
    private int $adminId;

    protected function setUp(): void
    {
        parent::setUp();
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute(['admin@test.dz']);
        $this->adminId = (int)$stmt->fetchColumn();
    }

    public function testCreateInsertsNotification(): void
    {
        Notification::create($this->adminId, 'info', 'Test Title', 'Test Message', ['key' => 'val']);

        $stmt = $this->pdo->prepare("SELECT * FROM notifications WHERE user_id = ?");
        $stmt->execute([$this->adminId]);
        $notif = $stmt->fetch();

        $this->assertNotFalse($notif);
        $this->assertEquals('Test Title', $notif['title']);
        $this->assertEquals('Test Message', $notif['message']);
        $this->assertEquals('info', $notif['type']);
        $this->assertEquals(0, (int)$notif['is_read']);
    }

    public function testGetUnreadCountReturnsZeroWhenNoNotifications(): void
    {
        $count = Notification::getUnreadCount($this->adminId);
        $this->assertEquals(0, $count);
    }

    public function testGetUnreadCountReturnsCorrectCount(): void
    {
        Notification::create($this->adminId, 'info', 'N1', 'Msg1');
        Notification::create($this->adminId, 'info', 'N2', 'Msg2');

        $count = Notification::getUnreadCount($this->adminId);
        $this->assertEquals(2, $count);
    }

    public function testMarkAsReadUpdatesNotification(): void
    {
        Notification::create($this->adminId, 'info', 'Test', 'Msg');

        $stmt = $this->pdo->query("SELECT id FROM notifications WHERE user_id = {$this->adminId} LIMIT 1");
        $notifId = (int)$stmt->fetchColumn();

        Notification::markAsRead($notifId, $this->adminId);

        $check = $this->pdo->prepare("SELECT is_read FROM notifications WHERE id = ?");
        $check->execute([$notifId]);
        $this->assertEquals(1, (int)$check->fetchColumn());
    }

    public function testMarkAllAsReadMarksAllNotifications(): void
    {
        Notification::create($this->adminId, 'info', 'N1', 'Msg1');
        Notification::create($this->adminId, 'info', 'N2', 'Msg2');

        Notification::markAllAsRead($this->adminId);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$this->adminId]);
        $this->assertEquals(0, (int)$stmt->fetchColumn());
    }

    public function testGetRecentReturnsNotificationsWithLimit(): void
    {
        for ($i = 0; $i < 15; $i++) {
            Notification::create($this->adminId, 'info', "N{$i}", "Msg{$i}");
        }

        $recent = Notification::getRecent($this->adminId);
        $this->assertCount(10, $recent);
    }

    public function testGetRecentReturnsLatestFirst(): void
    {
        Notification::create($this->adminId, 'info', 'First', 'Old');
        sleep(1);
        Notification::create($this->adminId, 'info', 'Second', 'New');

        $recent = Notification::getRecent($this->adminId);
        $this->assertEquals('Second', $recent[0]['title']);
    }
}
