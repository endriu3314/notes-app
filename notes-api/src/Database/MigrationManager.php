<?php

namespace NotesApi\Database;

use NotesApi\Config\Database;

class MigrationManager
{
    private readonly Database $db;

    private readonly MigrationsRegistrar $registrar;

    private string $migrationsTable = 'migrations';

    public function __construct(MigrationsRegistrar $registrar)
    {
        $this->db = Database::getInstance();
        $this->registrar = $registrar;
        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->db->query($sql);
    }

    public function runMigrations(): void
    {
        $batch = $this->getNextBatchNumber();

        foreach ($this->registrar->getMigrations() as $identifier => $migration) {
            if (! $this->isMigrated($identifier)) {
                $this->runMigration($migration, $batch);
            }
        }
    }

    private function runMigration(Migration $migration, int $batch): void
    {
        echo 'Running migration: '.$migration->getIdentifier()."\n";

        $migration->up();

        $migrationIdentifier = $migration->getIdentifier();
        $sql = "INSERT INTO {$this->migrationsTable} (migration, batch) VALUES (?, ?)";
        $this->db->query($sql, [$migrationIdentifier, $batch]);
    }

    public function rollbackMigrations(): void
    {
        $batch = $this->getLastBatchNumber();
        $migrations = $this->getMigrationsByBatch($batch);

        foreach ($migrations as $migration) {
            $this->rollbackMigration($migration);
        }
    }

    private function rollbackMigration(string $migrationIdentifier): void
    {
        echo "Rolling back migration: {$migrationIdentifier}\n";

        $migration = $this->registrar->getMigration($migrationIdentifier);
        $migration->down();

        $sql = "DELETE FROM {$this->migrationsTable} WHERE migration = ?";
        $this->db->query($sql, [$migrationIdentifier]);
    }

    private function isMigrated(string $migrationIdentifier): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->migrationsTable} WHERE migration = ?";
        $result = $this->db->query($sql, [$migrationIdentifier])->fetch();

        return $result['count'] > 0;
    }

    private function getNextBatchNumber(): int
    {
        $sql = "SELECT MAX(batch) as max_batch FROM {$this->migrationsTable}";
        $result = $this->db->query($sql)->fetch();

        return ($result['max_batch'] ?? 0) + 1;
    }

    private function getLastBatchNumber(): int
    {
        $sql = "SELECT MAX(batch) as max_batch FROM {$this->migrationsTable}";
        $result = $this->db->query($sql)->fetch();

        return $result['max_batch'] ?? 0;
    }

    private function getMigrationsByBatch(int $batch): array
    {
        $sql = "SELECT migration FROM {$this->migrationsTable} WHERE batch = ? ORDER BY id DESC";
        $result = $this->db->query($sql, [$batch])->fetchAll();

        return array_column($result, 'migration');
    }
}
