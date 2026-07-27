<?php
namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Helpers\Database;
use App\Helpers\Rbac;

class HeatmapController extends Controller {
    public function index(): void {
        $this->auth();
        $this->requirePermission('dashboard.view');
        $db = Database::getConnection();
        $scope = Rbac::scopeReports();
        
        $where = $scope['where'] ?: '1=1';
        $stmt = $db->prepare("SELECT latitude, longitude, priority, status 
            FROM reports 
            WHERE {$where} AND latitude IS NOT NULL AND longitude IS NOT NULL AND deleted_at IS NULL");
        $stmt->execute($scope['params'] ?? []);
        
        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll());
    }
}
