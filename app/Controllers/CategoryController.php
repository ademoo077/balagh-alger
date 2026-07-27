<?php
namespace App\Controllers;

use App\Controllers\Controller;
use App\Helpers\Rbac;
use App\Helpers\Database;
use App\Helpers\Csrf;
use App\Helpers\Helper;
use App\Helpers\I18n;
use App\Helpers\Session;

class CategoryController extends Controller {

    public function index(): void {
        $this->auth();
        $this->requirePermission('categories.view');
        I18n::init();

        $db = Database::getConnection();
        $categories = $db->query(
            "SELECT c.*, 
                (SELECT COUNT(*) FROM reports r WHERE r.category_id = c.id AND r.deleted_at IS NULL) as report_count 
             FROM categories c 
             ORDER BY c.sort_order ASC, c.name ASC"
        )->fetchAll();

        $canManage = Rbac::has('categories.manage');
        $csrfToken = Csrf::generate();

        $this->view('categories/index', compact('categories', 'canManage', 'csrfToken'));
    }

    public function create(): void {
        $this->auth();
        $this->requirePermission('categories.manage');
        I18n::init();

        $csrfToken = Csrf::generate();
        $this->view('categories/create', compact('csrfToken'));
    }

    public function store(): void {
        $this->auth();
        $this->requirePermission('categories.manage');
        I18n::init();

        if (!Csrf::verify($_POST['_token'] ?? '')) {
            $this->withError('Token de sécurité invalide.');
            $this->redirect('/categories/create');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $this->withError('Le nom est obligatoire.');
            $this->redirect('/categories/create');
        }

        $db = Database::getConnection();
        $slug = trim($_POST['slug'] ?? '');
        if ($slug === '') {
            $slug = Helper::slugify($name);
        } else {
            $slug = Helper::slugify($slug);
        }

        $description = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fas fa-exclamation-triangle');
        $color = trim($_POST['color'] ?? '#3a7bd5');
        $deadlineDays = max(1, (int)($_POST['deadline_days'] ?? 7));
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder = max(0, (int)($_POST['sort_order'] ?? 0));

        $stmt = $db->prepare(
            "INSERT INTO categories (name, slug, description, icon, color, deadline_days, is_active, sort_order, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$name, $slug, $description, $icon, $color, $deadlineDays, $isActive, $sortOrder]);

        $this->audit('create', 'Category', (int)$db->lastInsertId());
        $this->withSuccess('Catégorie créée avec succès.');
        $this->redirect('/categories');
    }

    public function edit(int $id): void {
        $this->auth();
        $this->requirePermission('categories.manage');
        I18n::init();

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();

        if (!$category) {
            $this->withError('Catégorie non trouvée.');
            $this->redirect('/categories');
        }

        $csrfToken = Csrf::generate();
        $this->view('categories/edit', compact('category', 'csrfToken'));
    }

    public function update(int $id): void {
        $this->auth();
        $this->requirePermission('categories.manage');
        I18n::init();

        if (!Csrf::verify($_POST['_token'] ?? '')) {
            $this->withError('Token de sécurité invalide.');
            $this->redirect("/categories/{$id}/edit");
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();

        if (!$category) {
            $this->withError('Catégorie non trouvée.');
            $this->redirect('/categories');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $this->withError('Le nom est obligatoire.');
            $this->redirect("/categories/{$id}/edit");
        }

        $slug = trim($_POST['slug'] ?? '');
        if ($slug === '') {
            $slug = Helper::slugify($name);
        } else {
            $slug = Helper::slugify($slug);
        }

        $description = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fas fa-exclamation-triangle');
        $color = trim($_POST['color'] ?? '#3a7bd5');
        $deadlineDays = max(1, (int)($_POST['deadline_days'] ?? 7));
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder = max(0, (int)($_POST['sort_order'] ?? 0));

        $stmt = $db->prepare(
            "UPDATE categories SET name = ?, slug = ?, description = ?, icon = ?, color = ?, deadline_days = ?, is_active = ?, sort_order = ?, updated_at = NOW() WHERE id = ?"
        );
        $stmt->execute([$name, $slug, $description, $icon, $color, $deadlineDays, $isActive, $sortOrder, $id]);

        $this->audit('update', 'Category', $id);
        $this->withSuccess('Catégorie mise à jour avec succès.');
        $this->redirect('/categories');
    }

    public function destroy(int $id): void {
        $this->auth();
        $this->requirePermission('categories.manage');
        I18n::init();

        if (!Csrf::verify($_POST['_token'] ?? '')) {
            $this->withError('Token de sécurité invalide.');
            $this->redirect('/categories');
        }

        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM reports WHERE category_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if ($result && (int)$result['cnt'] > 0) {
            $this->withError('Impossible de supprimer cette catégorie car elle contient des signalements actifs.');
            $this->redirect('/categories');
        }

        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);

        $this->audit('delete', 'Category', $id);
        $this->withSuccess('Catégorie supprimée.');
        $this->redirect('/categories');
    }

    public function toggle(int $id): void {
        $this->auth();
        $this->requirePermission('categories.manage');
        I18n::init();

        if (!Csrf::verify($_POST['_token'] ?? '')) {
            $this->withError('Token de sécurité invalide.');
            $this->redirect('/categories');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT is_active FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();

        if (!$category) {
            $this->withError('Catégorie non trouvée.');
            $this->redirect('/categories');
        }

        $newState = $category['is_active'] ? 0 : 1;
        $stmt = $db->prepare("UPDATE categories SET is_active = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$newState, $id]);

        $this->audit('toggle', 'Category', $id, ['is_active' => $category['is_active']], ['is_active' => $newState]);

        $label = $newState ? 'activée' : 'désactivée';
        $this->withSuccess("Catégorie {$label}.");
        $this->redirect('/categories');
    }
}
