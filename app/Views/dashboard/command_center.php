<?php \App\Helpers\I18n::init();
$lang = \App\Helpers\I18n::getLang();
$isRtl = \App\Helpers\I18n::isRtl();
$dir = \App\Helpers\I18n::getDir();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('app.name') ?> — NOC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>(function(){var t=localStorage.getItem('balagh-theme');if(t)document.documentElement.setAttribute('data-bs-theme',t);})();</script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── DARK THEME (default) ── */
        :root {
            --bg-base: #04080f;
            --bg-surface: #0a1020;
            --bg-card: #0c1424;
            --bg-elevated: #111c30;
            --bg-inset: #070d1a;

            --border-subtle: rgba(0, 255, 136, 0.06);
            --border-default: rgba(0, 255, 136, 0.10);
            --border-strong: rgba(0, 255, 136, 0.18);
            --border-glow: rgba(0, 255, 136, 0.30);

            --text-primary: #e4ecf4;
            --text-secondary: #8899aa;
            --text-muted: #506070;
            --text-accent: #00ff88;

            --accent: #00ff88;
            --accent-dim: rgba(0, 255, 136, 0.08);
            --accent-glow: rgba(0, 255, 136, 0.25);
            --cyan: #00d4ff;
            --cyan-dim: rgba(0, 212, 255, 0.08);
            --red: #ff3355;
            --red-dim: rgba(255, 51, 85, 0.10);
            --yellow: #ffaa00;
            --yellow-dim: rgba(255, 170, 0, 0.10);
            --purple: #aa66ff;
            --purple-dim: rgba(170, 102, 255, 0.10);
            --pink: #ff5599;

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.4);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.5);
            --shadow-glow: 0 0 24px rgba(0,255,136,0.12);
        }

        /* ── LIGHT THEME ── */
        [data-bs-theme="light"] {
            --bg-base: #f0f3f7;
            --bg-surface: #ffffff;
            --bg-card: #ffffff;
            --bg-elevated: #f8fafb;
            --bg-inset: #eef1f5;

            --border-subtle: rgba(0, 100, 50, 0.06);
            --border-default: rgba(0, 100, 50, 0.10);
            --border-strong: rgba(0, 100, 50, 0.16);
            --border-glow: rgba(0, 100, 50, 0.25);

            --text-primary: #1a2332;
            --text-secondary: #556677;
            --text-muted: #94a3b0;
            --text-accent: #00884a;

            --accent: #00884a;
            --accent-dim: rgba(0, 136, 74, 0.06);
            --accent-glow: rgba(0, 136, 74, 0.15);
            --cyan: #0077bb;
            --cyan-dim: rgba(0, 119, 187, 0.06);
            --red: #cc2244;
            --red-dim: rgba(204, 34, 68, 0.06);
            --yellow: #bb8800;
            --yellow-dim: rgba(187, 136, 0, 0.06);
            --purple: #7744bb;
            --purple-dim: rgba(119, 68, 187, 0.06);
            --pink: #cc4477;

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
            --shadow-glow: none;
        }

        /* ── BASE ── */
        html, body {
            background: var(--bg-base);
            color: var(--text-primary);
            font-family: 'Inter', 'Noto Sans Arabic', sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
            transition: background 0.4s, color 0.4s;
        }

        /* ── SCANLINES ── */
        .scanlines {
            pointer-events: none;
            position: fixed; inset: 0; z-index: 9999;
            background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.012) 2px, rgba(0,0,0,0.012) 4px);
            opacity: 0.5;
        }
        [data-bs-theme="light"] .scanlines { opacity: 0.04; }

        /* ── TOPBAR ── */
        .topbar {
            position: sticky; top: 0; z-index: 1000;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.2rem; height: 46px;
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-default);
            backdrop-filter: blur(16px);
        }
        .topbar::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent 5%, var(--accent) 50%, transparent 95%);
            opacity: 0.25;
        }
        .brand { display: flex; align-items: center; gap: 0.6rem; }
        .brand img { height: 24px; width: 24px; border-radius: 5px; object-fit: contain; border: 1px solid var(--border-default); }
        .brand-name {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700; font-size: 0.85rem;
            letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--text-accent);
        }
        .brand-sub {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.55rem; color: var(--text-muted);
            letter-spacing: 0.06em; text-transform: uppercase;
        }
        .topbar-center {
            position: absolute; left: 50%; transform: translateX(-50%);
            text-align: center;
        }
        .clock {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.1rem; font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: var(--text-accent); letter-spacing: 0.08em;
            text-shadow: 0 0 12px var(--accent-glow);
        }
        .clock-date {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.6rem; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.08em;
        }
        .topbar-right { display: flex; align-items: center; gap: 0.45rem; }

        .live-badge {
            display: flex; align-items: center; gap: 0.3rem;
            padding: 0.15rem 0.45rem; border-radius: 4px;
            background: var(--accent-dim); border: 1px solid var(--border-default);
        }
        .live-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 6px var(--accent);
            animation: blink 2s infinite;
        }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.35; } }
        .live-text {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.6rem; font-weight: 700;
            color: var(--text-accent); letter-spacing: 0.08em;
        }
        .timer {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.6rem; color: var(--text-muted); letter-spacing: 0.06em;
        }
        .tb-btn {
            padding: 0.3rem 0.55rem; border-radius: 4px;
            border: 1px solid var(--border-default); background: transparent;
            color: var(--text-muted); font-size: 0.75rem; cursor: pointer;
            transition: all 0.2s; text-decoration: none;
            display: inline-flex; align-items: center; gap: 0.25rem;
        }
        .tb-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-dim); }

        /* ── MAIN CONTENT ── */
        .main { padding: 0.7rem; display: flex; flex-direction: column; gap: 0.65rem; }

        /* ── KPI ROW ── */
        .kpi-row { display: grid; grid-template-columns: repeat(6, 1fr); gap: 0.6rem; }
        .kpi {
            position: relative; background: var(--bg-card);
            border: 1px solid var(--border-subtle); border-radius: 8px;
            padding: 0.85rem 0.5rem; text-align: center; overflow: hidden;
            transition: all 0.3s;
        }
        .kpi:hover { border-color: var(--border-strong); box-shadow: var(--shadow-glow); transform: translateY(-2px); }
        .kpi::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            border-radius: 8px 8px 0 0;
        }
        .kpi-glow {
            position: absolute; top: -30px; left: 50%; transform: translateX(-50%);
            width: 80px; height: 30px; border-radius: 50%;
            filter: blur(12px); opacity: 0.2; pointer-events: none;
        }
        .kpi-icon { font-size: 0.95rem; margin-bottom: 0.3rem; opacity: 0.6; }
        .kpi-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.7rem; font-weight: 800; line-height: 1;
            font-variant-numeric: tabular-nums;
            position: relative;
        }
        .kpi-lbl {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.55rem; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.1em;
            margin-top: 0.25rem; font-weight: 600;
        }
        .kpi--total::before { background: var(--accent); }
        .kpi--total .kpi-val { color: var(--accent); }
        .kpi--total .kpi-glow { background: var(--accent); }
        .kpi--pending::before { background: var(--yellow); }
        .kpi--pending .kpi-val { color: var(--yellow); }
        .kpi--pending .kpi-glow { background: var(--yellow); }
        .kpi--progress::before { background: var(--cyan); }
        .kpi--progress .kpi-val { color: var(--cyan); }
        .kpi--progress .kpi-glow { background: var(--cyan); }
        .kpi--resolved::before { background: var(--accent); opacity: 0.6; }
        .kpi--resolved .kpi-val { color: var(--accent); opacity: 0.85; }
        .kpi--resolved .kpi-glow { background: var(--accent); }
        .kpi--urgent::before { background: var(--red); }
        .kpi--urgent .kpi-val { color: var(--red); }
        .kpi--urgent .kpi-glow { background: var(--red); }
        .kpi--overdue::before { background: var(--pink); }
        .kpi--overdue .kpi-val { color: var(--pink); }
        .kpi--overdue .kpi-glow { background: var(--pink); }

        @keyframes kpiPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.06); } }
        .kpi.pulse .kpi-val { animation: kpiPulse 0.4s ease; }
        @keyframes urgentFlash { 0%, 100% { box-shadow: 0 0 4px var(--red-dim); } 50% { box-shadow: 0 0 20px var(--red-dim), inset 0 0 12px var(--red-dim); } }
        .kpi--urgent.pulse { animation: urgentFlash 0.8s ease 3; border-color: var(--red); }

        /* ── PANELS ── */
        .panel {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            overflow: hidden;
            display: flex; flex-direction: column;
            transition: background 0.3s, border-color 0.3s;
        }
        .panel:hover { border-color: var(--border-default); }
        .panel-head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid var(--border-subtle);
            background: var(--accent-dim);
        }
        .panel-head h6 {
            margin: 0;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em;
            color: var(--text-accent);
            display: flex; align-items: center; gap: 0.35rem;
        }
        .panel-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.52rem; padding: 0.1rem 0.4rem;
            border-radius: 3px; font-weight: 700;
            background: var(--accent-dim); color: var(--accent);
            border: 1px solid var(--border-default);
        }
        .panel-body { flex: 1; overflow: auto; padding: 0.5rem; }
        .panel-body::-webkit-scrollbar { width: 3px; }
        .panel-body::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 3px; }

        /* ── EMPTY STATE ── */
        .empty {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 0.8rem 0.5rem;
            text-align: center; opacity: 0.5;
        }
        .empty i { font-size: 1.3rem; color: var(--text-muted); margin-bottom: 0.35rem; }
        .empty span {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.62rem; color: var(--text-muted);
            letter-spacing: 0.04em;
        }

        /* ── MIDDLE ROW ── */
        .mid-row { display: grid; grid-template-columns: 1fr 360px; gap: 0.65rem; }
        .mid-left { display: flex; flex-direction: column; gap: 0.55rem; }
        .mid-left .panel { flex: 1; }
        .mid-right { display: flex; flex-direction: column; gap: 0.55rem; }
        .mid-right .panel { flex: 1; }
        .mid-right .panel-tiny { flex: 0 0 auto; }

        /* ── FEED ── */
        .feed { display: flex; flex-direction: column; gap: 0.3rem; }
        .fi {
            display: flex; align-items: flex-start; gap: 0.5rem;
            padding: 0.45rem 0.55rem; border-radius: 6px;
            background: var(--accent-dim);
            border: 1px solid transparent;
            transition: all 0.2s;
            animation: slideIn 0.3s ease-out;
        }
        .fi:hover { border-color: var(--border-default); background: var(--bg-elevated); }
        .fi.new { border-color: var(--cyan); box-shadow: 0 0 10px var(--cyan-dim); }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: none; } }
        .fi-icon {
            width: 26px; height: 26px; border-radius: 6px;
            flex-shrink: 0; display: flex; align-items: center;
            justify-content: center; font-size: 0.6rem;
        }
        .fi-body { flex: 1; min-width: 0; }
        .fi-title {
            font-size: 0.78rem; font-weight: 600;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            color: var(--text-primary);
        }
        .fi-title a { color: inherit; text-decoration: none; }
        .fi-title a:hover { color: var(--accent); text-decoration: underline; }
        .fi-meta {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.55rem; color: var(--text-muted);
            display: flex; gap: 0.4rem; margin-top: 0.1rem;
            flex-wrap: wrap;
        }
        .fi-time {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.55rem; color: var(--text-muted);
            white-space: nowrap; flex-shrink: 0; margin-top: 0.05rem;
        }

        /* ── HEATMAP ── */
        .hm-header { display: grid; grid-template-columns: 28px repeat(24, 1fr); gap: 1px; margin-bottom: 2px; }
        .hm-hour {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.48rem; color: var(--text-muted);
            text-align: center; font-weight: 500;
        }
        .hm-grid { display: grid; grid-template-columns: 28px repeat(24, 1fr); gap: 1px; }
        .hm-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.5rem; color: var(--text-muted);
            display: flex; align-items: center; justify-content: flex-end;
            padding-right: 3px; font-weight: 500;
        }
        .hm-cell {
            aspect-ratio: 1; border-radius: 2px;
            background: var(--accent-dim);
            transition: all 0.15s; cursor: default;
        }
        .hm-cell:hover { transform: scale(1.6); z-index: 2; box-shadow: 0 0 6px var(--accent-glow); }
        .hm-cell.l1 { background: rgba(0,255,136,0.10); }
        .hm-cell.l2 { background: rgba(0,255,136,0.22); }
        .hm-cell.l3 { background: rgba(0,255,136,0.36); }
        .hm-cell.l4 { background: rgba(0,212,255,0.48); }
        .hm-cell.l5 { background: rgba(0,255,136,0.68); }
        [data-bs-theme="light"] .hm-cell.l1 { background: rgba(0,136,74,0.08); }
        [data-bs-theme="light"] .hm-cell.l2 { background: rgba(0,136,74,0.18); }
        [data-bs-theme="light"] .hm-cell.l3 { background: rgba(0,136,74,0.30); }
        [data-bs-theme="light"] .hm-cell.l4 { background: rgba(0,119,187,0.40); }
        [data-bs-theme="light"] .hm-cell.l5 { background: rgba(0,136,74,0.55); }

        /* ── ALERTS ── */
        .alert-item {
            display: flex; align-items: center; gap: 0.35rem;
            padding: 0.4rem 0.55rem; border-radius: 5px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.62rem; font-weight: 600; letter-spacing: 0.02em;
            margin-bottom: 0.2rem;
        }
        .alert-item.urgent { background: var(--red-dim); border: 1px solid rgba(255,51,85,0.18); color: var(--red); }
        .alert-item.warn { background: var(--yellow-dim); border: 1px solid rgba(255,170,0,0.18); color: var(--yellow); }
        .alert-item.ok { background: var(--accent-dim); border: 1px solid var(--border-default); color: var(--accent); }

        /* ── SYS ROWS ── */
        .sys-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.38rem 0.5rem; border-radius: 5px;
            background: var(--accent-dim);
            margin-bottom: 0.2rem; transition: background 0.2s;
        }
        .sys-row:hover { background: var(--bg-elevated); }
        .sys-name {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.62rem; font-weight: 600;
            display: flex; align-items: center; gap: 0.35rem;
            color: var(--text-primary);
        }
        .sys-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.5rem; padding: 0.08rem 0.35rem;
            border-radius: 3px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.04em;
        }
        .sys-badge.ok { background: var(--accent-dim); color: var(--accent); border: 1px solid var(--border-default); }

        /* ── WEATHER ── */
        .weather { display: flex; align-items: center; gap: 0.55rem; padding: 0.35rem 0; }
        .weather-icon { font-size: 1.5rem; }
        .weather-temp {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.2rem; font-weight: 800; color: var(--text-primary);
        }
        .weather-desc {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.58rem; color: var(--text-muted);
        }

        /* ── CHART ── */
        .chart-wrap { width: 100%; height: 100%; padding: 0.4rem; }
        .chart-wrap canvas { width: 100% !important; height: 100% !important; }

        /* ── MAP ── */
        .map-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            overflow: hidden;
            display: flex; flex-direction: column;
            transition: background 0.3s;
        }
        .map-panel:hover { border-color: var(--border-default); }
        .map-body { position: relative; height: 420px; }
        #ccMap { width: 100%; height: 420px; background: var(--bg-inset); }
        .map-gradient {
            position: absolute; top: 0; left: 0; right: 0; height: 40px;
            background: linear-gradient(180deg, var(--bg-card), transparent);
            z-index: 400; pointer-events: none;
        }
        .map-legend {
            position: absolute; bottom: 10px; left: 10px; z-index: 500;
            background: var(--bg-card); backdrop-filter: blur(10px);
            border: 1px solid var(--border-strong); border-radius: 5px;
            padding: 0.4rem 0.65rem; display: flex; gap: 0.65rem;
        }
        .map-lg {
            display: flex; align-items: center; gap: 0.25rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.55rem; color: var(--text-muted); font-weight: 500;
        }
        .map-lg-dot { width: 8px; height: 8px; border-radius: 50%; }
        .map-info {
            position: absolute; top: 10px; right: 10px; z-index: 500;
            background: var(--bg-card); backdrop-filter: blur(10px);
            border: 1px solid var(--border-strong); border-radius: 5px;
            padding: 0.35rem 0.6rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.55rem; color: var(--text-accent);
            letter-spacing: 0.05em; font-weight: 600;
        }

        /* ── MAP MARKERS ── */
        .cc-marker {
            width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.6rem; font-weight: 800; color: #fff;
            border: 2px solid rgba(255,255,255,0.85);
            transition: transform 0.2s; cursor: pointer;
            position: relative;
        }
        .cc-marker::after {
            content: ''; position: absolute; inset: -4px;
            border-radius: 50%; border: 1px solid currentColor;
            opacity: 0.25; animation: markerPulse 2s infinite;
        }
        @keyframes markerPulse { 0%, 100% { transform: scale(1); opacity: 0.25; } 50% { transform: scale(1.2); opacity: 0; } }
        .cc-marker:hover { transform: scale(1.2); }
        .cc-marker.urgent { background: var(--red); box-shadow: 0 0 12px var(--red-dim); }
        .cc-marker.high { background: var(--yellow); }
        .cc-marker.medium { background: var(--accent); }
        .cc-marker.low { background: var(--cyan); }

        /* ── BOTTOM ROW ── */
        .bottom-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.6rem; }

        /* ── LEAFLET POPUP ── */
        .leaflet-popup-content-wrapper {
            background: var(--bg-card) !important; color: var(--text-primary) !important;
            border: 1px solid var(--border-strong) !important;
            border-radius: 6px !important; box-shadow: var(--shadow-md) !important;
            font-family: 'Inter', sans-serif;
        }
        .leaflet-popup-tip { background: var(--bg-card) !important; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1200px) {
            .kpi-row { grid-template-columns: repeat(3, 1fr); }
            .mid-row { grid-template-columns: 1fr; }
            .bottom-row { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .kpi-row { grid-template-columns: repeat(2, 1fr); }
            .topbar { padding: 0 0.6rem; }
            .topbar-center { display: none; }
            .bottom-row { grid-template-columns: 1fr; }
            .map-body { height: 300px; }
            #ccMap { height: 300px; }
        }
    </style>
</head>
<body>
<div class="scanlines"></div>

<!-- ═══ TOPBAR ═══ -->
<header class="topbar">
    <div class="brand">
        <img src="/assets/img/balagh-alger-logo.png" alt="Logo">
        <div>
            <div class="brand-name">Balagh NOC</div>
            <div class="brand-sub">Network Operations Center</div>
        </div>
    </div>
    <div class="topbar-center">
        <div class="clock" id="ccClock">--:--:--</div>
        <div class="clock-date" id="ccDate">--</div>
    </div>
    <div class="topbar-right">
        <div class="live-badge">
            <div class="live-dot"></div>
            <span class="live-text">LIVE</span>
        </div>
        <span class="timer" id="ccTimer">T-15</span>
        <button class="tb-btn" onclick="toggleSound()" id="soundBtn" title="Sound"><i class="fas fa-volume-high"></i></button>
        <button class="tb-btn" onclick="toggleTheme()" id="themeBtn" title="Theme"><i class="fas fa-sun" id="themeIcon"></i></button>
        <a href="/dashboard" class="tb-btn" title="Exit"><i class="fas fa-arrow-left"></i></a>
    </div>
</header>

<!-- ═══ MAIN ═══ -->
<div class="main">

    <!-- KPI ROW -->
    <div class="kpi-row">
        <div class="kpi kpi--total">
            <div class="kpi-glow"></div>
            <div class="kpi-icon"><i class="fas fa-file-lines"></i></div>
            <div class="kpi-val" data-key="total">0</div>
            <div class="kpi-lbl">Total</div>
        </div>
        <div class="kpi kpi--pending">
            <div class="kpi-glow"></div>
            <div class="kpi-icon"><i class="fas fa-clock"></i></div>
            <div class="kpi-val" data-key="pending">0</div>
            <div class="kpi-lbl">Pending</div>
        </div>
        <div class="kpi kpi--progress">
            <div class="kpi-glow"></div>
            <div class="kpi-icon"><i class="fas fa-spinner"></i></div>
            <div class="kpi-val" data-key="inProgress">0</div>
            <div class="kpi-lbl">In Progress</div>
        </div>
        <div class="kpi kpi--resolved">
            <div class="kpi-glow"></div>
            <div class="kpi-icon"><i class="fas fa-check-double"></i></div>
            <div class="kpi-val" data-key="resolved">0</div>
            <div class="kpi-lbl">Resolved</div>
        </div>
        <div class="kpi kpi--urgent">
            <div class="kpi-glow"></div>
            <div class="kpi-icon"><i class="fas fa-triangle-exclamation"></i></div>
            <div class="kpi-val" data-key="urgent">0</div>
            <div class="kpi-lbl">Urgent</div>
        </div>
        <div class="kpi kpi--overdue">
            <div class="kpi-glow"></div>
            <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="kpi-val" data-key="overdue">0</div>
            <div class="kpi-lbl">Overdue</div>
        </div>
    </div>

    <!-- MIDDLE ROW -->
    <div class="mid-row">
        <div class="mid-left">
            <!-- LIVE FEED -->
            <div class="panel">
                <div class="panel-head">
                    <h6><i class="fas fa-satellite-dish"></i> Live Feed</h6>
                    <span class="panel-badge" id="feedCount">0</span>
                </div>
                <div class="panel-body">
                    <div class="feed" id="ccFeed">
                        <div class="empty"><i class="fas fa-satellite-dish"></i><span>Connecting...</span></div>
                    </div>
                </div>
            </div>
            <!-- HEATMAP -->
            <div class="panel">
                <div class="panel-head">
                    <h6><i class="fas fa-fire"></i> Activity Heatmap</h6>
                </div>
                <div class="panel-body" id="ccHeatmap">
                    <div class="empty"><i class="fas fa-fire"></i><span>Loading...</span></div>
                </div>
            </div>
        </div>

        <div class="mid-right">
            <!-- PRIORITY CHART -->
            <div class="panel">
                <div class="panel-head">
                    <h6><i class="fas fa-chart-pie"></i> Priority</h6>
                </div>
                <div class="chart-wrap"><canvas id="ccPieChart"></canvas></div>
            </div>
            <!-- ALERTS -->
            <div class="panel">
                <div class="panel-head">
                    <h6><i class="fas fa-bell"></i> Alerts</h6>
                </div>
                <div class="panel-body" id="ccAlerts">
                    <div class="empty"><i class="fas fa-bell"></i><span>Loading...</span></div>
                </div>
            </div>
            <!-- TODAY -->
            <div class="panel panel-tiny">
                <div class="panel-head">
                    <h6><i class="fas fa-calendar-day"></i> Today</h6>
                </div>
                <div class="panel-body">
                    <div class="sys-row">
                        <span class="sys-name"><i class="fas fa-plus-circle" style="color:var(--cyan)"></i> New</span>
                        <span class="kpi-val" id="kpiToday" style="font-size:1rem;color:var(--cyan)">0</span>
                    </div>
                    <div class="sys-row">
                        <span class="sys-name"><i class="fas fa-check" style="color:var(--accent)"></i> Done</span>
                        <span class="kpi-val" id="kpiResolvedToday" style="font-size:1rem;color:var(--accent)">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TACTICAL MAP -->
    <div class="map-panel">
        <div class="panel-head">
            <h6><i class="fas fa-map-marked-alt"></i> Tactical Map</h6>
            <span class="panel-badge" id="mapCount">0 markers</span>
        </div>
        <div class="map-body">
            <div class="map-gradient"></div>
            <div id="ccMap"></div>
            <div class="map-legend">
                <div class="map-lg"><div class="map-lg-dot" style="background:var(--red)"></div> Urgent</div>
                <div class="map-lg"><div class="map-lg-dot" style="background:var(--yellow)"></div> High</div>
                <div class="map-lg"><div class="map-lg-dot" style="background:var(--accent)"></div> Medium</div>
                <div class="map-lg"><div class="map-lg-dot" style="background:var(--cyan)"></div> Low</div>
            </div>
            <div class="map-info" id="mapStats">--</div>
        </div>
    </div>

    <!-- BOTTOM ROW -->
    <div class="bottom-row">
        <div class="panel">
            <div class="panel-head"><h6><i class="fas fa-server"></i> Systems</h6></div>
            <div class="panel-body">
                <div class="sys-row"><span class="sys-name"><i class="fas fa-database" style="color:var(--cyan)"></i> MySQL</span><span class="sys-badge ok">Online</span></div>
                <div class="sys-row"><span class="sys-name"><i class="fas fa-server" style="color:var(--accent)"></i> PHP <?= PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION ?></span><span class="sys-badge ok">Online</span></div>
                <div class="sys-row"><span class="sys-name"><i class="fas fa-network-wired" style="color:var(--purple)"></i> Apache2</span><span class="sys-badge ok">Online</span></div>
                <div class="sys-row"><span class="sys-name"><i class="fas fa-envelope" style="color:var(--yellow)"></i> SMTP</span><span class="sys-badge ok">Online</span></div>
                <div class="sys-row"><span class="sys-name"><i class="fas fa-globe" style="color:var(--cyan)"></i> Domain</span><span class="sys-badge ok">Online</span></div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-head"><h6><i class="fas fa-cloud-sun"></i> Weather</h6></div>
            <div class="panel-body">
                <div class="weather">
                    <div class="weather-icon" style="color:var(--yellow)"><i class="fas fa-cloud-sun"></i></div>
                    <div>
                        <div class="weather-temp" id="ccTemp">--°C</div>
                        <div class="weather-desc" id="ccWeatherDesc">Connecting...</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-head"><h6><i class="fas fa-chart-line"></i> Metrics</h6></div>
            <div class="panel-body">
                <div class="sys-row"><span class="sys-name"><i class="fas fa-percentage" style="color:var(--cyan)"></i> Resolution</span><span class="sys-badge ok" id="ccResRate">--</span></div>
                <div class="sys-row"><span class="sys-name"><i class="fas fa-tachometer-alt" style="color:var(--yellow)"></i> SLA</span><span class="sys-badge ok" id="ccSla">--</span></div>
                <div class="sys-row"><span class="sys-name"><i class="fas fa-clock" style="color:var(--accent)"></i> Avg Time</span><span class="sys-badge ok" id="ccAvgTime">--</span></div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-head"><h6><i class="fas fa-layer-group"></i> By Status</h6></div>
            <div class="panel-body" id="ccByStatus">
                <div class="empty"><i class="fas fa-layer-group"></i><span>Loading...</span></div>
            </div>
        </div>
    </div>
</div>

<script>
/* ── CLOCK ── */
function updateClock() {
    const n = new Date();
    document.getElementById('ccClock').textContent = n.toLocaleTimeString('fr-DZ', { hour12: false });
    document.getElementById('ccDate').textContent = n.toLocaleDateString('<?= $isRtl ? "ar-DZ" : "fr-DZ" ?>', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}
setInterval(updateClock, 1000); updateClock();

/* ── SOUND ── */
let soundOn = true;
function toggleSound() {
    soundOn = !soundOn;
    document.getElementById('soundBtn').innerHTML = soundOn ? '<i class="fas fa-volume-high"></i>' : '<i class="fas fa-volume-xmark"></i>';
}
function beep() {
    if (!soundOn) return;
    try {
        const c = new (window.AudioContext || window.webkitAudioContext)();
        const o = c.createOscillator(), g = c.createGain();
        o.connect(g); g.connect(c.destination);
        o.type = 'sine'; o.frequency.value = 880;
        g.gain.setValueAtTime(0.12, c.currentTime);
        g.gain.exponentialRampToValueAtTime(0.001, c.currentTime + 0.4);
        o.start(); o.stop(c.currentTime + 0.4);
    } catch (e) {}
}

/* ── THEME ── */
function getTheme() { return document.documentElement.getAttribute('data-bs-theme') || 'dark'; }
function toggleTheme() {
    const h = document.documentElement, n = getTheme() === 'dark' ? 'light' : 'dark';
    h.setAttribute('data-bs-theme', n);
    localStorage.setItem('balagh-theme', n);
    document.getElementById('themeIcon').className = 'fas ' + (n === 'dark' ? 'fa-sun' : 'fa-moon');
    updateMapTiles();
    if (pieChart) {
        const tc = getTheme() === 'dark' ? '#8899aa' : '#556677';
        pieChart.options.plugins.legend.labels.color = tc;
        pieChart.update('none');
    }
}
(function () { document.getElementById('themeIcon').className = 'fas ' + (getTheme() === 'dark' ? 'fa-sun' : 'fa-moon'); })();

/* ── MAP ── */
const ccMap = L.map('ccMap', { center: [36.7538, 3.0588], zoom: 11, zoomControl: false, attributionControl: false });
L.control.zoom({ position: 'topright' }).addTo(ccMap);
const darkTiles = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { maxZoom: 19, subdomains: 'abcd' });
const lightTiles = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19, subdomains: 'abcd' });
let curTiles = getTheme() === 'dark' ? darkTiles : lightTiles;
curTiles.addTo(ccMap);
function updateMapTiles() {
    ccMap.removeLayer(curTiles);
    curTiles = getTheme() === 'dark' ? darkTiles : lightTiles;
    curTiles.addTo(ccMap);
}
let mapMarkers = [];

