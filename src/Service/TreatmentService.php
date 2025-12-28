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
    public function getTreatmentsForUser(int $userId, array $filters = []): array
    {
        return $this->repository->findByFilters($userId, $filters);
    }

    public function getTreatmentForUser(int $id, int $userId): ?Treatment
    {
        return $this->repository->findByIdAndUserId($id, $userId);
    }

    public function createTreatment(array $data): void
    {
        $this->validate($data);
        $treatment = Treatment::fromArray($data);
        $this->repository->save($treatment);
    }

    public function updateTreatmentForUser(int $id, int $userId, array $data): void
    {
        $this->validate($data);
        $data['id'] = $id;
        $data['user_id'] = $userId;
        $treatment = Treatment::fromArray($data);
        $this->repository->save($treatment);
    }

    public function deleteTreatmentForUser(int $id, int $userId): void
    {
        $this->repository->deleteAndUserId($id, $userId);
    }

    public function getStats(int $userId): array
    {
        return [
            'total' => $this->repository->countAllByUserId($userId),
            'legal_basis' => $this->repository->countByLegalBasis($userId),
            'treatments' => $this->repository->findAllByUserId($userId),
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