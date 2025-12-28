<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\RightsExercise;
use App\Repository\RightsExerciseRepository;
use App\Service\AuditLogService;

class RightsExerciseController
{
    private RightsExerciseRepository $repository;
    private AuditLogService $auditLogService;
    private int $userId;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        $this->repository = new RightsExerciseRepository();
        $this->auditLogService = new AuditLogService();
        $this->userId = (int) $_SESSION['user_id'];
    }

    public function list(): void
    {
        $exercises = $this->repository->findAllByUserId($this->userId);
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

        $this->repository->save($exercise);
        $this->auditLogService->log('RIGHTS_EXERCISE_CREATE', 'rights_exercise', null, ['applicant' => $exercise->applicantName]);

        $_SESSION['flash_success'] = 'Demande enregistrée avec succès.';
        header('Location: index.php?page=rights&action=list');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $exercise = $this->repository->findByIdAndUserId($id, $this->userId);

        if (!$exercise) {
            $_SESSION['flash_error'] = 'Dossier non trouvé.';
            header('Location: index.php?page=rights&action=list');
            exit;
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
        $exercise = $this->repository->findByIdAndUserId($id, $this->userId);

        if (!$exercise) {
            die('Dossier non trouvé');
        }

        $updatedExercise = RightsExercise::fromArray($_POST);
        $updatedExercise->id = $id;
        $updatedExercise->userId = $this->userId;

        $this->repository->save($updatedExercise);
        $this->auditLogService->log('RIGHTS_EXERCISE_UPDATE', 'rights_exercise', $id, ['applicant' => $updatedExercise->applicantName]);

        $_SESSION['flash_success'] = 'Dossier mis à jour.';
        header('Location: index.php?page=rights&action=list');
    }

    public function delete(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();
        $id = (int) ($_POST['id'] ?? 0);
        $this->repository->deleteAndUserId($id, $this->userId);
        $this->auditLogService->log('RIGHTS_EXERCISE_DELETE', 'rights_exercise', $id);

        $_SESSION['flash_success'] = 'Dossier supprimé.';
        header('Location: index.php?page=rights&action=list');
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

