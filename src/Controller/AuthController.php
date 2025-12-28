<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthService;
use Exception;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showLogin(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('index.php?page=treatment&action=list');
        }

        $this->render('auth/login', [
            'title' => 'Connexion'
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
            $_SESSION['flash_success'] = "Bienvenue, " . $user->name;
            $this->redirect('index.php?page=treatment&action=list');
        } else {
            $_SESSION['flash_error'] = "Identifiants incorrects.";
            $this->redirect('index.php?page=auth&action=login');
        }
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
            $_SESSION['flash_success'] = "Compte créé avec succès. Vous pouvez vous connecter.";
            $this->redirect('index.php?page=auth&action=login');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('index.php?page=auth&action=register');
        }
    }

    public function logout(): void
    {
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