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

    public function findAll(int $userId, int $limit = 10, int $offset = 0): array
    {
        $sql = <<<'SQL'
        SELECT id, user_id, expires_at, last_used_at, created_at, updated_at 
        FROM access_tokens
        WHERE user_id = ?
        LIMIT ?
        OFFSET ?
        SQL;

        $result = $this->db->query($sql, [$userId, $limit, $offset])->fetchAll();

        return array_map(fn ($accessToken) => new AccessToken(
            id: $accessToken['id'],
            userId: $accessToken['user_id'],
            expiresAt: $accessToken['expires_at'],
            lastUsedAt: $accessToken['last_used_at'],
            createdAt: $accessToken['created_at'],
            updatedAt: $accessToken['updated_at'],
        ), $result);
    }

    public function count(int $userId)
    {
        $sql = <<<'SQL'
        SELECT COUNT(*) as total
        FROM access_tokens
        WHERE user_id = ?
        SQL;

        $params = [$userId];

        $result = $this->db->query($sql, $params)->fetch();

        return (int) $result['total'];
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

    public function updateLastUsedAt(int $id): void
    {
        $this->db->query('UPDATE access_tokens SET last_used_at = NOW() WHERE id = ?', [
            $id,
        ]);
    }
}
