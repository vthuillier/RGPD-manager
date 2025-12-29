<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\Organization;
use PDO;

class OrganizationRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function find(int $id): ?Organization
    {
        $stmt = $this->pdo->prepare('SELECT * FROM organizations WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? Organization::fromArray($data) : null;
    }

    public function save(Organization $organization): int
    {
        if ($organization->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO organizations (name) VALUES (:name) RETURNING id'
            );
            $stmt->execute(['name' => $organization->name]);
            return (int) $stmt->fetchColumn();
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE organizations SET name = :name WHERE id = :id'
            );
            $stmt->execute([
                'id' => $organization->id,
                'name' => $organization->name
            ]);
            return $organization->id;
        }
    }
    /**
     * @return Organization[]
     */
    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT o.* FROM organizations o 
             JOIN user_organizations uo ON o.id = uo.organization_id 
             WHERE uo.user_id = :user_id 
             ORDER BY o.name ASC'
        );
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => Organization::fromArray($data), $results);
    }

    /**
     * @return Organization[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM organizations WHERE id != -1 ORDER BY name ASC');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => Organization::fromArray($data), $results);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM organizations WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
