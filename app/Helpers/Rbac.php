<?php
namespace App\Helpers;

use App\Helpers\Database;
use App\Helpers\Session;

/**
 * RBAC Helper - Centralized permission checking and query scoping.
 * 
 * Hierarchy: admin_central(7) > resp_central(6) > chef_unite(5) > chef_section(4) > intervenant(3) > citizen(1)
 * 
 * Scoping rules:
 * - citizen: own reports only
 * - intervenant: assigned reports only
 * - chef_section: org + daira
 * - chef_unite: org + daira
 * - resp_central: org (all dairas)
 * - admin_local: org (all dairas)
 * - admin_central: everything
 */
class Rbac {

    const HIERARCHY = [
        'admin_central'  => 7,
        'resp_central'   => 6,
        'admin_local'    => 6,
        'chef_unite'     => 5,
        'chef_section'   => 4,
        'intervenant'    => 3,
        'citizen'        => 1,
    ];

    /**
     * Get the user's primary (highest) role name.
     */
    public static function getPrimaryRole(): ?string {
        $roles = Session::get('user_roles', []);
        $best = null;
        $bestLevel = 0;
        foreach ($roles as $roleName) {
            $level = self::HIERARCHY[$roleName] ?? 0;
            if ($level > $bestLevel) {
                $bestLevel = $level;
                $best = $roleName;
            }
        }
        return $best;
    }

    /**
     * Check if user has a specific permission.
     * Uses permissions loaded into session at login.
     */
    public static function has(string $permission): bool {
        $roles = Session::get('user_roles', []);
        
        // admin_central has everything
        if (in_array('admin_central', $roles)) return true;
        
        $userPerms = Session::get('user_permissions', []);
        return in_array($permission, $userPerms);
    }

    /**
     * Check if user has ANY of the given permissions.
     */
    public static function hasAny(array $permissions): bool {
        foreach ($permissions as $perm) {
            if (self::has($perm)) return true;
        }
        return false;
    }

    /**
     * Check if user has ALL of the given permissions.
     */
    public static function hasAll(array $permissions): bool {
        foreach ($permissions as $perm) {
            if (!self::has($perm)) return false;
        }
        return true;
    }

    /**
     * Check if user has a specific role.
     */
    public static function isRole(string ...$roleNames): bool {
        $userRoles = Session::get('user_roles', []);
        return (bool) array_intersect($roleNames, $userRoles);
    }

    /**
     * Check if user has at least the given hierarchy level.
     */
    public static function minLevel(int $level): bool {
        $primary = self::getPrimaryRole();
        if (!$primary) return false;
        return (self::HIERARCHY[$primary] ?? 0) >= $level;
    }

    /**
     * Is this user a staff member (non-citizen)?
     */
    public static function isStaff(): bool {
        return !self::isRole('citizen');
    }

    /**
     * Can this user manage other users (create/edit/suspend)?
     */
    public static function canManageUsers(): bool {
        return self::hasAny(['users.create', 'users.update', 'users.suspend']);
    }

    /**
     * Can this user create accounts for the given target role?
     * Hierarchy rules:
     * - admin_central: can create all roles
     * - resp_central / admin_local: can create chef_unite, chef_section, intervenant
     * - chef_unite: can create chef_section, intervenant
     * - chef_section: can create intervenant
     */
    public static function canCreateRole(string $targetRole): bool {
        $primary = self::getPrimaryRole();
        if (!$primary) return false;

        $myLevel = self::HIERARCHY[$primary] ?? 0;
        $targetLevel = self::HIERARCHY[$targetRole] ?? 0;

        // Can only create roles at a LOWER level than yourself
        return $myLevel > $targetLevel;
    }

