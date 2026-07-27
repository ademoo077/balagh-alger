<?php $pageTitle = $daira['name']; ?>
<div class="d-flex justify-content-between mb-4">
    <h4><i class="fas fa-map-marked-alt me-2 text-primary"></i> <?= $daira['name'] ?> <small class="text-muted" dir="rtl"><?= $daira['name_ar'] ?></small></h4>
    <a href="/dairas" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
</div>
<div class="row">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent"><h6 class="mb-0">Communes (<?= count($communes) ?>)</h6></div>
            <div class="list-group list-group-flush">
                <?php foreach ($communes as $c): ?>
                <div class="list-group-item bg-transparent border-0">
                    <strong><?= $c['name'] ?></strong>
                    <span class="text-muted ms-2" dir="rtl"><?= $c['name_ar'] ?></span>
                    <span class="badge bg-secondary float-end"><?= $c['postal_code'] ?? '' ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent"><h6 class="mb-0">Signalements récents</h6></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0"><thead class="table-dark"><tr><th>Code</th><th>Titre</th><th>Catégorie</th><th>Statut</th></tr></thead>
                <tbody><?php foreach ($reports as $r): ?><tr><td><a href="/reports/<?= $r['id'] ?>"><?= $r['tracking_code'] ?></a></td><td><?= mb_strimwidth($r['title'],0,35,'...') ?></td><td><?= $r['category_name'] ?></td><td><?= \App\Helpers\Helper::getStatusBadge($r['status']) ?></td></tr><?php endforeach; ?></tbody></table>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Carte</h6></div>
            <div class="card-body p-0"><div id="dairaMap" style="height:300px;"></div></div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($daira['latitude'] && $daira['longitude']): ?>
    var map = L.map('dairaMap').setView([<?= $daira['latitude'] ?>, <?= $daira['longitude'] ?>], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'&copy; OSM'}).addTo(map);
    L.marker([<?= $daira['latitude'] ?>, <?= $daira['longitude'] ?>]).addTo(map).bindPopup('<?= $daira['name'] ?>');
    <?php else: ?>
    var map = L.map('dairaMap').setView([36.7538, 3.0588], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'&copy; OSM'}).addTo(map);
    <?php endif; ?>
});
</script>
