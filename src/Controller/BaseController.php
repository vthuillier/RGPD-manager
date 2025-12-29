<?php
declare(strict_types=1);

namespace App\Controller;

use Exception;

abstract class BaseController
{
    protected function render(string $template, array $data = [], bool $standalone = false): void
    {
        extract($data);
        $templatePath = __DIR__ . '/../../templates/' . $template . '.php';

        if ($standalone) {
            require $templatePath;
            return;
        }

        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        require __DIR__ . '/../../templates/layout.php';
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    protected function validateCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
            $this->auditLog('CSRF_FAILURE', null, null, ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
            die('Erreur de sécurité CSRF : Session expirée ou jeton invalide.');
        }
    }

    protected function ensureAuthenticated(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    }

    protected function ensureRole(array $roles): void
    {
        $this->ensureAuthenticated();
        $userRole = $_SESSION['user_role'] ?? 'user';

        if (!in_array($userRole, $roles)) {
            $this->auditLog('ACCESS_DENIED', 'internal', null, ['required_roles' => $roles, 'current_role' => $userRole]);
            $_SESSION['flash_error'] = "Accès refusé : Vous n'avez pas les droits nécessaires.";
            $this->redirect('index.php?page=treatment&action=dashboard');
        }
    }

    protected function validateNotGuest(): void
    {
        if (($_SESSION['user_role'] ?? '') === 'guest') {
            $_SESSION['flash_error'] = "Action interdite en mode consultation.";
            $this->redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
        }
    }

    protected function auditLog(string $action, ?string $entityType = null, ?int $entityId = null, ?array $details = null): void
    {
        $auditService = new \App\Service\AuditLogService();
        $auditService->log($action, $entityType, $entityId, $details);
    }

    protected function validatePasswordStrength(string $password): void
    {
        if (strlen($password) < 12) {
            throw new Exception("Le mot de passe doit contenir au moins 12 caractères.");
        }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            throw new Exception("Le mot de passe doit contenir des majuscules, minuscules et chiffres.");
        }
    }
}
