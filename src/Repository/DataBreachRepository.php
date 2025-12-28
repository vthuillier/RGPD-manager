<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\DataBreach;
use PDO;

class DataBreachRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    /**
     * @return DataBreach[]
     */
    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM data_breaches WHERE user_id = :user_id ORDER BY discovery_date DESC');
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => DataBreach::fromArray($data), $results);
    }

    public function findByIdAndUserId(int $id, int $userId): ?DataBreach
    {
        $stmt = $this->pdo->prepare('SELECT * FROM data_breaches WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? DataBreach::fromArray($data) : null;
    }

    public function save(DataBreach $breach): void
    {
        if ($breach->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO data_breaches (user_id, discovery_date, nature, data_categories, subjects_count, records_count, consequences, measures_taken, is_notified_authority, notification_authority_date, is_notified_individuals)
                 VALUES (:user_id, :discovery_date, :nature, :data_categories, :subjects_count, :records_count, :consequences, :measures_taken, :is_notified_authority, :notification_authority_date, :is_notified_individuals)'
            );
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE data_breaches SET discovery_date = :discovery_date, nature = :nature, data_categories = :data_categories, 
                        subjects_count = :subjects_count, records_count = :records_count, consequences = :consequences, 
                        measures_taken = :measures_taken, is_notified_authority = :is_notified_authority, 
                        notification_authority_date = :notification_authority_date, is_notified_individuals = :is_notified_individuals
                 WHERE id = :id AND user_id = :user_id'
            );
            $stmt->bindValue(':id', $breach->id);
        }

        $stmt->bindValue(':user_id', $breach->userId);
        $stmt->bindValue(':discovery_date', $breach->discoveryDate);
        $stmt->bindValue(':nature', $breach->nature);
        $stmt->bindValue(':data_categories', $breach->dataCategories);
        $stmt->bindValue(':subjects_count', $breach->subjectsCount, PDO::PARAM_INT);
        $stmt->bindValue(':records_count', $breach->recordsCount, PDO::PARAM_INT);
        $stmt->bindValue(':consequences', $breach->consequences);
        $stmt->bindValue(':measures_taken', $breach->measuresTaken);
        $stmt->bindValue(':is_notified_authority', (int) $breach->isNotifiedAuthority, PDO::PARAM_INT);
        $stmt->bindValue(':notification_authority_date', $breach->notificationAuthorityDate);
        $stmt->bindValue(':is_notified_individuals', (int) $breach->isNotifiedIndividuals, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function deleteAndUserId(int $id, int $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM data_breaches WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public function getStats(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM data_breaches WHERE user_id = :userId');
        $stmt->execute(['userId' => $userId]);
        $total = (int) $stmt->fetchColumn();

        $limit72h = date('Y-m-d H:i:s', strtotime('-72 hours'));
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) FROM data_breaches 
            WHERE user_id = :userId 
            AND is_notified_authority = FALSE 
            AND discovery_date <= :limit72h
        ');

        $stmt->execute(['userId' => $userId, 'limit72h' => $limit72h]);
        $urgent = (int) $stmt->fetchColumn();

        return [
            'total' => $total,
            'urgent' => $urgent
        ];
    }
}
