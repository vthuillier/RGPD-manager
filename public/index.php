<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\TreatmentController;
use App\Controller\AuthController;
use App\Controller\SubprocessorController;
use App\Controller\AuditLogController;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Auto-initialize database
try {
    $schemaManager = new \App\Database\SchemaManager();
    $schemaManager->init();
} catch (\Exception $e) {
    // We log but continue, the app might crash later if DB is really missing
    // error_log("Database initialization failed: " . $e->getMessage());
}


$page = $_GET['page'] ?? 'treatment';
$action = $_GET['action'] ?? 'list';

try {
    if ($page === 'auth') {
        $controller = new AuthController();
        switch ($action) {
            case 'login':
                $controller->showLogin();
                break;
            case 'login_process':
                $controller->login();
                break;
            case 'register':
                $controller->showRegister();
                break;
            case 'register_process':
                $controller->register();
                break;
            case 'logout':
                $controller->logout();
                break;
            default:
                header('Location: index.php?page=auth&action=login');
                break;
        }
    } elseif ($page === 'treatment') {
        $controller = new TreatmentController();
        switch ($action) {
            case 'dashboard':
                $controller->dashboard();
                break;
            case 'list':
                $controller->list();
                break;
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'export_csv':
                $controller->exportCsv();
                break;
            case 'export_pdf':
                $controller->exportPdf();
                break;
            default:
                $controller->dashboard();
                break;
        }
    } elseif ($page === 'subprocessor') {
        $controller = new SubprocessorController();
        switch ($action) {
            case 'list':
                $controller->list();
                break;
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->list();
                break;
        }
    } elseif ($page === 'logs') {
        $controller = new AuditLogController();
        switch ($action) {
            case 'list':
                $controller->list();
                break;
            default:
                $controller->list();
                break;
        }
    } else {
        header('Location: index.php?page=treatment&action=dashboard');
    }


} catch (\Exception $e) {
    echo "Une erreur est survenue : " . $e->getMessage();
}