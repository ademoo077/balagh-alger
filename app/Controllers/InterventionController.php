<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Helper;
use App\Helpers\Csrf;
use App\Helpers\AuditLog;
use App\Helpers\Notification;
use App\Helpers\AssignmentEngine;
use App\Helpers\Rbac;
use App\Controllers\Controller;

class InterventionController extends Controller {

    public function index(): void {
        $this->auth();
        $db = Database::getConnection();

        $scope = Rbac::scopeInterventions();
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

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $countStmt = $db->prepare("SELECT COUNT(*) FROM reports r WHERE {$where}");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();
        $totalPages = ceil($total / $perPage);

        $sql = "SELECT r.*, c.name as category_name, d.name as daira_name, com.name as commune_name,
                o.name as org_name,
                u.first_name as assigned_first_name, u.last_name as assigned_last_name
                FROM reports r
                JOIN categories c ON r.category_id = c.id
                JOIN dairas d ON r.daira_id = d.id
                JOIN communes com ON r.commune_id = com.id
                LEFT JOIN organizations o ON r.organization_id = o.id
                LEFT JOIN users u ON r.assigned_to = u.id
                WHERE {$where}
                ORDER BY 
                    CASE r.priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END,
                    r.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $reports = $stmt->fetchAll();

        $this->view('interventions/index', compact('reports', 'total', 'totalPages', 'page'));
    }