    /**
     * Can this user manage the target user?
     * Rules:
     * - admin_central: everyone
     * - resp_central / admin_local: only users in same org
     * - chef_unite: users in same org + daira
     * - chef_section: intervenants in same org + daira
     */
    public static function canManageUser(int $targetUserId): bool {
        $primary = self::getPrimaryRole();
        if (!$primary) return false;

        if ($primary === 'admin_central') return true;

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT u.organization_id, u.daira_id, GROUP_CONCAT(r.name) as roles
            FROM users u LEFT JOIN user_roles ur ON u.id = ur.user_id LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.id = ? GROUP BY u.id");
        $stmt->execute([$targetUserId]);
        $target = $stmt->fetch();
        if (!$target) return false;

        $myOrgId = Session::get('organization_id');
        $myDairaId = Session::get('daira_id');

        if (in_array('resp_central', $roles = Session::get('user_roles', [])) || in_array('admin_local', $roles)) {
            return $target['organization_id'] == $myOrgId;
        }

        if (in_array('chef_unite', $roles)) {
            return $target['organization_id'] == $myOrgId && $target['daira_id'] == $myDairaId;
        }

        if (in_array('chef_section', $roles)) {
            $targetRoles = explode(',', $target['roles'] ?? '');
            return $target['organization_id'] == $myOrgId 
                && $target['daira_id'] == $myDairaId 
                && in_array('intervenant', $targetRoles);
        }

        return false;
    }

    /**
     * Can this user view the given report?
     */
    public static function canViewReport(array $report): bool {
        $primary = self::getPrimaryRole();
        if (!$primary) return false;

        $userId = Session::getUserId();
        $myOrgId = Session::get('organization_id');
        $myDairaId = Session::get('daira_id');

        switch ($primary) {
            case 'admin_central':
                return true;

            case 'resp_central':
            case 'admin_local':
                return $report['organization_id'] == $myOrgId;

            case 'chef_unite':
                return $report['organization_id'] == $myOrgId
                    && ($report['daira_id'] == $myDairaId || $report['assigned_to'] == $userId);

            case 'chef_section':
                return $report['organization_id'] == $myOrgId
                    && ($report['daira_id'] == $myDairaId || $report['assigned_to'] == $userId);

            case 'intervenant':
                return $report['assigned_to'] == $userId;

            case 'citizen':
                return $report['citizen_id'] == $userId;

            default:
                return false;
        }
    }

    /**
     * Can this user intervene on the report (field work)?
     */
    public static function canIntervene(array $report): bool {
        $primary = self::getPrimaryRole();
        $userId = Session::getUserId();
        return $primary === 'intervenant' && $report['assigned_to'] == $userId;
    }

    /**
     * Can this user assign the report to someone?
     * Assignment hierarchy:
     * - resp_central/admin_local: to chef_unite, chef_section, or agent (within org)
     * - chef_unite: to chef_section or agent (within org + daira)
     * - chef_section: to agent only (within org + daira)
     */
    public static function canAssignReport(array $report): bool {
        $primary = self::getPrimaryRole();
        $userId = Session::getUserId();
        $myOrgId = Session::get('organization_id');
        $myDairaId = Session::get('daira_id');

        if ($primary === 'admin_central') return true;

        if (in_array($primary, ['resp_central', 'admin_local'])) {
            return $report['organization_id'] == $myOrgId;
        }

        if ($primary === 'chef_unite') {
            return $report['organization_id'] == $myOrgId
                && ($report['daira_id'] == $myDairaId || $report['assigned_to'] == $userId);
        }

        if ($primary === 'chef_section') {
            return $report['organization_id'] == $myOrgId
                && ($report['daira_id'] == $myDairaId || $report['assigned_to'] == $userId);
        }

        return false;
    }

    /**
     * Can this user validate at the section level? (workflow step 5 -> 6)
     */
    public static function canValidateSection(array $report): bool {
        $primary = self::getPrimaryRole();
        $userId = Session::getUserId();
        $myOrgId = Session::get('organization_id');
        $myDairaId = Session::get('daira_id');
        $wf = $report['workflow_step'] ?? 0;

        if ($primary === 'admin_central') return true;

        if (in_array($primary, ['resp_central', 'admin_local'])) {
            return $report['organization_id'] == $myOrgId && $wf >= 5;
        }

        if ($primary === 'chef_section') {
            return $report['organization_id'] == $myOrgId
                && ($report['daira_id'] == $myDairaId || $report['assigned_to'] == $userId)
                && $wf >= 5 && $wf < 7;
        }

        return false;
    }

