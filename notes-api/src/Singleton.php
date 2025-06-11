<?php

namespace NotesApi;

class Singleton
{
    private static array $instances = [];

    protected function __construct() {}

    protected function __clone() {}

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton');
    }

    public static function getInstance(): static
    {
        $sublcass = static::class;
        if (! isset(self::$instances[$sublcass])) {
            self::$instances[$sublcass] = new static;
        }

        return self::$instances[$sublcass];
    }
}
