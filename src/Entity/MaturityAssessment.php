<?php

declare(strict_types=1);

namespace App\Entity;

class MaturityAssessment
{
    public function __construct(
        public ?int $id,
        public int $organizationId,
        public int $userId,
        public float $governanceScore,
        public float $registryScore,
        public float $rightsScore,
        public float $securityScore,
        public float $riskScore,
        public ?string $comments = null,
        public ?string $createdAt = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) $data['organization_id'],
            (int) $data['user_id'],
            (float) ($data['governance_score'] ?? 0),
            (float) ($data['registry_score'] ?? 0),
            (float) ($data['rights_score'] ?? 0),
            (float) ($data['security_score'] ?? 0),
            (float) ($data['risk_score'] ?? 0),
            $data['comments'] ?? null,
            $data['created_at'] ?? null
        );
    }
}
