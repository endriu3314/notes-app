<?php

require_once __DIR__.'/vendor/autoload.php';

use NotesApi\Config\EnvLoader;
use NotesApi\Database\MigrationManager;
use NotesApi\Database\MigrationsRegistrar;

EnvLoader::load(__DIR__.'/.env');

$registrar = new MigrationsRegistrar;
$registrar->autoloadMigrations();

$manager = new MigrationManager($registrar);

$command = $argv[1] ?? null;
switch ($command) {
    case 'up':
        $registrar->printMigrations();
        $manager->runMigrations();
        echo "\nMigrations completed successfully.\n";
        break;
    case 'down':
        $registrar->printMigrations();
        $manager->rollbackMigrations();
        echo "\nMigrations rolled back successfully.\n";
        break;
    default:
        echo "Usage: php migrate.php [up|down]\n";
        break;
}
