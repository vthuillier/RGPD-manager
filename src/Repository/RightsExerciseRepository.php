<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\RightsExercise;
use PDO;

class RightsExerciseRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    /**
     * @return RightsExercise[]
     */
    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM rights_exercises WHERE user_id = :user_id ORDER BY request_date DESC');
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => RightsExercise::fromArray($data), $results);
    }

    public function findByIdAndUserId(int $id, int $userId): ?RightsExercise
    {
        $stmt = $this->pdo->prepare('SELECT * FROM rights_exercises WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? RightsExercise::fromArray($data) : null;
    }

    public function save(RightsExercise $exercise): void
    {
        if ($exercise->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO rights_exercises (user_id, applicant_name, request_date, request_type, status, completion_date, details)
                 VALUES (:user_id, :applicant_name, :request_date, :request_type, :status, :completion_date, :details)'
            );
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE rights_exercises SET applicant_name = :applicant_name, request_date = :request_date, 
                        request_type = :request_type, status = :status, completion_date = :completion_date, details = :details
                 WHERE id = :id AND user_id = :user_id'
            );
            $stmt->bindValue(':id', $exercise->id);
        }

        $stmt->bindValue(':user_id', $exercise->userId);
        $stmt->bindValue(':applicant_name', $exercise->applicantName);
        $stmt->bindValue(':request_date', $exercise->requestDate);
        $stmt->bindValue(':request_type', $exercise->requestType);
        $stmt->bindValue(':status', $exercise->status);
        $stmt->bindValue(':completion_date', $exercise->completionDate);
        $stmt->bindValue(':details', $exercise->details);

        $stmt->execute();
    }

    public function deleteAndUserId(int $id, int $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM rights_exercises WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public function getStats(int $userId): array
    {
        // Total
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM rights_exercises WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $total = (int) $stmt->fetchColumn();

        // Count by status
        $stmt = $this->pdo->prepare('SELECT status, COUNT(*) as count FROM rights_exercises WHERE user_id = :user_id GROUP BY status');
        $stmt->execute(['user_id' => $userId]);
        $byStatus = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Urgent (pending and more than 23 days old)
        $limitDate = date('Y-m-d', strtotime('-23 days'));
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM rights_exercises WHERE user_id = :user_id AND status = \'En attente\' AND request_date <= :limit_date');
        $stmt->execute(['user_id' => $userId, 'limit_date' => $limitDate]);
        $urgent = (int) $stmt->fetchColumn();

        return [
            'total' => $total,
            'pending' => $byStatus['En attente'] ?? 0,
            'completed' => $byStatus['Terminé'] ?? 0,
            'urgent' => $urgent
        ];
    }
}

