<?php $pageTitle = 'Paramètres Landing Page'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-cog me-2 text-pink" style="color:var(--pink,#ec4899);"></i> Paramètres</h4>
    <a href="/admin/landing" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:700px;">
    <div class="card-body p-4">
        <form method="POST" action="/admin/landing/settings" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= $csrfToken ?>">

            <h6 class="mb-3"><i class="fas fa-image me-1"></i> Image Hero</h6>
            <?php if (!empty($settings['hero_image'])): ?>
            <div class="mb-3"><img src="<?= htmlspecialchars($settings['hero_image']) ?>" alt="Hero" style="max-height:150px;border-radius:10px;border:2px solid #ddd;"></div>
            <?php endif; ?>
            <div class="row g-3 mb-4">
                <div class="col-md-6"><label class="form-label">Upload fichier</label><input type="file" name="hero_image" class="form-control" accept="image/jpeg,image/png,image/webp"></div>
                <div class="col-md-6"><label class="form-label">— ou URL —</label><input type="url" name="hero_image_url" class="form-control" value="<?= htmlspecialchars($settings['hero_image'] ?? '') ?>" placeholder="https://images.unsplash.com/..."></div>
            </div>

            <hr>
            <h6 class="mb-3"><i class="fas fa-share-nodes me-1"></i> Liens Sociaux</h6>
            <div class="mb-3"><label class="form-label"><i class="fab fa-facebook me-1"></i> Facebook</label><input type="url" name="facebook_url" class="form-control" value="<?= htmlspecialchars($settings['facebook_url'] ?? '#') ?>" placeholder="https://facebook.com/..."></div>
            <div class="mb-3"><label class="form-label"><i class="fab fa-twitter me-1"></i> Twitter / X</label><input type="url" name="twitter_url" class="form-control" value="<?= htmlspecialchars($settings['twitter_url'] ?? '#') ?>" placeholder="https://twitter.com/..."></div>
            <div class="mb-3"><label class="form-label"><i class="fab fa-instagram me-1"></i> Instagram</label><input type="url" name="instagram_url" class="form-control" value="<?= htmlspecialchars($settings['instagram_url'] ?? '#') ?>" placeholder="https://instagram.com/..."></div>
            <div class="mb-3"><label class="form-label"><i class="fab fa-youtube me-1"></i> YouTube</label><input type="url" name="youtube_url" class="form-control" value="<?= htmlspecialchars($settings['youtube_url'] ?? '#') ?>" placeholder="https://youtube.com/..."></div>

            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer</button></div>
        </form>
    </div>
</div>
