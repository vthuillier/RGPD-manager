<?php

declare(strict_types=1);

namespace App\Entity;

class Dtia
{
    public function __construct(
        public ?int $id,
        public int $organizationId,
        public ?int $treatmentId,
        public ?int $subprocessorId,
        public string $countryName,
        public string $transferMechanism,
        public string $dataExporter,
        public string $dataImporter,
        public string $dataCategories,
        public string $riskLevel,
        public ?string $supplementaryMeasures,
        public string $assessmentDate,
        public string $status = 'draft',
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) $data['organization_id'],
            isset($data['treatment_id']) ? (int) $data['treatment_id'] : null,
            isset($data['subprocessor_id']) ? (int) $data['subprocessor_id'] : null,
            (string) ($data['country_name'] ?? ''),
            (string) ($data['transfer_mechanism'] ?? ''),
            (string) ($data['data_exporter'] ?? ''),
            (string) ($data['data_importer'] ?? ''),
            (string) ($data['data_categories'] ?? ''),
            (string) ($data['risk_level'] ?? 'medium'),
            $data['supplementary_measures'] ?? null,
            (string) ($data['assessment_date'] ?? date('Y-m-d')),
            (string) ($data['status'] ?? 'draft'),
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }
}
