<?php

namespace NotesApi\Middleware;

class ApiMiddleware
{
    public function __invoke()
    {
        header('Content-Type: application/json');
    }
}
