<?php
namespace App\Controllers\Api;

use App\Helpers\Database;
use App\Controllers\Controller;

class ReportController extends Controller {
    public function map(): void {
        $reports = Database::getConnection()->query("SELECT r.id, r.tracking_code, r.title, r.status, r.priority, r.latitude, r.longitude, c.name as category_name, c.icon as category_icon, c.color as category_color FROM reports r JOIN categories c ON r.category_id = c.id WHERE r.latitude IS NOT NULL AND r.longitude IS NOT NULL AND r.deleted_at IS NULL ORDER BY r.created_at DESC LIMIT 500")->fetchAll();
        $this->json($reports);
    }

    public function checkDuplicate(): void {
        $categoryId = $_GET['category_id'] ?? null;
        $subcategoryId = $_GET['subcategory_id'] ?? null;
        $dairaId = $_GET['daira_id'] ?? null;

        if (!$categoryId) {
            $this->json(['duplicates' => [], 'count' => 0]);
            return;
        }

        $db = Database::getConnection();
        $sql = "SELECT r.id, r.tracking_code, r.title, r.status, r.created_at, r.category_id, r.daira_id, c.name as category_name, d.name as daira_name FROM reports r JOIN categories c ON r.category_id = c.id JOIN dairas d ON r.daira_id = d.id WHERE r.category_id = ? AND r.deleted_at IS NULL AND r.status NOT IN ('resolved','closed','rejected')";
        $params = [$categoryId];

        if ($subcategoryId) {
            $sql .= " AND r.subcategory_id = ?";
            $params[] = $subcategoryId;
        }

        if ($dairaId) {
            $sql .= " AND r.daira_id = ?";
            $params[] = $dairaId;
        }

        $sql .= " ORDER BY r.created_at DESC LIMIT 5";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $duplicates = $stmt->fetchAll();

        $this->json(['duplicates' => $duplicates, 'count' => count($duplicates)]);
    }

    public function search(): void {
        $q = $_GET['q'] ?? '';
        if (strlen($q) < 2) { $this->json([]); return; }

        $db = Database::getConnection();
        $like = "%{$q}%";

        $reports = $db->prepare("SELECT r.id, r.tracking_code, r.title, r.status, r.priority, c.name as category_name, 'report' as result_type FROM reports r JOIN categories c ON r.category_id = c.id WHERE r.deleted_at IS NULL AND (r.title LIKE ? OR r.tracking_code LIKE ? OR r.description LIKE ?) ORDER BY r.created_at DESC LIMIT 10");
        $reports->execute([$like, $like, $like]);

        $users = $db->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as title, email as category_name, 'user' as result_type FROM users WHERE deleted_at IS NULL AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?) LIMIT 10");
        $users->execute([$like, $like, $like]);

        $results = array_merge($reports->fetchAll(), $users->fetchAll());
        $this->json($results);
    }

    public function similar(): void {
        $q = $_GET['q'] ?? '';
        $catId = $_GET['category_id'] ?? null;
        if (strlen($q) < 3 && !$catId) { $this->json([]); return; }
        $db = Database::getConnection();

        $sql = "SELECT r.id, r.tracking_code, r.title, r.status, r.description, r.created_at, c.name as category_name, c.color as category_color, d.name as daira_name,
                MATCH(r.title, r.description) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
                FROM reports r
                JOIN categories c ON r.category_id = c.id
                JOIN dairas d ON r.daira_id = d.id
                WHERE r.deleted_at IS NULL";
        $params = [$q];

        if ($catId) {
            $sql .= " AND r.category_id = ?";
            $params[] = $catId;
        }

        if (strlen($q) >= 3) {
            $sql .= " AND (MATCH(r.title, r.description) AGAINST(? IN NATURAL LANGUAGE MODE) OR r.title LIKE ? OR r.description LIKE ?)";
            $params[] = $q;
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
            $sql .= " HAVING relevance > 0";
        }

        $sql .= " ORDER BY created_at DESC LIMIT 5";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $this->json(['results' => $stmt->fetchAll()]);
    }
}
