<?php
declare(strict_types=1);

namespace App\Entity;

class Treatment
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public string $name,
        public string $purpose,
        public string $legalBasis,
        public string $dataCategories,
        public string $retentionPeriod,
        public bool $hasSensitiveData = false,
        public bool $isLargeScale = false,
        public ?string $createdAt = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            (int) ($data['user_id'] ?? 0),
            $data['name'] ?? '',
            $data['purpose'] ?? '',
            $data['legal_basis'] ?? '',
            $data['data_categories'] ?? '',
            $data['retention_period'] ?? '',
            (bool) ($data['has_sensitive_data'] ?? false),
            (bool) ($data['is_large_scale'] ?? false),
            $data['created_at'] ?? null
        );
    }
}
