<?php
namespace App\Controllers;

use App\Controllers\Controller;
use App\Helpers\Rbac;
use App\Helpers\Database;
use App\Helpers\Csrf;
use App\Helpers\I18n;
use App\Helpers\Session;

class LandingPageController extends Controller {

    private const UPLOAD_DIR = '/var/www/balagh-alger/public/uploads/landing';
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public function index(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();

        $db = Database::getConnection();
        $stats = [
            'partners'    => (int)$db->query("SELECT COUNT(*) FROM landing_partners")->fetchColumn(),
            'gallery'     => (int)$db->query("SELECT COUNT(*) FROM landing_gallery")->fetchColumn(),
            'testimonials'=> (int)$db->query("SELECT COUNT(*) FROM landing_testimonials")->fetchColumn(),
            'before_after'=> (int)$db->query("SELECT COUNT(*) FROM landing_before_after")->fetchColumn(),
            'faq'         => (int)$db->query("SELECT COUNT(*) FROM landing_faq")->fetchColumn(),
        ];
        $this->view('admin/landing/index', compact('stats'));
    }

    // ========================================
    // PARTNERS
    // ========================================

    public function partners(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();

        $db = Database::getConnection();
        $partners = $db->query("SELECT * FROM landing_partners ORDER BY sort_order ASC, id ASC")->fetchAll();
        $csrfToken = Csrf::generate();
        $this->view('admin/landing/partners', compact('partners', 'csrfToken'));
    }

    public function partnerStore(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/partners');

        $name = trim($_POST['name'] ?? '');
        if ($name === '') { $this->withError('Le nom est obligatoire.'); $this->redirect('/admin/landing/partners'); }

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO landing_partners (name, icon, color, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $name,
            trim($_POST['icon'] ?? 'fas fa-building'),
            trim($_POST['color'] ?? 'var(--primary-light)'),
            max(0, (int)($_POST['sort_order'] ?? 0)),
            isset($_POST['is_active']) ? 1 : 0
        ]);

