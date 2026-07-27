<?php $pageTitle = __('users.profile'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.profile-hero{position:relative;background:linear-gradient(135deg,#667eea 0%,#764ba2 50%,#f093fb 100%);border-radius:0 0 40px 40px;padding:60px 0 40px;margin:-1.5rem -15px 2rem;text-align:center;overflow:hidden}
.profile-hero::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle at 30% 50%,rgba(255,255,255,0.1) 0%,transparent 50%);animation:profileHeroShine 8s ease-in-out infinite}
@keyframes profileHeroShine{0%,100%{transform:translateX(-20%) rotate(0deg)}50%{transform:translateX(20%) rotate(5deg)}}
.profile-hero-particles{position:absolute;inset:0;overflow:hidden;pointer-events:none}
.profile-hero-particle{position:absolute;width:4px;height:4px;background:rgba(255,255,255,0.3);border-radius:50%;animation:profileParticle 6s ease-in-out infinite}
.profile-hero-particle:nth-child(1){left:10%;top:20%;animation-delay:0s}
.profile-hero-particle:nth-child(2){left:30%;top:60%;animation-delay:1.5s;width:6px;height:6px}
.profile-hero-particle:nth-child(3){left:50%;top:30%;animation-delay:3s}
.profile-hero-particle:nth-child(4){left:70%;top:70%;animation-delay:4.5s;width:5px;height:5px}
.profile-hero-particle:nth-child(5){left:90%;top:40%;animation-delay:2s}
.profile-hero-particle:nth-child(6){left:20%;top:80%;animation-delay:5s;width:3px;height:3px}
.profile-hero-particle:nth-child(7){left:80%;top:15%;animation-delay:1s;width:6px;height:6px}
@keyframes profileParticle{0%,100%{transform:translateY(0) scale(1);opacity:0.3}50%{transform:translateY(-20px) scale(1.5);opacity:0.7}}
.profile-hero-content{position:relative;z-index:2}

.profile-avatar-wrap{position:relative;width:130px;height:130px;margin:0 auto 1.2rem}
.profile-avatar-ring{position:absolute;inset:-4px;border-radius:50%;border:3px solid rgba(255,255,255,0.4);animation:profileRingPulse 3s ease-in-out infinite}
@keyframes profileRingPulse{0%,100%{transform:scale(1);opacity:0.4}50%{transform:scale(1.05);opacity:0.8}}
.profile-avatar-img{width:130px;height:130px;border-radius:50%;object-fit:cover;border:4px solid rgba(255,255,255,0.6);box-shadow:0 8px 32px rgba(0,0,0,0.2);transition:transform 0.3s}
.profile-avatar-img:hover{transform:scale(1.05)}
.profile-avatar-letter{width:130px;height:130px;border-radius:50%;background:linear-gradient(135deg,rgba(255,255,255,0.3),rgba(255,255,255,0.1));backdrop-filter:blur(10px);border:4px solid rgba(255,255,255,0.6);display:flex;align-items:center;justify-content:center;font-size:3.2rem;font-weight:700;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.2);box-shadow:0 8px 32px rgba(0,0,0,0.2);transition:transform 0.3s}
.profile-avatar-letter:hover{transform:scale(1.05)}
.profile-avatar-badge{position:absolute;bottom:4px;right:4px;width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#22c55e,#16a34a);border:3px solid #fff;display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.75rem;box-shadow:0 4px 12px rgba(34,197,94,0.4);cursor:pointer;transition:transform 0.2s}
.profile-avatar-badge:hover{transform:scale(1.15)}

.profile-hero-name{font-size:1.6rem;font-weight:700;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.15);margin-bottom:0.3rem}
.profile-hero-email{font-size:0.85rem;color:rgba(255,255,255,0.85);margin-bottom:1rem}
.profile-hero-role{display:inline-flex;align-items:center;gap:0.4rem;padding:0.35rem 1rem;border-radius:20px;background:rgba(255,255,255,0.2);backdrop-filter:blur(10px);color:#fff;font-size:0.78rem;font-weight:600;border:1px solid rgba(255,255,255,0.3)}

.profile-stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;margin-bottom:2rem}
.profile-stat-card{background:var(--bg-card);border-radius:16px;padding:1.2rem;text-align:center;border:1px solid var(--border);transition:transform 0.2s,box-shadow 0.2s;position:relative;overflow:hidden}
.profile-stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:16px 16px 0 0}
.profile-stat-card:nth-child(1)::before{background:linear-gradient(90deg,#667eea,#764ba2)}
.profile-stat-card:nth-child(2)::before{background:linear-gradient(90deg,#f093fb,#f5576c)}
.profile-stat-card:nth-child(3)::before{background:linear-gradient(90deg,#4facfe,#00f2fe)}
.profile-stat-card:nth-child(4)::before{background:linear-gradient(90deg,#43e97b,#38f9d7)}
.profile-stat-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,0.1)}
.profile-stat-icon{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 0.6rem;font-size:1rem}
.profile-stat-card:nth-child(1) .profile-stat-icon{background:linear-gradient(135deg,#667eea22,#764ba222);color:#667eea}
.profile-stat-card:nth-child(2) .profile-stat-icon{background:linear-gradient(135deg,#f093fb22,#f5576c22);color:#f5576c}
.profile-stat-card:nth-child(3) .profile-stat-icon{background:linear-gradient(135deg,#4facfe22,#00f2fe22);color:#4facfe}
.profile-stat-card:nth-child(4) .profile-stat-icon{background:linear-gradient(135deg,#43e97b22,#38f9d722);color:#43e97b}
.profile-stat-value{font-size:1.4rem;font-weight:700;color:var(--text-primary);line-height:1}
.profile-stat-label{font-size:0.72rem;color:var(--text-muted);margin-top:0.3rem;text-transform:uppercase;letter-spacing:0.5px}

.profile-glass-card{background:var(--bg-card);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s}
.profile-glass-card:hover{box-shadow:0 8px 32px rgba(0,0,0,0.08)}
.profile-card-header{padding:1.2rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:0.6rem}
.profile-card-header h6{margin:0;font-weight:600;color:var(--text-primary)}
.profile-card-header i{color:var(--accent)}
.profile-card-body{padding:1.5rem}

.profile-form-group{margin-bottom:1.2rem}
.profile-form-group label{display:block;font-weight:600;font-size:0.82rem;color:var(--text-secondary);margin-bottom:0.4rem;text-transform:uppercase;letter-spacing:0.3px}
.profile-form-input{width:100%;padding:0.7rem 1rem;border:2px solid var(--border);border-radius:12px;background:var(--bg-elevated);color:var(--text-primary);font-size:0.9rem;transition:border-color 0.2s,box-shadow 0.2s;outline:none}
.profile-form-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.profile-form-input:disabled{opacity:0.6;cursor:not-allowed;background:var(--bg-elevated)}
.profile-form-hint{font-size:0.72rem;color:var(--text-muted);margin-top:0.3rem}

.profile-avatar-upload-zone{position:relative;border:2px dashed var(--border);border-radius:16px;padding:1.5rem;text-align:center;cursor:pointer;transition:border-color 0.2s,background 0.2s}
.profile-avatar-upload-zone:hover{border-color:var(--accent);background:var(--accent-surface)}
.profile-avatar-upload-zone.dragover{border-color:var(--accent);background:var(--accent-glow);transform:scale(1.01)}
.profile-avatar-upload-icon{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#764ba2);display:flex;align-items:center;justify-content:center;margin:0 auto 0.6rem;color:#fff;font-size:1.1rem}
.profile-avatar-upload-text{font-size:0.85rem;color:var(--text-secondary);margin-bottom:0.3rem}
.profile-avatar-upload-text strong{color:var(--accent)}
.profile-avatar-upload-hint{font-size:0.72rem;color:var(--text-muted)}
.profile-avatar-upload-preview{margin-top:0.8rem;display:none}
.profile-avatar-upload-preview img{width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid var(--accent);box-shadow:0 4px 12px rgba(0,0,0,0.1)}
.profile-avatar-upload-name{font-size:0.78rem;color:var(--text-secondary);margin-top:0.4rem;word-break:break-all}
.profile-avatar-upload-remove{display:inline-flex;align-items:center;gap:0.3rem;margin-top:0.4rem;padding:0.2rem 0.6rem;border-radius:8px;background:#ef444422;color:#ef4444;border:none;font-size:0.72rem;cursor:pointer;transition:background 0.2s}
.profile-avatar-upload-remove:hover{background:#ef444433}

.profile-pw-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:0.85rem;padding:4px}
.profile-pw-toggle:hover{color:var(--text-primary)}

.profile-pw-strength{margin-top:0.5rem}
.profile-pw-strength-bar{height:4px;border-radius:2px;background:var(--bg-elevated);overflow:hidden}
.profile-pw-strength-fill{height:100%;width:0;border-radius:2px;transition:width 0.3s,background 0.3s}
.profile-pw-strength-label{font-size:0.72rem;margin-top:0.25rem;font-weight:600}

.profile-save-btn{display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 2rem;border:none;border-radius:12px;background:linear-gradient(135deg,var(--accent),#764ba2);color:#fff;font-weight:600;font-size:0.9rem;cursor:pointer;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 4px 16px rgba(102,126,234,0.3)}
.profile-save-btn:hover{transform:translateY(-2px);box-shadow:0 6px 24px rgba(102,126,234,0.4)}
.profile-save-btn:active{transform:translateY(0)}

.profile-info-item{display:flex;align-items:center;gap:0.8rem;padding:0.8rem 1rem;border-radius:12px;transition:background 0.2s;margin-bottom:0.4rem}
.profile-info-item:hover{background:var(--bg-elevated)}
.profile-info-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.8rem;flex-shrink:0}
.profile-info-icon.email{background:linear-gradient(135deg,#667eea22,#764ba222);color:#667eea}
.profile-info-icon.phone{background:linear-gradient(135deg,#4facfe22,#00f2fe22);color:#4facfe}
.profile-info-icon.calendar{background:linear-gradient(135deg,#f093fb22,#f5576c22);color:#f5576c}
.profile-info-icon.role{background:linear-gradient(135deg,#43e97b22,#38f9d722);color:#43e97b}
.profile-info-text{flex:1;min-width:0}
.profile-info-label{font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.1rem}
.profile-info-value{font-size:0.85rem;color:var(--text-primary);font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

.profile-alert{padding:0.8rem 1.2rem;border-radius:12px;display:flex;align-items:center;gap:0.6rem;font-size:0.85rem;margin-bottom:1rem;animation:profileAlertIn 0.4s ease-out}
.profile-alert-success{background:linear-gradient(135deg,#43e97b15,#38f9d715);border:1px solid #43e97b33;color:#16a34a}
.profile-alert-error{background:linear-gradient(135deg,#f5576c15,#f093fb15);border:1px solid #f5576c33;color:#ef4444}
@keyframes profileAlertIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}

.profile-section-title{font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);font-weight:600}

@media(max-width:768px){
    .profile-hero{border-radius:0 0 24px 24px;padding:40px 0 30px}
    .profile-hero-name{font-size:1.3rem}
    .profile-stats-row{grid-template-columns:repeat(2,1fr);gap:0.8rem}
    .profile-avatar-wrap{width:100px;height:100px}
    .profile-avatar-img,.profile-avatar-letter{width:100px;height:100px;font-size:2.4rem}
    .profile-avatar-ring{inset:-3px}
}
</style>

<div class="profile-hero">
    <div class="profile-hero-particles">
        <div class="profile-hero-particle"></div>
        <div class="profile-hero-particle"></div>
        <div class="profile-hero-particle"></div>
        <div class="profile-hero-particle"></div>
        <div class="profile-hero-particle"></div>
        <div class="profile-hero-particle"></div>
        <div class="profile-hero-particle"></div>
    </div>
    <div class="profile-hero-content">
        <div class="profile-avatar-wrap">
            <div class="profile-avatar-ring"></div>
            <?php
            $hasAvatar = !empty($user['avatar']) && $user['avatar'] !== 'default.png';
            $avatarInitial = strtoupper(substr($user['first_name'], 0, 1));
            ?>
            <?php if ($hasAvatar): ?>
            <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="profile-avatar-img" id="heroAvatar">
            <?php else: ?>
            <div class="profile-avatar-letter" id="heroAvatar"><?= $avatarInitial ?></div>
            <?php endif; ?>
            <label class="profile-avatar-badge" for="avatarInput" title="Changer la photo">
                <i class="fas fa-camera"></i>
            </label>
        </div>
        <div class="profile-hero-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
        <div class="profile-hero-email"><?= htmlspecialchars($user['email']) ?></div>
        <?php $primaryRole = \App\Helpers\Rbac::getPrimaryRole(); ?>
        <?php if ($primaryRole): ?>
        <div class="profile-hero-role">
            <i class="fas fa-shield-halved"></i>
            <?= ucfirst(str_replace('_', ' ', $primaryRole)) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($flash = \App\Helpers\Session::getFlash('success')): ?>
<div class="profile-alert profile-alert-success"><i class="fas fa-check-circle"></i> <?= $flash ?></div>
<?php endif; ?>
<?php if ($flash = \App\Helpers\Session::getFlash('error')): ?>
<div class="profile-alert profile-alert-error"><i class="fas fa-exclamation-circle"></i> <?= $flash ?></div>
<?php endif; ?>

<div class="profile-stats-row">
    <?php
    $db = \App\Helpers\Database::getConnection();
    $uid = \App\Helpers\Session::getUserId();
    $reportCount = $db->prepare("SELECT COUNT(*) FROM reports WHERE citizen_id = ? AND deleted_at IS NULL");
    $reportCount->execute([$uid]);
    $reportCount = $reportCount->fetchColumn();
    $notifCount = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $notifCount->execute([$uid]);
    $notifCount = $notifCount->fetchColumn();
    $roleObj = $db->prepare("SELECT r.name FROM roles r JOIN user_roles ur ON ur.role_id=r.id WHERE ur.user_id=? ORDER BY r.level DESC LIMIT 1");
    $roleObj->execute([$uid]);
    $roleName = $roleObj->fetchColumn() ?: ucfirst(str_replace('_', ' ', $primaryRole ?? 'citizen'));
    ?>
    <div class="profile-stat-card">
        <div class="profile-stat-icon"><i class="fas fa-flag"></i></div>
        <div class="profile-stat-value"><?= $reportCount ?></div>
        <div class="profile-stat-label"><?= __('stats.reports') ?></div>
    </div>
    <div class="profile-stat-card">
        <div class="profile-stat-icon"><i class="fas fa-bell"></i></div>
        <div class="profile-stat-value"><?= $notifCount ?></div>
        <div class="profile-stat-label">Non lues</div>
    </div>
    <div class="profile-stat-card">
        <div class="profile-stat-icon"><i class="fas fa-shield-halved"></i></div>
        <div class="profile-stat-value" style="font-size:0.85rem;"><?= ucfirst(str_replace('_', ' ', $roleName)) ?></div>
        <div class="profile-stat-label"><?= __('users.role') ?></div>
    </div>
    <div class="profile-stat-card">
        <div class="profile-stat-icon"><i class="fas fa-clock"></i></div>
        <div class="profile-stat-value" style="font-size:0.85rem;"><?= \App\Helpers\I18n::formatDate($user['created_at']) ?></div>
        <div class="profile-stat-label">Inscrit le</div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="profile-glass-card">
            <div class="profile-card-header">
                <i class="fas fa-pen-to-square"></i>
                <h6><?= __('users.edit_profile') ?></h6>
            </div>
            <div class="profile-card-body">
                <form method="POST" action="/profile" enctype="multipart/form-data" id="profileForm">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">

                    <div class="profile-section-title">Informations personnelles</div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="profile-form-group">
                                <label><?= __('users.first_name') ?></label>
                                <input type="text" class="profile-form-input" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-form-group">
                                <label><?= __('users.last_name') ?></label>
                                <input type="text" class="profile-form-input" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-form-group">
                                <label><?= __('users.phone') ?></label>
                                <input type="text" class="profile-form-input" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="0555 55 55 55">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-form-group">
                                <label><?= __('users.email') ?></label>
                                <input type="email" class="profile-form-input" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                <div class="profile-form-hint"><i class="fas fa-lock me-1"></i><?= __('users.email_readonly') ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section-title" style="margin-top:1.5rem;">Photo de profil</div>

                    <div class="profile-form-group">
                        <div class="profile-avatar-upload-zone" id="avatarDropZone">
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp" id="avatarInput" style="display:none">
                            <div class="profile-avatar-upload-icon"><i class="fas fa-cloud-arrow-up"></i></div>
                            <div class="profile-avatar-upload-text">Glissez votre photo ici ou <strong>parcourir</strong></div>
                            <div class="profile-avatar-upload-hint">JPEG, PNG, GIF ou WebP — max 5 Mo</div>
                            <div class="profile-avatar-upload-preview" id="avatarPreviewWrap">
                                <img src="" alt="Aperçu" id="avatarPreviewImg">
                                <div class="profile-avatar-upload-name" id="avatarFileName"></div>
                                <button type="button" class="profile-avatar-upload-remove" id="avatarRemove">
                                    <i class="fas fa-times"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section-title" style="margin-top:1.5rem;">Sécurité</div>

                    <div class="profile-form-group" style="position:relative;">
                        <label><?= __('users.new_password') ?></label>
                        <input type="password" class="profile-form-input" name="password" placeholder="<?= __('users.password_placeholder') ?>" minlength="6" id="passwordInput" style="padding-right:40px;">
                        <button type="button" class="profile-pw-toggle" onclick="togglePw()" tabindex="-1"><i class="fas fa-eye" id="pwEye"></i></button>
                        <div class="profile-pw-strength" id="passwordStrength" style="display:none;">
                            <div class="profile-pw-strength-bar"><div class="profile-pw-strength-fill" id="pwBar"></div></div>
                            <div class="profile-pw-strength-label" id="pwLabel"></div>
                        </div>
                        <div class="profile-form-hint"><?= __('users.password_help') ?></div>
                    </div>

                    <div style="margin-top:1.8rem;">
                        <button type="submit" class="profile-save-btn">
                            <i class="fas fa-check"></i> <?= __('common.save') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="profile-glass-card" style="margin-bottom:1.2rem;">
            <div class="profile-card-header">
                <i class="fas fa-circle-info"></i>
                <h6><?= __('users.info') ?></h6>
            </div>
            <div class="profile-card-body" style="padding:1rem;">
                <div class="profile-info-item">
                    <div class="profile-info-icon email"><i class="fas fa-envelope"></i></div>
                    <div class="profile-info-text">
                        <div class="profile-info-label">Email</div>
                        <div class="profile-info-value"><?= htmlspecialchars($user['email']) ?></div>
                    </div>
                </div>
                <div class="profile-info-item">
                    <div class="profile-info-icon phone"><i class="fas fa-phone"></i></div>
                    <div class="profile-info-text">
                        <div class="profile-info-label">Téléphone</div>
                        <div class="profile-info-value"><?= htmlspecialchars($user['phone'] ?: '—') ?></div>
                    </div>
                </div>
                <div class="profile-info-item">
                    <div class="profile-info-icon calendar"><i class="fas fa-calendar-check"></i></div>
                    <div class="profile-info-text">
                        <div class="profile-info-label">Membre depuis</div>
                        <div class="profile-info-value"><?= \App\Helpers\I18n::formatDate($user['created_at']) ?></div>
                    </div>
                </div>
                <div class="profile-info-item">
                    <div class="profile-info-icon role"><i class="fas fa-shield-halved"></i></div>
                    <div class="profile-info-text">
                        <div class="profile-info-label">Rôle principal</div>
                        <div class="profile-info-value"><?= ucfirst(str_replace('_', ' ', $primaryRole ?? 'citizen')) ?></div>
                    </div>
                </div>
                <?php if (!empty($user['organization_id'])): ?>
                <?php
                $orgQ = $db->prepare("SELECT name FROM organizations WHERE id = ?");
                $orgQ->execute([$user['organization_id']]);
                $orgName = $orgQ->fetchColumn();
                ?>
                <?php if ($orgName): ?>
                <div class="profile-info-item">
                    <div class="profile-info-icon" style="background:linear-gradient(135deg,#f59e0b22,#f9731622);color:#f59e0b;"><i class="fas fa-building"></i></div>
                    <div class="profile-info-text">
                        <div class="profile-info-label">Organisation</div>
                        <div class="profile-info-value"><?= htmlspecialchars($orgName) ?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
                <?php if (!empty($user['daira_id'])): ?>
                <?php
                $dairaQ = $db->prepare("SELECT name FROM dairas WHERE id = ?");
                $dairaQ->execute([$user['daira_id']]);
                $dairaName = $dairaQ->fetchColumn();
                ?>
                <?php if ($dairaName): ?>
                <div class="profile-info-item">
                    <div class="profile-info-icon" style="background:linear-gradient(135deg,#8b5cf622,#a78bfa22);color:#8b5cf6;"><i class="fas fa-map-location-dot"></i></div>
                    <div class="profile-info-text">
                        <div class="profile-info-label">Daira</div>
                        <div class="profile-info-value"><?= htmlspecialchars($dairaName) ?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-glass-card">
            <div class="profile-card-header">
                <i class="fas fa-key"></i>
                <h6>Actions rapides</h6>
            </div>
            <div class="profile-card-body" style="padding:1rem;">
                <a href="/notifications" class="profile-info-item" style="text-decoration:none;color:inherit;cursor:pointer;">
                    <div class="profile-info-icon" style="background:linear-gradient(135deg,#f59e0b22,#f9731622);color:#f59e0b;"><i class="fas fa-bell"></i></div>
                    <div class="profile-info-text">
                        <div class="profile-info-label">Notifications</div>
                        <div class="profile-info-value" style="color:var(--accent);"><?= $notifCount > 0 ? $notifCount . ' non lues' : 'Tout est lu' ?></div>
                    </div>
                </a>
                <a href="/reports" class="profile-info-item" style="text-decoration:none;color:inherit;cursor:pointer;">
                    <div class="profile-info-icon" style="background:linear-gradient(135deg,#667eea22,#764ba222);color:#667eea;"><i class="fas fa-list-check"></i></div>
                    <div class="profile-info-text">
                        <div class="profile-info-label">Mes signalements</div>
                        <div class="profile-info-value" style="color:var(--accent);">Voir tous →</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Virtual ID Card -->
<style>
.idcard-section{margin-top:2.5rem}
.idcard-section-title{font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:1.2rem;font-weight:600;display:flex;align-items:center;gap:0.5rem}
.idcard-section-title i{font-size:0.85rem;color:var(--accent)}
.idcard-wrapper{perspective:1200px;margin:0 auto;max-width:580px}
.idcard{position:relative;width:100%;aspect-ratio:1.586/1;border-radius:14px;overflow:hidden;box-shadow:0 12px 48px rgba(0,0,0,0.18),0 2px 8px rgba(0,0,0,0.08);transition:transform 0.4s;transform-style:preserve-3d}
.idcard:hover{transform:rotateY(3deg) rotateX(1deg) scale(1.01)}
.idcard-front,.idcard-back{position:absolute;inset:0;backface-visibility:hidden;border-radius:14px}
.idcard-front{background:linear-gradient(165deg,#1e3a5f 0%,#0f2744 40%,#1a2f4a 100%);z-index:2}
.idcard-back{background:linear-gradient(165deg,#0f2744 0%,#1e3a5f 50%,#0d1f38 100%);transform:rotateY(180deg);z-index:1}
.idcard-holo{position:absolute;top:0;right:0;width:120px;height:120px;background:radial-gradient(circle at 70% 30%,rgba(255,215,0,0.15),rgba(255,215,0,0.05) 40%,transparent 70%);pointer-events:none;z-index:10}
.idcard-holo::after{content:'';position:absolute;inset:0;background:repeating-linear-gradient(135deg,transparent,transparent 2px,rgba(255,255,255,0.02) 2px,rgba(255,255,255,0.02) 4px);z-index:1}
.idcard-stripe{position:absolute;bottom:0;left:0;right:0;height:6px;background:linear-gradient(90deg,#c0392b,#e74c3c,#f39c12,#27ae60,#2980b9,#8e44ad);z-index:10}
.idcard-header{display:flex;align-items:center;gap:0.8rem;padding:0.8rem 1rem;border-bottom:1px solid rgba(255,255,255,0.08);position:relative;z-index:5}
.idcard-logo{width:48px;height:48px;border-radius:50%;object-fit:contain;background:rgba(255,255,255,0.1);padding:3px;border:1.5px solid rgba(255,255,255,0.15)}
.idcard-header-text{flex:1}
.idcard-header-title{font-size:0.85rem;font-weight:700;color:#fff;letter-spacing:0.5px;text-transform:uppercase}
.idcard-header-sub{font-size:0.62rem;color:rgba(255,255,255,0.55);letter-spacing:1px;text-transform:uppercase}
.idcard-body{display:flex;gap:1rem;padding:0.8rem 1rem;position:relative;z-index:5}
.idcard-photo-wrap{flex-shrink:0;width:90px;height:110px;border-radius:8px;overflow:hidden;border:2px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center}
.idcard-photo{width:100%;height:100%;object-fit:cover}
.idcard-photo-letter{font-size:2.2rem;font-weight:700;color:rgba(255,255,255,0.6)}
.idcard-info{flex:1;display:flex;flex-direction:column;justify-content:center;gap:0.25rem;min-width:0}
.idcard-name{font-size:1.05rem;font-weight:700;color:#fff;letter-spacing:0.3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.idcard-email{font-size:0.68rem;color:rgba(255,255,255,0.5);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.idcard-details{display:flex;flex-direction:column;gap:0.2rem;margin-top:0.3rem}
.idcard-detail{display:flex;align-items:center;gap:0.4rem;font-size:0.65rem;color:rgba(255,255,255,0.6)}
.idcard-detail i{width:12px;font-size:0.6rem;color:rgba(255,255,255,0.35)}
.idcard-detail strong{color:rgba(255,255,255,0.85);font-weight:600}
.idcard-role-badge{display:inline-flex;align-items:center;gap:0.25rem;padding:0.15rem 0.5rem;border-radius:4px;background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.75);font-size:0.6rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-top:0.3rem;width:fit-content;border:1px solid rgba(255,255,255,0.08)}
.idcard-qr{position:absolute;bottom:0.8rem;right:1rem;z-index:5}
.idcard-qr canvas{border-radius:4px;background:#fff;padding:3px}
.idcard-qr-label{font-size:0.5rem;color:rgba(255,255,255,0.35);text-align:center;margin-top:2px;letter-spacing:0.5px}
.idcard-serial{position:absolute;bottom:0.8rem;left:1rem;font-size:0.55rem;color:rgba(255,255,255,0.25);letter-spacing:1.5px;font-family:'Courier New',monospace;z-index:5}
.idcard-micro{position:absolute;top:0;left:0;right:0;height:40px;background:repeating-linear-gradient(0deg,transparent,transparent 1px,rgba(255,255,255,0.015) 1px,rgba(255,255,255,0.015) 2px);z-index:1;pointer-events:none}

@media print{
    body *{visibility:hidden!important}
    .idcard-wrapper,.idcard-wrapper *{visibility:visible!important}
    .idcard-wrapper{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%)!important;margin:0!important}
    .idcard{box-shadow:none!important;border:2px solid #333!important}
}
@media(max-width:576px){
    .idcard{border-radius:10px}
    .idcard-header{padding:0.6rem 0.8rem}
    .idcard-logo{width:38px;height:38px}
    .idcard-header-title{font-size:0.75rem}
    .idcard-body{padding:0.6rem 0.8rem;gap:0.7rem}
    .idcard-photo-wrap{width:72px;height:90px}
    .idcard-name{font-size:0.9rem}
    .idcard-qr{bottom:0.5rem;right:0.7rem}
    .idcard-qr canvas{width:55px!important;height:55px!important}
}
</style>

<div class="idcard-section">
    <div class="idcard-section-title"><i class="fas fa-id-card"></i> Carte d'identité virtuelle</div>
    <div class="idcard-wrapper">
        <div class="idcard" id="idCard">
            <div class="idcard-front">
                <div class="idcard-micro"></div>
                <div class="idcard-holo"></div>
                <div class="idcard-stripe"></div>

                <div class="idcard-header">
                    <img src="/assets/img/wilaya-logo.png" alt="Wilaya" class="idcard-logo">
                    <div class="idcard-header-text">
                        <div class="idcard-header-title">République Algérienne Démocratique et Populaire</div>
                        <div class="idcard-header-sub">Wilaya d'Alger — Balagh Alger</div>
                    </div>
                </div>

                <div class="idcard-body">
                    <div class="idcard-photo-wrap">
                        <?php $hasAvatar = !empty($user['avatar']) && $user['avatar'] !== 'default.png'; ?>
                        <?php if ($hasAvatar): ?>
                        <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Photo" class="idcard-photo" id="idCardPhoto">
                        <?php else: ?>
                        <div class="idcard-photo-letter" id="idCardPhoto"><?= strtoupper(substr($user['first_name'], 0, 1)) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="idcard-info">
                        <div class="idcard-name" id="idCardName"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                        <div class="idcard-email" id="idCardEmail"><?= htmlspecialchars($user['email']) ?></div>
                        <div class="idcard-details">
                            <div class="idcard-detail"><i class="fas fa-shield-halved"></i> <strong id="idCardRole"><?= ucfirst(str_replace('_', ' ', $primaryRole ?? 'citizen')) ?></strong></div>
                            <?php if (!empty($user['phone'])): ?>
                            <div class="idcard-detail"><i class="fas fa-phone"></i> <?= htmlspecialchars($user['phone']) ?></div>
                            <?php endif; ?>
                            <div class="idcard-detail"><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($user['created_at'])) ?></div>
                        </div>
                        <div class="idcard-role-badge"><i class="fas fa-id-badge"></i> Badge N° <?= str_pad($user['id'], 6, '0', STR_PAD_LEFT) ?></div>
                    </div>
                </div>

                <div class="idcard-qr">
                    <div id="idCardQr"></div>
                    <div class="idcard-qr-label">IDENTIFIANT</div>
                </div>
                <div class="idcard-serial">BA-<?= str_pad($user['id'], 6, '0', STR_PAD_LEFT) ?>-<?= date('Y') ?></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var qrContainer = document.getElementById('idCardQr');
    if (qrContainer) {
        var profileUrl = window.location.origin + '/profile';
        new QRCode(qrContainer, {
            text: profileUrl,
            width: 64,
            height: 64,
            colorDark: '#ffffff',
            colorLight: '#1e3a5f',
            correctLevel: QRCode.CorrectLevel.M
        });
    }

    var avatarInput = document.getElementById('avatarInput');
    var dropZone = document.getElementById('avatarDropZone');
    var previewWrap = document.getElementById('avatarPreviewWrap');
    var previewImg = document.getElementById('avatarPreviewImg');
    var fileName = document.getElementById('avatarFileName');
    var removeBtn = document.getElementById('avatarRemove');
    var heroAvatar = document.getElementById('heroAvatar');

    if (dropZone) {
        dropZone.addEventListener('click', function(e) {
            if (e.target === removeBtn || e.target.closest('.profile-avatar-upload-remove')) return;
            avatarInput.click();
        });
        dropZone.addEventListener('dragover', function(e) { e.preventDefault(); dropZone.classList.add('dragover'); });
        dropZone.addEventListener('dragleave', function() { dropZone.classList.remove('dragover'); });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                avatarInput.files = e.dataTransfer.files;
                handleAvatarFile(e.dataTransfer.files[0]);
            }
        });
    }

    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            if (this.files[0]) handleAvatarFile(this.files[0]);
        });
    }

    function handleAvatarFile(file) {
        if (!file.type.startsWith('image/')) return;
        if (file.size > 5 * 1024 * 1024) {
            alert('La photo ne doit pas dépasser 5 Mo.');
            return;
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewWrap.style.display = 'block';
            fileName.textContent = file.name;
            if (heroAvatar.tagName === 'IMG') {
                heroAvatar.src = e.target.result;
            } else {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Avatar';
                img.id = 'heroAvatar';
                img.className = 'profile-avatar-img';
                heroAvatar.parentNode.replaceChild(img, heroAvatar);
            }
        };
        reader.readAsDataURL(file);
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            avatarInput.value = '';
            previewWrap.style.display = 'none';
            previewImg.src = '';
            fileName.textContent = '';
        });
    }

    window.togglePw = function() {
        var inp = document.getElementById('passwordInput');
        var eye = document.getElementById('pwEye');
        if (inp.type === 'password') { inp.type = 'text'; eye.className = 'fas fa-eye-slash'; }
        else { inp.type = 'password'; eye.className = 'fas fa-eye'; }
    };

    var pwInput = document.getElementById('passwordInput');
    var pwStrength = document.getElementById('passwordStrength');
    var pwBar = document.getElementById('pwBar');
    var pwLabel = document.getElementById('pwLabel');
    if (pwInput && pwBar) {
        pwInput.addEventListener('input', function() {
            var v = this.value;
            if (!v) { pwStrength.style.display = 'none'; return; }
            pwStrength.style.display = 'block';
            var score = 0;
            if (v.length >= 6) score++;
            if (v.length >= 10) score++;
            if (/[A-Z]/.test(v)) score++;
            if (/[0-9]/.test(v)) score++;
            if (/[^A-Za-z0-9]/.test(v)) score++;
            var levels = [
                { w: '20%', bg: '#ef4444', label: 'Très faible', color: '#ef4444' },
                { w: '40%', bg: '#f97316', label: 'Faible', color: '#f97316' },
                { w: '60%', bg: '#eab308', label: 'Moyen', color: '#eab308' },
                { w: '80%', bg: '#22c55e', label: 'Fort', color: '#22c55e' },
                { w: '100%', bg: '#059669', label: 'Très fort', color: '#059669' }
            ];
            var l = levels[Math.min(score, 4)];
            pwBar.style.width = l.w;
            pwBar.style.background = l.bg;
            pwLabel.textContent = l.label;
            pwLabel.style.color = l.color;
        });
    }
});
</script>
