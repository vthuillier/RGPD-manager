<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Database\MigrationManager;
use Exception;

class UpgradeController extends BaseController
{
    private MigrationManager $manager;
    public function __construct()
    {
        $this->manager = new MigrationManager(Connection::get());
    }

    public function showUpgrade(): void
    {
        $pending = $this->manager->getPendingMigrations();
        if (empty($pending)) {
            $this->redirect('index.php');
        }

        $this->render('upgrade/index', [
            'title' => 'Mise à jour du système',
            'pending' => $pending,
            'targetVersion' => defined('APP_VERSION') ? APP_VERSION : '?.?.?'
        ]);
    }

    public function processStep(): void
    {
        header('Content-Type: application/json');
        try {
            $migrationName = $this->manager->applyNextMigration();
            if ($migrationName) {
                echo json_encode(['success' => true, 'migration' => $migrationName, 'finished' => false]);
            } else {
                echo json_encode(['success' => true, 'finished' => true]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
}
