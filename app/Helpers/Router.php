<?php
namespace App\Helpers;

class Router {
    private static array $routes = [];
    private static array $middleware = [];
    
    public static function load(array $routes): void {
        self::$routes = $routes;
    }
    
    public static function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';
        
        // Check exact match
        $key = "$method $uri";
        if (isset(self::$routes[$key])) {
            self::call(self::$routes[$key]);
            return;
        }
        
        // Check with trailing slash
        $key = "$method $uri/";
        if (isset(self::$routes[$key])) {
            self::call(self::$routes[$key]);
            return;
        }
        
        // Check parameterized routes
        foreach (self::$routes as $route => $handler) {
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = '#^' . preg_replace('/\s+/', ' ', preg_replace('/\//', '\\/', $pattern)) . '$#';
            
            if (preg_match($pattern, "$method $uri", $matches)) {
                $params = array_map(function($v) {
                    return ctype_digit($v) ? (int)$v : $v;
                }, array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
                self::call($handler, $params);
                return;
            }
        }
        
        http_response_code(404);
        require VIEW_PATH . '/layouts/404.php';
        exit;
    }
    
    private static function call(array $handler, array $params = []): void {
        [$controllerName, $action] = $handler;
        
        $namespace = str_contains($controllerName, '\\') ? $controllerName : "App\\Controllers\\{$controllerName}";
        
        if (!class_exists($namespace)) {
            throw new \RuntimeException("Controller {$namespace} not found.");
        }
        
        $controller = new $namespace();
        
        if (!method_exists($controller, $action)) {
            throw new \RuntimeException("Action {$action} not found in {$namespace}.");
        }
        
        call_user_func_array([$controller, $action], $params);
    }
    
    public static function baseUrl(): string {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? 0) == 443
            || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
            || ($_SERVER['HTTP_CF_VISITOR'] ?? '') === '{"scheme":"https"}'
            ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        return $scheme . '://' . $host;
    }

    public static function url(string $path): string {
        return rtrim(self::baseUrl(), '/') . '/' . ltrim($path, '/');
    }

    public static function redirect(string $url): void {
        header('Location: ' . self::url($url));
        exit;
    }
}
