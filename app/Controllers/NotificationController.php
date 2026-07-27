<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Controllers\Controller;

class NotificationController extends Controller {
    public function index(): void {
        $this->auth();
        $userId = Session::getUserId();

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $notifications = Database::getConnection()->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 8");
            $notifications->execute([$userId]);
            $notifications = $notifications->fetchAll();
            require VIEW_PATH . '/notifications/_dropdown_items.php';
            exit;
        }

        $notifications = Database::getConnection()->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
        $notifications->execute([$userId]);
        $notifications = $notifications->fetchAll();
        $csrfToken = \App\Helpers\Csrf::generate();
        $this->view('notifications/index', compact('notifications', 'csrfToken'));
    }

    public function markRead(int $id): void {
        $this->auth();
        $this->checkCsrf('/notifications');
        \App\Helpers\Notification::markAsRead($id, Session::getUserId());
        $this->redirect('/notifications');
    }

    public function markAllRead(): void {
        $this->auth();
        $this->checkCsrf('/notifications');
        \App\Helpers\Notification::markAllAsRead(Session::getUserId());
        $this->withSuccess(__('notifications.all_read'));
        $this->redirect('/notifications');
    }

    public function count(): void {
        $this->auth();
        $userId = Session::getUserId();
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        $this->json(['count' => (int)$stmt->fetchColumn()]);
    }
}
