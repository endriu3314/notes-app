<?php

namespace NotesApi;

use NotesApi\Request\DumpRequestToFile;
use NotesApi\Request\Request;
use NotesApi\Request\ResponseBuilder;

class Router
{
    private array $routes = [];

    private string $prefix = '';

    private array $middleware = [];

    private array $groupMiddlewareStack = [];

    public function __construct()
    {
        $this->routes = [
            'GET' => [],
            'POST' => [],
            'PUT' => [],
            'DELETE' => [],
        ];
    }

    public function group(string $prefix, callable $callback): void
    {
        $previousPrefix = $this->prefix;
        $this->prefix .= $prefix;

        $previousMiddleware = $this->middleware;
        $this->groupMiddlewareStack[] = $previousMiddleware;

        $callback($this);

        $this->middleware = array_pop($this->groupMiddlewareStack);
        $this->prefix = $previousPrefix;
    }

    public function middleware(callable $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function get(string $path, mixed $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, mixed $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, mixed $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, mixed $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, mixed $handler): void
    {
        $fullPath = $this->prefix.$path;
        $this->routes[$method][$fullPath] = [
            'handler' => $handler,
            'middleware' => $this->middleware,
        ];
    }

    public function dispatch(string $method, string $uri): string
    {
        $method = strtoupper($method);

        if (! isset($this->routes[$method])) {
            return $this->notFound();
        }

        $request = new Request;

        $dumpRequestToFile = new DumpRequestToFile;
        $dumpRequestToFile->execute($request);

        foreach ($this->routes[$method] as $route => $data) {
            $pattern = $this->convertToPattern($route);

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                foreach ($data['middleware'] as $middleware) {
                    if (is_callable($middleware)) {
                        $middleware($request);
                    }
                }

                if (is_array($data['handler'])) {
                    $controller = new $data['handler'][0];
                    $method = $data['handler'][1];

                    if (is_callable([$controller, $method])) {
                        return call_user_func_array([$controller, $method], [
                            $request,
                            ...$params,
                        ]);
                    }
                }

                if (is_callable($data['handler'])) {
                    return call_user_func_array($data['handler'], [
                        $request,
                        ...$params,
                    ]);
                }
            }
        }

        return $this->notFound();
    }

    private function convertToPattern(string $route): string
    {
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $route);

        return '#^'.$pattern.'$#';
    }

    private function notFound(): string
    {
        return ResponseBuilder::buildJsonResponse(['error' => 'Not Found'], 404);
    }
}
