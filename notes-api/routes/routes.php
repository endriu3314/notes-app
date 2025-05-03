<?php

use NotesApi\Middleware\ApiMiddleware;
use NotesApi\Middleware\AuthMiddleware;
use NotesApi\Middleware\CorsMiddleware;
use NotesApi\ResponseBuilder;
use NotesApi\Router;
use NotesApi\TemplateRenderer;

$router = $kernel->router;

$router->middleware(new CorsMiddleware);

$router->group('/api', function (Router $router) {
    $router->middleware(new ApiMiddleware);

    $router->get('/notes', function () {
        return ResponseBuilder::buildJsonResponse(['notes' => ['Note 1', 'Note 2']]);
    });

    $router->get('/notes/{id}', function ($id) {
        return ResponseBuilder::buildJsonResponse(['note' => "Note with ID: $id"]);
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