    public function show(int $id): void {
        $this->auth();
        $report = $this->getReportOrRedirect($id, '/interventions');
        $db = Database::getConnection();

        $intervention = $db->prepare("SELECT ri.*, u.first_name as agent_first_name, u.last_name as agent_last_name
            FROM report_interventions ri
            JOIN users u ON ri.agent_id = u.id
            WHERE ri.report_id = ?
            ORDER BY ri.created_at DESC LIMIT 1");
        $intervention->execute([$id]);
        $intervention = $intervention->fetch();

        $photos = $db->prepare("SELECT ip.*, u.first_name, u.last_name
            FROM intervention_photos ip
            JOIN users u ON ip.uploaded_by = u.id
            WHERE ip.report_id = ?
            ORDER BY ip.photo_type, ip.created_at");
        $photos->execute([$id]);
        $photos = $photos->fetchAll();

        $citizenPhotos = $db->prepare("SELECT * FROM report_images WHERE report_id = ? ORDER BY is_primary DESC, created_at ASC");
        $citizenPhotos->execute([$id]);
        $citizenPhotos = $citizenPhotos->fetchAll();

        $comments = $db->prepare("SELECT cm.*, u.first_name, u.last_name
            FROM report_comments cm
            JOIN users u ON cm.user_id = u.id
            WHERE cm.report_id = ? AND cm.deleted_at IS NULL
            ORDER BY cm.created_at ASC");
        $comments->execute([$id]);
        $comments = $comments->fetchAll();

        $history = $db->prepare("SELECT h.*, u.first_name, u.last_name
            FROM report_history h
            LEFT JOIN users u ON h.user_id = u.id
            WHERE h.report_id = ?
            ORDER BY h.created_at DESC");
        $history->execute([$id]);
        $history = $history->fetchAll();

        // Get assignable users based on RBAC
        $agents = Rbac::getAssignableUsers($report['organization_id'], $report['daira_id']);

        // Permission flags per RBAC
        $canAssign = Rbac::canAssignReport($report);
        $canIntervene = Rbac::canIntervene($report);
        $canValidateSection = Rbac::canValidateSection($report);
        $canValidateUnite = Rbac::canValidateUnite($report);
        $canClose = Rbac::canCloseReport($report);
        $canRedirect = Rbac::canRedirectReport($report);
        $canComment = Rbac::has('reports.comment');

        $csrfToken = Csrf::generate();
        $this->view('interventions/show', compact(
            'report', 'intervention', 'photos', 'citizenPhotos', 'comments', 'history', 'agents',
            'canAssign', 'canIntervene', 'canValidateSection', 'canValidateUnite',
            'canClose', 'canRedirect', 'canComment', 'csrfToken'
        ));
    }

    public function assign(int $id): void {
        $this->auth();
        $this->checkCsrf("/interventions/{$id}");

        $db = Database::getConnection();
        $report = $db->prepare("SELECT * FROM reports WHERE id = ? AND deleted_at IS NULL");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report) {
            $this->withError('Signalement non trouvé.');
            $this->redirect('/interventions');
        }

        if (!Rbac::canAssignReport($report)) {
            $this->withError('Vous n\'avez pas la permission d\'assigner ce signalement.');
            $this->redirect("/interventions/{$id}");
        }

        $userId = Session::getUserId();
        $assignedTo = (int)($_POST['assigned_to'] ?? 0);

        if (!$assignedTo) {
            $this->withError('Veuillez sélectionner un agent.');
            $this->redirect("/interventions/{$id}");
        }

        // Get the assignee's role
        $assigneeStmt = $db->prepare("SELECT r.name FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = ? ORDER BY r.level DESC LIMIT 1");
        $assigneeStmt->execute([$assignedTo]);
        $assigneeRole = $assigneeStmt->fetchColumn();

        // Route to correct assignment method based on caller's role
        $primaryRole = Rbac::getPrimaryRole();

        if ($primaryRole === 'chef_section') {
            // Chef section can only assign to agents
            AssignmentEngine::assignToAgent($id, $userId, $assignedTo);
        } elseif ($primaryRole === 'chef_unite') {
            // Chef unite can assign to chef_section or agent
            if ($assigneeRole === 'intervenant') {
                AssignmentEngine::assignToAgent($id, $userId, $assignedTo);
            } else {
                AssignmentEngine::assignToChefSection($id, $userId, $assignedTo);
            }
        } else {
            // resp_central, admin_local, admin_central can assign to anyone
            if ($assigneeRole === 'chef_unite') {
                AssignmentEngine::assignToChefUnite($id, $userId, $assignedTo);
            } elseif ($assigneeRole === 'chef_section') {
                AssignmentEngine::assignToChefSection($id, $userId, $assignedTo);
            } else {
                AssignmentEngine::assignToAgent($id, $userId, $assignedTo);
            }
        }

        $this->withSuccess('Signalement assigné avec succès.');
        $this->redirect("/interventions/{$id}");
    }

    public function startIntervention(int $id): void {
        $this->auth();
        $this->checkCsrf("/interventions/{$id}");

        $db = Database::getConnection();
        $userId = Session::getUserId();

        $report = $db->prepare("SELECT * FROM reports WHERE id = ?");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report || !Rbac::canIntervene($report)) {
            $this->withError('Accès non autorisé.');
            $this->redirect("/interventions/{$id}");
        }

        $stmt = $db->prepare("INSERT INTO report_interventions (report_id, agent_id, status, description, latitude, longitude, started_at)
            VALUES (?, ?, 'in_progress', ?, ?, ?, NOW())");
        $stmt->execute([
            $id,
            $userId,
            $_POST['description'] ?? null,
            $_POST['latitude'] ?? null,
            $_POST['longitude'] ?? null,
        ]);

        $db->prepare("UPDATE reports SET status = 'in_progress' WHERE id = ?")->execute([$id]);

        $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value, latitude, longitude)
            VALUES (?, ?, 'intervention_started', 'Intervention démarrée sur place', ?, ?)")
            ->execute([$id, $userId, $_POST['latitude'] ?? null, $_POST['longitude'] ?? null]);

        if ($report['citizen_id']) {
            $title = __('notifications.intervention_started_title');
            $msg = str_replace(':code', $report['tracking_code'], __('notifications.intervention_started_msg'));
            Notification::create($report['citizen_id'], 'status_update', $title, $msg, ['report_id' => $id]);
        }

        $this->withSuccess('Intervention démarrée. GPS enregistré.');
        $this->redirect("/interventions/{$id}");
    }

    public function uploadPhoto(int $id): void {
        $this->auth();
        $this->checkCsrf("/interventions/{$id}");

        $db = Database::getConnection();
        $userId = Session::getUserId();

        $report = $db->prepare("SELECT * FROM reports WHERE id = ?");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report || !Rbac::canIntervene($report)) {
            $this->withError('Accès non autorisé.');
            $this->redirect("/interventions/{$id}");
        }

        $photoType = $_POST['photo_type'] ?? 'before';

        $intStmt = $db->prepare("SELECT id FROM report_interventions WHERE report_id = ? ORDER BY created_at DESC LIMIT 1");
        $intStmt->execute([$id]);
        $intervention = $intStmt->fetch();

        if (!$intervention) {
            $db->prepare("INSERT INTO report_interventions (report_id, agent_id, status, started_at) VALUES (?, ?, 'in_progress', NOW())")
                ->execute([$id, $userId]);
            $interventionId = $db->lastInsertId();
        } else {
            $interventionId = $intervention['id'];
        }

        if (!empty($_FILES['photo']['tmp_name'])) {
            $uploadDir = PUBLIC_PATH . '/uploads/interventions/' . $id . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['photo']['tmp_name']);
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mime, $allowed)) {
                $this->withError('Type de fichier non autorisé.');
                $this->redirect("/interventions/{$id}");
                return;
            }
            if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
                $this->withError('Fichier trop volumineux (max 5 Mo).');
                $this->redirect("/interventions/{$id}");
                return;
            }

            $safeExt = match($mime) {
                'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp', default => 'bin',
            };
            $filename = $photoType . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $safeExt;
            $filepath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $filepath)) {
                $db->prepare("INSERT INTO intervention_photos (intervention_id, report_id, filename, original_name, mime_type, file_size, photo_type, latitude, longitude, caption, uploaded_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
                    ->execute([
                        $interventionId, $id, $filename, $_FILES['photo']['name'],
                        $mime, $_FILES['photo']['size'], $photoType,
                        $_POST['latitude'] ?? null, $_POST['longitude'] ?? null,
                        $_POST['caption'] ?? null, $userId,
                    ]);

                $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value)
                    VALUES (?, ?, 'photo_upload', ?)")
                    ->execute([$id, $userId, "Photo {$photoType} ajoutée: " . $_FILES['photo']['name']]);

                $this->withSuccess('Photo ajoutée avec succès.');
            } else {
                $this->withError('Erreur lors de l\'upload.');
            }
        } else {
            $this->withError('Aucun fichier sélectionné.');
        }

        $this->redirect("/interventions/{$id}");
    }

    public function completeIntervention(int $id): void {
        $this->auth();
        $this->checkCsrf("/interventions/{$id}");

        $db = Database::getConnection();
        $userId = Session::getUserId();

        $report = $db->prepare("SELECT * FROM reports WHERE id = ?");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report || !Rbac::canIntervene($report)) {
            $this->withError('Accès non autorisé.');
            $this->redirect("/interventions/{$id}");
        }

        if (!AssignmentEngine::hasAfterPhotos($id)) {
            $this->withError('Vous devez ajouter au moins une photo "Après intervention" avant de clôturer.');
            $this->redirect("/interventions/{$id}");
        }

        $db->prepare("UPDATE report_interventions SET status = 'completed', completed_at = NOW(), description = CONCAT(COALESCE(description, ''), ' ', ?)
            WHERE report_id = ? AND agent_id = ? ORDER BY created_at DESC LIMIT 1")
            ->execute([$_POST['completion_note'] ?? '', $id, $userId]);

        $db->prepare("UPDATE reports SET status = 'pending_review', resolved_at = NOW(), resolution_note = ?, workflow_step = 5 WHERE id = ?")
            ->execute([$_POST['completion_note'] ?? null, $id]);

        $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value)
            VALUES (?, ?, 'intervention_completed', 'Intervention terminée - en attente de validation du chef de section')")
            ->execute([$id, $userId]);

        // Find the chef_section who assigned this report to the intervenant
        $histStmt = $db->prepare("SELECT user_id FROM report_history WHERE report_id = ? AND action = 'assign_agent' ORDER BY created_at DESC LIMIT 1");
        $histStmt->execute([$id]);
        $chefSecId = $histStmt->fetchColumn();

        if (!$chefSecId) {
            // Fallback: find chef_section by org+daira
            $chefSec = AssignmentEngine::getChefSection($report['organization_id'], $report['daira_id'], null);
            $chefSecId = $chefSec['id'] ?? null;
        }

        if ($chefSecId) {
            $title = __('notifications.work_to_validate_title');
            $msg = str_replace(':code', $report['tracking_code'], __('notifications.work_to_validate_msg'));
            Notification::create($chefSecId, 'validation', $title, $msg, ['report_id' => $id]);
        }
        if ($report['citizen_id']) {
            $title = __('notifications.report_in_validation_title');
            $msg = str_replace(':code', $report['tracking_code'], __('notifications.report_in_validation_msg'));
            Notification::create($report['citizen_id'], 'status_update', $title, $msg, ['report_id' => $id]);
        }

        $this->withSuccess('Intervention terminée. En attente de validation du chef de section.');
        $this->redirect("/interventions/{$id}");
    }

    public function validateWork(int $id): void {
        $this->auth();
        $this->checkCsrf("/interventions/{$id}");

        $db = Database::getConnection();
        $userId = Session::getUserId();
        $action = $_POST['validation_action'] ?? 'validate';

        $report = $db->prepare("SELECT * FROM reports WHERE id = ?");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report) {
            $this->withError('Signalement non trouvé.');
            $this->redirect('/interventions');
        }

        $primaryRole = Rbac::getPrimaryRole();

        if ($action === 'reject') {
            $reason = trim($_POST['rejection_reason'] ?? '');
            if (empty($reason)) {
                $this->withError('Veuillez indiquer le motif du rejet.');
                $this->redirect("/interventions/{$id}");
            }

            // Both chef_section and chef_unite can reject
            if (!Rbac::canValidateSection($report) && !Rbac::canValidateUnite($report)) {
                $this->withError('Vous n\'avez pas la permission de rejeter ce signalement.');
                $this->redirect("/interventions/{$id}");
            }

            $db->prepare("UPDATE report_interventions SET status = 'rejected', rejection_reason = ?, validated_by = ?, validated_at = NOW()
                WHERE report_id = ? ORDER BY created_at DESC LIMIT 1")
                ->execute([$reason, $userId, $id]);

            $db->prepare("UPDATE reports SET status = 'in_progress', workflow_step = 4 WHERE id = ?")->execute([$id]);

            $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value, note)
                VALUES (?, ?, 'intervention_rejected', 'Travaux rejetés - retour à l''agent', ?)")
                ->execute([$id, $userId, $reason]);

            if ($report['assigned_to']) {
                $title = __('notifications.work_rejected_title');
                $msg = str_replace(':reason', $reason, str_replace(':code', $report['tracking_code'], __('notifications.work_rejected_msg')));
                Notification::create($report['assigned_to'], 'rejection', $title, $msg, ['report_id' => $id]);
            }

            $this->withSuccess('Travaux rejetés. L\'agent a été notifié.');
        } else {
            // Validate
            if (in_array($primaryRole, ['chef_section', 'chef_unite', 'admin_central', 'resp_central', 'admin_local'])) {
                if ($primaryRole === 'chef_section') {
                    // Chef Section validates -> sends to Chef Unite (workflow_step = 6)
                    if (!Rbac::canValidateSection($report)) {
                        $this->withError('Vous ne pouvez pas valider ce signalement à ce stade.');
                        $this->redirect("/interventions/{$id}");
                    }

                    $db->prepare("UPDATE report_interventions SET status = 'validated_by_section', validated_by = ?, validated_at = NOW()
                        WHERE report_id = ? ORDER BY created_at DESC LIMIT 1")
                        ->execute([$userId, $id]);

                    $db->prepare("UPDATE reports SET status = 'pending_unite', workflow_step = 6 WHERE id = ?")->execute([$id]);

                    $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value)
                        VALUES (?, ?, 'section_validated', 'Validé par le chef de section - en attente de validation du chef d''unité')")
                        ->execute([$id, $userId]);

                    // Find the chef_unite who assigned this report to the chef_section
                    $histStmt = $db->prepare("SELECT user_id FROM report_history WHERE report_id = ? AND action = 'assign_chef_section' ORDER BY created_at DESC LIMIT 1");
                    $histStmt->execute([$id]);
                    $chefUniteId = $histStmt->fetchColumn();

                    if (!$chefUniteId) {
                        // Fallback: find chef_unite by org+daira
                        $chefUnite = AssignmentEngine::getChefUnite($report['organization_id'], $report['daira_id']);
                        $chefUniteId = $chefUnite['id'] ?? null;
                    }

                    if ($chefUniteId) {
                        $title = __('notifications.validation_required_title');
                        $msg = str_replace(':code', $report['tracking_code'], __('notifications.validation_required_msg'));
                        Notification::create($chefUniteId, 'validation', $title, $msg, ['report_id' => $id]);
                    }

                    $this->withSuccess('Validé. Transmis au chef d\'unité pour validation finale.');
                } else {
                    // Chef Unite or Admin validates -> report validated (workflow_step = 7)
                    if (!Rbac::canValidateUnite($report)) {
                        $this->withError('Vous ne pouvez pas valider ce signalement à ce stade.');
                        $this->redirect("/interventions/{$id}");
                    }

                    $db->prepare("UPDATE report_interventions SET status = 'validated', validated_by = ?, validated_at = NOW()
                        WHERE report_id = ? ORDER BY created_at DESC LIMIT 1")
                        ->execute([$userId, $id]);

                    $db->prepare("UPDATE reports SET status = 'validated', workflow_step = 7 WHERE id = ?")->execute([$id]);

                    $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value)
                        VALUES (?, ?, 'unite_validated', 'Validé par le chef d''unité - dossier approuvé')")
                        ->execute([$id, $userId]);

                    if ($report['citizen_id']) {
                        $title = __('notifications.report_validated_title');
                        $msg = str_replace(':code', $report['tracking_code'], __('notifications.report_validated_msg'));
                        Notification::create($report['citizen_id'], 'status_update', $title, $msg, ['report_id' => $id]);
                    }

                    $this->withSuccess('Validation finale confirmée. Le dossier est approuvé.');
                }
            } else {
                $this->withError('Vous n\'avez pas la permission de valider.');
                $this->redirect("/interventions/{$id}");
            }
        }

        $this->redirect("/interventions/{$id}");
    }

    public function close(int $id): void {
        $this->auth();
        $this->checkCsrf("/interventions/{$id}");

        $db = Database::getConnection();
        $userId = Session::getUserId();

        $report = $db->prepare("SELECT * FROM reports WHERE id = ?");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report) {
            $this->withError('Signalement non trouvé.');
            $this->redirect('/interventions');
        }

        if (!Rbac::canCloseReport($report)) {
            $this->withError('Vous n\'avez pas la permission de clôturer ce signalement.');
            $this->redirect("/interventions/{$id}");
        }

        $db->prepare("UPDATE reports SET status = 'closed', workflow_step = 8, resolution_note = CONCAT(COALESCE(resolution_note, ''), ' | Clôturé: ', ?) WHERE id = ?")
            ->execute([$_POST['close_note'] ?? 'Clôturé par l\'administration', $id]);

        $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value)
            VALUES (?, ?, 'closed', 'Dossier clôturé définitivement')")
            ->execute([$id, $userId]);

        $this->audit('close', 'Report', $id);

        if ($report['citizen_id']) {
            $title = __('notifications.report_closed_final_title');
            $msg = str_replace(':code', $report['tracking_code'], __('notifications.report_closed_final_msg'));
            Notification::create($report['citizen_id'], 'status_update', $title, $msg, ['report_id' => $id]);
        }

        $this->withSuccess('Dossier clôturé définitivement.');
        $this->redirect("/interventions/{$id}");
    }
}
