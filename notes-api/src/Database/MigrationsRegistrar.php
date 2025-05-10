<?php

namespace NotesApi\Database;

class MigrationsRegistrar
{
    private array $migrations = [];

    public function autoloadMigrations(): array
    {
        $migrationsDir = __DIR__.'/../Database/Migrations';
        $files = glob($migrationsDir.'/*.php');

        foreach ($files as $file) {

            $migration = require $file;
            $this->migrations[$migration->getIdentifier()] = $migration;
        }

        return $this->migrations;
    }

    public function getMigrations(): array
    {
        return $this->migrations;
    }

    public function getMigration(string $identifier): Migration
    {
        return $this->migrations[$identifier];
    }

    public function printMigrations(): void
    {
        echo 'Registered migrations: '.count($this->migrations)."\n";
        foreach ($this->migrations as $identifier => $migration) {
            echo $identifier."\n";
        }
        echo "\n";
    }
}
