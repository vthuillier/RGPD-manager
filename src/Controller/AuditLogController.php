<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\AuditLogService;

class AuditLogController extends BaseController
{
    private AuditLogService $service;

    public function __construct()
    {
        $this->ensureRole(['super_admin']);
        $this->service = new AuditLogService();
    }


    public function list(): void
    {
        $logs = $this->service->getRecentLogs(100);
        $this->render('logs/list', [
            'logs' => $logs,
            'title' => 'Journaux d\'audit (Logs)'
        ]);
    }
}
