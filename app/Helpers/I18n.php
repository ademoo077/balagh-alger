<?php
namespace App\Helpers;

class I18n {
    private static ?array $translations = null;
    private static string $currentLang = 'fr';
    private static array $locales = [
        'fr' => ['dir' => 'ltr', 'label' => 'Français', 'flag' => '🇫🇷', 'date_format' => 'd/m/Y', 'datetime_format' => 'd/m/Y H:i', 'decimal_sep' => ',', 'thousand_sep' => ' ', 'number_decimals' => 0],
        'ar' => ['dir' => 'rtl', 'label' => 'العربية', 'flag' => '🇩🇿', 'date_format' => 'Y/m/d', 'datetime_format' => 'Y/m/d H:i', 'decimal_sep' => '.', 'thousand_sep' => ',', 'number_decimals' => 0],
    ];

    public static function init(): void {
        $lang = $_COOKIE['lang'] ?? $_SESSION['lang'] ?? 'fr';
        if (!isset(self::$locales[$lang])) $lang = 'fr';
        self::$currentLang = $lang;
        self::load($lang);
    }

    public static function setLang(string $lang): void {
        if (!isset(self::$locales[$lang])) return;
        self::$currentLang = $lang;
        $_SESSION['lang'] = $lang;
        setcookie('lang', $lang, [
            'expires' => time() + 86400 * 365,
            'path' => '/',
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
        self::load($lang);
    }

    public static function getLang(): string {
        return self::$currentLang;
    }

    public static function getDir(): string {
        return self::$locales[self::$currentLang]['dir'] ?? 'ltr';
    }

    public static function isRtl(): bool {
        return self::getDir() === 'rtl';
    }

    public static function getLocaleInfo(): array {
        return self::$locales[self::$currentLang];
    }

    private static function load(string $lang): void {
        $file = __DIR__ . '/../../lang/' . $lang . '.json';
        if (file_exists($file)) {
            self::$translations = json_decode(file_get_contents($file), true) ?? [];
        } else {
            self::$translations = [];
        }
    }

    public static function t(string $key, array $replace = []): string {
        $value = self::resolve($key, self::$translations ?? []);
        if ($value === null) {
            return $key;
        }
        if (!empty($replace)) {
            foreach ($replace as $k => $v) {
                $value = str_replace(':' . $k, $v, $value);
            }
        }
        return $value;
    }

    private static function resolve(string $key, array $data): ?string {
        $keys = explode('.', $key);
        $current = $data;
        foreach ($keys as $k) {
            if (!isset($current[$k])) return null;
            $current = $current[$k];
        }
        return is_string($current) ? $current : null;
    }

    public static function formatNumber(float $number): string {
        $info = self::getLocaleInfo();
        return number_format($number, $info['number_decimals'], $info['decimal_sep'], $info['thousand_sep']);
    }

    public static function formatDate(string $date): string {
        $info = self::getLocaleInfo();
        return date($info['date_format'], strtotime($date));
    }

    public static function formatDateTime(string $datetime): string {
        $info = self::getLocaleInfo();
        return date($info['datetime_format'], strtotime($datetime));
    }

    public static function timeAgo(string $datetime): string {
        $diff = time() - strtotime($datetime);
        if ($diff < 60) return self::t('time.just_now');
        if ($diff < 3600) { $m = floor($diff / 60); return self::t($m > 1 ? 'time.minutes_ago' : 'time.minute_ago', ['count' => $m]); }
        if ($diff < 86400) { $h = floor($diff / 3600); return self::t($h > 1 ? 'time.hours_ago' : 'time.hour_ago', ['count' => $h]); }
        $days = floor($diff / 86400);
        if ($days < 30) return self::t($days > 1 ? 'time.days_ago' : 'time.day_ago', ['count' => $days]);
        $months = floor($days / 30);
        if ($months < 12) return self::t($months > 1 ? 'time.months_ago' : 'time.month_ago', ['count' => $months]);
        $years = floor($months / 12);
        return self::t($years > 1 ? 'time.years_ago' : 'time.year_ago', ['count' => $years]);
    }
}

function __(string $key, array $replace = []): string {
    return \App\Helpers\I18n::t($key, $replace);
}
