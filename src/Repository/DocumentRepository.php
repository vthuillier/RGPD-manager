<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\Document;
use PDO;

class DocumentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::get();
    }

    public function save(Document $doc): int
    {
        if ($doc->id) {
            $stmt = $this->db->prepare("
                UPDATE documents 
                SET file_name = ?, file_type = ?, file_size = ?
                WHERE id = ? AND organization_id = ?
            ");
            $stmt->execute([
                $doc->fileName,
                $doc->fileType,
                $doc->fileSize,
                $doc->id,
                $doc->organizationId
            ]);
            return $doc->id;
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO documents (organization_id, entity_type, entity_id, file_path, file_name, file_type, file_size)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $doc->organizationId,
                $doc->entityType,
                $doc->entityId,
                $doc->filePath,
                $doc->fileName,
                $doc->fileType,
                $doc->fileSize
            ]);
            return (int) $this->db->lastInsertId();
        }
    }

    /**
     * @return Document[]
     */
    public function findByEntity(string $entityType, int $entityId, int $organizationId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM documents 
            WHERE entity_type = ? AND entity_id = ? AND organization_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$entityType, $entityId, $organizationId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => Document::fromArray($data), $results);
    }

    public function findById(int $id, int $organizationId): ?Document
    {
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE id = ? AND organization_id = ?");
        $stmt->execute([$id, $organizationId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? Document::fromArray($data) : null;
    }

    public function delete(int $id, int $organizationId): void
    {
        $stmt = $this->db->prepare("DELETE FROM documents WHERE id = ? AND organization_id = ?");
        $stmt->execute([$id, $organizationId]);
    }
}
