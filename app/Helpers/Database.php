<?php
namespace App\Helpers;

class Database {
    private static ?\PDO $instance = null;

    public static function getConnection(): \PDO {
        if (self::$instance === null) {
            $config = require APP_PATH . '/Config/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
            self::$instance = new \PDO($dsn, $config['user'], $config['pass'], $config['options']);
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args) {
        return static::getConnection()->$method(...$args);
    }
}
