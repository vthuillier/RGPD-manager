<?php
declare(strict_types=1);

namespace App\Entity;

class AuditLog
{
    public function __construct(
        public ?int $id,
        public ?int $userId,
        public string $action,
        public ?string $entityType,
        public ?int $entityId,
        public ?string $details,
        public ?string $ipAddress,
        public ?string $createdAt = null,
        public ?string $userName = null // Optional, for display
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            isset($data['user_id']) ? (int) $data['user_id'] : null,
            $data['action'] ?? '',
            $data['entity_type'] ?? null,
            isset($data['entity_id']) ? (int) $data['entity_id'] : null,
            $data['details'] ?? null,
            $data['ip_address'] ?? null,
            $data['created_at'] ?? null,
            $data['user_name'] ?? null
        );
    }
}
