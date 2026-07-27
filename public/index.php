<?php
define('APP_START', microtime(true));

// Load .env file
$envFile = __DIR__ . '/../.env';
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

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/paths.php';
require_once APP_PATH . '/Helpers/Session.php';
require_once APP_PATH . '/Helpers/Database.php';
require_once APP_PATH . '/Helpers/Router.php';
require_once APP_PATH . '/Helpers/Csrf.php';
require_once APP_PATH . '/Helpers/Validator.php';
require_once APP_PATH . '/Helpers/Helper.php';
require_once APP_PATH . '/Helpers/AuditLog.php';
require_once APP_PATH . '/Helpers/Notification.php';
require_once APP_PATH . '/Helpers/I18n.php';
require_once APP_PATH . '/Controllers/Controller.php';

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

\App\Helpers\Session::start();

$config = require APP_PATH . '/Config/app.php';
date_default_timezone_set($config['timezone']);

$webRoutes = require APP_PATH . '/Routes/web.php';
\App\Helpers\Router::load($webRoutes);
\App\Helpers\Router::dispatch();
