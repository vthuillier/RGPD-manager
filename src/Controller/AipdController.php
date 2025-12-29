<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Aipd;
use App\Repository\AipdRepository;
use App\Repository\TreatmentRepository;
use App\Repository\UserRepository;

class AipdController extends BaseController
{
    private AipdRepository $repository;
    private TreatmentRepository $treatmentRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->ensureAuthenticated();
        $this->repository = new AipdRepository();
        $this->treatmentRepository = new TreatmentRepository();
        $this->userRepository = new UserRepository();
    }

    public function list(): void
    {
        $aipds = $this->repository->findAllByOrganizationId((int) $_SESSION['organization_id']);
        $this->render('aipd/list', [
            'aipds' => $aipds,
            'title' => 'Analyses d\'Impact (AIPD)'
        ]);
    }

    public function create(): void
    {
        $orgId = (int) $_SESSION['organization_id'];
        $treatments = $this->treatmentRepository->findAllByOrganizationId($orgId);
        $users = $this->userRepository->findAllByOrganizationContext($orgId);

        $this->render('aipd/form', [
            'treatments' => $treatments,
            'users' => $users,
            'title' => 'Nouvelle Analyse d\'Impact'
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();

        $data = $_POST;
        $data['user_id'] = (int) $_SESSION['user_id'];
        $data['organization_id'] = (int) $_SESSION['organization_id'];
        $data['dpo_id'] = !empty($_POST['dpo_id']) ? (int) $_POST['dpo_id'] : null;
        $data['manager_id'] = !empty($_POST['manager_id']) ? (int) $_POST['manager_id'] : null;

        $aipd = Aipd::fromArray($data);

        $id = $this->repository->save($aipd);
        $this->auditLog('AIPD_CREATE', 'aipd', $id, ['treatment_id' => $aipd->treatmentId]);

        $_SESSION['flash_success'] = 'Analyse d\'impact créée avec succès.';
        $this->redirect('index.php?page=aipd&action=list');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $orgId = (int) $_SESSION['organization_id'];
        $aipd = $this->repository->findByIdAndOrganizationId($id, $orgId);

        if (!$aipd) {
            $_SESSION['flash_error'] = 'Analyse d\'impact non trouvée.';
            $this->redirect('index.php?page=aipd&action=list');
        }

        $treatments = $this->treatmentRepository->findAllByOrganizationId($orgId);
        $users = $this->userRepository->findAllByOrganizationContext($orgId);

        $this->render('aipd/form', [
            'aipd' => $aipd,
            'treatments' => $treatments,
            'users' => $users,
            'title' => 'Modifier l\'Analyse d\'Impact'
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();

        $id = (int) ($_POST['id'] ?? 0);
        $aipd = $this->repository->findByIdAndOrganizationId($id, (int) $_SESSION['organization_id']);

        if (!$aipd) {
            $_SESSION['flash_error'] = 'Analyse d\'impact non trouvée.';
            $this->redirect('index.php?page=aipd&action=list');
        }

        $aipd->status = $_POST['status'] ?? 'draft';
        $aipd->necessityAssessment = $_POST['necessity_assessment'] ?? null;
        $aipd->riskAssessment = $_POST['risk_assessment'] ?? null;
        $aipd->measuresPlanned = $_POST['measures_planned'] ?? null;
        $aipd->dpoOpinion = $_POST['dpo_opinion'] ?? null;
        $aipd->managerDecision = $_POST['manager_decision'] ?? null;
        $aipd->isHighRisk = isset($_POST['is_high_risk']);
        $aipd->dpoId = !empty($_POST['dpo_id']) ? (int) $_POST['dpo_id'] : null;
        $aipd->managerId = !empty($_POST['manager_id']) ? (int) $_POST['manager_id'] : null;

        $this->repository->save($aipd);
        $this->auditLog('AIPD_UPDATE', 'aipd', $id, ['status' => $aipd->status]);

        $_SESSION['flash_success'] = 'Analyse d\'impact mise à jour.';
        $this->redirect('index.php?page=aipd&action=list');
    }

    public function view(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $aipd = $this->repository->findByIdAndOrganizationId($id, (int) $_SESSION['organization_id']);

        if (!$aipd) {
            $_SESSION['flash_error'] = 'Analyse d\'impact non trouvée.';
            $this->redirect('index.php?page=aipd&action=list');
        }

        $this->render('aipd/view', [
            'aipd' => $aipd,
            'title' => 'Détails de l\'Analyse d\'Impact'
        ]);
    }

    public function delete(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();

        $id = (int) ($_POST['id'] ?? 0);
        $this->repository->deleteAndOrganizationId($id, (int) $_SESSION['organization_id']);
        $this->auditLog('AIPD_DELETE', 'aipd', $id);

        $_SESSION['flash_success'] = 'Analyse d\'impact supprimée.';
        $this->redirect('index.php?page=aipd&action=list');
    }
}
