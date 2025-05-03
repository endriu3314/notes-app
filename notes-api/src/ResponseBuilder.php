<?php

namespace NotesApi;

class ResponseBuilder
{
    public static function buildViewResponse(string $content, int $status = 200): string
    {
        header('Content-Type: text/html');
        http_response_code($status);

        return $content;
    }

    public static function buildJsonResponse(array $data, int $status = 200): string
    {
        header('Content-Type: application/json');
        http_response_code($status);

        return json_encode($data);
    }
}
