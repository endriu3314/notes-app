<?php

namespace NotesApi\Repositories;

use NotesApi\Config\Database;
use NotesApi\Dto\Note;
use NotesApi\Dto\User;

class NoteRepository
{
    private readonly Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll(int $userId, bool $includeAuthorized = true, int $limit = 10, int $offset = 0): array
    {
        $sql = <<<'SQL'
        SELECT
            n.id,
            n.user_id,
            u.id AS owner_id,
            u.name AS owner_name,
            u.email AS owner_email,
            u.created_at AS owner_created_at,
            u.updated_at AS owner_updated_at,
            n.title,
            n.content,
            n.created_at,
            n.updated_at
        FROM notes n
        INNER JOIN users u ON n.user_id = u.id
        WHERE n.user_id = ?
        SQL;

        $params = [$userId];

        if ($includeAuthorized) {
            $sql .= ' '.<<<'SQL'
            OR EXISTS (
                SELECT 1
                FROM user_notes un
                WHERE un.note_id = n.id AND un.user_id = ?
            )
            SQL;

            $params[] = $userId;
        }

        $sql .= ' ORDER BY n.created_at DESC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;

        $result = $this->db->query($sql, $params)->fetchAll();

        return array_map(fn ($note) => new Note(
            id: $note['id'],
            userId: $note['user_id'],
            user: new User(
                id: $note['owner_id'],
                name: $note['owner_name'],
                email: $note['owner_email'],
                createdAt: $note['owner_created_at'],
                updatedAt: $note['owner_updated_at'],
            ),
            title: $note['title'],
            content: $note['content'],
            createdAt: $note['created_at'],
            updatedAt: $note['updated_at'],
        ), $result);
    }

    public function count(int $userId, bool $includeAuthorized = true): int
    {
        $sql = <<<'SQL'
        SELECT COUNT(*) as total
        FROM notes n
        WHERE n.user_id = ?
        SQL;

        $params = [$userId];

        if ($includeAuthorized) {
            $sql .= ' '.<<<'SQL'
            OR EXISTS (
                SELECT 1
                FROM user_notes un
                WHERE un.note_id = n.id AND un.user_id = ?
            )
            SQL;

            $params[] = $userId;
        }

        $result = $this->db->query($sql, $params)->fetch();

        return (int) $result['total'];
    }

    public function findById(int $id): ?Note
    {
        $result = $this->db->query(<<<'SQL'
        SELECT
            n.id,
            n.user_id,
            u.id AS owner_id,
            u.name AS owner_name,
            u.email AS owner_email,
            u.created_at AS owner_created_at,
            u.updated_at AS owner_updated_at,
            n.title,
            n.content,
            n.created_at,
            n.updated_at
        FROM notes n
        INNER JOIN users u ON n.user_id = u.id
        WHERE n.id = ?
        SQL, [$id])->fetch();

        if (! $result) {
            return null;
        }

        $authorizedUsers = $this->db->query(<<<'SQL'
        SELECT
            u.id,
            u.name,
            u.email,
            u.created_at,
            u.updated_at
        FROM users u
        INNER JOIN user_notes un ON un.user_id = u.id AND un.note_id = ?
        SQL, [$id])->fetchAll();

        return new Note(
            user: new User(
                id: $result['owner_id'],
                name: $result['owner_name'],
                email: $result['owner_email'],
                createdAt: $result['owner_created_at'],
                updatedAt: $result['owner_updated_at'],
            ),
            authorizedUsers: array_map(fn ($user) => new User(
                id: $user['id'],
                name: $user['name'],
                email: $user['email'],
                createdAt: $user['created_at'],
                updatedAt: $user['updated_at'],
            ), $authorizedUsers),
            id: $result['id'],
            userId: $result['user_id'],
            title: $result['title'],
            content: $result['content'],
            createdAt: $result['created_at'],
            updatedAt: $result['updated_at'],
        );
    }

    public function checkAuthorization(int $noteId, int $userId): bool
    {
        $result = $this->db->query(<<<'SQL'
        SELECT EXISTS (
            SELECT 1 FROM user_notes WHERE note_id = ? AND user_id = ?
            UNION
            SELECT 1 FROM notes WHERE id = ? AND user_id = ?
        ) AS `exists`
        SQL, [$noteId, $userId, $noteId, $userId])->fetch();

        return (bool) $result['exists'];
    }

    public function authorize(int $noteId, int $userId): void
    {
        $this->db->query(<<<'SQL'
        INSERT INTO user_notes (note_id, user_id) VALUES (?, ?)
        SQL, [$noteId, $userId]);
    }

    public function unauthorize(int $noteId, int $userId): void
    {
        $this->db->query(<<<'SQL'
        DELETE FROM user_notes WHERE note_id = ? AND user_id = ?
        SQL, [$noteId, $userId]);
    }

    public function create(int $userId, string $title, string $content): Note
    {
        $this->db->query(<<<'SQL'
        INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)
        SQL, [$userId, $title, $content]);

        $lastInsertId = $this->db->getConnection()->lastInsertId();

        $note = $this->findById($lastInsertId);
        if (! $note) {
            throw new \Exception('Failed to create note');
        }

        return $note;
    }

    public function update(int $id, string $title, string $content): Note
    {
        $this->db->query(<<<'SQL'
        UPDATE notes SET title = ?, content = ? WHERE id = ?
        SQL, [$title, $content, $id]);

        return $this->findById($id);
    }

    public function delete(int $id): void
    {
        $this->db->query(<<<'SQL'
        DELETE FROM notes WHERE id = ?
        SQL, [$id]);
    }
}
