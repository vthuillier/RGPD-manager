<?php

namespace App\Controller;

use App\Service\TreatmentService;

class TreatmentController
{

    private TreatmentService $treatmentService;

    public function __construct()
    {
        $this->treatmentService = new TreatmentService();
    }

    public function list(): void
    {
        $treatments = $this->treatmentService->getAll();
        echo json_encode($treatments);
    }

}