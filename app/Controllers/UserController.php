<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Csrf;
use App\Helpers\Helper;
use App\Helpers\AuditLog;
use App\Helpers\Rbac;
use App\Controllers\Controller;

class UserController extends Controller {

    public function index(): void {
        $this->auth();
        $this->requirePermission('users.view');
        $db = Database::getConnection();

        $scope = Rbac::scopeUsers();
        $where = "u.deleted_at IS NULL" . $scope['where'];
        $params = $scope['params'];

        if (!empty($_GET['search'])) {
            $where .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $s = '%' . $_GET['search'] . '%';
            $params[] = $s; $params[] = $s; $params[] = $s;
        }
        if (!empty($_GET['role'])) {
            $where .= " AND EXISTS (SELECT 1 FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = u.id AND r.name = ?)";
            $params[] = $_GET['role'];
        }
        if (!empty($_GET['status'])) {
            $where .= " AND u.status = ?";
            $params[] = $_GET['status'];
        }

        $stmt = $db->prepare("SELECT u.*, GROUP_CONCAT(r.label) as role_labels, GROUP_CONCAT(r.name) as role_names, 
            o.name as org_name, d.name as daira_name
            FROM users u 
            LEFT JOIN user_roles ur ON u.id = ur.user_id 
            LEFT JOIN roles r ON ur.role_id = r.id 
            LEFT JOIN organizations o ON u.organization_id = o.id
            LEFT JOIN dairas d ON u.daira_id = d.id
            WHERE {$where} GROUP BY u.id ORDER BY u.created_at DESC");
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        $roles = $db->query("SELECT name, label FROM roles ORDER BY level")->fetchAll();
        $organizations = $db->query("SELECT id, name FROM organizations WHERE is_active = 1 ORDER BY name")->fetchAll();
        $canCreate = Rbac::has('users.create');
        $canSuspend = Rbac::has('users.suspend');
        $canDelete = Rbac::minLevel(7);
        $csrfToken = Csrf::generate();

        $this->view('users/index', compact('users', 'roles', 'organizations', 'canCreate', 'canSuspend', 'canDelete', 'csrfToken'));
    }

    public function create(): void {
        $this->auth();
        $this->requirePermission('users.create');
        $db = Database::getConnection();

        $primaryRole = Rbac::getPrimaryRole();
        $myLevel = Rbac::HIERARCHY[$primaryRole] ?? 0;

        // Can only create roles BELOW your level
        $roles = $db->prepare("SELECT id, name, label, level FROM roles WHERE level < ? AND is_active = 1 ORDER BY level");
        $roles->execute([$myLevel]);
        $roles = $roles->fetchAll();

        $organizations = $db->query("SELECT id, name FROM organizations WHERE is_active = 1 ORDER BY name")->fetchAll();
        $dairas = $db->query("SELECT id, name FROM dairas WHERE is_active = 1 ORDER BY name")->fetchAll();

        // Scope organizations based on role
        if (in_array($primaryRole, ['resp_central', 'admin_local', 'chef_unite', 'chef_section'])) {
            $orgId = Session::get('organization_id');
            $orgStmt = $db->prepare("SELECT id, name FROM organizations WHERE id = ?");
            $orgStmt->execute([$orgId]);
            $organizations = $orgStmt->fetchAll();
        }

        // Scope dairas based on role
        if (in_array($primaryRole, ['chef_unite', 'chef_section'])) {
            $dairaId = Session::get('daira_id');
            $dairaStmt = $db->prepare("SELECT id, name FROM dairas WHERE id = ?");
            $dairaStmt->execute([$dairaId]);
            $dairas = $dairaStmt->fetchAll();
        }

        $csrfToken = Csrf::generate();
        $this->view('users/create', compact('roles', 'organizations', 'dairas', 'csrfToken', 'primaryRole'));
    }

    public function store(): void {
        $this->auth();
        $this->requirePermission('users.create');
        $this->checkCsrf('/users/create');

        $errors = $this->validate([
            'first_name' => ['required', 'label' => 'Prénom', 'max' => 100],
            'last_name' => ['required', 'label' => 'Nom', 'max' => 100],
            'email' => ['required', 'email', 'label' => 'Email'],
            'password' => ['required', 'label' => 'Mot de passe', 'min' => 6],
            'role_id' => ['required', 'label' => 'Rôle'],
        ]);

        if ($errors) {
            Session::flash('errors', $errors);
            $this->redirect('/users/create');
        }

        // Verify the target role is at a lower level than the creator
        $targetRoleId = (int) $_POST['role_id'];
        $db = Database::getConnection();
        $targetRole = $db->prepare("SELECT name, level FROM roles WHERE id = ?");
        $targetRole->execute([$targetRoleId]);
        $targetRole = $targetRole->fetch();

        if (!$targetRole) {
            $this->withError('Rôle invalide.');
            $this->redirect('/users/create');
        }

        if (!Rbac::canCreateRole($targetRole['name'])) {
            $this->withError('Vous ne pouvez pas créer un compte avec ce rôle.');
            $this->redirect('/users/create');
        }

        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->fetch()) {
            $this->withError('Cet email est déjà utilisé.');
            $this->redirect('/users/create');
        }

        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff), random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Enforce org/daira scoping
        $orgId = $_POST['organization_id'] ?? null;
        $dairaId = $_POST['daira_id'] ?? null;
        $communeId = $_POST['commune_id'] ?? null;

