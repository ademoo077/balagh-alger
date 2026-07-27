<!DOCTYPE html>
<html lang="fr" dir="ltr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($ogTitle) ?> — Balagh Alger</title>

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($ogTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($ogDesc) ?>">
    <meta property="og:url" content="<?= $ogUrl ?>">
    <meta property="og:site_name" content="Balagh Alger">
    <meta property="og:locale" content="fr_DZ">
    <?php if ($ogImage): ?>
    <meta property="og:image" content="<?= $ogImage ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="<?= $ogImage ? 'summary_large_image' : 'summary' ?>">
    <meta name="twitter:title" content="<?= htmlspecialchars($ogTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($ogDesc) ?>">
    <?php if ($ogImage): ?>
    <meta name="twitter:image" content="<?= $ogImage ?>">
    <?php endif; ?>

    <meta name="theme-color" content="#6366f1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0e1a; --surface: #111827; --card: rgba(17,24,39,0.85);
            --border: rgba(255,255,255,0.06); --glass: rgba(255,255,255,0.03);
            --accent: #6366f1; --accent-glow: rgba(99,102,241,0.25);
            --text: #f1f5f9; --text-sec: #cbd5e1; --muted: #64748b;
            --green: #22c55e; --amber: #f59e0b; --red: #ef4444; --cyan: #06b6d4; --purple: #a855f7;
            --radius: 16px;
        }
        *, *::before, *::after { box-sizing: border-box; }
        html { font-family: 'Inter', -apple-system, sans-serif; background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; }
        body { margin: 0; padding: 0; min-height: 100vh; }

        .share-topbar {
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 16px; background: rgba(10,14,26,0.92); backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }
        .share-topbar-brand { display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.95rem; color: var(--text); text-decoration: none; }
        .share-topbar-brand i { color: var(--accent); }
        .share-topbar-login {
            padding: 7px 16px; border-radius: 10px; font-size: 0.78rem; font-weight: 600;
            background: var(--accent); color: #fff; text-decoration: none; transition: all 0.2s;
        }
        .share-topbar-login:active { transform: scale(0.95); }

        .share-container { max-width: 640px; margin: 0 auto; padding: 16px; }

        /* Hero photo */
        .share-hero { border-radius: var(--radius); overflow: hidden; margin-bottom: 16px; position: relative; }
        .share-hero img { width: 100%; aspect-ratio: 16/10; object-fit: cover; display: block; background: var(--surface); }
        .share-hero-badge {
            position: absolute; top: 12px; left: 12px;
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 20px;
            background: rgba(10,14,26,0.8); backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.1);
            font-size: 0.72rem; font-weight: 600; color: #fff;
        }
        .share-hero-count {
            position: absolute; bottom: 12px; right: 12px;
            padding: 4px 10px; border-radius: 8px;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);
            font-size: 0.68rem; font-weight: 600; color: #fff;
        }

        /* Photo gallery */
        .share-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 8px; margin-bottom: 16px; }
        .share-gallery-item {
            border-radius: 10px; overflow: hidden; aspect-ratio: 4/3; background: var(--surface);
            border: 1px solid var(--border); cursor: pointer; transition: all 0.2s;
        }
        .share-gallery-item:hover { border-color: var(--accent); transform: scale(1.02); }
        .share-gallery-item img { width: 100%; height: 100%; object-fit: cover; }

        /* Status + meta */
        .share-status-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
        .share-status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 12px;
            font-size: 0.78rem; font-weight: 600;
        }
        .share-meta-pill {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 10px;
            background: var(--glass); border: 1px solid var(--border);
            font-size: 0.72rem; color: var(--text-sec);
        }
        .share-meta-pill i { font-size: 0.68rem; }

        /* Title + description */
        .share-title { font-size: 1.2rem; font-weight: 800; line-height: 1.3; margin-bottom: 6px; }
        .share-code { font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 700; color: var(--accent); letter-spacing: 0.03em; margin-bottom: 12px; }
        .share-desc { font-size: 0.88rem; color: var(--text-sec); line-height: 1.6; margin-bottom: 16px; }

        /* Workflow */
        .share-wf { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin-bottom: 16px; }
        .share-wf-title { font-size: 0.78rem; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
        .share-wf-title i { color: var(--accent); }
        .share-wf-track { display: flex; gap: 3px; height: 6px; border-radius: 3px; overflow: hidden; background: var(--surface); margin-bottom: 10px; }
        .share-wf-seg { flex: 1; border-radius: 3px; transition: background 0.5s; }
        .share-wf-labels { display: flex; justify-content: space-between; }
        .share-wf-labels span { font-size: 0.58rem; color: var(--muted); font-weight: 500; text-align: center; flex: 1; }
        .share-wf-labels span.done { color: var(--green); font-weight: 700; }
        .share-wf-labels span.active { color: var(--accent); font-weight: 700; }

        /* Info grid */
        .share-info { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px; }
        .share-info-item {
            display: flex; align-items: center; gap: 8px; padding: 10px 12px;
            background: var(--card); border: 1px solid var(--border); border-radius: 12px;
        }
        .share-info-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.72rem; flex-shrink: 0; }
        .share-info-label { font-size: 0.62rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.03em; }
        .share-info-value { font-size: 0.8rem; font-weight: 600; }

        /* Map */
        .share-map { width: 100%; height: 200px; border-radius: var(--radius); overflow: hidden; margin-bottom: 16px; border: 1px solid var(--border); }

        /* Timeline */
        .share-timeline { position: relative; padding-left: 28px; margin-bottom: 16px; }
        .share-timeline::before { content: ''; position: absolute; left: 9px; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, var(--accent), var(--cyan), var(--green), transparent); border-radius: 2px; }
        .share-tl-item { position: relative; padding-bottom: 14px; }
        .share-tl-dot {
            position: absolute; left: -28px; top: 2px; width: 20px; height: 20px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-size: 0.5rem;
            box-shadow: 0 0 0 3px var(--bg);
        }
        .share-tl-card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; }
        .share-tl-text { font-size: 0.8rem; font-weight: 600; }
        .share-tl-time { font-size: 0.65rem; color: var(--muted); margin-top: 2px; }

        /* Share buttons */
        .share-actions { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin-bottom: 16px; }
        .share-actions-title { font-size: 0.82rem; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
        .share-actions-title i { color: var(--accent); }
        .share-btns { display: flex; gap: 8px; }
        .share-btn {
            flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px;
            padding: 12px; border-radius: 12px; border: none;
            font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s;
            text-decoration: none; color: #fff;
        }
        .share-btn:active { transform: scale(0.95); }
        .share-btn.whatsapp { background: #25d366; }
        .share-btn.sms { background: var(--cyan); }
        .share-btn.copy { background: var(--accent); }
        .share-btn.copied { background: var(--green); }

        /* CTA */
        .share-cta {
            text-align: center; padding: 24px 16px; margin-bottom: 16px;
            background: linear-gradient(135deg, rgba(99,102,241,0.08), rgba(139,92,246,0.08));
            border: 1px solid rgba(99,102,241,0.15); border-radius: var(--radius);
        }
        .share-cta-icon { font-size: 2rem; color: var(--accent); margin-bottom: 8px; }
        .share-cta h5 { font-weight: 700; margin-bottom: 4px; }
        .share-cta p { font-size: 0.82rem; color: var(--muted); margin-bottom: 14px; }
        .share-cta-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 24px; border-radius: 12px;
            background: var(--accent); color: #fff; text-decoration: none;
            font-size: 0.85rem; font-weight: 600; transition: all 0.2s;
        }
        .share-cta-btn:active { transform: scale(0.95); }

        /* Footer */
        .share-footer { text-align: center; padding: 16px; font-size: 0.7rem; color: var(--muted); }

        /* Lightbox */
        .share-lb {
            display: none; position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,0.92); backdrop-filter: blur(20px);
            align-items: center; justify-content: center;
        }
        .share-lb.active { display: flex; }
        .share-lb img { max-width: 92vw; max-height: 85vh; border-radius: 12px; object-fit: contain; }
        .share-lb-close {
            position: absolute; top: 16px; right: 16px;
            width: 40px; height: 40px; border-radius: 50%; border: none;
            background: rgba(255,255,255,0.1); color: #fff; font-size: 1.1rem;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
        }

        @media (max-width: 480px) {
            .share-info { grid-template-columns: 1fr; }
            .share-btns { flex-direction: column; }
        }
    </style>
