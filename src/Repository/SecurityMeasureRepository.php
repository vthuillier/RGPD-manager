<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\SecurityMeasure;
use PDO;

class SecurityMeasureRepository
{
    private PDO $pdo;
    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    /**
     * @return SecurityMeasure[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM security_measures ORDER BY category, name');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => SecurityMeasure::fromArray($data), $results);
    }

    /**
     * Find all measures available for an organization (global + specific)
     * @return SecurityMeasure[]
     */
    public function findAllForOrganization(int $organizationId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM security_measures 
            WHERE organization_id IS NULL OR organization_id = :org_id 
            ORDER BY category, name
        ');
        $stmt->execute(['org_id' => $organizationId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => SecurityMeasure::fromArray($data), $results);
    }

    /**
     * Find only specific measures created by an organization
     * @return SecurityMeasure[]
     */
    public function findByOrganizationId(int $organizationId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM security_measures 
            WHERE organization_id = :org_id 
            ORDER BY category, name
        ');
        $stmt->execute(['org_id' => $organizationId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => SecurityMeasure::fromArray($data), $results);
    }

    public function findById(int $id): ?SecurityMeasure
    {
        $stmt = $this->pdo->prepare('SELECT * FROM security_measures WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? SecurityMeasure::fromArray($data) : null;
    }

    public function save(SecurityMeasure $measure): int
    {
        if ($measure->id) {
            $stmt = $this->pdo->prepare('
                UPDATE security_measures 
                SET category = :category, name = :name, description = :description, weight = :weight, organization_id = :organization_id
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $measure->id,
                'category' => $measure->category,
                'name' => $measure->name,
                'description' => $measure->description,
                'weight' => $measure->weight,
                'organization_id' => $measure->organizationId
            ]);
            return $measure->id;
        } else {
            $stmt = $this->pdo->prepare('
                INSERT INTO security_measures (category, name, description, weight, organization_id)
                VALUES (:category, :name, :description, :weight, :organization_id)
                RETURNING id
            ');
            $stmt->execute([
                'category' => $measure->category,
                'name' => $measure->name,
                'description' => $measure->description,
                'weight' => $measure->weight,
                'organization_id' => $measure->organizationId
            ]);
            return (int) $stmt->fetchColumn();
        }
    }

    public function delete(int $id, int $organizationId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM security_measures WHERE id = :id AND organization_id = :org_id');
        $stmt->execute(['id' => $id, 'org_id' => $organizationId]);
    }

    /**
     * @return SecurityMeasure[]
     */
    public function findAllByTreatmentId(int $treatmentId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT sm.* 
            FROM security_measures sm
            JOIN treatment_security_measures tsm ON sm.id = tsm.measure_id
            WHERE tsm.treatment_id = :treatment_id
        ');
        $stmt->execute(['treatment_id' => $treatmentId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => SecurityMeasure::fromArray($data), $results);
    }

    public function getMeasureIdsByTreatmentId(int $treatmentId): array
    {
        $stmt = $this->pdo->prepare('SELECT measure_id FROM treatment_security_measures WHERE treatment_id = :treatment_id');
        $stmt->execute(['treatment_id' => $treatmentId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
