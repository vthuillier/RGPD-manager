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

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? User::fromArray($data) : null;
    }

    public function save(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (email, password, name) 
             VALUES (:email, :password, :name)'
        );

        $stmt->execute([
            'email' => $user->email,
            'password' => $user->password,
            'name' => $user->name,
        ]);
    }
}
