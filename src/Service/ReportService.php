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
    private \App\Repository\AipdRepository $aipdRepo;

    public function __construct()
    {
        $this->treatmentRepo = new TreatmentRepository();
        $this->subprocessorRepo = new SubprocessorRepository();
        $this->rightsRepo = new RightsExerciseRepository();
        $this->breachRepo = new DataBreachRepository();
        $this->aipdRepo = new \App\Repository\AipdRepository();
    }

    public function getAnnualData(int $organizationId): array
    {
        return [
            'year' => date('Y'),
            'date' => date('d/m/Y'),
            'treatments' => [
                'total' => $this->treatmentRepo->countAllByOrganizationId($organizationId),
                'by_legal_basis' => $this->treatmentRepo->countByLegalBasis($organizationId),
                'list' => $this->treatmentRepo->findAllByOrganizationId($organizationId),
            ],
            'subprocessors' => [
                'total' => count($this->subprocessorRepo->findAllByOrganizationId($organizationId)),
            ],
            'aipds' => [
                'total' => $this->aipdRepo->countByOrganizationId($organizationId),
                'list' => $this->aipdRepo->findAllByOrganizationId($organizationId),
            ],
            'rights' => $this->rightsRepo->getStats($organizationId),
            'breaches' => $this->breachRepo->getStats($organizationId),
            'recent_breaches' => $this->breachRepo->findAllByOrganizationId($organizationId),
        ];
    }
    public function getAipdData(int $id, int $organizationId): array
    {
        $aipd = $this->aipdRepo->findByIdAndOrganizationId($id, $organizationId);
        if (!$aipd) {
            throw new \Exception("Analyse d'impact non trouvée.");
        }

        return [
            'date' => date('d/m/Y'),
            'aipd' => $aipd
        ];
    }
}
