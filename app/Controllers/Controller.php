<?php
namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Csrf;
use App\Helpers\Router;
use App\Helpers\Rbac;
use App\Middleware\AuthMiddleware;

abstract class Controller {
    
    protected string $layout = 'layouts/main';
    
    protected function view(string $view, array $data = []): void {
        extract($data);
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View {$view} not found.");
        }
        
        \App\Helpers\I18n::init();
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        
        $layoutFile = VIEW_PATH . '/' . $this->layout . '.php';
        require $layoutFile;
    }
    
    protected function viewRaw(string $view, array $data = []): void {
        extract($data);
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View {$view} not found.");
        }
        require $viewFile;
        exit;
    }

    protected function viewPartial(string $view, array $data = []): void {
        extract($data);
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
    }
    
    protected function json($data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect(string $url): void {
        Router::redirect($url);
    }
    
    protected function withSuccess(string $message): void {
        Session::flash('success', $message);
    }
    
    protected function withError(string $message): void {
        Session::flash('error', $message);
    }
    
    protected function validate(array $rules): ?array {
        $validator = new \App\Helpers\Validator($_POST);
        foreach ($rules as $field => $ruleSet) {
            $label = $ruleSet['label'] ?? $field;
            if (in_array('required', $ruleSet)) $validator->required($field, $label);
            if (in_array('email', $ruleSet)) $validator->email($field, $label);
            if (isset($ruleSet['min'])) $validator->minLength($field, $ruleSet['min'], $label);
            if (isset($ruleSet['max'])) $validator->maxLength($field, $ruleSet['max'], $label);
        }
        return $validator->fails() ? $validator->errors() : null;
    }
    
    protected function auth(): void {
        AuthMiddleware::requireAuth();
    }
    
    protected function requireRole(string ...$roles): void {
        AuthMiddleware::requireRole(...$roles);
    }

    protected function requirePermission(string $permission): void {
        AuthMiddleware::requirePermission($permission);
    }

    protected function requireMinLevel(int $level): void {
        AuthMiddleware::requireMinLevel($level);
    }

    protected function requireStaff(): void {
        AuthMiddleware::requireStaff();
    }
    
    protected function getUser(): ?array {
        $userId = Session::getUserId();
        if (!$userId) return null;
        $stmt = \App\Helpers\Database::getConnection()->prepare(
            "SELECT u.*, GROUP_CONCAT(r.name) as roles FROM users u 
             LEFT JOIN user_roles ur ON u.id = ur.user_id 
             LEFT JOIN roles r ON ur.role_id = r.id 
             WHERE u.id = ? GROUP BY u.id"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get report by ID with all joins, or redirect if not found.
     */
    protected function getReportOrRedirect(int $id, string $redirect = '/reports'): array {
        $db = \App\Helpers\Database::getConnection();
        $stmt = $db->prepare("SELECT r.*, c.name as category_name, c.icon as category_icon, c.color as category_color, c.deadline_days,
            d.name as daira_name, com.name as commune_name, o.name as org_name, o.phone as org_phone, o.email as org_email,
            u.first_name as assigned_first_name, u.last_name as assigned_last_name, u.email as assigned_email,
            cb.first_name as citizen_first_name, cb.last_name as citizen_last_name
            FROM reports r
            JOIN categories c ON r.category_id = c.id
            JOIN dairas d ON r.daira_id = d.id
            JOIN communes com ON r.commune_id = com.id
            LEFT JOIN organizations o ON r.organization_id = o.id
            LEFT JOIN users u ON r.assigned_to = u.id
            LEFT JOIN users cb ON r.citizen_id = cb.id
            WHERE r.id = ? AND r.deleted_at IS NULL");
        $stmt->execute([$id]);
        $report = $stmt->fetch();

        if (!$report) {
            $this->withError('Signalement non trouvé.');
            $this->redirect($redirect);
        }

        // RBAC check
        if (!Rbac::canViewReport($report)) {
            $this->withError('Accès non autorisé à ce signalement.');
            $this->redirect($redirect);
        }

        return $report;
    }

    /**
     * Check CSRF and redirect if invalid.
     */
    protected function checkCsrf(string $redirectUrl): void {
        if (!Csrf::verify($_POST['_token'] ?? '')) {
            $this->withError('Token de sécurité invalide.');
            $this->redirect($redirectUrl);
        }
    }

    /**
     * Log to audit and return the log entry ID.
     */
    protected function audit(string $action, string $model, ?int $modelId = null, ?array $old = null, ?array $new = null): void {
        \App\Helpers\AuditLog::log($action, $model, $modelId, $old, $new);
    }
}
