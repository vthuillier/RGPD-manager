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

    public function createTreatment(array $data): void
    {
        $this->validate($data);
        $treatment = Treatment::fromArray($data);
        $id = $this->repository->save($treatment);

        if (isset($data['subprocessors']) && is_array($data['subprocessors'])) {
            $this->repository->linkSubprocessors($id, $data['subprocessors']);
        }
    }

    public function updateTreatmentForOrganization(int $id, int $organizationId, array $data): void
    {
        $this->validate($data);
        $data['id'] = $id;
        $data['organization_id'] = $organizationId;
        $treatment = Treatment::fromArray($data);
        $this->repository->save($treatment);

        if (isset($data['subprocessors']) && is_array($data['subprocessors'])) {
            $this->repository->linkSubprocessors($id, $data['subprocessors']);
        } else {
            $this->repository->linkSubprocessors($id, []);
        }
    }

    public function getSubprocessorIds(int $treatmentId): array
    {
        return $this->repository->getSubprocessorIds($treatmentId);
    }


    public function deleteTreatmentForOrganization(int $id, int $organizationId): void
    {
        $this->repository->deleteAndOrganizationId($id, $organizationId);
    }

    public function getStatsForOrganization(int $organizationId): array
    {
        $rightsRepo = new \App\Repository\RightsExerciseRepository();
        $breachRepo = new \App\Repository\DataBreachRepository();
        return [
            'total' => $this->repository->countAllByOrganizationId($organizationId),
            'legal_basis' => $this->repository->countByLegalBasis($organizationId),
            'treatments' => $this->repository->findAllByOrganizationId($organizationId),
            'rights' => $rightsRepo->getStats($organizationId),
            'breaches' => $breachRepo->getStats($organizationId)
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