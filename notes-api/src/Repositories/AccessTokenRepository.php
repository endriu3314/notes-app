<?php

namespace NotesApi\Repositories;

use NotesApi\Config\Database;
use NotesApi\Dto\AccessToken;

class AccessTokenRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?AccessToken
    {
        $result = $this->db->query('SELECT id, token, user_id, expires_at, last_used_at, created_at, updated_at FROM access_tokens WHERE id = ?', [
            $id,
        ])->fetch();

        if (! $result) {
            return null;
        }

        return new AccessToken(
            id: $result['id'],
            userId: $result['user_id'],
            expiresAt: $result['expires_at'],
            lastUsedAt: $result['last_used_at'],
            createdAt: $result['created_at'],
            updatedAt: $result['updated_at'],
        );
    }

    public function create(int $userId, string $token, string $expiresAt): AccessToken
    {
        $this->db->query('INSERT INTO access_tokens (user_id, token, expires_at) VALUES (?, ?, ?)', [
            $userId, $token, $expiresAt,
        ]);

        $lastInsertId = $this->db->getConnection()->lastInsertId();

        $accessToken = $this->findById($lastInsertId);
        if (! $accessToken) {
            throw new \Exception('Failed to create access token');
        }

        return $accessToken;
    }

    public function findByToken(string $token): ?AccessToken
    {
        $hashedToken = hash('sha256', $token);

        $result = $this->db->query('SELECT id, token,user_id, expires_at, last_used_at, created_at, updated_at FROM access_tokens WHERE token = ?', [
            $hashedToken,
        ])->fetch();

        if (! $result) {
            return null;
        }

        if (! hash_equals($result['token'], $hashedToken)) {
            return null;
        }

        return new AccessToken(
            id: $result['id'],
            userId: $result['user_id'],
            expiresAt: $result['expires_at'],
            lastUsedAt: $result['last_used_at'],
            createdAt: $result['created_at'],
            updatedAt: $result['updated_at'],
        );
    }

    public function delete(int $id): void
    {
        $this->db->query('DELETE FROM access_tokens WHERE id = ?', [
            $id,
        ]);
    }
}
