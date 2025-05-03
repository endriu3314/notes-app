<?php

namespace NotesApi\Controllers\Api;

use NotesApi\Repositories\AccessTokenRepository;
use NotesApi\Repositories\UserRepository;
use NotesApi\Request\InputSanitizer;
use NotesApi\Request\Request;
use NotesApi\Request\ResponseBuilder;

class AuthController
{
    private readonly UserRepository $userRepository;

    private readonly AccessTokenRepository $accessTokenRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository;
        $this->accessTokenRepository = new AccessTokenRepository;
    }

    public function me(Request $request)
    {
        // this should be handled by the middleware
        if (! $request->user()) {
            return ResponseBuilder::buildJsonResponse(['error' => 'Unauthorized'], 401);
        }

        return ResponseBuilder::buildJsonResponse([
            'message' => 'User authenticated',
            'user' => $request->user(),
        ]);
    }

    public function register(Request $request)
    {
        ['email' => $email, 'password' => $password, 'name' => $name] = $request->only(['email', 'password', 'name']);

        if (! $email || ! $password || ! $name) {
            return ResponseBuilder::buildJsonResponse(['error' => 'Email, password and name are required'], 400);
        }

        $email = InputSanitizer::sanitize($email);
        $password = InputSanitizer::sanitize($password);
        $name = InputSanitizer::sanitize($name);

        $user = $this->userRepository->findByEmail($email);
        if ($user) {
            return ResponseBuilder::buildJsonResponse(['error' => 'User already exists'], 400);
        }

        $this->userRepository->create($email, password_hash($password, PASSWORD_DEFAULT), $name);

        return ResponseBuilder::buildJsonResponse(['message' => 'User created successfully', 'user' => $user]);
    }

    public function login(Request $request)
    {
        ['email' => $email, 'password' => $password] = $request->only(['email', 'password']);

        if (! $email || ! $password) {
            return ResponseBuilder::buildJsonResponse(['error' => 'Email and password are required'], 400);
        }

        $email = InputSanitizer::sanitize($email);
        $password = InputSanitizer::sanitize($password);

        $user = $this->userRepository->findByEmail($email);

        if (! $user) {
            return ResponseBuilder::buildJsonResponse(['error' => 'User not found'], 404);
        }

        if (! password_verify($password, $user->password)) {
            return ResponseBuilder::buildJsonResponse(['error' => 'Invalid password'], 401);
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $accessToken = $this->accessTokenRepository->create($user->id, hash('sha256', $token), $expiresAt);

        return ResponseBuilder::buildJsonResponse([
            'message' => 'Login successful',
            'token' => $token,
            'expiresAt' => $accessToken->expiresAt,
        ]);
    }
}
