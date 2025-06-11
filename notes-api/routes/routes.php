<?php

use NotesApi\Controllers\Api\AuthController as ApiAuthController;
use NotesApi\Controllers\Api\NotesController;
use NotesApi\Controllers\Auth\AuthController;
use NotesApi\Middleware\ApiAuthMiddleware;
use NotesApi\Middleware\ApiMiddleware;
use NotesApi\Middleware\AuthMiddleware;
use NotesApi\Middleware\CorsMiddleware;
use NotesApi\Middleware\CsrfMiddleware;
use NotesApi\Middleware\SessionAwareMiddleware;
use NotesApi\Request\Request;
use NotesApi\Request\ResponseBuilder;
use NotesApi\Router;
use NotesApi\TemplateRenderer;

$router = $this;

$router->middleware(new CorsMiddleware);

$router->group('/api', function (Router $router) {
    $router->middleware(new ApiMiddleware);

    $router->group('/auth', function (Router $router) {
        $router->post('/register', [ApiAuthController::class, 'register']);
        $router->post('/login', [ApiAuthController::class, 'login']);
    });

    $router->group('/auth', function (Router $router) {
        $router->middleware(new ApiAuthMiddleware);

        $router->get('/me', [ApiAuthController::class, 'me']);
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

$router->middleware(new SessionAwareMiddleware);

$router->get('/', function (Request $request) {
    return $request->redirect('/app/dashboard');
});

$router->group('/auth', function (Router $router) {
    $router->middleware(new CsrfMiddleware);

    $router->get('/login', [AuthController::class, 'login']);
    $router->post('/login', [AuthController::class, 'authenticate']);

    $router->get('/register', [AuthController::class, 'register']);
    $router->post('/register', [AuthController::class, 'authenticateRegister']);

    $router->middleware(new AuthMiddleware);
    $router->post('/logout', [AuthController::class, 'logout']);
});

$router->group('/app', function (Router $router) {
    $router->middleware(new AuthMiddleware);
    $router->middleware(new CsrfMiddleware);

    $router->get('/dashboard', function ($request) {
        // dd($request->user());
        return ResponseBuilder::buildViewResponse(
            TemplateRenderer::renderTemplate('dashboard', [
                'title' => 'Dashboard',
            ])
        );
    });
});
