<?php $pageTitle = $report['tracking_code']; ?>
<?php
$catColor = $report['category_color'] ?? '#6366f1';
$currentWf = $report['workflow_step'] ?? 0;
$wfSteps = [
    0 => [__('interventions.workflow_created'), 'fas fa-plus-circle'],
    1 => [__('interventions.workflow_received'), 'fas fa-inbox'],
    2 => [__('interventions.workflow_chef_u'), 'fas fa-user-tie'],
    3 => [__('interventions.workflow_chef_s'), 'fas fa-user-shield'],
    4 => [__('interventions.workflow_agent'), 'fas fa-hard-hat'],
    5 => [__('interventions.workflow_verified'), 'fas fa-clipboard-check'],
    6 => [__('interventions.workflow_validated_u'), 'fas fa-check-double'],
    7 => [__('interventions.workflow_approved'), 'fas fa-certificate'],
];
?>
<!-- ==================== WORKFLOW STEPPER ==================== -->
<div class="card mb-3 animate-fade-in-up show-card" style="overflow:hidden;">
    <div class="show-wf-stepper">
        <?php foreach ($wfSteps as $stepNum => $stepInfo): ?>
        <div class="wf-col">
            <div class="wf-node <?= $stepNum < $currentWf ? 'done' : ($stepNum === $currentWf ? 'active' : 'pending') ?>">
                <i class="<?= $stepInfo[1] ?>"></i>
            </div>
            <div class="wf-label" style="color:<?= $stepNum <= $currentWf ? 'var(--text-primary)' : 'var(--text-muted)' ?>;">
                <?= $stepInfo[0] ?>
            </div>
        </div>
        <?php if ($stepNum < 7): ?>
        <div style="flex:0.5;display:flex;align-items:center;padding-top:0;">
            <div class="wf-line" style="background:<?= $stepNum < $currentWf ? 'var(--accent)' : 'var(--border)' ?>;"></div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<!-- ==================== OFFICIAL BANNER ==================== -->