function renderMap(data) {
    mapMarkers.forEach(m => ccMap.removeLayer(m)); mapMarkers = [];
    if (!data || data.length === 0) {
        document.getElementById('mapCount').textContent = '0 markers';
        document.getElementById('mapStats').textContent = 'NO DATA';
        ccMap.invalidateSize();
        return;
    }
    const prioLabel = { urgent: '!', high: 'H', medium: 'M', low: 'L' };
    data.forEach(r => {
        if (!r.latitude || !r.longitude) return;
        const ico = L.divIcon({
            className: '',
            html: '<div class="cc-marker ' + r.priority + '">' + (prioLabel[r.priority] || '?') + '</div>',
            iconSize: [28, 28], iconAnchor: [14, 14]
        });
        const m = L.marker([r.latitude, r.longitude], { icon: ico }).addTo(ccMap);
        m.bindPopup(
            '<div style="font-family:Inter,sans-serif;font-size:11px;min-width:160px">' +
            '<div style="font-weight:700;margin-bottom:3px;color:var(--text-accent)">' + r.tracking_code + '</div>' +
            '<div style="color:var(--text-secondary);margin-bottom:4px">' + r.title + '</div>' +
            '<div style="display:flex;gap:6px;font-size:9px">' +
            '<span style="color:var(--cyan)">' + r.category_name + '</span>' +
            '<span style="color:var(--yellow)">' + r.priority + '</span>' +
            '</div></div>'
        );
        mapMarkers.push(m);
    });
    document.getElementById('mapCount').textContent = mapMarkers.length + ' markers';
    document.getElementById('mapStats').textContent = mapMarkers.length + ' TARGETS';
    if (mapMarkers.length > 0) {
        const g = L.featureGroup(mapMarkers);
        ccMap.fitBounds(g.getBounds().pad(0.12));
    }
    setTimeout(() => ccMap.invalidateSize(), 200);
}

