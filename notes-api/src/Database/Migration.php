<?php

namespace NotesApi\Database;

use NotesApi\Config\Database;
use PDOStatement;

abstract class Migration
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    abstract public function up();

    abstract public function down();

    public function execute($sql): bool|PDOStatement
    {
        return $this->db->query($sql);
    }

    public function createTable(string $tableName, array $columns): bool|PDOStatement
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$tableName} (";
        $sql .= implode(', ', $columns);
        $sql .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

        return $this->execute($sql);
    }

    public function dropTable(string $tableName): bool|PDOStatement
    {
        return $this->execute("DROP TABLE IF EXISTS {$tableName}");
    }

    public function addColumn(string $tableName, string $columnName, string $definition): bool|PDOStatement
    {
        return $this->execute("ALTER TABLE {$tableName} ADD COLUMN {$columnName} {$definition}");
    }

    public function dropColumn(string $tableName, string $columnName): bool|PDOStatement
    {
        return $this->execute("ALTER TABLE {$tableName} DROP COLUMN {$columnName}");
    }
}
