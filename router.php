<?php
/**
 * HTTP Router
 * File: router.php
 */

class Router {
    private $routes = [];

    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }

    private function addRoute($method, $path, $callback) {
        $this->routes[] = ['method' => $method, 'path' => $path, 'callback' => $callback];
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route['path']);
                $pattern = '#^' . $pattern . '$#';
                if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches);
                    call_user_func_array($route['callback'], $matches);
                    return;
                }
            }
        }
        http_response_code(404);
        echo "404 Not Found";
    }
}
