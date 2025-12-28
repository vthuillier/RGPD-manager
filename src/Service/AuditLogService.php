<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\AuditLog;
use App\Repository\AuditLogRepository;

class AuditLogService
{
    private AuditLogRepository $repository;

    public function __construct()
    {
        $this->repository = new AuditLogRepository();
    }

    public function log(string $action, ?string $entityType = null, ?int $entityId = null, ?array $details = null): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;

        $log = new AuditLog(
            null,
            $userId ? (int) $userId : null,
            $action,
            $entityType,
            $entityId,
            $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null,
            $ipAddress
        );

        $this->repository->save($log);
    }

    /**
     * @return AuditLog[]
     */
    public function getRecentLogs(int $limit = 50): array
    {
        return $this->repository->findAll($limit);
    }
}
