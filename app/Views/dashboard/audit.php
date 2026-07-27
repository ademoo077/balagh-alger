<?php $isRtl = \App\Helpers\I18n::isRtl(); ?><?php $pageTitle = 'Journal d\'audit'; ?>
<div class="page-header animate-fade-in-up">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4><i class="fas fa-history me-2 text-accent"></i> <?= __('dashboard.audit_title') ?></h4>
            <small class="text-secondary">Traçabilité complète des actions</small>
        </div>
    </div>
</div>

<div class="card mb-4 animate-fade-in-up">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label" style="font-size:0.78rem;">Utilisateur</label>
                <input type="text" name="user" class="form-control form-control-sm" placeholder="Nom ou email..." value="<?= htmlspecialchars($_GET['user'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:0.78rem;">Action</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">Toutes</option>
                    <option value="status_change" <?= ($_GET['action'] ?? '') === 'status_change' ? 'selected' : '' ?>>Changement statut</option>
                    <option value="create" <?= ($_GET['action'] ?? '') === 'create' ? 'selected' : '' ?>>Création</option>
                    <option value="update" <?= ($_GET['action'] ?? '') === 'update' ? 'selected' : '' ?>>Modification</option>
                    <option value="assign" <?= ($_GET['action'] ?? '') === 'assign' ? 'selected' : '' ?>>Assignation</option>
                    <option value="login" <?= ($_GET['action'] ?? '') === 'login' ? 'selected' : '' ?>>Connexion</option>
                    <option value="delete" <?= ($_GET['action'] ?? '') === 'delete' ? 'selected' : '' ?>>Suppression</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:0.78rem;">Modèle</label>
                <select name="model" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="Report" <?= ($_GET['model'] ?? '') === 'Report' ? 'selected' : '' ?>>Signalement</option>
                    <option value="User" <?= ($_GET['model'] ?? '') === 'User' ? 'selected' : '' ?>>Utilisateur</option>
                    <option value="Intervention" <?= ($_GET['model'] ?? '') === 'Intervention' ? 'selected' : '' ?>>Intervention</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:0.78rem;">Date début</label>
                <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:0.78rem;">Date fin</label>
                <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm animate-fade-in-up">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="background:linear-gradient(135deg,var(--accent),#764ba2);color:#fff;">
                        <th style="border:none;padding:12px 16px;font-size:0.8rem;font-weight:600;">Date</th>
                        <th style="border:none;padding:12px 16px;font-size:0.8rem;font-weight:600;">Utilisateur</th>
                        <th style="border:none;padding:12px 16px;font-size:0.8rem;font-weight:600;">Action</th>
                        <th style="border:none;padding:12px 16px;font-size:0.8rem;font-weight:600;">Modèle</th>
                        <th style="border:none;padding:12px 16px;font-size:0.8rem;font-weight:600;">ID</th>
                        <th style="border:none;padding:12px 16px;font-size:0.8rem;font-weight:600;">Détails</th>
                        <th style="border:none;padding:12px 16px;font-size:0.8rem;font-weight:600;">IP</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $actionColors = [
                    'status_change' => ['#6366f1', 'bg-primary'],
                    'create' => ['#22c55e', 'bg-success'],
                    'update' => ['#f59e0b', 'bg-warning'],
                    'assign' => ['#06b6d4', 'bg-info'],
                    'login' => ['#8b5cf6', 'bg-purple'],
                    'delete' => ['#ef4444', 'bg-danger'],
                ];
                foreach ($logs as $i => $l):
                    $color = $actionColors[$l['action']] ?? ['#6b7280', 'bg-secondary'];
                    $label = match($l['action']) {
                        'status_change' => 'Changement statut',
                        'create' => 'Création',
                        'update' => 'Modification',
                        'assign' => 'Assignation',
                        'login' => 'Connexion',
                        'delete' => 'Suppression',
                        default => $l['action']
                    };
                ?>
                <tr style="animation:fadeIn 0.3s ease <?= $i * 0.03 ?>s both;">
                    <td style="padding:10px 16px;">
                        <div style="font-size:0.82rem;font-weight:500;"><?= date('d/m/Y', strtotime($l['created_at'])) ?></div>
                        <div style="font-size:0.72rem;color:var(--text-muted);"><?= date('H:i:s', strtotime($l['created_at'])) ?></div>
                    </td>
                    <td style="padding:10px 16px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:<?= $color[0] ?>22;color:<?= $color[0] ?>;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:600;flex-shrink:0;">
                                <?= strtoupper(substr($l['first_name'] ?? 'S', 0, 1)) ?>
                            </div>
                            <div>
                                <div style="font-size:0.82rem;font-weight:500;"><?= $l['first_name'] ? $l['first_name'] . ' ' . $l['last_name'] : 'Système' ?></div>
                                <div style="font-size:0.7rem;color:var(--text-muted);"><?= $l['email'] ?? '' ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:10px 16px;">
                        <span style="font-size:0.72rem;padding:3px 10px;border-radius:12px;background:<?= $color[0] ?>22;color:<?= $color[0] ?>;font-weight:600;"><?= $label ?></span>
                    </td>
                    <td style="padding:10px 16px;font-size:0.82rem;"><?= $l['model'] ?></td>
                    <td style="padding:10px 16px;font-size:0.82rem;"><?= $l['model_id'] ?? '-' ?></td>
                    <td style="padding:10px 16px;font-size:0.75rem;color:var(--text-muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= $l['new_value'] ?? '-' ?></td>
                    <td style="padding:10px 16px;"><small class="text-muted" style="font-family:monospace;font-size:0.7rem;"><?= $l['ip_address'] ?></small></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:translateY(0); } }
</style>
