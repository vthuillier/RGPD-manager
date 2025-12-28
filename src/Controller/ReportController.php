<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ReportService;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController
{
    private ReportService $service;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
        $this->service = new ReportService();
    }

    public function generateAnnual(): void
    {
        $data = $this->service->getAnnualData((int) $_SESSION['user_id']);

        // Add logo base64 (only if GD is available for Dompdf)
        $logoPath = __DIR__ . '/../../public/assets/logo_texte.png';
        $logoBase64 = '';
        if (file_exists($logoPath) && extension_loaded('gd')) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }


        // Render HTML
        extract($data);

        ob_start();
        require __DIR__ . '/../../templates/reports/annual_pdf.php';
        $html = ob_get_clean();

        // Setup Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output to browser
        $filename = "Rapport_Annuel_RGPD_" . date('Y') . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }
}
