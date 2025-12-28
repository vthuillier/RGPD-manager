<?php
declare(strict_types=1);

namespace App\Entity;

class DataBreach
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public string $discoveryDate,
        public string $nature,
        public string $dataCategories,
        public ?int $subjectsCount,
        public ?int $recordsCount,
        public ?string $consequences,
        public ?string $measuresTaken,
        public bool $isNotifiedAuthority = false,
        public ?string $notificationAuthorityDate = null,
        public bool $isNotifiedIndividuals = false,
        public ?string $createdAt = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) ($data['user_id'] ?? 0),
            $data['discovery_date'] ?? date('Y-m-d H:i'),
            $data['nature'] ?? '',
            $data['data_categories'] ?? '',
            isset($data['subjects_count']) ? (int) $data['subjects_count'] : null,
            isset($data['records_count']) ? (int) $data['records_count'] : null,
            $data['consequences'] ?? null,
            $data['measures_taken'] ?? null,
            (bool) ($data['is_notified_authority'] ?? false),
            $data['notification_authority_date'] ?? null,
            (bool) ($data['is_notified_individuals'] ?? false),
            $data['created_at'] ?? null
        );
    }
}
