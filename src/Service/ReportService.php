<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\TreatmentRepository;
use App\Repository\SubprocessorRepository;
use App\Repository\RightsExerciseRepository;
use App\Repository\DataBreachRepository;

class ReportService
{
    private TreatmentRepository $treatmentRepo;
    private SubprocessorRepository $subprocessorRepo;
    private RightsExerciseRepository $rightsRepo;
    private DataBreachRepository $breachRepo;

    public function __construct()
    {
        $this->treatmentRepo = new TreatmentRepository();
        $this->subprocessorRepo = new SubprocessorRepository();
        $this->rightsRepo = new RightsExerciseRepository();
        $this->breachRepo = new DataBreachRepository();
    }

    public function getAnnualData(int $userId): array
    {
        return [
            'year' => date('Y'),
            'date' => date('d/m/Y'),
            'treatments' => [
                'total' => $this->treatmentRepo->countAllByUserId($userId),
                'by_legal_basis' => $this->treatmentRepo->countByLegalBasis($userId),
                'list' => $this->treatmentRepo->findAllByUserId($userId),
            ],
            'subprocessors' => [
                'total' => count($this->subprocessorRepo->findAllByUserId($userId)),
            ],

            'rights' => $this->rightsRepo->getStats($userId),
            'breaches' => $this->breachRepo->getStats($userId),
            'recent_breaches' => $this->breachRepo->findAllByUserId($userId),
        ];
    }
}
