<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Helper;
use App\Helpers\Csrf;
use App\Helpers\AuditLog;
use App\Helpers\Notification;
use App\Helpers\Rbac;
use App\Helpers\I18n;
use App\Controllers\Controller;

class ReportController extends Controller {

    private function applyCitizenLayout(): void {
        if (Rbac::isRole('citizen')) {
            $this->layout = 'layouts/citizen';
        }
    }

    public function index(): void {
        $this->auth();
        $this->applyCitizenLayout();
        $db = Database::getConnection();

        $scope = Rbac::scopeReports();
        $where = "r.deleted_at IS NULL" . $scope['where'];
        $params = $scope['params'];

        if (!empty($_GET['status'])) {
            $where .= " AND r.status = ?";
            $params[] = $_GET['status'];
        }
        if (!empty($_GET['priority'])) {
            $where .= " AND r.priority = ?";
            $params[] = $_GET['priority'];
        }
        if (!empty($_GET['category_id'])) {
            $where .= " AND r.category_id = ?";
            $params[] = $_GET['category_id'];
        }
        if (!empty($_GET['subcategory_id'])) {
            $where .= " AND r.subcategory_id = ?";
            $params[] = $_GET['subcategory_id'];
        }
        if (!empty($_GET['daira_id'])) {
            $where .= " AND r.daira_id = ?";
            $params[] = $_GET['daira_id'];
        }
        if (!empty($_GET['organization_id'])) {
            $where .= " AND r.organization_id = ?";
            $params[] = $_GET['organization_id'];
        }
        if (!empty($_GET['from'])) {
            $where .= " AND r.created_at >= ?";
            $params[] = $_GET['from'] . ' 00:00:00';
        }
        if (!empty($_GET['to'])) {
            $where .= " AND r.created_at <= ?";
            $params[] = $_GET['to'] . ' 23:59:59';
        }
        if (!empty($_GET['search'])) {
            $where .= " AND (r.title LIKE ? OR r.tracking_code LIKE ? OR r.description LIKE ?)";
            $search = '%' . $_GET['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        if (!empty($_GET['commune_id'])) {
            $where .= " AND r.commune_id = ?";
            $params[] = (int)$_GET['commune_id'];
        }
        if (!empty($_GET['period'])) {
            $periodMap = ['today' => 1, 'week' => 7, 'month' => 30];
            $days = $periodMap[$_GET['period']] ?? 0;
            if ($days > 0) {
                $where .= " AND r.created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)";
            }
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $countStmt = $db->prepare("SELECT COUNT(*) FROM reports r WHERE {$where}");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();
        $totalPages = ceil($total / $perPage);

        $sql = "SELECT r.*, c.name as category_name, c.deadline_days, d.name as daira_name, com.name as commune_name, o.name as org_name,
                u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM reports r
                JOIN categories c ON r.category_id = c.id
                JOIN dairas d ON r.daira_id = d.id
                JOIN communes com ON r.commune_id = com.id
                LEFT JOIN organizations o ON r.organization_id = o.id
                LEFT JOIN users u ON r.assigned_to = u.id
                WHERE {$where}
                ORDER BY r.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $reports = $stmt->fetchAll();

        $categories = $db->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name")->fetchAll();
        $dairas = $db->query("SELECT id, name FROM dairas WHERE is_active = 1 ORDER BY name")->fetchAll();
        $communes = $db->query("SELECT id, name FROM communes WHERE is_active = 1 ORDER BY name")->fetchAll();

        $isAdmin = Rbac::minLevel(6);
        $isCitizen = Rbac::isRole('citizen');
        $canExport = Rbac::has('reports.export');
        $canCreate = Rbac::has('reports.create');

        $this->view('reports/index', compact('reports', 'categories', 'dairas', 'communes', 'total', 'totalPages', 'page', 'isAdmin', 'isCitizen', 'canExport', 'canCreate'));
    }

    public function create(): void {
        $this->auth();
        $this->applyCitizenLayout();
        $db = Database::getConnection();
        
        // Citizens can create reports; staff too
        if (!Rbac::has('reports.create')) {
            $this->withError('Vous n\'avez pas la permission de créer des signalements.');
            $this->redirect('/reports');
        }

        $categories = $db->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
        $dairas = $db->query("SELECT id, name FROM dairas WHERE is_active = 1 ORDER BY name")->fetchAll();
        $csrfToken = Csrf::generate();
        $this->view('reports/create', compact('categories', 'dairas', 'csrfToken'));
    }

    public function store(): void {
        $this->auth();
        $this->checkCsrf('/reports/create');

        if (!Rbac::has('reports.create')) {
            $this->withError('Vous n\'avez pas la permission de créer des signalements.');
            $this->redirect('/reports');
        }

        $errors = $this->validate([
            'title' => ['required', 'label' => 'Titre', 'max' => 255],
            'description' => ['required', 'label' => 'Description'],
            'category_id' => ['required', 'label' => 'Catégorie'],
            'daira_id' => ['required', 'label' => 'Daïra'],
            'commune_id' => ['required', 'label' => 'Commune'],
            'address' => ['required', 'label' => 'Adresse'],
        ]);

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $_POST);
            $this->redirect('/reports/create');
        }

        $db = Database::getConnection();
        $trackingCode = Helper::generateTrackingCode();

        $categoryId = (int) $_POST['category_id'];
        $subcategoryId = !empty($_POST['subcategory_id']) ? (int) $_POST['subcategory_id'] : null;
        $dairaId = (int) $_POST['daira_id'];
        $orgId = \App\Helpers\AssignmentEngine::resolve($categoryId, $subcategoryId, $dairaId);

        $stmt = $db->prepare("INSERT INTO reports (tracking_code, title, description, category_id, subcategory_id, priority, status, daira_id, commune_id, address, latitude, longitude, citizen_name, citizen_phone, citizen_email, citizen_id, organization_id, workflow_step) VALUES (?, ?, ?, ?, ?, ?, 'submitted', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->execute([
            $trackingCode,
            $_POST['title'],
            $_POST['description'],
            $categoryId,
            $subcategoryId,
            $_POST['priority'] ?? 'medium',
            $dairaId,
            $_POST['commune_id'],
            $_POST['address'],
            $_POST['latitude'] ?? null,
            $_POST['longitude'] ?? null,
            $_POST['citizen_name'] ?? Session::getUserName(),
            $_POST['citizen_phone'] ?? null,
            $_POST['citizen_email'] ?? Session::get('user_email'),
            Session::getUserId(),
            $orgId,
        ]);

