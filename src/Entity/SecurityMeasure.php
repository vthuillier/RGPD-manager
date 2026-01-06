<?php
declare(strict_types=1);

namespace App\Entity;

class SecurityMeasure
{
    public function __construct(
        public ?int $id,
        public string $category,
        public string $name,
        public ?string $description,
        public int $weight = 1,
        public ?int $organizationId = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['category'] ?? '',
            $data['name'] ?? '',
            $data['description'] ?? null,
            (int) ($data['weight'] ?? 1),
            isset($data['organization_id']) ? (int) $data['organization_id'] : null
        );
    }
}
