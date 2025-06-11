<?php

namespace NotesApi\Middleware;

use NotesApi\Repositories\UserRepository;
use NotesApi\Request\Request;

class AuthMiddleware
{
    private readonly UserRepository $userRepository;

    private const SESSION_TIMEOUT = 1800;

    public function __construct()
    {
        $this->userRepository = new UserRepository;
    }

    public function __invoke(Request $request)
    {
        if (! isset($_SESSION['user_id']) || ! isset($_SESSION['session_token']) || ! isset($_SESSION['last_activity'])) {
            $this->clearSession();

            return $request->redirect('/auth/login');
        }

        if (time() - $_SESSION['last_activity'] > self::SESSION_TIMEOUT) {
            $this->clearSession();

            return $request->redirect('/auth/login');
        }
        $_SESSION['last_activity'] = time();

        $user = $this->userRepository->findById($_SESSION['user_id']);
        if (! $user) {
            $this->clearSession();

            return $request->redirect('/auth/login');
        }

        if (! hash_equals($user->sessionToken ?? '', $_SESSION['session_token'])) {
            $this->clearSession();

            return $request->redirect('/auth/login');
        }

        $request->setUser($user);
    }

    private function clearSession(): void
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['session_token']);
        unset($_SESSION['last_activity']);
        session_destroy();
    }
}
