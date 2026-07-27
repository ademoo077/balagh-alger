<?php
namespace App\Controllers\Api;

use App\Helpers\Database;
use App\Controllers\Controller;

class DairaController extends Controller {
    public function communes(int $dairaId): void {
        $communes = Database::getConnection()->prepare("SELECT id, name FROM communes WHERE daira_id = ? AND is_active = 1 ORDER BY name");
        $communes->execute([$dairaId]);
        $this->json($communes->fetchAll());
    }
}
