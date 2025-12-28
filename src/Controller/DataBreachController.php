<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\DataBreach;
use App\Repository\DataBreachRepository;
use App\Service\AuditLogService;

class DataBreachController
{
    private DataBreachRepository $repository;
    private AuditLogService $auditLogService;
    private int $userId;
    private int $organizationId;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        $this->repository = new DataBreachRepository();
        $this->auditLogService = new AuditLogService();
        $this->userId = (int) $_SESSION['user_id'];
        $this->organizationId = (int) $_SESSION['organization_id'];
    }

    public function list(): void
    {
        $breaches = $this->repository->findAllByOrganizationId($this->organizationId);
        $this->render('data_breaches/list', [
            'breaches' => $breaches,
            'title' => 'Registre des Violations de Données'
        ]);
    }

    public function create(): void
    {
        $this->render('data_breaches/form', [
            'title' => 'Déclarer une violation de données'
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();

        $data = $_POST;
        $data['user_id'] = $this->userId;
        $data['organization_id'] = $this->organizationId;
        $data['is_notified_authority'] = isset($_POST['is_notified_authority']);
        $data['is_notified_individuals'] = isset($_POST['is_notified_individuals']);

        $breach = DataBreach::fromArray($data);
        $this->repository->save($breach);

        $this->auditLogService->log('DATA_BREACH_CREATE', 'data_breach', null, ['nature' => substr($breach->nature, 0, 50)]);

        $_SESSION['flash_success'] = 'Violation de données enregistrée avec succès.';
        header('Location: index.php?page=breach&action=list');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $breach = $this->repository->findByIdAndOrganizationId($id, $this->organizationId);

        if (!$breach) {
            $_SESSION['flash_error'] = 'Violation non trouvée.';
            header('Location: index.php?page=breach&action=list');
            exit;
        }

        $this->render('data_breaches/form', [
            'breach' => $breach,
            'title' => 'Modifier le dossier de violation'
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();
        $id = (int) ($_POST['id'] ?? 0);
        $breach = $this->repository->findByIdAndOrganizationId($id, $this->organizationId);

        if (!$breach) {
            die('Dossier non trouvé');
        }

        $data = $_POST;
        $data['id'] = $id;
        $data['user_id'] = $this->userId;
        $data['organization_id'] = $this->organizationId;
        $data['is_notified_authority'] = isset($_POST['is_notified_authority']);
        $data['is_notified_individuals'] = isset($_POST['is_notified_individuals']);

        $updatedBreach = DataBreach::fromArray($data);
        $this->repository->save($updatedBreach);

        $this->auditLogService->log('DATA_BREACH_UPDATE', 'data_breach', $id, ['nature' => substr($updatedBreach->nature, 0, 50)]);

        $_SESSION['flash_success'] = 'Dossier mis à jour.';
        header('Location: index.php?page=breach&action=list');
    }

    public function delete(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();
        $id = (int) ($_POST['id'] ?? 0);
        $this->repository->deleteAndOrganizationId($id, $this->organizationId);
        $this->auditLogService->log('DATA_BREACH_DELETE', 'data_breach', $id);

        $_SESSION['flash_success'] = 'Dossier supprimé.';
        header('Location: index.php?page=breach&action=list');
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

    private function validateNotGuest(): void
    {
        if (($_SESSION['user_role'] ?? '') === 'guest') {
            $_SESSION['flash_error'] = "Action interdite en mode consultation.";
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
            exit;
        }
    }
}

