<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Helper;

class ShareController extends Controller {
    public function show(string $code): void {
        \App\Helpers\I18n::init();
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT r.*, c.name as category_name, c.icon as category_icon, c.color as category_color,
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
            http_response_code(404);
            $this->viewRaw('share/not_found', ['code' => $code]);
            return;
        }

        $images = $db->prepare("SELECT * FROM report_images WHERE report_id = ? ORDER BY is_primary DESC");
        $images->execute([$report['id']]);
        $images = $images->fetchAll();

        $history = $db->prepare("SELECT h.*, u.first_name, u.last_name FROM report_history h LEFT JOIN users u ON h.user_id = u.id WHERE h.report_id = ? ORDER BY h.created_at DESC");
        $history->execute([$report['id']]);
        $history = $history->fetchAll();

        $baseUrl = \App\Helpers\Router::baseUrl();

        $ogTitle = $report['title'] ?: 'Signalement ' . $report['tracking_code'];
        $ogDesc = $report['description'] ? mb_substr(strip_tags($report['description']), 0, 160) : 'Signalement public sur Balagh Alger — ' . ($report['category_name'] ?? '');
        $ogImage = '';
        if (!empty($images)) {
            $ogImage = $baseUrl . '/uploads/reports/' . $images[0]['filename'];
        }
        $ogUrl = $baseUrl . '/partager/' . $report['tracking_code'];

        $this->viewRaw('share/show', compact('report', 'images', 'history', 'baseUrl', 'ogTitle', 'ogDesc', 'ogImage', 'ogUrl'));
    }
}
