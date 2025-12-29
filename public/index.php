<?php
// Secure session configuration
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_samesite', 'Lax');

// Set secure flag if over HTTPS
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', '1');
}

session_start();

// Security Headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:;");

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
    // We log but continue
}

// Installation Check
$isInstalled = \App\Controller\SetupController::isInstalled();
$page = $_GET['page'] ?? null;
$action = $_GET['action'] ?? null;

if (!$isInstalled && $page !== 'setup') {
    header('Location: index.php?page=setup');
    exit;
}

if ($isInstalled && !$page && !isset($_SESSION['user_id'])) {
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
            case 'switch_org':
                $controller->switchOrganization();
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
    } elseif ($page === 'setup') {
        $controller = new \App\Controller\SetupController();
        switch ($action) {
            case 'process':
                $controller->setup();
                break;
            default:
                $controller->showSetup();
                break;
        }
    } elseif ($page === 'user') {
        $controller = new \App\Controller\UserController();
        switch ($action) {
            case 'list':
                $controller->list();
                break;
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'store':
                $controller->store();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->list();
                break;
        }
    } elseif ($page === 'organization') {
        $controller = new \App\Controller\OrganizationController();
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
            case 'backup':
                $controller->backup();
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
    } elseif ($page === 'credits') {
        $title = 'Crédits';
        extract(['title' => $title]);
        ob_start();
        require __DIR__ . '/../templates/credits.php';
        $content = ob_get_clean();
        require __DIR__ . '/../templates/layout.php';
        exit;
    } else {
        header('Location: index.php?page=treatment&action=dashboard');
    }
} catch (\Throwable $e) {
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
    $displayErrors = getenv('DISPLAY_ERRORS') === '1';
    if ($displayErrors) {
        echo "Une erreur est survenue : " . htmlspecialchars($e->getMessage());
    } else {
        echo "Une erreur interne est survenue. L'administrateur a été notifié.";
    }
}