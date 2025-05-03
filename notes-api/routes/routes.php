<?php

use NotesApi\Controllers\Api\AuthController;
use NotesApi\Middleware\ApiAuthMiddleware;
use NotesApi\Middleware\ApiMiddleware;
use NotesApi\Middleware\AuthMiddleware;
use NotesApi\Middleware\CorsMiddleware;
use NotesApi\Request\ResponseBuilder;
use NotesApi\Router;
use NotesApi\TemplateRenderer;

$router = $kernel->router;

$router->middleware(new CorsMiddleware);

$router->group('/api', function (Router $router) {
    $router->middleware(new ApiMiddleware);

    $router->group('/auth', function (Router $router) {
        $router->post('/register', [AuthController::class, 'register']);
        $router->post('/login', [AuthController::class, 'login']);
    });

    $router->group('/auth', function (Router $router) {
        $router->middleware(new ApiAuthMiddleware);

        $router->get('/me', [AuthController::class, 'me']);
    });
});

$router->group('/app', function (Router $router) {
    $router->middleware(new AuthMiddleware);

    $router->get('/dashboard', function () {
        return ResponseBuilder::buildViewResponse(
            TemplateRenderer::renderTemplate('dashboard', [
                'title' => 'Dashboard',
            ])
        );
    });
});
