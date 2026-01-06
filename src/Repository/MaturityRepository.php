<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\MaturityAssessment;
use PDO;

class MaturityRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function save(MaturityAssessment $assessment): int
    {
        $sql = "INSERT INTO maturity_assessments 
                (organization_id, user_id, governance_score, registry_score, rights_score, security_score, risk_score, comments)
                VALUES (:org_id, :user_id, :gov, :reg, :rights, :sec, :risk, :comments)
                RETURNING id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'org_id' => $assessment->organizationId,
            'user_id' => $assessment->userId,
            'gov' => $assessment->governanceScore,
            'reg' => $assessment->registryScore,
            'rights' => $assessment->rightsScore,
            'sec' => $assessment->securityScore,
            'risk' => $assessment->riskScore,
            'comments' => $assessment->comments
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return MaturityAssessment[]
     */
    public function findAllByOrganizationId(int $orgId): array
    {
        $sql = "SELECT * FROM maturity_assessments 
                WHERE organization_id = :org_id 
                ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['org_id' => $orgId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($data) => MaturityAssessment::fromArray($data), $results);
    }

    public function findLatestByOrganizationId(int $orgId): ?MaturityAssessment
    {
        $sql = "SELECT * FROM maturity_assessments 
                WHERE organization_id = :org_id 
                ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['org_id' => $orgId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? MaturityAssessment::fromArray($data) : null;
    }
}
