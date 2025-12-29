<?php
declare(strict_types=1);

namespace App\Entity;

class Aipd
{
    public function __construct(
        public ?int $id,
        public int $treatmentId,
        public int $userId,
        public ?int $organizationId,
        public string $status = 'draft',
        public ?string $necessityAssessment = null,
        public ?string $riskAssessment = null,
        public ?string $measuresPlanned = null,
        public ?string $dpoOpinion = null,
        public ?string $managerDecision = null,
        public bool $isHighRisk = true,
        public ?int $dpoId = null,
        public ?int $managerId = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?string $treatmentName = null, // Useful for listing
        public ?string $dpoName = null,
        public ?string $managerName = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            (int) ($data['treatment_id'] ?? 0),
            (int) ($data['user_id'] ?? 0),
            isset($data['organization_id']) ? (int) $data['organization_id'] : null,
            $data['status'] ?? 'draft',
            $data['necessity_assessment'] ?? null,
            $data['risk_assessment'] ?? null,
            $data['measures_planned'] ?? null,
            $data['dpo_opinion'] ?? null,
            $data['manager_decision'] ?? null,
            (bool) ($data['is_high_risk'] ?? true),
            isset($data['dpo_id']) ? (int) $data['dpo_id'] : null,
            isset($data['manager_id']) ? (int) $data['manager_id'] : null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
            $data['treatment_name'] ?? null,
            $data['dpo_name'] ?? null,
            $data['manager_name'] ?? null
        );
    }
}
