<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\TreatmentService;

class TreatmentController
{
    private TreatmentService $service;

    public function __construct()
    {
        $this->service = new TreatmentService();
    }

    public function list(): void
    {
        echo json_encode($this->service->getAll());
    }

    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $this->service->create($data);

        http_response_code(201);
        echo json_encode(['message' => 'Treatment created']);
    }
}
