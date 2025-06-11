<?php

namespace NotesApi\Dto;

use SensitiveParameter;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $name,
        public readonly string $createdAt,
        public readonly ?string $updatedAt,
        #[SensitiveParameter]
        public readonly string $password = '',
        #[SensitiveParameter]
        public readonly ?string $sessionToken = null,
    ) {}
}