<div class="c-official-banner animate-fade-in-up" style="background:linear-gradient(135deg,#D2122E 0%,#A30E24 50%,#8B0A1C 100%);box-shadow:0 16px 48px rgba(210,18,46,0.35);">
    <div class="banner-flag">
        <div class="banner-flag-green"></div>
        <div class="banner-flag-white"></div>
        <div class="banner-flag-red"></div>
    </div>
    <div class="banner-content">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="show-banner-label-group">
                    <div class="show-banner-icon"><i class="fas fa-shield-halved"></i></div>
                    <div>
                        <div class="show-banner-wilaya"><?= __('app.wilaya') ?></div>
                        <div class="show-banner-sublabel"><?= __('reports.wilaya_banner') ?></div>
                    </div>
                </div>
                <h4 class="show-banner-title"><?= \App\Helpers\Helper::sanitize($report['title']) ?></h4>
                <div class="show-banner-badges">
                    <span class="qr-badge" style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.2);">
                        <i class="fas fa-hashtag" style="font-size:0.65rem;"></i>
                        <?= $report['tracking_code'] ?>
                    </span>
                    <?= \App\Helpers\Helper::getStatusBadge($report['status']) ?>
                    <?= \App\Helpers\Helper::getPriorityBadge($report['priority']) ?>
                    <?= \App\Helpers\DeadlineHelper::renderBadge($report['created_at'], (int)($report['deadline_days'] ?? 7), $report['status']) ?>
                </div>
            </div>
            <a href="/reports" class="show-banner-back"><i class="fas fa-arrow-left"></i> <?= __('common.back') ?></a>
        </div>
    </div>
    <div class="show-banner-meta">
        <div class="show-banner-meta-group">
            <div class="show-banner-meta-item"><i class="fas fa-calendar-check" style="color:#059669;"></i><span><?= date('d/m/Y', strtotime($report['created_at'])) ?></span></div>
            <div class="show-banner-meta-item"><i class="fas fa-clock" style="color:#d97706;"></i><span><?= date('H:i:s', strtotime($report['created_at'])) ?></span></div>
            <div class="show-banner-meta-item muted"><i class="fas fa-stopwatch"></i><span><?= \App\Helpers\Helper::timeAgo($report['created_at']) ?></span></div>
        </div>
        <div class="show-banner-meta-item ghost"><i class="fas fa-user"></i><span><?= $report['citizen_name'] ?? __('reports.anonymous') ?></span></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">

        <!-- ==================== DETAILS ==================== -->
        <div class="card mb-3 animate-fade-in-up show-card">
            <div class="card-header">
                <div class="show-section-header">
                    <div class="show-section-icon" style="background:var(--accent-surface);color:var(--accent);"><i class="fas fa-info-circle"></i></div>
                    <h6 class="mb-0"><?= __('reports.details') ?></h6>
                </div>
            </div>
            <div class="card-body">
                <p class="show-description"><?= nl2br(\App\Helpers\Helper::sanitize($report['description'])) ?></p>

                <div class="show-info-grid">
                    <div class="show-info-item">
                        <div class="info-icon" style="background:<?= $catColor ?>15;color:<?= $catColor ?>;"><i class="fas fa-tag"></i></div>
                        <div><div class="info-label"><?= __('common.category') ?></div><div class="info-value"><?= $report['category_name'] ?></div></div>
                    </div>
                    <div class="show-info-item">
                        <div class="info-icon" style="background:var(--cyan-surface);color:var(--cyan);"><i class="fas fa-map"></i></div>
                        <div><div class="info-label"><?= __('common.daira') ?></div><div class="info-value"><?= $report['daira_name'] ?></div></div>
                    </div>
                    <div class="show-info-item">
                        <div class="info-icon" style="background:var(--green-surface);color:var(--green);"><i class="fas fa-city"></i></div>
                        <div><div class="info-label"><?= __('common.commune') ?></div><div class="info-value"><?= $report['commune_name'] ?></div></div>
                    </div>
                    <div class="show-info-item">
                        <div class="info-icon" style="background:var(--amber-surface);color:var(--amber);"><i class="fas fa-location-dot"></i></div>
                        <div><div class="info-label"><?= __('common.address') ?></div><div class="info-value"><?= $report['address'] ?: '—' ?></div></div>
                    </div>
                    <div class="show-info-item">
                        <div class="info-icon" style="background:rgba(124,58,237,0.08);color:var(--purple);"><i class="fas fa-building"></i></div>
                        <div><div class="info-label"><?= __('common.organization') ?></div><div class="info-value"><?= $report['org_name'] ?? __('reports.not_assigned') ?></div></div>
                    </div>
                    <div class="show-info-item">
                        <div class="info-icon" style="background:var(--red-surface);color:var(--red);"><i class="fas fa-user"></i></div>
                        <div><div class="info-label"><?= __('reports.assign_to') ?></div><div class="info-value"><?= $report['assigned_first_name'] ? $report['assigned_first_name'].' '.$report['assigned_last_name'] : __('reports.not_assigned') ?></div></div>
                    </div>
                </div>

                <?php if ($report['latitude'] && $report['longitude']): ?>
                <div class="mt-3">
                    <div id="reportMap" class="c-show-map"></div>
                    <div class="show-map-coords">
                        <small><?= $report['latitude'] ?>, <?= $report['longitude'] ?></small>
                        <a href="https://www.google.com/maps?q=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>" target="_blank" class="show-map-link"><?= __('reports.google_maps') ?> <i class="fas fa-external-link-alt" style="font-size:0.65rem;"></i></a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ==================== PHOTOS ==================== -->
        <?php if (!empty($images)): ?>
        <div class="card mb-3 animate-fade-in-up show-card">
            <div class="card-header">
                <div class="show-section-header">
                    <div class="show-section-icon" style="background:var(--cyan-surface);color:var(--cyan);"><i class="fas fa-images"></i></div>
                    <h6 class="mb-0"><?= __('reports.photos') ?> (<?= count($images) ?>)</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="show-gallery">
                    <?php foreach ($images as $img):
                        $isVideo = str_starts_with($img['mime_type'] ?? '', 'video/');
                    ?>
                    <div class="show-gallery-item">
                        <?php if ($isVideo): ?>
                        <video controls preload="metadata" style="width:100%;height:100%;object-fit:cover;">
                            <source src="/uploads/reports/<?= htmlspecialchars($img['filename']) ?>" type="<?= htmlspecialchars($img['mime_type']) ?>">
                        </video>
                        <?php else: ?>
                        <a href="/uploads/reports/<?= htmlspecialchars($img['filename']) ?>">
                            <img src="/uploads/reports/<?= htmlspecialchars($img['filename']) ?>" alt="<?= htmlspecialchars($img['original_name']) ?>" loading="lazy">
                        </a>
                        <?php endif; ?>
                        <div class="gallery-overlay">
                            <span><?php if ($isVideo): ?><i class="fas fa-video me-1"></i><?php else: ?><i class="fas fa-expand me-1"></i><?php endif; ?><?= htmlspecialchars($img['original_name']) ?></span>
                        </div>
                        <?php if ($img['is_primary']): ?>
                        <div class="gallery-badge"><i class="fas fa-star me-1"></i><?= __('reports.main_photo') ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ==================== INTERVENTION PHOTOS ==================== -->
        <?php if (!empty($interventionPhotos)): ?>
        <div class="card mb-3 animate-fade-in-up show-card">
            <div class="card-header">
                <div class="show-section-header">
                    <div class="show-section-icon" style="background:var(--green-surface);color:var(--green);"><i class="fas fa-camera-retro"></i></div>
                    <h6 class="mb-0"><?= __('reports.intervention_photos') ?> (<?= count($interventionPhotos) ?>)</h6>
                </div>
            </div>
            <div class="card-body">
                <?php
                $grouped = ['before' => [], 'during' => [], 'after' => []];
                foreach ($interventionPhotos as $ip) { $grouped[$ip['photo_type']][] = $ip; }
                $typeLabels = ['before' => __('interventions.before'), 'during' => __('interventions.during'), 'after' => __('interventions.after')];
                $typeIcons = ['before' => 'fas fa-camera', 'during' => 'fas fa-video', 'after' => 'fas fa-check-double'];

                $hasCitizenPhotos = !empty($images);
                $hasAfterPhotos = !empty($grouped['after']);
                ?>
                <?php if ($hasCitizenPhotos && $hasAfterPhotos): ?>
                <!-- Before/After Comparison: citizen photo = before, intervention after = after -->
                <div class="show-comparison mb-3">
                    <div class="comp-card before">
                        <div class="comp-label"><i class="fas fa-arrow-left me-1"></i><?= $typeLabels['before'] ?></div>
                        <div class="show-gallery">
                            <?php foreach ($images as $img): ?>
                            <div class="show-gallery-item" style="aspect-ratio:1;">
                                <a href="/uploads/reports/<?= htmlspecialchars($img['filename']) ?>">
                                    <img src="/uploads/reports/<?= htmlspecialchars($img['filename']) ?>" alt="" loading="lazy">
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="comp-card after">
                        <div class="comp-label"><i class="fas fa-arrow-right me-1"></i><?= $typeLabels['after'] ?></div>
                        <div class="show-gallery">
                            <?php foreach ($grouped['after'] as $p): ?>
                            <div class="show-gallery-item" style="aspect-ratio:1;">
                                <a href="/uploads/interventions/<?= $p['report_id'] ?>/<?= htmlspecialchars($p['filename']) ?>">
                                    <img src="/uploads/interventions/<?= $p['report_id'] ?>/<?= htmlspecialchars($p['filename']) ?>" alt="" loading="lazy">
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php foreach ($grouped as $type => $photos): ?>
                    <?php if (empty($photos) || $type === 'before' || ($type === 'after' && $hasCitizenPhotos)) continue; ?>
                    <div class="mb-3">
                        <h6 class="show-intervention-section-title"><i class="<?= $typeIcons[$type] ?>" style="color:<?= $type === 'before' ? 'var(--amber)' : ($type === 'after' ? 'var(--green)' : 'var(--accent)') ?>;"></i><?= $typeLabels[$type] ?></h6>
                        <div class="show-gallery">
                            <?php foreach ($photos as $p): ?>
                            <div class="show-gallery-item">
                                <a href="/uploads/interventions/<?= $p['report_id'] ?>/<?= htmlspecialchars($p['filename']) ?>">
                                    <img src="/uploads/interventions/<?= $p['report_id'] ?>/<?= htmlspecialchars($p['filename']) ?>" alt="" loading="lazy">
                                </a>
                                <?php if (!empty($p['caption'])): ?>
                                <div class="gallery-overlay"><span><?= htmlspecialchars($p['caption']) ?></span></div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ==================== COMMENTS ==================== -->
        <div class="card mb-3 animate-fade-in-up show-card">
            <div class="card-header">
                <div class="show-section-header">
                    <div class="show-section-icon" style="background:rgba(124,58,237,0.08);color:var(--purple);"><i class="fas fa-comments"></i></div>
                    <h6 class="mb-0"><?= __('reports.comments') ?> (<?= count($comments) ?>)</h6>
                </div>
            </div>
            <div class="card-body">
                <?php foreach ($comments as $cm): ?>
                <div class="show-comment">
                    <div class="comment-avatar" style="background:var(--accent-surface);color:var(--accent);">
                        <?= strtoupper(mb_substr($cm['first_name'], 0, 1)) ?>
                    </div>
                    <div class="comment-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="comment-author"><?= $cm['first_name'] ?> <?= $cm['last_name'] ?></span>
                            <span class="comment-time"><?= \App\Helpers\Helper::timeAgo($cm['created_at']) ?></span>
                        </div>
                        <div class="comment-text"><?= nl2br(\App\Helpers\Helper::sanitize($cm['comment'])) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($comments)): ?>
                <div class="show-empty-state">
                    <i class="fas fa-comment-slash"></i>
                    <p><?= __('reports.no_comments') ?></p>
                </div>
                <?php endif; ?>
                <form method="POST" action="/reports/<?= $report['id'] ?>/comment" class="mt-3">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <div class="show-comment-form">
                        <div class="comment-avatar" style="background:var(--accent-surface);color:var(--accent);width:34px;height:34px;min-width:34px;">
                            <?= strtoupper(substr(\App\Helpers\Session::getUserName(), 0, 1)) ?>
                        </div>
                        <div class="show-comment-input">
                            <input type="text" class="form-control" name="comment" placeholder="<?= __('reports.add_comment') ?>" required style="border-radius:10px;font-size:0.85rem;">
                            <button class="btn btn-primary show-comment-send" type="submit"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ==================== HISTORY ==================== -->
        <div class="card mb-3 animate-fade-in-up show-card">
            <div class="card-header">
                <div class="show-section-header">
                    <div class="show-section-icon" style="background:var(--amber-surface);color:var(--amber);"><i class="fas fa-history"></i></div>
                    <h6 class="mb-0"><?= __('reports.history') ?></h6>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($history)): ?>
                <div class="show-history">
                    <?php foreach ($history as $i => $h): ?>
                    <?php
                    $actionColors = [
                        'created' => ['#059669', 'fas fa-plus-circle'],
                        'status_change' => ['#6366f1', 'fas fa-exchange-alt'],
                        'assign_agent' => ['#0891b2', 'fas fa-user-check'],
                        'assign_chef_section' => ['#8b5cf6', 'fas fa-user-shield'],
                        'section_validated' => ['#059669', 'fas fa-check-double'],
                        'intervention_completed' => ['#10b981', 'fas fa-clipboard-check'],
                        'intervention_rejected' => ['#dc2626', 'fas fa-times-circle'],
                        'comment' => ['#d97706', 'fas fa-comment'],
                        'photo_upload' => ['#0891b2', 'fas fa-camera'],
                        'redirect' => ['#f59e0b', 'fas fa-share'],
                        'rated' => ['#f59e0b', 'fas fa-star'],
                    ];
                    [$hColor, $hIcon] = $actionColors[$h['action']] ?? ['#6b7280', 'fas fa-circle'];
                    ?>
                    <div class="hist-item">
                        <div class="hist-dot" style="background:<?= $hColor ?>;color:white;">
                            <i class="<?= $hIcon ?>"></i>
                        </div>
                        <div class="hist-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="show-history-action"><?= htmlspecialchars($h['action']) ?></div>
                                    <?php if ($h['first_name']): ?>
                                    <div class="show-history-user"><?= htmlspecialchars($h['first_name']) ?> <?= htmlspecialchars($h['last_name']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <small class="show-history-time"><?= \App\Helpers\Helper::timeAgo($h['created_at']) ?></small>
                            </div>
                            <?php if ($h['old_value'] && $h['new_value']): ?>
                            <div class="show-history-diff">
                                <span class="show-history-diff-old"><?= $h['old_value'] ?></span>
                                <i class="fas fa-arrow-right show-history-diff-arrow"></i>
                                <span class="show-history-diff-new"><?= $h['new_value'] ?></span>
                            </div>
                            <?php elseif ($h['new_value']): ?>
                            <div class="show-history-value"><?= htmlspecialchars(mb_substr($h['new_value'], 0, 120)) ?></div>
                            <?php endif; ?>
                            <?php if ($h['note']): ?>
                            <div class="show-history-note"><?= htmlspecialchars($h['note']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="show-empty-state">
                    <i class="fas fa-clock"></i>
                    <p><?= __('reports.no_history') ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ==================== SIDEBAR ==================== -->
    <div class="col-lg-4">

        <!-- Actions -->
        <?php if ($canAssign || $canChangeStatus || $canRedirect): ?>
        <div class="show-side-card animate-fade-in-up">
            <div class="sc-header">
                <div class="sc-icon" style="background:var(--amber-surface);color:var(--amber);"><i class="fas fa-bolt"></i></div>
                <h6><?= __('reports.actions') ?></h6>
            </div>
            <div class="sc-body">
                <?php if ($canAssign && $report['workflow_step'] < 4): ?>
                <form method="POST" action="/reports/<?= $report['id'] ?>/assign" class="mb-3">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <label class="form-label fw-bold" style="font-size:0.78rem;"><?= __('reports.assign_to') ?></label>
                    <select name="assigned_to" class="form-select form-select-sm mb-2" required>
                        <option value=""><?= __('report_create.select') ?></option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $report['assigned_to'] == $u['id'] ? 'selected' : '' ?>><?= $u['first_name'] ?> <?= $u['last_name'] ?> <small class="text-muted">(<?= $u['role_name'] ?>)</small></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-warning show-action-btn"><i class="fas fa-user-check me-1"></i> <?= __('reports.assign') ?></button>
                </form>
                <?php endif; ?>

                <?php if ($canChangeStatus): ?>
                <?php
                $statusOptions = [
                    'admin_central' => ['submitted','acknowledged','assigned','in_progress','pending_review','pending_unite','validated','resolved','closed','rejected'],
                    'resp_central' => ['acknowledged','assigned','in_progress','resolved','closed'],
                    'admin_local' => ['acknowledged','assigned','in_progress','resolved','closed'],
                    'chef_unite' => ['assigned','in_progress','validated'],
                    'chef_section' => ['assigned','in_progress','pending_unite'],
                    'intervenant' => ['in_progress'],
                ];
                $availableStatuses = $statusOptions[$primaryRole] ?? [];
                if (!empty($availableStatuses)):
                ?>
                <form method="POST" action="/reports/<?= $report['id'] ?>/status" class="mb-3">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <label class="form-label fw-bold" style="font-size:0.78rem;"><?= __('reports.change_status') ?></label>
                    <select name="status" class="form-select form-select-sm mb-2" required>
                        <?php foreach ($availableStatuses as $s): ?>
                        <option value="<?= $s ?>" <?= $report['status'] === $s ? 'selected' : '' ?>><?= __('statuses.' . $s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <textarea class="form-control form-control-sm mb-2" name="resolution_note" placeholder="<?= __('reports.note_optional') ?>" rows="2" style="border-radius:8px;"></textarea>
                    <button type="submit" class="btn btn-sm btn-primary show-action-btn" data-confirm="<?= __('ui.modify_status') ?>"><i class="fas fa-save me-1"></i> <?= __('reports.update') ?></button>
                </form>
                <?php endif; ?>
                <?php endif; ?>

                <?php if ($canRedirect): ?>
                <form method="POST" action="/reports/<?= $report['id'] ?>/redirect" class="mb-3">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <label class="form-label fw-bold" style="font-size:0.78rem;"><i class="fas fa-exchange-alt me-1 text-amber"></i><?= __('reports.redirect') ?></label>
                    <select name="organization_id" class="form-select form-select-sm mb-2" required>
                        <option value=""><?= __('report_create.select') ?></option>
                        <?php foreach ($organizations as $org): ?>
                        <option value="<?= $org['id'] ?>" <?= $report['organization_id'] == $org['id'] ? 'selected' : '' ?>><?= $org['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-warning show-action-btn" data-confirm="<?= __('ui.redirect_report') ?>"><i class="fas fa-exchange-alt me-1"></i> <?= __('reports.redirect') ?></button>
                </form>
                <?php endif; ?>

                <div class="show-action-row">
                    <a href="/reports/<?= $report['id'] ?>/pdf" class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-pdf me-1"></i> PDF</a>
                    <a href="javascript:window.print()" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print me-1"></i> <?= __('reports.print') ?></a>
                </div>

                <!-- Share -->
                <div style="margin-top:10px;">
                    <div class="show-share-row">
                        <a href="/partager/<?= $report['tracking_code'] ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-share-nodes me-1"></i> Partager</a>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="https://wa.me/?text=<?= urlencode($report['title'] . "\n" . ($report['tracking_code'] ?? '') . "\n" . \App\Helpers\Router::baseUrl() . '/partager/' . $report['tracking_code']) ?>" target="_blank" class="show-share-whatsapp"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                        <button type="button" class="show-share-copy" onclick="navigator.clipboard.writeText('<?= \App\Helpers\Router::baseUrl() ?>/partager/<?= $report['tracking_code'] ?>');this.innerHTML='<i class=\'fas fa-check\'></i> Copié';setTimeout(()=>this.innerHTML='<i class=\'fas fa-link\'></i> Lien',1500);"><i class="fas fa-link"></i> Lien</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Contact -->
        <div class="show-side-card animate-fade-in-up">
            <div class="sc-header">
                <div class="sc-icon" style="background:var(--cyan-surface);color:var(--cyan);"><i class="fas fa-address-card"></i></div>
                <h6><?= __('reports.contact') ?></h6>
            </div>
            <div class="sc-body">
                <div class="show-contact-item"><i class="fas fa-user"></i><span><?= $report['citizen_name'] ?? __('reports.anonymous') ?></span></div>
                <div class="show-contact-item"><i class="fas fa-phone"></i><span><?= $report['citizen_phone'] ?: '—' ?></span></div>
                <div class="show-contact-item"><i class="fas fa-envelope"></i><span><?= $report['citizen_email'] ?: '—' ?></span></div>
            </div>
        </div>

        <!-- Rating -->
        <?php if ($rating): ?>
        <div class="show-side-card animate-fade-in-up">
            <div class="sc-header">
                <div class="sc-icon" style="background:rgba(245,158,11,0.08);color:#f59e0b;"><i class="fas fa-star"></i></div>
                <h6><?= __('reports.rating_label') ?></h6>
            </div>
            <div class="sc-body">
                <div class="show-rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star <?= $i <= $rating['rating'] ? 'filled' : 'empty' ?>"></i>
                    <?php endfor; ?>
                    <span class="show-rating-score"><?= $rating['rating'] ?>/5</span>
                </div>
                <?php if ($rating['comment']): ?>
                <p class="show-rating-comment"><?= nl2br(\App\Helpers\Helper::sanitize($rating['comment'])) ?></p>
                <?php endif; ?>
            </div>
        <?php elseif ($canRate): ?>
        <div class="show-side-card animate-fade-in-up">
            <div class="sc-header">
                <div class="sc-icon" style="background:rgba(245,158,11,0.08);color:#f59e0b;"><i class="fas fa-star"></i></div>
                <h6><?= __('reports.rating_title') ?></h6>
            </div>
            <div class="sc-body">
                <form method="POST" action="/reports/<?= $report['id'] ?>/rate">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <div class="star-picker mb-2" id="starRating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star star" data-value="<?= $i ?>" onclick="document.getElementById('ratingInput').value=<?= $i ?>;updateStars(<?= $i ?>)"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" value="5">
                    <textarea name="comment" class="form-control mb-2" rows="2" placeholder="<?= __('reports.rate_placeholder') ?>" style="border-radius:8px;font-size:0.82rem;"></textarea>
                    <button type="submit" class="btn btn-sm btn-warning w-100" style="border-radius:8px;font-weight:700;"><i class="fas fa-paper-plane me-1"></i> <?= __('reports.rate_button') ?></button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Organization -->
        <?php if ($report['org_name']): ?>
        <div class="show-side-card animate-fade-in-up">
            <div class="sc-header">
                <div class="sc-icon" style="background:var(--accent-surface);color:var(--accent);"><i class="fas fa-building"></i></div>
                <h6><?= __('common.organization') ?></h6>
            </div>
            <div class="sc-body">
                <p class="show-org-name"><?= $report['org_name'] ?></p>
                <?php if ($report['org_phone']): ?>
                <div class="show-contact-item"><i class="fas fa-phone"></i><span><?= $report['org_phone'] ?></span></div>
                <?php endif; ?>
                <?php if ($report['org_email']): ?>
                <div class="show-contact-item"><i class="fas fa-envelope"></i><span><?= $report['org_email'] ?></span></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($report['latitude'] && $report['longitude']): ?>
    var theme = document.documentElement.getAttribute('data-bs-theme');
    var tileUrl = theme === 'dark' ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
    var map = L.map('reportMap').setView([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>], 15);
    L.tileLayer(tileUrl, { attribution: '&copy; OSM CARTO' }).addTo(map);
    var marker = L.circleMarker([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>], {
        radius: 10, fillColor: '<?= $catColor ?>', color: '#fff', weight: 3, opacity: 1, fillOpacity: 0.9
    }).addTo(map).bindPopup('<?= $report['tracking_code'] ?>').openPopup();
    <?php endif; ?>
});
function updateStars(n) {
    document.querySelectorAll('#starRating .star').forEach(function(s, i) {
        s.classList.toggle('active', i < n);
    });
}
updateStars(5);
</script>
