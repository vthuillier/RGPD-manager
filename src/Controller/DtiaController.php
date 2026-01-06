<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Dtia;
use App\Repository\DtiaRepository;
use App\Repository\TreatmentRepository;
use App\Repository\SubprocessorRepository;
use Exception;

class DtiaController extends BaseController
{
    private DtiaRepository $repository;
    private int $organizationId;

    public function __construct()
    {
        $this->ensureAuthenticated();
        $this->repository = new DtiaRepository();
        $this->organizationId = (int) $_SESSION['organization_id'];
    }

    public function list(): void
    {
        $dtias = $this->repository->findAllByOrganizationId($this->organizationId);
        $this->render('dtia/list', [
            'title' => 'Transferts Hors-UE (DTIA)',
            'dtias' => $dtias
        ]);
    }

    public function create(): void
    {
        $treatmentRepo = new TreatmentRepository();
        $subprocessorRepo = new SubprocessorRepository();

        $this->render('dtia/form', [
            'title' => 'Nouvelle DTIA',
            'treatments' => $treatmentRepo->findAllByOrganizationId($this->organizationId),
            'subprocessors' => $subprocessorRepo->findAllByOrganizationId($this->organizationId),
            'dtia' => null
        ]);
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $dtia = $this->repository->findById($id, $this->organizationId);

        if (!$dtia) {
            $_SESSION['flash_error'] = "DTIA non trouvée.";
            $this->redirect('index.php?page=dtia&action=list');
        }

        $treatmentRepo = new TreatmentRepository();
        $subprocessorRepo = new SubprocessorRepository();

        $this->render('dtia/form', [
            'title' => 'Modifier la DTIA',
            'dtia' => $dtia,
            'treatments' => $treatmentRepo->findAllByOrganizationId($this->organizationId),
            'subprocessors' => $subprocessorRepo->findAllByOrganizationId($this->organizationId)
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();

        try {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : null;

            $dtia = new Dtia(
                $id,
                $this->organizationId,
                !empty($_POST['treatment_id']) ? (int) $_POST['treatment_id'] : null,
                !empty($_POST['subprocessor_id']) ? (int) $_POST['subprocessor_id'] : null,
                $_POST['country_name'] ?? '',
                $_POST['transfer_mechanism'] ?? '',
                $_POST['data_exporter'] ?? '',
                $_POST['data_importer'] ?? '',
                $_POST['data_categories'] ?? '',
                $_POST['risk_level'] ?? 'medium',
                $_POST['supplementary_measures'] ?? null,
                $_POST['assessment_date'] ?? date('Y-m-d'),
                $_POST['status'] ?? 'draft'
            );

            $this->repository->save($dtia);
            $this->auditLog($id ? 'DTIA_UPDATE' : 'DTIA_CREATE', 'dtia', $id);

            $_SESSION['flash_success'] = "Évaluation de transfert enregistrée.";
            $this->redirect('index.php?page=dtia&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Erreur : " . $e->getMessage();
            $this->redirect('index.php?page=dtia&action=list');
        }
    }

    public function delete(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($this->repository->delete($id, $this->organizationId)) {
            $this->auditLog('DTIA_DELETE', 'dtia', $id);
            $_SESSION['flash_success'] = "DTIA supprimée.";
        } else {
            $_SESSION['flash_error'] = "Erreur lors de la suppression.";
        }
        $this->redirect('index.php?page=dtia&action=list');
    }
}
