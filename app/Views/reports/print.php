<!DOCTYPE html>
<html lang="<?= (Session::get('lang') ?? 'fr') === 'ar' ? 'ar' : 'fr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= __('report_print.title') ?> - <?= $report['tracking_code'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display: none !important; } body { font-size: 12pt; } }
        .report-header { border-bottom: 3px solid #3a7bd5; padding-bottom: 15px; margin-bottom: 20px; }
    </style>
</head>
<body class="p-4">
    <div class="no-print mb-3"><button onclick="window.print()" class="btn btn-primary btn-sm"><i class="fas fa-print me-1"></i> <?= __('report_print.print_btn') ?></button> <button onclick="window.close()" class="btn btn-secondary btn-sm"><?= __('report_print.close_btn') ?></button></div>
    <div class="report-header d-flex justify-content-between">
        <div><h4><?= __('report_print.header') ?></h4><p class="text-muted"><?= __('app.wilaya') ?></p></div>
        <div class="text-end"><h3 style="color:#3a7bd5;"><?= $report['tracking_code'] ?></h3><p><?= __('report_print.date_label') ?> <?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></p></div>
    </div>
    <table class="table table-bordered mb-4">
        <tr><th width="25%"><?= __('report_print.title_label') ?></th><td><?= htmlspecialchars($report['title']) ?></td></tr>
        <tr><th><?= __('report_print.category_label') ?></th><td><?= htmlspecialchars($report['category_name']) ?></td></tr>
        <tr><th><?= __('report_print.priority_label') ?></th><td><?= __('priorities.' . $report['priority']) ?></td></tr>
        <tr><th><?= __('report_print.status_label') ?></th><td><?= __('statuses.' . $report['status']) ?></td></tr>
        <tr><th><?= __('report_print.daira_commune') ?></th><td><?= htmlspecialchars($report['daira_name']) ?> / <?= htmlspecialchars($report['commune_name']) ?></td></tr>
        <tr><th><?= __('report_print.address_label') ?></th><td><?= htmlspecialchars($report['address']) ?></td></tr>
        <tr><th><?= __('report_print.org_label') ?></th><td><?= htmlspecialchars($report['org_name'] ?? __('reports.not_assigned')) ?></td></tr>
        <tr><th><?= __('report_print.description_label') ?></th><td><?= nl2br(htmlspecialchars($report['description'])) ?></td></tr>
        <tr><th><?= __('report_print.reporter') ?></th><td><?= htmlspecialchars($report['citizen_name'] ?? __('reports.anonymous')) ?></td></tr>
    </table>
    <div class="text-muted text-center mt-4"><small><?= __('report_print.generated') ?> <?= date('d/m/Y à H:i') ?> - Balagh Alger <?= __('app.version') ?></small></div>
</body>
</html>
