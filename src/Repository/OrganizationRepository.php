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
}
