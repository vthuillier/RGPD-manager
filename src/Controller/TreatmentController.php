<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\TreatmentService;
use App\Service\ExportService;
use App\Entity\Treatment;
use Exception;

class TreatmentController extends BaseController
{
    private TreatmentService $service;
    private \App\Service\DocumentService $documentService;
    private int $userId;
    private int $organizationId;

    public function __construct()
    {
        $this->ensureAuthenticated();
        $this->service = new TreatmentService();
        $this->documentService = new \App\Service\DocumentService();
        $this->userId = (int) $_SESSION['user_id'];
        $this->organizationId = (int) $_SESSION['organization_id'];
    }


    public function dashboard(): void
    {
        $stats = $this->service->getStatsForOrganization($this->organizationId);
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

        $treatments = $this->service->getTreatmentsForOrganization($this->organizationId, $filters);

        $this->render('treatments/list', [
            'title' => 'Mon Registre',
            'treatments' => $treatments,
            'filters' => $filters
        ]);
    }

    public function create(): void
    {
        $subprocessorRepo = new \App\Repository\SubprocessorRepository();
        $allSubprocessors = $subprocessorRepo->findAllByOrganizationId($this->organizationId);

        $this->render('treatments/form', [
            'title' => 'Nouveau traitement',
            'allSubprocessors' => $allSubprocessors,
            'selectedSubprocessors' => [],
            'documents' => []
        ]);
    }


    public function store(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();
        try {
            $data = $_POST;
            $data['user_id'] = $this->userId;
            $data['organization_id'] = $this->organizationId;
            $treatmentId = $this->service->createTreatment($data);

            if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
                $this->documentService->upload($_FILES['document'], 'treatment', $treatmentId, $this->organizationId);
            }

            $this->auditLog('TREATMENT_CREATE', 'treatment', $treatmentId, ['name' => $data['name'] ?? '']);

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
        $treatment = $this->service->getTreatmentForOrganization($id, $this->organizationId);

        if (!$treatment) {
            $_SESSION['flash_error'] = "Traitement introuvable ou vous n'avez pas les droits.";
            $this->redirect('index.php?page=treatment&action=list');
        }

        $subprocessorRepo = new \App\Repository\SubprocessorRepository();
        $allSubprocessors = $subprocessorRepo->findAllByOrganizationId($this->organizationId);
        $selectedSubprocessors = $this->service->getSubprocessorIds($id);

        $documents = $this->documentService->getDocuments('treatment', $id, $this->organizationId);

        $this->render('treatments/form', [
            'title' => 'Modifier le traitement',
            'treatment' => $treatment,
            'allSubprocessors' => $allSubprocessors,
            'selectedSubprocessors' => $selectedSubprocessors,
            'documents' => $documents
        ]);

    }

    public function update(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();
        $id = (int) ($_POST['id'] ?? 0);
        try {
            $data = $_POST;
            $data['user_id'] = $this->userId;
            $data['organization_id'] = $this->organizationId;
            $this->service->updateTreatmentForOrganization($id, $this->organizationId, $data);

            if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
                $this->documentService->upload($_FILES['document'], 'treatment', $id, $this->organizationId);
            }

            $this->auditLog('TREATMENT_UPDATE', 'treatment', $id, ['name' => $data['name'] ?? '']);

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
        $this->validateNotGuest();
        $id = (int) ($_POST['id'] ?? 0);
        $this->service->deleteTreatmentForOrganization($id, $this->organizationId);

        $this->auditLog('TREATMENT_DELETE', 'treatment', $id);

        $_SESSION['flash_success'] = "Traitement supprimé.";
        $this->redirect('index.php?page=treatment&action=list');
    }


    public function exportCsv(): void
    {
        $treatments = $this->service->getTreatmentsForOrganization($this->organizationId);
        $exportService = new ExportService();
        $this->auditLog('TREATMENT_EXPORT_CSV', 'treatment');
        $exportService->exportCsv($treatments);
    }


    public function exportPdf(): void
    {
        $treatments = $this->service->getTreatmentsForOrganization($this->organizationId);
        $this->auditLog('TREATMENT_EXPORT_PDF', 'treatment');
        $this->render('treatments/print', [
            'title' => 'Registre des activités de traitement',
            'treatments' => $treatments,
            'isPrint' => true
        ], true);
    }
}

