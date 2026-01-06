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
            'organizations' => $organizations,
            'currentOrgId' => $this->organizationId
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        try {
            $email = $_POST['email'] ?? '';
            $name = $_POST['name'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $userRole = $_SESSION['user_role'] ?? 'user';
            if (!$email || !$name) {
                throw new Exception("Nom et email sont obligatoires.");
            }

            if ($this->repository->findByEmail($email)) {
                throw new Exception("Cet email est déjà utilisé.");
            }

            $selectedOrgs = $_POST['organizations'] ?? [];
            $orgRepo = new \App\Repository\OrganizationRepository();
            $allowedOrgs = ($userRole === 'super_admin')
                ? array_map(fn($o) => $o->id, $orgRepo->findAll())
                : array_map(fn($o) => $o->id, $orgRepo->findAllByUserId((int) $_SESSION['user_id']));
            $validOrgs = array_intersect($selectedOrgs, $allowedOrgs);
            $token = bin2hex(random_bytes(32));
            $expires = (new \DateTime())->modify('+48 hours')->format('Y-m-d H:i:s');
            $user = new User(
                null,
                $email,
                'INVITATION_PENDING', // Placeholder password
                $name,
                $role,
                !empty($validOrgs) ? (int) $validOrgs[0] : $this->organizationId,
                null,
                $token,
                $expires
            );
            $userId = $this->repository->save($user);

            foreach ($validOrgs as $orgId) {
                $this->repository->addOrganization($userId, (int) $orgId);
            }

            // Envoi de l'invitation
            $this->sendInvitationEmail($user);
            $this->auditLog('USER_CREATE', 'user', $userId, ['email' => $email, 'role' => $role, 'orgs' => $validOrgs]);
            $_SESSION['flash_success'] = "Utilisateur créé avec succès. Un email d'invitation a été envoyé.";
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
            'userOrgIds' => $userOrgIds,
            'currentOrgId' => $this->organizationId
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
            $this->redirect('index.php?page=user&action=edit&id=' . $id);
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

    public function reset(): void
    {
        $this->validateCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $user = $this->repository->find($id);
        if (!$user) {
            $_SESSION['flash_error'] = "Utilisateur non trouvé.";
            $this->redirect('index.php?page=user&action=list');
        }

        try {
            $token = bin2hex(random_bytes(32));
            $expires = (new \DateTime())->modify('+24 hours')->format('Y-m-d H:i:s');
            $user->resetToken = $token;
            $user->resetExpiresAt = $expires;
            $this->repository->save($user);
            $this->sendResetEmail($user);
            $this->auditLog('USER_PASSWORD_RESET_REQUESTED', 'user', $id, ['email' => $user->email]);
            $_SESSION['flash_success'] = "Un email de réinitialisation a été envoyé à " . $user->email;
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Erreur lors de l'envoi de l'email : " . $e->getMessage();
        }

        $this->redirect('index.php?page=user&action=list');
    }

    private function sendInvitationEmail(User $user): void
    {
        $mailService = new \App\Service\MailService();
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $url = "$protocol://$host/index.php?page=password&action=setup&token=" . $user->resetToken;
        $subject = "🚀 Bienvenue sur RGPD Manager";
        $title = "Activez votre compte";
        $content = "<p>Bonjour <strong>" . htmlspecialchars($user->name) . "</strong>,</p>" .
            "<p>Un compte vous a été créé sur la plateforme <strong>RGPD Manager</strong>.<br>" .
            "Vous pourrez ainsi gérer la conformité RGPD de votre organisation en toute simplicité.</p>" .
            "<p>Pour finaliser votre inscription et choisir votre mot de passe, merci de cliquer sur le bouton ci-dessous :</p>";
        $htmlBody = $mailService->getHtmlLayout($title, $content, "Activer mon compte", $url);
        $mailService->sendSystemMail($user->email, $subject, $htmlBody, true);
    }

    private function sendResetEmail(User $user): void
    {
        $mailService = new \App\Service\MailService();
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $url = "$protocol://$host/index.php?page=password&action=setup&token=" . $user->resetToken;
        $subject = "🔑 Réinitialisation de votre mot de passe";
        $title = "Mot de passe oublié ?";
        $content = "<p>Bonjour <strong>" . htmlspecialchars($user->name) . "</strong>,</p>" .
            "<p>Une demande de réinitialisation de mot de passe a été effectuée pour votre compte <strong>RGPD Manager</strong>.</p>" .
            "<p>Si vous êtes à l'origine de cette demande, vous pouvez définir un nouveau mot de passe en cliquant ici :</p>";
        $htmlBody = $mailService->getHtmlLayout($title, $content, "Réinitialiser mon mot de passe", $url);
        $mailService->sendSystemMail($user->email, $subject, $htmlBody, true);
    }
}
