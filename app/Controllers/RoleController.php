<?php
namespace App\Controllers;

use App\Helpers\Rbac;
use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\AuditLog;

class RoleController extends Controller {
    public function index(): void {
        $this->auth();
        if (!Rbac::has('settings.manage')) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/dashboard');
            return;
        }

        $db = Database::getConnection();

        $roles = $db->query("SELECT * FROM roles ORDER BY level DESC, label")->fetchAll();

        $permissions = $db->query("SELECT * FROM permissions ORDER BY module, name")->fetchAll();

        $rolePerms = $db->query("SELECT role_id, permission_id FROM role_permissions")->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_COLUMN);

        $modules = [];
        foreach ($permissions as $p) {
            $modules[$p['module']][] = $p;
        }

        $csrfToken = \App\Helpers\Csrf::generate();
        $this->view('settings/roles', compact('roles', 'permissions', 'rolePerms', 'modules', 'csrfToken'));
    }

    public function update(): void {
        $this->auth();
        if (!Rbac::has('settings.manage')) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/dashboard');
            return;
        }

        if (!\App\Helpers\Csrf::verify($_POST['_token'] ?? '')) {
            $this->withError('Token invalide.');
            $this->redirect('/settings/roles');
            return;
        }

        $roleId = (int) ($_POST['role_id'] ?? 0);
        $permissions = $_POST['permissions'] ?? [];

        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT id FROM roles WHERE id = ?");
        $stmt->execute([$roleId]);
        if (!$stmt->fetch()) {
            $this->withError('Rôle introuvable.');
            $this->redirect('/settings/roles');
            return;
        }

        $db->beginTransaction();
        try {
            $db->prepare("DELETE FROM role_permissions WHERE role_id = ?")->execute([$roleId]);

            if (!empty($permissions)) {
                $stmt = $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
                foreach ($permissions as $permId) {
                    $stmt->execute([$roleId, (int) $permId]);
                }
            }

            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            $this->withError('Erreur lors de la sauvegarde : ' . $e->getMessage());
            $this->redirect('/settings/roles');
            return;
        }

        AuditLog::log('update', 'RolePermissions', $roleId, null, ['permissions' => $permissions]);
        $this->withSuccess('Permissions mises à jour avec succès.');
        $this->redirect('/settings/roles');
    }
}
