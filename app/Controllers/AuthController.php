<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Csrf;
use App\Helpers\Validator;
use App\Helpers\Helper;
use App\Helpers\AuditLog;
use App\Helpers\Rbac;
use App\Controllers\Controller;

class AuthController extends Controller {

    public function loginForm(): void {
        if (Session::isAuthenticated()) {
            $this->redirect('/dashboard');
        }
        $csrfToken = Csrf::generate();
        $this->viewRaw('auth/login', compact('csrfToken'));
    }

    public function login(): void {
        if (!Csrf::verify($_POST['_token'] ?? '')) {
            $this->withError('Token de sécurité invalide.');
            $this->redirect('/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->withError('Email et mot de passe requis.');
            $this->redirect('/login');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT u.*, GROUP_CONCAT(r.name) as roles FROM users u LEFT JOIN user_roles ur ON u.id = ur.user_id LEFT JOIN roles r ON ur.role_id = r.id WHERE u.email = ? AND u.deleted_at IS NULL GROUP BY u.id");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Rate limiting: check if account is locked
        if ($user && !empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            AuditLog::log('login_locked', 'User', $user['id'], ['locked_until' => $user['locked_until']]);
            $this->withError('Compte temporairement verrouillé. Réessayez dans 15 minutes.');
            $this->redirect('/login');
        }

        if (!$user || !password_verify($password, $user['password'])) {
            AuditLog::log('login_failed', 'User', null, ['email' => $email]);
            // Increment login_attempts
            if ($user) {
                $attempts = (int)($user['login_attempts'] ?? 0) + 1;
                if ($attempts >= 5) {
                    $db->prepare("UPDATE users SET login_attempts = ?, locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id = ?")->execute([$attempts, $user['id']]);
                } else {
                    $db->prepare("UPDATE users SET login_attempts = ? WHERE id = ?")->execute([$attempts, $user['id']]);
                }
            }
            $this->withError('Identifiants incorrects.');
            $this->redirect('/login');
        }

        if ($user['status'] !== 'active') {
            $this->withError('Compte suspendu ou inactif.');
            $this->redirect('/login');
        }

        // Reset login attempts and lock on success
        $db->prepare("UPDATE users SET last_login_at = NOW(), login_attempts = 0, locked_until = NULL WHERE id = ?")->execute([$user['id']]);

        // Session regeneration to prevent fixation
        session_regenerate_id(true);

        Session::setAuthenticated($user['id'], $user['email'], $user['first_name'] . ' ' . $user['last_name']);
        Session::set('user_roles', explode(',', $user['roles']));
        Session::set('user_avatar', $user['avatar']);
        Session::set('organization_id', $user['organization_id']);
        Session::set('daira_id', $user['daira_id']);
        Session::set('commune_id', $user['commune_id']);

        // Load permissions into session for RBAC
        Rbac::loadPermissions($user['id']);

        AuditLog::log('login', 'User', $user['id']);
        $this->redirect('/dashboard');
    }

    public function logout(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
        }
        $this->checkCsrf('/dashboard');
        $userId = Session::getUserId();
        if ($userId) {
            AuditLog::log('logout', 'User', $userId);
        }
        Session::destroy();
        $this->redirect('/login');
    }

    public function registerForm(): void {
        if (Session::isAuthenticated()) {
            $this->redirect('/dashboard');
        }
        $csrfToken = Csrf::generate();
        $dairas = Database::getConnection()->query("SELECT id, name, name_ar FROM dairas WHERE is_active = 1 ORDER BY name")->fetchAll();
        $this->viewRaw('auth/register', compact('csrfToken', 'dairas'));
    }

    public function register(): void {
        if (!Csrf::verify($_POST['_token'] ?? '')) {
            $this->withError('Token de sécurité invalide.');
            $this->redirect('/register');
        }

        $errors = $this->validate([
            'first_name' => ['required', 'label' => 'Prénom', 'max' => 100],
            'last_name' => ['required', 'label' => 'Nom', 'max' => 100],
            'email' => ['required', 'email', 'label' => 'Email'],
            'phone' => ['label' => 'Téléphone', 'max' => 30],
            'password' => ['required', 'label' => 'Mot de passe', 'min' => 6],
        ]);

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $_POST);
            $this->redirect('/register');
        }

        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->fetch()) {
            $this->withError('Cet email est déjà utilisé.');
            $this->redirect('/register');
        }

        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff), random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );

        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (uuid, first_name, last_name, email, phone, password, status, email_verified_at) VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())");
        $stmt->execute([$uuid, $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'] ?? null, $hash]);

        $userId = $db->lastInsertId();

        $stmt = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, (SELECT id FROM roles WHERE name = 'citizen'))");
        $stmt->execute([$userId]);

        AuditLog::log('register', 'User', $userId);
        $this->withSuccess('Compte créé avec succès. Connectez-vous.');
        $this->redirect('/login');
    }

    public function forgotPasswordForm(): void {
        $csrfToken = Csrf::generate();
        $this->viewRaw('auth/forgot_password', compact('csrfToken'));
    }

    public function forgotPassword(): void {
        if (!Csrf::verify($_POST['_token'] ?? '')) {
            $this->withError('Token invalide.');
            $this->redirect('/forgot-password');
        }
        $this->withSuccess('Si cet email existe, un lien de réinitialisation vous a été envoyé.');
        $this->redirect('/forgot-password');
    }
}
