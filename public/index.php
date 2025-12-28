<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\TreatmentController;
use App\Controller\AuthController;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
    } else {
        header('Location: index.php?page=treatment&action=list');
    }
} catch (\Exception $e) {
    echo "Une erreur est survenue : " . $e->getMessage();
}