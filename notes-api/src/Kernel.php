<?php

namespace NotesApi;

use NotesApi\Config\EnvLoader;
use NotesApi\Request\Request;

class Kernel
{
    public readonly Router $router;

    private readonly string $routesFile;

    public function __construct()
    {
        $this->router = new Router;
        $this->routesFile = __DIR__.'/../routes/routes.php';

        EnvLoader::load(__DIR__.'/../.env');
    }

    public function handle(): void
    {
        $this->router->loadRouteFile($this->routesFile);
        
        $response = $this->router->dispatch(Request::getInstance());

        echo $response;
    }
}
