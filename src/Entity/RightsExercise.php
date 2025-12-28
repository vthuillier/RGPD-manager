<?php
declare(strict_types=1);

namespace App\Entity;

class RightsExercise
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public string $applicantName,
        public string $requestDate,
        public string $requestType,
        public string $status = 'En attente',
        public ?string $completionDate = null,
        public ?string $details = null,
        public ?int $organizationId = null,
        public ?string $createdAt = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) ($data['user_id'] ?? 0),
            $data['applicant_name'] ?? '',
            $data['request_date'] ?? '',
            $data['request_type'] ?? '',
            $data['status'] ?? 'En attente',
            !empty($data['completion_date']) ? $data['completion_date'] : null,
            !empty($data['details']) ? $data['details'] : null,
            isset($data['organization_id']) ? (int) $data['organization_id'] : null,
            $data['created_at'] ?? null
        );
    }


}
