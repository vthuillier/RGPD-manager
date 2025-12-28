<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Treatment;

class ExportService
{
    /**
     * @param Treatment[] $treatments
     */
    public function exportCsv(array $treatments): void
    {
        $filename = "registre_rgpd_" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // CSV Headers
        fputcsv($output, [
            'Nom',
            'Finalité',
            'Base légale',
            'Catégories de données',
            'Durée de conservation',
            'Date de création'
        ], ';');

        foreach ($treatments as $treatment) {
            fputcsv($output, [
                $treatment->name,
                $treatment->purpose,
                $treatment->legalBasis,
                $treatment->dataCategories,
                $treatment->retentionPeriod,
                $treatment->createdAt
            ], ';');
        }

        fclose($output);
        exit;
    }
}
