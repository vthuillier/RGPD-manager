<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\RightsExercise;
use App\Repository\RightsExerciseRepository;
use App\Service\AuditLogService;

class RightsExerciseController extends BaseController
{
    private RightsExerciseRepository $repository;
    private int $userId;
    private int $organizationId;

    public function __construct()
    {
        $this->ensureAuthenticated();
        $this->repository = new RightsExerciseRepository();
        $this->userId = (int) $_SESSION['user_id'];
        $this->organizationId = (int) $_SESSION['organization_id'];
    }

    public function list(): void
    {
        $exercises = $this->repository->findAllByOrganizationId($this->organizationId);
        $this->render('rights_exercises/list', [
            'exercises' => $exercises,
            'title' => 'Registre des Exercices de Droits'
        ]);
    }

    public function create(): void
    {
        $this->render('rights_exercises/form', [
            'title' => 'Nouveau dossier d\'exercice de droits'
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();

        $exercise = RightsExercise::fromArray($_POST);
        $exercise->userId = $this->userId;
        $exercise->organizationId = $this->organizationId;

        $this->repository->save($exercise);
        $this->auditLog('RIGHTS_EXERCISE_CREATE', 'rights_exercise', null, ['applicant' => $exercise->applicantName]);

        $_SESSION['flash_success'] = 'Demande enregistrée avec succès.';
        $this->redirect('index.php?page=rights&action=list');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $exercise = $this->repository->findByIdAndOrganizationId($id, $this->organizationId);

        if (!$exercise) {
            $_SESSION['flash_error'] = 'Dossier non trouvé.';
            $this->redirect('index.php?page=rights&action=list');
        }

        $this->render('rights_exercises/form', [
            'exercise' => $exercise,
            'title' => 'Modifier le dossier'
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();
        $id = (int) ($_POST['id'] ?? 0);
        $exercise = $this->repository->findByIdAndOrganizationId($id, $this->organizationId);

        if (!$exercise) {
            die('Dossier non trouvé');
        }

        $updatedExercise = RightsExercise::fromArray($_POST);
        $updatedExercise->id = $id;
        $updatedExercise->userId = $this->userId;
        $updatedExercise->organizationId = $this->organizationId;

        $this->repository->save($updatedExercise);
        $this->auditLog('RIGHTS_EXERCISE_UPDATE', 'rights_exercise', $id, ['applicant' => $updatedExercise->applicantName]);

        $_SESSION['flash_success'] = 'Dossier mis à jour.';
        $this->redirect('index.php?page=rights&action=list');
    }

    public function delete(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();
        $id = (int) ($_POST['id'] ?? 0);
        $this->repository->deleteAndOrganizationId($id, $this->organizationId);
        $this->auditLog('RIGHTS_EXERCISE_DELETE', 'rights_exercise', $id);

        $_SESSION['flash_success'] = 'Dossier supprimé.';
        $this->redirect('index.php?page=rights&action=list');
    }
}

