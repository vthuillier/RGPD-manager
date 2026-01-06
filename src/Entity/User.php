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
        public ?int $organizationId = null,
        public ?string $createdAt = null,
        public ?string $resetToken = null,
        public ?string $resetExpiresAt = null
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
            $data['organization_id'] ?? null,
            $data['created_at'] ?? null,
            $data['reset_token'] ?? null,
            $data['reset_expires_at'] ?? null
        );
    }


    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
