<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Treatment;
use App\Repository\TreatmentRepository;

class TreatmentService
{
    private TreatmentRepository $repository;

    public function __construct()
    {
        $this->repository = new TreatmentRepository();
    }

    /**
     * @return Treatment[]
     */
    public function getTreatmentsForOrganization(int $organizationId, array $filters = []): array
    {
        return $this->repository->findByFilters($organizationId, $filters);
    }

    public function getTreatmentForOrganization(int $id, int $organizationId): ?Treatment
    {
        return $this->repository->findByIdAndOrganizationId($id, $organizationId);
    }

    public function createTreatment(array $data): int
    {
        $this->validate($data);
        $treatment = Treatment::fromArray($data);
        $id = $this->repository->save($treatment);

        if (isset($data['subprocessors']) && is_array($data['subprocessors'])) {
            $this->repository->linkSubprocessors($id, $data['subprocessors'], (int) $data['organization_id']);
        }

        if (isset($data['security_measures']) && is_array($data['security_measures'])) {
            $this->repository->linkSecurityMeasures($id, $data['security_measures']);
        }

        return $id;
    }

    public function updateTreatmentForOrganization(int $id, int $organizationId, array $data): void
    {
        $this->validate($data);
        $data['id'] = $id;
        $data['organization_id'] = $organizationId;
        $treatment = Treatment::fromArray($data);
        $this->repository->save($treatment);

        if (isset($data['subprocessors']) && is_array($data['subprocessors'])) {
            $this->repository->linkSubprocessors($id, $data['subprocessors'], $organizationId);
        } else {
            $this->repository->linkSubprocessors($id, [], $organizationId);
        }

        if (isset($data['security_measures']) && is_array($data['security_measures'])) {
            $this->repository->linkSecurityMeasures($id, $data['security_measures']);
        } else {
            $this->repository->linkSecurityMeasures($id, []);
        }
    }

    public function getSubprocessorIds(int $treatmentId): array
    {
        return $this->repository->getSubprocessorIds($treatmentId);
    }

    public function getSecurityMeasureIds(int $treatmentId): array
    {
        $measureRepo = new \App\Repository\SecurityMeasureRepository();
        return $measureRepo->getMeasureIdsByTreatmentId($treatmentId);
    }

    public function getSecurityScore(int $treatmentId, int $organizationId): int
    {
        $measureRepo = new \App\Repository\SecurityMeasureRepository();
        $measures = $measureRepo->findAllByTreatmentId($treatmentId);
        $allMeasures = $measureRepo->findAllForOrganization($organizationId);

        $totalWeight = array_reduce($allMeasures, fn($sum, $m) => $sum + $m->weight, 0);
        $appliedWeight = array_reduce($measures, fn($sum, $m) => $sum + $m->weight, 0);

        if ($totalWeight === 0)
            return 0;
        return (int) round(($appliedWeight / $totalWeight) * 100);
    }


    public function deleteTreatmentForOrganization(int $id, int $organizationId): void
    {
        $this->repository->deleteAndOrganizationId($id, $organizationId);
    }

    public function getStatsForOrganization(int $organizationId): array
    {
        $rightsRepo = new \App\Repository\RightsExerciseRepository();
        $breachRepo = new \App\Repository\DataBreachRepository();
        $aipdRepo = new \App\Repository\AipdRepository();
        return [
            'total' => $this->repository->countAllByOrganizationId($organizationId),
            'legal_basis' => $this->repository->countByLegalBasis($organizationId),
            'treatments' => $this->repository->findAllByOrganizationId($organizationId),
            'rights' => $rightsRepo->getStats($organizationId),
            'breaches' => $breachRepo->getStats($organizationId),
            'aipd_count' => $aipdRepo->countByOrganizationId($organizationId)
        ];
    }




    private function validate(array $data): void
    {
        $errors = [];
        if (empty($data['name']))
            $errors[] = "Le nom est obligatoire.";
        if (empty($data['purpose']))
            $errors[] = "La finalité est obligatoire.";
        if (empty($data['user_id']))
            $errors[] = "L'utilisateur est manquant.";

        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' ', $errors));
        }
    }
}