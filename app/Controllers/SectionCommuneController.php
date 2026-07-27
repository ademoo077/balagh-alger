<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Csrf;
use App\Helpers\AuditLog;
use App\Helpers\Rbac;
use App\Controllers\Controller;

class SectionCommuneController extends Controller {

    public function index(): void {
        $this->auth();
        $db = Database::getConnection();
        $userId = Session::getUserId();
        $primaryRole = Rbac::getPrimaryRole();
        $orgId = Session::get('organization_id');
        $dairaId = Session::get('daira_id');

        if (!in_array($primaryRole, ['admin_central', 'resp_central', 'admin_local', 'chef_unite'])) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/dashboard');
        }

        $dairas = [];
        $chefSections = [];
        $communes = [];
        $assignments = [];

        if ($primaryRole === 'admin_central') {
            $dairas = $db->query("SELECT id, name, code FROM dairas WHERE is_active = 1 ORDER BY name")->fetchAll();
            $chefSections = $db->query("SELECT u.id, u.first_name, u.last_name, u.organization_id, u.daira_id,
                o.name as org_name, d.name as daira_name, u.section
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id
                LEFT JOIN organizations o ON u.organization_id = o.id
                LEFT JOIN dairas d ON u.daira_id = d.id
                WHERE r.name = 'chef_section' AND u.status = 'active' AND u.deleted_at IS NULL
                ORDER BY o.name, d.name, u.last_name")->fetchAll();
        } elseif (in_array($primaryRole, ['resp_central', 'admin_local'])) {
            $dairas = $db->prepare("SELECT id, name, code FROM dairas WHERE is_active = 1 ORDER BY name");
            $dairas->execute();
            $dairas = $dairas->fetchAll();

            $stmt = $db->prepare("SELECT u.id, u.first_name, u.last_name, u.organization_id, u.daira_id,
                o.name as org_name, d.name as daira_name, u.section
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id
                LEFT JOIN organizations o ON u.organization_id = o.id
                LEFT JOIN dairas d ON u.daira_id = d.id
                WHERE r.name = 'chef_section' AND u.status = 'active' AND u.deleted_at IS NULL
                AND u.organization_id = ?
                ORDER BY d.name, u.last_name");
            $stmt->execute([$orgId]);
            $chefSections = $stmt->fetchAll();
        } else {
            $dairaStmt = $db->prepare("SELECT id, name, code FROM dairas WHERE id = ?");
            $dairaStmt->execute([$dairaId]);
            $dairas = $dairaStmt->fetchAll();

            $stmt = $db->prepare("SELECT u.id, u.first_name, u.last_name, u.organization_id, u.daira_id,
                o.name as org_name, d.name as daira_name, u.section
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id
                LEFT JOIN organizations o ON u.organization_id = o.id
                LEFT JOIN dairas d ON u.daira_id = d.id
                WHERE r.name = 'chef_section' AND u.status = 'active' AND u.deleted_at IS NULL
                AND u.organization_id = ? AND u.daira_id = ?
                ORDER BY u.last_name");
            $stmt->execute([$orgId, $dairaId]);
            $chefSections = $stmt->fetchAll();
        }

        if ($primaryRole === 'chef_unite') {
            $communeStmt = $db->prepare("SELECT c.id, c.name, c.daira_id, d.name as daira_name
                FROM communes c JOIN dairas d ON c.daira_id = d.id
                WHERE c.is_active = 1 AND c.daira_id = ? ORDER BY c.name");
            $communeStmt->execute([$dairaId]);
            $allCommunes = $communeStmt->fetchAll();
        } elseif (in_array($primaryRole, ['resp_central', 'admin_local'])) {
            $communeStmt = $db->prepare("SELECT c.id, c.name, c.daira_id, d.name as daira_name
                FROM communes c JOIN dairas d ON c.daira_id = d.id
                WHERE c.is_active = 1 ORDER BY d.name, c.name");
            $communeStmt->execute();
            $allCommunes = $communeStmt->fetchAll();
        } else {
            $allCommunes = $db->query("SELECT c.id, c.name, c.daira_id, d.name as daira_name
                FROM communes c JOIN dairas d ON c.daira_id = d.id
                WHERE c.is_active = 1 ORDER BY d.name, c.name")->fetchAll();
        }

        $assignSql = "SELECT sc.user_id, sc.commune_id, sc.assigned_at, sc.organization_id, sc.daira_id,
            cu.first_name as assigned_by_first, cu.last_name as assigned_by_last,
            cs.first_name as chef_first, cs.last_name as chef_last,
            co.name as commune_name, d.name as daira_name
            FROM section_communes sc
            JOIN users cu ON sc.assigned_by = cu.id
            JOIN users cs ON sc.user_id = cs.id
            JOIN communes co ON sc.commune_id = co.id
            JOIN dairas d ON sc.daira_id = d.id";

        $assignParams = [];
        if ($primaryRole === 'chef_unite') {
            $assignSql .= " WHERE sc.organization_id = ? AND sc.daira_id = ?";
            $assignParams = [$orgId, $dairaId];
        } elseif (in_array($primaryRole, ['resp_central', 'admin_local'])) {
            $assignSql .= " WHERE sc.organization_id = ?";
            $assignParams = [$orgId];
        }

        $assignSql .= " ORDER BY d.name, co.name";
        $assignStmt = $db->prepare($assignSql);
        $assignStmt->execute($assignParams);
        $assignments = $assignStmt->fetchAll();

        $csrfToken = Csrf::generate();

        $this->view('section-communes/index', compact(
            'dairas', 'chefSections', 'allCommunes', 'assignments',
            'csrfToken', 'primaryRole', 'orgId', 'dairaId'
        ));
    }

    public function assign(): void {
        $this->auth();
        $this->checkCsrf('/section-communes');

        $db = Database::getConnection();
        $userId = Session::getUserId();
        $primaryRole = Rbac::getPrimaryRole();
        $orgId = Session::get('organization_id');
        $dairaId = Session::get('daira_id');

        if (!in_array($primaryRole, ['admin_central', 'resp_central', 'admin_local', 'chef_unite'])) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/section-communes');
        }

        $chefSectionId = (int)($_POST['user_id'] ?? 0);
        $communeId = (int)($_POST['commune_id'] ?? 0);

        if (!$chefSectionId || !$communeId) {
            $this->withError('Veuillez sélectionner un chef de section et une commune.');
            $this->redirect('/section-communes');
        }

        $csStmt = $db->prepare("SELECT u.id, u.organization_id, u.daira_id FROM users u
            JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id
            WHERE u.id = ? AND r.name = 'chef_section' AND u.status = 'active' AND u.deleted_at IS NULL");
        $csStmt->execute([$chefSectionId]);
        $cs = $csStmt->fetch();

        if (!$cs) {
            $this->withError('Chef de section non trouvé.');
            $this->redirect('/section-communes');
        }

        $cStmt = $db->prepare("SELECT id, daira_id FROM communes WHERE id = ?");
        $cStmt->execute([$communeId]);
        $commune = $cStmt->fetch();

        if (!$commune) {
            $this->withError('Commune non trouvée.');
            $this->redirect('/section-communes');
        }

        if (in_array($primaryRole, ['resp_central', 'admin_local'])) {
            if ($cs['organization_id'] != $orgId) {
                $this->withError('Ce chef de section n\'appartient pas à votre organisme.');
                $this->redirect('/section-communes');
            }
        } elseif ($primaryRole === 'chef_unite') {
            if ($cs['organization_id'] != $orgId || $cs['daira_id'] != $dairaId) {
                $this->withError('Ce chef de section n\'appartient pas à votre daïra.');
                $this->redirect('/section-communes');
            }
            if ($commune['daira_id'] != $dairaId) {
                $this->withError('Cette commune n\'appartient pas à votre daïra.');
                $this->redirect('/section-communes');
            }
        }

        $stmt = $db->prepare("INSERT INTO section_communes (user_id, commune_id, organization_id, daira_id, assigned_by)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE assigned_by = VALUES(assigned_by), assigned_at = CURRENT_TIMESTAMP()");
        $stmt->execute([$chefSectionId, $communeId, $cs['organization_id'], $commune['daira_id'], $userId]);

        AuditLog::log('assign_commune', 'SectionCommune', null, null, [
            'user_id' => $chefSectionId,
            'commune_id' => $communeId,
        ]);

        $this->withSuccess('Commune assignée au chef de section.');
        $this->redirect('/section-communes');
    }

    public function remove(): void {
        $this->auth();
        $this->checkCsrf('/section-communes');

        $db = Database::getConnection();
        $userId = Session::getUserId();
        $primaryRole = Rbac::getPrimaryRole();
        $orgId = Session::get('organization_id');
        $dairaId = Session::get('daira_id');

        if (!in_array($primaryRole, ['admin_central', 'resp_central', 'admin_local', 'chef_unite'])) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/section-communes');
        }

        $chefSectionId = (int)($_POST['user_id'] ?? 0);
        $communeId = (int)($_POST['commune_id'] ?? 0);

        if (!$chefSectionId || !$communeId) {
            $this->withError('Paramètres invalides.');
            $this->redirect('/section-communes');
        }

        $check = $db->prepare("SELECT id FROM section_communes WHERE user_id = ? AND commune_id = ?");
        $check->execute([$chefSectionId, $communeId]);
        if (!$check->fetch()) {
            $this->withError('Assignment non trouvée.');
            $this->redirect('/section-communes');
        }

        $stmt = $db->prepare("DELETE FROM section_communes WHERE user_id = ? AND commune_id = ?");
        $stmt->execute([$chefSectionId, $communeId]);

        AuditLog::log('remove_commune', 'SectionCommune', null, [
            'user_id' => $chefSectionId,
            'commune_id' => $communeId,
        ], null);

        $this->withSuccess('Commune retirée du chef de section.');
        $this->redirect('/section-communes');
    }

    public function getByUser(int $userId): void {
        $this->auth();
        $db = Database::getConnection();
        $primaryRole = Rbac::getPrimaryRole();
        $orgId = Session::get('organization_id');
        $dairaId = Session::get('daira_id');

        $sql = "SELECT sc.commune_id, c.name as commune_name, d.name as daira_name,
            sc.assigned_at, u.first_name, u.last_name
            FROM section_communes sc
            JOIN communes c ON sc.commune_id = c.id
            JOIN dairas d ON sc.daira_id = d.id
            JOIN users u ON sc.assigned_by = u.id
            WHERE sc.user_id = ?";
        $params = [$userId];

        if ($primaryRole === 'chef_unite') {
            $sql .= " AND sc.organization_id = ? AND sc.daira_id = ?";
            $params[] = $orgId;
            $params[] = $dairaId;
        } elseif (in_array($primaryRole, ['resp_central', 'admin_local'])) {
            $sql .= " AND sc.organization_id = ?";
            $params[] = $orgId;
        }

        $sql .= " ORDER BY d.name, c.name";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $this->json($stmt->fetchAll());
    }
}
