<?php
declare(strict_types=1);

namespace App\Entity;

class Subprocessor
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public string $name,
        public string $service,
        public string $location,
        public ?string $guarantees,
        public ?int $organizationId = null,
        public ?string $createdAt = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            (int) ($data['user_id'] ?? 0),
            $data['name'] ?? '',
            $data['service'] ?? '',
            $data['location'] ?? '',
            $data['guarantees'] ?? '',
            isset($data['organization_id']) ? (int) $data['organization_id'] : null,
            $data['created_at'] ?? null
        );
    }

}
