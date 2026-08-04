<?php $pageTitle = __('common.edit_profile'); $activeTab = 'profile';
$csrfToken = \App\Helpers\Csrf::generate();
?>

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
