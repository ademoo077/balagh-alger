<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\I18n;

class TrackingController extends Controller {
    public function index(): void {
        I18n::init();
        $code = $_GET['code'] ?? '';
        if (!empty($code)) {
            $this->redirect('/suivi/' . urlencode($code));
        }
        $this->viewRaw('tracking/index');
    }

    public function show(string $code): void {
        I18n::init();
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT r.*, c.name as category_name, c.icon as category_icon, c.color as category_color, c.deadline_days,
            d.name as daira_name, co.name as commune_name, o.name as org_name
            FROM reports r
            LEFT JOIN categories c ON r.category_id = c.id
            LEFT JOIN dairas d ON r.daira_id = d.id
            LEFT JOIN communes co ON r.commune_id = co.id
            LEFT JOIN organizations o ON r.organization_id = o.id
            WHERE r.tracking_code = ? AND r.deleted_at IS NULL");
        $stmt->execute([$code]);
        $report = $stmt->fetch();

        if (!$report) {
            $this->viewRaw('tracking/not_found', ['code' => $code]);
            return;
        }

        $hist = $db->prepare("SELECT * FROM audit_logs WHERE model = 'Report' AND model_id = ? ORDER BY created_at ASC");
        $hist->execute([$report['id']]);
        $history = $hist->fetchAll();

        $intQ = $db->prepare("SELECT ri.*, u.first_name, u.last_name FROM report_interventions ri LEFT JOIN users u ON ri.agent_id = u.id WHERE ri.report_id = ? ORDER BY ri.created_at ASC");
        $intQ->execute([$report['id']]);
        $interventions = $intQ->fetchAll();

        $catDays = (int) ($report['deadline_days'] ?? 7);

        $this->viewRaw('tracking/show', compact('report', 'history', 'interventions', 'catDays'));
    }
}
