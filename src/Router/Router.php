<?php
declare(strict_types=1);

namespace App\Router;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handle): void
    {
        $this->routes['GET'][$path] = $handle;
    }

    public function post(string $path, callable $handle): void
    {
        $this->routes['POST'][$path] = $handle;
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (isset($this->routes[$method][$uri])) {
            $handle = $this->routes[$method][$uri];
            $handle();
        } else {
            http_response_code(404);
            echo json_encode([
                'error' => 'Not Found'
            ]);
        }
    }
}