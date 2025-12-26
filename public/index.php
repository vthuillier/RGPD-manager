<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;

header('Content-Type: application/json');

$router = new Router();

$router->get('/health', function() {
    echo json_encode(['status' => 'RGPD Manager OK']);
});

$router->handleRequest();