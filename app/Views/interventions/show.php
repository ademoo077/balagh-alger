<?php $pageTitle = $report['tracking_code'] . ' — ' . __('interventions.title'); ?>

<!-- Workflow Progress Bar -->
<div class="card mb-3 animate-fade-in-up">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1" style="font-size:0.72rem;">
            <?php
            $steps = [
                0 => [__('interventions.workflow_created'), 'fas fa-plus-circle'],
                1 => [__('interventions.workflow_received'), 'fas fa-inbox'],
                2 => [__('interventions.workflow_chef_u'), 'fas fa-user-tie'],
                3 => [__('interventions.workflow_chef_s'), 'fas fa-user-shield'],
                4 => [__('interventions.workflow_agent'), 'fas fa-hard-hat'],
                5 => [__('interventions.workflow_verified'), 'fas fa-clipboard-check'],
                6 => [__('interventions.workflow_validated_u'), 'fas fa-check-double'],
                7 => [__('interventions.workflow_approved'), 'fas fa-certificate'],
            ];
            $currentStep = $report['workflow_step'] ?? 0;
            ?>
            <?php foreach ($steps as $stepNum => $stepInfo): ?>
            <div class="text-center" style="flex:1;min-width:45px;">
                <div style="width:22px;height:22px;border-radius:50%;margin:0 auto 2px;display:flex;align-items:center;justify-content:center;
                    background:<?= $stepNum <= $currentStep ? 'var(--accent)' : 'var(--bg-elevated)' ?>;
                    color:<?= $stepNum <= $currentStep ? '#fff' : 'var(--text-muted)' ?>;font-size:0.5rem;transition:all 0.3s;">
                    <i class="<?= $stepInfo[1] ?>"></i>
                </div>
                <div style="color:<?= $stepNum <= $currentStep ? 'var(--text-primary)' : 'var(--text-muted)' ?>;font-weight:<?= $stepNum <= $currentStep ? '700' : '400' ?>;font-size:0.6rem;">
                    <?= $stepInfo[0] ?>
                </div>
            </div>
            <?php if ($stepNum < 7): ?>
            <div style="flex:0.15;height:2px;background:<?= $stepNum < $currentStep ? 'var(--accent)' : 'var(--border)' ?>;border-radius:2px;"></div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <!-- Report Info -->
        <div class="card mb-3 animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-file-alt me-2 text-accent"></i><?= __('interventions.details_card') ?></h6></div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge" style="background:<?= $report['category_color'] ?? 'var(--accent)' ?>20;color:<?= $report['category_color'] ?? 'var(--accent)' ?>;">
                            <i class="<?= $report['category_icon'] ?? 'fas fa-flag' ?>"></i> <?= $report['category_name'] ?>
                        </span>
                        <h5 class="mt-2 mb-1" style="font-size:1rem;font-weight:700;"><?= \App\Helpers\Helper::sanitize($report['title']) ?></h5>
                        <small class="text-muted" style="font-size:0.75rem;"><?= $report['tracking_code'] ?> — <?= __('interventions.workflow_created') ?> <?= \App\Helpers\Helper::timeAgo($report['created_at']) ?></small>
                    </div>
                    <div class="text-end">
                        <?= \App\Helpers\Helper::getStatusBadge($report['status']) ?>
                        <?= \App\Helpers\Helper::getPriorityBadge($report['priority']) ?>
                    </div>
                </div>
                <p class="mb-3" style="font-size:0.85rem;"><?= nl2br(\App\Helpers\Helper::sanitize($report['description'])) ?></p>
                <div class="row g-2" style="font-size:0.82rem;">
                    <div class="col-sm-6"><i class="fas fa-map text-muted me-1" style="width:12px;font-size:0.75rem;"></i> <strong><?= __('interventions.daira_label') ?></strong> <?= $report['daira_name'] ?></div>
                    <div class="col-sm-6"><i class="fas fa-city text-muted me-1" style="width:12px;font-size:0.75rem;"></i> <strong><?= __('interventions.commune_label') ?></strong> <?= $report['commune_name'] ?></div>
                    <div class="col-sm-6"><i class="fas fa-location-dot text-muted me-1" style="width:12px;font-size:0.75rem;"></i> <strong><?= __('interventions.address_label') ?></strong> <?= $report['address'] ?></div>
                    <div class="col-sm-6"><i class="fas fa-building text-muted me-1" style="width:12px;font-size:0.75rem;"></i> <strong><?= __('interventions.org_label') ?></strong> <?= $report['org_name'] ?? __('reports.not_assigned') ?></div>
                </div>
                <?php if ($report['latitude'] && $report['longitude']): ?>
                <div class="mt-3">
                    <div id="reportMap" style="height:180px;border-radius:10px;overflow:hidden;"></div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="text-muted font-mono" style="font-size:0.7rem;"><?= $report['latitude'] ?>, <?= $report['longitude'] ?></small>
                        <a href="https://www.google.com/maps?q=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>" target="_blank" style="color:var(--accent);font-size:0.78rem;text-decoration:none;"><i class="fas fa-external-link-alt me-1"></i><?= __('reports.google_maps') ?></a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Citizen Before Photos -->
        <div class="card mb-3 animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-camera me-2 text-cyan"></i><?= __('interventions.citizen_photos') ?></h6></div>
            <div class="card-body">
                <?php if (empty($citizenPhotos)): ?>
                <p class="text-muted" style="font-size:0.82rem;"><?= __('interventions.no_citizen_photo') ?></p>
                <?php else: ?>
                <div class="row g-2">
                    <?php foreach ($citizenPhotos as $p): ?>
                    <div class="col-sm-4"><div class="photo-gallery-item"><img src="/uploads/reports/<?= htmlspecialchars($p['filename']) ?>" alt="Photo citoyen" loading="lazy"></div></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Agent Intervention Photos -->
        <?php $duringPhotos = array_filter($photos, fn($p) => $p['photo_type'] === 'during');
        $afterPhotos = array_filter($photos, fn($p) => $p['photo_type'] === 'after');
        if (!empty($duringPhotos) || !empty($afterPhotos)): ?>
        <div class="card mb-3 animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-camera-retro me-2 text-green"></i><?= __('interventions.intervention_photos') ?></h6></div>
            <div class="card-body">
                <?php if (!empty($duringPhotos)): ?>
                <h6 style="font-size:0.78rem;color:var(--text-muted);margin-bottom:8px;"><i class="fas fa-wrench me-1"></i> <?= __('interventions.during') ?></h6>
                <div class="row g-2 mb-3">
                    <?php foreach ($duringPhotos as $p): ?>
                    <div class="col-sm-4">
                        <div class="photo-gallery-item"><img src="/uploads/interventions/<?= $report['id'] ?>/<?= $p['filename'] ?>" alt="Pendant" loading="lazy"></div>
                        <?php if ($p['caption']): ?><small class="text-muted d-block mt-1"><?= $p['caption'] ?></small><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($afterPhotos)): ?>
                <h6 style="font-size:0.78rem;color:var(--text-muted);margin-bottom:8px;"><i class="fas fa-check-circle me-1 text-green"></i> <?= __('interventions.after') ?></h6>
                <div class="row g-2">
                    <?php foreach ($afterPhotos as $p): ?>
                    <div class="col-sm-4">
                        <div class="photo-gallery-item" style="border:2px solid var(--green);"><img src="/uploads/interventions/<?= $report['id'] ?>/<?= $p['filename'] ?>" alt="Après" loading="lazy"></div>
                        <?php if ($p['caption']): ?><small class="text-muted d-block mt-1"><?= $p['caption'] ?></small><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Comments -->
        <div class="card mb-3 animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-comments me-2 text-accent"></i><?= __('interventions.comments') ?> (<?= count($comments) ?>)</h6></div>
            <div class="card-body">
                <?php foreach ($comments as $cm): ?>
                <div class="d-flex mb-3 pb-3" style="border-bottom:1px solid var(--border);">
                    <div class="user-avatar me-3" style="width:28px;height:28px;border-radius:7px;font-size:0.6rem;flex-shrink:0;"><?= strtoupper(substr($cm['first_name'], 0, 1)) ?></div>
                    <div>
                        <div class="fw-semibold" style="font-size:0.8rem;"><?= $cm['first_name'] ?> <?= $cm['last_name'] ?> <small class="text-muted fw-normal ms-2"><?= \App\Helpers\Helper::timeAgo($cm['created_at']) ?></small></div>
                        <div class="text-secondary mt-1" style="font-size:0.8rem;"><?= nl2br(\App\Helpers\Helper::sanitize($cm['comment'])) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($comments)): ?>
                <p class="text-muted" style="font-size:0.82rem;"><?= __('interventions.no_comments') ?></p>
                <?php endif; ?>
                <form method="POST" action="/reports/<?= $report['id'] ?>/comment" class="mt-2">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <div class="input-group">
                        <input type="text" class="form-control" name="comment" placeholder="<?= __('interventions.add_comment') ?>" required>
                        <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- History -->
        <div class="card mb-3 animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-history me-2 text-accent"></i><?= __('interventions.history') ?></h6></div>
            <div class="card-body">
                <?php foreach ($history as $h): ?>
                <div class="activity-item" style="font-size:0.8rem;">
                    <div class="activity-dot"></div>
                    <div>
                        <strong><?= $h['action'] ?></strong>
                        <?php if ($h['first_name']): ?><span class="text-secondary"> par <?= $h['first_name'] ?> <?= $h['last_name'] ?></span><?php endif; ?>
                        <small class="text-muted ms-1"><?= \App\Helpers\Helper::timeAgo($h['created_at']) ?></small>
                        <?php if ($h['note']): ?><div class="text-secondary mt-1"><?= $h['note'] ?></div><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Assignment -->
        <?php if ($canAssign && $report['workflow_step'] < 4): ?>
        <div class="card mb-3 animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-user-check me-2 text-amber"></i><?= __('interventions.assign_card') ?></h6></div>
            <div class="card-body">
                <form method="POST" action="/interventions/<?= $report['id'] ?>/assign">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <select name="assigned_to" class="form-select form-select-sm mb-2" required>
                        <option value=""><?= __('report_create.select') ?></option>
                        <?php foreach ($agents as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= $a['first_name'] ?> <?= $a['last_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-warning w-100"><i class="fas fa-user-check me-1"></i> <?= __('interventions.assign_button') ?></button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Agent Actions -->
        <?php if ($canIntervene): ?>
        <?php if (in_array($report['status'], ['assigned', 'in_progress']) && $report['workflow_step'] == 4): ?>
        <div class="card mb-3 animate-fade-in-up" style="border-top:3px solid var(--amber);">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-play-circle me-2 text-amber"></i><?= __('interventions.start_card') ?></h6></div>
            <div class="card-body">
                <form method="POST" action="/interventions/<?= $report['id'] ?>/start">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <textarea class="form-control form-control-sm mb-2" name="description" rows="2" placeholder="<?= __('interventions.start_notes') ?>"></textarea>
                    <input type="hidden" name="latitude" id="agentLat">
                    <input type="hidden" name="longitude" id="agentLng">
                    <small class="text-muted d-block mb-2" style="font-size:0.7rem;" id="gpsStatus"><?= __('interventions.gps_waiting') ?></small>
                    <button type="submit" class="btn btn-sm w-100" style="background:var(--amber);color:#fff;font-weight:700;"><i class="fas fa-play me-1"></i> <?= __('interventions.start_button') ?></button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($report['status'] === 'in_progress'): ?>
        <div class="card mb-3 animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-camera me-2 text-cyan"></i><?= __('interventions.add_photo') ?></h6></div>
            <div class="card-body">
                <form method="POST" action="/interventions/<?= $report['id'] ?>/photo" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <select name="photo_type" class="form-select form-select-sm mb-2">
                        <option value="during"><?= __('interventions.photo_during') ?></option>
                        <option value="after"><?= __('interventions.photo_after') ?></option>
                    </select>
                    <input type="file" class="form-control form-control-sm mb-2" name="photo" accept="image/*" capture="environment" required>
                    <input type="text" class="form-control form-control-sm mb-2" name="caption" placeholder="<?= __('interventions.photo_legend') ?>">
                    <input type="hidden" name="latitude" value="<?= $report['latitude'] ?>">
                    <input type="hidden" name="longitude" value="<?= $report['longitude'] ?>">
                    <button type="submit" class="btn btn-sm btn-info w-100"><i class="fas fa-upload me-1"></i> <?= __('interventions.send') ?></button>
                </form>
            </div>
        </div>

        <div class="card mb-3 animate-fade-in-up" style="border-top:3px solid var(--green);">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-check-circle me-2 text-green"></i><?= __('interventions.finish_card') ?></h6></div>
            <div class="card-body">
                <?php if (empty($afterPhotos)): ?>
                <div class="alert alert-warning py-2" style="font-size:0.78rem;"><i class="fas fa-exclamation-triangle me-1"></i> <?= __('interventions.finish_warning') ?></div>
                <?php endif; ?>
                <form method="POST" action="/interventions/<?= $report['id'] ?>/complete">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <textarea class="form-control form-control-sm mb-2" name="completion_note" rows="2" placeholder="<?= __('interventions.finish_notes') ?>"></textarea>
                    <button type="submit" class="btn btn-sm w-100" style="background:var(--green);color:#fff;font-weight:700;" <?= empty($afterPhotos) ? 'disabled' : '' ?> data-confirm="<?= __('ui.finish_intervention') ?>">
                        <i class="fas fa-check me-1"></i> <?= __('interventions.finish_button') ?>
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Chef Section Validation -->
        <?php if ($canValidateSection && $report['status'] === 'pending_review'): ?>
        <div class="card mb-3 animate-fade-in-up validation-card section-review">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-clipboard-check me-2 text-amber"></i><?= __('interventions.verify_card') ?></h6></div>
            <div class="card-body">
                <p style="font-size:0.78rem;color:var(--text-muted);"><?= __('interventions.verify_info') ?></p>
                <form method="POST" action="/interventions/<?= $report['id'] ?>/validate">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <div class="d-flex gap-2 mb-2">
                        <button type="submit" name="validation_action" value="validate" class="btn btn-sm flex-fill" style="background:var(--green);color:#fff;font-weight:700;" data-confirm="<?= __('ui.validate_report') ?>"><i class="fas fa-check me-1"></i> <?= __('interventions.validate') ?></button>
                        <button type="submit" name="validation_action" value="reject" class="btn btn-sm flex-fill" style="background:var(--red);color:#fff;font-weight:700;" data-confirm="<?= __('ui.reject_report') ?>"><i class="fas fa-redo me-1"></i> <?= __('interventions.reject') ?></button>
                    </div>
                    <textarea class="form-control form-control-sm" name="rejection_reason" rows="2" placeholder="<?= __('interventions.reject_reason') ?>"></textarea>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Chef Unite Validation -->
        <?php if ($canValidateUnite && $report['status'] === 'pending_unite'): ?>
        <div class="card mb-3 animate-fade-in-up validation-card unite-review">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-check-double me-2 text-accent"></i><?= __('interventions.validate_unite_card') ?></h6></div>
            <div class="card-body">
                <p style="font-size:0.78rem;color:var(--text-muted);"><?= __('interventions.validate_unite_info') ?></p>
                <form method="POST" action="/interventions/<?= $report['id'] ?>/validate">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <div class="d-flex gap-2 mb-2">
                        <button type="submit" name="validation_action" value="validate" class="btn btn-sm flex-fill" style="background:var(--green);color:#fff;font-weight:700;" data-confirm="<?= __('ui.validate_definitive') ?>"><i class="fas fa-check me-1"></i> <?= __('interventions.validate') ?></button>
                        <button type="submit" name="validation_action" value="reject" class="btn btn-sm flex-fill" style="background:var(--red);color:#fff;font-weight:700;" data-confirm="<?= __('ui.reject_report') ?>"><i class="fas fa-redo me-1"></i> <?= __('interventions.reject') ?></button>
                    </div>
                    <textarea class="form-control form-control-sm" name="rejection_reason" rows="2" placeholder="<?= __('interventions.reject_reason') ?>"></textarea>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Close -->
        <?php if ($canClose && in_array($report['status'], ['validated', 'resolved'])): ?>
        <div class="card mb-3 animate-fade-in-up validation-card close-card">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-folder-check me-2 text-purple"></i><?= __('interventions.close_card') ?></h6></div>
            <div class="card-body">
                <form method="POST" action="/interventions/<?= $report['id'] ?>/close">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <textarea class="form-control form-control-sm mb-2" name="close_note" rows="2" placeholder="<?= __('interventions.close_notes') ?>"></textarea>
                    <button type="submit" class="btn btn-sm w-100" style="background:var(--purple);color:#fff;font-weight:700;" data-confirm="<?= __('ui.clone_definitive') ?>"><i class="fas fa-lock me-1"></i> <?= __('interventions.close_button') ?></button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Citizen Info -->
        <div class="card mb-3 animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-user me-2 text-cyan"></i><?= __('interventions.citizen_card') ?></h6></div>
            <div class="card-body" style="font-size:0.82rem;">
                <div class="mb-1"><i class="fas fa-user text-muted me-1" style="width:12px;font-size:0.72rem;"></i> <?= $report['citizen_name'] ?? __('reports.anonymous') ?></div>
                <div class="mb-1"><i class="fas fa-phone text-muted me-1" style="width:12px;font-size:0.72rem;"></i> <?= $report['citizen_phone'] ?? '-' ?></div>
                <div><i class="fas fa-envelope text-muted me-1" style="width:12px;font-size:0.72rem;"></i> <?= $report['citizen_email'] ?? '-' ?></div>
            </div>
        </div>

        <!-- Chronology -->
        <div class="card animate-fade-in-up">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-clock me-2 text-amber"></i><?= __('interventions.timeline') ?></h6></div>
            <div class="card-body" style="font-size:0.78rem;">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted"><?= __('interventions.workflow_created') ?></span>
                    <span class="font-mono"><?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></span>
                </div>
                <?php if ($report['assigned_at_central']): ?>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted"><?= __('interventions.workflow_received') ?></span><span class="font-mono"><?= date('d/m/Y H:i', strtotime($report['assigned_at_central'])) ?></span></div>
                <?php endif; ?>
                <?php if ($report['assigned_at_chef_unite']): ?>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted"><?= __('interventions.timeline_chef_u') ?></span><span class="font-mono"><?= date('d/m/Y H:i', strtotime($report['assigned_at_chef_unite'])) ?></span></div>
                <?php endif; ?>
                <?php if ($report['assigned_at_chef_section']): ?>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted"><?= __('interventions.timeline_chef_s') ?></span><span class="font-mono"><?= date('d/m/Y H:i', strtotime($report['assigned_at_chef_section'])) ?></span></div>
                <?php endif; ?>
                <?php if ($report['assigned_at_agent']): ?>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted"><?= __('interventions.workflow_agent') ?></span><span class="font-mono"><?= date('d/m/Y H:i', strtotime($report['assigned_at_agent'])) ?></span></div>
                <?php endif; ?>
                <?php if ($report['resolved_at']): ?>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted"><?= __('statuses.resolved') ?></span><span class="font-mono" style="color:var(--green);"><?= date('d/m/Y H:i', strtotime($report['resolved_at'])) ?></span></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var theme = document.documentElement.getAttribute('data-bs-theme');
    <?php if ($report['latitude'] && $report['longitude']): ?>
    var tileUrl = theme === 'dark' ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
    var map = L.map('reportMap').setView([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>], 15);
    L.tileLayer(tileUrl, { attribution: '&copy; OSM CARTO' }).addTo(map);
    L.marker([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>]).addTo(map).bindPopup('<?= $report['tracking_code'] ?>').openPopup();
    <?php endif; ?>

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            var latEl = document.getElementById('agentLat');
            var lngEl = document.getElementById('agentLng');
            var gpsEl = document.getElementById('gpsStatus');
            if (latEl) latEl.value = pos.coords.latitude.toFixed(6);
            if (lngEl) lngEl.value = pos.coords.longitude.toFixed(6);
            if (gpsEl) { gpsEl.textContent = 'GPS: ' + pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6); gpsEl.style.color = 'var(--green)'; }
        }, function() {
            var gpsEl = document.getElementById('gpsStatus');
            if (gpsEl) gpsEl.textContent = '<?= __('interventions.gps_unavailable') ?>';
        });
    }
});
</script>
