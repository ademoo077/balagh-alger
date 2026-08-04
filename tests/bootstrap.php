<?php
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEW_PATH', APP_PATH . '/Views');
define('UPLOAD_PATH', PUBLIC_PATH . '/assets/uploads');

putenv('DB_HOST=localhost');
putenv('DB_NAME=balagh_alger_test');
putenv('DB_USER=balagh_user');
putenv('DB_PASS=BalaghPass2026!');
putenv('APP_KEY=balagh-alger-secret-key-2026');
putenv('APP_URL=http://balagh-alger.local');

require_once ROOT_PATH . '/vendor/autoload.php';
require_once APP_PATH . '/Helpers/Session.php';
require_once APP_PATH . '/Helpers/I18n.php';
require_once APP_PATH . '/Helpers/Csrf.php';
require_once APP_PATH . '/Helpers/Validator.php';
require_once APP_PATH . '/Helpers/Helper.php';
require_once APP_PATH . '/Helpers/Router.php';
require_once APP_PATH . '/Helpers/Database.php';

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
