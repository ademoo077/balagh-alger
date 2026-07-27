<?php $pageTitle = $org['name']; ?>
<div class="d-flex justify-content-between mb-4">
    <h4><i class="fas fa-building me-2 text-primary"></i> <?= $org['name'] ?></h4>
    <div class="d-flex gap-2">
        <a href="/organizations/<?= $org['id'] ?>/edit" class="btn btn-sm btn-warning"><i class="fas fa-edit me-1"></i> Modifier</a>
        <a href="/organizations" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4"><div class="card-body">
            <h6>Informations</h6>
            <p><strong>Code:</strong> <?= $org['code'] ?></p>
            <p><strong>Email:</strong> <?= $org['email'] ?? '-' ?></p>
            <p><strong>Téléphone:</strong> <?= $org['phone'] ?? '-' ?></p>
            <p><strong>Adresse:</strong> <?= $org['address'] ?? '-' ?></p>
            <p><strong>Description:</strong> <?= $org['description'] ?? '-' ?></p>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent"><h6 class="mb-0">Utilisateurs (<?= count($users) ?>)</h6></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0"><thead class="table-dark"><tr><th>Nom</th><th>Email</th><th>Rôle</th></tr></thead>
                <tbody><?php foreach ($users as $u): ?><tr><td><?= $u['first_name'] ?> <?= $u['last_name'] ?></td><td><?= $u['email'] ?></td><td><span class="badge bg-secondary"><?= $u['role_labels'] ?></span></td></tr><?php endforeach; ?></tbody></table>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Signalements récents</h6></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0"><thead class="table-dark"><tr><th>Code</th><th>Titre</th><th>Statut</th><th>Date</th></tr></thead>
                <tbody><?php foreach ($reports as $r): ?><tr><td><a href="/reports/<?= $r['id'] ?>"><?= $r['tracking_code'] ?></a></td><td><?= mb_strimwidth($r['title'],0,30,'...') ?></td><td><?= \App\Helpers\Helper::getStatusBadge($r['status']) ?></td><td><?= \App\Helpers\Helper::timeAgo($r['created_at']) ?></td></tr><?php endforeach; ?></tbody></table>
            </div>
        </div>
    </div>
</div>