        $primaryRole = Rbac::getPrimaryRole();
        if (in_array($primaryRole, ['resp_central', 'admin_local', 'chef_unite', 'chef_section'])) {
            $orgId = Session::get('organization_id'); // Force to own org
        }
        if (in_array($primaryRole, ['chef_unite', 'chef_section'])) {
            $dairaId = Session::get('daira_id'); // Force to own daira
        }

        $stmt = $db->prepare("INSERT INTO users (uuid, first_name, last_name, email, phone, password, status, organization_id, daira_id, commune_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$uuid, $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'] ?? null, $hash, $_POST['status'] ?? 'active', $orgId, $dairaId, $communeId]);

        $userId = $db->lastInsertId();

        $stmt = $db->prepare("INSERT INTO user_roles (user_id, role_id, assigned_by) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $targetRoleId, Session::getUserId()]);

        $this->audit('create', 'User', $userId);
        $this->withSuccess('Utilisateur créé avec succès.');
        $this->redirect('/users');
    }

    public function show(int $id): void {
        $this->auth();
        $this->requirePermission('users.view');
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT u.*, GROUP_CONCAT(r.name) as roles, GROUP_CONCAT(r.label) as role_labels, 
            o.name as org_name, d.name as daira_name 
            FROM users u 
            LEFT JOIN user_roles ur ON u.id = ur.user_id 
            LEFT JOIN roles r ON ur.role_id = r.id 
            LEFT JOIN organizations o ON u.organization_id = o.id 
            LEFT JOIN dairas d ON u.daira_id = d.id 
            WHERE u.id = ? AND u.deleted_at IS NULL GROUP BY u.id");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            $this->withError('Utilisateur non trouvé.');
            $this->redirect('/users');
        }

        $scope = Rbac::scopeUsers();
        if ($scope['where']) {
            $check = $db->prepare("SELECT COUNT(*) FROM users u WHERE u.id = ? AND u.deleted_at IS NULL" . $scope['where']);
            $check->execute(array_merge([$id], $scope['params']));
            if ($check->fetchColumn() == 0) {
                $this->withError('Accès non autorisé.');
                $this->redirect('/users');
            }
        }

        $userRoles = explode(',', $user['roles'] ?? '');
        $primaryRole = $userRoles[0] ?? 'citizen';

        $stmt2 = $db->prepare("SELECT COUNT(*) FROM reports WHERE assigned_to = ? AND deleted_at IS NULL");
        $stmt2->execute([$id]);
        $assignedCount = (int) $stmt2->fetchColumn();

        $stats = ['assigned' => $assignedCount];

        if (in_array($primaryRole, ['admin_central', 'resp_central', 'admin_local'])) {
            $orgId = $user['organization_id'];
            $dairaId = $user['daira_id'];
            if ($primaryRole === 'admin_central') {
                $q = $db->prepare("SELECT COUNT(*) FROM reports WHERE deleted_at IS NULL");
                $q->execute([]);
            } elseif ($orgId) {
                $q = $db->prepare("SELECT COUNT(*) FROM reports WHERE organization_id = ? AND deleted_at IS NULL");
                $q->execute([$orgId]);
            } else {
                $q = $db->prepare("SELECT COUNT(*) FROM reports WHERE 1=0");
                $q->execute([]);
            }
            $stats['orgTotal'] = (int) $q->fetchColumn();

            if ($primaryRole === 'admin_central') {
                $q2 = $db->prepare("SELECT status, COUNT(*) as cnt FROM reports WHERE deleted_at IS NULL GROUP BY status ORDER BY cnt DESC");
                $q2->execute([]);
            } elseif ($orgId) {
                $q2 = $db->prepare("SELECT status, COUNT(*) as cnt FROM reports WHERE organization_id = ? AND deleted_at IS NULL GROUP BY status ORDER BY cnt DESC");
                $q2->execute([$orgId]);
            } else {
                $q2 = $db->prepare("SELECT 'none' as status, 0 as cnt");
                $q2->execute([]);
            }
            $stats['orgByStatus'] = $q2->fetchAll();

            $qAssignedBy = $db->prepare("SELECT COUNT(*) FROM reports WHERE assigned_by = ? AND deleted_at IS NULL");
            $qAssignedBy->execute([$id]);
            $stats['assigned'] = (int) $qAssignedBy->fetchColumn();

        } elseif ($primaryRole === 'chef_unite') {
            $dairaId = $user['daira_id'];
            if ($dairaId) {
                $q = $db->prepare("SELECT COUNT(*) FROM reports WHERE daira_id = ? AND deleted_at IS NULL");
                $q->execute([$dairaId]);
                $stats['dairaTotal'] = (int) $q->fetchColumn();

                $q2 = $db->prepare("SELECT c.name as commune_name, COUNT(r.id) as report_count,
                    SUM(CASE WHEN r.status IN ('validated','resolved','closed') THEN 1 ELSE 0 END) as resolved_count,
                    SUM(CASE WHEN r.status IN ('submitted','acknowledged','assigned') THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN r.status IN ('in_progress','pending_review','pending_unite') THEN 1 ELSE 0 END) as active_count
                    FROM reports r
                    LEFT JOIN communes c ON r.commune_id = c.id
                    WHERE r.daira_id = ? AND r.deleted_at IS NULL
                    GROUP BY r.commune_id ORDER BY report_count DESC");
                $q2->execute([$dairaId]);
                $stats['communeStats'] = $q2->fetchAll();
            }

            $q3 = $db->prepare("SELECT status, COUNT(*) as cnt FROM reports WHERE daira_id = ? AND deleted_at IS NULL GROUP BY status");
            $q3->execute([$dairaId]);
            $stats['dairaByStatus'] = $q3->fetchAll();

        } elseif ($primaryRole === 'chef_section') {
            $dairaId = $user['daira_id'];
            if ($dairaId) {
                $q = $db->prepare("SELECT COUNT(*) FROM reports WHERE daira_id = ? AND deleted_at IS NULL");
                $q->execute([$dairaId]);
                $stats['sectionTotal'] = (int) $q->fetchColumn();
            }

            $q2 = $db->prepare("SELECT status, COUNT(*) as cnt FROM reports WHERE daira_id = ? AND deleted_at IS NULL GROUP BY status");
            $q2->execute([$dairaId]);
            $stats['sectionByStatus'] = $q2->fetchAll();

        } elseif ($primaryRole === 'intervenant') {
            $stmt3 = $db->prepare("SELECT COUNT(*) FROM report_interventions WHERE agent_id = ?");
            $stmt3->execute([$id]);
            $stats['interventions'] = (int) $stmt3->fetchColumn();

            $stmt4 = $db->prepare("SELECT ri.status, COUNT(*) as cnt FROM report_interventions ri WHERE ri.agent_id = ? GROUP BY ri.status");
            $stmt4->execute([$id]);
            $stats['interventionByStatus'] = $stmt4->fetchAll();
        }

        $canEdit = Rbac::canManageUser($id);
        $canSuspend = Rbac::has('users.suspend');

        $this->view('users/show', compact('user', 'stats', 'primaryRole', 'canEdit', 'canSuspend'));
    }

    public function edit(int $id): void {
        $this->auth();
        $this->requirePermission('users.update');
        $db = Database::getConnection();

        $user = $db->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
        $user->execute([$id]);
        $user = $user->fetch();

        if (!$user) {
            $this->withError('Utilisateur non trouvé.');
            $this->redirect('/users');
        }

        if (!Rbac::canManageUser($id)) {
            $this->withError('Vous ne pouvez pas modifier cet utilisateur.');
            $this->redirect('/users');
        }

        $primaryRole = Rbac::getPrimaryRole();
        $myLevel = Rbac::HIERARCHY[$primaryRole] ?? 0;

        $roles = $db->prepare("SELECT id, name, label, level FROM roles WHERE level < ? AND is_active = 1 ORDER BY level");
        $roles->execute([$myLevel]);
        $roles = $roles->fetchAll();

        $organizations = $db->query("SELECT id, name FROM organizations WHERE is_active = 1 ORDER BY name")->fetchAll();
        $dairas = $db->query("SELECT id, name FROM dairas WHERE is_active = 1 ORDER BY name")->fetchAll();

        if (in_array($primaryRole, ['resp_central', 'admin_local', 'chef_unite', 'chef_section'])) {
            $orgId = Session::get('organization_id');
            $orgStmt = $db->prepare("SELECT id, name FROM organizations WHERE id = ?");
            $orgStmt->execute([$orgId]);
            $organizations = $orgStmt->fetchAll();
        }

        if (in_array($primaryRole, ['chef_unite', 'chef_section'])) {
            $dairaId = Session::get('daira_id');
            $dairaStmt = $db->prepare("SELECT id, name FROM dairas WHERE id = ?");
            $dairaStmt->execute([$dairaId]);
            $dairas = $dairaStmt->fetchAll();
        }

        $roleQ = $db->prepare("SELECT role_id FROM user_roles WHERE user_id = ? LIMIT 1");
        $roleQ->execute([$id]);
        $user['role_id'] = $roleQ->fetchColumn() ?: '';

        $csrfToken = Csrf::generate();
        $canEdit = Rbac::canManageUser($id);
        $this->view('users/edit', compact('user', 'roles', 'organizations', 'dairas', 'csrfToken', 'primaryRole', 'canEdit'));
    }

    public function update(int $id): void {
        $this->auth();
        $this->requirePermission('users.update');
        $this->checkCsrf("/users/{$id}/edit");

        $db = Database::getConnection();

        $user = $db->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
        $user->execute([$id]);
        $user = $user->fetch();

        if (!$user || !Rbac::canManageUser($id)) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/users');
        }

        // Enforce org/daira scoping
        $orgId = $_POST['organization_id'] ?? null;
        $dairaId = $_POST['daira_id'] ?? null;
        $communeId = $_POST['commune_id'] ?? null;

        $primaryRole = Rbac::getPrimaryRole();
        if (in_array($primaryRole, ['resp_central', 'admin_local', 'chef_unite', 'chef_section'])) {
            $orgId = Session::get('organization_id');
        }
        if (in_array($primaryRole, ['chef_unite', 'chef_section'])) {
            $dairaId = Session::get('daira_id');
        }

        $params = [$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'] ?? null, $_POST['status'], $orgId, $dairaId, $communeId, $id];

        $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, status = ?, organization_id = ?, daira_id = ?, commune_id = ?";

        if (!empty($_POST['password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = ?";
        $db->prepare($sql)->execute($params);

        if (!empty($_POST['role_id'])) {
            $targetRoleId = (int) $_POST['role_id'];
            $targetRole = $db->prepare("SELECT name, level FROM roles WHERE id = ?");
            $targetRole->execute([$targetRoleId]);
            $targetRole = $targetRole->fetch();

            if ($targetRole && Rbac::canCreateRole($targetRole['name'])) {
                $db->prepare("DELETE FROM user_roles WHERE user_id = ?")->execute([$id]);
                $db->prepare("INSERT INTO user_roles (user_id, role_id, assigned_by) VALUES (?, ?, ?)")
                    ->execute([$id, $targetRoleId, Session::getUserId()]);
            }
        }

        $this->audit('update', 'User', $id);
        $this->withSuccess('Utilisateur mis à jour.');
        $this->redirect("/users/{$id}");
    }

    public function profile(): void {
        $this->auth();
        $db = Database::getConnection();
        $userId = Session::getUserId();

        $user = $db->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
        $user->execute([$userId]);
        $user = $user->fetch();

        if (!$user) {
            $this->withError('Utilisateur non trouvé.');
            $this->redirect('/dashboard');
        }

        $csrfToken = Csrf::generate();
        $this->view('users/profile', compact('user', 'csrfToken'));
    }

    public function updateProfile(): void {
        $this->auth();
        $this->checkCsrf('/profile');

        $userId = Session::getUserId();
        $db = Database::getConnection();

        $user = $db->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
        $user->execute([$userId]);
        $user = $user->fetch();

        if (!$user) {
            $this->withError('Utilisateur non trouvé.');
            $this->redirect('/dashboard');
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($firstName === '' || $lastName === '') {
            $this->withError('Le prénom et le nom sont obligatoires.');
            $this->redirect('/profile');
        }

        $sql = "UPDATE users SET first_name = ?, last_name = ?, phone = ?";
        $params = [$firstName, $lastName, $phone ?: null];

        if (!empty($_POST['password'])) {
            $password = $_POST['password'];
            if (strlen($password) < 6) {
                $this->withError('Le mot de passe doit contenir au moins 6 caractères.');
                $this->redirect('/profile');
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
                $this->redirect('/profile');
            }
            if (in_array($mime, $allowed, true)) {
                $ext = match($mime) { 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp', default => 'jpg' };
                $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                $destDir = __DIR__ . '/../../public/uploads/avatars';
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0777, true);
                    chmod($destDir, 0777);
                }
                $dest = $destDir . '/' . $filename;
                if (move_uploaded_file($tmpPath, $dest)) {
                    chmod($dest, 0644);
                    $oldAvatar = $user['avatar'] ?? '';
                    if (!empty($oldAvatar) && $oldAvatar !== 'default.png' && file_exists(__DIR__ . '/../../public' . $oldAvatar)) {
                        unlink(__DIR__ . '/../../public' . $oldAvatar);
                    }
                    $sql .= ", avatar = ?";
                    $params[] = '/uploads/avatars/' . $filename;
                } else {
                    $this->withError('Erreur lors de l\'upload de la photo.');
                    $this->redirect('/profile');
                }
            } else {
                $this->withError('Format de fichier non supporté. Utilisez JPEG, PNG, GIF ou WebP.');
                $this->redirect('/profile');
            }
        }

        $sql .= " WHERE id = ?";
        $params[] = $userId;
        $db->prepare($sql)->execute($params);

        Session::set('user_name', trim($firstName . ' ' . $lastName));

        $newAvatar = $db->prepare("SELECT avatar FROM users WHERE id = ?");
        $newAvatar->execute([$userId]);
        $avatarPath = $newAvatar->fetchColumn();
        if ($avatarPath && $avatarPath !== 'default.png') {
            Session::set('user_avatar', $avatarPath);
        }

        $this->audit('update_profile', 'User', $userId);
        $this->withSuccess('Profil mis à jour avec succès.');
        $this->redirect('/profile');
    }

    public function delete(int $id): void {
        $this->auth();
        $this->requirePermission('users.update');
        $this->checkCsrf("/users/{$id}");

        $db = Database::getConnection();

        if (!Rbac::canManageUser($id)) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/users');
        }

        $db->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?")->execute([$id]);
        $this->audit('delete', 'User', $id);
        $this->withSuccess('Utilisateur supprimé.');
        $this->redirect('/users');
    }

    public function changeStatus(int $id): void {
        $this->auth();
        $this->requirePermission('users.suspend');
        $this->checkCsrf("/users/{$id}");

        $db = Database::getConnection();

        if (!Rbac::canManageUser($id)) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/users');
        }

        $status = $_POST['status'] ?? 'inactive';
        $db->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$status, $id]);
        $this->audit('status_change', 'User', $id);
        $this->withSuccess('Statut mis à jour.');
        $this->redirect("/users/{$id}");
    }
}
