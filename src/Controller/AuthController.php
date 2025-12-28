<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthService;
use Exception;

class AuthController
{
    private AuthService $authService;
    private \App\Service\AuditLogService $auditLogService;
    private bool $allowGuest;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->auditLogService = new \App\Service\AuditLogService();
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
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_role'] = $user->role;
            $_SESSION['flash_success'] = "Bienvenue, " . $user->name;


            $this->auditLogService->log('LOGIN', 'user', $user->id, ['email' => $email]);

            $this->redirect('index.php?page=treatment&action=dashboard');
        } else {
            $this->auditLogService->log('LOGIN_FAILED', 'user', null, ['email' => $email]);
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
        $_SESSION['user_name'] = "Invité";
        $_SESSION['user_role'] = 'guest';
        $_SESSION['flash_success'] = "Connecté en mode consultation.";

        $this->auditLogService->log('LOGIN_GUEST', 'user', 0);

        $this->redirect('index.php?page=treatment&action=dashboard');
    }


    public function showRegister(): void
    {
        $this->render('auth/register', [
            'title' => 'Inscription'
        ]);
    }

    public function register(): void
    {
        $this->validateCsrf();
        try {
            $this->authService->register($_POST);
            $this->auditLogService->log('REGISTER', 'user', null, ['email' => $_POST['email'] ?? '']);
            $_SESSION['flash_success'] = "Compte créé avec succès. Vous pouvez vous connecter.";
            $this->redirect('index.php?page=auth&action=login');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('index.php?page=auth&action=register');
        }

    }

    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $this->auditLogService->log('LOGOUT', 'user', (int) $userId);
        }

        session_destroy();
        session_start();
        $_SESSION['flash_success'] = "Vous avez été déconnecté.";
        $this->redirect('index.php?page=auth&action=login');
    }


    private function render(string $template, array $data = []): void
    {
        extract($data);
        $templatePath = __DIR__ . '/../../templates/' . $template . '.php';

        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        require __DIR__ . '/../../templates/layout.php';
    }

    private function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    private function validateCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
            die('Erreur de sécurité CSRF');
        }
    }
}