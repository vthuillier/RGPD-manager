<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SecurityMeasureRepository;
use App\Entity\SecurityMeasure;
use Exception;

class SecurityMeasureController extends BaseController
{
    private SecurityMeasureRepository $repository;
    private int $organizationId;
    public function __construct()
    {
        $this->ensureAuthenticated();
        $this->repository = new SecurityMeasureRepository();
        $this->organizationId = (int) $_SESSION['organization_id'];
    }

    public function list(): void
    {
        $measures = $this->repository->findByOrganizationId($this->organizationId);
        $this->render('security_measures/list', [
            'title' => 'Mesures de sécurité personnalisées',
            'measures' => $measures
        ]);
    }

    public function create(): void
    {
        $this->render('security_measures/form', [
            'title' => 'Nouvelle mesure de sécurité',
            'measure' => null
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();
        try {
            $measure = new SecurityMeasure(null, $_POST['category'] ?? '', $_POST['name'] ?? '', $_POST['description'] ?? null, (int) ($_POST['weight'] ?? 1), $this->organizationId);
            $this->repository->save($measure);
            $this->auditLog('SECURITY_MEASURE_CREATE', 'security_measure', null, ['name' => $measure->name]);
            $_SESSION['flash_success'] = "Mesure de sécurité ajoutée.";
            $this->redirect('index.php?page=security_measure&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('index.php?page=security_measure&action=create');
        }
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $measure = $this->repository->findById($id);
        if (!$measure || $measure->organizationId !== $this->organizationId) {
            $_SESSION['flash_error'] = "Mesure introuvable.";
            $this->redirect('index.php?page=security_measure&action=list');
        }

        $this->render('security_measures/form', [
            'title' => 'Modifier la mesure',
            'measure' => $measure
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();
        $id = (int) ($_POST['id'] ?? 0);
        $existing = $this->repository->findById($id);
        if (!$existing || $existing->organizationId !== $this->organizationId) {
            $_SESSION['flash_error'] = "Mesure introuvable.";
            $this->redirect('index.php?page=security_measure&action=list');
        }

        try {
            $measure = new SecurityMeasure($id, $_POST['category'] ?? '', $_POST['name'] ?? '', $_POST['description'] ?? null, (int) ($_POST['weight'] ?? 1), $this->organizationId);
            $this->repository->save($measure);
            $this->auditLog('SECURITY_MEASURE_UPDATE', 'security_measure', $id, ['name' => $measure->name]);
            $_SESSION['flash_success'] = "Mesure mise à jour.";
            $this->redirect('index.php?page=security_measure&action=list');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $this->redirect('index.php?page=security_measure&action=edit&id=' . $id);
        }
    }

    public function delete(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();
        $id = (int) ($_POST['id'] ?? 0);
        $this->repository->delete($id, $this->organizationId);
        $this->auditLog('SECURITY_MEASURE_DELETE', 'security_measure', $id);
        $_SESSION['flash_success'] = "Mesure supprimée.";
        $this->redirect('index.php?page=security_measure&action=list');
    }
}
