<?php

namespace NotesApi\Middleware;

use NotesApi\Request\Request;
use NotesApi\Request\ResponseBuilder;

class CsrfMiddleware
{
    private const TOKEN_NAME = 'csrf_token';

    private const TOKEN_LENGTH = 32;

    public function __invoke(Request $request): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->generateToken($request);

            return;
        }

        if (! $this->validateToken($request)) {
            echo ResponseBuilder::buildJsonResponse(['error' => 'Invalid CSRF token'], 403);
            exit;
        }
    }

    private function generateToken(Request $request): void
    {
        $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(self::TOKEN_LENGTH));
    }

    private function validateToken(Request $request): bool
    {
        $token = $request->input(self::TOKEN_NAME);
        $sessionToken = $_SESSION[self::TOKEN_NAME];

        if (! $token || ! $sessionToken) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }
}
