<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\Organization;
use App\Repository\DataBreachRepository;
use App\Repository\OrganizationRepository;
use App\Repository\RightsExerciseRepository;
use App\Repository\SubprocessorRepository;
use App\Repository\TreatmentRepository;
use App\Repository\UserRepository;
use Exception;

class OrganizationController extends BaseController
{
    private OrganizationRepository $repository;

    public function __construct()
    {
        $this->ensureRole(['super_admin']);
        $this->repository = new OrganizationRepository();
    }

    public function list(): void
    {
        $organizations = $this->repository->findAll();
        $this->render('organizations/list', [
            'organizations' => $organizations,
            'title' => 'Gestion des organismes'
        ]);
    }

    public function create(): void
    {
        $this->render('organizations/form', [
            'title' => 'Ajouter un organisme'
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        try {
            $name = $_POST['name'] ?? '';
            if (!$name) {
                throw new Exception("Le nom de l'organisme est obligatoire.");
            }

            $id = $this->repository->save(new Organization(null, $name));
            $this->auditLog('ORG_CREATE', 'organization', $id, ['name' => $name]);

            $_SESSION['flash_success'] = "Organisme créé avec succès.";
            $this->redirect('index.php?page=organization&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('index.php?page=organization&action=create');
        }
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $organization = $this->repository->find($id);

        if (!$organization) {
            $_SESSION['flash_error'] = "Organisme non trouvé.";
            $this->redirect('index.php?page=organization&action=list');
        }

        $this->render('organizations/form', [
            'title' => 'Modifier l\'organisme',
            'organization' => $organization
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();
        try {
            $id = (int) ($_POST['id'] ?? 0);
            $name = $_POST['name'] ?? '';

            if (!$id || !$name) {
                throw new Exception("Données manquantes.");
            }

            $this->repository->save(new Organization($id, $name));
            $this->auditLog('ORG_UPDATE', 'organization', $id, ['name' => $name]);

            $_SESSION['flash_success'] = "Organisme mis à jour.";
            $this->redirect('index.php?page=organization&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('index.php?page=organization&action=edit&id=' . ($id ?? 0));
        }
    }

    public function delete(): void
    {
        $this->validateCsrf();
        try {
            $id = (int) ($_POST['id'] ?? 0);

            if (!$id) {
                throw new Exception("ID de l'organisme manquant.");
            }

            if ($id === (int) ($_SESSION['organization_id'] ?? 0)) {
                throw new Exception("Vous ne pouvez pas supprimer l'organisme sur lequel vous êtes actuellement positionné. Basculez d'abord vers un autre organisme.");
            }

            $this->repository->delete($id);
            $this->auditLog('ORG_DELETE', 'organization', $id);

            $_SESSION['flash_success'] = "Organisme supprimé avec succès.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Erreur lors de la suppression : " . $e->getMessage();
        }

        $this->redirect('index.php?page=organization&action=list');
    }

    public function backup(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $organization = $this->repository->find($id);

        if (!$organization) {
            $_SESSION['flash_error'] = "Organisme non trouvé.";
            $this->redirect('index.php?page=organization&action=list');
        }

        $data = $this->gatherOrganizationData($id);
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $this->auditLog('ORG_BACKUP', 'organization', $id);

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="backup_' . urlencode($organization->name) . '_' . date('Y-m-d') . '.json"');
        echo $json;
        exit;
    }

    private function gatherOrganizationData(int $orgId): array
    {
        $treatRepo = new TreatmentRepository();
        $subRepo = new SubprocessorRepository();
        $rightsRepo = new RightsExerciseRepository();
        $breachRepo = new DataBreachRepository();
        $userRepo = new UserRepository();

        return [
            'organization' => $this->repository->find($orgId),
            'treatments' => $treatRepo->findAllByOrganizationId($orgId),
            'subprocessors' => $subRepo->findAllByOrganizationId($orgId),
            'rights_exercises' => $rightsRepo->findAllByOrganizationId($orgId),
            'data_breaches' => $breachRepo->findAllByOrganizationId($orgId),
            'users' => $userRepo->findAllByOrganizationId($orgId)
        ];
    }
}
