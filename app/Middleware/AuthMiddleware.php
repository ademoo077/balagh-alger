<?php
namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Rbac;
use App\Helpers\Router;

class AuthMiddleware {

    /**
     * Require authentication. Redirect to login if not authenticated.
     */
    public static function requireAuth(): void {
        if (!Session::isAuthenticated()) {
            Router::redirect('/login');
        }
    }

    /**
     * Require specific role(s). Must be called after requireAuth().
     */
    public static function requireRole(string ...$roles): void {
        self::requireAuth();
        if (!Rbac::isRole(...$roles)) {
            self::deny();
        }
    }

    /**
     * Require minimum hierarchy level.
     */
    public static function requireMinLevel(int $level): void {
        self::requireAuth();
        if (!Rbac::minLevel($level)) {
            self::deny();
        }
    }

    /**
     * Require specific permission.
     */
    public static function requirePermission(string $permission): void {
        self::requireAuth();
        if (!Rbac::has($permission)) {
            self::deny();
        }
    }

    /**
     * Require ANY of the given permissions.
     */
    public static function requireAnyPermission(array $permissions): void {
        self::requireAuth();
        if (!Rbac::hasAny($permissions)) {
            self::deny();
        }
    }

    /**
     * Require staff role (non-citizen).
     */
    public static function requireStaff(): void {
        self::requireAuth();
        if (!Rbac::isStaff()) {
            self::deny();
        }
    }

    /**
     * Deny access with error and redirect to dashboard.
     */
    private static function deny(): void {
        Session::flash('error', 'Accès non autorisé. Vous n\'avez pas les permissions nécessaires.');
        Router::redirect('/dashboard');
    }
}
