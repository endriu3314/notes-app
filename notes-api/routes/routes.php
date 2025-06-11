<?php

use NotesApi\Controllers\Api\AuthController as ApiAuthController;
use NotesApi\Controllers\Api\NotesController as ApiNotesController;
use NotesApi\Controllers\Auth\AuthController;
use NotesApi\Controllers\NotesController;
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

        $router->get('', [ApiNotesController::class, 'index']);
        $router->get('/{id}', [ApiNotesController::class, 'show']);
        $router->post('', [ApiNotesController::class, 'create']);
        $router->put('/{id}', [ApiNotesController::class, 'update']);
        $router->delete('/{id}', [ApiNotesController::class, 'destroy']);

        $router->post('/{id}/authorize', [ApiNotesController::class, 'authorize']);
        $router->delete('/{id}/authorize/{userId}', [ApiNotesController::class, 'unauthorize']);
    });
});

$router->middleware(new SessionAwareMiddleware);
$router->middleware(new CsrfMiddleware);

$router->get('/', function (Request $request) {
    return $request->redirect('/app/dashboard');
});

$router->group('/auth', function (Router $router) {
    $router->get('/login', [AuthController::class, 'login']);
    $router->post('/login', [AuthController::class, 'authenticate']);

    $router->get('/register', [AuthController::class, 'register']);
    $router->post('/register', [AuthController::class, 'authenticateRegister']);

    $router->middleware(new AuthMiddleware);
    $router->post('/logout', [AuthController::class, 'logout']);
});

$router->group('/app', function (Router $router) {
    $router->middleware(new AuthMiddleware);

    $router->get('/dashboard', function ($request) {
        return ResponseBuilder::buildViewResponse(
            TemplateRenderer::renderTemplate('dashboard', [
                'title' => 'Dashboard',
            ])
        );
    });

    $router->group('/notes', function (Router $router) {
        $router->get('', [NotesController::class, 'index']);
        $router->get('/create', [NotesController::class, 'create']);
        $router->post('/create', [NotesController::class, 'store']);
        $router->get('/{id}', [NotesController::class, 'show']);
        $router->put('/{id}', [NotesController::class, 'update']);
        $router->delete('/{id}', [NotesController::class, 'destroy']);

        $router->post('/{id}/authorize', [NotesController::class, 'authorize']);
        $router->delete('/{id}/unauthorize/{userId}', [NotesController::class, 'unauthorize']);
    });
});
