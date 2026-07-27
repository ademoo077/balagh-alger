<?php
namespace App\Controllers\Api;

use App\Helpers\Database;
use App\Controllers\Controller;

class CategoryController extends Controller {
    public function subcategories(int $categoryId): void {
        $stmt = Database::getConnection()->prepare("SELECT id, name FROM subcategories WHERE category_id = ? AND is_active = 1 ORDER BY sort_order, name");
        $stmt->execute([$categoryId]);
        $this->json($stmt->fetchAll());
    }
}
