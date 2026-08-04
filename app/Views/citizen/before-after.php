<?php $pageTitle = 'Avant / Après'; $activeTab = '';
?>
<div class="c-section-title c-anim-fade">
    <h6><i class="fas fa-images"></i> Avant / Après</h6>
</div>

<?php if (empty($pairs)): ?>
<div class="c-empty c-anim-fade c-delay-1">
    <svg class="c-empty-svg" viewBox="0 0 140 120" fill="none">
        <circle class="c-sv-circle" cx="70" cy="60" r="50"/>
        <rect x="40" y="44" width="24" height="32" rx="6" fill="var(--c-accent-surface)" stroke="var(--c-accent)" stroke-width="1.5" opacity="0.6"/>
        <rect x="76" y="44" width="24" height="32" rx="6" fill="var(--c-green-surface)" stroke="var(--c-green)" stroke-width="1.5" opacity="0.6"/>
        <path class="c-sv-float" d="M52 60l4 4 6-8" stroke="var(--c-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        <path d="M84 60l4 4 6-8" stroke="var(--c-green)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" opacity="0.8"/>
        <circle class="c-sv-dot" cx="36" cy="38" r="4"/>
        <circle class="c-sv-dot" cx="106" cy="42" r="3"/>
    </svg>
    <h5>Aucune comparaison disponible</h5>
    <p>Les comparaisons avant/après apparaîtront ici une fois les travaux terminés.</p>
</div>
<?php else: ?>
<?php foreach ($pairs as $i => $pair): ?>
<div class="c-card c-anim-fade" style="animation-delay:<?= ($i + 1) * 0.06 ?>s;overflow:hidden;">
    <div class="c-compare" data-compare>
        <img src="/uploads/reports/<?= htmlspecialchars($pair['before_photo']) ?>" class="c-compare-before" alt="Avant">
        <img src="/uploads/interventions/<?= htmlspecialchars($pair['id']) ?>/<?= htmlspecialchars($pair['after_photo']) ?>" class="c-compare-after" alt="Après">
        <div class="c-compare-slider" data-slider></div>
        <span class="c-compare-label before">Avant</span>
        <span class="c-compare-label after">Après</span>
    </div>
    <div style="padding:12px 14px;">
        <div style="font-weight:600;font-size:0.88rem;margin-bottom:4px;"><?= htmlspecialchars($pair['title']) ?></div>
        <div class="c-report-meta">
            <span style="color:<?= $pair['category_color'] ?>;font-weight:600;"><?= htmlspecialchars($pair['category_name']) ?></span>
            <span>&middot;</span>
            <span><?= htmlspecialchars($pair['commune_name']) ?></span>
        </div>
        <a href="/reports/<?= $pair['id'] ?>" class="c-btn c-btn-outline c-btn-sm" style="margin-top:8px;">Voir le rapport →</a>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-compare]').forEach(function(el) {
        var slider = el.querySelector('[data-slider]');
        var before = el.querySelector('.c-compare-before');
        var dragging = false;

        function setPosition(x) {
            var rect = el.getBoundingClientRect();
            var pct = Math.max(0, Math.min(100, ((x - rect.left) / rect.width) * 100));
            slider.style.left = pct + '%';
            before.style.clipPath = 'inset(0 ' + (100 - pct) + '% 0 0)';
        }

        slider.addEventListener('mousedown', function(e) { e.preventDefault(); dragging = true; });
        el.addEventListener('touchstart', function() { dragging = true; }, {passive: true});
        document.addEventListener('mousemove', function(e) { if (dragging) setPosition(e.clientX); });
        document.addEventListener('touchmove', function(e) { if (dragging) setPosition(e.touches[0].clientX); }, {passive: true});
        document.addEventListener('mouseup', function() { dragging = false; });
        document.addEventListener('touchend', function() { dragging = false; });
    });
});
</script>
