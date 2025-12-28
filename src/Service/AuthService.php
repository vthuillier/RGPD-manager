<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use InvalidArgumentException;

class AuthService
{
    private UserRepository $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function register(array $data): void
    {
        if (empty($data['email']) || empty($data['password']) || empty($data['name'])) {
            throw new InvalidArgumentException("Tous les champs sont obligatoires.");
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("L'adresse email est invalide.");
        }

        if ($this->repository->findByEmail($data['email'])) {
            throw new InvalidArgumentException("Cette adresse email est déjà utilisée.");
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $user = new User(
            null,
            $data['email'],
            $hashedPassword,
            $data['name']
        );

        $this->repository->save($user);
    }

    public function login(string $email, string $password): ?User
    {
        $user = $this->repository->findByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return null;
    }
}