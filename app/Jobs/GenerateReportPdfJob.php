<?php
namespace App\Jobs;

use App\Helpers\Database;
use App\Helpers\PdfHelper;

class GenerateReportPdfJob extends Job {
    private int $reportId;

    public function __construct(int $reportId) {
        $this->reportId = $reportId;
    }

    public function handle(): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, c.name AS category_name, d.name AS daira_name, co.name AS commune_name,
                   o.name AS org_name, u.first_name, u.last_name
            FROM reports r
            LEFT JOIN categories c ON r.category_id = c.id
            LEFT JOIN dairas d ON r.daira_id = d.id
            LEFT JOIN communes co ON r.commune_id = co.id
            LEFT JOIN organizations o ON r.organization_id = o.id
            LEFT JOIN users u ON r.citizen_id = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$this->reportId]);
        $report = $stmt->fetch();

        if (!$report) {
            throw new \RuntimeException("Report #{$this->reportId} not found");
        }

        $pdf = PdfHelper::generateReportPdf($report);

        $dir = ROOT_PATH . '/storage/pdfs';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $dir . "/report_{$report['tracking_code']}.pdf";
        file_put_contents($file, $pdf);

        // Store reference in DB
        $db->prepare("UPDATE reports SET pdf_path = ? WHERE id = ?")->execute([$file, $this->reportId]);
    }

    public function queue(): string {
        return 'default';
    }

    public function maxTries(): int {
        return 2;
    }
}
