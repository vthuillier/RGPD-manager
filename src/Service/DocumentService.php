<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Document;
use App\Repository\DocumentRepository;
use Exception;

class DocumentService
{
    private DocumentRepository $repository;
    private string $uploadDir;

    public function __construct()
    {
        $this->repository = new DocumentRepository();
        // Le dossier public/uploads doit exister et être accessible en écriture
        $this->uploadDir = __DIR__ . '/../../public/uploads/';
    }

    /**
     * @param array $file L'élément provenant de $_FILES['name']
     */
    public function upload(array $file, string $entityType, int $entityId, int $organizationId): int
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur lors du téléchargement du fichier.");
        }

        // Sécurité de base sur les types de fichiers (extensibilité possible)
        $allowedExtensions = ['pdf', 'docx', 'jpg', 'png', 'txt', 'xlsx'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            throw new Exception("Type de fichier non autorisé. Autorisés : " . implode(', ', $allowedExtensions));
        }

        // Créer un nom unique pour éviter les collisions
        $newFileName = bin2hex(random_bytes(16)) . '.' . $ext;
        $relativeDir = $organizationId . '/' . $entityType;
        $targetDir = $this->uploadDir . $relativeDir;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetPath = $targetDir . '/' . $newFileName;
        $relativeFilePath = 'uploads/' . $relativeDir . '/' . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Impossible de déplacer le fichier vers sa destination.");
        }

        $document = new Document(
            null,
            $organizationId,
            $entityType,
            $entityId,
            $relativeFilePath,
            $file['name'],
            $file['type'],
            (int) $file['size']
        );

        return $this->repository->save($document);
    }

    /**
     * @return Document[]
     */
    public function getDocuments(string $entityType, int $entityId, int $organizationId): array
    {
        return $this->repository->findByEntity($entityType, $entityId, $organizationId);
    }

    public function deleteDocument(int $id, int $organizationId): void
    {
        $doc = $this->repository->findById($id, $organizationId);
        if ($doc) {
            $fullPath = __DIR__ . '/../../public/' . $doc->filePath;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            $this->repository->delete($id, $organizationId);
        }
    }
}
