<?php $pageTitle = 'Mon Impact'; $activeTab = 'home'; ?>
<style>
.impact-hero{background:linear-gradient(135deg,#6366f1 0%,#8b5cf6 40%,#a855f7 100%);border-radius:20px;padding:40px 32px;color:#fff;margin-bottom:2rem;position:relative;overflow:hidden}
.impact-hero::before{content:'';position:absolute;top:-50%;right:-20%;width:400px;height:400px;background:radial-gradient(circle,rgba(255,255,255,0.1) 0%,transparent 70%);border-radius:50%}
.impact-hero::after{content:'';position:absolute;bottom:-30%;left:-10%;width:300px;height:300px;background:radial-gradient(circle,rgba(255,255,255,0.08) 0%,transparent 70%);border-radius:50%}
.impact-hero *{position:relative;z-index:1}
.impact-hero h2{font-size:1.8rem;font-weight:800;margin-bottom:4px}
.impact-hero .big-number{font-size:4rem;font-weight:800;line-height:1;background:linear-gradient(135deg,#fff,#c4b5fd);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.impact-kpi{text-align:center;padding:24px 16px;border-radius:16px;background:var(--c-card);border:1px solid var(--c-card-border);transition:transform 0.2s,box-shadow 0.2s}
.impact-kpi:hover{transform:translateY(-4px);box-shadow:0 8px 32px rgba(99,102,241,0.15)}
.impact-kpi .kpi-value{font-size:2rem;font-weight:800;color:var(--c-accent)}
.impact-kpi .kpi-label{font-size:0.78rem;color:var(--c-text-muted);margin-top:4px}
.impact-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
@media(max-width:768px){.impact-grid{grid-template-columns:repeat(2,1fr)}}
.impact-badge-card{display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:14px;background:var(--c-card);border:1px solid var(--c-card-border);transition:all 0.2s}
.impact-badge-card:hover{border-color:var(--c-accent);transform:translateX(4px)}
.impact-badge-card .badge-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff;flex-shrink:0}
.impact-badge-card.locked{opacity:0.45;filter:grayscale(1)}
.impact-report{display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:12px;background:var(--c-card);border:1px solid var(--c-card-border);transition:all 0.15s;text-decoration:none;color:inherit;}
.impact-report:hover{border-color:var(--c-accent);transform:translateX(2px)}
.cat-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.impact-columns{display:grid;grid-template-columns:2fr 1fr;gap:16px}
@media(max-width:768px){.impact-columns{grid-template-columns:1fr}}
</style>

<div class="impact-hero c-anim-fade">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;">
        <div>
            <h2><?= __('impact.title') ?></h2>
            <p style="opacity:0.8;font-size:0.9rem;margin:0;"><?= __('impact.subtitle') ?></p>
        </div>
        <div class="big-number" data-count-up="<?= $stats['total'] ?>"><?= $stats['total'] ?></div>
    </div>
</div>

<div class="impact-grid">
    <div class="impact-kpi c-anim-fade c-delay-1">
        <div class="kpi-value" data-count-up="<?= $stats['total'] ?>"><?= number_format($stats['total']) ?></div>
        <div class="kpi-label"><?= __('impact.total_reports') ?></div>
    </div>
    <div class="impact-kpi c-anim-fade c-delay-2">
        <div class="kpi-value" style="color:var(--c-green);" data-count-up="<?= $stats['resolved'] ?>"><?= number_format($stats['resolved']) ?></div>
        <div class="kpi-label"><?= __('impact.resolved') ?></div>
    </div>
    <div class="impact-kpi c-anim-fade c-delay-3">
        <div class="kpi-value" style="color:var(--c-cyan);" data-count-up="<?= $stats['active'] ?>"><?= number_format($stats['active']) ?></div>
        <div class="kpi-label"><?= __('impact.in_progress') ?></div>
    </div>
    <div class="impact-kpi c-anim-fade c-delay-4">
        <div class="kpi-value" style="color:var(--c-amber);"><?= $stats['rate'] ?>%</div>
        <div class="kpi-label"><?= __('impact.resolution_rate') ?></div>
    </div>
</div>

<?php if (count($badges) > 0): ?>
<div class="c-card c-anim-fade c-delay-3" style="margin-bottom:20px;">
    <div class="c-section-title" style="margin-bottom:12px;"><h6><i class="fas fa-trophy" style="color:var(--c-amber);"></i> <?= __('impact.badges_title') ?> (<?= count($badges) ?>/<?= count($badgeDefs) ?>)</h6></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px;">
    <?php foreach ($badgeDefs as $key => $def):
        $earned = false;
        foreach ($badges as $b) { if ($b['badge_key'] === $key) { $earned = true; break; } }
    ?>
        <div class="impact-badge-card <?= $earned ? '' : 'locked' ?>">
            <div class="badge-icon" style="background:<?= $def['color'] ?>;"><i class="fas <?= $def['icon'] ?>"></i></div>
            <div>
                <div style="font-weight:600;font-size:0.88rem;"><?= htmlspecialchars($def['name']) ?></div>
                <div style="font-size:0.72rem;color:var(--c-text-muted);"><?= htmlspecialchars($def['desc']) ?></div>
            </div>
            <?php if (!$earned): ?>
                <div style="margin-left:auto;"><i class="fas fa-lock" style="color:var(--c-text-muted);font-size:0.8rem;"></i></div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="impact-columns">
    <div>
        <div class="c-card c-anim-fade c-delay-4" style="margin-bottom:20px;">
            <div class="c-section-title" style="margin-bottom:12px;"><h6><i class="fas fa-clock"></i> <?= __('impact.recent_reports') ?></h6></div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <?php foreach ($recent as $r):
                    $statusColors = ['submitted'=>'var(--c-amber)','in_progress'=>'var(--c-cyan)','resolved'=>'var(--c-green)','closed'=>'var(--c-text-muted)'];
                    $sc = $statusColors[$r['status']] ?? 'var(--c-text-muted)';
                ?>
                <a href="/reports/<?= $r['id'] ?>" class="impact-report">
                    <div class="cat-dot" style="background:<?= htmlspecialchars($r['category_color']) ?>"></div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($r['title']) ?></div>
                        <div style="font-size:0.72rem;color:var(--c-text-muted);"><?= htmlspecialchars($r['category_name']) ?> · <?= date('d/m/Y', strtotime($r['created_at'])) ?></div>
                    </div>
                    <span style="font-size:0.68rem;padding:3px 10px;border-radius:10px;background:<?= $sc ?>22;color:<?= $sc ?>;font-weight:600;flex-shrink:0;"><?= $r['status'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div>
        <div class="c-card c-anim-fade c-delay-4" style="margin-bottom:20px;">
            <div class="c-section-title" style="margin-bottom:12px;"><h6><i class="fas fa-chart-pie"></i> <?= __('impact.by_category') ?></h6></div>
            <canvas id="impactCatChart" height="200"></canvas>
        </div>
        <div class="c-card c-anim-fade c-delay-5">
            <div class="c-section-title" style="margin-bottom:12px;"><h6><i class="fas fa-chart-line"></i> <?= __('impact.activity') ?></h6></div>
            <canvas id="impactMonthChart" height="140"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var catData = <?= json_encode($byCategory) ?>;
    var monthData = <?= json_encode($byMonth) ?>;
    var isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

    if (catData.length) {
        new Chart(document.getElementById('impactCatChart'), {
            type: 'doughnut',
            data: {
                labels: catData.map(function(c) { return c.category_name; }),
                datasets: [{data: catData.map(function(c) { return c.count; }), backgroundColor: catData.map(function(c) { return c.category_color; }), borderWidth: 0}]
            },
            options: {responsive:true,plugins:{legend:{position:'bottom',labels:{color:isDark?'#e2e8f0':'#333',font:{size:10}}}}}
        });
    }

    if (monthData.length) {
        new Chart(document.getElementById('impactMonthChart'), {
            type: 'line',
            data: {
                labels: monthData.map(function(m) { return ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'][m.month - 1]; }),
                datasets: [{label:'Signalements',data:monthData.map(function(m) { return m.count; }),borderColor:'#6366f1',backgroundColor:'rgba(99,102,241,0.1)',fill:true,tension:0.4,pointRadius:4,pointBackgroundColor:'#6366f1'}]
            },
            options: {responsive:true,plugins:{legend:{display:false}},scales:{x:{ticks:{color:isDark?'#94a3b8':'#64748b',font:{size:10}},grid:{display:false}},y:{ticks:{color:isDark?'#94a3b8':'#64748b',font:{size:10}},grid:{color:isDark?'rgba(255,255,255,0.05)':'rgba(0,0,0,0.05)'}}}}
        });
    }
});
</script>
