<?php
/**
 * SLA Deadline Alert Cron Script
 * Run: php /var/www/balagh-alger/cron/sla_alerts.php
 * Or via queue: php artisan sla:run
 * Crontab: 0 9 * * * php /var/www/balagh-alger/cron/sla_alerts.php
 */
$root = dirname(__DIR__);
require_once $root . '/vendor/autoload.php';
require_once $root . '/app/Config/paths.php';
require_once APP_PATH . '/Helpers/Session.php';
require_once APP_PATH . '/Helpers/Database.php';
require_once APP_PATH . '/Helpers/Notification.php';
require_once APP_PATH . '/Helpers/Queue.php';

if (!function_exists('__')) {
    function __(string $key, array $replace = []): string {
        return \App\Helpers\I18n::t($key, $replace);
    }
}

spl_autoload_register(function ($class) {
    $path = APP_PATH . '/' . str_replace('\\', '/', str_replace('App\\', '', $class)) . '.php';
    if (file_exists($path)) { require_once $path; }
});

use App\Helpers\Queue;

if (Queue::isAvailable()) {
    Queue::dispatch(\App\Jobs\SlaAlertJob::class);
    echo date('Y-m-d H:i:s') . " — SLA Cron: job dispatched to queue\n";
} else {
    // Fallback: run synchronously (legacy)
    require_once APP_PATH . '/Jobs/SlaAlertJob.php';
    $job = new \App\Jobs\SlaAlertJob();
    $job->handle();
    echo date('Y-m-d H:i:s') . " — SLA Cron: ran synchronously (Redis unavailable)\n";
}