/* ── DATA ── */
let prevTotal = 0, pieChart = null;

async function fetchData() {
    try {
        const res = await fetch('/api/command-center', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) throw new Error();
        const d = await res.json();

        animKpi('total', d.total); animKpi('pending', d.pending);
        animKpi('inProgress', d.inProgress); animKpi('resolved', d.resolved);
        animKpi('urgent', d.urgent); animKpi('overdue', d.overdue);
        document.getElementById('kpiToday').textContent = d.today;
        document.getElementById('kpiResolvedToday').textContent = d.resolved;

        if (prevTotal > 0 && d.total > prevTotal) {
            beep();
            const u = document.querySelector('.kpi--urgent');
            if (u) { u.classList.add('pulse'); setTimeout(() => u.classList.remove('pulse'), 2500); }
        }
        prevTotal = d.total;

        renderFeed(d.recent);
        renderAlerts(d);
        renderHeatmap(d.heatData);
        renderPieChart(d.byPriority);
        renderByStatus(d.byStatus);
        renderMap(d.mapData || []);

        document.getElementById('feedCount').textContent = d.recent.length;
        if (d.total > 0) {
            document.getElementById('ccResRate').textContent = Math.round(d.resolved / d.total * 100) + '%';
            document.getElementById('ccSla').textContent = Math.round(d.resolved / d.total * 100) + '%';
        }
        const avgDays = d.total > 0 ? (d.pending + d.inProgress > 0 ? Math.round((d.total - d.resolved) * 0.5) : '<1') : '0';
        document.getElementById('ccAvgTime').textContent = avgDays + 'd';
    } catch (e) { console.error('NOC error:', e); }

    let cd = 15;
    const t = setInterval(() => {
        cd--;
        document.getElementById('ccTimer').textContent = 'T-' + cd;
        if (cd <= 0) { clearInterval(t); fetchData(); }
    }, 1000);
}

