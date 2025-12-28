<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\TreatmentController;
use App\Controller\AuthController;
use App\Controller\SubprocessorController;
use App\Controller\AuditLogController;
use App\Controller\RightsExerciseController;
use App\Controller\DataBreachController;
use App\Controller\ReportController;



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


$page = $_GET['page'] ?? null;
$action = $_GET['action'] ?? null;

if (!$page && !isset($_SESSION['user_id'])) {
    header('Location: landing.html');
    exit;
}

$page = $page ?? 'treatment';
$action = $action ?? 'dashboard';

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
            case 'login_guest':
                $controller->loginGuest();
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
    } elseif ($page === 'rights') {
        $controller = new RightsExerciseController();
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
    } elseif ($page === 'breach') {
        $controller = new DataBreachController();
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
    } elseif ($page === 'report') {
        $controller = new ReportController();
        switch ($action) {
            case 'annual':
                $controller->generateAnnual();
                break;
            default:
                $controller->generateAnnual();
                break;
        }
    } elseif ($page === 'credits') {
        $title = 'Crédits';
        extract(['title' => $title]);
        ob_start();
        require __DIR__ . '/../templates/credits.php';
        $content = ob_get_clean();
        require __DIR__ . '/../templates/layout.php';
        exit;
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