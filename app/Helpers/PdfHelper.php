<?php
namespace App\Helpers;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfHelper {
    public static function generateReportPdf(array $report): string {
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        
        $dompdf = new Dompdf($options);
        
        $trackingUrl = "https://balagh-alger.dz/suivi/{$report['tracking_code']}";
        
        $html = self::buildReportHtml($report, $trackingUrl);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->output();
    }

    public static function generateMonthlyReport(
        array $orgStats,
        string $month,
        int $totalReports,
        int $totalResolved,
        int $totalPending,
        float $resolvedPct,
        float $avgDays,
        ?array $ratings
    ): string {
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);

        $monthDate = new \DateTime($month . '-01');
        $monthLabel = ucfirst($monthDate->format('F Y'));
        $footerDate = date('d/m/Y à H:i');
        $avgRating = $ratings && $ratings['avg_rating'] ? round((float)$ratings['avg_rating'], 1) : '—';
        $totalRatings = $ratings ? (int)$ratings['total_ratings'] : 0;
        $satisfactionDisplay = $totalRatings > 0 ? $avgRating . '/5 (' . $totalRatings . ' avis)' : '—';

        $orgRows = '';
        foreach ($orgStats as $s) {
            $orgAvgDays = $s['avg_resolution_days'] !== null ? round((float)$s['avg_resolution_days'], 1) . ' j' : '—';
            $orgRows .= <<<ROW
<tr>
    <td style="font-weight:600;">{$s['name']}</td>
    <td style="text-align:center;">{$s['total']}</td>
    <td style="text-align:center;color:#059669;">{$s['resolved']}</td>
    <td style="text-align:center;color:#d97706;">{$s['pending']}</td>
    <td style="text-align:center;color:#6366f1;">{$s['in_progress']}</td>
    <td style="text-align:center;color:#ef4444;">{$s['rejected']}</td>
    <td style="text-align:center;">{$orgAvgDays}</td>
</tr>
ROW;
        }

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; margin: 20px; }
        .header { text-align: center; border-bottom: 3px solid #6366f1; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #6366f1; margin: 0; font-size: 22px; }
        .header p { color: #666; margin: 5px 0 0; font-size: 12px; }
        .section-title { color: #6366f1; font-size: 14px; font-weight: bold; margin: 20px 0 10px; border-bottom: 2px solid #6366f1; padding-bottom: 5px; }
        .kpi-grid { display: flex; flex-wrap: wrap; gap: 10px; margin: 15px 0; }
        .kpi-box { flex: 1; min-width: 120px; background: #f5f5f5; border-radius: 8px; padding: 12px 15px; text-align: center; }
        .kpi-box .value { font-size: 20px; font-weight: 700; color: #6366f1; }
        .kpi-box .label { font-size: 10px; color: #666; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f5f5f5; font-weight: bold; color: #333; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 2px solid #eee; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>بلاغ الجزائر — Balagh Alger</h1>
        <p>Rapport Mensuel — {$monthLabel}</p>
    </div>

    <div class="section-title">Indicateurs Clés (KPI)</div>
    <div class="kpi-grid">
        <div class="kpi-box">
            <div class="value">{$totalReports}</div>
            <div class="label">Total Signalements</div>
        </div>
        <div class="kpi-box">
            <div class="value" style="color:#059669;">{$totalResolved}</div>
            <div class="label">Résolus</div>
        </div>
        <div class="kpi-box">
            <div class="value" style="color:#059669;">{$resolvedPct}%</div>
            <div class="label">Taux Résolution</div>
        </div>
        <div class="kpi-box">
            <div class="value" style="color:#d97706;">{$totalPending}</div>
            <div class="label">En attente</div>
        </div>
        <div class="kpi-box">
            <div class="value">{$avgDays} j</div>
            <div class="label">Temps Moyen Résolution</div>
        </div>
        <div class="kpi-box">
            <div class="value" style="color:#6366f1;">{$satisfactionDisplay}</div>
            <div class="label">Satisfaction</div>
        </div>
    </div>

    <div class="section-title">Détail par Organisme</div>
    <table>
        <thead>
            <tr>
                <th>Organisme</th>
                <th style="text-align:center;">Total</th>
                <th style="text-align:center;">Résolus</th>
                <th style="text-align:center;">En attente</th>
                <th style="text-align:center;">En cours</th>
                <th style="text-align:center;">Rejetés</th>
                <th style="text-align:center;">Temps Moyen</th>
            </tr>
        </thead>
        <tbody>
            {$orgRows}
        </tbody>
    </table>

    <div class="footer">
        Document généré le {$footerDate} — Balagh Alger v2.0 — Wilaya d'Alger<br>
        Ce document est généré automatiquement et ne constitue pas un acte officiel.
    </div>
</body>
</html>
HTML;

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
    
    private static function buildReportHtml(array $report, string $trackingUrl): string {
        $statusLabels = [
            'submitted' => 'Soumis', 'acknowledged' => 'Accusé de réception',
            'assigned' => 'Assigné', 'in_progress' => 'En cours',
            'pending_review' => 'À vérifier', 'pending_unite' => 'Validation unité',
            'validated' => 'Validé', 'resolved' => 'Résolu',
            'closed' => 'Clôturé', 'rejected' => 'Rejeté',
        ];
        $priorityLabels = ['low' => 'Faible', 'medium' => 'Moyen', 'high' => 'Élevé', 'urgent' => 'Urgent'];
        
        $status = $statusLabels[$report['status']] ?? $report['status'];
        $priority = $priorityLabels[$report['priority']] ?? $report['priority'];
        
        $geoSection = '';
        if (!empty($report['latitude'])) {
            $geoSection = '<div class="section-title">Géolocalisation</div><p>Lat: ' . htmlspecialchars($report['latitude']) . ' | Lng: ' . htmlspecialchars($report['longitude']) . '</p>';
        }
        
        $orgName = $report['org_name'] ?? 'Non assigné';
        $citizenName = $report['citizen_name'] ?: 'Anonyme';
        $footerDate = date('d/m/Y à H:i');
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; margin: 20px; }
        .header { text-align: center; border-bottom: 3px solid #6366f1; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #6366f1; margin: 0; font-size: 22px; }
        .header p { color: #666; margin: 5px 0 0; font-size: 12px; }
        .qr { float: right; margin: -10px 0 10px 15px; }
        .qr img { border: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f5f5f5; font-weight: bold; width: 140px; color: #333; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; color: white; }
        .bg-submitted { background: #0ea5e9; }
        .bg-acknowledged { background: #6366f1; }
        .bg-assigned { background: #f59e0b; color: #000; }
        .bg-in_progress { background: #8b5cf6; }
        .bg-resolved { background: #10b981; }
        .bg-closed { background: #374151; }
        .bg-rejected { background: #ef4444; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 2px solid #eee; color: #999; font-size: 10px; }
        .section-title { color: #6366f1; font-size: 14px; font-weight: bold; margin: 20px 0 10px; border-bottom: 2px solid #6366f1; padding-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>بلاغ الجزائر — Balagh Alger</h1>
        <p>Fiche de Signalement Officiel — Wilaya d'Alger</p>
    </div>
    
    <div class="qr">
        <div style="border:1px solid #ddd;padding:15px;text-align:center;margin-bottom:5px;">
            <div style="font-size:10px;color:#666;">Suivez votre signalement</div>
            <div style="font-size:11px;font-weight:bold;color:#6366f1;margin-top:5px;">{$trackingUrl}</div>
        </div>
    </div>
    
    <h2 style="color:#6366f1;margin:0 0 15px;">{$report['tracking_code']}</h2>
    
    <table>
        <tr><th>Titre</th><td>{$report['title']}</td></tr>
        <tr><th>Catégorie</th><td>{$report['category_name']}</td></tr>
        <tr><th>Priorité</th><td>{$priority}</td></tr>
        <tr><th>Statut</th><td><span class="badge bg-{$report['status']}">{$status}</span></td></tr>
        <tr><th>Daïra</th><td>{$report['daira_name']}</td></tr>
        <tr><th>Commune</th><td>{$report['commune_name']}</td></tr>
        <tr><th>Adresse</th><td>{$report['address']}</td></tr>
        <tr><th>Organisme</th><td>{$orgName}</td></tr>
        <tr><th>Date de création</th><td>{$report['created_at']}</td></tr>
        <tr><th>Signaleur</th><td>{$citizenName}</td></tr>
    </table>
    
    <div class="section-title">Description</div>
    <p>{$report['description']}</p>
    
    {$geoSection}
    
    <div class="footer">
        Document généré le {$footerDate} — Balagh Alger v2.0 — Wilaya d'Alger<br>
        Ce document est généré automatiquement et ne constitue pas un acte officiel.
    </div>
</body>
</html>
HTML;
    }
}
