<?php
namespace App\Helpers;

class Helper {
    public static function sanitize(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function generateTrackingCode(): string {
        return 'BA-' . date('Y') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function slugify(string $text): string {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text);
    }

    public static function timeAgo(string $datetime): string {
        return \App\Helpers\I18n::timeAgo($datetime);
    }

    public static function formatPhone(string $phone): string {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 10) {
            return substr($phone, 0, 2) . ' ' . substr($phone, 2, 2) . ' ' . substr($phone, 4, 2) . ' ' . substr($phone, 6, 2) . ' ' . substr($phone, 8);
        }
        return $phone;
    }

    public static function getStatusBadge(string $status): string {
        $key = "statuses.{$status}";
        $label = \App\Helpers\I18n::t($key);
        if ($label === $key) $label = ucfirst(str_replace('_', ' ', $status));
        $classMap = [
            'submitted' => 'bg-info', 'acknowledged' => 'bg-primary',
            'assigned' => 'bg-warning text-dark', 'in_progress' => 'bg-secondary',
            'pending_review' => 'bg-warning text-dark', 'pending_unite' => 'bg-info',
            'validated' => 'bg-success', 'resolved' => 'bg-success',
            'closed' => 'bg-dark', 'rejected' => 'bg-danger',
        ];
        $class = $classMap[$status] ?? 'bg-secondary';
        return "<span class='badge {$class}'>{$label}</span>";
    }

    public static function getPriorityBadge(string $priority): string {
        $key = "priorities.{$priority}";
        $label = \App\Helpers\I18n::t($key);
        if ($label === $key) $label = ucfirst($priority);
        $classMap = [
            'low' => 'bg-success', 'medium' => 'bg-warning text-dark',
            'high' => 'bg-danger', 'urgent' => 'bg-danger text-white pulse',
        ];
        $class = $classMap[$priority] ?? 'bg-secondary';
        return "<span class='badge {$class}'>{$label}</span>";
    }
}
