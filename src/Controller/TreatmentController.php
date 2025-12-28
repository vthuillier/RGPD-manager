<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\TreatmentService;
use App\Service\ExportService;
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

    public function dashboard(): void
    {
        $stats = $this->service->getStats($this->userId);
        $this->render('treatments/dashboard', [
            'title' => 'Tableau de bord',
            'stats' => $stats
        ]);
    }

    public function list(): void
    {
        $filters = [
            'search' => $_GET['search'] ?? '',
            'legal_basis' => $_GET['legal_basis'] ?? ''
        ];

        $treatments = $this->service->getTreatmentsForUser($this->userId, $filters);

        $this->render('treatments/list', [
            'title' => 'Mon Registre',
            'treatments' => $treatments,
            'filters' => $filters
        ]);
    }

    public function create(): void
    {
        $subprocessorRepo = new \App\Repository\SubprocessorRepository();
        $allSubprocessors = $subprocessorRepo->findAllByUserId($this->userId);

        $this->render('treatments/form', [
            'title' => 'Nouveau traitement',
            'allSubprocessors' => $allSubprocessors,
            'selectedSubprocessors' => []
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

        $subprocessorRepo = new \App\Repository\SubprocessorRepository();
        $allSubprocessors = $subprocessorRepo->findAllByUserId($this->userId);
        $selectedSubprocessors = $this->service->getSubprocessorIds($id);

        $this->render('treatments/form', [
            'title' => 'Modifier le traitement',
            'treatment' => $treatment,
            'allSubprocessors' => $allSubprocessors,
            'selectedSubprocessors' => $selectedSubprocessors
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

    public function exportCsv(): void
    {
        $treatments = $this->service->getTreatmentsForUser($this->userId);
        $exportService = new ExportService();
        $exportService->exportCsv($treatments);
    }

    public function exportPdf(): void
    {
        $treatments = $this->service->getTreatmentsForUser($this->userId);
        $this->render('treatments/print', [
            'title' => 'Registre des activités de traitement',
            'treatments' => $treatments,
            'isPrint' => true
        ], true);
    }

    private function render(string $template, array $data = [], bool $standalone = false): void
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
