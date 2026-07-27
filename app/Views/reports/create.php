<?php $pageTitle = __('report_create.title'); ?>

<div class="progress-thin mb-3 animate-fade-in">
    <div class="progress-bar" id="wizardProgressBar" style="width:0%"></div>
</div>

<div class="page-header animate-fade-in-up">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4><i class="fas fa-plus-circle me-2 text-accent"></i> <?= __('report_create.title') ?></h4>
            <small class="text-secondary"><?= __('report_create.subtitle') ?></small>
        </div>
        <a href="/reports" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> <?= __('common.back') ?></a>
    </div>
</div>

<?php if ($errors = \App\Helpers\Session::getFlash('errors')): ?>
<div class="alert alert-danger animate-fade-in"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<div class="card mb-4 animate-fade-in-up">
    <div class="card-body py-2">
        <div class="wizard-steps" id="wizardSteps">
            <div class="wizard-step active" data-step="0"><div class="step-number"><i class="fas fa-camera"></i></div><div class="step-label"><?= __('report_create.step_photos') ?></div></div>
            <div class="wizard-connector"></div>
            <div class="wizard-step" data-step="1"><div class="step-number"><i class="fas fa-tag"></i></div><div class="step-label"><?= __('report_create.step_category') ?></div></div>
            <div class="wizard-connector"></div>
            <div class="wizard-step" data-step="2"><div class="step-number"><i class="fas fa-list"></i></div><div class="step-label"><?= __('report_create.step_subcat') ?></div></div>
            <div class="wizard-connector"></div>
            <div class="wizard-step" data-step="3"><div class="step-number"><i class="fas fa-map"></i></div><div class="step-label"><?= __('report_create.step_daira') ?></div></div>
            <div class="wizard-connector"></div>
            <div class="wizard-step" data-step="4"><div class="step-number"><i class="fas fa-city"></i></div><div class="step-label"><?= __('report_create.step_commune') ?></div></div>
            <div class="wizard-connector"></div>
            <div class="wizard-step" data-step="5"><div class="step-number"><i class="fas fa-location-dot"></i></div><div class="step-label"><?= __('report_create.step_gps') ?></div></div>
            <div class="wizard-connector"></div>
            <div class="wizard-step" data-step="6"><div class="step-number"><i class="fas fa-pen"></i></div><div class="step-label"><?= __('report_create.step_description') ?></div></div>
            <div class="wizard-connector"></div>
            <div class="wizard-step" data-step="7"><div class="step-number"><i class="fas fa-check"></i></div><div class="step-label"><?= __('report_create.step_confirm') ?></div></div>
        </div>
    </div>
</div>

