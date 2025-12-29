<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AuditLogService;
use Exception;

class UserController extends BaseController
{
    private UserRepository $repository;
    private int $organizationId;

    public function __construct()
    {
        $this->ensureRole(['super_admin', 'org_admin']);
        $this->repository = new UserRepository();
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
            $userRole = $_SESSION['user_role'] ?? 'user';

            if (!$email || !$name || !$password) {
                throw new Exception("Tous les champs sont obligatoires.");
            }

            $this->validatePasswordStrength($password);

            if ($this->repository->findByEmail($email)) {
                throw new Exception("Cet email est déjà utilisé.");
            }

            $selectedOrgs = $_POST['organizations'] ?? [];
            $orgRepo = new \App\Repository\OrganizationRepository();
            $allowedOrgs = ($userRole === 'super_admin')
                ? array_map(fn($o) => $o->id, $orgRepo->findAll())
                : array_map(fn($o) => $o->id, $orgRepo->findAllByUserId((int) $_SESSION['user_id']));

            $validOrgs = array_intersect($selectedOrgs, $allowedOrgs);

            $user = new User(
                null,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $name,
                $role,
                !empty($validOrgs) ? (int) $validOrgs[0] : $this->organizationId
            );

            $this->repository->save($user);
            $userId = (int) \App\Database\Connection::get()->lastInsertId();

            if (!$userId) {
                $savedUser = $this->repository->findByEmail($email);
                $userId = $savedUser->id;
            }

            foreach ($validOrgs as $orgId) {
                $this->repository->addOrganization($userId, (int) $orgId);
            }

            $this->auditLog('USER_CREATE', 'user', $userId, ['email' => $email, 'role' => $role, 'orgs' => $validOrgs]);

            $_SESSION['flash_success'] = "Utilisateur créé avec succès.";
            $this->redirect('index.php?page=user&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('index.php?page=user&action=create');
        }
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $user = $this->repository->find($id);

        if (!$user) {
            $_SESSION['flash_error'] = "Utilisateur non trouvé.";
            $this->redirect('index.php?page=user&action=list');
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

            if ($password) {
                $this->validatePasswordStrength($password);
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
            $orgRepo = new \App\Repository\OrganizationRepository();
            $userRole = $_SESSION['user_role'] ?? 'user';
            $allowedOrgs = ($userRole === 'super_admin')
                ? array_map(fn($o) => $o->id, $orgRepo->findAll())
                : array_map(fn($o) => $o->id, $orgRepo->findAllByUserId((int) $_SESSION['user_id']));

            $validOrgs = array_intersect($selectedOrgs, $allowedOrgs);

            if (!empty($validOrgs)) {
                $user->organizationId = (int) $validOrgs[0];
            }

            $this->repository->save($user);

            $this->repository->clearOrganizations($id);
            foreach ($validOrgs as $orgId) {
                $this->repository->addOrganization($id, (int) $orgId);
            }

            $this->auditLog('USER_UPDATE', 'user', $id, ['email' => $email, 'orgs' => $validOrgs]);

            $_SESSION['flash_success'] = "Utilisateur mis à jour.";
            $this->redirect('index.php?page=user&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('index.php?page=user&action=edit&id=' . ($id ?? 0));
        }
    }

    public function delete(): void
    {
        $this->validateCsrf();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id === (int) $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Vous ne pouvez pas supprimer votre propre compte.";
            $this->redirect('index.php?page=user&action=list');
        }

        $this->repository->delete($id, $this->organizationId);

        $this->auditLog('USER_DELETE', 'user', $id);
        $_SESSION['flash_success'] = "Utilisateur supprimé.";
        $this->redirect('index.php?page=user&action=list');
    }
}
