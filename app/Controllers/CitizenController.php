<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Rbac;
use App\Helpers\Helper;

class CitizenController extends Controller {

    protected string $layout = 'layouts/citizen';

    public function home(): void {
        $this->auth();
        if (!Rbac::isRole('citizen')) { $this->redirect('/dashboard'); return; }

        $db = Database::getConnection();
        $userId = Session::getUserId();

        $total = $this->count($db, "r.citizen_id = ?", [$userId]);
        $inProgress = $this->count($db, "r.citizen_id = ? AND r.status IN ('submitted','acknowledged','assigned','in_progress')", [$userId]);
        $resolved = $this->count($db, "r.citizen_id = ? AND r.status IN ('resolved','validated')", [$userId]);

        $categories = $db->query("SELECT id, name, icon, color FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();

        $stmt = $db->prepare("
            SELECT r.id, r.tracking_code, r.title, r.status, r.priority, r.latitude, r.longitude, r.created_at,
                   c.name as category_name, c.icon as category_icon, c.color as category_color,
                   com.name as commune_name
            FROM reports r
            JOIN categories c ON r.category_id = c.id
            JOIN communes com ON r.commune_id = com.id
            WHERE r.deleted_at IS NULL AND r.latitude IS NOT NULL AND r.longitude IS NOT NULL
            AND r.citizen_id = ?
            ORDER BY r.created_at DESC LIMIT 50
        ");
        $stmt->execute([$userId]);
        $mapReports = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT r.id, r.tracking_code, r.title, r.status, r.priority, r.created_at,
                   c.name as category_name, c.icon as category_icon, c.color as category_color,
                   com.name as commune_name
            FROM reports r
            JOIN categories c ON r.category_id = c.id
            JOIN communes com ON r.commune_id = com.id
            WHERE r.deleted_at IS NULL AND r.citizen_id = ?
            ORDER BY r.created_at DESC LIMIT 8
        ");
        $stmt->execute([$userId]);
        $recentReports = $stmt->fetchAll();

        $stats = \App\Helpers\Badge::getUserStats($userId);

        $this->view('citizen/home', compact(
            'total', 'inProgress', 'resolved', 'categories', 'mapReports', 'recentReports', 'stats'
        ));
    }

    public function quickReport(): void {
        $this->auth();
        if (!Rbac::isRole('citizen')) { $this->redirect('/dashboard'); return; }

        $db = Database::getConnection();
        $categories = $db->query("SELECT id, name, icon, color FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();

        $this->view('citizen/quick-report', compact('categories'));
    }

    public function quickReportStore(): void {
        $this->auth();
        header('Content-Type: application/json');

        if (!Rbac::isRole('citizen')) {
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            return;
        }

        $categoryId = (int)($_POST['category_id'] ?? 0);
        if (!$categoryId) {
            echo json_encode(['success' => false, 'message' => 'Catégorie requise']);
            return;
        }

        $db = Database::getConnection();
        $trackingCode = Helper::generateTrackingCode();
        $subcategoryId = !empty($_POST['subcategory_id']) ? (int)$_POST['subcategory_id'] : null;
        $lat = $_POST['latitude'] ?? null;
        $lng = $_POST['longitude'] ?? null;

        // Resolve org + commune from GPS or default
        $communeId = null;
        $dairaId = null;
        if ($lat && $lng) {
            // Try to find commune from coordinates
            $geo = $db->prepare("SELECT c.id as commune_id, d.id as daira_id FROM communes c JOIN dairas d ON c.daira_id = d.id ORDER BY ABS(c.latitude - ?) + ABS(c.longitude - ?) LIMIT 1");
            $geo->execute([$lat, $lng]);
            $geoRow = $geo->fetch();
            if ($geoRow) {
                $communeId = $geoRow['commune_id'];
                $dairaId = $geoRow['daira_id'];
            }
        }

        // Fallback: use citizen's daira
        if (!$dairaId) {
            $dairaId = Session::get('daira_id');
        }
        if (!$communeId && $dairaId) {
            $com = $db->prepare("SELECT id FROM communes WHERE daira_id = ? LIMIT 1");
            $com->execute([$dairaId]);
            $communeId = $com->fetchColumn();
        }

        if (!$dairaId || !$communeId) {
            echo json_encode(['success' => false, 'message' => 'Localisation requise. Activez le GPS.']);
            return;
        }

        $orgId = \App\Helpers\AssignmentEngine::resolve($categoryId, $subcategoryId, $dairaId);

        $title = trim($_POST['title'] ?? '') ?: 'Signalement #' . $trackingCode;
        $description = trim($_POST['description'] ?? '') ?: '';

        $stmt = $db->prepare("INSERT INTO reports (tracking_code, title, description, category_id, subcategory_id, priority, status, daira_id, commune_id, address, latitude, longitude, citizen_name, citizen_phone, citizen_email, citizen_id, organization_id, workflow_step) VALUES (?, ?, ?, ?, ?, 'medium', 'submitted', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->execute([
            $trackingCode, $title, $description, $categoryId, $subcategoryId,
            $dairaId, $communeId,
            'Signalé depuis l\'application mobile',
            $lat, $lng,
            Session::getUserName(), null, Session::get('user_email'),
            Session::getUserId(), $orgId
        ]);

        $reportId = (int)$db->lastInsertId();

        // Deadline
        $catQ = $db->prepare("SELECT deadline_days FROM categories WHERE id = ?");
        $catQ->execute([$categoryId]);
        $catDays = (int)($catQ->fetchColumn() ?: 7);
        $deadlineAt = date('Y-m-d H:i:s', strtotime("now +{$catDays} days"));
        $db->prepare("UPDATE reports SET deadline_at = ? WHERE id = ?")->execute([$deadlineAt, $reportId]);

        // Photos
        if (!empty($_FILES['photos']['name'][0])) {
            $uploadDir = __DIR__ . '/../../public/uploads/reports/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 10 * 1024 * 1024;
            $isFirst = true;
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            foreach ($_FILES['photos']['name'] as $i => $name) {
                if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $mime = $finfo->file($_FILES['photos']['tmp_name'][$i]);
                if (!in_array($mime, $allowed)) continue;
                if ($_FILES['photos']['size'][$i] > $maxSize) continue;
                $safeExt = match($mime) {
                    'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp', default => 'bin',
                };
                $filename = $trackingCode . '_' . ($i + 1) . '.' . $safeExt;
                if (move_uploaded_file($_FILES['photos']['tmp_name'][$i], $uploadDir . $filename)) {
                    $db->prepare("INSERT INTO report_images (report_id, filename, original_name, mime_type, file_size, is_primary, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())")
                        ->execute([$reportId, $filename, $name, $mime, $_FILES['photos']['size'][$i], $isFirst ? 1 : 0]);
                    $isFirst = false;
                }
            }
        }

        $this->audit('create', 'Report', $reportId, null, ['tracking_code' => $trackingCode, 'organization_id' => $orgId]);

        // Auto-assign
        $handler = null;
        if ($orgId) {
            $handler = \App\Helpers\AssignmentEngine::autoAssignToCentral($reportId, $orgId);
        }

        // Award badges
        \App\Helpers\Badge::checkAndAward(Session::getUserId());

        // Award gamification points
        \App\Helpers\Gamification::addPoints(Session::getUserId(), 'report_created', $reportId, 'report');
        $countStmt = $db->prepare("SELECT COUNT(*) FROM reports WHERE citizen_id = ? AND deleted_at IS NULL");
        $countStmt->execute([Session::getUserId()]);
        $userReportCount = (int) $countStmt->fetchColumn();
        if ($userReportCount <= 1) {
            \App\Helpers\Gamification::addPoints(Session::getUserId(), 'first_report', $reportId, 'report');
        }

        echo json_encode([
            'success' => true,
            'report_id' => $reportId,
            'tracking_code' => $trackingCode,
            'message' => "Signalement {$trackingCode} créé avec succès"
        ]);
    }

    public function feed(): void {
        $this->auth();
        if (!Rbac::isRole('citizen')) { $this->redirect('/dashboard'); return; }
        $this->view('citizen/feed', []);
    }

    public function leaderboard(): void {
        $this->auth();
        if (!Rbac::isRole('citizen')) { $this->redirect('/dashboard'); return; }
        $this->view('citizen/leaderboard', []);
    }

    public function map(): void {
        $this->auth();
        if (!Rbac::isRole('citizen')) { $this->redirect('/dashboard'); return; }
        $this->view('citizen/map', []);
    }

    public function beforeAfter(): void {
        $this->auth();
        if (!Rbac::isRole('citizen')) { $this->redirect('/dashboard'); return; }

        $db = Database::getConnection();
        $userId = Session::getUserId();

        $stmt = $db->prepare("
            SELECT r.id, r.title, r.tracking_code, r.status, r.resolved_at,
                   (SELECT ri.filename FROM report_images ri WHERE ri.report_id = r.id ORDER BY ri.created_at ASC LIMIT 1) as before_photo,
                   (SELECT ip.filename FROM intervention_photos ip WHERE ip.report_id = r.id AND ip.photo_type = 'after' ORDER BY ip.created_at DESC LIMIT 1) as after_photo,
                   c.name as category_name, c.color as category_color, com.name as commune_name
            FROM reports r
            JOIN categories c ON r.category_id = c.id
            JOIN communes com ON r.commune_id = com.id
            WHERE r.deleted_at IS NULL AND r.citizen_id = ? AND r.status IN ('resolved','validated','closed')
            HAVING before_photo IS NOT NULL AND after_photo IS NOT NULL
            ORDER BY r.resolved_at DESC
            LIMIT 20
        ");
        $stmt->execute([$userId]);
        $pairs = $stmt->fetchAll();

        $this->view('citizen/before-after', compact('pairs'));
    }

    public function profile(): void {
        $this->auth();
        if (!Rbac::isRole('citizen')) { $this->redirect('/dashboard'); return; }
        $user = $this->getUser();
        $primaryRole = Rbac::getPrimaryRole();
        $this->view('citizen/profile', compact('user', 'primaryRole'));
    }

    public function editProfile(): void {
        $this->auth();
        if (!Rbac::isRole('citizen')) { $this->redirect('/dashboard'); return; }
        $db = Database::getConnection();
        $userId = Session::getUserId();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user) { $this->redirect('/home'); return; }
        $this->view('citizen/profile-edit', compact('user'));
    }

    public function updateProfile(): void {
        $this->auth();
        if (!Rbac::isRole('citizen')) { $this->redirect('/dashboard'); return; }
        $userId = Session::getUserId();
        $db = Database::getConnection();

        $csrfToken = $_POST['_token'] ?? '';
        if (!\App\Helpers\Csrf::verify($csrfToken)) {
            $this->withError('Token de sécurité invalide.');
            $this->redirect('/my-profile/edit');
            return;
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($firstName === '' || $lastName === '') {
            $this->withError('Le prénom et le nom sont obligatoires.');
            $this->redirect('/my-profile/edit');
            return;
        }

        $sql = "UPDATE users SET first_name = ?, last_name = ?, phone = ?";
        $params = [$firstName, $lastName, $phone ?: null];

        if (!empty($_POST['password'])) {
            $password = $_POST['password'];
            if (strlen($password) < 6) {
                $this->withError('Le mot de passe doit contenir au moins 6 caractères.');
                $this->redirect('/my-profile/edit');
                return;
            }
            $sql .= ", password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (!empty($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['avatar']['tmp_name'];
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmpPath);
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024;
            if ($_FILES['avatar']['size'] > $maxSize) {
                $this->withError('La photo ne doit pas dépasser 5 Mo.');
                $this->redirect('/my-profile/edit');
                return;
            }
            if (in_array($mime, $allowed, true)) {
                $ext = match($mime) { 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp', default => 'jpg' };
                $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                $destDir = __DIR__ . '/../../public/uploads/avatars';
                if (!is_dir($destDir)) { mkdir($destDir, 0777, true); }
                $dest = $destDir . '/' . $filename;
                if (move_uploaded_file($tmpPath, $dest)) {
                    chmod($dest, 0644);
                    $stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $oldAvatar = $stmt->fetchColumn();
                    if (!empty($oldAvatar) && $oldAvatar !== 'default.png' && file_exists(__DIR__ . '/../../public' . $oldAvatar)) {
                        unlink(__DIR__ . '/../../public' . $oldAvatar);
                    }
                    $sql .= ", avatar = ?";
                    $params[] = '/uploads/avatars/' . $filename;
                }
            }
        }

        $sql .= " WHERE id = ?";
        $params[] = $userId;
        $db->prepare($sql)->execute($params);

        Session::set('user_name', trim($firstName . ' ' . $lastName));

        $this->withSuccess('Profil mis à jour avec succès.');
        $this->redirect('/my-profile');
    }

    private function count($db, string $where, array $params): int {
        $stmt = $db->prepare("SELECT COUNT(*) FROM reports r WHERE r.deleted_at IS NULL AND {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
