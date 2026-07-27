<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Rbac;
use App\Controllers\Controller;

class DashboardController extends Controller {

    public function index(): void {
        $this->auth();
        $db = Database::getConnection();
        $userId = Session::getUserId();
        $primaryRole = Rbac::getPrimaryRole();

        switch ($primaryRole) {
            case 'citizen':
                $this->redirect('/home');
                return;
            case 'intervenant':
                $this->agentDashboard($db, $userId);
                break;
            case 'chef_section':
                $this->sectionDashboard($db, $userId);
                break;
            case 'chef_unite':
                $this->dairaDashboard($db, $userId);
                break;
            case 'resp_central':
            case 'admin_local':
                $this->orgDashboard($db, $userId);
                break;
            case 'admin_central':
            default:
                $this->adminCentralDashboard($db, $userId);
                break;
        }
    }

    private function citizenDashboard($db, int $userId): void {
        $total = $this->count($db, "r.deleted_at IS NULL AND r.citizen_id = ?", [$userId]);
        $submitted = $this->count($db, "r.deleted_at IS NULL AND r.citizen_id = ? AND r.status = 'submitted'", [$userId]);
        $inProgress = $this->count($db, "r.deleted_at IS NULL AND r.citizen_id = ? AND r.status = 'in_progress'", [$userId]);
        $resolved = $this->count($db, "r.deleted_at IS NULL AND r.citizen_id = ? AND r.status IN ('resolved','validated')", [$userId]);
        $pending = $this->count($db, "r.deleted_at IS NULL AND r.citizen_id = ? AND r.status IN ('submitted','acknowledged','assigned')", [$userId]);
        $closed = $this->count($db, "r.deleted_at IS NULL AND r.citizen_id = ? AND r.status = 'closed'", [$userId]);

        $myReports = $db->prepare("SELECT r.*, c.name as category_name, c.icon as category_icon, c.color as category_color, c.deadline_days,
            d.name as daira_name, com.name as commune_name, o.name as org_name,
            u.first_name as assigned_first_name, u.last_name as assigned_last_name
            FROM reports r
            JOIN categories c ON r.category_id = c.id
            JOIN dairas d ON r.daira_id = d.id
            JOIN communes com ON r.commune_id = com.id
            LEFT JOIN organizations o ON r.organization_id = o.id
            LEFT JOIN users u ON r.assigned_to = u.id
            WHERE r.deleted_at IS NULL AND r.citizen_id = ?
            ORDER BY r.created_at DESC");
        $myReports->execute([$userId]);
        $myReports = $myReports->fetchAll();

        $recentHistory = $db->prepare("SELECT h.*, r.tracking_code, r.title as report_title
            FROM report_history h
            JOIN reports r ON h.report_id = r.id
            WHERE r.citizen_id = ? AND r.deleted_at IS NULL
            ORDER BY h.created_at DESC LIMIT 10");
        $recentHistory->execute([$userId]);
        $recentHistory = $recentHistory->fetchAll();

        $avgRating = $db->prepare("SELECT ROUND(AVG(rr.rating), 1) FROM report_ratings rr JOIN reports r ON rr.report_id = r.id WHERE r.citizen_id = ?");
        $avgRating->execute([$userId]);
        $avgRating = $avgRating->fetchColumn() ?: 0;

        $byMonth = $db->prepare("SELECT DATE_FORMAT(r.created_at, '%Y-%m') as month, COUNT(*) as count
            FROM reports r WHERE r.citizen_id = ? AND r.deleted_at IS NULL
            GROUP BY month ORDER BY month ASC LIMIT 12");
        $byMonth->execute([$userId]);
        $byMonth = $byMonth->fetchAll();

        $byCategory = $db->prepare("SELECT c.name, c.color, COUNT(r.id) as count
            FROM reports r JOIN categories c ON r.category_id = c.id
            WHERE r.citizen_id = ? AND r.deleted_at IS NULL
            GROUP BY c.id ORDER BY count DESC");
        $byCategory->execute([$userId]);
        $byCategory = $byCategory->fetchAll();

        $bySubcategory = $db->prepare("SELECT sc.name as subcat_name, c.name as cat_name, c.color, COUNT(r.id) as count
            FROM reports r JOIN subcategories sc ON r.subcategory_id = sc.id JOIN categories c ON sc.category_id = c.id
            WHERE r.citizen_id = ? AND r.deleted_at IS NULL
            GROUP BY sc.id ORDER BY count DESC LIMIT 20");
        $bySubcategory->execute([$userId]);
        $bySubcategory = $bySubcategory->fetchAll();

        $this->view('dashboard/citizen', compact(
            'total', 'submitted', 'inProgress', 'resolved', 'pending', 'closed',
            'myReports', 'recentHistory', 'avgRating', 'byMonth', 'byCategory', 'bySubcategory'
        ));
    }

    private function getPeriodSql(): array {
        $period = $_GET['period'] ?? '';
        $periodSql = '';
        $periodLabel = 'Tout';
        if ($period === 'today') { $periodSql = " AND DATE(r.created_at) = CURDATE()"; $periodLabel = "Aujourd'hui"; }
        elseif ($period === 'week') { $periodSql = " AND r.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"; $periodLabel = '7 derniers jours'; }
        elseif ($period === 'month') { $periodSql = " AND MONTH(r.created_at) = MONTH(CURDATE()) AND YEAR(r.created_at) = YEAR(CURDATE())"; $periodLabel = 'Ce mois'; }
        elseif ($period === 'quarter') { $periodSql = " AND r.created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)"; $periodLabel = '3 derniers mois'; }
        elseif ($period === 'year') { $periodSql = " AND YEAR(r.created_at) = YEAR(CURDATE())"; $periodLabel = "Cette année"; }
        return [$period, $periodSql, $periodLabel];
    }

    private function getChartQueries($db, string $where, array $params) {
        $byCategory = $db->prepare("SELECT c.name, c.color, COUNT(r.id) as count FROM reports r JOIN categories c ON r.category_id = c.id WHERE {$where} GROUP BY c.id ORDER BY count DESC");
        $byCategory->execute($params);
        $byCategory = $byCategory->fetchAll();

        $bySubcategory = $db->prepare("SELECT sc.name as subcat_name, c.name as cat_name, c.color, COUNT(r.id) as count FROM reports r JOIN subcategories sc ON r.subcategory_id = sc.id JOIN categories c ON sc.category_id = c.id WHERE {$where} GROUP BY sc.id ORDER BY count DESC LIMIT 20");
        $bySubcategory->execute($params);
        $bySubcategory = $bySubcategory->fetchAll();

        $byPriority = $db->prepare("SELECT r.priority, COUNT(r.id) as count FROM reports r WHERE {$where} GROUP BY r.priority");
        $byPriority->execute($params);
        $byPriority = $byPriority->fetchAll();

        $byStatus = $db->prepare("SELECT r.status, COUNT(r.id) as count FROM reports r WHERE {$where} GROUP BY r.status");
        $byStatus->execute($params);
        $byStatus = $byStatus->fetchAll();

        $recentReports = $db->prepare("SELECT r.*, c.name as category_name, c.deadline_days, d.name as daira_name, com.name as commune_name, o.name as org_name FROM reports r JOIN categories c ON r.category_id = c.id JOIN dairas d ON r.daira_id = d.id JOIN communes com ON r.commune_id = com.id LEFT JOIN organizations o ON r.organization_id = o.id WHERE {$where} ORDER BY r.created_at DESC LIMIT 10");
        $recentReports->execute($params);
        $recentReports = $recentReports->fetchAll();

        $mapData = $db->prepare("SELECT r.id, r.tracking_code, r.title, r.status, r.priority, r.latitude, r.longitude, c.name as category_name FROM reports r JOIN categories c ON r.category_id = c.id WHERE r.latitude IS NOT NULL AND r.longitude IS NOT NULL AND {$where} ORDER BY r.created_at DESC LIMIT 100");
        $mapData->execute($params);
        $mapData = $mapData->fetchAll();

        $byMonth = $db->prepare("SELECT MONTH(r.created_at) as month, COUNT(r.id) as count FROM reports r WHERE {$where} GROUP BY MONTH(r.created_at) ORDER BY month ASC");
        $byMonth->execute($params);
        $byMonth = $byMonth->fetchAll();

        return compact('byCategory', 'bySubcategory', 'byPriority', 'byStatus', 'recentReports', 'mapData', 'byMonth');
    }

    private function getKPIs($db, string $where, array $params, string $periodSql): array {
        $total = $this->count($db, $where . $periodSql, $params);
        $submitted = $this->count($db, $where . $periodSql, $params, " AND r.status = 'submitted'");
        $inProgress = $this->count($db, $where . $periodSql, $params, " AND r.status = 'in_progress'");
        $resolved = $this->count($db, $where . $periodSql, $params, " AND r.status IN ('resolved','validated')");
        $urgent = $this->count($db, $where . $periodSql, $params, " AND r.priority = 'urgent' AND r.status NOT IN ('resolved','closed')");
        $today = $this->count($db, $where, $params, " AND DATE(r.created_at) = CURDATE()");
        $pending = $this->count($db, $where . $periodSql, $params, " AND r.status IN ('submitted','acknowledged')");
        $closed = $this->count($db, $where . $periodSql, $params, " AND r.status = 'closed'");
        $pendingReview = $this->count($db, $where . $periodSql, $params, " AND r.status = 'pending_review'");
        $pendingUnite = $this->count($db, $where, $params, " AND r.status = 'pending_unite'");
        $overdue = $this->count($db, $where, $params, " AND r.deadline_at IS NOT NULL AND r.deadline_at < NOW() AND r.status NOT IN ('resolved','closed','rejected')");
        return compact('total', 'submitted', 'inProgress', 'resolved', 'urgent', 'today', 'pending', 'closed', 'pendingReview', 'pendingUnite', 'overdue');
    }

    private function adminCentralDashboard($db, int $userId): void {
        [$period, $periodSql, $periodLabel] = $this->getPeriodSql();
        $scope = Rbac::scopeReports();
        $where = "r.deleted_at IS NULL" . $scope['where'];
        $params = $scope['params'];

        $k = $this->getKPIs($db, $where, $params, $periodSql);
        $charts = $this->getChartQueries($db, $where, $params);

        $byDaira = $db->prepare("SELECT d.name, COUNT(r.id) as count FROM reports r JOIN dairas d ON r.daira_id = d.id WHERE {$where} GROUP BY d.id ORDER BY count DESC");
        $byDaira->execute($params);
        $byDaira = $byDaira->fetchAll();

        $userStats = $db->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL")->fetchColumn();
        $orgStats = $db->query("SELECT COUNT(*) FROM organizations WHERE is_active = 1")->fetchColumn();

        $this->view('dashboard/index', array_merge($k, $charts, compact(
            'byDaira', 'orgStats', 'userStats', 'period', 'periodLabel'
        ), ['primaryRole' => 'admin_central', 'canViewStats' => true, 'canViewAudit' => true]));
    }

    private function orgDashboard($db, int $userId): void {
        [$period, $periodSql, $periodLabel] = $this->getPeriodSql();
        $scope = Rbac::scopeReports();
        $where = "r.deleted_at IS NULL" . $scope['where'];
        $params = $scope['params'];

        $k = $this->getKPIs($db, $where, $params, $periodSql);
        $charts = $this->getChartQueries($db, $where, $params);

        $byDaira = $db->prepare("SELECT d.name, COUNT(r.id) as count FROM reports r JOIN dairas d ON r.daira_id = d.id WHERE {$where} GROUP BY d.id ORDER BY count DESC");
        $byDaira->execute($params);
        $byDaira = $byDaira->fetchAll();

        $userScope = Rbac::scopeUsers();
        $uWhere = "u.deleted_at IS NULL" . $userScope['where'];
        $uParams = $userScope['params'];
        $userStmt = $db->prepare("SELECT COUNT(*) FROM users u WHERE {$uWhere}");
        $userStmt->execute($uParams);
        $userStats = $userStmt->fetchColumn();
        $orgStats = null;

        $this->view('dashboard/index', array_merge($k, $charts, compact(
            'byDaira', 'orgStats', 'userStats', 'period', 'periodLabel'
        ), ['primaryRole' => Rbac::getPrimaryRole(), 'canViewStats' => Rbac::canViewStats(), 'canViewAudit' => Rbac::canViewAudit()]));
    }

    private function dairaDashboard($db, int $userId): void {
        [$period, $periodSql, $periodLabel] = $this->getPeriodSql();
        $scope = Rbac::scopeReports();
        $where = "r.deleted_at IS NULL" . $scope['where'];
        $params = $scope['params'];
        $dairaId = Session::get('daira_id');

        $k = $this->getKPIs($db, $where, $params, $periodSql);
        $charts = $this->getChartQueries($db, $where, $params);

        $byCommune = $db->prepare("SELECT com.name, COUNT(r.id) as count FROM reports r JOIN communes com ON r.commune_id = com.id WHERE {$where} GROUP BY com.id ORDER BY count DESC");
        $byCommune->execute($params);
        $byCommune = $byCommune->fetchAll();

        $byDaira = [];

        $userScope = Rbac::scopeUsers();
        $uWhere = "u.deleted_at IS NULL" . $userScope['where'];
        $uParams = $userScope['params'];
        $userStmt = $db->prepare("SELECT COUNT(*) FROM users u WHERE {$uWhere}");
        $userStmt->execute($uParams);
        $userStats = $userStmt->fetchColumn();
        $orgStats = null;

        $this->view('dashboard/index', array_merge($k, $charts, compact(
            'byDaira', 'byCommune', 'orgStats', 'userStats', 'period', 'periodLabel'
        ), ['primaryRole' => 'chef_unite', 'canViewStats' => Rbac::canViewStats(), 'canViewAudit' => Rbac::canViewAudit()]));
    }

    private function sectionDashboard($db, int $userId): void {
        [$period, $periodSql, $periodLabel] = $this->getPeriodSql();
        $scope = Rbac::scopeReports();
        $where = "r.deleted_at IS NULL" . $scope['where'];
        $params = $scope['params'];

        $k = $this->getKPIs($db, $where, $params, $periodSql);
        $charts = $this->getChartQueries($db, $where, $params);

        $assignedCommunes = $db->prepare("SELECT sc.commune_id, com.name as commune_name FROM section_communes sc JOIN communes com ON sc.commune_id = com.id WHERE sc.user_id = ? AND sc.organization_id = ?");
        $assignedCommunes->execute([$userId, Session::get('organization_id')]);
        $assignedCommunes = $assignedCommunes->fetchAll();

        $byCommune = $db->prepare("SELECT com.name, COUNT(r.id) as count FROM reports r JOIN communes com ON r.commune_id = com.id WHERE {$where} GROUP BY com.id ORDER BY count DESC");
        $byCommune->execute($params);
        $byCommune = $byCommune->fetchAll();

        $communeIds = array_column($assignedCommunes, 'commune_id');
        $communeStats = [];
        if (!empty($communeIds)) {
            $placeholders = implode(',', array_fill(0, count($communeIds), '?'));
            $csSql = "SELECT com.id as commune_id, com.name as commune_name,
                COUNT(r.id) as total,
                SUM(CASE WHEN r.status IN ('submitted','acknowledged') THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN r.status IN ('assigned','in_progress') THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN r.status IN ('resolved','validated','closed') THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN r.is_late = 1 THEN 1 ELSE 0 END) as overdue,
                SUM(CASE WHEN r.priority = 'urgent' THEN 1 ELSE 0 END) as urgent
                FROM communes com
                LEFT JOIN reports r ON r.commune_id = com.id AND r.deleted_at IS NULL
                WHERE com.id IN ({$placeholders})
                GROUP BY com.id, com.name
                ORDER BY total DESC";
            $csStmt = $db->prepare($csSql);
            $csStmt->execute($communeIds);
            $communeStats = $csStmt->fetchAll();
        }

        $byDaira = [];

        $userScope = Rbac::scopeUsers();
        $uWhere = "u.deleted_at IS NULL" . $userScope['where'];
        $uParams = $userScope['params'];
        $userStmt = $db->prepare("SELECT COUNT(*) FROM users u WHERE {$uWhere}");
        $userStmt->execute($uParams);
        $userStats = $userStmt->fetchColumn();
        $orgStats = null;

        $this->view('dashboard/index', array_merge($k, $charts, compact(
            'byDaira', 'byCommune', 'assignedCommunes', 'communeStats', 'orgStats', 'userStats', 'period', 'periodLabel'
        ), ['primaryRole' => 'chef_section', 'canViewStats' => Rbac::canViewStats(), 'canViewAudit' => Rbac::canViewAudit()]));
    }

    private function agentDashboard($db, int $userId): void {
        $stmt = $db->prepare("SELECT r.*, c.name as category_name, c.icon as category_icon, c.color as category_color, c.deadline_days,
            d.name as daira_name, com.name as commune_name,
            (SELECT ip.filename FROM intervention_photos ip WHERE ip.report_id = r.id AND ip.photo_type = 'before' ORDER BY ip.created_at ASC LIMIT 1) as before_photo,
            (SELECT ip.filename FROM intervention_photos ip WHERE ip.report_id = r.id AND ip.photo_type = 'after' ORDER BY ip.created_at DESC LIMIT 1) as after_photo
            FROM reports r
            JOIN categories c ON r.category_id = c.id
            JOIN dairas d ON r.daira_id = d.id
            JOIN communes com ON r.commune_id = com.id
            WHERE r.assigned_to = ? AND r.deleted_at IS NULL
            ORDER BY r.created_at DESC");
        $stmt->execute([$userId]);
        $missions = $stmt->fetchAll();

        $total = count($missions);
        $inProgress = 0; $completed = 0; $pendingReview = 0;
        foreach ($missions as $m) {
            if ($m['status'] === 'in_progress') $inProgress++;
            elseif (in_array($m['status'], ['resolved', 'validated', 'closed'])) $completed++;
            elseif ($m['status'] === 'pending_review') $pendingReview++;
        }

        $this->view('dashboard/agent', compact('missions', 'total', 'inProgress', 'completed', 'pendingReview'));
    }

    private function count($db, string $where, array $params, string $extra = ''): int {
        $stmt = $db->prepare("SELECT COUNT(*) FROM reports r WHERE {$where}{$extra}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function impact(): void {
        $this->auth();
        if (Rbac::isRole('citizen')) {
            $this->layout = 'layouts/citizen';
        }
        $userId = Session::getUserId();
        $db = Database::getConnection();

        $stats = \App\Helpers\Badge::getUserStats($userId);
        $badges = \App\Helpers\Badge::getUserBadges($userId);
        $badgeDefs = \App\Helpers\Badge::getDefinitions();

        $stmt = $db->prepare("
            SELECT r.id, r.tracking_code, r.title, r.status, r.created_at, r.resolved_at, c.name as category_name, c.color as category_color
            FROM reports r JOIN categories c ON r.category_id = c.id
            WHERE r.citizen_id = ? AND r.deleted_at IS NULL
            ORDER BY r.created_at DESC LIMIT 10
        ");
        $stmt->execute([$userId]);
        $recent = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT c.name as category_name, c.color as category_color, COUNT(*) as count
            FROM reports r JOIN categories c ON r.category_id = c.id
            WHERE r.citizen_id = ? AND r.deleted_at IS NULL
            GROUP BY c.id, c.name, c.color ORDER BY count DESC LIMIT 8
        ");
        $stmt->execute([$userId]);
        $byCategory = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT MONTH(r.created_at) as month, COUNT(*) as count
            FROM reports r WHERE r.citizen_id = ? AND r.deleted_at IS NULL AND r.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY MONTH(r.created_at) ORDER BY month ASC
        ");
        $stmt->execute([$userId]);
        $byMonth = $stmt->fetchAll();

        $this->view('dashboard/impact', compact('stats', 'badges', 'badgeDefs', 'recent', 'byCategory', 'byMonth'));
    }
}
