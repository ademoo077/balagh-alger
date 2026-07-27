<?php
namespace App\Helpers;

class DeadlineHelper {
    public static function getDeadline(string $createdAt, int $deadlineDays): string {
        $deadline = date('Y-m-d H:i:s', strtotime($createdAt . " +{$deadlineDays} days"));
        return $deadline;
    }

    public static function getStatus(string $createdAt, int $deadlineDays, string $currentStatus): array {
        if (in_array($currentStatus, ['resolved', 'closed', 'rejected'])) {
            return ['label' => 'Terminé', 'class' => 'success', 'days_left' => null, 'is_late' => false];
        }

        $deadline = new \DateTime(self::getDeadline($createdAt, $deadlineDays));
        $now = new \DateTime();
        $diff = $now->diff($deadline);
        $daysLeft = (int)$diff->format('%r%a');

        if ($diff->invert) {
            return ['label' => "En retard de {$daysLeft} jour" . ($daysLeft > 1 ? 's' : ''), 'class' => 'danger', 'days_left' => -$daysLeft, 'is_late' => true];
        } elseif ($daysLeft <= ceil($deadlineDays * 0.2)) {
            return ['label' => "{$daysLeft} jour" . ($daysLeft > 1 ? 's' : '') . " restant" . ($daysLeft > 1 ? 's' : ''), 'class' => 'warning', 'days_left' => $daysLeft, 'is_late' => false];
        } else {
            return ['label' => "{$daysLeft} jour" . ($daysLeft > 1 ? 's' : '') . " restant" . ($daysLeft > 1 ? 's' : ''), 'class' => 'success', 'days_left' => $daysLeft, 'is_late' => false];
        }
    }

    public static function renderBadge(string $createdAt, int $deadlineDays, string $currentStatus): string {
        $status = self::getStatus($createdAt, $deadlineDays, $currentStatus);
        if ($status['days_left'] === null) {
            return "<span class=\"badge bg-success\"><i class=\"fas fa-check me-1\"></i>{$status['label']}</span>";
        }
        $icon = $status['is_late'] ? 'fa-exclamation-triangle' : ($status['class'] === 'warning' ? 'fa-clock' : 'fa-check-circle');
        $class = 'bg-' . $status['class'];
        if ($status['class'] === 'warning') $class .= ' text-dark';
        return "<span class=\"badge {$class}\"><i class=\"fas {$icon} me-1\"></i>{$status['label']}</span>";
    }
}
