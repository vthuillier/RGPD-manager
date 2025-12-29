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

        $userRole = $_SESSION['user_role'] ?? 'user';
        if ($userRole !== 'super_admin' && $userRole !== 'org_admin') {
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
        $userRole = $_SESSION['user_role'] ?? 'user';
        $userId = (int) $_SESSION['user_id'];

        if ($userRole === 'super_admin') {
            $users = $this->repository->findAll();
        } else {
            // Find all users who are in the same organizations as the current org_admin
            $orgRepo = new \App\Repository\OrganizationRepository();
            $myOrgs = $orgRepo->findAllByUserId($userId);
            $myOrgIds = array_map(fn($o) => $o->id, $myOrgs);

            $allUsers = $this->repository->findAll();
            $users = [];
            foreach ($allUsers as $u) {
                $uOrgs = $orgRepo->findAllByUserId((int) $u->id);
                $uOrgIds = array_map(fn($o) => $o->id, $uOrgs);
                if (array_intersect($myOrgIds, $uOrgIds)) {
                    $users[] = $u;
                }
            }
        }

        $this->render('users/list', [
            'users' => $users,
            'title' => 'Gestion des utilisateurs'
        ]);
    }

    public function create(): void
    {
        $orgRepo = new \App\Repository\OrganizationRepository();
        $userRole = $_SESSION['user_role'] ?? 'user';

        if ($userRole === 'super_admin') {
            $organizations = $orgRepo->findAll();
        } else {
            $organizations = $orgRepo->findAllByUserId((int) $_SESSION['user_id']);
        }

        $this->render('users/form', [
            'title' => 'Ajouter un utilisateur',
            'organizations' => $organizations
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

            $selectedOrgs = $_POST['organizations'] ?? [];

            $user = new User(
                null,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $name,
                $role,
                !empty($selectedOrgs) ? (int) $selectedOrgs[0] : $this->organizationId
            );

            $this->repository->save($user);
            $userId = (int) \App\Database\Connection::get()->lastInsertId();

            if (!$userId) {
                $savedUser = $this->repository->findByEmail($email);
                $userId = $savedUser->id;
            }

            foreach ($selectedOrgs as $orgId) {
                $this->repository->addOrganization($userId, (int) $orgId);
            }

            $this->auditLogService->log('USER_CREATE', 'user', $userId, ['email' => $email, 'role' => $role, 'orgs' => $selectedOrgs]);

            $_SESSION['flash_success'] = "Utilisateur créé avec succès.";
            header('Location: index.php?page=user&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: index.php?page=user&action=create');
        }
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $user = $this->repository->find($id);

        if (!$user) {
            $_SESSION['flash_error'] = "Utilisateur non trouvé.";
            header('Location: index.php?page=user&action=list');
            exit;
        }

        $orgRepo = new \App\Repository\OrganizationRepository();
        $userRole = $_SESSION['user_role'] ?? 'user';

        if ($userRole === 'super_admin') {
            $organizations = $orgRepo->findAll();
        } else {
            $organizations = $orgRepo->findAllByUserId((int) $_SESSION['user_id']);
        }

        $userOrgs = $orgRepo->findAllByUserId($id);
        $userOrgIds = array_map(fn($o) => $o->id, $userOrgs);

        $this->render('users/form', [
            'title' => 'Modifier un utilisateur',
            'user' => $user,
            'organizations' => $organizations,
            'userOrgIds' => $userOrgIds
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();
        try {
            $id = (int) ($_POST['id'] ?? 0);
            $user = $this->repository->find($id);

            if (!$user) {
                throw new Exception("Utilisateur non trouvé.");
            }

            $email = $_POST['email'] ?? '';
            $name = $_POST['name'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';

            if (!$email || !$name) {
                throw new Exception("Nom et email sont obligatoires.");
            }

            if ($email !== $user->email && $this->repository->findByEmail($email)) {
                throw new Exception("Cet email est déjà utilisé.");
            }

            $user->email = $email;
            $user->name = $name;
            $user->role = $role;
            if ($password) {
                $user->password = password_hash($password, PASSWORD_DEFAULT);
            }

            $selectedOrgs = $_POST['organizations'] ?? [];
            if (!empty($selectedOrgs)) {
                $user->organizationId = (int) $selectedOrgs[0];
            }

            $this->repository->save($user);

            $this->repository->clearOrganizations($id);
            foreach ($selectedOrgs as $orgId) {
                $this->repository->addOrganization($id, (int) $orgId);
            }

            $this->auditLogService->log('USER_UPDATE', 'user', $id, ['email' => $email, 'orgs' => $selectedOrgs]);

            $_SESSION['flash_success'] = "Utilisateur mis à jour.";
            header('Location: index.php?page=user&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: index.php?page=user&action=edit&id=' . ($id ?? 0));
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
