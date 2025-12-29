<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Service\AuditLogService;
use Exception;

class OrganizationController
{
    private OrganizationRepository $repository;
    private AuditLogService $auditLogService;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        if (($_SESSION['user_role'] ?? 'user') !== 'super_admin') {
            $_SESSION['flash_error'] = "Accès réservé à l'administrateur logiciel.";
            header('Location: index.php?page=treatment&action=dashboard');
            exit;
        }

        $this->repository = new OrganizationRepository();
        $this->auditLogService = new AuditLogService();
    }

    public function list(): void
    {
        $organizations = $this->repository->findAll();
        $this->render('organizations/list', [
            'organizations' => $organizations,
            'title' => 'Gestion des organismes'
        ]);
    }

    public function create(): void
    {
        $this->render('organizations/form', [
            'title' => 'Ajouter un organisme'
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        try {
            $name = $_POST['name'] ?? '';
            if (!$name) {
                throw new Exception("Le nom est obligatoire.");
            }

            $org = new Organization(null, $name);
            $id = $this->repository->save($org);

            $this->auditLogService->log('ORG_CREATE', 'organization', $id, ['name' => $name]);

            $_SESSION['flash_success'] = "Organisme créé avec succès.";
            header('Location: index.php?page=organization&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: index.php?page=organization&action=create');
        }
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $org = $this->repository->find($id);

        if (!$org) {
            $_SESSION['flash_error'] = "Organisme non trouvé.";
            header('Location: index.php?page=organization&action=list');
            exit;
        }

        $this->render('organizations/form', [
            'title' => 'Modifier l\'organisme',
            'organization' => $org
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();
        try {
            $id = (int) ($_POST['id'] ?? 0);
            $org = $this->repository->find($id);

            if (!$org) {
                throw new Exception("Organisme non trouvé.");
            }

            $name = $_POST['name'] ?? '';
            if (!$name) {
                throw new Exception("Le nom est obligatoire.");
            }

            $org->name = $name;
            $this->repository->save($org);

            $this->auditLogService->log('ORG_UPDATE', 'organization', $id, ['name' => $name]);

            $_SESSION['flash_success'] = "Organisme mis à jour.";
            header('Location: index.php?page=organization&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: index.php?page=organization&action=edit&id=' . ($id ?? 0));
        }
    }

    public function delete(): void
    {
        $this->validateCsrf();
        try {
            $id = (int) ($_POST['id'] ?? 0);

            if (!$id) {
                throw new Exception("ID de l'organisme manquant.");
            }

            if ($id === (int) ($_SESSION['organization_id'] ?? 0)) {
                throw new Exception("Vous ne pouvez pas supprimer l'organisme sur lequel vous êtes actuellement positionné. Basculez d'abord vers un autre organisme.");
            }

            $this->repository->delete($id);
            $this->auditLogService->log('ORG_DELETE', 'organization', $id);

            $_SESSION['flash_success'] = "Organisme supprimé avec succès.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Erreur lors de la suppression : " . $e->getMessage();
        }

        header('Location: index.php?page=organization&action=list');
    }

    public function backup(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $org = $this->repository->find($id);

        if (!$org) {
            $_SESSION['flash_error'] = "Organisme non trouvé.";
            header('Location: index.php?page=organization&action=list');
            exit;
        }

        // Simple backup logic: gather all data for this org
        $data = [
            'organization' => [
                'id' => $org->id,
                'name' => $org->name
            ],
            'export_date' => date('Y-m-d H:i:s'),
            'data' => $this->gatherOrganizationData($id)
        ];

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="backup_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $org->name)) . '_' . date('Y-m-d') . '.json"');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function gatherOrganizationData(int $orgId): array
    {
        $pdo = \App\Database\Connection::get();
        $tables = ['treatments', 'subprocessors', 'rights_exercises', 'data_breaches'];
        $allData = [];

        foreach ($tables as $table) {
            $stmt = $pdo->prepare("SELECT * FROM $table WHERE organization_id = :org_id");
            $stmt->execute(['org_id' => $orgId]);
            $allData[$table] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Get users linked to this org
        $stmt = $pdo->prepare("
            SELECT u.* FROM users u
            JOIN user_organizations uo ON u.id = uo.user_id
            WHERE uo.organization_id = :org_id
        ");
        $stmt->execute(['org_id' => $orgId]);
        $allData['users'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $allData;
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        ob_start();
        $templatePath = __DIR__ . '/../../templates/' . $template . '.php';
        if (!file_exists($templatePath)) {
            echo "Template not found: $templatePath";
        } else {
            require $templatePath;
        }
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