    /**
     * Can this user validate at the unite level? (workflow step 6 -> 7)
     */
    public static function canValidateUnite(array $report): bool {
        $primary = self::getPrimaryRole();
        $userId = Session::getUserId();
        $myOrgId = Session::get('organization_id');
        $myDairaId = Session::get('daira_id');
        $wf = $report['workflow_step'] ?? 0;

        if ($primary === 'admin_central') return true;

        if (in_array($primary, ['resp_central', 'admin_local'])) {
            return $report['organization_id'] == $myOrgId && $wf >= 6;
        }

        if ($primary === 'chef_unite') {
            return $report['organization_id'] == $myOrgId
                && ($report['daira_id'] == $myDairaId || $report['assigned_to'] == $userId)
                && $wf >= 6;
        }

        return false;
    }

    /**
     * Can this user close the report permanently?
     */
    public static function canCloseReport(array $report): bool {
        $primary = self::getPrimaryRole();
        $myOrgId = Session::get('organization_id');
        $wf = $report['workflow_step'] ?? 0;

        if ($primary === 'admin_central') return true;

        if (in_array($primary, ['resp_central', 'admin_local'])) {
            return $report['organization_id'] == $myOrgId && $wf >= 7;
        }

        return false;
    }

    /**
     * Can this user redirect/reassign the report to a different organization?
     */
    public static function canRedirectReport(array $report): bool {
        $primary = self::getPrimaryRole();
        return $primary === 'admin_central' || in_array($primary, ['resp_central', 'admin_local']);
    }

