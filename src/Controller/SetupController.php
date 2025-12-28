<?php
declare(strict_types=1);

namespace App\Controller;

use App\Database\Connection;
use App\Entity\Organization;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\UserRepository;
use Exception;

class SetupController
{
    private OrganizationRepository $orgRepo;
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->orgRepo = new OrganizationRepository();
        $this->userRepo = new UserRepository();
    }

    public static function isInstalled(): bool
    {
        $pdo = Connection::get();
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM users");
            return $stmt !== false && (int) $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    public function showSetup(): void
    {
        if (self::isInstalled()) {
            header('Location: index.php');
            exit;
        }

        $this->render('setup/index', [
            'title' => 'Configuration Initiale'
        ]);
    }

    public function setup(): void
    {
        if (self::isInstalled()) {
            die('Déjà installé');
        }

        try {
            $orgName = $_POST['org_name'] ?? '';
            $adminName = $_POST['admin_name'] ?? '';
            $adminEmail = $_POST['admin_email'] ?? '';
            $adminPassword = $_POST['admin_password'] ?? '';

            if (!$orgName || !$adminName || !$adminEmail || !$adminPassword) {
                throw new Exception("Tous les champs sont obligatoires.");
            }

            // Create Organization
            $orgId = $this->orgRepo->save(new Organization(null, $orgName));

            // Create Admin User
            $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
            $user = new User(
                null,
                $adminEmail,
                $hashedPassword,
                $adminName,
                'admin',
                $orgId
            );
            $this->userRepo->save($user);

            // Log in the new admin
            session_start();
            $dbUser = $this->userRepo->findByEmail($adminEmail);
            if ($dbUser) {
                $_SESSION['user_id'] = $dbUser->id;
                $_SESSION['organization_id'] = $dbUser->organizationId;
                $_SESSION['user_name'] = $dbUser->name;
                $_SESSION['user_role'] = $dbUser->role;
                $_SESSION['flash_success'] = "Installation terminée ! Bienvenue.";
            }

            header('Location: index.php?page=treatment&action=dashboard');
            exit;

        } catch (Exception $e) {
            session_start();
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: index.php?page=setup');
            exit;
        }
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
}
