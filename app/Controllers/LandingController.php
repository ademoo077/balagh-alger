<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Controllers\Controller;

class LandingController extends Controller {

    public function landing(): void {
        if (Session::isAuthenticated()) {
            $this->redirect('/dashboard');
        }

        $db = Database::getConnection();

        $stats = [
            'total' => (int) $db->query("SELECT COUNT(*) FROM reports WHERE deleted_at IS NULL")->fetchColumn(),
            'resolved' => (int) $db->query("SELECT COUNT(*) FROM reports WHERE status IN ('resolved','validated','closed') AND deleted_at IS NULL")->fetchColumn(),
            'dairas' => (int) $db->query("SELECT COUNT(*) FROM dairas WHERE is_active = 1")->fetchColumn(),
            'organizations' => (int) $db->query("SELECT COUNT(*) FROM organizations WHERE is_active = 1")->fetchColumn(),
        ];

        $categories = $db->query("SELECT name, icon, color, slug FROM categories WHERE is_active = 1 AND slug IS NOT NULL ORDER BY sort_order LIMIT 10")->fetchAll();

        $dairas = $db->query("
            SELECT d.id, d.name,
                (SELECT COUNT(*) FROM reports r WHERE r.daira_id = d.id AND r.deleted_at IS NULL) as report_count
            FROM dairas d
            WHERE d.is_active = 1
            ORDER BY d.name
        ")->fetchAll();

        $statsLive = [
            'today' => (int) $db->query("SELECT COUNT(*) FROM reports WHERE DATE(created_at) = CURDATE() AND deleted_at IS NULL")->fetchColumn(),
            'in_progress' => (int) $db->query("SELECT COUNT(*) FROM reports WHERE status IN ('pending_review','in_progress','assigned') AND deleted_at IS NULL")->fetchColumn(),
            'resolved_month' => (int) $db->query("SELECT COUNT(*) FROM reports WHERE status IN ('resolved','validated','closed') AND MONTH(resolved_at) = MONTH(CURDATE()) AND YEAR(resolved_at) = YEAR(CURDATE()) AND deleted_at IS NULL")->fetchColumn(),
        ];

        // Landing page dynamic content
        $landingPartners = $db->query("SELECT * FROM landing_partners WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
        $landingGallery = $db->query("SELECT * FROM landing_gallery WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
        $landingTestimonials = $db->query("SELECT * FROM landing_testimonials WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
        $landingBeforeAfter = $db->query("SELECT * FROM landing_before_after WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
        $landingFaq = $db->query("SELECT * FROM landing_faq WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();

        // Settings (hero image, social links)
        $rows = $db->query("SELECT key_name, value FROM settings WHERE group_name = 'landing' AND is_public = 1")->fetchAll();
        $landingSettings = [];
        foreach ($rows as $r) $landingSettings[$r['key_name']] = $r['value'];

        $this->viewRaw('landing/index', compact('stats', 'categories', 'dairas', 'statsLive', 'landingPartners', 'landingGallery', 'landingTestimonials', 'landingBeforeAfter', 'landingFaq', 'landingSettings'));
    }

    public function statsApi(): void {
        $db = Database::getConnection();
        $statsLive = [
            'today' => (int) $db->query("SELECT COUNT(*) FROM reports WHERE DATE(created_at) = CURDATE() AND deleted_at IS NULL")->fetchColumn(),
            'in_progress' => (int) $db->query("SELECT COUNT(*) FROM reports WHERE status IN ('pending_review','in_progress','assigned') AND deleted_at IS NULL")->fetchColumn(),
            'resolved_month' => (int) $db->query("SELECT COUNT(*) FROM reports WHERE status IN ('resolved','validated','closed') AND MONTH(resolved_at) = MONTH(CURDATE()) AND YEAR(resolved_at) = YEAR(CURDATE()) AND deleted_at IS NULL")->fetchColumn(),
        ];
        header('Content-Type: application/json');
        echo json_encode($statsLive);
        exit;
    }
}
