<?php
namespace App\Controllers;

use App\Controllers\Controller;

class AuditController extends Controller {
    public function index(): void {
        $this->auth();
        $this->requirePermission('audit.view');
        $db = \App\Helpers\Database::getConnection();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $where = ['1=1'];
        $params = [];

        if (!empty($_GET['user'])) {
            $where[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $like = '%' . $_GET['user'] . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($_GET['action'])) {
            $where[] = "al.action = ?";
            $params[] = $_GET['action'];
        }
        if (!empty($_GET['model'])) {
            $where[] = "al.model = ?";
            $params[] = $_GET['model'];
        }
        if (!empty($_GET['from'])) {
            $where[] = "al.created_at >= ?";
            $params[] = $_GET['from'] . ' 00:00:00';
        }
        if (!empty($_GET['to'])) {
            $where[] = "al.created_at <= ?";
            $params[] = $_GET['to'] . ' 23:59:59';
        }

        $whereSql = implode(' AND ', $where);
        $countStmt = $db->prepare("SELECT COUNT(*) FROM audit_logs al LEFT JOIN users u ON al.user_id = u.id WHERE {$whereSql}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $db->prepare("SELECT al.*, u.first_name, u.last_name, u.email FROM audit_logs al LEFT JOIN users u ON al.user_id = u.id WHERE {$whereSql} ORDER BY al.created_at DESC LIMIT {$perPage} OFFSET {$offset}");
        $stmt->execute($params);
        $logs = $stmt->fetchAll();

        $this->view('dashboard/audit', compact('logs', 'total', 'page', 'perPage'));
    }
}
