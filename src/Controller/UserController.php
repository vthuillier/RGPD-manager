<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AuditLogService;
use Exception;

class UserController
{
    private UserRepository $repository;
    private AuditLogService $auditLogService;
    private int $organizationId;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        if (($_SESSION['user_role'] ?? 'user') !== 'admin') {
            $_SESSION['flash_error'] = "Accès réservé aux administrateurs.";
            header('Location: index.php?page=treatment&action=dashboard');
            exit;
        }

        $this->repository = new UserRepository();
        $this->auditLogService = new AuditLogService();
        $this->organizationId = (int) $_SESSION['organization_id'];
    }

    public function list(): void
    {
        $users = $this->repository->findAllByOrganizationId($this->organizationId);
        $this->render('users/list', [
            'users' => $users,
            'title' => 'Gestion des utilisateurs'
        ]);
    }

    public function create(): void
    {
        $this->render('users/form', [
            'title' => 'Ajouter un utilisateur'
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        try {
            $email = $_POST['email'] ?? '';
            $name = $_POST['name'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';

            if (!$email || !$name || !$password) {
                throw new Exception("Tous les champs sont obligatoires.");
            }

            if ($this->repository->findByEmail($email)) {
                throw new Exception("Cet email est déjà utilisé.");
            }

            $user = new User(
                null,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $name,
                $role,
                $this->organizationId
            );

            $this->repository->save($user);
            $this->auditLogService->log('USER_CREATE', 'user', null, ['email' => $email, 'role' => $role]);

            $_SESSION['flash_success'] = "Utilisateur créé avec succès.";
            header('Location: index.php?page=user&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: index.php?page=user&action=create');
        }
    }

    public function delete(): void
    {
        $this->validateCsrf();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id === (int) $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Vous ne pouvez pas supprimer votre propre compte.";
            header('Location: index.php?page=user&action=list');
            exit;
        }

        // We need a delete method in UserRepository
        // For now let's assume it exists or use PDO directly if needed, 
        // but better add it to Repo.

        $this->repository->delete($id, $this->organizationId);


        $this->auditLogService->log('USER_DELETE', 'user', $id);
        $_SESSION['flash_success'] = "Utilisateur supprimé.";
        header('Location: index.php?page=user&action=list');
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../../templates/' . $template . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../../templates/layout.php';
    }

    private function validateCsrf(): void
    {
        if (($_POST['csrf_token'] ?? '') !== $_SESSION['csrf_token']) {
            die('CSRF token invalid');
        }
    }
}
