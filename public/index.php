<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;
use App\Controller\AuthController;
use App\Controller\TreatmentController;

header('Content-Type: application/json');

$router = new Router();

$router->get('/health', function () {
    echo json_encode(['status' => 'RGPD Manager OK']);
});


$authController = new AuthController();
$treatmentController = new TreatmentController();

$router->get('/login', [$authController, 'login']);
$router->post('/register', [$authController, 'register']);
$router->post('/logout', [$authController, 'logout']);

$router->get('/treatments', [$treatmentController, 'list']);

$router->handleRequest();