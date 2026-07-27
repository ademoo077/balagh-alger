<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Csrf;
use App\Helpers\Helper;
use App\Helpers\AuditLog;
use App\Helpers\Rbac;
use App\Controllers\Controller;

class OrganizationController extends Controller {

    public function index(): void {
        $this->auth();
        $this->requirePermission('organizations.view');

        $scope = Rbac::scopeOrganizations();
        $db = Database::getConnection();

        $where = "o.is_active = 1" . $scope['where'];
        $params = $scope['params'];

        $stmt = $db->prepare("SELECT o.*, 
            (SELECT COUNT(*) FROM users WHERE organization_id = o.id AND deleted_at IS NULL) as users_count, 
            (SELECT COUNT(*) FROM reports WHERE organization_id = o.id AND deleted_at IS NULL) as reports_count 
            FROM organizations o WHERE {$where} ORDER BY o.name");
        $stmt->execute($params);
        $organizations = $stmt->fetchAll();

        $canCreate = Rbac::has('organizations.create');
        $this->view('organizations/index', compact('organizations', 'canCreate'));
    }

    public function create(): void {
        $this->auth();
        $this->requirePermission('organizations.create');
        $csrfToken = Csrf::generate();
        $this->view('organizations/create', compact('csrfToken'));
    }

    public function store(): void {
        $this->auth();
        $this->requirePermission('organizations.create');
        $this->checkCsrf('/organizations');

        $db = Database::getConnection();
        $slug = Helper::slugify($_POST['name']);
        $stmt = $db->prepare("INSERT INTO organizations (name, name_ar, slug, code, address, phone, email, website, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['name'], $_POST['name_ar'] ?? null, $slug, $_POST['code'], $_POST['address'] ?? null, $_POST['phone'] ?? null, $_POST['email'] ?? null, $_POST['website'] ?? null, $_POST['description'] ?? null]);

        $this->audit('create', 'Organization', $db->lastInsertId());
        $this->withSuccess('Organisme créé.');
        $this->redirect('/organizations');
    }

    public function show(int $id): void {
        $this->auth();
        $this->requirePermission('organizations.view');

        $db = Database::getConnection();
        $org = $db->prepare("SELECT * FROM organizations WHERE id = ?");
        $org->execute([$id]);
        $org = $org->fetch();
        if (!$org) { $this->withError('Organisme non trouvé.'); $this->redirect('/organizations'); }

        // Verify scope
        $scope = Rbac::scopeOrganizations();
        if ($scope['where']) {
            $check = $db->prepare("SELECT COUNT(*) FROM organizations o WHERE o.id = ?" . $scope['where']);
            $check->execute(array_merge([$id], $scope['params']));
            if ($check->fetchColumn() == 0) {
                $this->withError('Accès non autorisé.');
                $this->redirect('/organizations');
            }
        }

        $users = $db->prepare("SELECT u.*, GROUP_CONCAT(r.label) as role_labels FROM users u LEFT JOIN user_roles ur ON u.id = ur.user_id LEFT JOIN roles r ON ur.role_id = r.id WHERE u.organization_id = ? AND u.deleted_at IS NULL GROUP BY u.id");
        $users->execute([$id]);
        $users = $users->fetchAll();

        $reports = $db->prepare("SELECT r.*, c.name as category_name FROM reports r JOIN categories c ON r.category_id = c.id WHERE r.organization_id = ? AND r.deleted_at IS NULL ORDER BY r.created_at DESC LIMIT 20");
        $reports->execute([$id]);
        $reports = $reports->fetchAll();

        $canEdit = Rbac::has('organizations.update');
        $this->view('organizations/show', compact('org', 'users', 'reports', 'canEdit'));
    }

    public function edit(int $id): void {
        $this->auth();
        $this->requirePermission('organizations.update');
        $db = Database::getConnection();
        $org = $db->prepare("SELECT * FROM organizations WHERE id = ?");
        $org->execute([$id]);
        $org = $org->fetch();
        if (!$org) { $this->redirect('/organizations'); }

        $csrfToken = Csrf::generate();
        $this->view('organizations/edit', compact('org', 'csrfToken'));
    }

    public function update(int $id): void {
        $this->auth();
        $this->requirePermission('organizations.update');
        $this->checkCsrf("/organizations/{$id}/edit");

        Database::getConnection()->prepare("UPDATE organizations SET name = ?, name_ar = ?, code = ?, address = ?, phone = ?, email = ?, website = ?, description = ? WHERE id = ?")
            ->execute([$_POST['name'], $_POST['name_ar'] ?? null, $_POST['code'], $_POST['address'] ?? null, $_POST['phone'] ?? null, $_POST['email'] ?? null, $_POST['website'] ?? null, $_POST['description'] ?? null, $id]);

        $this->audit('update', 'Organization', $id);
        $this->withSuccess('Organisme mis à jour.');
        $this->redirect("/organizations/{$id}");
    }
}
