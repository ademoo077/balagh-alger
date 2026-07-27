<?php $pageTitle = __('users.edit_title'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.usr-form-hero{position:relative;background:linear-gradient(135deg,#f59e0b 0%,#f97316 50%,#ef4444 100%);border-radius:0 0 32px 32px;padding:40px 0 30px;margin:-1.5rem -15px 2rem;text-align:center;overflow:hidden}
.usr-form-hero::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle at 30% 50%,rgba(255,255,255,0.08) 0%,transparent 50%);animation:usrFormShine 10s ease-in-out infinite}
@keyframes usrFormShine{0%,100%{transform:translateX(-20%) rotate(0deg)}50%{transform:translateX(20%) rotate(3deg)}}
.usr-form-hero-particles{position:absolute;inset:0;overflow:hidden;pointer-events:none}
.usr-form-hero-particle{position:absolute;width:4px;height:4px;background:rgba(255,255,255,0.25);border-radius:50%;animation:usrFormParticle 7s ease-in-out infinite}
.usr-form-hero-particle:nth-child(1){left:10%;top:20%;animation-delay:0s}
.usr-form-hero-particle:nth-child(2){left:35%;top:60%;animation-delay:2s;width:5px;height:5px}
.usr-form-hero-particle:nth-child(3){left:60%;top:25%;animation-delay:3.5s}
.usr-form-hero-particle:nth-child(4){left:85%;top:55%;animation-delay:1.5s;width:6px;height:6px}
@keyframes usrFormParticle{0%,100%{transform:translateY(0) scale(1);opacity:0.25}50%{transform:translateY(-18px) scale(1.4);opacity:0.6}}
.usr-form-hero-content{position:relative;z-index:2}
.usr-form-hero-avatar{position:relative;width:100px;height:100px;margin:0 auto 1rem}
.usr-form-hero-letter{width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.2);backdrop-filter:blur(10px);border:3px solid rgba(255,255,255,0.4);display:flex;align-items:center;justify-content:center;font-size:2.4rem;font-weight:700;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.15);box-shadow:0 8px 32px rgba(0,0,0,0.15)}
.usr-form-hero-ring{position:absolute;inset:-4px;border-radius:50%;border:2px solid rgba(255,255,255,0.3);animation:usrFormRing 3s ease-in-out infinite}
@keyframes usrFormRing{0%,100%{transform:scale(1);opacity:0.3}50%{transform:scale(1.04);opacity:0.7}}
.usr-form-hero-title{font-size:1.6rem;font-weight:700;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:0.3rem}
.usr-form-hero-sub{font-size:0.85rem;color:rgba(255,255,255,0.85)}

.usr-form-glass{background:var(--bg-card);border:1px solid var(--border);border-radius:20px;overflow:hidden;transition:box-shadow 0.2s}
.usr-form-glass:hover{box-shadow:0 8px 32px rgba(0,0,0,0.06)}
.usr-form-header{padding:1.2rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.usr-form-header-left{display:flex;align-items:center;gap:0.6rem}
.usr-form-header-left h6{margin:0;font-weight:600;color:var(--text-primary)}
.usr-form-header-left i{color:var(--accent)}
.usr-form-body{padding:1.5rem}

.usr-form-section{font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);font-weight:600;display:flex;align-items:center;gap:0.5rem}
.usr-form-section i{font-size:0.8rem;color:var(--accent)}

