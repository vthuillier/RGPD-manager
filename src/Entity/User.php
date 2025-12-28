<?php
declare(strict_types=1);

namespace App\Entity;

class User
{
    public function __construct(
        public ?int $id,
        public string $email,
        public string $password,
        public string $name,
        public string $role = 'user',
        public ?string $createdAt = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['name'] ?? '',
            $data['role'] ?? 'user',
            $data['created_at'] ?? null
        );
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
