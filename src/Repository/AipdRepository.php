<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\Aipd;
use PDO;

class AipdRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    /**
     * @return Aipd[]
     */
    public function findAllByOrganizationId(int $organizationId): array
    {
        $sql = 'SELECT a.*, t.name as treatment_name, u_dpo.name as dpo_name, u_mgr.name as manager_name
                FROM aipds a 
                JOIN treatments t ON a.treatment_id = t.id 
                LEFT JOIN users u_dpo ON a.dpo_id = u_dpo.id
                LEFT JOIN users u_mgr ON a.manager_id = u_mgr.id
                WHERE a.organization_id = :organization_id 
                ORDER BY a.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['organization_id' => $organizationId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => Aipd::fromArray($data), $results);
    }

    public function findByIdAndOrganizationId(int $id, int $organizationId): ?Aipd
    {
        $sql = 'SELECT a.*, t.name as treatment_name, u_dpo.name as dpo_name, u_mgr.name as manager_name
                FROM aipds a 
                JOIN treatments t ON a.treatment_id = t.id 
                LEFT JOIN users u_dpo ON a.dpo_id = u_dpo.id
                LEFT JOIN users u_mgr ON a.manager_id = u_mgr.id
                WHERE a.id = :id AND a.organization_id = :organization_id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'organization_id' => $organizationId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? Aipd::fromArray($data) : null;
    }

    public function save(Aipd $aipd): int
    {
        if ($aipd->id === null) {
            return $this->insert($aipd);
        } else {
            $this->update($aipd);
            return $aipd->id;
        }
    }

    private function insert(Aipd $aipd): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO aipds (treatment_id, organization_id, user_id, status, necessity_assessment, risk_assessment, measures_planned, dpo_opinion, manager_decision, is_high_risk, dpo_id, manager_id)
            VALUES (:treatment_id, :organization_id, :user_id, :status, :necessity_assessment, :risk_assessment, :measures_planned, :dpo_opinion, :manager_decision, :is_high_risk, :dpo_id, :manager_id)
            RETURNING id'
        );

        $stmt->execute([
            'treatment_id' => $aipd->treatmentId,
            'organization_id' => $aipd->organizationId,
            'user_id' => $aipd->userId,
            'status' => $aipd->status,
            'necessity_assessment' => $aipd->necessityAssessment,
            'risk_assessment' => $aipd->riskAssessment,
            'measures_planned' => $aipd->measuresPlanned,
            'dpo_opinion' => $aipd->dpoOpinion,
            'manager_decision' => $aipd->managerDecision,
            'is_high_risk' => (int) $aipd->isHighRisk,
            'dpo_id' => $aipd->dpoId,
            'manager_id' => $aipd->managerId
        ]);

        return (int) $stmt->fetchColumn();
    }

    private function update(Aipd $aipd): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE aipds SET 
                status = :status, 
                necessity_assessment = :necessity_assessment, 
                risk_assessment = :risk_assessment, 
                measures_planned = :measures_planned, 
                dpo_opinion = :dpo_opinion, 
                manager_decision = :manager_decision,
                is_high_risk = :is_high_risk,
                dpo_id = :dpo_id,
                manager_id = :manager_id,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id AND organization_id = :organization_id'
        );

        $stmt->execute([
            'id' => $aipd->id,
            'organization_id' => $aipd->organizationId,
            'status' => $aipd->status,
            'necessity_assessment' => $aipd->necessityAssessment,
            'risk_assessment' => $aipd->riskAssessment,
            'measures_planned' => $aipd->measuresPlanned,
            'dpo_opinion' => $aipd->dpoOpinion,
            'manager_decision' => $aipd->managerDecision,
            'is_high_risk' => (int) $aipd->isHighRisk,
            'dpo_id' => $aipd->dpoId,
            'manager_id' => $aipd->managerId
        ]);
    }

    public function deleteAndOrganizationId(int $id, int $organizationId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM aipds WHERE id = :id AND organization_id = :organization_id');
        $stmt->execute(['id' => $id, 'organization_id' => $organizationId]);
    }

    public function countByOrganizationId(int $organizationId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM aipds WHERE organization_id = :organization_id');
        $stmt->execute(['organization_id' => $organizationId]);
        return (int) $stmt->fetchColumn();
    }
}
