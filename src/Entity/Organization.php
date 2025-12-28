<?php
declare(strict_types=1);

namespace App\Entity;

class Organization
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?string $createdAt = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? '',
            $data['created_at'] ?? null
        );
    }
}
