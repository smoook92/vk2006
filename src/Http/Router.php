<?php
declare(strict_types=1);

namespace App\Http;

use Closure;

final class Router
{
    private array $dynamicRoutes = [];
    private array $routes = [];

    public function get(string $path, Closure|array $handler, array $middleware = []): void
    {
        $this->map('GET', $path, $handler, $middleware);
    }

    public function post(string $path, Closure|array $handler, array $middleware = []): void
    {
        $this->map('POST', $path, $handler, $middleware);
    }

    private function map(string $method, string $path, Closure|array $handler, array $middleware): void
    {
        if (str_contains($path, '{')) {
            // динамический маршрут
            $pattern = preg_replace('#\{(\w+):([^}]+)\}#', '(?P<$1>$2)', $path);
            $pattern = '#^' . $pattern . '$#';

            $this->dynamicRoutes[$method][] = [
                'pattern' => $pattern,
                'handler' => $handler,
                'middleware' => $middleware,
            ];
            return;
        }

        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri    = $request->getUri();

        $route = null;

        // 1. точные маршруты
        if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];
        } else {
            // 2. динамические маршруты
            foreach ($this->dynamicRoutes[$method] ?? [] as $r) {
                if (preg_match($r['pattern'], $uri, $m)) {
                    foreach ($m as $k => $v) {
                        if (!is_int($k)) {
                            $request->setAttribute($k, $v);
                        }
                    }
                    $route = $r;
                    break;
                }
            }
        }

        if (!$route) {
            return new Response('Not Found', 404);
        }

        $handler = $this->resolveHandler($route['handler']);

        $pipeline = array_reduce(
            array_reverse($route['middleware']),
            fn ($next, $mw) => fn ($req) => $mw->handle($req, $next),
            $handler
        );

        return $pipeline($request);
    }

    private function resolveHandler(Closure|array $handler): Closure
    {
        if ($handler instanceof Closure) {
            return $handler;
        }

        [$class, $method] = $handler;

        if (!$this->factory) {
            throw new \RuntimeException('ControllerFactory not set');
        }

        $controller = $this->factory->make($class);

        return fn (Request $r) => $controller->$method($r);
    }


    private ?ControllerFactory $factory = null;

    public function setControllerFactory(ControllerFactory $factory): void
    {
        $this->factory = $factory;
    }

}
