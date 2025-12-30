<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\Subprocessor;
use PDO;

class SubprocessorRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    /**
     * @return Subprocessor[]
     */
    public function findAllByOrganizationId(int $organizationId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM subprocessors WHERE organization_id = :organization_id ORDER BY name ASC');
        $stmt->execute(['organization_id' => $organizationId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => Subprocessor::fromArray($data), $results);
    }

    public function findByIdAndOrganizationId(int $id, int $organizationId): ?Subprocessor
    {
        $stmt = $this->pdo->prepare('SELECT * FROM subprocessors WHERE id = :id AND organization_id = :organization_id');
        $stmt->execute(['id' => $id, 'organization_id' => $organizationId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? Subprocessor::fromArray($data) : null;
    }

    public function save(Subprocessor $subprocessor): int
    {
        if ($subprocessor->id === null) {
            return $this->insert($subprocessor);
        } else {
            $this->update($subprocessor);
            return $subprocessor->id;
        }
    }

    private function insert(Subprocessor $subprocessor): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO subprocessors (user_id, organization_id, name, service, location, guarantees)
            VALUES (:user_id, :organization_id, :name, :service, :location, :guarantees)'
        );

        $stmt->execute([
            'user_id' => $subprocessor->userId,
            'organization_id' => $subprocessor->organizationId,
            'name' => $subprocessor->name,
            'service' => $subprocessor->service,
            'location' => $subprocessor->location,
            'guarantees' => $subprocessor->guarantees
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    private function update(Subprocessor $subprocessor): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE subprocessors SET 
                name = :name, 
                service = :service, 
                location = :location, 
                guarantees = :guarantees
            WHERE id = :id AND organization_id = :organization_id'
        );

        $stmt->execute([
            'id' => $subprocessor->id,
            'organization_id' => $subprocessor->organizationId,
            'name' => $subprocessor->name,
            'service' => $subprocessor->service,
            'location' => $subprocessor->location,
            'guarantees' => $subprocessor->guarantees
        ]);
    }

    public function deleteAndOrganizationId(int $id, int $organizationId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM subprocessors WHERE id = :id AND organization_id = :organization_id');
        $stmt->execute(['id' => $id, 'organization_id' => $organizationId]);
    }


    /**
     * Link a subprocessor to a treatment
     */
    public function linkToTreatment(int $subprocessorId, int $treatmentId): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO treatment_subprocessors (treatment_id, subprocessor_id) VALUES (:treatment_id, :subprocessor_id) ON CONFLICT DO NOTHING');
        $stmt->execute(['treatment_id' => $treatmentId, 'subprocessor_id' => $subprocessorId]);
    }

    /**
     * Unlink all subprocessors from a treatment
     */
    public function unlinkAllFromTreatment(int $treatmentId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM treatment_subprocessors WHERE treatment_id = :treatment_id');
        $stmt->execute(['treatment_id' => $treatmentId]);
    }

    /**
     * Get all subprocessors for a treatment
     * @return Subprocessor[]
     */
    public function findByTreatmentId(int $treatmentId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT s.* FROM subprocessors s
            JOIN treatment_subprocessors ts ON s.id = ts.subprocessor_id
            WHERE ts.treatment_id = :treatment_id
            ORDER BY s.name ASC
        ');
        $stmt->execute(['treatment_id' => $treatmentId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => Subprocessor::fromArray($data), $results);
    }
}
