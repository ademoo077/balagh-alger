<?php
namespace App\Controllers\Api;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Rbac;
use App\Controllers\Controller;

class NearbyController extends Controller {
    public function index(): void {
        $lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
        $lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;
        $radius = isset($_GET['radius']) ? max(100, min(10000, floatval($_GET['radius']))) : 1000;
        $categoryId = !empty($_GET['category_id']) ? intval($_GET['category_id']) : null;

        if ($lat === null || $lng === null) {
            $this->json(['error' => 'lat and lng are required'], 400);
            return;
        }

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            $this->json(['error' => 'Invalid coordinates'], 400);
            return;
        }

        $db = Database::getConnection();
        $userId = Session::getUserId();
        $isCitizen = $userId && Rbac::isRole('citizen');

        $sql = "SELECT id, title, category_id, status, latitude, longitude,
            (6371000 * ACOS(
                COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) +
                SIN(RADIANS(?)) * SIN(RADIANS(latitude))
            )) AS distance_meters
            FROM reports
            WHERE deleted_at IS NULL
              AND latitude IS NOT NULL AND longitude IS NOT NULL";

        $params = [$lat, $lng, $lat];

        if ($isCitizen) {
            $sql .= " AND citizen_id = ?";
            $params[] = $userId;
        }

        if ($categoryId !== null) {
            $sql .= " AND category_id = ?";
            $params[] = $categoryId;
        }

        $sql .= " HAVING distance_meters <= ?
            ORDER BY distance_meters ASC
            LIMIT 5";

        $params[] = $radius;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $reports = $stmt->fetchAll();

        $this->json(['reports' => $reports, 'count' => count($reports)]);
    }
}
