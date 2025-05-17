<?php

namespace NotesApi\Controllers\Auth;

use NotesApi\Repositories\UserRepository;
use NotesApi\Request\InputSanitizer;
use NotesApi\Request\Request;
use NotesApi\Request\ResponseBuilder;
use NotesApi\TemplateRenderer;

class AuthController
{
    private readonly UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository;
    }

    public function login(Request $request)
    {
        return ResponseBuilder::buildViewResponse(
            TemplateRenderer::renderTemplate('auth/login', [
                'title' => 'Login',
                'errors' => $request->getFlash('errors', []),
            ], 'auth')
        );
    }

    public function authenticate(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (! $email) {
            return $request
                ->withErrors(['email' => 'Email is required'])
                ->with('email', $email)
                ->with('password', $password)
                ->redirectBack();
        }

        if (! $password) {
            return $request
                ->withErrors(['password' => 'Password is required'])
                ->with('email', $email)
                ->with('password', $password)
                ->redirectBack();
        }

        $email = InputSanitizer::sanitize($email);
        $password = InputSanitizer::sanitize($password);

        $user = $this->userRepository->findByEmail($email);
        if (! $user) {
            return $request
                ->withErrors(['password' => 'Invalid email or password'])
                ->with('email', $email)
                ->with('password', $password)
                ->redirectBack();
        }

        if (! password_verify($password, $user->password)) {
            return $request
                ->withErrors(['password' => 'Invalid email or password'])
                ->with('email', $email)
                ->with('password', $password)
                ->redirectBack();
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['session_token'] = bin2hex(random_bytes(32));
        $_SESSION['last_activity'] = time();

        $this->userRepository->updateSessionToken($user->id, $_SESSION['session_token']);

        return $request->redirect('/app/dashboard');
    }

    public function register(Request $request)
    {
        return ResponseBuilder::buildViewResponse(
            TemplateRenderer::renderTemplate('auth/register', [
                'title' => 'Register',
                'errors' => $request->getFlash('errors', []),
            ], 'auth')
        );
    }

    public function authenticateRegister(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        if (! $name) {
            return $request
                ->withErrors(['name' => 'Name is required'])
                ->with('email', $email)
                ->with('password', $password)
                ->with('name', $name)
                ->redirectBack();
        }

        if (! $email) {
            return $request
                ->withErrors(['email' => 'Email is required'])
                ->with('email', $email)
                ->with('password', $password)
                ->with('name', $name)
                ->redirectBack();
        }

        if (! $password) {
            return $request
                ->withErrors(['password' => 'Password is required'])
                ->with('email', $email)
                ->with('password', $password)
                ->with('name', $name)
                ->redirectBack();
        }

        $name = InputSanitizer::sanitize($name);
        $email = InputSanitizer::sanitize($email);
        $password = InputSanitizer::sanitize($password);

        $user = $this->userRepository->findByEmail($email);
        if ($user) {
            return $request
                ->withErrors(['email' => 'Email already exists'])
                ->with('email', $email)
                ->with('password', $password)
                ->with('name', $name)
                ->redirectBack();
        }

        $user = $this->userRepository->create($email, password_hash($password, PASSWORD_DEFAULT), $name);

        return $request->redirect('/auth/login');
    }

    public function logout(Request $request)
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['session_token']);
        unset($_SESSION['last_activity']);
        session_destroy();

        $this->userRepository->clearSessionToken($request->user()->id);

        return $request->redirect('/auth/login');
    }
}
