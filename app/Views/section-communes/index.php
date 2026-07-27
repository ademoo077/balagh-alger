<?php $pageTitle = 'Communes à Suivre'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-map-marked-alt me-2 text-primary"></i> Communes à Suivre</h4>
    <small class="text-muted">Gérez l'affectation des communes aux chefs de section</small>
</div>

<div class="row g-4">
    <!-- Assignment Form -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Assigner une commune</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/section-communes/assign">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chef de Section</label>
                        <select name="user_id" id="csSelect" class="form-select" required>
                            <option value="">— Sélectionner —</option>
                            <?php foreach ($chefSections as $cs): ?>
                                <option value="<?= $cs['id'] ?>"
                                    data-daira="<?= $cs['daira_id'] ?>"
                                    data-org="<?= $cs['organization_id'] ?>">
                                    <?= htmlspecialchars($cs['first_name'] . ' ' . $cs['last_name']) ?>
                                    <small class="text-muted">(<?= $cs['org_name'] ?? '' ?> — <?= $cs['daira_name'] ?? '' ?>)</small>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Commune</label>
                        <select name="commune_id" id="communeSelect" class="form-select" required>
                            <option value="">— Sélectionner d'abord un chef de section —</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-link me-1"></i> Assigner la commune
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Current Assignments -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-list me-2"></i> Affectations actuelles</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($assignments)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                        <p>Aucune commune assignée pour le moment.</p>
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Chef de Section</th>
                                <th>Commune</th>
                                <th>Daïra</th>
                                <th>Assigné par</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($assignments as $a): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($a['chef_first'] . ' ' . $a['chef_last']) ?></strong>
                                </td>
                                <td><span class="badge bg-info"><?= htmlspecialchars($a['commune_name']) ?></span></td>
                                <td><?= htmlspecialchars($a['daira_name']) ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($a['assigned_by_first'] . ' ' . $a['assigned_by_last']) ?></td>
                                <td>
                                    <form method="POST" action="/section-communes/remove" class="d-inline">
                                        <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="user_id" value="<?= $a['user_id'] ?>">
                                        <input type="hidden" name="commune_id" value="<?= $a['commune_id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" title="Retirer" data-confirm="<?= __('ui.confirm_remove') ?>">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Summary by Chef de Section -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-users me-2"></i> Vue par chef de section</h6>
            </div>
            <div class="card-body">
                <?php
                $byCs = [];
                foreach ($assignments as $a) {
                    $key = $a['user_id'];
                    if (!isset($byCs[$key])) {
                        $byCs[$key] = ['name' => $a['chef_first'] . ' ' . $a['chef_last'], 'communes' => []];
                    }
                    $byCs[$key]['communes'][] = $a['commune_name'] . ' (' . $a['daira_name'] . ')';
                }
                foreach ($chefSections as $cs):
                    $csId = $cs['id'];
                    $assigned = $byCs[$csId]['communes'] ?? [];
                ?>
                <div class="mb-3 p-3 rounded" style="background: var(--input-bg); border: 1px solid var(--card-border);">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong><?= htmlspecialchars($cs['first_name'] . ' ' . $cs['last_name']) ?></strong>
                        <span class="badge bg-<?= count($assigned) > 0 ? 'success' : 'warning' ?>">
                            <?= count($assigned) ?> commune<?= count($assigned) !== 1 ? 's' : '' ?>
                        </span>
                    </div>
                    <small class="text-muted"><?= htmlspecialchars($cs['org_name'] ?? '') ?> — <?= htmlspecialchars($cs['daira_name'] ?? '') ?></small>
                    <?php if (empty($assigned)): ?>
                        <div class="mt-2 text-warning small"><i class="fas fa-exclamation-triangle me-1"></i> Aucune commune assignée</div>
                    <?php else: ?>
                        <div class="mt-2">
                            <?php foreach ($assigned as $c): ?>
                                <span class="badge bg-info-subtle text-info me-1 mb-1"><?= htmlspecialchars($c) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csSelect = document.getElementById('csSelect');
    const communeSelect = document.getElementById('communeSelect');

    const allCommunes = <?= json_encode($allCommunes) ?>;

    const userAssigned = {};
    <?php foreach ($assignments as $a): ?>
    if (!userAssigned[<?= $a['user_id'] ?>]) userAssigned[<?= $a['user_id'] ?>] = [];
    userAssigned[<?= $a['user_id'] ?>].push(<?= $a['commune_id'] ?>);
    <?php endforeach; ?>

    csSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        communeSelect.innerHTML = '<option value="">— Sélectionner une commune —</option>';

        if (!selected.value) return;

        const dairaId = selected.dataset.daira;
        const csId = parseInt(selected.value);
        const csAssigned = userAssigned[csId] || [];

        allCommunes.forEach(function(c) {
            if (c.daira_id == dairaId) {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;
                if (csAssigned.includes(parseInt(c.id))) {
                    opt.textContent += ' (déjà assignée)';
                    opt.disabled = true;
                }
                communeSelect.appendChild(opt);
            }
        });
    });
});
</script>