</head>
<body>

    <!-- Top bar -->
    <div class="share-topbar">
        <a href="/" class="share-topbar-brand"><i class="fas fa-bullhorn"></i> Balagh Alger</a>
        <a href="/login" class="share-topbar-login"><i class="fas fa-right-to-bracket"></i> Connexion</a>
    </div>

    <div class="share-container">

        <!-- Hero photo -->
        <?php if (!empty($images)): ?>
        <div class="share-hero">
            <img src="/uploads/reports/<?= htmlspecialchars($images[0]['filename']) ?>" alt="<?= htmlspecialchars($report['title']) ?>" loading="eager">
            <div class="share-hero-badge">
                <i class="fas <?= $report['category_icon'] ?? 'fa-flag' ?>" style="color:<?= $report['category_color'] ?? 'var(--accent)' ?>;"></i>
                <?= htmlspecialchars($report['category_name'] ?? '') ?>
            </div>
            <?php if (count($images) > 1): ?>
            <div class="share-hero-count"><i class="fas fa-images"></i> <?= count($images) ?> photos</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Photo gallery (if multiple) -->
        <?php if (count($images) > 1): ?>
        <div class="share-gallery">
            <?php foreach ($images as $img): ?>
            <div class="share-gallery-item" onclick="openShareLB('/uploads/reports/<?= htmlspecialchars($img['filename']) ?>')">
                <img src="/uploads/reports/<?= htmlspecialchars($img['filename']) ?>" alt="" loading="lazy">
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Status + meta -->
        <?php
        $statusColors = ['submitted'=>'#f59e0b','acknowledged'=>'#6366f1','assigned'=>'#06b6d4','in_progress'=>'#06b6d4','pending_review'=>'#f59e0b','pending_unite'=>'#f59e0b','validated'=>'#22c55e','resolved'=>'#22c55e','closed'=>'#64748b','rejected'=>'#ef4444'];
        $statusLabels = ['submitted'=>'Soumis','acknowledged'=>'Accusé','assigned'=>'Assigné','in_progress'=>'En cours','pending_review'=>'En revue','pending_unite'=>'Valid. unite','validated'=>'Validé','resolved'=>'Résolu','closed'=>'Fermé','rejected'=>'Rejeté'];
        $sc = $statusColors[$report['status']] ?? '#6366f1';
        $sl = $statusLabels[$report['status']] ?? $report['status'];
        ?>
        <div class="share-status-row">
            <span class="share-status-badge" style="background:<?= $sc ?>22;color:<?= $sc ?>;">
                <span style="width:7px;height:7px;border-radius:50%;background:<?= $sc ?>;"></span>
                <?= $sl ?>
            </span>
            <span class="share-meta-pill"><i class="fas fa-location-dot"></i> <?= htmlspecialchars($report['commune_name'] ?? '') ?></span>
            <span class="share-meta-pill"><i class="fas fa-calendar"></i> <?= (new \DateTime($report['created_at']))->format('d/m/Y') ?></span>
        </div>

        <!-- Title -->
        <h1 class="share-title"><?= htmlspecialchars($report['title'] ?: 'Signalement #' . $report['tracking_code']) ?></h1>
        <div class="share-code"><i class="fas fa-fingerprint"></i> <?= htmlspecialchars($report['tracking_code']) ?></div>

        <?php if ($report['description']): ?>
        <p class="share-desc"><?= nl2br(htmlspecialchars($report['description'])) ?></p>
        <?php endif; ?>

        <!-- Info grid -->
        <div class="share-info">
            <div class="share-info-item">
                <div class="share-info-icon" style="background:<?= $report['category_color'] ?? 'var(--accent)' ?>20;color:<?= $report['category_color'] ?? 'var(--accent)' ?>;">
                    <i class="fas <?= $report['category_icon'] ?? 'fa-flag' ?>"></i>
                </div>
                <div>
                    <div class="share-info-label">Catégorie</div>
                    <div class="share-info-value"><?= htmlspecialchars($report['category_name'] ?? '—') ?></div>
                </div>
            </div>
            <div class="share-info-item">
                <div class="share-info-icon" style="background:var(--cyan-surface,var(--cyan));color:var(--cyan);">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <div class="share-info-label">Daira</div>
                    <div class="share-info-value"><?= htmlspecialchars($report['daira_name'] ?? '—') ?></div>
                </div>
            </div>
            <div class="share-info-item">
                <div class="share-info-icon" style="background:rgba(168,85,247,0.1);color:var(--purple);">
                    <i class="fas fa-map-pin"></i>
                </div>
                <div>
                    <div class="share-info-label">Commune</div>
                    <div class="share-info-value"><?= htmlspecialchars($report['commune_name'] ?? '—') ?></div>
                </div>
            </div>
            <?php if ($report['org_name']): ?>
            <div class="share-info-item">
                <div class="share-info-icon" style="background:rgba(245,158,11,0.1);color:var(--amber);">
                    <i class="fas fa-building-columns"></i>
                </div>
                <div>
                    <div class="share-info-label">Organisation</div>
                    <div class="share-info-value"><?= htmlspecialchars($report['org_name']) ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Workflow -->
        <?php
        $wfSteps = ['submitted','acknowledged','assigned','in_progress','validated','resolved'];
        $wfLabels = ['Soumis','Accusé','Assigné','En cours','Validé','Résolu'];
        $currentIdx = array_search($report['status'], $wfSteps);
        if ($currentIdx === false) $currentIdx = 0;
        $wfPct = round(($currentIdx / (count($wfSteps) - 1)) * 100);
        ?>
        <div class="share-wf">
            <div class="share-wf-title"><i class="fas fa-route"></i> Progression du traitement</div>
            <div class="share-wf-track">
                <?php foreach ($wfSteps as $i => $step): ?>
                <div class="share-wf-seg" style="background:<?= $i <= $currentIdx ? 'var(--accent)' : 'var(--surface)' ?>;"></div>
                <?php endforeach; ?>
            </div>
            <div class="share-wf-labels">
                <?php foreach ($wfLabels as $i => $label): ?>
                <span class="<?= $i < $currentIdx ? 'done' : ($i === $currentIdx ? 'active' : '') ?>"><?= $label ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Map -->
        <?php if ($report['latitude'] && $report['longitude']): ?>
        <div class="share-map" id="shareMap"></div>
        <?php endif; ?>

        <!-- Timeline -->
        <?php if (!empty($history)): ?>
        <div style="margin-bottom:16px;">
            <div style="font-size:0.82rem;font-weight:700;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-clock-rotate-left" style="color:var(--accent);"></i> Historique
            </div>
            <div class="share-timeline">
                <?php foreach (array_slice($history, 0, 10) as $h): ?>
                <div class="share-tl-item">
                    <div class="share-tl-dot" style="background:var(--accent);color:#fff;">
                        <i class="fas fa-circle" style="font-size:6px;"></i>
                    </div>
                    <div class="share-tl-card">
                        <div class="share-tl-text"><?= htmlspecialchars($h['action'] ?? $h['description'] ?? '') ?></div>
                        <?php if (!empty($h['first_name'])): ?>
                        <div style="font-size:0.68rem;color:var(--muted);"><?= htmlspecialchars($h['first_name'] . ' ' . $h['last_name']) ?></div>
                        <?php endif; ?>
                        <div class="share-tl-time"><?= \App\Helpers\Helper::timeAgo($h['created_at']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Share buttons -->
        <div class="share-actions">
            <div class="share-actions-title"><i class="fas fa-share-nodes"></i> Partager ce signalement</div>
            <div class="share-btns">
                <a class="share-btn whatsapp" id="shareWA" href="#" target="_blank" rel="noopener">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <a class="share-btn sms" id="shareSMS" href="sms:?body=">
                    <i class="fas fa-comment-sms"></i> SMS
                </a>
                <button class="share-btn copy" id="shareCopy" type="button">
                    <i class="fas fa-link"></i> Copier
                </button>
            </div>
        </div>

        <!-- CTA -->
        <div class="share-cta">
            <div class="share-cta-icon"><i class="fas fa-bullhorn"></i></div>
            <h5>Un problème chez vous ?</h5>
            <p>Signalez-le sur Balagh Alger et suivez sa résolution en temps réel.</p>
            <a href="/register" class="share-cta-btn"><i class="fas fa-user-plus"></i> Créer un compte</a>
            <a href="/login" class="share-cta-btn" style="background:transparent;border:1px solid var(--border);color:var(--text-sec);margin-left:8px;"><i class="fas fa-right-to-bracket"></i> Se connecter</a>
        </div>

        <div class="share-footer">
            <i class="fas fa-shield-halved"></i> Signalement #<?= htmlspecialchars($report['tracking_code']) ?> — Balagh Alger
        </div>
    </div>

    <!-- Lightbox -->
    <div class="share-lb" id="shareLB">
        <button class="share-lb-close" onclick="closeShareLB()"><i class="fas fa-times"></i></button>
        <img src="" alt="" id="shareLBImg">
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    (function() {
        var shareUrl = <?= json_encode($ogUrl) ?>;
        var shareText = <?= json_encode($ogTitle) ?>;
        var fullText = shareText + '\n' + shareUrl;

        // WhatsApp
        document.getElementById('shareWA').href = 'https://wa.me/?text=' + encodeURIComponent(fullText);

        // SMS
        document.getElementById('shareSMS').href = 'sms:?body=' + encodeURIComponent(fullText);

        // Copy
        document.getElementById('shareCopy').addEventListener('click', function() {
            var btn = this;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(shareUrl).then(function() {
                    btn.innerHTML = '<i class="fas fa-check"></i> Copié !';
                    btn.classList.add('copied');
                    setTimeout(function() { btn.innerHTML = '<i class="fas fa-link"></i> Copier'; btn.classList.remove('copied'); }, 2000);
                });
            } else {
                var ta = document.createElement('textarea');
                ta.value = shareUrl; document.body.appendChild(ta); ta.select();
                document.execCommand('copy'); document.body.removeChild(ta);
                btn.innerHTML = '<i class="fas fa-check"></i> Copié !';
                btn.classList.add('copied');
                setTimeout(function() { btn.innerHTML = '<i class="fas fa-link"></i> Copier'; btn.classList.remove('copied'); }, 2000);
            }
        });

        // Map
        <?php if ($report['latitude'] && $report['longitude']): ?>
        var map = L.map('shareMap', { zoomControl: false, attributionControl: false, scrollWheelZoom: false })
            .setView([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>], 15);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);
        var color = <?= json_encode($sc) ?>;
        var icon = L.divIcon({
            className: '',
            html: '<div style="width:36px;height:36px;border-radius:50%;background:' + color + ';border:3px solid #fff;box-shadow:0 2px 12px ' + color + '66;display:flex;align-items:center;justify-content:center;"><i class="fas <?= $report['category_icon'] ?? 'fa-flag' ?>" style="font-size:13px;color:#fff;"></i></div>',
            iconSize: [36, 36], iconAnchor: [18, 18]
        });
        L.marker([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>], { icon: icon }).addTo(map);
        <?php endif; ?>
    })();

    function openShareLB(src) {
        var lb = document.getElementById('shareLB');
        document.getElementById('shareLBImg').src = src;
        lb.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeShareLB() {
        document.getElementById('shareLB').classList.remove('active');
        document.body.style.overflow = '';
    }
    document.getElementById('shareLB').addEventListener('click', function(e) {
        if (e.target === this) closeShareLB();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeShareLB();
    });
    </script>
</body>
</html>
