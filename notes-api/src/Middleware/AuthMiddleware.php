<?php

namespace NotesApi\Middleware;

use NotesApi\Request\ResponseBuilder;

class AuthMiddleware
{
    public function __invoke()
    {
        echo ResponseBuilder::buildJsonResponse(['error' => 'Unauthorized'], 401);
        exit;
    }
}
