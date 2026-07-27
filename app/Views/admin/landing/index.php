<?php $pageTitle = 'Landing Page'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-globe me-2 text-primary"></i> Landing Page</h4>
    <a href="/" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-external-link-alt me-1"></i> Voir la page</a>
</div>

<div class="row g-4">
    <div class="col-sm-6 col-lg-4">
        <a href="/admin/landing/partners" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="transition:transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3" style="width:56px;height:56px;border-radius:16px;background:rgba(99,102,241,0.12);color:var(--primary,#6366f1);display:inline-flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-handshake"></i></div>
                    <h5 class="mb-1">Partenaires</h5>
                    <p class="text-muted mb-2 small">Logos et noms des partenaires</p>
                    <span class="badge bg-primary fs-6"><?= $stats['partners'] ?></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4">
        <a href="/admin/landing/gallery" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="transition:transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3" style="width:56px;height:56px;border-radius:16px;background:rgba(6,182,212,0.12);color:var(--accent,#06b6d4);display:inline-flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-images"></i></div>
                    <h5 class="mb-1">Galerie Photos</h5>
                    <p class="text-muted mb-2 small">Images de la section galerie</p>
                    <span class="badge bg-info text-dark fs-6"><?= $stats['gallery'] ?></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4">
        <a href="/admin/landing/testimonials" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="transition:transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3" style="width:56px;height:56px;border-radius:16px;background:rgba(16,185,129,0.12);color:var(--green,#10b981);display:inline-flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-comments"></i></div>
                    <h5 class="mb-1">Témoignages</h5>
                    <p class="text-muted mb-2 small">Avis des citoyens</p>
                    <span class="badge bg-success fs-6"><?= $stats['testimonials'] ?></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4">
        <a href="/admin/landing/before-after" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="transition:transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3" style="width:56px;height:56px;border-radius:16px;background:rgba(245,158,11,0.12);color:var(--amber,#f59e0b);display:inline-flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-arrows-left-right"></i></div>
                    <h5 class="mb-1">Avant / Après</h5>
                    <p class="text-muted mb-2 small">Comparaisons visuelles</p>
                    <span class="badge bg-warning text-dark fs-6"><?= $stats['before_after'] ?></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4">
        <a href="/admin/landing/faq" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="transition:transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3" style="width:56px;height:56px;border-radius:16px;background:rgba(139,92,246,0.12);color:#8b5cf6;display:inline-flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-circle-question"></i></div>
                    <h5 class="mb-1">FAQ</h5>
                    <p class="text-muted mb-2 small">Questions fréquentes</p>
                    <span class="badge bg-purple fs-6" style="background:#8b5cf6;"><?= $stats['faq'] ?></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4">
        <a href="/admin/landing/settings" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="transition:transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3" style="width:56px;height:56px;border-radius:16px;background:rgba(236,72,153,0.12);color:var(--pink,#ec4899);display:inline-flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-cog"></i></div>
                    <h5 class="mb-1">Paramètres</h5>
                    <p class="text-muted mb-2 small">Image hero + liens sociaux</p>
                    <span class="badge bg-pink" style="background:var(--pink,#ec4899);">5</span>
                </div>
            </div>
        </a>
    </div>
</div>
