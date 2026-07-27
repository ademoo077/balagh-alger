<?php
namespace App\Helpers;

use App\Helpers\Database;

class AssignmentEngine {

    /**
     * Determine the responsible organization for a report.
     * Priority: subcategory-specific rule > category-only rule > fallback.
     * If multiple rules match for the same daira, the daira-specific one wins.
     */
    public static function resolve(int $categoryId, ?int $subcategoryId, int $dairaId): ?int {
        $db = Database::getConnection();

        // 1. Exact match: subcategory + daira
        if ($subcategoryId) {
            $stmt = $db->prepare("SELECT organization_id FROM organization_rules 
                WHERE category_id = ? AND subcategory_id = ? AND daira_id = ? AND is_active = 1 
                ORDER BY priority_order ASC LIMIT 1");
            $stmt->execute([$categoryId, $subcategoryId, $dairaId]);
            $orgId = $stmt->fetchColumn();
            if ($orgId) return (int) $orgId;
        }

        // 2. Subcategory + any daira (NULL daira = all dairas)
        if ($subcategoryId) {
            $stmt = $db->prepare("SELECT organization_id FROM organization_rules 
                WHERE category_id = ? AND subcategory_id = ? AND daira_id IS NULL AND is_active = 1 
                ORDER BY priority_order ASC LIMIT 1");
            $stmt->execute([$categoryId, $subcategoryId]);
            $orgId = $stmt->fetchColumn();
            if ($orgId) return (int) $orgId;
        }

        // 3. Category + specific daira
        $stmt = $db->prepare("SELECT organization_id FROM organization_rules 
            WHERE category_id = ? AND subcategory_id IS NULL AND daira_id = ? AND is_active = 1 
            ORDER BY priority_order ASC LIMIT 1");
        $stmt->execute([$categoryId, $dairaId]);
        $orgId = $stmt->fetchColumn();
        if ($orgId) return (int) $orgId;

        // 4. Category only (fallback for all dairas)
        $stmt = $db->prepare("SELECT organization_id FROM organization_rules 
            WHERE category_id = ? AND subcategory_id IS NULL AND daira_id IS NULL AND is_active = 1 
            ORDER BY priority_order ASC LIMIT 1");
        $stmt->execute([$categoryId]);
        $orgId = $stmt->fetchColumn();
        if ($orgId) return (int) $orgId;

        return null;
    }

    /**
     * Get the responsible central user (resp_central or chef_unite) for an organization.
     * This is the first person who sees the report after citizen creation.
     */
    public static function getCentralHandler(int $organizationId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT u.id, u.first_name, u.last_name, u.email, r.name as role_name
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE u.organization_id = ? AND u.status = 'active'
            AND r.name IN ('resp_central', 'chef_unite')
            ORDER BY r.level DESC
            LIMIT 1");
        $stmt->execute([$organizationId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get chef_unite for a specific daira within an organization.
     */
    public static function getChefUnite(int $organizationId, int $dairaId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT u.id, u.first_name, u.last_name, u.email
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE u.organization_id = ? AND u.daira_id = ? AND u.status = 'active'
            AND r.name = 'chef_unite'
            LIMIT 1");
        $stmt->execute([$organizationId, $dairaId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get chef_section for a specific section within an organization and daira.
     */
    public static function getChefSection(int $organizationId, int $dairaId, ?string $section): ?array {
        $db = Database::getConnection();
        $params = [$organizationId, $dairaId];
        $where = "u.organization_id = ? AND u.daira_id = ? AND u.status = 'active' AND r.name = 'chef_section'";
        
        if ($section) {
            $where .= " AND u.section = ?";
            $params[] = $section;
        }
        
        $stmt = $db->prepare("SELECT u.id, u.first_name, u.last_name, u.email
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE {$where}
            LIMIT 1");
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get available agents for a section within an organization and daira.
     */
    public static function getAgents(int $organizationId, int $dairaId, ?string $section): array {
        $db = Database::getConnection();
        $params = [$organizationId, $dairaId];
        $where = "u.organization_id = ? AND u.daira_id = ? AND u.status = 'active' AND r.name = 'intervenant'";
        
        if ($section) {
            $where .= " AND u.section = ?";
            $params[] = $section;
        }
        
        $stmt = $db->prepare("SELECT u.id, u.first_name, u.last_name, u.email
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE {$where}
            ORDER BY u.first_name");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Auto-assign report to central handler after citizen creation.
     * Returns the handler info.
     */
    public static function autoAssignToCentral(int $reportId, int $organizationId): ?array {
        $db = Database::getConnection();
        $handler = self::getCentralHandler($organizationId);
        
        if (!$handler) return null;
        
        $stmt = $db->prepare("UPDATE reports SET 
            assigned_to = ?, assigned_by = ?, assigned_at = NOW(), 
            assigned_at_central = NOW(), workflow_step = 1, status = 'acknowledged'
            WHERE id = ?");
        $stmt->execute([$handler['id'], $handler['id'], $reportId]);
        
        // Log in history
        $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value) 
            VALUES (?, ?, 'auto_assign_central', ?)")
            ->execute([$reportId, $handler['id'], "Affecté automatiquement à {$handler['first_name']} {$handler['last_name']} ({$handler['role_name']})"]);
        
        // Notify
        $title = __('notifications.new_assignment_title');
        $msg = __('notifications.new_assignment_msg');
        Notification::create($handler['id'], 'assignment', $title, $msg, ['report_id' => $reportId]);
        
        return $handler;
    }

    /**
     * Assign from central to chef_unite.
     */
    public static function assignToChefUnite(int $reportId, int $assignedBy, int $chefUniteId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE reports SET 
            assigned_to = ?, assigned_by = ?, assigned_at = NOW(), 
            assigned_at_chef_unite = NOW(), workflow_step = 2, status = 'assigned'
            WHERE id = ?");
        $stmt->execute([$chefUniteId, $assignedBy, $reportId]);
        
        $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value) 
            VALUES (?, ?, 'assign_chef_unite', ?)")
            ->execute([$reportId, $assignedBy, "Assigné au Chef d'Unité"]);
        
        $title = __('notifications.assigned_title');
        $msg = __('notifications.assigned_by_central_msg');
        Notification::create($chefUniteId, 'assignment', $title, $msg, ['report_id' => $reportId]);
    }

    /**
     * Assign from chef_unite to chef_section.
     */
    public static function assignToChefSection(int $reportId, int $assignedBy, int $chefSectionId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE reports SET 
            assigned_to = ?, assigned_by = ?, assigned_at = NOW(), 
            assigned_at_chef_section = NOW(), workflow_step = 3, status = 'assigned'
            WHERE id = ?");
        $stmt->execute([$chefSectionId, $assignedBy, $reportId]);
        
        $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value) 
            VALUES (?, ?, 'assign_chef_section', ?)")
            ->execute([$reportId, $assignedBy, "Assigné au Chef de Section"]);
        
        $title = __('notifications.assigned_title');
        $msg = __('notifications.assigned_by_chef_unite_msg');
        Notification::create($chefSectionId, 'assignment', $title, $msg, ['report_id' => $reportId]);
    }

    /**
     * Assign from chef_section to agent.
     */
    public static function assignToAgent(int $reportId, int $assignedBy, int $agentId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE reports SET 
            assigned_to = ?, assigned_by = ?, assigned_at = NOW(), 
            assigned_at_agent = NOW(), workflow_step = 4, status = 'in_progress'
            WHERE id = ?");
        $stmt->execute([$agentId, $assignedBy, $reportId]);
        
        $db->prepare("INSERT INTO report_history (report_id, user_id, action, new_value) 
            VALUES (?, ?, 'assign_agent', ?)")
            ->execute([$reportId, $assignedBy, "Assigné à l'agent intervenant"]);
        
        $title = __('notifications.mission_title');
        $msg = __('notifications.mission_msg');
        Notification::create($agentId, 'assignment', $title, $msg, ['report_id' => $reportId]);
    }

    /**
     * Check if all required 'after' photos exist for a report.
     */
    public static function hasAfterPhotos(int $reportId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM intervention_photos 
            WHERE report_id = ? AND photo_type = 'after'");
        $stmt->execute([$reportId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
