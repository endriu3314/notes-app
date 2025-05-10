<?php

namespace NotesApi\Dto;

class Note
{
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly string $title,
        public readonly string $content,
        public readonly string $createdAt,
        public readonly string $updatedAt,
        public readonly ?User $user = null,
        public readonly ?array $authorizedUsers = null,
    ) {}
}
