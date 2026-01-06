<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Entity\Dtia;
use PDO;

class DtiaRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function save(Dtia $dtia): bool
    {
        if ($dtia->id) {
            $sql = "UPDATE dtias SET 
                    treatment_id = :t_id, 
                    subprocessor_id = :s_id, 
                    country_name = :country, 
                    transfer_mechanism = :mech, 
                    data_exporter = :exp, 
                    data_importer = :imp, 
                    data_categories = :cats, 
                    risk_level = :risk, 
                    supplementary_measures = :supp, 
                    assessment_date = :adate, 
                    status = :status,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id AND organization_id = :org_id";
            $params = [
                'id' => $dtia->id,
                'org_id' => $dtia->organizationId,
                't_id' => $dtia->treatmentId,
                's_id' => $dtia->subprocessorId,
                'country' => $dtia->countryName,
                'mech' => $dtia->transferMechanism,
                'exp' => $dtia->dataExporter,
                'imp' => $dtia->dataImporter,
                'cats' => $dtia->dataCategories,
                'risk' => $dtia->riskLevel,
                'supp' => $dtia->supplementaryMeasures,
                'adate' => $dtia->assessmentDate,
                'status' => $dtia->status
            ];
        } else {
            $sql = "INSERT INTO dtias 
                    (organization_id, treatment_id, subprocessor_id, country_name, transfer_mechanism, 
                     data_exporter, data_importer, data_categories, risk_level, supplementary_measures, 
                     assessment_date, status)
                    VALUES (:org_id, :t_id, :s_id, :country, :mech, :exp, :imp, :cats, :risk, :supp, :adate, :status)";
            $params = [
                'org_id' => $dtia->organizationId,
                't_id' => $dtia->treatmentId,
                's_id' => $dtia->subprocessorId,
                'country' => $dtia->countryName,
                'mech' => $dtia->transferMechanism,
                'exp' => $dtia->dataExporter,
                'imp' => $dtia->dataImporter,
                'cats' => $dtia->dataCategories,
                'risk' => $dtia->riskLevel,
                'supp' => $dtia->supplementaryMeasures,
                'adate' => $dtia->assessmentDate,
                'status' => $dtia->status
            ];
        }

        return $this->pdo->prepare($sql)->execute($params);
    }

    public function findById(int $id, int $orgId): ?Dtia
    {
        $stmt = $this->pdo->prepare("SELECT * FROM dtias WHERE id = :id AND organization_id = :org_id");
        $stmt->execute(['id' => $id, 'org_id' => $orgId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? Dtia::fromArray($data) : null;
    }

    /**
     * @return Dtia[]
     */
    public function findAllByOrganizationId(int $orgId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM dtias WHERE organization_id = :org_id ORDER BY assessment_date DESC");
        $stmt->execute(['org_id' => $orgId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($d) => Dtia::fromArray($d), $results);
    }

    public function delete(int $id, int $orgId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM dtias WHERE id = :id AND organization_id = :org_id");
        return $stmt->execute(['id' => $id, 'org_id' => $orgId]);
    }
}
