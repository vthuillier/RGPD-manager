<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\Treatment;
use PDO;

class TreatmentRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    /**
     * @return Treatment[]
     */
    public function findAllByUserId(int $userId): array
    {
        return $this->findByFilters($userId, []);
    }

    /**
     * @return Treatment[]
     */
    public function findByFilters(int $userId, array $filters): array
    {
        $sql = 'SELECT * FROM treatments WHERE user_id = :user_id';
        $params = ['user_id' => $userId];

        if (!empty($filters['search'])) {
            $sql .= ' AND (name ILIKE :search OR purpose ILIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['legal_basis'])) {
            $sql .= ' AND legal_basis = :legal_basis';
            $params['legal_basis'] = $filters['legal_basis'];
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => Treatment::fromArray($data), $results);
    }

    public function findByIdAndUserId(int $id, int $userId): ?Treatment
    {
        $stmt = $this->pdo->prepare('SELECT * FROM treatments WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? Treatment::fromArray($data) : null;
    }

    public function save(Treatment $treatment): int
    {
        if ($treatment->id === null) {
            return $this->insert($treatment);
        } else {
            $this->update($treatment);
            return $treatment->id;
        }
    }


    private function insert(Treatment $treatment): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO treatments (user_id, name, purpose, legal_basis, data_categories, retention_period, has_sensitive_data, is_large_scale, retention_years)
            VALUES (:user_id, :name, :purpose, :legal_basis, :data_categories, :retention_period, :has_sensitive_data, :is_large_scale, :retention_years)
            RETURNING id'
        );

        $stmt->execute([
            'user_id' => $treatment->userId,
            'name' => $treatment->name,
            'purpose' => $treatment->purpose,
            'legal_basis' => $treatment->legalBasis,
            'data_categories' => $treatment->dataCategories,
            'retention_period' => $treatment->retentionPeriod,
            'has_sensitive_data' => (int) $treatment->hasSensitiveData,
            'is_large_scale' => (int) $treatment->isLargeScale,
            'retention_years' => $treatment->retentionYears
        ]);

        return (int) $stmt->fetchColumn();
    }


    private function update(Treatment $treatment): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE treatments SET 
                name = :name, 
                purpose = :purpose, 
                legal_basis = :legal_basis, 
                data_categories = :data_categories, 
                retention_period = :retention_period,
                has_sensitive_data = :has_sensitive_data,
                is_large_scale = :is_large_scale,
                retention_years = :retention_years
            WHERE id = :id AND user_id = :user_id'
        );

        $stmt->execute([
            'id' => $treatment->id,
            'user_id' => $treatment->userId,
            'name' => $treatment->name,
            'purpose' => $treatment->purpose,
            'legal_basis' => $treatment->legalBasis,
            'data_categories' => $treatment->dataCategories,
            'retention_period' => $treatment->retentionPeriod,
            'has_sensitive_data' => (int) $treatment->hasSensitiveData,
            'is_large_scale' => (int) $treatment->isLargeScale,
            'retention_years' => $treatment->retentionYears
        ]);
    }

    public function deleteAndUserId(int $id, int $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM treatments WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public function countAllByUserId(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM treatments WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function countByLegalBasis(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT legal_basis, COUNT(*) as count 
            FROM treatments 
            WHERE user_id = :user_id 
            GROUP BY legal_basis
        ');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubprocessorIds(int $treatmentId): array
    {
        $stmt = $this->pdo->prepare('SELECT subprocessor_id FROM treatment_subprocessors WHERE treatment_id = :treatment_id');
        $stmt->execute(['treatment_id' => $treatmentId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function linkSubprocessors(int $treatmentId, array $subprocessorIds): void
    {
        // First clear existing links
        $stmt = $this->pdo->prepare('DELETE FROM treatment_subprocessors WHERE treatment_id = :treatment_id');
        $stmt->execute(['treatment_id' => $treatmentId]);

        // Then add new ones
        if (empty($subprocessorIds))
            return;

        $stmt = $this->pdo->prepare('INSERT INTO treatment_subprocessors (treatment_id, subprocessor_id) VALUES (:treatment_id, :subprocessor_id)');
        foreach ($subprocessorIds as $sid) {
            $stmt->execute(['treatment_id' => $treatmentId, 'subprocessor_id' => (int) $sid]);
        }
    }
}