        $this->audit('create', 'LandingPartner', (int)$db->lastInsertId());
        $this->withSuccess('Partenaire ajouté.');
        $this->redirect('/admin/landing/partners');
    }

    public function partnerEdit(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM landing_partners WHERE id = ?");
        $stmt->execute([$id]);
        $partner = $stmt->fetch();
        if (!$partner) { $this->withError('Partenaire non trouvé.'); $this->redirect('/admin/landing/partners'); }

        $csrfToken = Csrf::generate();
        $this->view('admin/landing/partner_form', compact('partner', 'csrfToken'));
    }

    public function partnerUpdate(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf("/admin/landing/partners/{$id}/edit");

        $name = trim($_POST['name'] ?? '');
        if ($name === '') { $this->withError('Le nom est obligatoire.'); $this->redirect("/admin/landing/partners/{$id}/edit"); }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE landing_partners SET name=?, icon=?, color=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([
            $name,
            trim($_POST['icon'] ?? 'fas fa-building'),
            trim($_POST['color'] ?? 'var(--primary-light)'),
            max(0, (int)($_POST['sort_order'] ?? 0)),
            isset($_POST['is_active']) ? 1 : 0,
            $id
        ]);

        $this->audit('update', 'LandingPartner', $id);
        $this->withSuccess('Partenaire mis à jour.');
        $this->redirect('/admin/landing/partners');
    }

    public function partnerDelete(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/partners');

        $db = Database::getConnection();
        $db->prepare("DELETE FROM landing_partners WHERE id = ?")->execute([$id]);
        $this->audit('delete', 'LandingPartner', $id);
        $this->withSuccess('Partenaire supprimé.');
        $this->redirect('/admin/landing/partners');
    }

    public function partnerToggle(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/partners');

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT is_active FROM landing_partners WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        if (!$item) { $this->withError('Non trouvé.'); $this->redirect('/admin/landing/partners'); }

        $newState = $item['is_active'] ? 0 : 1;
        $db->prepare("UPDATE landing_partners SET is_active = ? WHERE id = ?")->execute([$newState, $id]);
        $this->audit('toggle', 'LandingPartner', $id, ['is_active' => $item['is_active']], ['is_active' => $newState]);
        $this->withSuccess($newState ? 'Partenaire activé.' : 'Partenaire désactivé.');
        $this->redirect('/admin/landing/partners');
    }

    // ========================================
    // GALLERY
    // ========================================

    public function gallery(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();

        $db = Database::getConnection();
        $images = $db->query("SELECT * FROM landing_gallery ORDER BY sort_order ASC, id ASC")->fetchAll();
        $csrfToken = Csrf::generate();
        $this->view('admin/landing/gallery', compact('images', 'csrfToken'));
    }

    public function galleryStore(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/gallery');

        $imageUrl = '';
        $altText = trim($_POST['alt_text'] ?? '');
        $sortOrder = max(0, (int)($_POST['sort_order'] ?? 0));

        if (!empty($_FILES['image']['tmp_name'])) {
            $imageUrl = $this->uploadImage($_FILES['image'], 'gallery');
            if (!$imageUrl) {
                $this->withError('Erreur lors de l\'upload de l\'image (max 5MB, JPG/PNG/WebP).');
                $this->redirect('/admin/landing/gallery');
            }
        } elseif (!empty($_POST['image_url'])) {
            $imageUrl = trim($_POST['image_url']);
        } else {
            $this->withError('Une image est requise.');
            $this->redirect('/admin/landing/gallery');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO landing_gallery (image_url, alt_text, sort_order, is_active) VALUES (?, ?, ?, ?)");
        $stmt->execute([$imageUrl, $altText, $sortOrder, isset($_POST['is_active']) ? 1 : 0]);

        $this->audit('create', 'LandingGallery', (int)$db->lastInsertId());
        $this->withSuccess('Image ajoutée à la galerie.');
        $this->redirect('/admin/landing/gallery');
    }

    public function galleryDelete(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/gallery');

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT image_url FROM landing_gallery WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();

        if ($item && str_starts_with($item['image_url'], '/uploads/landing/')) {
            $fullPath = '/var/www/balagh-alger/public' . $item['image_url'];
            if (file_exists($fullPath)) unlink($fullPath);
        }

        $db->prepare("DELETE FROM landing_gallery WHERE id = ?")->execute([$id]);
        $this->audit('delete', 'LandingGallery', $id);
        $this->withSuccess('Image supprimée.');
        $this->redirect('/admin/landing/gallery');
    }

    public function galleryToggle(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/gallery');

        $db = Database::getConnection();
        $item = $db->prepare("SELECT is_active FROM landing_gallery WHERE id = ?");
        $item->execute([$id]);
        $item = $item->fetch();
        if (!$item) { $this->withError('Non trouvé.'); $this->redirect('/admin/landing/gallery'); }

        $newState = $item['is_active'] ? 0 : 1;
        $db->prepare("UPDATE landing_gallery SET is_active = ? WHERE id = ?")->execute([$newState, $id]);
        $this->audit('toggle', 'LandingGallery', $id);
        $this->withSuccess($newState ? 'Image activée.' : 'Image désactivée.');
        $this->redirect('/admin/landing/gallery');
    }

    // ========================================
    // TESTIMONIALS
    // ========================================

    public function testimonials(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();

        $db = Database::getConnection();
        $items = $db->query("SELECT * FROM landing_testimonials ORDER BY sort_order ASC, id ASC")->fetchAll();
        $csrfToken = Csrf::generate();
        $this->view('admin/landing/testimonials', compact('items', 'csrfToken'));
    }

    public function testimonialCreate(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();
        $csrfToken = Csrf::generate();
        $this->view('admin/landing/testimonial_form', compact('csrfToken'));
    }

    public function testimonialStore(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/testimonials');

        $textFr = trim($_POST['text_fr'] ?? '');
        $textAr = trim($_POST['text_ar'] ?? '');
        if ($textFr === '' || $textAr === '') {
            $this->withError('Les textes FR et AR sont requis.');
            $this->redirect('/admin/landing/testimonials');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO landing_testimonials (text_fr, text_ar, author_name, author_role, avatar_letter, avatar_gradient, rating, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $textFr, $textAr,
            trim($_POST['author_name'] ?? ''),
            trim($_POST['author_role'] ?? ''),
            strtoupper(substr(trim($_POST['author_name'] ?? 'U'), 0, 1)),
            trim($_POST['avatar_gradient'] ?? 'var(--gradient-accent)'),
            max(1, min(5, (int)($_POST['rating'] ?? 5))),
            isset($_POST['is_active']) ? 1 : 0,
            max(0, (int)($_POST['sort_order'] ?? 0))
        ]);

        $this->audit('create', 'LandingTestimonial', (int)$db->lastInsertId());
        $this->withSuccess('Témoignage ajouté.');
        $this->redirect('/admin/landing/testimonials');
    }

    public function testimonialEdit(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM landing_testimonials WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        if (!$item) { $this->withError('Non trouvé.'); $this->redirect('/admin/landing/testimonials'); }

        $csrfToken = Csrf::generate();
        $this->view('admin/landing/testimonial_form', compact('item', 'csrfToken'));
    }

    public function testimonialUpdate(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf("/admin/landing/testimonials/{$id}/edit");

        $textFr = trim($_POST['text_fr'] ?? '');
        $textAr = trim($_POST['text_ar'] ?? '');
        if ($textFr === '' || $textAr === '') {
            $this->withError('Les textes FR et AR sont requis.');
            $this->redirect("/admin/landing/testimonials/{$id}/edit");
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE landing_testimonials SET text_fr=?, text_ar=?, author_name=?, author_role=?, avatar_letter=?, avatar_gradient=?, rating=?, is_active=?, sort_order=? WHERE id=?");
        $stmt->execute([
            $textFr, $textAr,
            trim($_POST['author_name'] ?? ''),
            trim($_POST['author_role'] ?? ''),
            strtoupper(substr(trim($_POST['author_name'] ?? 'U'), 0, 1)),
            trim($_POST['avatar_gradient'] ?? 'var(--gradient-accent)'),
            max(1, min(5, (int)($_POST['rating'] ?? 5))),
            isset($_POST['is_active']) ? 1 : 0,
            max(0, (int)($_POST['sort_order'] ?? 0)),
            $id
        ]);

        $this->audit('update', 'LandingTestimonial', $id);
        $this->withSuccess('Témoignage mis à jour.');
        $this->redirect('/admin/landing/testimonials');
    }

    public function testimonialDelete(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/testimonials');

        $db = Database::getConnection();
        $db->prepare("DELETE FROM landing_testimonials WHERE id = ?")->execute([$id]);
        $this->audit('delete', 'LandingTestimonial', $id);
        $this->withSuccess('Témoignage supprimé.');
        $this->redirect('/admin/landing/testimonials');
    }

    public function testimonialToggle(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/testimonials');

        $db = Database::getConnection();
        $item = $db->prepare("SELECT is_active FROM landing_testimonials WHERE id = ?");
        $item->execute([$id]);
        $item = $item->fetch();
        if (!$item) { $this->withError('Non trouvé.'); $this->redirect('/admin/landing/testimonials'); }

        $newState = $item['is_active'] ? 0 : 1;
        $db->prepare("UPDATE landing_testimonials SET is_active = ? WHERE id = ?")->execute([$newState, $id]);
        $this->audit('toggle', 'LandingTestimonial', $id);
        $this->withSuccess($newState ? 'Témoignage activé.' : 'Témoignage désactivé.');
        $this->redirect('/admin/landing/testimonials');
    }

    // ========================================
    // BEFORE / AFTER
    // ========================================

    public function beforeAfter(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();

        $db = Database::getConnection();
        $items = $db->query("SELECT * FROM landing_before_after ORDER BY sort_order ASC, id ASC")->fetchAll();
        $csrfToken = Csrf::generate();
        $this->view('admin/landing/before_after', compact('items', 'csrfToken'));
    }

    public function beforeAfterCreate(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();
        $csrfToken = Csrf::generate();
        $this->view('admin/landing/ba_form', compact('csrfToken'));
    }

    public function beforeAfterStore(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/before-after');

        $beforeImg = '';
        $afterImg = '';

        if (!empty($_FILES['before_image']['tmp_name'])) {
            $beforeImg = $this->uploadImage($_FILES['before_image'], 'before-after');
        } elseif (!empty($_POST['before_image_url'])) {
            $beforeImg = trim($_POST['before_image_url']);
        }
        if (!empty($_FILES['after_image']['tmp_name'])) {
            $afterImg = $this->uploadImage($_FILES['after_image'], 'before-after');
        } elseif (!empty($_POST['after_image_url'])) {
            $afterImg = trim($_POST['after_image_url']);
        }

        if ($beforeImg === '' || $afterImg === '') {
            $this->withError('Les deux images (avant/après) sont requises.');
            $this->redirect('/admin/landing/before-after');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO landing_before_after (before_image, after_image, title_fr, title_ar, desc_fr, desc_ar, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $beforeImg, $afterImg,
            trim($_POST['title_fr'] ?? ''),
            trim($_POST['title_ar'] ?? ''),
            trim($_POST['desc_fr'] ?? ''),
            trim($_POST['desc_ar'] ?? ''),
            max(0, (int)($_POST['sort_order'] ?? 0)),
            isset($_POST['is_active']) ? 1 : 0
        ]);

        $this->audit('create', 'LandingBeforeAfter', (int)$db->lastInsertId());
        $this->withSuccess('Élément avant/après ajouté.');
        $this->redirect('/admin/landing/before-after');
    }

    public function beforeAfterEdit(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM landing_before_after WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        if (!$item) { $this->withError('Non trouvé.'); $this->redirect('/admin/landing/before-after'); }

        $csrfToken = Csrf::generate();
        $this->view('admin/landing/ba_form', compact('item', 'csrfToken'));
    }

    public function beforeAfterUpdate(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf("/admin/landing/before-after/{$id}/edit");

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT before_image, after_image FROM landing_before_after WHERE id = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch();

        $beforeImg = $existing['before_image'] ?? '';
        $afterImg = $existing['after_image'] ?? '';

        if (!empty($_FILES['before_image']['tmp_name'])) {
            $this->deleteLocalImage($beforeImg);
            $beforeImg = $this->uploadImage($_FILES['before_image'], 'before-after');
        } elseif (!empty($_POST['before_image_url']) && trim($_POST['before_image_url']) !== $beforeImg) {
            $this->deleteLocalImage($beforeImg);
            $beforeImg = trim($_POST['before_image_url']);
        }

        if (!empty($_FILES['after_image']['tmp_name'])) {
            $this->deleteLocalImage($afterImg);
            $afterImg = $this->uploadImage($_FILES['after_image'], 'before-after');
        } elseif (!empty($_POST['after_image_url']) && trim($_POST['after_image_url']) !== $afterImg) {
            $this->deleteLocalImage($afterImg);
            $afterImg = trim($_POST['after_image_url']);
        }

        $stmt = $db->prepare("UPDATE landing_before_after SET before_image=?, after_image=?, title_fr=?, title_ar=?, desc_fr=?, desc_ar=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([
            $beforeImg, $afterImg,
            trim($_POST['title_fr'] ?? ''),
            trim($_POST['title_ar'] ?? ''),
            trim($_POST['desc_fr'] ?? ''),
            trim($_POST['desc_ar'] ?? ''),
            max(0, (int)($_POST['sort_order'] ?? 0)),
            isset($_POST['is_active']) ? 1 : 0,
            $id
        ]);

        $this->audit('update', 'LandingBeforeAfter', $id);
        $this->withSuccess('Élément avant/après mis à jour.');
        $this->redirect('/admin/landing/before-after');
    }

    public function beforeAfterDelete(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/before-after');

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT before_image, after_image FROM landing_before_after WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();

        if ($item) {
            $this->deleteLocalImage($item['before_image']);
            $this->deleteLocalImage($item['after_image']);
        }

        $db->prepare("DELETE FROM landing_before_after WHERE id = ?")->execute([$id]);
        $this->audit('delete', 'LandingBeforeAfter', $id);
        $this->withSuccess('Élément supprimé.');
        $this->redirect('/admin/landing/before-after');
    }

    public function beforeAfterToggle(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/before-after');

        $db = Database::getConnection();
        $item = $db->prepare("SELECT is_active FROM landing_before_after WHERE id = ?");
        $item->execute([$id]);
        $item = $item->fetch();
        if (!$item) { $this->withError('Non trouvé.'); $this->redirect('/admin/landing/before-after'); }

        $newState = $item['is_active'] ? 0 : 1;
        $db->prepare("UPDATE landing_before_after SET is_active = ? WHERE id = ?")->execute([$newState, $id]);
        $this->audit('toggle', 'LandingBeforeAfter', $id);
        $this->withSuccess($newState ? 'Élément activé.' : 'Élément désactivé.');
        $this->redirect('/admin/landing/before-after');
    }

    // ========================================
    // FAQ
    // ========================================

    public function faq(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();

        $db = Database::getConnection();
        $items = $db->query("SELECT * FROM landing_faq ORDER BY sort_order ASC, id ASC")->fetchAll();
        $csrfToken = Csrf::generate();
        $this->view('admin/landing/faq', compact('items', 'csrfToken'));
    }

    public function faqCreate(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();
        $csrfToken = Csrf::generate();
        $this->view('admin/landing/faq_form', compact('csrfToken'));
    }

    public function faqStore(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/faq');

        $questionFr = trim($_POST['question_fr'] ?? '');
        $questionAr = trim($_POST['question_ar'] ?? '');
        $answerFr = trim($_POST['answer_fr'] ?? '');
        $answerAr = trim($_POST['answer_ar'] ?? '');

        if ($questionFr === '' || $answerFr === '') {
            $this->withError('Les champs FR sont requis.');
            $this->redirect('/admin/landing/faq');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO landing_faq (question_fr, question_ar, answer_fr, answer_ar, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $questionFr, $questionAr, $answerFr, $answerAr,
            max(0, (int)($_POST['sort_order'] ?? 0)),
            isset($_POST['is_active']) ? 1 : 0
        ]);

        $this->audit('create', 'LandingFaq', (int)$db->lastInsertId());
        $this->withSuccess('Question FAQ ajoutée.');
        $this->redirect('/admin/landing/faq');
    }

    public function faqEdit(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM landing_faq WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        if (!$item) { $this->withError('Non trouvé.'); $this->redirect('/admin/landing/faq'); }

        $csrfToken = Csrf::generate();
        $this->view('admin/landing/faq_form', compact('item', 'csrfToken'));
    }

    public function faqUpdate(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf("/admin/landing/faq/{$id}/edit");

        $questionFr = trim($_POST['question_fr'] ?? '');
        $answerFr = trim($_POST['answer_fr'] ?? '');
        if ($questionFr === '' || $answerFr === '') {
            $this->withError('Les champs FR sont requis.');
            $this->redirect("/admin/landing/faq/{$id}/edit");
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE landing_faq SET question_fr=?, question_ar=?, answer_fr=?, answer_ar=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([
            $questionFr, trim($_POST['question_ar'] ?? ''),
            $answerFr, trim($_POST['answer_ar'] ?? ''),
            max(0, (int)($_POST['sort_order'] ?? 0)),
            isset($_POST['is_active']) ? 1 : 0,
            $id
        ]);

        $this->audit('update', 'LandingFaq', $id);
        $this->withSuccess('Question FAQ mise à jour.');
        $this->redirect('/admin/landing/faq');
    }

    public function faqDelete(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/faq');

        $db = Database::getConnection();
        $db->prepare("DELETE FROM landing_faq WHERE id = ?")->execute([$id]);
        $this->audit('delete', 'LandingFaq', $id);
        $this->withSuccess('Question FAQ supprimée.');
        $this->redirect('/admin/landing/faq');
    }

    public function faqToggle(int $id): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/faq');

        $db = Database::getConnection();
        $item = $db->prepare("SELECT is_active FROM landing_faq WHERE id = ?");
        $item->execute([$id]);
        $item = $item->fetch();
        if (!$item) { $this->withError('Non trouvé.'); $this->redirect('/admin/landing/faq'); }

        $newState = $item['is_active'] ? 0 : 1;
        $db->prepare("UPDATE landing_faq SET is_active = ? WHERE id = ?")->execute([$newState, $id]);
        $this->audit('toggle', 'LandingFaq', $id);
        $this->withSuccess($newState ? 'Question activée.' : 'Question désactivée.');
        $this->redirect('/admin/landing/faq');
    }

    // ========================================
    // SETTINGS (hero + social links)
    // ========================================

    public function settings(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        I18n::init();

        $db = Database::getConnection();
        $rows = $db->prepare("SELECT key_name, value FROM settings WHERE group_name = 'landing'");
        $rows->execute();
        $settings = [];
        while ($r = $rows->fetch()) $settings[$r['key_name']] = $r['value'];

        $csrfToken = Csrf::generate();
        $this->view('admin/landing/settings', compact('settings', 'csrfToken'));
    }

    public function settingsUpdate(): void {
        $this->auth();
        $this->requirePermission('landing.manage');
        $this->checkCsrf('/admin/landing/settings');

        $db = Database::getConnection();
        $keys = ['facebook_url', 'twitter_url', 'instagram_url', 'youtube_url'];

        foreach ($keys as $key) {
            $val = trim($_POST[$key] ?? '#');
            $stmt = $db->prepare("UPDATE settings SET value = ? WHERE group_name = 'landing' AND key_name = ?");
            $stmt->execute([$val, $key]);
        }

        // Hero image: upload or URL
        if (!empty($_FILES['hero_image']['tmp_name'])) {
            $url = $this->uploadImage($_FILES['hero_image'], 'hero');
            if ($url) {
                $db->prepare("UPDATE settings SET value = ? WHERE group_name = 'landing' AND key_name = 'hero_image'")->execute([$url]);
            }
        } elseif (!empty($_POST['hero_image_url'])) {
            $db->prepare("UPDATE settings SET value = ? WHERE group_name = 'landing' AND key_name = 'hero_image'")->execute([trim($_POST['hero_image_url'])]);
        }

        $this->audit('update', 'LandingSettings', 0);
        $this->withSuccess('Paramètres de la landing page mis à jour.');
        $this->redirect('/admin/landing/settings');
    }

    // ========================================
    // HELPERS
    // ========================================

    private function uploadImage(array $file, string $subfolder): ?string {
        if ($file['error'] !== UPLOAD_ERR_OK) return null;
        if ($file['size'] > self::MAX_FILE_SIZE) return null;

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_TYPES)) return null;

        $dir = self::UPLOAD_DIR . '/' . $subfolder;
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $ext = match($mime) { 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', default => 'jpg' };
        $filename = uniqid('lp_', true) . '.' . $ext;
        $dest = $dir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

        return '/uploads/landing/' . $subfolder . '/' . $filename;
    }

    private function deleteLocalImage(?string $url): void {
        if (!$url || !str_starts_with($url, '/uploads/landing/')) return;
        $fullPath = '/var/www/balagh-alger/public' . $url;
        if (file_exists($fullPath)) unlink($fullPath);
    }
}
