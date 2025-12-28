<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\TreatmentService;
use App\Entity\Treatment;
use Exception;

class TreatmentController
{
    private TreatmentService $service;
    private int $userId;

    public function __construct()
    {
        $this->ensureAuthenticated();
        $this->service = new TreatmentService();
        $this->userId = $_SESSION['user_id'];
    }

    public function list(): void
    {
        $treatments = $this->service->getTreatmentsForUser($this->userId);
        $this->render('treatments/list', [
            'title' => 'Mon Registre',
            'treatments' => $treatments
        ]);
    }

    public function create(): void
    {
        $this->render('treatments/form', [
            'title' => 'Nouveau traitement'
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        try {
            $data = $_POST;
            $data['user_id'] = $this->userId;
            $this->service->createTreatment($data);
            $_SESSION['flash_success'] = "Traitement ajouté avec succès.";
            $this->redirect('index.php?page=treatment&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('index.php?page=treatment&action=create');
        }
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $treatment = $this->service->getTreatmentForUser($id, $this->userId);

        if (!$treatment) {
            $_SESSION['flash_error'] = "Traitement introuvable ou vous n'avez pas les droits.";
            $this->redirect('index.php?page=treatment&action=list');
        }

        $this->render('treatments/form', [
            'title' => 'Modifier le traitement',
            'treatment' => $treatment
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        try {
            $data = $_POST;
            $data['user_id'] = $this->userId;
            $this->service->updateTreatmentForUser($id, $this->userId, $data);
            $_SESSION['flash_success'] = "Traitement mis à jour.";
            $this->redirect('index.php?page=treatment&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('index.php?page=treatment&action=edit&id=' . $id);
        }
    }

    public function delete(): void
    {
        $this->validateCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $this->service->deleteTreatmentForUser($id, $this->userId);
        $_SESSION['flash_success'] = "Traitement supprimé.";
        $this->redirect('index.php?page=treatment&action=list');
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

    private function ensureAuthenticated(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    }
}
