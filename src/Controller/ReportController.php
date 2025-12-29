<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ReportService;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController extends BaseController
{
    private ReportService $service;

    public function __construct()
    {
        $this->ensureAuthenticated();
        $this->service = new ReportService();
    }

    public function generateAnnual(): void
    {
        $data = $this->service->getAnnualData((int) $_SESSION['organization_id']);


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

    public function generateAipd(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $data = $this->service->getAipdData($id, (int) $_SESSION['organization_id']);

        // Add logo base64
        $logoPath = __DIR__ . '/../../public/assets/logo_texte.png';
        $logoBase64 = '';
        if (file_exists($logoPath) && extension_loaded('gd')) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }

        extract($data);

        ob_start();
        require __DIR__ . '/../../templates/reports/aipd_pdf.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "AIPD_" . preg_replace('/[^a-z0-9]/i', '_', $aipd->treatmentName) . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]);
        exit;
    }
}
