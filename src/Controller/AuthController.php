<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthService;
use Exception;

class AuthController extends BaseController
{
    private AuthService $authService;
    private bool $allowGuest;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->allowGuest = (bool) (getenv('ALLOW_GUEST') ?: false);
    }


    public function showLogin(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('index.php?page=treatment&action=dashboard');
        }

        $this->render('auth/login', [
            'title' => 'Connexion',
            'allowGuest' => $this->allowGuest
        ]);
    }

    public function login(): void
    {
        $this->validateCsrf();
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->authService->login($email, $password);

        if ($user) {
            // Prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user->id;
            $_SESSION['organization_id'] = $user->organizationId;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_role'] = $user->role;
            $_SESSION['flash_success'] = "Bienvenue, " . $user->name;


            $this->auditLog('LOGIN', 'user', $user->id, ['email' => $email]);

            $this->redirect('index.php?page=treatment&action=dashboard');
        } else {
            // Brute force mitigation: small delay
            usleep(500000); // 500ms

            $this->auditLog('LOGIN_FAILED', 'user', null, ['email' => $email]);
            $_SESSION['flash_error'] = "Identifiants incorrects.";
            $this->redirect('index.php?page=auth&action=login');
        }
    }

    public function loginGuest(): void
    {
        if (!$this->allowGuest) {
            $this->redirect('index.php?page=auth&action=login');
        }

        $_SESSION['user_id'] = (int) (getenv('GUEST_TARGET_ID') ?: 1);
        $_SESSION['organization_id'] = 1; // Default organization for guest mode
        $_SESSION['user_name'] = "Invité";
        $_SESSION['user_role'] = 'guest';
        $_SESSION['flash_success'] = "Connecté en mode consultation.";

        $this->auditLog('LOGIN_GUEST', 'user', 0);

        $this->redirect('index.php?page=treatment&action=dashboard');
    }



    public function showRegister(): void
    {
        $_SESSION['flash_error'] = "L'inscription publique est désactivée. Veuillez contacter votre administrateur.";
        $this->redirect('index.php?page=auth&action=login');
    }

    public function register(): void
    {
        $this->showRegister();
    }


    public function switchOrganization(): void
    {
        $orgId = (int) ($_GET['org_id'] ?? 0);
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        if (!$orgId || !$userId) {
            $this->redirect('index.php');
        }

        // Verify user has access to this organization
        $role = $_SESSION['user_role'] ?? 'user';
        $hasAccess = false;

        if ($role === 'super_admin') {
            $hasAccess = true;
        } else {
            $orgRepo = new \App\Repository\OrganizationRepository();
            $userOrgs = $orgRepo->findAllByUserId($userId);

            foreach ($userOrgs as $org) {
                if ($org->id === $orgId) {
                    $hasAccess = true;
                    break;
                }
            }
        }

        if ($hasAccess) {
            $_SESSION['organization_id'] = $orgId;
            $_SESSION['flash_success'] = "Organisme changé avec succès.";
        } else {
            $_SESSION['flash_error'] = "Vous n'avez pas accès à cet organisme.";
        }

        $this->redirect('index.php?page=treatment&action=dashboard');
    }

    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $this->auditLog('LOGOUT', 'user', (int) $userId);
        }

        session_destroy();
        session_start();
        $_SESSION['flash_success'] = "Vous avez été déconnecté.";
        $this->redirect('index.php?page=auth&action=login');
    }
}
