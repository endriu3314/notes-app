<?php

namespace NotesApi\Repositories;

use NotesApi\Config\Database;
use NotesApi\Dto\User;

class UserRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?User
    {
        $result = $this->db->query('SELECT id, name, email, password, created_at, updated_at FROM users WHERE email = ?', [
            $email,
        ])->fetch();

        if (! $result) {
            return null;
        }

        return new User(
            id: $result['id'],
            name: $result['name'],
            email: $result['email'],
            password: $result['password'],
            createdAt: $result['created_at'],
            updatedAt: $result['updated_at'],
        );
    }

    public function findById(int $id): ?User
    {
        $result = $this->db->query('SELECT id, name, email, password, created_at, updated_at FROM users WHERE id = ?', [
            $id,
        ])->fetch();

        if (! $result) {
            return null;
        }

        return new User(
            id: $result['id'],
            name: $result['name'],
            email: $result['email'],
            password: $result['password'],
            createdAt: $result['created_at'],
            updatedAt: $result['updated_at'],
        );
    }

    public function create(string $email, string $password, string $name): User
    {
        $this->db->query('INSERT INTO users (email, password, name) VALUES (?, ?, ?)', [
            $email, $password, $name,
        ]);

        $lastInsertId = $this->db->getConnection()->lastInsertId();

        $user = $this->findById($lastInsertId);
        if (! $user) {
            throw new \Exception('Failed to create user');
        }

        return $user;
    }
}
