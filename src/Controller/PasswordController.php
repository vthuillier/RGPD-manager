<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Exception;

class PasswordController extends BaseController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Affiche le formulaire pour définir/réinitialiser le mot de passe via un jeton
     */
    public function setup(): void
    {
        $token = $_GET['token'] ?? '';
        if (!$token) {
            $this->redirect('index.php?page=auth&action=login');
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            $_SESSION['flash_error'] = "Le lien de réinitialisation est invalide ou a expiré.";
            $this->redirect('index.php?page=auth&action=login');
        }

        $this->render('auth/password_setup', [
            'title' => 'Définir votre mot de passe',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Enregistre le nouveau mot de passe
     */
    public function update(): void
    {
        $this->validateCsrf();

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        try {
            if (!$token)
                throw new Exception("Jeton manquant.");
            if (!$password)
                throw new Exception("Le mot de passe est obligatoire.");
            if ($password !== $passwordConfirm)
                throw new Exception("Les mots de passe ne correspondent pas.");

            $this->validatePasswordStrength($password);

            $user = $this->userRepository->findByToken($token);
            if (!$user) {
                throw new Exception("Le lien est invalide ou a expiré.");
            }

            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->resetToken = null;
            $user->resetExpiresAt = null;

            $this->userRepository->save($user);

            $this->auditLog('PASSWORD_SETUP_SUCCESS', 'user', $user->id, ['email' => $user->email]);

            $_SESSION['flash_success'] = "Votre mot de passe a été défini avec succès. Vous pouvez maintenant vous connecter.";
            $this->redirect('index.php?page=auth&action=login');

        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('index.php?page=password&action=setup&token=' . $token);
        }
    }
}
