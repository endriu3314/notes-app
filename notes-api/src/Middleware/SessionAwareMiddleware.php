<?php

namespace NotesApi\Middleware;

use NotesApi\Request\Request;

class SessionAwareMiddleware
{
    public function __invoke(Request $request): void
    {
        session_start([
            'cookie_httponly' => true,
            'cookie_secure' => false,
            'cookie_samesite' => 'Lax',
        ]);
    }
}
