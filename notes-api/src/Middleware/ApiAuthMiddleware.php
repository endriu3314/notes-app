<?php

namespace NotesApi\Middleware;

use NotesApi\Repositories\AccessTokenRepository;
use NotesApi\Repositories\UserRepository;
use NotesApi\Request\Request;
use NotesApi\Request\ResponseBuilder;

class ApiAuthMiddleware
{
    private AccessTokenRepository $accessTokenRepository;

    private UserRepository $userRepository;

    public function __construct(
    ) {
        $this->accessTokenRepository = new AccessTokenRepository;
        $this->userRepository = new UserRepository;
    }

    public function __invoke(Request $request): void
    {
        $apiKey = $request->header('Authorization');

        if (! $apiKey) {
            echo ResponseBuilder::buildJsonResponse(['error' => 'Unauthorized'], 401);
            exit;
        }

        $apiKey = str_replace('Bearer ', '', $apiKey);

        $accessToken = $this->accessTokenRepository->findByToken($apiKey);
        if (! $accessToken) {
            echo ResponseBuilder::buildJsonResponse(['error' => 'Unauthorized'], 401);
            exit;
        }

        if (strtotime($accessToken->expiresAt) < time()) {
            echo ResponseBuilder::buildJsonResponse(['error' => 'Unauthorized'], 401);
            $this->accessTokenRepository->delete($accessToken->id);
            exit;
        }

        $user = $this->userRepository->findById($accessToken->userId);
        if (! $user) {
            echo ResponseBuilder::buildJsonResponse(['error' => 'Unauthorized'], 401);
            exit;
        }

        $this->accessTokenRepository->updateLastUsedAt($accessToken->id);

        $request->setUser($user);
    }
}
