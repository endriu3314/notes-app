<?php

namespace NotesApi;

class Kernel
{
    public readonly Router $router;

    private readonly string $routesFile;

    public function __construct()
    {
        $this->router = new Router;
        $this->routesFile = __DIR__.'/../routes/routes.php';
    }

    public function handle(): void
    {
        if (file_exists($this->routesFile)) {
            $kernel = $this;
            require $this->routesFile;
        }

        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $response = $this->router->dispatch($method, $uri);

        echo $response;
    }
}
