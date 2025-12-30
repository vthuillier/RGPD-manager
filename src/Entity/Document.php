<?php
declare(strict_types=1);

namespace App\Entity;

class Document
{
    public function __construct(
        public ?int $id,
        public int $organizationId,
        public string $entityType,
        public int $entityId,
        public string $filePath,
        public string $fileName,
        public ?string $fileType = null,
        public ?int $fileSize = null,
        public ?string $createdAt = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) $data['organization_id'],
            $data['entity_type'],
            (int) $data['entity_id'],
            $data['file_path'],
            $data['file_name'],
            $data['file_type'] ?? null,
            isset($data['file_size']) ? (int) $data['file_size'] : null,
            $data['created_at'] ?? null
        );
    }
}
