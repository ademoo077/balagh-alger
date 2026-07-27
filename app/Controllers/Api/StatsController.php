<?php
namespace App\Controllers\Api;

use App\Helpers\Database;
use App\Controllers\Controller;

class StatsController extends Controller {
    public function index(): void {
        $db = Database::getConnection();
        $this->json([
            'total_reports' => (int)$db->query("SELECT COUNT(*) FROM reports WHERE deleted_at IS NULL")->fetchColumn(),
            'submitted' => (int)$db->query("SELECT COUNT(*) FROM reports WHERE status='submitted' AND deleted_at IS NULL")->fetchColumn(),
            'in_progress' => (int)$db->query("SELECT COUNT(*) FROM reports WHERE status='in_progress' AND deleted_at IS NULL")->fetchColumn(),
            'resolved' => (int)$db->query("SELECT COUNT(*) FROM reports WHERE status='resolved' AND deleted_at IS NULL")->fetchColumn(),
            'urgent' => (int)$db->query("SELECT COUNT(*) FROM reports WHERE priority='urgent' AND status NOT IN ('resolved','closed') AND deleted_at IS NULL")->fetchColumn(),
            'today' => (int)$db->query("SELECT COUNT(*) FROM reports WHERE DATE(created_at)=CURDATE() AND deleted_at IS NULL")->fetchColumn(),
            'total_users' => (int)$db->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL")->fetchColumn(),
            'total_organizations' => (int)$db->query("SELECT COUNT(*) FROM organizations WHERE is_active=1")->fetchColumn(),
        ]);
    }

    public function communeRanking(): void {
        $db = Database::getConnection();
        $ranking = $db->query("
            SELECT com.name as commune_name, d.name as daira_name,
                   COUNT(r.id) as total,
                   SUM(CASE WHEN r.status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                   ROUND(SUM(CASE WHEN r.status = 'resolved' THEN 1 ELSE 0 END) / COUNT(r.id) * 100, 1) as resolution_rate,
                   SUM(CASE WHEN r.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as month_count
            FROM reports r
            JOIN communes com ON r.commune_id = com.id
            JOIN dairas d ON r.daira_id = d.id
            WHERE r.deleted_at IS NULL
            GROUP BY com.id, com.name, d.name
            HAVING total > 0
            ORDER BY month_count DESC, resolution_rate DESC
            LIMIT 20
        ")->fetchAll();
        $this->json($ranking);
    }
}
