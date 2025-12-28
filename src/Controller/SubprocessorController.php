<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Subprocessor;
use App\Repository\SubprocessorRepository;

class SubprocessorController
{
    private SubprocessorRepository $repository;
    private \App\Service\AuditLogService $auditLogService;

    public function __construct()
    {
        $this->repository = new SubprocessorRepository();
        $this->auditLogService = new \App\Service\AuditLogService();


        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    }

    public function list(): void
    {
        $subprocessors = $this->repository->findAllByOrganizationId((int) $_SESSION['organization_id']);
        $this->render('subprocessors/list', [
            'subprocessors' => $subprocessors,
            'title' => 'Registre des Sous-traitants'
        ]);
    }

    public function create(): void
    {
        $this->render('subprocessors/form', [
            'title' => 'Nouveau Sous-traitant'
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();

        $subprocessor = new Subprocessor(
            null,
            (int) $_SESSION['user_id'],
            $_POST['name'],
            $_POST['service'],
            $_POST['location'],
            $_POST['guarantees'],
            (int) $_SESSION['organization_id']
        );

        $this->repository->save($subprocessor);
        $this->auditLogService->log('SUBPROCESSOR_CREATE', 'subprocessor', null, ['name' => $_POST['name']]);
        $_SESSION['flash_success'] = 'Sous-traitant ajouté avec succès.';
        header('Location: index.php?page=subprocessor&action=list');
    }


    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $subprocessor = $this->repository->findByIdAndOrganizationId($id, (int) $_SESSION['organization_id']);

        if (!$subprocessor) {
            $_SESSION['flash_error'] = 'Sous-traitant non trouvé.';
            header('Location: index.php?page=subprocessor&action=list');
            exit;
        }

        $this->render('subprocessors/form', [
            'subprocessor' => $subprocessor,
            'title' => 'Modifier le Sous-traitant'
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();

        $id = (int) ($_POST['id'] ?? 0);
        $subprocessor = $this->repository->findByIdAndOrganizationId($id, (int) $_SESSION['organization_id']);

        if (!$subprocessor) {
            die('Subprocessor not found');
        }

        $subprocessor->name = $_POST['name'];
        $subprocessor->service = $_POST['service'];
        $subprocessor->location = $_POST['location'];
        $subprocessor->guarantees = $_POST['guarantees'];

        $this->repository->save($subprocessor);
        $this->auditLogService->log('SUBPROCESSOR_UPDATE', 'subprocessor', $id, ['name' => $_POST['name']]);
        $_SESSION['flash_success'] = 'Sous-traitant mis à jour.';
        header('Location: index.php?page=subprocessor&action=list');
    }


    public function delete(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();

        $id = (int) ($_POST['id'] ?? 0);
        $this->repository->deleteAndOrganizationId($id, (int) $_SESSION['organization_id']);
        $this->auditLogService->log('SUBPROCESSOR_DELETE', 'subprocessor', $id);

        $_SESSION['flash_success'] = 'Sous-traitant supprimé.';
        header('Location: index.php?page=subprocessor&action=list');
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

