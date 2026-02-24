<?php

namespace App\Http;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable|string $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                $handler = $route['handler'];

                if (is_callable($handler)) {
                    $handler();
                    return;
                }

                if (is_string($handler)) {
                    [$controller, $action] = explode('@', $handler);
                    $controller = "App\\Http\\Controllers\\$controller";
                    $instance = new $controller();
                    $instance->$action();
                    return;
                }
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