function animKpi(k, v) {
    const el = document.querySelector('[data-key="' + k + '"]');
    if (!el) return;
    const c = parseInt(el.textContent) || 0;
    if (c === v) return;
    el.closest('.kpi').classList.add('pulse');
    setTimeout(() => el.closest('.kpi').classList.remove('pulse'), 500);
    if (c < v) {
        let s = Math.max(1, Math.ceil((v - c) / 15)), x = c;
        const iv = setInterval(() => {
            x += s; if (x >= v) { x = v; clearInterval(iv); }
            el.textContent = x.toLocaleString('fr-DZ');
        }, 25);
    } else {
        el.textContent = v.toLocaleString('fr-DZ');
    }
}

function renderFeed(reports) {
    const f = document.getElementById('ccFeed');
    if (!reports || reports.length === 0) {
        f.innerHTML = '<div class="empty"><i class="fas fa-satellite-dish"></i><span>No reports</span></div>';
        return;
    }
    const sc = { submitted: 'var(--cyan)', acknowledged: 'var(--cyan)', assigned: 'var(--purple)', in_progress: 'var(--yellow)', resolved: 'var(--accent)', closed: 'var(--accent)', rejected: 'var(--red)' };
    const si = { submitted: 'fa-plus', acknowledged: 'fa-check', assigned: 'fa-user', in_progress: 'fa-spinner', resolved: 'fa-check-double', closed: 'fa-lock', rejected: 'fa-times' };
    const pc = { urgent: 'var(--red)', high: 'var(--yellow)', medium: 'var(--accent)', low: 'var(--cyan)' };
    f.innerHTML = reports.map((r, i) =>
        '<div class="fi' + (i === 0 ? ' new' : '') + '">' +
        '<div class="fi-icon" style="background:' + (sc[r.status] || 'var(--accent)') + '18;color:' + (sc[r.status] || 'var(--accent)') + '"><i class="fas ' + (si[r.status] || 'fa-bell') + '"></i></div>' +
        '<div class="fi-body"><div class="fi-title"><a href="/reports/' + r.id + '">' + r.tracking_code + '</a> — ' + esc(r.title) + '</div>' +
        '<div class="fi-meta"><span style="color:' + (pc[r.priority] || 'var(--text-muted)') + '"><i class="fas fa-flag"></i> ' + r.priority + '</span>' +
        '<span><i class="fas fa-tag"></i> ' + esc(r.category_name) + '</span>' +
        '<span><i class="fas fa-map-pin"></i> ' + esc(r.daira_name) + '</span></div></div>' +
        '<div class="fi-time">' + ago(r.created_at) + '</div></div>'
    ).join('');
}

