<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Subprocessor;
use App\Repository\SubprocessorRepository;

class SubprocessorController extends BaseController
{
    private SubprocessorRepository $repository;

    public function __construct()
    {
        $this->ensureAuthenticated();
        $this->repository = new SubprocessorRepository();
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
        $this->auditLog('SUBPROCESSOR_CREATE', 'subprocessor', null, ['name' => $_POST['name']]);
        $_SESSION['flash_success'] = 'Sous-traitant ajouté avec succès.';
        $this->redirect('index.php?page=subprocessor&action=list');
    }


    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $subprocessor = $this->repository->findByIdAndOrganizationId($id, (int) $_SESSION['organization_id']);

        if (!$subprocessor) {
            $_SESSION['flash_error'] = 'Sous-traitant non trouvé.';
            $this->redirect('index.php?page=subprocessor&action=list');
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
        $this->auditLog('SUBPROCESSOR_UPDATE', 'subprocessor', $id, ['name' => $_POST['name']]);
        $_SESSION['flash_success'] = 'Sous-traitant mis à jour.';
        $this->redirect('index.php?page=subprocessor&action=list');
    }


    public function delete(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();

        $id = (int) ($_POST['id'] ?? 0);
        $this->repository->deleteAndOrganizationId($id, (int) $_SESSION['organization_id']);
        $this->auditLog('SUBPROCESSOR_DELETE', 'subprocessor', $id);

        $_SESSION['flash_success'] = 'Sous-traitant supprimé.';
        $this->redirect('index.php?page=subprocessor&action=list');
    }
}

