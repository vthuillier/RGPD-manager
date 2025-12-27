<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use PDO;

class TreatmentRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM treatments');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $name, string $purpose, string $legalBasis): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO treatments (name, purpose, legal_basis)
            VALUES (:name, :purpose, :legal_basis)'
        );

        $stmt->execute([
            'name' => $name,
            'purpose' => $purpose,
            'legal_basis' => $legalBasis
        ]);
    }

}