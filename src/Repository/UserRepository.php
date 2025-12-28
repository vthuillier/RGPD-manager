<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\User;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function find(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? User::fromArray($data) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? User::fromArray($data) : null;
    }

    public function save(User $user): void
    {
        if ($user->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO users (email, password, name, role, organization_id) 
                 VALUES (:email, :password, :name, :role, :organization_id)'
            );
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE users SET email = :email, password = :password, name = :name, role = :role, organization_id = :organization_id WHERE id = :id'
            );
            $stmt->bindValue(':id', $user->id, PDO::PARAM_INT);
        }

        $stmt->bindValue(':email', $user->email);
        $stmt->bindValue(':password', $user->password);
        $stmt->bindValue(':name', $user->name);
        $stmt->bindValue(':role', $user->role);
        $stmt->bindValue(':organization_id', $user->organizationId, PDO::PARAM_INT);

        $stmt->execute();
    }

    /**
     * @return User[]
     */
    public function findAllByOrganizationId(int $organizationId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE organization_id = :organization_id ORDER BY name ASC');
        $stmt->execute(['organization_id' => $organizationId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => User::fromArray($data), $results);
    }

    public function delete(int $id, int $organizationId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id AND organization_id = :organization_id');
        $stmt->execute(['id' => $id, 'organization_id' => $organizationId]);
    }




}