<form method="POST" action="/reports/store" id="reportForm" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="<?= $csrfToken ?>">

    <!-- STEP 0: Photos -->
    <div class="wizard-content active" data-step="0">
        <div class="card animate-fade-in">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-camera me-2 text-cyan"></i><?= __('report_create.photos_title') ?></h6></div>
            <div class="card-body">
                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:16px;"><?= __('report_create.photos_help') ?></p>

                <!-- Drop zone (clickable) -->
                <div id="photoDropZone" style="border:2px dashed var(--border);border-radius:12px;padding:32px;text-align:center;cursor:pointer;transition:all 0.2s;position:relative;">
                    <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:var(--text-muted);margin-bottom:8px;display:block;"></i>
                    <span style="font-size:0.85rem;color:var(--text-secondary);"><?= __('report_create.photos_drop') ?></span><br>
                    <small style="color:var(--text-muted);"><?= __('report_create.photos_formats') ?></small>

                    <!-- File input placed INSIDE the drop zone, overlaid on top -->
                    <input type="file" id="photos" name="photos[]" accept="image/*,video/*" multiple
                        style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;z-index:2;">
                </div>

                <!-- Mobile camera button -->
                <div class="mt-2 d-md-none">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnCamera">
                        <i class="fas fa-camera me-1"></i> <?= __('report_create.take_photo') ?>
                    </button>
                    <input type="file" id="photoCamera" name="photos[]" accept="image/*" capture="environment" style="display:none;">
                </div>

                <div id="photoPreview" class="row g-2 mt-2"></div>
                <div class="mt-2"><small class="text-muted"><?= __('report_create.photos_optional') ?></small></div>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-primary btn-next"><?= __('report_create.next') ?> <i class="fas fa-arrow-right ms-1"></i></button>
        </div>
    </div>

    <!-- STEP 1: Category -->
    <div class="wizard-content" data-step="1">
        <div class="card animate-fade-in">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-tag me-2 text-accent"></i><?= __('report_create.category_title') ?> *</h6></div>
            <div class="card-body">
                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:16px;"><?= __('report_create.category_help') ?></p>
                <select class="form-select" name="category_id" id="categorySelect" required>
                    <option value=""><?= __('report_create.select_category') ?></option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($_POST['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="row g-2 mt-3">
                    <?php foreach ($categories as $c): ?>
                    <div class="col-sm-6 col-md-4">
                        <div class="kpi-mini cat-card" style="cursor:pointer;" data-cat-id="<?= $c['id'] ?>">
                            <div class="kpi-icon" style="background:<?= $c['color'] ?? 'var(--accent-surface)' ?>18;color:<?= $c['color'] ?? 'var(--accent)' ?>;">
                                <i class="<?= $c['icon'] ?? 'fas fa-flag' ?>"></i>
                            </div>
                            <div><div style="font-size:0.8rem;font-weight:600;"><?= $c['name'] ?></div></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-1"></i> <?= __('report_create.previous') ?></button>
            <button type="button" class="btn btn-primary btn-next"><?= __('report_create.next') ?> <i class="fas fa-arrow-right ms-1"></i></button>
        </div>
    </div>

    <!-- STEP 2: Subcategory + Priority -->
    <div class="wizard-content" data-step="2">
        <div class="card animate-fade-in">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-list me-2 text-accent"></i><?= __('report_create.subcategory_title') ?> *</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label"><?= __('report_create.subcategory_label') ?> *</label>
                        <select class="form-select" name="subcategory_id" id="subcategorySelect" required>
                            <option value=""><?= __('report_create.select_subcat_first') ?></option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><?= __('report_create.priority_label') ?> *</label>
                        <select class="form-select" name="priority" required>
                            <option value="low"><?= __('priorities.low') ?></option>
                            <option value="medium" selected><?= __('priorities.medium') ?></option>
                            <option value="high"><?= __('priorities.high') ?></option>
                            <option value="urgent"><?= __('priorities.urgent') ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-1"></i> <?= __('report_create.previous') ?></button>
            <button type="button" class="btn btn-primary btn-next"><?= __('report_create.next') ?> <i class="fas fa-arrow-right ms-1"></i></button>
        </div>
    </div>

    <!-- STEP 3: Daira -->
    <div class="wizard-content" data-step="3">
        <div class="card animate-fade-in">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-map me-2 text-cyan"></i><?= __('report_create.daira_title') ?> *</h6></div>
            <div class="card-body">
                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:16px;"><?= __('report_create.daira_help') ?></p>
                <select class="form-select" name="daira_id" id="dairaSelect" required style="max-width:400px;">
                    <option value=""><?= __('report_create.select_daira') ?></option>
                    <?php foreach ($dairas as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($_POST['daira_id'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= $d['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-1"></i> <?= __('report_create.previous') ?></button>
            <button type="button" class="btn btn-primary btn-next"><?= __('report_create.next') ?> <i class="fas fa-arrow-right ms-1"></i></button>
        </div>
    </div>

    <!-- STEP 4: Commune -->
    <div class="wizard-content" data-step="4">
        <div class="card animate-fade-in">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-city me-2 text-cyan"></i><?= __('report_create.commune_title') ?> *</h6></div>
            <div class="card-body">
                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:16px;"><?= __('report_create.commune_help') ?></p>
                <select class="form-select" name="commune_id" id="communeSelect" required style="max-width:400px;">
                    <option value=""><?= __('report_create.choose_daira_first') ?></option>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-1"></i> <?= __('report_create.previous') ?></button>
            <button type="button" class="btn btn-primary btn-next"><?= __('report_create.next') ?> <i class="fas fa-arrow-right ms-1"></i></button>
        </div>
    </div>

    <!-- STEP 5: GPS Map -->
    <div class="wizard-content" data-step="5">
        <div class="card animate-fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-location-dot me-2 text-red"></i><?= __('report_create.gps_title') ?> *</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnGetGps"><i class="fas fa-crosshairs me-1"></i> <?= __('report_create.my_position') ?></button>
            </div>
            <div class="card-body">
                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:12px;"><?= __('report_create.gps_help') ?></p>
                <div class="gps-map-container" id="gpsMapContainer">
                    <div id="reportMap" style="height:320px;"></div>
                </div>
                <div class="row g-2 mt-3">
                    <div class="col-md-4">
                        <label class="form-label"><?= __('report_create.latitude') ?> *</label>
                        <input type="number" step="any" class="form-control" name="latitude" id="latitude" required placeholder="36.7538">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><?= __('report_create.longitude') ?> *</label>
                        <input type="number" step="any" class="form-control" name="longitude" id="longitude" required placeholder="3.0588">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><?= __('report_create.address') ?> *</label>
                        <input type="text" class="form-control" name="address" id="address" required placeholder="<?= __('report_create.address_placeholder') ?>">
                    </div>
                </div>
                <div id="gpsStatus" class="mt-2" style="font-size:0.78rem;color:var(--text-muted);">
                    <i class="fas fa-info-circle me-1"></i> <?= __('report_create.gps_status') ?>
                </div>
                <div id="similarReports" class="mt-3" style="display:none;"></div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-1"></i> <?= __('report_create.previous') ?></button>
            <button type="button" class="btn btn-primary btn-next"><?= __('report_create.next') ?> <i class="fas fa-arrow-right ms-1"></i></button>
        </div>
    </div>

    <!-- STEP 6: Description -->
    <div class="wizard-content" data-step="6">
        <div class="card animate-fade-in">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-pen me-2 text-accent"></i><?= __('report_create.description_title') ?> *</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label"><?= __('report_create.title_label') ?> *</label>
                    <input type="text" class="form-control" name="title" id="inputTitle" value="<?= htmlspecialchars(($_POST['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required placeholder="<?= __('report_create.title_placeholder') ?>" style="max-width:500px;">
                </div>
                <div class="mb-3">
                    <label class="form-label"><?= __('report_create.desc_label') ?> *</label>
                    <textarea class="form-control" name="description" id="inputDesc" rows="5" required placeholder="<?= __('report_create.desc_placeholder') ?>"><?= htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <hr>
                <h6 style="font-size:0.82rem;font-weight:600;margin-bottom:10px;color:var(--text-secondary);"><?= __('report_create.contact_info') ?></h6>
                <div class="row g-2">
                    <div class="col-md-4"><input type="text" class="form-control" name="citizen_name" placeholder="<?= __('report_create.contact_name') ?>" value="<?= htmlspecialchars(($_POST['citizen_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-md-4"><input type="tel" class="form-control" name="citizen_phone" placeholder="<?= __('report_create.contact_phone') ?>" value="<?= htmlspecialchars(($_POST['citizen_phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-md-4"><input type="email" class="form-control" name="citizen_email" placeholder="<?= __('users.email') ?>" value="<?= htmlspecialchars(($_POST['citizen_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-1"></i> <?= __('report_create.previous') ?></button>
            <button type="button" class="btn btn-primary btn-next"><?= __('report_create.next') ?> <i class="fas fa-arrow-right ms-1"></i></button>
        </div>
    </div>

    <!-- STEP 7: Confirmation -->
    <div class="wizard-content" data-step="7">
        <div class="card animate-fade-in">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-check-circle me-2 text-green"></i><?= __('report_create.confirm_submit') ?></h6></div>
            <div class="card-body">
                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:16px;"><?= __('report_create.confirm_help') ?></p>
                <div class="row g-3">
                    <div class="col-sm-6 col-md-3"><div style="font-size:0.7rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;"><?= __('common.title') ?></div><div style="font-size:0.85rem;font-weight:600;" id="sumTitle">—</div></div>
                    <div class="col-sm-6 col-md-3"><div style="font-size:0.7rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;"><?= __('common.category') ?></div><div style="font-size:0.85rem;font-weight:600;" id="sumCategory">—</div></div>
                    <div class="col-sm-6 col-md-3"><div style="font-size:0.7rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;"><?= __('common.daira') ?></div><div style="font-size:0.85rem;font-weight:600;" id="sumDaira">—</div></div>
                    <div class="col-sm-6 col-md-3"><div style="font-size:0.7rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;"><?= __('common.commune') ?></div><div style="font-size:0.85rem;font-weight:600;" id="sumCommune">—</div></div>
                    <div class="col-sm-6 col-md-3"><div style="font-size:0.7rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;"><?= __('common.priority') ?></div><div id="sumPriority">—</div></div>
                    <div class="col-sm-6 col-md-3"><div style="font-size:0.7rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;"><?= __('common.address') ?></div><div style="font-size:0.85rem;" id="sumAddress">—</div></div>
                    <div class="col-sm-6 col-md-3"><div style="font-size:0.7rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;">GPS</div><div style="font-size:0.82rem;font-family:monospace;" id="sumGps">—</div></div>
                    <div class="col-sm-6 col-md-3"><div style="font-size:0.7rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;"><?= __('common.description') ?></div><div style="font-size:0.85rem;" id="sumDesc">—</div></div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary btn-prev"><i class="fas fa-arrow-left me-1"></i> <?= __('report_create.previous') ?></button>
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-paper-plane me-2"></i> <?= __('report_create.submit') ?></button>
        </div>
    </div>
</form>

<script>
(function() {
    var currentStep = 0;
    var totalSteps = 8;
    var contents = document.querySelectorAll('.wizard-content');
    var steps = document.querySelectorAll('.wizard-step');
    var connectors = document.querySelectorAll('.wizard-connector');
    var progressBar = document.getElementById('wizardProgressBar');

    function showStep(n) {
        currentStep = n;
        contents.forEach(function(el, i) {
            if (i === n) { el.classList.add('active'); el.style.display = 'block'; }
            else { el.classList.remove('active'); el.style.display = 'none'; }
        });
        steps.forEach(function(el, i) {
            el.classList.toggle('active', i === n);
            el.classList.toggle('completed', i < n);
        });
        connectors.forEach(function(el, i) {
            el.classList.toggle('completed', i < n);
        });
        progressBar.style.width = (totalSteps > 1 ? (n / (totalSteps - 1)) * 100 : 0) + '%';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateStep(n) {
        var content = contents[n];
        if (!content) return true;
        var required = content.querySelectorAll('[required]');
        var valid = true;
        required.forEach(function(el) {
            if (!el.value || !el.value.trim()) {
                el.style.borderColor = 'var(--red)';
                valid = false;
                el.addEventListener('input', function handler() {
                    el.style.borderColor = '';
                    el.removeEventListener('input', handler);
                });
            }
        });
        if (!valid) {
            try {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'warning',
                    title: <?= json_encode(__('report_create.fill_required')) ?>,
                    showConfirmButton: false, timer: 2000,
                    background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#1a2235' : '#fff',
                    color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f1f5f9' : '#111827'
                });
            } catch(e) {}
        }
        return valid;
    }

    function updateSummary() {
        var t = document.getElementById('inputTitle');
        var c = document.getElementById('categorySelect');
        var d = document.getElementById('dairaSelect');
        var cm = document.getElementById('communeSelect');
        var p = document.querySelector('[name="priority"]');
        var a = document.getElementById('address');
        var la = document.getElementById('latitude');
        var lo = document.getElementById('longitude');
        var desc = document.getElementById('inputDesc');
        document.getElementById('sumTitle').textContent = (t && t.value) || '—';
        document.getElementById('sumCategory').textContent = (c && c.selectedIndex > 0) ? c.options[c.selectedIndex].text : '—';
        document.getElementById('sumDaira').textContent = (d && d.selectedIndex > 0) ? d.options[d.selectedIndex].text : '—';
        document.getElementById('sumCommune').textContent = (cm && cm.selectedIndex > 0) ? cm.options[cm.selectedIndex].text : '—';
        document.getElementById('sumAddress').textContent = (a && a.value) || '—';
        var pl = {low:<?= json_encode(__('priorities.low')) ?>,medium:<?= json_encode(__('priorities.medium')) ?>,high:<?= json_encode(__('priorities.high')) ?>,urgent:<?= json_encode(__('priorities.urgent')) ?>};
        document.getElementById('sumPriority').innerHTML = pl[p.value] || '—';
        if (la && la.value && lo && lo.value) {
            document.getElementById('sumGps').innerHTML = '<i class="fas fa-check-circle" style="color:var(--green);"></i> ' + la.value + ', ' + lo.value;
        } else {
            document.getElementById('sumGps').innerHTML = '<span style="color:var(--red);"><?= addslashes(__('common.undefined')) ?></span>';
        }
        document.getElementById('sumDesc').textContent = (desc && desc.value) ? desc.value.substring(0, 80) + (desc.value.length > 80 ? '...' : '') : '—';
    }

    function advanceStep() {
        if (currentStep === 6) updateSummary();
        if (currentStep < totalSteps - 1) showStep(currentStep + 1);
    }

    document.querySelectorAll('.btn-next').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!validateStep(currentStep)) return;

            if (currentStep === 2) {
                var catId = document.getElementById('categorySelect').value;
                var subId = document.getElementById('subcategorySelect').value;
                var daiId = document.getElementById('dairaSelect').value;
                if (!catId || !subId) { advanceStep(); return; }

                var params = 'category_id=' + encodeURIComponent(catId) + '&subcategory_id=' + encodeURIComponent(subId);
                if (daiId) params += '&daira_id=' + encodeURIComponent(daiId);

                var isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                fetch('/api/reports/check-duplicate?' + params)
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.count > 0) {
                            var rows = '';
                            data.duplicates.forEach(function(d) {
                                var statusColors = {pending:'var(--amber)',in_progress:'var(--cyan)',resolved:'var(--green)',closed:'var(--text-muted)',rejected:'var(--red)'};
                                var statusLabels = <?= json_encode(__('statuses')) ?>;
                                var sc = statusColors[d.status] || 'var(--text-muted)';
                                var sl = (typeof statusLabels === 'object' && statusLabels[d.status]) ? statusLabels[d.status] : d.status;
                                rows += '<div style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;padding:10px 14px;margin-bottom:8px;text-align:left;">' +
                                    '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">' +
                                    '<span style="font-weight:600;font-size:0.85rem;">' + (d.title || '—') + '</span>' +
                                    '<span style="font-size:0.7rem;padding:2px 8px;border-radius:10px;background:' + sc + '22;color:' + sc + ';font-weight:600;">' + sl + '</span>' +
                                    '</div>' +
                                    '<div style="font-size:0.75rem;color:var(--text-muted);">' +
                                    '<i class="fas fa-hashtag me-1"></i>' + (d.tracking_code || '') +
                                    ' &middot; <i class="fas fa-tag me-1"></i>' + (d.category_name || '') +
                                    ' &middot; <i class="fas fa-map-marker-alt me-1"></i>' + (d.daira_name || '') +
                                    ' &middot; ' + (d.created_at || '') +
                                    '</div></div>';
                            });
                            Swal.fire({
                                title: <?= json_encode(__('report_create.duplicate_title')) ?>,
                                html: '<div style="text-align:center;margin-bottom:12px;"><i class="fas fa-exclamation-triangle" style="font-size:2.5rem;color:var(--amber);"></i></div>' +
                                    '<p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:14px;">' + <?= json_encode(__('report_create.duplicate_count')) ?>.replace('{count}', data.count) + '</p>' +
                                    '<div style="max-height:260px;overflow-y:auto;">' + rows + '</div>',
                                showCancelButton: true,
                                confirmButtonText: <?= json_encode(__('report_create.duplicate_track')) ?>,
                                cancelButtonText: <?= json_encode(__('report_create.duplicate_continue')) ?>,
                                confirmButtonColor: 'var(--accent)',
                                cancelButtonColor: 'var(--text-muted)',
                                reverseButtons: true,
                                width: 560,
                                background: isDark ? '#1a2235' : '#fff',
                                color: isDark ? '#f1f5f9' : '#111827'
                            }).then(function(result) {
                                if (result.isConfirmed) {
                                    window.location.href = '/reports/' + data.duplicates[0].id;
                                } else {
                                    advanceStep();
                                }
                            });
                        } else {
                            advanceStep();
                        }
                    })
                    .catch(function() { advanceStep(); });
            } else {
                advanceStep();
            }
        });
    });

    document.querySelectorAll('.btn-prev').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentStep > 0) showStep(currentStep - 1);
        });
    });

    steps.forEach(function(step, i) {
        step.addEventListener('click', function() { showStep(i); });
    });

    showStep(0);

    // === Category cards click ===
    document.querySelectorAll('.cat-card').forEach(function(card) {
        card.addEventListener('click', function() {
            var sel = document.getElementById('categorySelect');
            sel.value = this.dataset.catId;
            sel.dispatchEvent(new Event('change'));
        });
    });

    // === Load communes when daira changes ===
    document.getElementById('dairaSelect').addEventListener('change', function() {
        var dairaId = this.value;
        var sel = document.getElementById('communeSelect');
        sel.innerHTML = '<option value=""><?= addslashes(__('common.loading')) ?></option>';
        if (dairaId) {
            fetch('/api/communes/' + dairaId)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    sel.innerHTML = '<option value=""><?= addslashes(__('report_create.select')) ?></option>';
                    data.forEach(function(c) { sel.innerHTML += '<option value="' + c.id + '">' + c.name + '</option>'; });
                });
        } else {
            sel.innerHTML = '<option value=""><?= addslashes(__('report_create.choose_daira_first')) ?></option>';
        }
    });

    // === Load subcategories when category changes ===
    document.getElementById('categorySelect').addEventListener('change', function() {
        var catId = this.value;
        var sel = document.getElementById('subcategorySelect');
        sel.innerHTML = '<option value=""><?= addslashes(__('common.loading')) ?></option>';
        if (catId) {
            fetch('/api/subcategories/' + catId)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    sel.innerHTML = '<option value=""><?= addslashes(__('report_create.select')) ?></option>';
                    data.forEach(function(s) { sel.innerHTML += '<option value="' + s.id + '">' + s.name + '</option>'; });
                })
                .catch(function() { sel.innerHTML = '<option value=""><?= addslashes(__('report_create.select')) ?></option>'; });
        } else {
            sel.innerHTML = '<option value=""><?= addslashes(__('report_create.select_subcat')) ?></option>';
        }
    });

    // === Photo/Video file input ===
    var photoInput = document.getElementById('photos');
    var photoDropZone = document.getElementById('photoDropZone');
    var photoCamera = document.getElementById('photoCamera');
    var photoPreview = document.getElementById('photoPreview');

    // No need for click handler — the input is overlaid on the drop zone

    // Hover effect on drop zone
    photoDropZone.addEventListener('dragenter', function(e) {
        e.preventDefault(); e.stopPropagation();
        photoDropZone.style.borderColor = 'var(--accent)';
        photoDropZone.style.background = 'var(--accent-surface)';
    });
    photoDropZone.addEventListener('dragover', function(e) {
        e.preventDefault(); e.stopPropagation();
    });
    photoDropZone.addEventListener('dragleave', function(e) {
        e.preventDefault(); e.stopPropagation();
        photoDropZone.style.borderColor = 'var(--border)';
        photoDropZone.style.background = 'transparent';
    });
    photoDropZone.addEventListener('drop', function(e) {
        e.preventDefault(); e.stopPropagation();
        photoDropZone.style.borderColor = 'var(--border)';
        photoDropZone.style.background = 'transparent';
        if (e.dataTransfer.files.length) {
            photoInput.files = e.dataTransfer.files;
            photoInput.dispatchEvent(new Event('change'));
        }
    });

    function renderPreviews(files) {
        photoPreview.innerHTML = '';
        Array.from(files).forEach(function(file) {
            var col = document.createElement('div');
            col.className = 'col-4 col-md-3';
            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    col.innerHTML = '<div style="border-radius:8px;overflow:hidden;aspect-ratio:1;background:var(--bg-elevated);position:relative;"><img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;"><span style="position:absolute;bottom:4px;inset-inline-start:4px;background:rgba(0,0,0,0.6);color:#fff;font-size:0.6rem;padding:2px 5px;border-radius:4px;"><i class="fas fa-image me-1"></i>' + (file.size/1024).toFixed(0) + ' KB</span></div>';
                    photoPreview.appendChild(col);
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                var video = document.createElement('video');
                video.preload = 'metadata';
                video.onloadeddata = function() {
                    video.currentTime = 1;
                };
                video.onseeked = function() {
                    var canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    col.innerHTML = '<div style="border-radius:8px;overflow:hidden;aspect-ratio:1;background:var(--bg-elevated);position:relative;"><img src="' + canvas.toDataURL() + '" style="width:100%;height:100%;object-fit:cover;"><span style="position:absolute;bottom:4px;inset-inline-start:4px;background:rgba(0,0,0,0.6);color:#fff;font-size:0.6rem;padding:2px 5px;border-radius:4px;"><i class="fas fa-video me-1"></i>' + (file.size/1024/1024).toFixed(1) + ' MB</span></div>';
                    photoPreview.appendChild(col);
                };
                video.src = URL.createObjectURL(file);
            }
        });
    }

    photoInput.addEventListener('change', function() {
        if (this.files.length) renderPreviews(this.files);
    });

    // Mobile camera button
    if (photoCamera) {
        var btnCamera = document.getElementById('btnCamera');
        if (btnCamera) {
            btnCamera.addEventListener('click', function() {
                photoCamera.click();
            });
            photoCamera.addEventListener('change', function() {
                if (this.files.length) {
                    // Merge camera files with gallery files
                    var dt = new DataTransfer();
                    if (photoInput.files.length) {
                        for (var i = 0; i < photoInput.files.length; i++) dt.items.add(photoInput.files[i]);
                    }
                    for (var j = 0; j < this.files.length; j++) dt.items.add(this.files[j]);
                    photoInput.files = dt.files;
                    renderPreviews(photoInput.files);
                }
            });
        }
    }

    // === GPS Map ===
    var map = null;
    var marker = null;
    var mapInitialized = false;

    function initMap() {
        if (mapInitialized) return;
        mapInitialized = true;
        try {
            map = L.map('reportMap').setView([36.7538, 3.0588], 12);
            var isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            var tileUrl = isDark
                ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
            L.tileLayer(tileUrl, { attribution: '&copy; OSM', maxZoom: 18 }).addTo(map);

            map.on('click', function(e) {
                if (marker) map.removeLayer(marker);
                marker = L.marker(e.latlng).addTo(map);
                document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
                document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
                document.getElementById('gpsMapContainer').classList.add('acquired');
                var status = document.getElementById('gpsStatus');
                status.innerHTML = '<i class="fas fa-check-circle me-1" style="color:var(--green);"></i> <?= addslashes(__('report_create.gps_acquired')) ?>: ' + e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6);
                status.style.color = 'var(--green)';
            });

            setTimeout(function() { map.invalidateSize(); }, 100);
        } catch(e) { console.error('Map init error:', e); }
    }

    // Init map when reaching GPS step
    var observer = new MutationObserver(function() {
        if (contents[5] && contents[5].classList.contains('active')) {
            initMap();
        }
    });
    observer.observe(contents[5], { attributes: true, attributeFilter: ['class'] });
    // Also init if we jump to step 5
    if (contents[5] && contents[5].classList.contains('active')) initMap();

    // === GPS Button ===
    document.getElementById('btnGetGps').addEventListener('click', function() {
        if (!navigator.geolocation) { alert('<?= __('ui.geolocation_unsupported') ?>'); return; }
        var status = document.getElementById('gpsStatus');
        status.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> <?= addslashes(__('report_create.gps_loading')) ?>';
        status.style.color = 'var(--amber)';
        navigator.geolocation.getCurrentPosition(function(pos) {
            var lat = pos.coords.latitude, lng = pos.coords.longitude;
            if (map) { map.setView([lat, lng], 16); }
            if (marker && map) map.removeLayer(marker);
            if (map) marker = L.marker([lat, lng]).addTo(map);
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
            document.getElementById('gpsMapContainer').classList.add('acquired');
            status.innerHTML = '<i class="fas fa-check-circle me-1" style="color:var(--green);"></i> <?= addslashes(__('report_create.gps_ready')) ?>: ' + lat.toFixed(6) + ', ' + lng.toFixed(6) + ' (précision: ' + Math.round(pos.coords.accuracy) + 'm)';
            status.style.color = 'var(--green)';
        }, function() {
            status.innerHTML = '<i class="fas fa-exclamation-triangle me-1" style="color:var(--red);"></i> <?= addslashes(__('report_create.gps_error')) ?>';
            status.style.color = 'var(--red)';
        }, { enableHighAccuracy: true, timeout: 10000 });
    });

    // === Similar Reports (nearby) ===
    var similarTimer = null;
    var similarContainer = document.getElementById('similarReports');
    var statusColors = {pending:'var(--amber)',in_progress:'var(--cyan)',resolved:'var(--green)',closed:'var(--text-muted)',rejected:'var(--red)'};
    var statusLabels = <?= json_encode(__('statuses')) ?>;

    function formatDistance(m) {
        return m >= 1000 ? (m / 1000).toFixed(1) + ' km' : Math.round(m) + ' m';
    }

    function fetchSimilarReports() {
        var catId = document.getElementById('categorySelect').value;
        var lat = document.getElementById('latitude').value;
        var lng = document.getElementById('longitude').value;
        if (!catId || !lat || !lng) { similarContainer.style.display = 'none'; return; }

        similarContainer.style.display = 'block';
        similarContainer.innerHTML = '<div class="sr-loading"><i class="fas fa-spinner fa-spin me-1"></i> <?= addslashes(__('report_create.searching_similar')) ?></div>';

        var url = '/api/reports/nearby?lat=' + encodeURIComponent(lat) + '&lng=' + encodeURIComponent(lng) + '&category_id=' + encodeURIComponent(catId);
        fetch(url)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.count === 0) {
                    similarContainer.innerHTML = '<div class="sr-none"><i class="fas fa-check-circle"></i> <?= addslashes(__('report_create.no_similar')) ?></div>';
                    return;
                }
                var html = '<div class="sr-header"><i class="fas fa-clone"></i> <?= addslashes(__('report_create.similar_found')) ?> (' + data.count + ')</div>';
                data.reports.forEach(function(r) {
                    var sc = statusColors[r.status] || 'var(--text-muted)';
                    var sl = (typeof statusLabels === 'object' && statusLabels[r.status]) ? statusLabels[r.status] : r.status;
                    var dist = r.distance_meters != null ? formatDistance(r.distance_meters) : '';
                    html += '<div class="sr-card">' +
                        '<div style="display:flex;align-items:center;gap:8px;">' +
                        '<a href="/reports/' + r.id + '" class="sr-title" target="_blank">' + (r.title || '—') + '</a>' +
                        '<span class="sr-badge" style="background:' + sc + '22;color:' + sc + ';">' + sl + '</span>' +
                        '<span class="sr-distance"><i class="fas fa-location-dot me-1"></i>' + dist + '</span>' +
                        '</div>' +
                        '</div>';
                });
                similarContainer.innerHTML = html;
            })
            .catch(function() {
                similarContainer.innerHTML = '';
                similarContainer.style.display = 'none';
            });
    }

    function debounceSimilar() {
        clearTimeout(similarTimer);
        similarTimer = setTimeout(fetchSimilarReports, 300);
    }

    document.getElementById('categorySelect').addEventListener('change', debounceSimilar);
    document.getElementById('latitude').addEventListener('input', debounceSimilar);
    document.getElementById('longitude').addEventListener('input', debounceSimilar);

    // Intercept programmatic value changes (map click, GPS button)
    function interceptValue(el) {
        var descriptor = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');
        Object.defineProperty(el, 'value', {
            get: function() { return descriptor.get.call(this); },
            set: function(v) { descriptor.set.call(this, v); debounceSimilar(); }
        });
    }
    interceptValue(document.getElementById('latitude'));
    interceptValue(document.getElementById('longitude'));
})();
</script>