        $reportId = (int) $db->lastInsertId();

        $catQ = $db->prepare("SELECT deadline_days FROM categories WHERE id = ?");
        $catQ->execute([$categoryId]);
        $catDays = (int) ($catQ->fetchColumn() ?: 7);
        $deadlineAt = date('Y-m-d H:i:s', strtotime("now +{$catDays} days"));
        $db->prepare("UPDATE reports SET deadline_at = ? WHERE id = ?")->execute([$deadlineAt, $reportId]);

        if (!empty($_FILES['photos']['name'][0])) {
            $uploadDir = __DIR__ . '/../../public/uploads/reports/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $allowed = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'video/mp4', 'video/quicktime', 'video/webm', 'video/3gpp',
            ];
            $maxSize = 50 * 1024 * 1024;
            $isFirst = true;
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            foreach ($_FILES['photos']['name'] as $i => $name) {
                if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $mime = $finfo->file($_FILES['photos']['tmp_name'][$i]);
                if (!in_array($mime, $allowed)) continue;
                if ($_FILES['photos']['size'][$i] > $maxSize) continue;
                $safeExt = match($mime) {
                    'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif',
                    'image/webp' => 'webp', 'video/mp4' => 'mp4', 'video/quicktime' => 'mov',
                    'video/webm' => 'webm', 'video/3gpp' => '3gp', default => 'bin',
                };
                $filename = $trackingCode . '_' . ($i + 1) . '.' . $safeExt;
                $dest = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['photos']['tmp_name'][$i], $dest)) {
                    $size = $_FILES['photos']['size'][$i];
                    $db->prepare("INSERT INTO report_images (report_id, filename, original_name, mime_type, file_size, is_primary, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())")
                        ->execute([$reportId, $filename, $name, $mime, $size, $isFirst ? 1 : 0]);
                    $isFirst = false;
                }
            }
        }

        $this->audit('create', 'Report', $reportId, null, [
            'tracking_code' => $trackingCode,
            'organization_id' => $orgId,
        ]);

        $orgName = '';
        $handler = null;
        if ($orgId) {
            $orgStmt = $db->prepare("SELECT name FROM organizations WHERE id = ?");
            $orgStmt->execute([$orgId]);
            $orgName = $orgStmt->fetchColumn();
            $handler = \App\Helpers\AssignmentEngine::autoAssignToCentral($reportId, $orgId);
        }

        $msg = "Signalement {$trackingCode} créé avec succès.";
        if ($orgId && isset($handler)) {
            $msg .= " Affecté automatiquement à {$orgName}.";
        } elseif ($orgId) {
            $msg .= " Organisme : {$orgName}.";
        } else {
            $msg .= " En attente d'affectation manuelle.";
        }

        $this->withSuccess($msg);

        // Award badges for citizen
        $citizenId = Session::getUserId();
        if ($citizenId) {
            \App\Helpers\Badge::checkAndAward($citizenId);
        }

        $this->redirect('/reports/' . $reportId);
    }

    public function show(int|string $id): void {
        $this->auth();
        $this->applyCitizenLayout();
        $db = Database::getConnection();

        if (is_string($id)) {
            $stmt = $db->prepare("SELECT id FROM reports WHERE tracking_code = ? AND deleted_at IS NULL LIMIT 1");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if (!$row) { $this->redirect('/reports'); return; }
            $id = (int)$row['id'];
        }

        $report = $this->getReportOrRedirect($id);
        $db->prepare("UPDATE reports SET view_count = view_count + 1 WHERE id = ?")->execute([$id]);

        $images = $db->prepare("SELECT * FROM report_images WHERE report_id = ? ORDER BY is_primary DESC");
        $images->execute([$id]);
        $images = $images->fetchAll();

        $comments = $db->prepare("SELECT cm.*, u.first_name, u.last_name, u.avatar FROM report_comments cm JOIN users u ON cm.user_id = u.id WHERE cm.report_id = ? AND cm.deleted_at IS NULL ORDER BY cm.created_at ASC");
        $comments->execute([$id]);
        $comments = $comments->fetchAll();

        $history = $db->prepare("SELECT h.*, u.first_name, u.last_name FROM report_history h LEFT JOIN users u ON h.user_id = u.id WHERE h.report_id = ? ORDER BY h.created_at DESC");
        $history->execute([$id]);
        $history = $history->fetchAll();

        $users = [];
        if (Rbac::canAssignReport($report)) {
            $users = Rbac::getAssignableUsers($report['organization_id'], $report['daira_id']);
        }

        $csrfToken = Csrf::generate();
        $canAssign = Rbac::canAssignReport($report);
        $canEdit = Rbac::minLevel(5) || ($report['citizen_id'] == Session::getUserId());
        $canDelete = Rbac::has('reports.delete');
        $canComment = Rbac::has('reports.comment');
        $canChangeStatus = Rbac::has('reports.update');
        $canRedirect = Rbac::canRedirectReport($report);
        $primaryRole = Rbac::getPrimaryRole();

        $rating = null;
        $stmtRating = $db->prepare("SELECT * FROM report_ratings WHERE report_id = ?");
        $stmtRating->execute([$id]);
        $rating = $stmtRating->fetch();

        $canRate = ($report['citizen_id'] == Session::getUserId()) 
            && in_array($report['status'], ['resolved', 'closed']) 
            && !$rating;

        $isOwner = ($report['citizen_id'] && $report['citizen_id'] == Session::getUserId());

        $intPhotosStmt = $db->prepare("SELECT ip.*, ri.status as intervention_status FROM intervention_photos ip LEFT JOIN report_interventions ri ON ip.intervention_id = ri.id WHERE ip.report_id = ? ORDER BY ip.photo_type ASC, ip.created_at ASC");
        $intPhotosStmt->execute([$id]);
        $interventionPhotos = $intPhotosStmt->fetchAll();

        $organizations = [];
        if (Rbac::canRedirectReport($report)) {
            $organizations = $db->query("SELECT id, name FROM organizations WHERE is_active = 1 ORDER BY name")->fetchAll();
        }

        $this->view('reports/show', compact('report', 'images', 'comments', 'history', 'users', 'csrfToken', 'canAssign', 'canEdit', 'canDelete', 'canComment', 'canChangeStatus', 'canRedirect', 'primaryRole', 'rating', 'canRate', 'interventionPhotos', 'organizations', 'isOwner'));
    }

    public function edit(int $id): void {
        $this->auth();
        $db = Database::getConnection();

        $report = $db->prepare("SELECT * FROM reports WHERE id = ? AND deleted_at IS NULL");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report) {
            $this->withError('Signalement non trouvé.');
            $this->redirect('/reports');
        }

        if (!Rbac::canViewReport($report)) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/reports');
        }

        // Only admin+ or the citizen who created it can edit
        if (!Rbac::minLevel(5) && $report['citizen_id'] != Session::getUserId()) {
            $this->withError('Vous ne pouvez modifier que vos propres signalements.');
            $this->redirect('/reports');
        }

        $categories = $db->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
        $dairas = $db->query("SELECT id, name FROM dairas WHERE is_active = 1 ORDER BY name")->fetchAll();
        $csrfToken = Csrf::generate();

        $this->view('reports/edit', compact('report', 'categories', 'dairas', 'csrfToken'));
    }

    public function update(int $id): void {
        $this->auth();
        $this->checkCsrf("/reports/{$id}/edit");

        $db = Database::getConnection();
        $report = $db->prepare("SELECT * FROM reports WHERE id = ? AND deleted_at IS NULL");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report) {
            $this->withError('Signalement non trouvé.');
            $this->redirect('/reports');
        }

        if (!Rbac::canViewReport($report)) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/reports');
        }

        if (!Rbac::minLevel(5) && $report['citizen_id'] != Session::getUserId()) {
            $this->withError('Vous ne pouvez modifier que vos propres signalements.');
            $this->redirect('/reports');
        }

        $oldReport = json_encode($report);

        $stmt = $db->prepare("UPDATE reports SET title = ?, description = ?, category_id = ?, subcategory_id = ?, priority = ?, daira_id = ?, commune_id = ?, address = ?, latitude = ?, longitude = ? WHERE id = ?");
        $stmt->execute([
            $_POST['title'], $_POST['description'], $_POST['category_id'],
            $_POST['subcategory_id'] ?? null, $_POST['priority'],
            $_POST['daira_id'], $_POST['commune_id'], $_POST['address'],
            $_POST['latitude'] ?? null, $_POST['longitude'] ?? null, $id
        ]);

        $db->prepare("INSERT INTO report_history (report_id, user_id, action, old_value, new_value) VALUES (?, ?, 'update', ?, ?)")
            ->execute([$id, Session::getUserId(), $oldReport, json_encode($_POST)]);

        $this->audit('update', 'Report', $id);
        $this->withSuccess('Signalement mis à jour.');
        $this->redirect("/reports/{$id}");
    }

    public function assign(int $id): void {
        $this->auth();
        $this->checkCsrf("/reports/{$id}");

        $db = Database::getConnection();
        $report = $db->prepare("SELECT * FROM reports WHERE id = ? AND deleted_at IS NULL");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report) {
            $this->withError('Signalement non trouvé.');
            $this->redirect('/reports');
        }

        if (!Rbac::canAssignReport($report)) {
            $this->withError('Vous n\'avez pas la permission d\'assigner ce signalement.');
            $this->redirect("/reports/{$id}");
        }

        $userId = Session::getUserId();
        $assignedTo = (int)($_POST['assigned_to'] ?? 0);

        if (!$assignedTo) {
            $this->withError('Veuillez sélectionner un utilisateur.');
            $this->redirect("/reports/{$id}");
        }

        $assigneeStmt = $db->prepare("SELECT r.name FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = ? ORDER BY r.level DESC LIMIT 1");
        $assigneeStmt->execute([$assignedTo]);
        $assigneeRole = $assigneeStmt->fetchColumn();

        $primaryRole = Rbac::getPrimaryRole();

        if ($primaryRole === 'chef_section') {
            \App\Helpers\AssignmentEngine::assignToAgent($id, $userId, $assignedTo);
        } elseif ($primaryRole === 'chef_unite') {
            if ($assigneeRole === 'intervenant') {
                \App\Helpers\AssignmentEngine::assignToAgent($id, $userId, $assignedTo);
            } else {
                \App\Helpers\AssignmentEngine::assignToChefSection($id, $userId, $assignedTo);
            }
        } else {
            if ($assigneeRole === 'chef_unite') {
                \App\Helpers\AssignmentEngine::assignToChefUnite($id, $userId, $assignedTo);
            } elseif ($assigneeRole === 'chef_section') {
                \App\Helpers\AssignmentEngine::assignToChefSection($id, $userId, $assignedTo);
            } else {
                \App\Helpers\AssignmentEngine::assignToAgent($id, $userId, $assignedTo);
            }
        }

        $this->audit('assign', 'Report', $id);
        $this->withSuccess('Signalement assigné avec succès.');
        $this->redirect("/reports/{$id}");
    }

    public function changeStatus(int $id): void {
        $this->auth();
        $this->checkCsrf("/reports/{$id}");

        $db = Database::getConnection();
        $report = $db->prepare("SELECT * FROM reports WHERE id = ? AND deleted_at IS NULL");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report) {
            $this->withError('Signalement non trouvé.');
            $this->redirect('/reports');
        }

        if (!Rbac::canViewReport($report)) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/reports');
        }

        $newStatus = $_POST['status'] ?? '';
        $note = $_POST['resolution_note'] ?? null;
        $primaryRole = Rbac::getPrimaryRole();
        $userId = Session::getUserId();
        $isOwner = ($report['citizen_id'] && $report['citizen_id'] == $userId);

        $citizenAllowed = ($isOwner && $primaryRole === 'citizen' && $newStatus === 'closed' && $report['status'] === 'resolved');

        if (!$citizenAllowed && !Rbac::has('reports.update')) {
            $this->withError('Vous n\'avez pas la permission de modifier le statut.');
            $this->redirect("/reports/{$id}");
        }

        $allowedStatuses = [
            'admin_central' => ['submitted', 'acknowledged', 'assigned', 'in_progress', 'pending_review', 'pending_unite', 'validated', 'resolved', 'closed', 'rejected'],
            'resp_central' => ['acknowledged', 'assigned', 'in_progress', 'resolved', 'closed'],
            'admin_local' => ['acknowledged', 'assigned', 'in_progress', 'resolved', 'closed'],
            'chef_unite' => ['assigned', 'in_progress', 'validated'],
            'chef_section' => ['assigned', 'in_progress', 'pending_unite'],
            'intervenant' => ['in_progress'],
            'citizen' => ['closed'],
        ];

        $allowed = $allowedStatuses[$primaryRole] ?? [];
        if (!in_array($newStatus, $allowed)) {
            $this->withError('Vous ne pouvez pas définir ce statut depuis votre rôle.');
            $this->redirect("/reports/{$id}");
        }

        $updates = ['status = ?', 'workflow_step = ?'];
        $params = [$newStatus];

        $stepMap = [
            'submitted' => 0,
            'acknowledged' => 1,
            'assigned' => max(2, $report['workflow_step']),
            'in_progress' => 4,
            'pending_review' => 5,
            'pending_unite' => 6,
            'validated' => 7,
            'resolved' => 7,
            'closed' => 7,
            'rejected' => 4,
        ];
        $params[] = $stepMap[$newStatus] ?? $report['workflow_step'];

        if ($newStatus === 'resolved') {
            $updates[] = 'resolved_at = NOW()';
            $updates[] = 'resolution_note = ?';
            $params[] = $note;
        } elseif ($newStatus === 'closed') {
            $updates[] = "resolution_note = CONCAT(COALESCE(resolution_note, ''), ?)";
            $params[] = ' | ' . ($note ?? 'Clôturé');
        } elseif ($note) {
            $updates[] = 'resolution_note = ?';
            $params[] = $note;
        }

        $params[] = $id;
        $db->prepare("UPDATE reports SET " . implode(', ', $updates) . " WHERE id = ?")->execute($params);

        $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value, note)
            VALUES (?, ?, 'status_change', ?, ?)")
            ->execute([$id, Session::getUserId(), $newStatus, $note]);

        if ($newStatus === 'resolved' && $report['citizen_id'] && !$isOwner) {
            $title = __('notifications.report_resolved_title');
            $msg = str_replace(':code', $report['tracking_code'], __('notifications.report_resolved_msg'));
            \App\Helpers\Notification::create($report['citizen_id'], 'status_update', $title, $msg, ['report_id' => $id]);
            \App\Controllers\Api\PushController::sendPush($report['citizen_id'], $title, $msg, '/reports/' . $id);
            \App\Helpers\Badge::checkAndAward($report['citizen_id']);
            \App\Helpers\Gamification::addPoints($report['citizen_id'], 'report_resolved', $id, 'report');
        } elseif ($newStatus === 'resolved' && $report['citizen_id'] && $isOwner) {
            \App\Helpers\Badge::checkAndAward($report['citizen_id']);
            \App\Helpers\Gamification::addPoints($report['citizen_id'], 'report_resolved', $id, 'report');
        } elseif ($newStatus === 'closed' && $report['citizen_id'] && !$isOwner) {
            $title = __('notifications.report_closed_title');
            $msg = str_replace(':code', $report['tracking_code'], __('notifications.report_closed_msg'));
            \App\Helpers\Notification::create($report['citizen_id'], 'status_update', $title, $msg, ['report_id' => $id]);
            \App\Controllers\Api\PushController::sendPush($report['citizen_id'], $title, $msg, '/reports/' . $id);
        } elseif ($report['citizen_id'] && !$isOwner) {
            $title = 'Mise à jour de votre signalement';
            $msg = str_replace(':code', $report['tracking_code'], "Le statut de votre signalement :code a été mis à jour.");
            \App\Controllers\Api\PushController::sendPush($report['citizen_id'], $title, $msg, '/reports/' . $id);
        }

        $this->audit('status_change', 'Report', $id, null, ['status' => $newStatus]);
        $this->withSuccess('Statut mis à jour.');
        $this->redirect("/reports/{$id}");
    }

    public function comment(int $id): void {
        $this->auth();
        $this->checkCsrf("/reports/{$id}");

        $db = Database::getConnection();

        // Verify user can view this report
        $report = $db->prepare("SELECT * FROM reports WHERE id = ?");
        $report->execute([$id]);
        $report = $report->fetch();
        if (!$report || !Rbac::canViewReport($report)) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/reports');
        }

        if (!Rbac::has('reports.comment')) {
            $this->withError('Vous n\'avez pas la permission de commenter.');
            $this->redirect("/reports/{$id}");
        }

        $comment = trim($_POST['comment'] ?? '');
        if (empty($comment)) {
            $this->withError('Commentaire vide.');
            $this->redirect("/reports/{$id}");
        }

        $db->prepare("INSERT INTO report_comments (report_id, user_id, comment) VALUES (?, ?, ?)")
            ->execute([$id, Session::getUserId(), $comment]);

        $this->withSuccess('Commentaire ajouté.');
        $this->redirect("/reports/{$id}");
    }

    public function export(): void {
        $this->auth();
        $this->requirePermission('reports.export');

        $db = Database::getConnection();
        $scope = Rbac::scopeReports();
        $where = "r.deleted_at IS NULL" . $scope['where'];
        $params = $scope['params'];

        $format = $_GET['format'] ?? 'csv';

        $sql = "SELECT r.tracking_code, r.title, r.description, r.priority, r.status, r.address, r.created_at, 
                c.name as category, d.name as daira, com.name as commune, o.name as organization
                FROM reports r 
                JOIN categories c ON r.category_id = c.id 
                JOIN dairas d ON r.daira_id = d.id 
                JOIN communes com ON r.commune_id = com.id 
                LEFT JOIN organizations o ON r.organization_id = o.id 
                WHERE {$where} ORDER BY r.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $reports = $stmt->fetchAll();

        if ($format === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=signalements_' . date('Y-m-d') . '.csv');
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['Code', 'Titre', 'Description', 'Priorité', 'Statut', 'Adresse', 'Date', 'Catégorie', 'Daïra', 'Commune', 'Organisme'], ';');
            foreach ($reports as $r) {
                fputcsv($output, [$r['tracking_code'], $r['title'], $r['description'], $r['priority'], $r['status'], $r['address'], $r['created_at'], $r['category'], $r['daira'], $r['commune'], $r['organization']], ';');
            }
            fclose($output);
            exit;
        }

        $this->redirect('/reports');
    }

    public function exportMonthly(): void {
        I18n::init();
        $this->auth();
        $this->requirePermission('reports.export');

        $db = Database::getConnection();

        $month = $_GET['month'] ?? date('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $stmt = $db->prepare("SELECT o.id, o.name,
            COUNT(r.id) as total,
            SUM(CASE WHEN r.status IN ('resolved','validated','closed') THEN 1 ELSE 0 END) as resolved,
            SUM(CASE WHEN r.status IN ('submitted','acknowledged','assigned','in_progress') THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN r.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN r.status = 'rejected' THEN 1 ELSE 0 END) as rejected,
            AVG(CASE WHEN r.status IN ('resolved','validated','closed') THEN DATEDIFF(r.updated_at, r.created_at) END) as avg_resolution_days
            FROM organizations o
            LEFT JOIN reports r ON r.organization_id = o.id AND r.created_at BETWEEN ? AND ? AND r.deleted_at IS NULL
            WHERE o.is_active = 1
            GROUP BY o.id, o.name
            HAVING total > 0
            ORDER BY total DESC");
        $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $orgStats = $stmt->fetchAll();

        $totalReports = array_sum(array_column($orgStats, 'total'));
        $totalResolved = array_sum(array_column($orgStats, 'resolved'));
        $totalPending = array_sum(array_column($orgStats, 'pending'));
        $resolvedPct = $totalReports > 0 ? round(($totalResolved / $totalReports) * 100, 1) : 0;
        $avgDays = $totalReports > 0 && $totalResolved > 0
            ? round(array_sum(array_map(fn($s) => ($s['avg_resolution_days'] ?? 0) * $s['resolved'], $orgStats)) / max(1, $totalResolved), 1)
            : 0;

        $ratingStmt = $db->prepare("SELECT AVG(rr.rating) as avg_rating, COUNT(rr.id) as total_ratings FROM report_ratings rr JOIN reports r ON rr.report_id = r.id WHERE r.created_at BETWEEN ? AND ? AND r.deleted_at IS NULL");
        $ratingStmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $ratings = $ratingStmt->fetch();

        $pdf = \App\Helpers\PdfHelper::generateMonthlyReport($orgStats, $month, $totalReports, $totalResolved, $totalPending, $resolvedPct, $avgDays, $ratings);

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="rapport-mensuel-' . $month . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    public function print(int $id): void {
        $this->auth();
        $report = $this->getReportOrRedirect($id);
        $this->view('reports/print', compact('report'));
    }

    public function downloadPdf(int $id): void {
        $this->auth();
        $report = $this->getReportOrRedirect($id);
        
        $pdf = \App\Helpers\PdfHelper::generateReportPdf($report);
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="balagh-' . $report['tracking_code'] . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    public function delete(int $id): void {
        $this->auth();
        $this->checkCsrf("/reports/{$id}");

        if (!Rbac::isRole('admin_central')) {
            $this->withError('Seul l\'admin central peut supprimer des signalements.');
            $this->redirect("/reports/{$id}");
        }

        $db = Database::getConnection();
        $report = $db->prepare("SELECT * FROM reports WHERE id = ? AND deleted_at IS NULL");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report) {
            $this->withError('Signalement non trouvé.');
            $this->redirect('/reports');
        }

        $db->prepare("UPDATE reports SET deleted_at = NOW() WHERE id = ?")->execute([$id]);

        $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value) VALUES (?, ?, 'deleted', 'Signalement supprimé')")
            ->execute([$id, Session::getUserId()]);

        $this->audit('delete', 'Report', $id, ['tracking_code' => $report['tracking_code']], null);

        if ($report['citizen_id']) {
            Notification::create($report['citizen_id'], 'report_deleted', 'Signalement supprimé',
                "Votre signalement {$report['tracking_code']} a été supprimé par l'administration.",
                ['report_id' => $id]);
        }

        $this->withSuccess('Signalement supprimé avec succès.');
        $this->redirect('/reports');
    }

    public function rate(int $id): void {
        $this->auth();
        $this->checkCsrf("/reports/{$id}");
        
        $db = Database::getConnection();
        $userId = Session::getUserId();
        
        $report = $db->prepare("SELECT * FROM reports WHERE id = ? AND citizen_id = ? AND status IN ('resolved','closed') AND deleted_at IS NULL");
        $report->execute([$id, $userId]);
        $report = $report->fetch();
        
        if (!$report) {
            $this->withError('Vous ne pouvez évaluer que vos propres signalements résolus.');
            $this->redirect('/reports/' . $id);
            return;
        }
        
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 3)));
        $comment = trim($_POST['comment'] ?? '');
        
        $stmt = $db->prepare("INSERT INTO report_ratings (report_id, citizen_id, rating, comment) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)");
        $stmt->execute([$id, $userId, $rating, $comment]);
        
        $this->withSuccess('Merci pour votre évaluation !');
        $this->redirect('/reports/' . $id);
    }

    public function redirectReport(int $id): void {
        $this->auth();
        $this->checkCsrf("/reports/{$id}");

        $db = Database::getConnection();
        $report = $db->prepare("SELECT * FROM reports WHERE id = ? AND deleted_at IS NULL");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report) {
            $this->withError('Signalement non trouvé.');
            $this->redirect('/reports');
        }

        if (!Rbac::canRedirectReport($report)) {
            $this->withError('Vous n\'avez pas la permission de rediriger ce signalement.');
            $this->redirect("/reports/{$id}");
        }

        $newOrgId = (int)($_POST['organization_id'] ?? 0);
        if (!$newOrgId) {
            $this->withError('Veuillez sélectionner un organisme.');
            $this->redirect("/reports/{$id}");
        }

        $orgStmt = $db->prepare("SELECT name FROM organizations WHERE id = ?");
        $orgStmt->execute([$newOrgId]);
        $newOrgName = $orgStmt->fetchColumn();

        if (!$newOrgName) {
            $this->withError('Organisme introuvable.');
            $this->redirect("/reports/{$id}");
        }

        $oldOrgId = $report['organization_id'];
        $userId = Session::getUserId();

        $db->prepare("UPDATE reports SET organization_id = ?, workflow_step = 1, status = 'acknowledged', assigned_to = NULL, assigned_by = NULL, assigned_at = NULL WHERE id = ?")
            ->execute([$newOrgId, $id]);

        $db->prepare("INSERT INTO report_history (report_id, user_id, action, old_value, new_value)
            VALUES (?, ?, 'redirect', ?, ?)")
            ->execute([$id, $userId, "Organisme #{$oldOrgId}", "Redirigé vers {$newOrgName} (#{$newOrgId})"]);

        $handler = \App\Helpers\AssignmentEngine::autoAssignToCentral($id, $newOrgId);

        $msg = "Signalement redirigé vers {$newOrgName}.";
        if ($handler) {
            $msg .= " Assigné à {$handler['first_name']} {$handler['last_name']}.";
        }

        $this->audit('redirect', 'Report', $id, ['organization_id' => $oldOrgId], ['organization_id' => $newOrgId]);
        $this->withSuccess($msg);
        $this->redirect("/reports/{$id}");
    }
}
