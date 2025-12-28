<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\AuditLogService;

class AuditLogController
{
    private AuditLogService $service;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        if (($_SESSION['user_role'] ?? 'user') !== 'admin') {
            $_SESSION['flash_error'] = "Accès réservé aux administrateurs.";
            header('Location: index.php?page=treatment&action=dashboard');
            exit;
        }

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

    private function render(string $template, array $data = []): void
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../../templates/' . $template . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../../templates/layout.php';
    }
}
