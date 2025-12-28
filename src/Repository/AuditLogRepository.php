<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\AuditLog;
use PDO;

class AuditLogRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function save(AuditLog $log): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO audit_logs (user_id, organization_id, action, entity_type, entity_id, details, ip_address)
            VALUES (:user_id, :organization_id, :action, :entity_type, :entity_id, :details, :ip_address)'
        );

        $stmt->execute([
            'user_id' => $log->userId,
            'organization_id' => $log->organizationId,
            'action' => $log->action,
            'entity_type' => $log->entityType,
            'entity_id' => $log->entityId,
            'details' => $log->details,
            'ip_address' => $log->ipAddress
        ]);
    }

    /**
     * @return AuditLog[]
     */
    public function findAllByOrganizationId(int $organizationId, int $limit = 100): array
    {
        $stmt = $this->pdo->prepare('
            SELECT l.*, u.name as user_name 
            FROM audit_logs l
            LEFT JOIN users u ON l.user_id = u.id
            WHERE l.organization_id = :organization_id
            ORDER BY l.created_at DESC
            LIMIT :limit
        ');
        $stmt->bindValue(':organization_id', $organizationId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => AuditLog::fromArray($data), $results);
    }

}
