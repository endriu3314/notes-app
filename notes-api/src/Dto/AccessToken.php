<?php

namespace NotesApi\Dto;

class AccessToken
{
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly ?string $expiresAt,
        public readonly ?string $lastUsedAt,
        public readonly string $createdAt,
        public readonly ?string $updatedAt,
    ) {}
}
