<?php $pageTitle = 'Daïras'; ?>
<h4 class="mb-4"><i class="fas fa-map-marked-alt me-2 text-primary"></i> Daïras - Wilaya d'Alger</h4>
<div class="row">
<?php foreach ($dairas as $d): ?>
<div class="col-xl-4 col-md-6 mb-4">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
            <h5 class="card-title mb-1"><?= $d['name'] ?></h5>
            <p class="text-muted" dir="rtl"><?= $d['name_ar'] ?></p>
            <span class="badge bg-info"><?= $d['communes_count'] ?> communes</span>
        </div>
        <div class="card-footer bg-transparent"><a href="/dairas/<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i> Voir les communes</a></div>
    </div>
</div>
<?php endforeach; ?>
</div>
