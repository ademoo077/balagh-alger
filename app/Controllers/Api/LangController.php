<?php
namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Helpers\I18n;

class LangController extends Controller {
    public function set(): void {
        $lang = $_POST['lang'] ?? '';
        if (!$lang) {
            $data = json_decode(file_get_contents('php://input'), true);
            $lang = $data['lang'] ?? '';
        }
        if (in_array($lang, ['fr', 'ar'])) {
            I18n::setLang($lang);
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'lang' => $lang]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid language']);
        }
    }
}
