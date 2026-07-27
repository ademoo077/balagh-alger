<?php
namespace App\Helpers;

class Csrf {
    public static function generate(): string {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }

    public static function field(): string {
        return '<input type="hidden" name="_token" value="' . self::generate() . '">';
    }

    public static function meta(): string {
        return '<meta name="csrf-token" content="' . self::generate() . '">';
    }

    public static function verify(string $token): bool {
        $stored = Session::get('csrf_token');
        return $stored && hash_equals($stored, $token);
    }
}