function renderAlerts(d) {
    const a = [];
    if (d.urgent > 0) a.push('<div class="alert-item urgent"><i class="fas fa-exclamation-triangle"></i> ' + d.urgent + ' URGENT</div>');
    if (d.overdue > 0) a.push('<div class="alert-item warn"><i class="fas fa-clock"></i> ' + d.overdue + ' OVERDUE</div>');
    if (d.pending > 50) a.push('<div class="alert-item warn"><i class="fas fa-inbox"></i> ' + d.pending + ' QUEUE</div>');
    if (a.length === 0) a.push('<div class="alert-item ok"><i class="fas fa-shield-halved"></i> ALL SYSTEMS NOMINAL</div>');
    document.getElementById('ccAlerts').innerHTML = a.join('');
}

function renderHeatmap(data) {
    const el = document.getElementById('ccHeatmap');
    if (!data || data.length === 0) {
        el.innerHTML = '<div class="empty"><i class="fas fa-fire"></i><span>No data</span></div>';
        return;
    }
    const days = ['', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const map = {}; let mx = 1;
    data.forEach(d => { map[d.day + '_' + d.hour] = d.count; if (d.count > mx) mx = d.count; });
    let h = '<div class="hm-header"><div></div>';
    for (let i = 0; i < 24; i++) h += '<div class="hm-hour">' + i + '</div>';
    h += '</div><div class="hm-grid">';
    for (let d = 1; d <= 7; d++) {
        h += '<div class="hm-label">' + days[d] + '</div>';
        for (let i = 0; i < 24; i++) {
            const c = map[d + '_' + i] || 0;
            const l = c === 0 ? '' : c < mx * 0.2 ? 'l1' : c < mx * 0.4 ? 'l2' : c < mx * 0.6 ? 'l3' : c < mx * 0.8 ? 'l4' : 'l5';
            h += '<div class="hm-cell ' + l + '" title="' + days[d] + ' ' + i + ':00 — ' + c + '"></div>';
        }
    }
    h += '</div>';
    el.innerHTML = h;
}

function renderPieChart(data) {
    const labels = data.map(d => d.priority), vals = data.map(d => parseInt(d.count));
    const colors = { urgent: '#ff3355', high: '#ffaa00', medium: '#00ff88', low: '#00d4ff' };
    const bg = labels.map(l => colors[l] || '#506070');
    const tc = getTheme() === 'dark' ? '#8899aa' : '#556677';
    if (pieChart) {
        pieChart.data.labels = labels;
        pieChart.data.datasets[0].data = vals;
        pieChart.data.datasets[0].backgroundColor = bg;
        pieChart.options.plugins.legend.labels.color = tc;
        pieChart.update('none');
    } else {
        pieChart = new Chart(document.getElementById('ccPieChart'), {
            type: 'doughnut',
            data: { labels, datasets: [{ data: vals, backgroundColor: bg, borderWidth: 0 }] },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '68%',
                plugins: { legend: { position: 'bottom', labels: { color: tc, font: { family: 'JetBrains Mono', size: 10 }, padding: 5, usePointStyle: true } } },
                animation: { animateRotate: true, duration: 700 }
            }
        });
    }
}

