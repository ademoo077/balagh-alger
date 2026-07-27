<?php $pageTitle = __('common.edit_profile'); $activeTab = 'profile';
$csrfToken = \App\Helpers\Csrf::generate();
?>
<style>
.pe-hero{background:linear-gradient(135deg,var(--c-accent),#8b5cf6,#06b6d4);border-radius:0 0 24px 24px;padding:24px 16px;margin:-12px -16px 16px;text-align:center;position:relative;overflow:hidden}
.pe-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 30% 20%,rgba(255,255,255,0.15),transparent 50%)}
.pe-hero *{position:relative;z-index:1}
.pe-avatar-wrap{position:relative;display:inline-block;margin-bottom:10px}
.pe-avatar{width:80px;height:80px;border-radius:50%;border:4px solid rgba(255,255,255,0.3);background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;color:#fff;overflow:hidden}
.pe-avatar img{width:100%;height:100%;object-fit:cover}
.pe-avatar-edit{position:absolute;bottom:0;right:0;width:28px;height:28px;border-radius:50%;background:var(--c-accent);border:3px solid var(--c-bg);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.65rem;cursor:pointer}
.pe-name{font-weight:800;font-size:1.15rem;color:#fff}
.pe-email{font-size:0.78rem;color:rgba(255,255,255,0.7);margin-top:2px}
.pe-field{margin-bottom:14px}
.pe-field label{display:block;font-weight:600;font-size:0.78rem;color:var(--c-text-muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.3px}
.pe-field input{width:100%;padding:10px 14px;border:2px solid var(--c-card-border);border-radius:12px;background:var(--c-card);color:var(--c-text);font-size:0.88rem;outline:none;transition:border-color 0.2s}
.pe-field input:focus{border-color:var(--c-accent)}
.pe-field input:disabled{opacity:0.5;cursor:not-allowed}
.pe-field .pe-hint{font-size:0.7rem;color:var(--c-text-muted);margin-top:3px}
.pe-btn{width:100%;padding:12px;border:none;border-radius:12px;background:var(--c-accent);color:#fff;font-weight:700;font-size:0.9rem;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:8px}
.pe-btn:hover{opacity:0.9;transform:translateY(-1px)}
.pe-btn:active{transform:translateY(0)}
.pe-btn-outline{background:transparent;border:2px solid var(--c-card-border);color:var(--c-text)}
.pe-pw-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--c-text-muted);cursor:pointer;font-size:0.85rem;padding:4px}
</style>

<div class="pe-hero">
    <div class="pe-avatar-wrap">
        <?php $hasAvatar = !empty($user['avatar']) && $user['avatar'] !== 'default.png'; ?>
        <div class="pe-avatar" id="peAvatar">
            <?php if ($hasAvatar): ?>
            <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" id="peAvatarImg">
            <?php else: ?>
            <?= strtoupper(substr($user['first_name'] ?? '?', 0, 1)) ?>
            <?php endif; ?>
        </div>
        <label class="pe-avatar-edit" for="peAvatarInput" title="Changer"><i class="fas fa-camera"></i></label>
    </div>
    <div class="pe-name"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></div>
    <div class="pe-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
</div>

<form method="POST" action="/my-profile/edit" enctype="multipart/form-data" id="peForm">
    <input type="hidden" name="_token" value="<?= $csrfToken ?>">
    <input type="file" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp" id="peAvatarInput" style="display:none">

    <div class="c-card c-anim-fade">
        <div class="c-section-title" style="margin-bottom:12px;"><h6><i class="fas fa-user-pen"></i> <?= __('common.edit_profile') ?></h6></div>

        <div class="pe-field">
            <label><?= __('users.first_name') ?></label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
        </div>
        <div class="pe-field">
            <label><?= __('users.last_name') ?></label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
        </div>
        <div class="pe-field">
            <label><?= __('users.phone') ?></label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="0555 55 55 55">
        </div>
        <div class="pe-field">
            <label><?= __('users.email') ?></label>
            <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
            <div class="pe-hint"><i class="fas fa-lock"></i> <?= __('users.email_readonly') ?></div>
        </div>
    </div>

    <div class="c-card c-anim-fade c-delay-1" style="margin-top:12px;">
        <div class="c-section-title" style="margin-bottom:12px;"><h6><i class="fas fa-key"></i> Sécurité</h6></div>

        <div class="pe-field" style="position:relative;">
            <label><?= __('users.new_password') ?></label>
            <input type="password" name="password" placeholder="<?= __('users.password_placeholder') ?>" minlength="6" id="pePwInput" style="padding-right:40px;">
            <button type="button" class="pe-pw-toggle" onclick="var i=document.getElementById('pePwInput');i.type=i.type==='password'?'text':'password';" tabindex="-1"><i class="fas fa-eye"></i></button>
            <div class="pe-hint"><?= __('users.password_help') ?></div>
        </div>
    </div>

    <div style="margin-top:16px;" class="c-anim-fade c-delay-2">
        <button type="submit" class="pe-btn"><i class="fas fa-check"></i> <?= __('common.save') ?></button>
    </div>
</form>

<div style="margin-top:12px;" class="c-anim-fade c-delay-3">
    <a href="/my-profile" class="pe-btn pe-btn-outline" style="text-decoration:none;"><i class="fas fa-arrow-left"></i> Retour au profil</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('peAvatarInput');
    if (input) {
        input.addEventListener('change', function() {
            if (this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var av = document.getElementById('peAvatar');
                    var existing = av.querySelector('img');
                    if (existing) { existing.src = e.target.result; }
                    else { av.innerHTML = '<img src="' + e.target.result + '" alt="Avatar" id="peAvatarImg">'; }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>
