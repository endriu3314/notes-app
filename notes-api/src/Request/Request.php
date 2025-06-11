<?php

namespace NotesApi\Request;

use NotesApi\Dto\User;
use NotesApi\Singleton;

class Request extends Singleton
{
    private array $post;

    private array $get;

    private ?array $put = [];

    private array $server;

    private array $files;

    private array $cookies;

    private array $headers;

    private ?array $json = [];

    private array $flash = [];

    public ?User $user = null;

    protected function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
        $this->headers = getallheaders();

        if ($this->isJson()) {
            $rawInput = file_get_contents('php://input');
            $this->json = json_decode($rawInput, true) ?? [];
        }

        if ($this->isPut()) {
            $this->parsePut();
        }
    }

    private function isJson(): bool
    {
        $contentType = $this->header('Content-Type');

        return $contentType && strpos($contentType, 'application/json') !== false;
    }

    private function parsePut(): void
    {
        $contentType = $this->header('Content-Type');

        $rawData = file_get_contents('php://input');
        $data = [];

        if ($contentType && strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
            parse_str($rawData, $data);
        } elseif ($contentType && strpos($contentType, 'application/json') !== false) {
            $data = json_decode($rawData, true) ?? [];
        } else {
            throw new \Exception('Unsupported content type');
        }

        $this->put = $data;
    }

    public function json(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->json;
        }

        return $this->json[$key] ?? $default;
    }

    public function post(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->post;
        }

        return $this->post[$key] ?? $default;
    }

    public function get(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->get;
        }

        return $this->get[$key] ?? $default;
    }

    public function put(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->put;
        }

        return $this->put[$key] ?? $default;
    }

    public function server(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->server;
        }

        return $this->server[$key] ?? $default;
    }

    public function files(?string $key = null)
    {
        if ($key === null) {
            return $this->files;
        }

        return $this->files[$key] ?? null;
    }

    public function cookie(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->cookies;
        }

        return $this->cookies[$key] ?? $default;
    }

    public function header(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->headers;
        }

        return $this->headers[$key] ?? $default;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function method(): string
    {
        $method = $this->server('REQUEST_METHOD', 'GET');
        if ($method === 'POST' && $this->post('_method')) {
            return strtoupper($this->post('_method'));
        }
        return $method;
    }

    public function uri(): string
    {
        return parse_url($this->server('REQUEST_URI', '/'), PHP_URL_PATH);
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    public function isPut(): bool
    {
        return $this->method() === 'PUT';
    }

    public function all(): array
    {
        return array_merge($this->get(), $this->post(), $this->json(), $this->put());
    }

    public function input(?string $key = null, $default = null)
    {
        if ($key === null) {
            return array_merge($this->post(), $this->json(), $this->put());
        }

        return $this->post($key) ?? $this->json($key) ?? $this->put($key) ?? $default;
    }

    public function any(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->all();
        }

        return $this->get($key) ?? $this->post($key) ?? $this->json($key) ?? $this->put($key) ?? $default;
    }

    public function only(array $keys): array
    {
        $all = $this->all();

        return array_intersect_key($all, array_flip($keys));
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    public function user(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function redirect(string $url): never
    {
        if (! empty($this->flash)) {
            $_SESSION['_flash'] = $this->flash;
        }

        header('Location: '.$url);
        exit;
    }

    public function redirectBack(): never
    {
        if (! empty($this->flash)) {
            $_SESSION['_flash'] = $this->flash;
        }

        header('Location: '.$this->server('HTTP_REFERER', '/'));
        exit;
    }

    public function with(string $key, $value): self
    {
        $this->flash[$key] = $value;

        return $this;
    }

    public function withErrors(array $errors): self
    {
        $this->flash['errors'] = array_merge_recursive($this->flash['errors'] ?? [], $errors);

        return $this;
    }

    public static function getFlash(string $key, $default = null)
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);

        return $value;
    }
}