function renderByStatus(data) {
    const el = document.getElementById('ccByStatus');
    if (!data || data.length === 0) {
        el.innerHTML = '<div class="empty"><i class="fas fa-layer-group"></i><span>No data</span></div>';
        return;
    }
    const labels = { submitted: 'Submitted', acknowledged: 'Acknowledged', assigned: 'Assigned', in_progress: 'In Progress', validated: 'Validated', resolved: 'Resolved', closed: 'Closed', rejected: 'Rejected' };
    const colors = { submitted: 'var(--cyan)', acknowledged: 'var(--cyan)', assigned: 'var(--purple)', in_progress: 'var(--yellow)', validated: 'var(--accent)', resolved: 'var(--accent)', closed: 'var(--text-muted)', rejected: 'var(--red)' };
    const icons = { submitted: 'fa-inbox', acknowledged: 'fa-eye', assigned: 'fa-user', in_progress: 'fa-spinner', validated: 'fa-check', resolved: 'fa-check-double', closed: 'fa-lock', rejected: 'fa-times' };
    el.innerHTML = data.map(d =>
        '<div class="sys-row"><span class="sys-name"><i class="fas ' + (icons[d.status] || 'fa-circle') + '" style="color:' + (colors[d.status] || 'var(--text-muted)') + '"></i> ' + (labels[d.status] || d.status) + '</span>' +
        '<span class="sys-badge ok" style="color:' + (colors[d.status] || 'var(--accent)') + '">' + d.count + '</span></div>'
    ).join('');
}

