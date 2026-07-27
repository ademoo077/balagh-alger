<?php
namespace App\Controllers;

use App\Controllers\Controller;
use App\Helpers\Rbac;

class DairaController extends Controller {
    public function index(): void {
        $this->auth();
        $this->requirePermission('dairas.view');
        $dairas = \App\Helpers\Database::getConnection()->query("SELECT d.*, (SELECT COUNT(*) FROM communes WHERE daira_id = d.id) as communes_count FROM dairas d WHERE d.is_active = 1 ORDER BY d.name")->fetchAll();
        $this->view('dairas/index', compact('dairas'));
    }

    public function show(int $id): void {
        $this->auth();
        $this->requirePermission('dairas.view');
        $db = \App\Helpers\Database::getConnection();
        $daira = $db->prepare("SELECT * FROM dairas WHERE id = ?");
        $daira->execute([$id]);
        $daira = $daira->fetch();
        if (!$daira) { $this->withError('Daïra non trouvée.'); $this->redirect('/dairas'); }

        $communes = $db->prepare("SELECT * FROM communes WHERE daira_id = ? ORDER BY name");
        $communes->execute([$id]);
        $communes = $communes->fetchAll();

        $scope = Rbac::scopeReports();
        $where = "r.daira_id = ? AND r.deleted_at IS NULL" . $scope['where'];
        $params = array_merge([$id], $scope['params']);

        $reports = $db->prepare("SELECT r.*, c.name as category_name FROM reports r JOIN categories c ON r.category_id = c.id WHERE {$where} ORDER BY r.created_at DESC LIMIT 20");
        $reports->execute($params);
        $reports = $reports->fetchAll();

        $this->view('dairas/show', compact('daira', 'communes', 'reports'));
    }
}
