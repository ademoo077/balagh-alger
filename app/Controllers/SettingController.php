<?php
namespace App\Controllers;

use App\Controllers\Controller;

class SettingController extends Controller {
    public function index(): void {
        $this->auth();
        if (!\App\Helpers\Rbac::has('settings.manage')) { $this->withError('Accès non autorisé.'); $this->redirect('/dashboard'); return; }
        $settings = \App\Helpers\Database::getConnection()->query("SELECT * FROM settings ORDER BY `group_name`, `key_name`")->fetchAll();
        $csrfToken = \App\Helpers\Csrf::generate();
        $this->view('settings/index', compact('settings', 'csrfToken'));
    }

    public function update(): void {
        $this->auth();
        if (!\App\Helpers\Rbac::has('settings.manage')) { $this->withError('Accès non autorisé.'); $this->redirect('/dashboard'); return; }
        if (!\App\Helpers\Csrf::verify($_POST['_token'] ?? '')) { $this->withError('Token invalide.'); $this->redirect('/settings'); return; }

        $db = \App\Helpers\Database::getConnection();
        $allowedKeys = $db->query("SELECT key_name FROM settings")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($_POST as $key => $value) {
            if ($key === '_token') continue;
            if (!in_array($key, $allowedKeys, true)) continue;
            $db->prepare("UPDATE settings SET `value` = ? WHERE `key_name` = ?")->execute([$value, $key]);
        }

        \App\Helpers\AuditLog::log('update', 'Settings');
        $this->withSuccess('Paramètres mis à jour.');
        $this->redirect('/settings');
    }
}