function ago(s) {
    const n = new Date(), d = new Date(s), sc = Math.floor((n - d) / 1000);
    if (isNaN(sc)) return '';
    if (sc < 60) return sc + 's';
    if (sc < 3600) return Math.floor(sc / 60) + 'm';
    if (sc < 86400) return Math.floor(sc / 3600) + 'h';
    return Math.floor(sc / 86400) + 'd';
}
function esc(s) { if (!s) return ''; const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

/* ── WEATHER ── */
async function fetchWeather() {
    try {
        const r = await fetch('https://wttr.in/Alger?format=j1');
        const d = await r.json();
        const c = d.current_condition[0], code = parseInt(c.weatherCode);
        let ic = 'fa-cloud-sun';
        if (code <= 116) ic = 'fa-sun';
        else if (code <= 299) ic = 'fa-cloud';
        else if (code <= 399) ic = 'fa-smog';
        else if (code <= 599) ic = 'fa-cloud-rain';
        else if (code <= 699) ic = 'fa-snowflake';
        else if (code <= 799) ic = 'fa-wind';
        document.getElementById('ccTemp').textContent = c.temp_C + '°C';
        document.getElementById('ccWeatherDesc').textContent = c.weatherDesc[0].value;
        document.querySelector('.weather-icon i').className = 'fas ' + ic;
    } catch (e) {
        document.getElementById('ccWeatherDesc').textContent = 'Offline';
    }
}

fetchData();
fetchWeather();
setInterval(fetchWeather, 600000);
</script>
</body>
</html>
