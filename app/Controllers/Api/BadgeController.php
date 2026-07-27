<?php
namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Helpers\Badge;
use App\Helpers\Session;

class BadgeController extends Controller {
    public function myBadges(): void {
        $this->auth();
        $userId = Session::getUserId();
        $this->json([
            'badges' => Badge::getUserBadges($userId),
            'definitions' => Badge::getDefinitions(),
            'stats' => Badge::getUserStats($userId)
        ]);
    }
}
