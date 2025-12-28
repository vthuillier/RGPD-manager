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
        public ?int $organizationId = null,
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
            !empty($data['subjects_count']) ? (int) $data['subjects_count'] : null,
            !empty($data['records_count']) ? (int) $data['records_count'] : null,
            !empty($data['consequences']) ? $data['consequences'] : null,
            !empty($data['measures_taken']) ? $data['measures_taken'] : null,
            (bool) ($data['is_notified_authority'] ?? false),
            !empty($data['notification_authority_date']) ? $data['notification_authority_date'] : null,
            (bool) ($data['is_notified_individuals'] ?? false),
            isset($data['organization_id']) ? (int) $data['organization_id'] : null,
            $data['created_at'] ?? null
        );
    }

}
