<?php
namespace App\Helpers;

class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function set(string $key, $value): void { $_SESSION[$key] = $value; }
    public static function get(string $key, $default = null) { return $_SESSION[$key] ?? $default; }
    public static function has(string $key): bool { return isset($_SESSION[$key]); }
    public static function remove(string $key): void { unset($_SESSION[$key]); }
    public static function destroy(): void { session_destroy(); $_SESSION = []; }

    public static function flash(string $key, $value): void { $_SESSION['flash'][$key] = $value; }
    public static function getFlash(string $key, $default = null) {
        $value = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    public static function setAuthenticated(int $userId, string $email, string $name): void {
        self::set('user_id', $userId);
        self::set('user_email', $email);
        self::set('user_name', $name);
        self::set('logged_in', true);
    }

    public static function isAuthenticated(): bool { return self::get('logged_in', false); }
    public static function getUserId(): ?int { return self::get('user_id'); }
    public static function getUserName(): string { return self::get('user_name', ''); }

    public static function getAvatar(): ?string {
        $avatar = self::get('user_avatar', '');
        if (empty($avatar) || $avatar === 'default.png') {
            return null;
        }
        return $avatar;
    }
}
