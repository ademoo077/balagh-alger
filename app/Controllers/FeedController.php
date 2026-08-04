<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Rbac;
use App\Helpers\Csrf;

class FeedController extends Controller {

    protected string $layout = 'layouts/citizen';

    public function index(): void {
        $this->auth();
        if (!Rbac::isRole('citizen')) { $this->redirect('/dashboard'); return; }

        $db = Database::getConnection();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $stmt = $db->prepare("
            SELECT p.*, u.first_name, u.last_name, u.avatar,
                   (SELECT COUNT(*) FROM community_likes WHERE post_id = p.id) as likes_count,
                   (SELECT COUNT(*) FROM community_comments WHERE post_id = p.id AND deleted_at IS NULL) as comments_count,
                   (SELECT COUNT(*) FROM community_photos WHERE post_id = p.id) as photos_count,
                   EXISTS(SELECT 1 FROM community_likes WHERE post_id = p.id AND user_id = ?) as user_liked
            FROM community_posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.deleted_at IS NULL
            ORDER BY p.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute([Session::getUserId()]);
        $posts = $stmt->fetchAll();

        foreach ($posts as &$post) {
            if ($post['photos_count'] > 0) {
                $photoStmt = $db->prepare("SELECT filename FROM community_photos WHERE post_id = ? ORDER BY sort_order ASC");
                $photoStmt->execute([$post['id']]);
                $post['photos'] = $photoStmt->fetchAll();
            } else {
                $post['photos'] = [];
            }
        }

        $this->view('citizen/feed', compact('posts'));
    }

    public function store(): void {
        $this->auth();
        if (!Rbac::isRole('citizen')) {
            echo json_encode(['success' => false]);
            return;
        }

        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!Csrf::verify($token)) {
            echo json_encode(['success' => false, 'message' => 'CSRF invalide']);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $reportId = !empty($_POST['report_id']) ? (int)$_POST['report_id'] : null;
        $allowedTypes = ['discussion', 'update', 'feedback', 'question'];
        $postType = in_array($_POST['post_type'] ?? '', $allowedTypes) ? $_POST['post_type'] : 'discussion';

        if (!$title && !$body) {
            echo json_encode(['success' => false, 'message' => 'Contenu requis']);
            return;
        }

        $db = Database::getConnection();
        $userId = Session::getUserId();

        $stmt = $db->prepare("INSERT INTO community_posts (user_id, title, body, post_type, report_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $title ?: 'Publication', $body, $postType, $reportId]);
        $postId = (int)$db->lastInsertId();

        // Photos
        if (!empty($_FILES['photos']['name'][0])) {
            $uploadDir = __DIR__ . '/../../public/uploads/community/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $order = 0;
            foreach ($_FILES['photos']['name'] as $i => $name) {
                if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $mime = $finfo->file($_FILES['photos']['tmp_name'][$i]);
                if (!in_array($mime, $allowed)) continue;
                $safeExt = match($mime) {
                    'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp', default => 'bin',
                };
                $filename = 'post_' . $postId . '_' . ($i + 1) . '.' . $safeExt;
                if (move_uploaded_file($_FILES['photos']['tmp_name'][$i], $uploadDir . $filename)) {
                    $db->prepare("INSERT INTO community_photos (post_id, filename, original_name, mime_type, file_size, sort_order) VALUES (?, ?, ?, ?, ?, ?)")
                        ->execute([$postId, $filename, $name, $mime, $_FILES['photos']['size'][$i], $order++]);
                }
            }
        }

        \App\Helpers\Gamification::addPoints($userId, 'post_created', $postId, 'post');

        echo json_encode(['success' => true, 'post_id' => $postId]);
    }

    public function like(int $postId): void {
        $this->auth();
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!Csrf::verify($token)) { echo json_encode(['success' => false]); return; }
        $userId = Session::getUserId();
        $db = Database::getConnection();

        $check = $db->prepare("SELECT id FROM community_likes WHERE user_id = ? AND post_id = ?");
        $check->execute([$userId, $postId]);

        if ($check->fetch()) {
            $db->prepare("DELETE FROM community_likes WHERE user_id = ? AND post_id = ?")->execute([$userId, $postId]);
            $db->prepare("UPDATE community_posts SET likes_count = GREATEST(0, likes_count - 1) WHERE id = ?")->execute([$postId]);
            echo json_encode(['liked' => false]);
        } else {
            $db->prepare("INSERT INTO community_likes (user_id, post_id) VALUES (?, ?)")->execute([$userId, $postId]);
            $db->prepare("UPDATE community_posts SET likes_count = likes_count + 1 WHERE id = ?")->execute([$postId]);
            echo json_encode(['liked' => true]);
        }
    }

    public function comment(int $postId): void {
        $this->auth();
        $token = $_POST['_token'] ?? '';
        if (!Csrf::verify($token)) { echo json_encode(['success' => false]); return; }
        $body = trim($_POST['body'] ?? '');
        if (!$body) { echo json_encode(['success' => false]); return; }

        $userId = Session::getUserId();
        $db = Database::getConnection();

        $stmt = $db->prepare("INSERT INTO community_comments (user_id, post_id, body) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $postId, $body]);
        $commentId = (int)$db->lastInsertId();

        $db->prepare("UPDATE community_posts SET comments_count = comments_count + 1 WHERE id = ?")->execute([$postId]);

        \App\Helpers\Gamification::addPoints($userId, 'comment_created', $postId, 'post');

        $user = $db->prepare("SELECT first_name, last_name, avatar FROM users WHERE id = ?");
        $user->execute([$userId]);
        $user = $user->fetch();

        echo json_encode([
            'success' => true,
            'comment' => [
                'id' => $commentId,
                'body' => $body,
                'user' => $user,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
