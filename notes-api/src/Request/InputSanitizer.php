<?php

namespace NotesApi;

class InputSanitizer
{
    public static function sanitize(string $input)
    {
        return strval(strip_tags(htmlspecialchars(trim(filter_var($input)), ENT_QUOTES, 'UTF-8')));
    }

    public static function sanitizeInt(string $input): int
    {
        return intval(self::sanitize($input));
    }

    public static function sanitizeFloat(string $input): float
    {
        return floatval(self::sanitize($input));
    }

    public static function sanitizeBool(string $input): bool
    {
        return boolval(self::sanitize($input));
    }

    public static function sanitizeArray(array $input)
    {
        return array_map(function ($value) {
            return self::sanitize($value);
        }, $input);
    }
}
