<?php
namespace App\Controllers\Api;

use App\Helpers\Database;
use App\Controllers\Controller;

class V1ReportController extends Controller {
    public function index(): void {
        $db = Database::getConnection();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $reports = $db->query("SELECT r.id, r.tracking_code, r.title, r.description, r.priority, r.status, r.created_at, c.name as category, d.name as daira, com.name as commune FROM reports r JOIN categories c ON r.category_id = c.id JOIN dairas d ON r.daira_id = d.id JOIN communes com ON r.commune_id = com.id WHERE r.deleted_at IS NULL ORDER BY r.created_at DESC LIMIT {$perPage} OFFSET {$offset}")->fetchAll();

        $this->json(['data' => $reports, 'page' => $page, 'per_page' => $perPage]);
    }

    public function show(int $id): void {
        $db = Database::getConnection();
        $report = $db->prepare("SELECT r.*, c.name as category_name, d.name as daira_name, com.name as commune_name FROM reports r JOIN categories c ON r.category_id = c.id JOIN dairas d ON r.daira_id = d.id JOIN communes com ON r.commune_id = com.id WHERE r.id = ?");
        $report->execute([$id]);
        $report = $report->fetch();

        if (!$report) { $this->json(['error' => 'Not found'], 404); return; }
        $this->json($report);
    }

    public function store(): void {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) { $this->json(['error' => 'Invalid data'], 400); return; }

        $db = Database::getConnection();
        $code = 'BA-' . date('Y') . '-' . str_pad((string)random_int(1, 999999), 6, '0', STR_PAD_LEFT);

        $stmt = $db->prepare("INSERT INTO reports (tracking_code, title, description, category_id, priority, status, daira_id, commune_id, address, latitude, longitude, citizen_name, citizen_phone) VALUES (?, ?, ?, ?, ?, 'submitted', ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$code, $data['title'] ?? '', $data['description'] ?? '', $data['category_id'] ?? 1, $data['priority'] ?? 'medium', $data['daira_id'] ?? 1, $data['commune_id'] ?? 1, $data['address'] ?? '', $data['latitude'] ?? null, $data['longitude'] ?? null, $data['citizen_name'] ?? 'Anonyme', $data['citizen_phone'] ?? null]);

        $this->json(['id' => $db->lastInsertId(), 'tracking_code' => $code, 'message' => 'Report created'], 201);
    }
}
