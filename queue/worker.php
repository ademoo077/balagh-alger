<?php
/**
 * Queue Worker — Balagh Alger
 *
 * Processes jobs from Redis queues.
 *
 * Usage:
 *   php queue/worker.php                    # Run once (default queue)
 *   php queue/worker.php --queue=default,high,low
 *   php queue/worker.php --once              # Process one job and exit
 *   php queue/worker.php --stop-after=60     # Stop after 60 seconds
 *   php queue/worker.php --status            # Show queue sizes
 *   php queue/worker.php --failed            # List failed jobs
 *   php queue/worker.php --retry=0           # Retry failed job at index 0
 *   php queue/worker.php --flush-failed      # Clear all failed jobs
 */

$root = dirname(__DIR__);

$envFile = $root . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!getenv($key)) putenv("{$key}={$value}");
        }
    }
}

require_once $root . '/vendor/autoload.php';
require_once $root . '/app/Config/paths.php';
require_once APP_PATH . '/Helpers/Session.php';
require_once APP_PATH . '/Helpers/Database.php';
require_once APP_PATH . '/Helpers/Router.php';
require_once APP_PATH . '/Helpers/Csrf.php';
require_once APP_PATH . '/Helpers/Validator.php';
require_once APP_PATH . '/Helpers/Helper.php';
require_once APP_PATH . '/Helpers/AuditLog.php';
require_once APP_PATH . '/Helpers/Notification.php';
require_once APP_PATH . '/Helpers/I18n.php';
require_once APP_PATH . '/Helpers/Queue.php';

if (!function_exists('__')) {
    function __(string $key, array $replace = []): string {
        return \App\Helpers\I18n::t($key, $replace);
    }
}

spl_autoload_register(function ($class) {
    $path = APP_PATH . '/' . str_replace('\\', '/', str_replace('App\\', '', $class)) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

use App\Helpers\Queue;

$args = getopt('', ['queue:', 'once', 'stop-after:', 'status', 'failed', 'retry:', 'flush-failed', 'help']);

if (isset($args['help'])) {
    echo <<<HELP
Balagh Alger Queue Worker

Usage:
  php queue/worker.php [OPTIONS]

Options:
  --queue=<list>       Comma-separated queues (default,high,low,push)
  --once               Process one job then exit
  --stop-after=<sec>   Stop after N seconds
  --status             Show queue sizes and exit
  --failed             List failed jobs and exit
  --retry=<index>      Retry failed job at index
  --flush-failed       Clear all failed jobs
  --help               Show this help

Examples:
  php queue/worker.php
  php queue/worker.php --queue=default,high
  php queue/worker.php --once
  php queue/worker.php --stop-after=3600
  php queue/worker.php --status
HELP;
    exit(0);
}

// --- Status ---
if (isset($args['status'])) {
    $sizes = Queue::sizes();
    echo "Queue sizes:\n";
    foreach ($sizes as $name => $count) {
        $bar = str_repeat('█', min($count, 40));
        echo "  {$name}: {$count} {$bar}\n";
    }
    exit(0);
}

// --- Failed ---
if (isset($args['failed'])) {
    $failed = Queue::getFailed();
    if (empty($failed)) {
        echo "No failed jobs.\n";
    } else {
        echo count($failed) . " failed job(s):\n";
        foreach ($failed as $i => $json) {
            $job = json_decode($json, true);
            $age = isset($job['failed_at']) ? round(microtime(true) - $job['failed_at']) : '?';
            $errorMsg = $job['error'] ?? '?';
            echo "  [{$i}] {$job['job']} | error: {$errorMsg} | {$age}s ago\n";
        }
    }
    exit(0);
}

// --- Retry ---
if (isset($args['retry'])) {
    $job = Queue::retryFailed((int)$args['retry']);
    if ($job) {
        echo "Retried job: {$job['job']}\n";
    } else {
        echo "No failed job at index {$args['retry']}\n";
    }
    exit(0);
}

// --- Flush failed ---
if (isset($args['flush-failed'])) {
    Queue::flushFailed();
    echo "Failed jobs cleared.\n";
    exit(0);
}

// --- Worker loop ---
$queues = isset($args['queue']) ? explode(',', $args['queue']) : ['default', 'high', 'push', 'low'];
$once = isset($args['once']);
$stopAfter = isset($args['stop-after']) ? (int)$args['stop-after'] : 0;
$startTime = microtime(true);
$processed = 0;

echo date('Y-m-d H:i:s') . " — Queue worker started (queues: " . implode(', ', $queues) . ")\n";

// Graceful shutdown
$running = true;
pcntl_signal(SIGTERM, function () use (&$running) { $running = false; });
pcntl_signal(SIGINT, function () use (&$running) { $running = false; });

while ($running) {
    pcntl_signal_dispatch();

    // Release delayed jobs
    Queue::releaseDelayed();

    // Pop from queues (priority: high > default > push > low)
    $job = null;
    foreach ($queues as $q) {
        $job = Queue::pop($q, 2);
        if ($job) break;
    }

    if (!$job) {
        if ($once) break;
        continue;
    }

    $jobClass = $job['job'];

    if (!class_exists($jobClass)) {
        error_log("[Worker] Job class not found: {$jobClass}");
        Queue::fail($job, "Class not found: {$jobClass}");
        continue;
    }

    try {
        $reflection = new \ReflectionClass($jobClass);
        $constructor = $reflection->getConstructor();

        if ($constructor && !empty($job['data'])) {
            $params = $constructor->getParameters();
            $ctorArgs = [];
            foreach ($params as $param) {
                $name = $param->getName();
                if (isset($job['data'][$name])) {
                    $ctorArgs[] = $job['data'][$name];
                } elseif ($param->isDefaultValueAvailable()) {
                    $ctorArgs[] = $param->getDefaultValue();
                } else {
                    throw new \RuntimeException("Missing parameter: {$name}");
                }
            }
            $jobInstance = $reflection->newInstanceArgs($ctorArgs);
        } else {
            $jobInstance = new $jobClass();
        }

        $jobInstance->handle();
        $processed++;

        $elapsed = round((microtime(true) - $job['created_at']) * 1000);
        echo date('Y-m-d H:i:s') . " ✓ {$jobClass} ({$elapsed}ms)\n";

    } catch (\Throwable $e) {
        $job['attempts'] = ($job['attempts'] ?? 0) + 1;
        $maxTries = $jobInstance->maxTries();

        error_log("[Worker] {$jobClass} failed (attempt {$job['attempts']}/{$maxTries}): {$e->getMessage()}");

        if ($job['attempts'] < $maxTries) {
            $delay = $jobInstance->retryDelay();
            Queue::later($delay, $jobClass, $job['data'], $job['queue'] ?? 'default');
            echo date('Y-m-d H:i:s') . " ✗ {$jobClass} — retrying in {$delay}s (attempt {$job['attempts']}/{$maxTries})\n";
        } else {
            $jobInstance->failed($e);
            Queue::fail($job, $e->getMessage());
            echo date('Y-m-d H:i:s') . " ✗ {$jobClass} — FAILED after {$maxTries} attempts\n";
        }
    }

    if ($once) break;

    if ($stopAfter > 0 && (microtime(true) - $startTime) >= $stopAfter) {
        echo date('Y-m-d H:i:s') . " — Stop-after limit reached\n";
        break;
    }
}

Queue::disconnect();
echo date('Y-m-d H:i:s') . " — Queue worker stopped (processed: {$processed})\n";
