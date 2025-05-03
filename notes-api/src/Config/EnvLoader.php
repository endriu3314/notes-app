<?php

namespace NotesApi\Config;

class EnvLoader
{
    public static function load(string $filePath)
    {
        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                putenv($line);
            }
        }
    }
}
