<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\TreatmentRepository;

class TreatmentService
{

    private TreatmentRepository $treatmentRepository;

    public function __construct()
    {
        $this->treatmentRepository = new TreatmentRepository();
    }

    public function getAll(): array
    {
        return $this->treatmentRepository->findAll();
    }

    public function create(array $data): void
    {
        $this->treatmentRepository->create(
            $data['name'],
            $data['purpose'],
            $data['legal_basis']
        );
    }

}