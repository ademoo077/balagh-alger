<?php $pageTitle = __('report_edit.title'); ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0"><i class="fas fa-edit me-2 text-warning"></i> <?= __('report_edit.title') ?> <code><?= htmlspecialchars($report['tracking_code']) ?></code></h5>
            </div>
            <div class="card-body">
                <?php if ($errors = \App\Helpers\Session::getFlash('errors')): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <form method="POST" action="/reports/<?= $report['id'] ?>/update" id="reportForm">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold"><?= __('report_create.title_label') ?> *</label>
                        <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($report['title']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold"><?= __('common.description') ?> *</label>
                        <textarea class="form-control" name="description" rows="4" required><?= htmlspecialchars($report['description']) ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?= __('common.category') ?> *</label>
                            <select class="form-select" name="category_id" id="categorySelect" required>
                                <option value=""><?= __('report_create.select') ?></option>
                                <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $report['category_id'] == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?= __('common.priority') ?> *</label>
                            <select class="form-select" name="priority" required>
                                <option value="low" <?= $report['priority'] === 'low' ? 'selected' : '' ?>><?= __('priorities.low') ?></option>
                                <option value="medium" <?= $report['priority'] === 'medium' ? 'selected' : '' ?>><?= __('priorities.medium') ?></option>
                                <option value="high" <?= $report['priority'] === 'high' ? 'selected' : '' ?>><?= __('priorities.high') ?></option>
                                <option value="urgent" <?= $report['priority'] === 'urgent' ? 'selected' : '' ?>><?= __('priorities.urgent') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?= __('common.daira') ?> *</label>
                            <select class="form-select" name="daira_id" id="dairaSelect" required>
                                <option value=""><?= __('report_create.select') ?></option>
                                <?php foreach ($dairas as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= $report['daira_id'] == $d['id'] ? 'selected' : '' ?>><?= $d['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?= __('common.commune') ?> *</label>
                            <select class="form-select" name="commune_id" id="communeSelect" required>
                                <option value=""><?= __('report_create.select') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold"><?= __('common.address') ?> *</label>
                        <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($report['address']) ?>" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?= __('report_create.latitude') ?></label>
                            <input type="number" step="any" class="form-control" name="latitude" id="latitude" value="<?= $report['latitude'] ?? '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><?= __('report_create.longitude') ?></label>
                            <input type="number" step="any" class="form-control" name="longitude" id="longitude" value="<?= $report['longitude'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold"><?= __('report_edit.coord_label') ?></label>
                        <div id="reportMap" style="height: 250px; border-radius: 10px;"></div>
                        <small class="text-muted"><?= __('report_edit.coord_help') ?></small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i> <?= __('report_edit.save') ?></button>
                        <a href="/reports/<?= $report['id'] ?>" class="btn btn-outline-secondary"><?= __('report_edit.cancel') ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6><i class="fas fa-info-circle me-2 text-info"></i> <?= __('report_edit.info_card') ?></h6>
                <p class="text-muted mb-1"><strong><?= __('report_edit.code_label') ?></strong> <?= htmlspecialchars($report['tracking_code']) ?></p>
                <p class="text-muted mb-1"><strong><?= __('report_edit.status_label') ?></strong> <?= htmlspecialchars($report['status']) ?></p>
                <p class="text-muted mb-0"><strong><?= __('report_edit.created_label') ?></strong> <?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = <?= $report['latitude'] ?: '36.7538' ?>;
    const lng = <?= $report['longitude'] ?: '3.0588' ?>;
    const map = L.map('reportMap').setView([lat, lng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OSM' }).addTo(map);
    let marker = L.marker([lat, lng]).addTo(map);

    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
        document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
    });

    document.getElementById('dairaSelect').addEventListener('change', function() {
        const dairaId = this.value;
        const communeSelect = document.getElementById('communeSelect');
        communeSelect.innerHTML = '<option value=""><?= addslashes(__('common.loading')) ?></option>';

        if (dairaId) {
            fetch('/api/communes/' + dairaId)
                .then(r => r.json())
                .then(data => {
                    communeSelect.innerHTML = '<option value=""><?= addslashes(__('report_create.select')) ?></option>';
                    data.forEach(c => {
                        const sel = c.id == <?= $report['commune_id'] ?> ? 'selected' : '';
                        communeSelect.innerHTML += '<option value="' + c.id + '" ' + sel + '>' + c.name + '</option>';
                    });
                });
        } else {
            communeSelect.innerHTML = '<option value=""><?= addslashes(__('report_create.choose_daira_first')) ?></option>';
        }
    });

    document.getElementById('dairaSelect').dispatchEvent(new Event('change'));
});
</script>