    /**
     * Get the list of users this user can assign reports to.
     * Returns users scoped by org and daira according to hierarchy.
     */
    public static function getAssignableUsers(int $organizationId, int $dairaId): array {
        $db = Database::getConnection();
        $primary = self::getPrimaryRole();
        $userId = Session::getUserId();
        $myDairaId = Session::get('daira_id');

        if ($primary === 'admin_central') {
            $stmt = $db->prepare("SELECT u.id, u.first_name, u.last_name, r.name as role_name, r.level
                FROM users u JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id
                WHERE u.organization_id = ? AND u.status = 'active' AND r.name != 'citizen'
                ORDER BY r.level DESC, u.first_name");
            $stmt->execute([$organizationId]);
        } elseif (in_array($primary, ['resp_central', 'admin_local'])) {
            $stmt = $db->prepare("SELECT u.id, u.first_name, u.last_name, r.name as role_name, r.level
                FROM users u JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id
                WHERE u.organization_id = ? AND u.status = 'active'
                AND r.name IN ('chef_unite', 'chef_section', 'intervenant')
                ORDER BY r.level DESC, u.first_name");
            $stmt->execute([$organizationId]);
        } elseif ($primary === 'chef_unite') {
            $stmt = $db->prepare("SELECT u.id, u.first_name, u.last_name, r.name as role_name, r.level
                FROM users u JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id
                WHERE u.organization_id = ? AND u.daira_id = ? AND u.status = 'active'
                AND r.name IN ('chef_section', 'intervenant')
                ORDER BY r.level DESC, u.first_name");
            $stmt->execute([$organizationId, $myDairaId]);
        } elseif ($primary === 'chef_section') {
            $stmt = $db->prepare("SELECT u.id, u.first_name, u.last_name, r.name as role_name, r.level
                FROM users u JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id
                WHERE u.organization_id = ? AND u.daira_id = ? AND u.status = 'active'
                AND r.name = 'intervenant'
                ORDER BY u.first_name");
            $stmt->execute([$organizationId, $myDairaId]);
        } else {
            return [];
        }

        return $stmt->fetchAll();
    }

    /**
     * Get the SQL WHERE clause to scope reports by the current user's role.
     * Returns ['where' => string, 'params' => array]
     */
    public static function scopeReports(): array {
        $primary = self::getPrimaryRole();
        $userId = Session::getUserId();
        $myOrgId = Session::get('organization_id');
        $myDairaId = Session::get('daira_id');

        $params = [];

        switch ($primary) {
            case 'admin_central':
                // No filtering - sees everything
                return ['where' => '', 'params' => []];

            case 'resp_central':
            case 'admin_local':
                return ['where' => ' AND r.organization_id = ?', 'params' => [$myOrgId]];

            case 'chef_unite':
                return ['where' => ' AND r.organization_id = ? AND (r.daira_id = ? OR r.assigned_to = ?)', 'params' => [$myOrgId, $myDairaId, $userId]];

            case 'chef_section':
                return ['where' => ' AND r.organization_id = ? AND (r.daira_id = ? OR r.assigned_to = ?)', 'params' => [$myOrgId, $myDairaId, $userId]];

            case 'intervenant':
                return ['where' => ' AND r.assigned_to = ?', 'params' => [$userId]];

            case 'citizen':
                return ['where' => ' AND r.citizen_id = ?', 'params' => [$userId]];

            default:
                return ['where' => ' AND 1=0', 'params' => []];
        }
    }

    /**
     * Get the SQL WHERE clause to scope users by the current user's role.
     */
    public static function scopeUsers(): array {
        $primary = self::getPrimaryRole();
        $myOrgId = Session::get('organization_id');
        $myDairaId = Session::get('daira_id');

        $params = [];

        switch ($primary) {
            case 'admin_central':
                return ['where' => '', 'params' => []];

            case 'resp_central':
            case 'admin_local':
                return ['where' => ' AND u.organization_id = ?', 'params' => [$myOrgId]];

            case 'chef_unite':
                return ['where' => ' AND u.organization_id = ? AND u.daira_id = ?', 'params' => [$myOrgId, $myDairaId]];

            case 'chef_section':
                return ['where' => ' AND u.organization_id = ? AND u.daira_id = ? AND EXISTS (SELECT 1 FROM user_roles ur2 JOIN roles r2 ON ur2.role_id = r2.id WHERE ur2.user_id = u.id AND r2.name = \'intervenant\')', 'params' => [$myOrgId, $myDairaId]];

            default:
                return ['where' => ' AND 1=0', 'params' => []];
        }
    }

    /**
     * Get the SQL WHERE clause to scope interventions (reports in workflow).
     */
    public static function scopeInterventions(): array {
        $primary = self::getPrimaryRole();
        $userId = Session::getUserId();
        $myOrgId = Session::get('organization_id');
        $myDairaId = Session::get('daira_id');

        $params = [];

        switch ($primary) {
            case 'admin_central':
                return ['where' => '', 'params' => []];

            case 'resp_central':
            case 'admin_local':
                return ['where' => ' AND r.organization_id = ?', 'params' => [$myOrgId]];

            case 'chef_unite':
                return ['where' => ' AND r.organization_id = ? AND (r.daira_id = ? OR r.assigned_to = ?)', 'params' => [$myOrgId, $myDairaId, $userId]];

            case 'chef_section':
                return ['where' => ' AND r.organization_id = ? AND (r.daira_id = ? OR r.assigned_to = ?)', 'params' => [$myOrgId, $myDairaId, $userId]];

            case 'intervenant':
                return ['where' => ' AND r.assigned_to = ?', 'params' => [$userId]];

            default:
                return ['where' => ' AND 1=0', 'params' => []];
        }
    }

    /**
     * Get the SQL WHERE clause to scope organizations.
     */
    public static function scopeOrganizations(): array {
        $primary = self::getPrimaryRole();
        $myOrgId = Session::get('organization_id');

        switch ($primary) {
            case 'admin_central':
                return ['where' => '', 'params' => []];
            case 'resp_central':
            case 'admin_local':
            case 'chef_unite':
            case 'chef_section':
                return ['where' => ' AND o.id = ?', 'params' => [$myOrgId]];
            default:
                return ['where' => ' AND 1=0', 'params' => []];
        }
    }

    /**
     * Load user permissions from DB into session.
     * Called at login.
     */
    public static function loadPermissions(int $userId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.name FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            JOIN user_roles ur ON rp.role_id = ur.role_id
            WHERE ur.user_id = ?");
        $stmt->execute([$userId]);
        $permissions = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        Session::set('user_permissions', array_unique($permissions));
    }

    /**
     * Check if user can view the dashboard.
     */
    public static function canViewDashboard(): bool {
        return self::has('dashboard.view');
    }

    /**
     * Check if user can view stats.
     */
    public static function canViewStats(): bool {
        return self::has('dashboard.stats');
    }

    /**
     * Check if user can view audit logs.
     */
    public static function canViewAudit(): bool {
        return self::has('audit.view');
    }

    /**
     * Check if user can manage settings.
     */
    public static function canManageSettings(): bool {
        return self::has('settings.update');
    }
}
