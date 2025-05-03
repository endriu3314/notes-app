<?php

require_once __DIR__.'/vendor/autoload.php';

use NotesApi\Config\EnvLoader;
use NotesApi\Database\MigrationManager;

EnvLoader::load(__DIR__.'/.env');

$migrations = [];
$migrationsDir = __DIR__.'/src/Database/Migrations';
$files = glob($migrationsDir.'/*.php');
foreach ($files as $file) {
    $migrationBasename = str_replace('.php', '',
        str_replace(__DIR__.'/src/Database/Migrations/', '', $file)
    );
    $migrationClassString = 'NotesApi\\Database\\Migrations\\'.$migrationBasename;
    $migration = new $migrationClassString;

    if ($migration instanceof \NotesApi\Database\Migration) {
        $migrations[] = $migration;
    }
}

function printMigrations($migrations)
{
    echo 'Registered migrations: '.count($migrations)."\n";
    foreach ($migrations as $migration) {
        echo $migration::class."\n";
    }
    echo "\n";
}

$manager = new MigrationManager;

$command = $argv[1] ?? null;
switch ($command) {
    case 'up':
        printMigrations($migrations);
        $manager->runMigrations($migrations);
        echo "\nMigrations completed successfully.\n";
        break;
    case 'down':
        printMigrations($migrations);
        $manager->rollbackMigrations();
        echo "\nMigrations rolled back successfully.\n";
        break;
    default:
        echo "Usage: php migrate.php [up|down]\n";
        break;
}
