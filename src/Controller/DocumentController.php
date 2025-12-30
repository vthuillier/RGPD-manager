<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\DocumentService;
use Exception;

class DocumentController extends BaseController
{
    private DocumentService $service;
    private int $organizationId;

    public function __construct()
    {
        $this->ensureAuthenticated();
        $this->service = new DocumentService();
        $this->organizationId = (int) $_SESSION['organization_id'];
    }

    public function upload(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();

        $entityType = $_POST['entity_type'] ?? '';
        $entityId = (int) ($_POST['entity_id'] ?? 0);
        $redirect = $_POST['redirect'] ?? 'index.php';

        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            try {
                $this->service->upload($_FILES['document'], $entityType, $entityId, $this->organizationId);
                $_SESSION['flash_success'] = "Document ajouté avec succès.";
            } catch (Exception $e) {
                $_SESSION['flash_error'] = $e->getMessage();
            }
        }

        header('Location: ' . $redirect);
        exit;
    }

    public function delete(): void
    {
        $this->validateCsrf();
        $this->validateNotGuest();

        $id = (int) ($_POST['id'] ?? 0);
        $redirect = $_POST['redirect'] ?? 'index.php';

        try {
            $this->service->deleteDocument($id, $this->organizationId);
            $this->auditLog('DOCUMENT_DELETE', 'document', $id);
            $_SESSION['flash_success'] = "Document supprimé.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        header('Location: ' . $redirect);
        exit;
    }
}