.usr-form-group{margin-bottom:1.2rem}
.usr-form-group label{display:block;font-weight:600;font-size:0.82rem;color:var(--text-secondary);margin-bottom:0.4rem;text-transform:uppercase;letter-spacing:0.3px}
.usr-form-group label .required{color:#ef4444;margin-left:2px}
.usr-form-input{width:100%;padding:0.7rem 1rem;border:2px solid var(--border);border-radius:12px;background:var(--bg-elevated);color:var(--text-primary);font-size:0.9rem;transition:border-color 0.2s,box-shadow 0.2s;outline:none}
.usr-form-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.usr-form-input::placeholder{color:var(--text-muted)}
.usr-form-select{width:100%;padding:0.7rem 1rem;border:2px solid var(--border);border-radius:12px;background:var(--bg-elevated);color:var(--text-primary);font-size:0.9rem;transition:border-color 0.2s,box-shadow 0.2s;outline:none;cursor:pointer;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:2.5rem}
.usr-form-select:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.usr-form-hint{font-size:0.72rem;color:var(--text-muted);margin-top:0.3rem}

.usr-form-actions{display:flex;gap:0.8rem;margin-top:1.8rem}
.usr-form-save{display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 2rem;border:none;border-radius:12px;background:linear-gradient(135deg,var(--accent),#764ba2);color:#fff;font-weight:600;font-size:0.9rem;cursor:pointer;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 4px 16px rgba(99,102,241,0.3)}
.usr-form-save:hover{transform:translateY(-2px);box-shadow:0 6px 24px rgba(99,102,241,0.4)}
.usr-form-cancel{display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 1.5rem;border:2px solid var(--border);border-radius:12px;background:transparent;color:var(--text-secondary);font-weight:600;font-size:0.9rem;cursor:pointer;transition:border-color 0.2s,color 0.2s;text-decoration:none}
.usr-form-cancel:hover{border-color:var(--accent);color:var(--accent)}
.usr-form-danger{display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 1.5rem;border:2px solid #ef444433;border-radius:12px;background:#ef444408;color:#ef4444;font-weight:600;font-size:0.9rem;cursor:pointer;transition:all 0.2s;text-decoration:none}
.usr-form-danger:hover{background:#ef444415;border-color:#ef4444}

@media(max-width:768px){
    .usr-form-hero{border-radius:0 0 24px 24px;padding:30px 0 24px}
    .usr-form-hero-title{font-size:1.3rem}
}
</style>

<div class="usr-form-hero">
    <div class="usr-form-hero-particles">
        <div class="usr-form-hero-particle"></div>
        <div class="usr-form-hero-particle"></div>
        <div class="usr-form-hero-particle"></div>
        <div class="usr-form-hero-particle"></div>
    </div>
    <div class="usr-form-hero-content">
        <div class="usr-form-hero-avatar">
            <div class="usr-form-hero-ring"></div>
            <div class="usr-form-hero-letter"><?= strtoupper(substr($user['first_name'], 0, 1)) ?></div>
        </div>
        <div class="usr-form-hero-title"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
        <div class="usr-form-hero-sub"><?= htmlspecialchars($user['email']) ?></div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="usr-form-glass">
            <div class="usr-form-header">
                <div class="usr-form-header-left">
                    <i class="fas fa-user-pen"></i>
                    <h6><?= __('users.edit_title') ?></h6>
                </div>
                <div class="d-flex gap-2">
                    <?php if ($canEdit): ?>
                    <a href="/users/<?= $user['id'] ?>" class="usr-form-cancel" style="padding:0.4rem 0.8rem;font-size:0.8rem;"><i class="fas fa-eye"></i> <?= __('common.view') ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="usr-form-body">
                <form method="POST" action="/users/<?= $user['id'] ?>/update">
                    <input type="hidden" name="_token" value="<?= $csrfToken ?>">

                    <div class="usr-form-section"><i class="fas fa-id-card"></i> Identité</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="usr-form-group">
                                <label><?= __('users.first_name') ?> <span class="required">*</span></label>
                                <input type="text" class="usr-form-input" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="usr-form-group">
                                <label><?= __('users.last_name') ?> <span class="required">*</span></label>
                                <input type="text" class="usr-form-input" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="usr-form-section" style="margin-top:1.5rem;"><i class="fas fa-envelope"></i> Contact</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="usr-form-group">
                                <label><?= __('users.email_label') ?> <span class="required">*</span></label>
                                <input type="email" class="usr-form-input" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="usr-form-group">
                                <label><?= __('users.phone_label_short') ?></label>
                                <input type="tel" class="usr-form-input" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="usr-form-section" style="margin-top:1.5rem;"><i class="fas fa-lock"></i> Sécurité</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="usr-form-group">
                                <label><?= __('users.new_password') ?></label>
                                <input type="password" class="usr-form-input" name="password" minlength="6" placeholder="Laisser vide pour conserver">
                                <div class="usr-form-hint">Laisser vide si vous ne souhaitez pas changer le mot de passe</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="usr-form-group">
                                <label><?= __('common.status') ?></label>
                                <select class="usr-form-select" name="status">
                                    <option value="active" <?= $user['status']==='active'?'selected':'' ?>><?= __('users.active') ?></option>
                                    <option value="inactive" <?= $user['status']==='inactive'?'selected':'' ?>><?= __('users.inactive') ?></option>
                                    <option value="suspended" <?= $user['status']==='suspended'?'selected':'' ?>><?= __('users.suspended') ?></option>
                                    <option value="pending" <?= $user['status']==='pending'?'selected':'' ?>><?= __('users.pending') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="usr-form-section" style="margin-top:1.5rem;"><i class="fas fa-shield-halved"></i> Affectation</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="usr-form-group">
                                <label><?= __('users.role') ?></label>
                                <select class="usr-form-select" name="role_id">
                                    <option value=""><?= __('report_create.select') ?></option>
                                    <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>" <?= ($user['role_id'] ?? '') == $r['id'] ? 'selected' : '' ?>><?= $r['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="usr-form-group">
                                <label><?= __('common.organization') ?></label>
                                <select class="usr-form-select" name="organization_id">
                                    <option value=""><?= __('report_create.select') ?></option>
                                    <?php foreach ($organizations as $o): ?>
                                    <option value="<?= $o['id'] ?>" <?= ($user['organization_id'] ?? '') == $o['id'] ? 'selected' : '' ?>><?= $o['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="usr-form-group">
                                <label><?= __('common.daira') ?></label>
                                <select class="usr-form-select" name="daira_id">
                                    <option value=""><?= __('report_create.select') ?></option>
                                    <?php foreach ($dairas as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= ($user['daira_id'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= $d['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="usr-form-actions">
                        <button type="submit" class="usr-form-save"><i class="fas fa-check"></i> <?= __('users.save') ?></button>
                        <a href="/users/<?= $user['id'] ?>" class="usr-form-cancel"><i class="fas fa-arrow-left"></i> <?= __('users.cancel') ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
