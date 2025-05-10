<?php

use NotesApi\Controllers\Api\AuthController;
use NotesApi\Controllers\Api\NotesController;
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

    $router->group('/notes', function (Router $router) {
        $router->middleware(new ApiAuthMiddleware);

        $router->get('', [NotesController::class, 'index']);
        $router->get('/{id}', [NotesController::class, 'show']);
        $router->post('', [NotesController::class, 'create']);
        $router->put('/{id}', [NotesController::class, 'update']);
        $router->delete('/{id}', [NotesController::class, 'destroy']);

        $router->post('/{id}/authorize', [NotesController::class, 'authorize']);
        $router->delete('/{id}/authorize/{userId}', [NotesController::class, 'unauthorize']);
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
