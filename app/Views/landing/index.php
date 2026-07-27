<?php \App\Helpers\I18n::init();
$lang = \App\Helpers\I18n::getLang();
$isRtl = \App\Helpers\I18n::isRtl();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('app.full_name') ?> — <?= __('app.subtitle') ?></title>
    <meta name="description" content="<?= __('landing.hero_subtitle') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary: #6366f1; --primary-dark: #4f46e5; --primary-light: #818cf8;
            --accent: #06b6d4; --accent-light: #22d3ee;
            --green: #10b981; --amber: #f59e0b; --red: #ef4444; --pink: #ec4899;
            --bg: #0f172a; --bg-2: #1e293b; --bg-3: #334155;
            --text: #f1f5f9; --text-muted: #94a3b8; --border: #334155;
            --card-bg: rgba(30,41,59,0.7);
            --glass: rgba(15,23,42,0.6);
            --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #0c1222 100%);
            --gradient-accent: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
            --gradient-warm: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            --gradient-cool: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
            --gradient-purple: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
            --font: 'Inter', 'Noto Sans Arabic', sans-serif;
            --shadow-lg: 0 20px 60px rgba(0,0,0,0.3);
            --shadow-glow: 0 0 40px rgba(99,102,241,0.15);
        }
        [data-bs-theme="light"] {
            --bg: #f8fafc; --bg-2: #ffffff; --bg-3: #f1f5f9;
            --text: #0f172a; --text-muted: #64748b; --border: #e2e8f0;
            --card-bg: rgba(255,255,255,0.85); --glass: rgba(248,250,252,0.8);
            --gradient-hero: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 40%, #f0fdfa 100%);
            --shadow-lg: 0 20px 60px rgba(0,0,0,0.08);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font); background: var(--bg); color: var(--text); overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; }
        [dir="rtl"] { font-family: 'Noto Sans Arabic', 'Inter', sans-serif; }

        /* ===== PARTICLES CANVAS ===== */
        #particles-canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; }

        /* ===== NAVBAR ===== */
        .landing-nav { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 1rem 0; transition: all 0.4s cubic-bezier(0.16,1,0.3,1); background: transparent; }
        .landing-nav.scrolled { padding: 0.5rem 0; background: var(--glass); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border-bottom: 1px solid rgba(255,255,255,0.06); box-shadow: 0 4px 30px rgba(0,0,0,0.2); }
        [data-bs-theme="light"] .landing-nav.scrolled { border-bottom-color: rgba(0,0,0,0.06); }
        .nav-brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 1.15rem; }
        .nav-brand-icon { width: 40px; height: 40px; border-radius: 12px; background: var(--gradient-accent); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1rem; box-shadow: 0 4px 15px rgba(99,102,241,0.3); transition: transform 0.3s; }
        .nav-brand:hover .nav-brand-icon { transform: rotate(-10deg) scale(1.05); }
        .nav-links { display: flex; align-items: center; gap: 0.35rem; }
        .nav-links a { padding: 0.45rem 0.9rem; border-radius: 8px; font-size: 0.82rem; font-weight: 500; color: var(--text-muted); transition: all 0.25s; }
        .nav-links a:hover { color: var(--text); background: rgba(99,102,241,0.1); }
        .btn-nav-primary { background: var(--gradient-accent) !important; color: #fff !important; padding: 0.5rem 1.2rem !important; font-weight: 700 !important; box-shadow: 0 4px 15px rgba(99,102,241,0.3); border-radius: 10px !important; }
        .btn-nav-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.45) !important; }
        .btn-nav-ghost { background: transparent !important; border: 1.5px solid var(--border) !important; color: var(--text) !important; padding: 0.45rem 1.1rem !important; }
        .btn-nav-ghost:hover { border-color: var(--primary) !important; color: var(--primary) !important; background: rgba(99,102,241,0.08) !important; }
        .lang-toggle { width: 38px; height: 38px; border-radius: 10px; border: 1.5px solid var(--border); background: transparent; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.72rem; font-weight: 700; transition: all 0.25s; }
        .lang-toggle:hover { border-color: var(--primary); color: var(--primary); transform: scale(1.05); }
        .mobile-menu-btn { width: 40px; height: 40px; border-radius: 10px; border: 1.5px solid var(--border); background: transparent; color: var(--text); cursor: pointer; display: none; align-items: center; justify-content: center; font-size: 1.1rem; transition: all 0.25s; }
        .mobile-menu-btn:hover { border-color: var(--primary); }

        /* ===== HERO ===== */
        .hero { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--gradient-hero); position: relative; overflow: hidden; padding: 120px 0 80px; }
        .hero::before { content: ''; position: absolute; top: -40%; right: -25%; width: 900px; height: 900px; border-radius: 50%; background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 65%); animation: heroFloat 12s ease-in-out infinite; }
        .hero::after { content: ''; position: absolute; bottom: -35%; left: -20%; width: 700px; height: 700px; border-radius: 50%; background: radial-gradient(circle, rgba(6,182,212,0.12) 0%, transparent 65%); animation: heroFloat 15s ease-in-out infinite reverse; }
        @keyframes heroFloat { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(40px,-40px) scale(1.05); } }
        .hero-content { position: relative; z-index: 2; text-align: center; max-width: 850px; margin: 0 auto; }
        .hero-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(99,102,241,0.12); border: 1px solid rgba(99,102,241,0.25); padding: 0.45rem 1.3rem; border-radius: 50px; font-size: 0.8rem; font-weight: 600; color: var(--primary-light); margin-bottom: 1.5rem; backdrop-filter: blur(10px); animation: badgePulse 3s ease-in-out infinite; }
        @keyframes badgePulse { 0%,100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.2); } 50% { box-shadow: 0 0 20px 4px rgba(99,102,241,0.1); } }
        .hero h1 { font-size: clamp(2.2rem, 5.5vw, 4rem); font-weight: 900; line-height: 1.12; margin-bottom: 1.3rem; letter-spacing: -0.025em; }
        .hero h1 .gradient-text { background: var(--gradient-accent); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; position: relative; }
        .hero h1 .gradient-text::after { content: ''; position: absolute; bottom: -4px; left: 0; right: 0; height: 4px; background: var(--gradient-accent); border-radius: 4px; opacity: 0.5; animation: underlineGlow 2s ease-in-out infinite; }
        @keyframes underlineGlow { 0%,100% { opacity: 0.3; transform: scaleX(0.8); } 50% { opacity: 0.6; transform: scaleX(1); } }
        .hero p { font-size: 1.1rem; color: var(--text-muted); max-width: 620px; margin: 0 auto 2.2rem; line-height: 1.75; }
        .hero-buttons { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn-hero { padding: 0.9rem 2.2rem; border-radius: 14px; font-weight: 700; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 8px; transition: all 0.35s cubic-bezier(0.16,1,0.3,1); border: none; cursor: pointer; position: relative; overflow: hidden; }
        .btn-hero::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent); opacity: 0; transition: opacity 0.3s; }
        .btn-hero:hover::before { opacity: 1; }
        .btn-hero-primary { background: var(--gradient-accent); color: #fff; box-shadow: 0 6px 25px rgba(99,102,241,0.35); }
        .btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 10px 40px rgba(99,102,241,0.5); }
        .btn-hero-outline { background: var(--glass); color: var(--text); border: 2px solid var(--border); padding: 0.85rem 2rem; backdrop-filter: blur(10px); }
        .btn-hero-outline:hover { border-color: var(--primary); color: var(--primary); background: rgba(99,102,241,0.1); transform: translateY(-2px); }
        .hero-showcase { margin-top: 3rem; position: relative; max-width: 700px; margin-left: auto; margin-right: auto; }
        .hero-showcase-inner { background: var(--card-bg); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; backdrop-filter: blur(16px); box-shadow: var(--shadow-lg), var(--shadow-glow); }
        .hero-showcase-top { background: rgba(0,0,0,0.3); padding: 0.6rem 1rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .hero-showcase-dot { width: 10px; height: 10px; border-radius: 50%; }
        .hero-showcase-img { width: 100%; height: 260px; object-fit: cover; display: block; }
        .hero-showcase-bar { padding: 0.8rem 1rem; display: flex; align-items: center; gap: 0.75rem; }
        .hero-showcase-input { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid var(--border); border-radius: 10px; padding: 0.5rem 1rem; color: var(--text-muted); font-size: 0.82rem; }

        /* ===== STATS BANNER ===== */
        .stats-banner { background: var(--bg-2); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 3rem 0; position: relative; }
        .stats-banner::before { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(99,102,241,0.05) 0%, rgba(6,182,212,0.05) 50%, rgba(99,102,241,0.05) 100%); }
        .stat-item { text-align: center; position: relative; z-index: 1; }
        .stat-icon { width: 52px; height: 52px; border-radius: 14px; margin: 0 auto 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: transform 0.3s; }
        .stat-item:hover .stat-icon { transform: scale(1.1) rotate(-5deg); }
        .stat-number { font-size: 2.4rem; font-weight: 900; letter-spacing: -0.02em; background: var(--gradient-accent); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .stat-label { font-size: 0.82rem; color: var(--text-muted); margin-top: 0.2rem; font-weight: 500; }

        /* ===== SECTIONS COMMON ===== */
        section { padding: 6rem 0; position: relative; }
        .section-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--primary-light); margin-bottom: 0.75rem; }
        .section-title { font-size: clamp(1.6rem, 3vw, 2.5rem); font-weight: 800; margin-bottom: 0.75rem; letter-spacing: -0.015em; }
        .section-subtitle { font-size: 1rem; color: var(--text-muted); max-width: 600px; line-height: 1.7; }
        .glow-line { width: 80px; height: 4px; border-radius: 4px; margin: 0 auto 2rem; background: var(--gradient-accent); opacity: 0.6; }

        /* ===== PARTNERS ===== */
        .partners-section { padding: 3rem 0; overflow: hidden; }
        .partners-track { display: flex; gap: 3rem; align-items: center; animation: marquee 30s linear infinite; }
        .partners-track:hover { animation-play-state: paused; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        [dir="rtl"] .partners-track { animation-name: marqueeRtl; }
        @keyframes marqueeRtl { 0% { transform: translateX(0); } 100% { transform: translateX(50%); } }
        .partner-logo { flex-shrink: 0; height: 48px; padding: 0.5rem 1.5rem; border-radius: 12px; background: var(--card-bg); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 0.78rem; font-weight: 600; color: var(--text-muted); transition: all 0.3s; filter: grayscale(0.5); opacity: 0.7; white-space: nowrap; }
        .partner-logo:hover { filter: grayscale(0); opacity: 1; border-color: var(--primary); color: var(--text); transform: translateY(-2px); box-shadow: 0 4px 15px rgba(99,102,241,0.15); }
        .partner-logo i { font-size: 1.1rem; }

        /* ===== STEPS ===== */
        .steps-wrapper { position: relative; }
        .steps-wrapper::before { content: ''; position: absolute; top: 40px; left: 15%; right: 15%; height: 3px; background: var(--gradient-accent); border-radius: 3px; opacity: 0.2; }
        .step-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 20px; padding: 2.2rem 1.5rem; text-align: center; transition: all 0.4s cubic-bezier(0.16,1,0.3,1); position: relative; backdrop-filter: blur(12px); z-index: 1; }
        .step-card:hover { transform: translateY(-8px); border-color: var(--primary); box-shadow: 0 12px 40px rgba(99,102,241,0.15); }
        .step-number { width: 64px; height: 64px; border-radius: 18px; margin: 0 auto 1.2rem; background: var(--gradient-accent); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 900; box-shadow: 0 8px 25px rgba(99,102,241,0.3); transition: transform 0.3s; }
        .step-card:hover .step-number { transform: scale(1.1) rotate(-5deg); }
        .step-card h5 { font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem; }
        .step-card p { font-size: 0.82rem; color: var(--text-muted); line-height: 1.65; margin: 0; }

        /* ===== ANOMALY CARDS ===== */
        .anomaly-card { border-radius: 20px; overflow: hidden; border: 1px solid var(--border); background: var(--card-bg); transition: all 0.4s cubic-bezier(0.16,1,0.3,1); backdrop-filter: blur(12px); position: relative; }
        .anomaly-card::before { content: ''; position: absolute; inset: 0; border-radius: 20px; background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(6,182,212,0.1)); opacity: 0; transition: opacity 0.4s; z-index: 1; pointer-events: none; }
        .anomaly-card:hover { transform: translateY(-8px); box-shadow: 0 16px 50px rgba(0,0,0,0.3); border-color: var(--primary); }
        .anomaly-card:hover::before { opacity: 1; }
        .anomaly-img-wrapper { height: 210px; overflow: hidden; position: relative; }
        .anomaly-img { height: 100%; width: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.16,1,0.3,1); }
        .anomaly-card:hover .anomaly-img { transform: scale(1.08); }
        .anomaly-img-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 50%); }
        .anomaly-badge { position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.5); backdrop-filter: blur(10px); padding: 0.3rem 0.75rem; border-radius: 8px; font-size: 0.7rem; font-weight: 600; color: #fff; z-index: 2; }
        .anomaly-body { padding: 1.3rem; position: relative; z-index: 2; }
        .anomaly-body h6 { font-weight: 700; font-size: 0.95rem; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem; }
        .anomaly-body p { font-size: 0.8rem; color: var(--text-muted); margin: 0; line-height: 1.55; }

        /* ===== MAP ===== */
        .map-section #dairaMap { height: 450px; border-radius: 20px; border: 1px solid var(--border); z-index: 1; }
        .map-section .leaflet-popup-content-wrapper { border-radius: 12px; }

        /* ===== GALLERY ===== */
        .gallery-section { overflow: hidden; }
        .gallery-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; max-width: 1000px; margin: 0 auto; }
        .gallery-item { border-radius: 16px; overflow: hidden; border: 1px solid var(--border); background: var(--card-bg); transition: all 0.4s; }
        .gallery-item:hover { transform: translateY(-5px) scale(1.02); box-shadow: var(--shadow-lg); }
        .gallery-item img { width: 100%; height: 200px; object-fit: cover; display: block; }
        .gallery-item:nth-child(2) { transform: translateY(20px); }
        .gallery-item:nth-child(2):hover { transform: translateY(15px) scale(1.02); }

        /* ===== BEFORE / AFTER ===== */
        .ba-card { border-radius: 20px; overflow: hidden; border: 1px solid var(--border); background: var(--card-bg); backdrop-filter: blur(12px); position: relative; }
        .ba-container { position: relative; height: 280px; overflow: hidden; cursor: ew-resize; }
        .ba-container img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
        .ba-after { clip-path: inset(0 0 0 50%); }
        .ba-slider { position: absolute; top: 0; bottom: 0; width: 4px; background: #fff; left: 50%; z-index: 5; box-shadow: 0 0 10px rgba(0,0,0,0.5); pointer-events: none; }
        .ba-slider::after { content: '⇔'; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 36px; height: 36px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; color: #333; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        .ba-labels { position: absolute; bottom: 10px; left: 0; right: 0; display: flex; justify-content: space-between; padding: 0 12px; z-index: 6; pointer-events: none; }
        .ba-label { background: rgba(0,0,0,0.6); backdrop-filter: blur(6px); color: #fff; font-size: 0.68rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em; }
        .ba-body { padding: 1.2rem; }
        .ba-body h6 { font-weight: 700; font-size: 0.9rem; margin-bottom: 0.25rem; }
        .ba-body p { font-size: 0.78rem; color: var(--text-muted); margin: 0; }

        /* ===== LIVE COUNTERS ===== */
        .live-section { background: var(--bg-2); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
        .live-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 20px; padding: 2rem; text-align: center; backdrop-filter: blur(12px); transition: all 0.3s; }
        .live-card:hover { border-color: var(--primary); transform: translateY(-3px); }
        .live-icon { width: 56px; height: 56px; border-radius: 16px; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .live-number { font-size: 2.8rem; font-weight: 900; transition: all 0.3s; }
        .live-label { font-size: 0.82rem; color: var(--text-muted); margin-top: 0.2rem; }
        .live-pulse { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--green); margin-<?= $isRtl ? 'right' : 'left' ?>: 6px; animation: livePulse 2s infinite; vertical-align: middle; }
        @keyframes livePulse { 0%,100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); } 50% { box-shadow: 0 0 0 8px rgba(16,185,129,0); } }

        /* ===== FEATURES ===== */
        .feature-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 20px; padding: 2rem; transition: all 0.4s cubic-bezier(0.16,1,0.3,1); backdrop-filter: blur(12px); position: relative; overflow: hidden; }
        .feature-card::after { content: ''; position: absolute; top: -50%; right: -50%; width: 100%; height: 100%; background: radial-gradient(circle, rgba(99,102,241,0.08) 0%, transparent 70%); transition: opacity 0.4s; opacity: 0; }
        .feature-card:hover { border-color: var(--primary); transform: translateY(-5px); }
        .feature-card:hover::after { opacity: 1; }
        .feature-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; margin-bottom: 1.2rem; transition: transform 0.3s; }
        .feature-card:hover .feature-icon { transform: scale(1.1) rotate(-5deg); }
        .feature-card h6 { font-weight: 700; font-size: 1rem; margin-bottom: 0.45rem; position: relative; z-index: 1; }
        .feature-card p { font-size: 0.82rem; color: var(--text-muted); margin: 0; line-height: 1.65; position: relative; z-index: 1; }

        /* ===== FAQ ===== */
        .faq-item { background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; margin-bottom: 0.75rem; overflow: hidden; backdrop-filter: blur(12px); transition: all 0.3s; }
        .faq-item:hover { border-color: var(--primary); }
        .faq-item.open { border-color: var(--primary); }
        .faq-q { padding: 1.1rem 1.5rem; display: flex; align-items: center; justify-content: space-between; cursor: pointer; font-weight: 600; font-size: 0.92rem; gap: 1rem; transition: background 0.2s; }
        .faq-q:hover { background: rgba(99,102,241,0.05); }
        .faq-q i { font-size: 0.7rem; color: var(--primary-light); transition: transform 0.3s; flex-shrink: 0; }
        .faq-item.open .faq-q i { transform: rotate(180deg); }
        .faq-a { max-height: 0; overflow: hidden; transition: max-height 0.4s cubic-bezier(0.16,1,0.3,1), padding 0.3s; }
        .faq-item.open .faq-a { max-height: 300px; }
        .faq-a-inner { padding: 0 1.5rem 1.2rem; font-size: 0.85rem; color: var(--text-muted); line-height: 1.7; }

        /* ===== APP TEASER ===== */
        .app-section { overflow: hidden; }
        .app-mockup { position: relative; max-width: 320px; }
        .phone-frame { width: 280px; height: 560px; background: #111; border-radius: 40px; padding: 12px; border: 3px solid #333; box-shadow: 0 30px 80px rgba(0,0,0,0.4); position: relative; overflow: hidden; }
        .phone-notch { position: absolute; top: 12px; left: 50%; transform: translateX(-50%); width: 120px; height: 28px; background: #111; border-radius: 0 0 16px 16px; z-index: 3; }
        .phone-screen { width: 100%; height: 100%; background: linear-gradient(180deg, #1e1b4b 0%, #0f172a 100%); border-radius: 30px; overflow: hidden; position: relative; }
        .phone-screen img { width: 100%; height: 100%; object-fit: cover; opacity: 0.8; }
        .phone-screen-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(30,27,75,0.3) 0%, rgba(15,23,42,0.9) 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; text-align: center; }
        .phone-screen-overlay i { font-size: 3rem; color: var(--primary-light); margin-bottom: 1rem; }
        .phone-screen-overlay h5 { color: #fff; font-size: 1rem; font-weight: 700; margin-bottom: 0.3rem; }
        .phone-screen-overlay p { color: rgba(255,255,255,0.6); font-size: 0.72rem; }
        .app-features { list-style: none; padding: 0; }
        .app-features li { display: flex; align-items: center; gap: 0.75rem; padding: 0.7rem 0; font-size: 0.88rem; color: var(--text-muted); }
        .app-features li i { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0; }
        .app-badges { display: flex; gap: 0.75rem; margin-top: 1.5rem; }
        .app-badge { padding: 0.6rem 1.2rem; border-radius: 10px; border: 1px solid var(--border); display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); opacity: 0.6; }
        .app-badge i { font-size: 1.2rem; }

        /* ===== TESTIMONIALS ===== */
        .testimonial-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 20px; padding: 2rem; backdrop-filter: blur(12px); transition: all 0.3s; position: relative; }
        .testimonial-card:hover { border-color: var(--primary); transform: translateY(-3px); }
        .testimonial-quote { font-size: 2.5rem; line-height: 1; background: var(--gradient-accent); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 0.5rem; font-weight: 900; }
        .testimonial-text { font-size: 0.88rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 1rem; font-style: italic; }
        .testimonial-author { display: flex; align-items: center; gap: 0.75rem; }
        .testimonial-avatar { width: 40px; height: 40px; border-radius: 12px; background: var(--gradient-accent); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; }
        .testimonial-name { font-weight: 700; font-size: 0.85rem; }
        .testimonial-role { font-size: 0.72rem; color: var(--text-muted); }
        .testimonial-stars { color: var(--amber); font-size: 0.75rem; margin-bottom: 0.75rem; }

        /* ===== CTA ===== */
        .cta-section { background: var(--gradient-accent); border-radius: 28px; padding: 4.5rem 3rem; text-align: center; color: #fff; position: relative; overflow: hidden; }
        .cta-section::before { content: ''; position: absolute; top: -60%; right: -25%; width: 600px; height: 600px; border-radius: 50%; background: rgba(255,255,255,0.08); animation: ctaFloat 8s ease-in-out infinite; }
        .cta-section::after { content: ''; position: absolute; bottom: -40%; left: -15%; width: 400px; height: 400px; border-radius: 50%; background: rgba(255,255,255,0.06); animation: ctaFloat 10s ease-in-out infinite reverse; }
        @keyframes ctaFloat { 0%,100% { transform: translate(0,0); } 50% { transform: translate(30px,-30px); } }
        .cta-section h2 { font-size: clamp(1.6rem, 3vw, 2.4rem); font-weight: 900; margin-bottom: 0.8rem; position: relative; z-index: 1; }
        .cta-section p { font-size: 1.05rem; opacity: 0.9; margin-bottom: 2.2rem; max-width: 550px; margin-left: auto; margin-right: auto; position: relative; z-index: 1; line-height: 1.7; }
        .btn-cta { background: #fff; color: var(--primary-dark); padding: 0.9rem 2.8rem; border-radius: 14px; font-weight: 800; font-size: 1rem; display: inline-flex; align-items: center; gap: 8px; transition: all 0.35s; border: none; cursor: pointer; position: relative; z-index: 1; box-shadow: 0 6px 25px rgba(0,0,0,0.15); }
        .btn-cta:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(0,0,0,0.25); }

        /* ===== FOOTER ===== */
        .landing-footer { padding: 3.5rem 0 1.5rem; border-top: 1px solid var(--border); color: var(--text-muted); font-size: 0.82rem; }
        .footer-links { display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap; }
        .footer-links a { color: var(--text-muted); transition: color 0.25s; }
        .footer-links a:hover { color: var(--primary-light); }
        .footer-social { display: flex; gap: 0.75rem; justify-content: center; margin-bottom: 1rem; }
        .footer-social a { width: 38px; height: 38px; border-radius: 10px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--text-muted); transition: all 0.25s; font-size: 0.85rem; }
        .footer-social a:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-2px); }

        /* ===== ANIMATIONS ===== */
        .fade-up { opacity: 0; transform: translateY(40px); transition: all 0.7s cubic-bezier(0.16,1,0.3,1); }
        .fade-up.visible { opacity: 1; transform: translateY(0); }
        .fade-left { opacity: 0; transform: translateX(-50px); transition: all 0.7s cubic-bezier(0.16,1,0.3,1); }
        .fade-left.visible { opacity: 1; transform: translateX(0); }
        .fade-right { opacity: 0; transform: translateX(50px); transition: all 0.7s cubic-bezier(0.16,1,0.3,1); }
        .fade-right.visible { opacity: 1; transform: translateX(0); }
        .scale-in { opacity: 0; transform: scale(0.85); transition: all 0.7s cubic-bezier(0.16,1,0.3,1); }
        .scale-in.visible { opacity: 1; transform: scale(1); }
        .delay-1 { transition-delay: 0.1s; } .delay-2 { transition-delay: 0.2s; } .delay-3 { transition-delay: 0.3s; }
        .delay-4 { transition-delay: 0.4s; } .delay-5 { transition-delay: 0.5s; } .delay-6 { transition-delay: 0.6s; }
        .stagger-children > * { opacity: 0; transform: translateY(30px); transition: all 0.6s cubic-bezier(0.16,1,0.3,1); }
        .stagger-children.visible > *:nth-child(1) { transition-delay: 0.05s; }
        .stagger-children.visible > *:nth-child(2) { transition-delay: 0.12s; }
        .stagger-children.visible > *:nth-child(3) { transition-delay: 0.19s; }
        .stagger-children.visible > *:nth-child(4) { transition-delay: 0.26s; }
        .stagger-children.visible > *:nth-child(5) { transition-delay: 0.33s; }
        .stagger-children.visible > *:nth-child(6) { transition-delay: 0.4s; }
        .stagger-children.visible > * { opacity: 1; transform: translateY(0); }
        .scroll-indicator { position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%); display: flex; flex-direction: column; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.7rem; animation: scrollBounce 2s infinite; }
        @keyframes scrollBounce { 0%,100% { transform: translateX(-50%) translateY(0); } 50% { transform: translateX(-50%) translateY(8px); } }

        /* ===== WAVE DIVIDERS ===== */
        .wave-divider { display: block; width: 100%; height: 60px; margin-top: -1px; }
        .wave-divider path { fill: var(--bg-2); }
        .wave-divider-inv path { fill: var(--bg); }

        /* ===== GRADIENT BORDER CARDS ===== */
        .anomaly-card, .step-card, .feature-card, .testimonial-card, .ba-card, .live-card {
            position: relative;
        }
        .anomaly-card::after, .step-card::after, .feature-card::after, .testimonial-card::after, .ba-card::after, .live-card::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 22px;
            background: var(--gradient-accent);
            opacity: 0;
            z-index: -1;
            transition: opacity 0.4s ease;
        }
        .anomaly-card:hover::after, .step-card:hover::after, .testimonial-card:hover::after, .ba-card:hover::after, .live-card:hover::after {
            opacity: 1;
        }
        .feature-card::after { background: var(--gradient-accent); }

        /* ===== RIPPLE EFFECT ===== */
        .btn-hero, .btn-cta, .btn-nav-primary { position: relative; overflow: hidden; }
        .ripple-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.35);
            transform: scale(0);
            animation: rippleAnim 0.6s ease-out forwards;
            pointer-events: none;
        }
        @keyframes rippleAnim { to { transform: scale(4); opacity: 0; } }

        /* ===== SHIMMER LOADING ===== */
        .shimmer-wrap { position: relative; overflow: hidden; background: var(--bg-3); }
        .shimmer-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.06) 40%, rgba(255,255,255,0.12) 50%, rgba(255,255,255,0.06) 60%, transparent 100%);
            animation: shimmerAnim 1.5s infinite;
            z-index: 2;
        }
        .shimmer-wrap.img-loaded::before { display: none; }
        @keyframes shimmerAnim { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }

        /* ===== PARTNER CAROUSEL GRADIENT MASKS ===== */
        .partners-section { position: relative; }
        .partners-section::before, .partners-section::after {
            content: '';
            position: absolute;
            top: 0; bottom: 0;
            width: 100px;
            z-index: 2;
            pointer-events: none;
        }
        .partners-section::before { left: 0; background: linear-gradient(to right, var(--bg), transparent); }
        .partners-section::after { right: 0; background: linear-gradient(to left, var(--bg), transparent); }
        [data-bs-theme="light"] .partners-section::before { background: linear-gradient(to right, var(--bg), transparent); }
        [data-bs-theme="light"] .partners-section::after { background: linear-gradient(to left, var(--bg), transparent); }

        /* ===== CUSTOM MAP MARKERS ===== */
        .map-marker-badge {
            background: var(--gradient-accent);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 800;
            min-width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2.5px solid #fff;
            box-shadow: 0 3px 12px rgba(0,0,0,0.35);
            transition: transform 0.2s;
            line-height: 1;
        }
        .map-marker-badge:hover { transform: scale(1.2); }
        .map-marker-badge.zero { background: linear-gradient(135deg, #10b981, #059669); }
        .map-marker-badge.low { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .map-marker-badge.high { background: linear-gradient(135deg, #ef4444, #dc2626); }

        /* ===== ANIMATED MOBILE MENU ===== */
        .nav-links.show { animation: menuSlideDown 0.35s cubic-bezier(0.16,1,0.3,1); }
        .nav-links.show a, .nav-links.show button { animation: menuLinkFade 0.3s ease forwards; opacity: 0; }
        .nav-links.show a:nth-child(1) { animation-delay: 0.05s; }
        .nav-links.show a:nth-child(2) { animation-delay: 0.1s; }
        .nav-links.show a:nth-child(3) { animation-delay: 0.15s; }
        .nav-links.show a:nth-child(4) { animation-delay: 0.2s; }
        .nav-links.show a:nth-child(5) { animation-delay: 0.25s; }
        .nav-links.show a:nth-child(6) { animation-delay: 0.3s; }
        .nav-links.show .lang-toggle { animation-delay: 0.35s; }
        @keyframes menuSlideDown { 0% { opacity: 0; transform: translateY(-10px); } 100% { opacity: 1; transform: translateY(0); } }
        @keyframes menuLinkFade { 0% { opacity: 0; transform: translateX(-8px); } 100% { opacity: 1; transform: translateX(0); } }
        [dir="rtl"] .nav-links.show a, [dir="rtl"] .nav-links.show button { animation-name: menuLinkFadeRtl; }
        @keyframes menuLinkFadeRtl { 0% { opacity: 0; transform: translateX(8px); } 100% { opacity: 1; transform: translateX(0); } }

        /* ===== LIVE COUNTER PULSE ===== */
        .live-number.pulse { animation: counterPulse 0.4s ease; }
        @keyframes counterPulse { 0% { transform: scale(1); } 50% { transform: scale(1.12); } 100% { transform: scale(1); } }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero { padding: 100px 0 60px; }
            .hero h1 { font-size: 1.8rem; }
            .hero-showcase { margin-top: 2rem; }
            .hero-showcase-img { height: 180px; }
            .stats-banner .stat-number { font-size: 1.8rem; }
            .stat-icon { width: 44px; height: 44px; font-size: 1rem; }
            .steps-wrapper::before { display: none; }
            .gallery-grid { grid-template-columns: 1fr 1fr; gap: 0.75rem; }
            .gallery-item:nth-child(2) { transform: none; }
            .cta-section { padding: 3rem 1.5rem; border-radius: 20px; margin: 0 1rem; }
            .scroll-indicator { display: none; }
            .nav-links { display: none; flex-direction: column; position: absolute; top: 100%; left: 0; right: 0; background: var(--glass); backdrop-filter: blur(24px); padding: 1rem; border-bottom: 1px solid var(--border); box-shadow: 0 12px 40px rgba(0,0,0,0.3); }
            .nav-links.show { display: flex; }
            .nav-links a { padding: 0.7rem 1rem; width: 100%; text-align: center; }
            .mobile-menu-btn { display: flex !important; }
            .phone-frame { width: 220px; height: 440px; }
            .map-section #dairaMap { height: 320px; }
            .ba-container { height: 200px; }
        }
        @media (min-width: 769px) { .mobile-menu-btn { display: none !important; } }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="landing-nav" id="landingNav">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="/" class="nav-brand">
            <div class="nav-brand-icon"><i class="fas fa-shield-halved"></i></div>
            <span><?= __('app.name') ?></span>
        </a>
        <div class="nav-links" id="navLinks">
            <a href="#how-it-works"><?= __('landing.how_it_works') ?></a>
            <a href="#anomalies"><?= __('landing.anomaly_types') ?></a>
            <a href="#features"><?= __('landing.features') ?></a>
            <a href="/suivi"><i class="fas fa-search me-1"></i> <?= __('landing.tracking') ?></a>
            <a href="/login" class="btn-nav-primary"><i class="fas fa-right-to-bracket me-1"></i> <?= __('auth.login_button') ?></a>
            <a href="/register" class="btn-nav-ghost"><i class="fas fa-user-plus me-1"></i> <?= __('auth.create_account') ?></a>
            <button class="lang-toggle" onclick="toggleLang()" title="<?= $isRtl ? 'Switch to French' : 'التبديل إلى العربية' ?>"><?= $isRtl ? 'FR' : 'AR' ?></button>
        </div>
        <button class="mobile-menu-btn" onclick="document.getElementById('navLinks').classList.toggle('show')"><i class="fas fa-bars"></i></button>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <canvas id="particles-canvas"></canvas>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge fade-up"><i class="fas fa-shield-halved"></i> <?= __('app.wilaya') ?> — <?= __('app.name') ?></div>
            <h1 class="fade-up delay-1"><?= __('landing.hero_title_1') ?><br><span class="gradient-text"><?= __('landing.hero_title_2') ?></span></h1>
            <p class="fade-up delay-2"><?= __('landing.hero_subtitle') ?></p>
            <div class="hero-buttons fade-up delay-3">
                <a href="/register" class="btn-hero btn-hero-primary"><i class="fas fa-flag"></i> <?= __('landing.report_now') ?></a>
                <a href="#how-it-works" class="btn-hero btn-hero-outline"><i class="fas fa-play-circle"></i> <?= __('landing.how_it_works') ?></a>
            </div>
            <div class="hero-showcase fade-up delay-4">
                <div class="hero-showcase-inner">
                    <div class="hero-showcase-top">
                        <div class="hero-showcase-dot" style="background:#ef4444;"></div>
                        <div class="hero-showcase-dot" style="background:#f59e0b;"></div>
                        <div class="hero-showcase-dot" style="background:#10b981;"></div>
                        <span style="font-size:0.72rem;color:var(--text-muted);margin-<?= $isRtl ? 'right' : 'left' ?>:0.5rem;">balagh-alger.dz</span>
                    </div>
                    <img src="<?= htmlspecialchars($landingSettings['hero_image'] ?? 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=900&q=80') ?>" alt="Alger" class="hero-showcase-img">
                    <div class="hero-showcase-bar">
                        <i class="fas fa-map-marker-alt" style="color:var(--red);"></i>
                        <div class="hero-showcase-input"><span style="color:var(--text-muted);">📍 <?= $isRtl ? 'شارع الحرية، الجزائر العاصمة' : 'Rue de la Liberté, Alger Centre' ?></span></div>
                        <div style="background:var(--gradient-accent);padding:0.4rem 1rem;border-radius:8px;color:#fff;font-size:0.78rem;font-weight:600;white-space:nowrap;"><i class="fas fa-paper-plane me-1"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator"><i class="fas fa-chevron-down"></i> <?= $isRtl ? 'اكتشف المزيد' : 'Découvrir' ?></div>
    </div>
</section>

<!-- WAVE: hero → stats -->
<svg class="wave-divider" viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,0 C480,60 960,60 1440,0 L1440,60 L0,60 Z"/></svg>

<!-- STATS BANNER -->
<div class="stats-banner">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3 fade-up">
                <div class="stat-item">
                    <div class="stat-icon" style="background:rgba(99,102,241,0.15);color:var(--primary-light);"><i class="fas fa-file-lines"></i></div>
                    <div class="stat-number" data-count="<?= $stats['total'] ?>">0</div>
                    <div class="stat-label"><?= __('landing.stat_reports') ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3 fade-up delay-1">
                <div class="stat-item">
                    <div class="stat-icon" style="background:rgba(16,185,129,0.15);color:var(--green);"><i class="fas fa-check-double"></i></div>
                    <div class="stat-number" data-count="<?= $stats['resolved'] ?>">0</div>
                    <div class="stat-label"><?= __('landing.stat_resolved') ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3 fade-up delay-2">
                <div class="stat-item">
                    <div class="stat-icon" style="background:rgba(245,158,11,0.15);color:var(--amber);"><i class="fas fa-map-location-dot"></i></div>
                    <div class="stat-number" data-count="<?= $stats['dairas'] ?>">0</div>
                    <div class="stat-label"><?= __('landing.stat_dairas') ?></div>
                </div>
            </div>
            <div class="col-6 col-md-3 fade-up delay-3">
                <div class="stat-item">
                    <div class="stat-icon" style="background:rgba(6,182,212,0.15);color:var(--accent);"><i class="fas fa-building-columns"></i></div>
                    <div class="stat-number" data-count="<?= $stats['organizations'] ?>">0</div>
                    <div class="stat-label"><?= __('landing.stat_orgs') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- WAVE: stats → partners -->
<svg class="wave-divider wave-divider-inv" viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,60 C360,0 1080,0 1440,60 L1440,0 L0,0 Z"/></svg>

<!-- PARTNERS -->
<section class="partners-section">
    <div class="container text-center mb-4">
        <div class="section-badge fade-up"><i class="fas fa-handshake"></i> <?= __('landing.partners_title') ?></div>
        <p class="section-subtitle mx-auto fade-up delay-1" style="font-size:0.88rem;"><?= __('landing.partners_subtitle') ?></p>
    </div>
    <div class="partners-track">
        <?php foreach ($landingPartners as $p): ?>
        <div class="partner-logo"><i class="<?= htmlspecialchars($p['icon']) ?>" style="color:<?= htmlspecialchars($p['color']) ?>;"></i> <?= htmlspecialchars($p['name']) ?></div>
        <?php endforeach; ?>
        <?php foreach ($landingPartners as $p): ?>
        <div class="partner-logo"><i class="<?= htmlspecialchars($p['icon']) ?>" style="color:<?= htmlspecialchars($p['color']) ?>;"></i> <?= htmlspecialchars($p['name']) ?></div>
        <?php endforeach; ?>
    </div>
</section>

<!-- WAVE: partners → how-it-works -->
<svg class="wave-divider wave-divider-inv" viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,60 C360,0 1080,0 1440,60 L1440,0 L0,0 Z"/></svg>

<!-- HOW IT WORKS -->
<section id="how-it-works" style="background:var(--bg-2);">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge fade-up"><i class="fas fa-route"></i> <?= __('landing.process') ?></div>
            <div class="glow-line fade-up delay-1"></div>
            <h2 class="section-title fade-up delay-1"><?= __('landing.how_title') ?></h2>
            <p class="section-subtitle mx-auto fade-up delay-2"><?= __('landing.how_subtitle') ?></p>
        </div>
        <div class="row g-4 steps-wrapper stagger-children">
            <div class="col-md-3"><div class="step-card"><div class="step-number"><i class="fas fa-camera"></i></div><h5><?= __('landing.step1_title') ?></h5><p><?= __('landing.step1_desc') ?></p></div></div>
            <div class="col-md-3"><div class="step-card"><div class="step-number"><i class="fas fa-paper-plane"></i></div><h5><?= __('landing.step2_title') ?></h5><p><?= __('landing.step2_desc') ?></p></div></div>
            <div class="col-md-3"><div class="step-card"><div class="step-number"><i class="fas fa-gears"></i></div><h5><?= __('landing.step3_title') ?></h5><p><?= __('landing.step3_desc') ?></p></div></div>
            <div class="col-md-3"><div class="step-card"><div class="step-number"><i class="fas fa-circle-check"></i></div><h5><?= __('landing.step4_title') ?></h5><p><?= __('landing.step4_desc') ?></p></div></div>
        </div>
    </div>
</section>

<!-- WAVE: how-it-works → anomalies -->
<svg class="wave-divider" viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,0 C480,60 960,60 1440,0 L1440,60 L0,60 Z"/></svg>

<!-- ANOMALY TYPES -->
<section id="anomalies">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge fade-up"><i class="fas fa-exclamation-triangle"></i> <?= __('landing.reportable') ?></div>
            <div class="glow-line fade-up delay-1"></div>
            <h2 class="section-title fade-up delay-1"><?= __('landing.anomalies_title') ?></h2>
            <p class="section-subtitle mx-auto fade-up delay-2"><?= __('landing.anomalies_subtitle') ?></p>
        </div>
        <div class="row g-4 stagger-children">
            <?php $anomalies = [
                ['img' => 'photo-1515162305055-fca6a7efaea5', 'icon' => 'fa-road', 'color' => 'var(--amber)', 'title_key' => 'anomaly_road_title', 'desc_key' => 'anomaly_road_desc', 'deadline' => '5-10j'],
                ['img' => 'photo-1504328345606-18bbc8c9d7d1', 'icon' => 'fa-droplet', 'color' => 'var(--accent)', 'title_key' => 'anomaly_water_title', 'desc_key' => 'anomaly_water_desc', 'deadline' => '15j'],
                ['img' => 'photo-1473341304170-971dccb5ac1e', 'icon' => 'fa-bolt', 'color' => 'var(--amber)', 'title_key' => 'anomaly_elec_title', 'desc_key' => 'anomaly_elec_desc', 'deadline' => '15j'],
                ['img' => 'photo-1532996122724-e3c354a0b15b', 'icon' => 'fa-trash-can', 'color' => 'var(--red)', 'title_key' => 'anomaly_trash_title', 'desc_key' => 'anomaly_trash_desc', 'deadline' => '7j'],
                ['img' => 'photo-1558618666-fcd25c85f82e', 'icon' => 'fa-lightbulb', 'color' => 'var(--amber)', 'title_key' => 'anomaly_light_title', 'desc_key' => 'anomaly_light_desc', 'deadline' => '7j'],
                ['img' => 'photo-1542601906990-b4d3fb778b09', 'icon' => 'fa-tree', 'color' => 'var(--green)', 'title_key' => 'anomaly_green_title', 'desc_key' => 'anomaly_green_desc', 'deadline' => '7j'],
            ]; ?>
            <?php foreach ($anomalies as $a): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="anomaly-card">
                    <div class="anomaly-img-wrapper">
                        <img src="https://images.unsplash.com/<?= $a['img'] ?>?w=600&q=80" alt="" class="anomaly-img" loading="lazy">
                        <div class="anomaly-img-overlay"></div>
                        <div class="anomaly-badge"><i class="fas <?= $a['icon'] ?> me-1"></i> <?= $isRtl ? $a['deadline'] : $a['deadline'] ?></div>
                    </div>
                    <div class="anomaly-body">
                        <h6><i class="fas <?= $a['icon'] ?>" style="color:<?= $a['color'] ?>;"></i> <?= __('landing.' . $a['title_key']) ?></h6>
                        <p><?= __('landing.' . $a['desc_key']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- WAVE: anomalies → map -->
<svg class="wave-divider wave-divider-inv" viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,60 C360,0 1080,0 1440,60 L1440,0 L0,0 Z"/></svg>

<!-- MAP -->
<section class="map-section" style="background:var(--bg-2);">
    <div class="container">
        <div class="text-center mb-4">
            <div class="section-badge fade-up"><i class="fas fa-map-marked-alt"></i> <?= __('landing.map_title') ?></div>
            <div class="glow-line fade-up delay-1"></div>
            <h2 class="section-title fade-up delay-1"><?= __('landing.map_subtitle') ?></h2>
        </div>
        <div class="fade-up delay-2">
            <div id="dairaMap"></div>
        </div>
    </div>
</section>

<!-- WAVE: map → gallery -->
<svg class="wave-divider" viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,0 C480,60 960,60 1440,0 L1440,60 L0,60 Z"/></svg>

<!-- GALLERY -->
<section class="gallery-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge fade-up"><i class="fas fa-images"></i> <?= $isRtl ? 'من العاصمة' : 'Depuis la capitale' ?></div>
            <div class="glow-line fade-up delay-1"></div>
            <h2 class="section-title fade-up delay-1"><?= $isRtl ? 'الجزائر في صور' : 'Alger en images' ?></h2>
            <p class="section-subtitle mx-auto fade-up delay-2"><?= $isRtl ? 'مشاكل المدينة تنتظر حلّها — كن أنت من يبدأ.' : 'Les problèmes de la ville attendent une solution — Soyez celui qui commence.' ?></p>
        </div>
        <div class="gallery-grid stagger-children">
            <?php foreach ($landingGallery as $g): ?>
            <div class="gallery-item"><img src="<?= htmlspecialchars($g['image_url']) ?>" alt="<?= htmlspecialchars($g['alt_text']) ?>" loading="lazy"></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- WAVE: gallery → before/after -->
<svg class="wave-divider wave-divider-inv" viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,60 C360,0 1080,0 1440,60 L1440,0 L0,0 Z"/></svg>

<!-- BEFORE / AFTER -->
<section style="background:var(--bg-2);">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge fade-up"><i class="fas fa-arrows-left-right"></i> <?= __('landing.before_after_title') ?></div>
            <div class="glow-line fade-up delay-1"></div>
            <h2 class="section-title fade-up delay-1"><?= __('landing.before_after_subtitle') ?></h2>
        </div>
        <div class="row g-4 stagger-children">
            <?php foreach ($landingBeforeAfter as $ba): ?>
            <div class="col-md-4">
                <div class="ba-card">
                    <div class="ba-container" data-ba>
                            <img src="<?= htmlspecialchars($ba['before_image']) ?>" alt="" loading="lazy">
                            <img src="<?= htmlspecialchars($ba['after_image']) ?>" alt="" class="ba-after" loading="lazy">
                        <div class="ba-slider"></div>
                        <div class="ba-labels">
                            <span class="ba-label"><?= $isRtl ? 'بعد' : 'Après' ?></span>
                            <span class="ba-label"><?= $isRtl ? 'قبل' : 'Avant' ?></span>
                        </div>
                    </div>
                    <div class="ba-body">
                        <h6><?= $isRtl ? htmlspecialchars($ba['title_ar']) : htmlspecialchars($ba['title_fr']) ?></h6>
                        <p><?= $isRtl ? htmlspecialchars($ba['desc_ar']) : htmlspecialchars($ba['desc_fr']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- WAVE: before/after → live counters (smooth same-bg transition) -->
<svg class="wave-divider wave-divider-inv" viewBox="0 0 1440 40" preserveAspectRatio="none"><path d="M0,40 C720,0 720,0 1440,40 L1440,0 L0,0 Z"/></svg>

<!-- LIVE COUNTERS -->
<section class="live-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge fade-up"><i class="fas fa-signal"></i> <?= __('landing.live_title') ?></div>
            <div class="glow-line fade-up delay-1"></div>
            <h2 class="section-title fade-up delay-1"><?= __('landing.live_subtitle') ?></h2>
        </div>
        <div class="row g-4 justify-content-center stagger-children">
            <div class="col-sm-4">
                <div class="live-card">
                    <div class="live-icon" style="background:rgba(99,102,241,0.15);color:var(--primary-light);"><i class="fas fa-flag"></i></div>
                    <div class="live-number" style="color:var(--primary-light);" id="liveToday"><?= $statsLive['today'] ?></div>
                    <div class="live-label"><?= __('landing.live_today') ?><span class="live-pulse"></span></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="live-card">
                    <div class="live-icon" style="background:rgba(245,158,11,0.15);color:var(--amber);"><i class="fas fa-clock-rotate-left"></i></div>
                    <div class="live-number" style="color:var(--amber);" id="liveProgress"><?= $statsLive['in_progress'] ?></div>
                    <div class="live-label"><?= __('landing.live_in_progress') ?></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="live-card">
                    <div class="live-icon" style="background:rgba(16,185,129,0.15);color:var(--green);"><i class="fas fa-check-circle"></i></div>
                    <div class="live-number" style="color:var(--green);" id="liveResolved"><?= $statsLive['resolved_month'] ?></div>
                    <div class="live-label"><?= __('landing.live_resolved_month') ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section id="features" style="background:var(--bg-2);">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge fade-up"><i class="fas fa-star"></i> <?= __('landing.why') ?></div>
            <div class="glow-line fade-up delay-1"></div>
            <h2 class="section-title fade-up delay-1"><?= __('landing.features_title') ?></h2>
            <p class="section-subtitle mx-auto fade-up delay-2"><?= __('landing.features_subtitle') ?></p>
        </div>
        <div class="row g-4 stagger-children">
            <div class="col-sm-6 col-lg-4"><div class="feature-card"><div class="feature-icon" style="background:rgba(99,102,241,0.15);color:var(--primary-light);"><i class="fas fa-qrcode"></i></div><h6><?= __('landing.feat_tracking_title') ?></h6><p><?= __('landing.feat_tracking_desc') ?></p></div></div>
            <div class="col-sm-6 col-lg-4"><div class="feature-card"><div class="feature-icon" style="background:rgba(6,182,212,0.15);color:var(--accent);"><i class="fas fa-camera"></i></div><h6><?= __('landing.feat_photo_title') ?></h6><p><?= __('landing.feat_photo_desc') ?></p></div></div>
            <div class="col-sm-6 col-lg-4"><div class="feature-card"><div class="feature-icon" style="background:rgba(16,185,129,0.15);color:var(--green);"><i class="fas fa-map-location-dot"></i></div><h6><?= __('landing.feat_gps_title') ?></h6><p><?= __('landing.feat_gps_desc') ?></p></div></div>
            <div class="col-sm-6 col-lg-4"><div class="feature-card"><div class="feature-icon" style="background:rgba(245,158,11,0.15);color:var(--amber);"><i class="fas fa-clock"></i></div><h6><?= __('landing.feat_deadline_title') ?></h6><p><?= __('landing.feat_deadline_desc') ?></p></div></div>
            <div class="col-sm-6 col-lg-4"><div class="feature-card"><div class="feature-icon" style="background:rgba(239,68,68,0.15);color:var(--red);"><i class="fas fa-bell"></i></div><h6><?= __('landing.feat_notif_title') ?></h6><p><?= __('landing.feat_notif_desc') ?></p></div></div>
            <div class="col-sm-6 col-lg-4"><div class="feature-card"><div class="feature-icon" style="background:rgba(139,92,246,0.15);color:#a78bfa;"><i class="fas fa-star"></i></div><h6><?= __('landing.feat_rate_title') ?></h6><p><?= __('landing.feat_rate_desc') ?></p></div></div>
        </div>
    </div>
</section>

<!-- WAVE: features → FAQ -->
<svg class="wave-divider" viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,0 C480,60 960,60 1440,0 L1440,60 L0,60 Z"/></svg>

<!-- FAQ -->
<section>
    <div class="container" style="max-width:750px;">
        <div class="text-center mb-5">
            <div class="section-badge fade-up"><i class="fas fa-circle-question"></i> <?= __('landing.faq_title') ?></div>
            <div class="glow-line fade-up delay-1"></div>
            <h2 class="section-title fade-up delay-1"><?= __('landing.faq_subtitle') ?></h2>
        </div>
        <div class="fade-up delay-2">
            <?php foreach ($landingFaq as $i => $faq): ?>
            <div class="faq-item<?= $i === 0 ? ' open' : '' ?>">
                <div class="faq-q" onclick="this.parentElement.classList.toggle('open')">
                    <span><?= $isRtl ? htmlspecialchars($faq['question_ar'] ?: $faq['question_fr']) : htmlspecialchars($faq['question_fr']) ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-a<?= $i === 0 ? ' faq-a-open' : '' ?>">
                    <div class="faq-a-inner"><?= $isRtl ? htmlspecialchars($faq['answer_ar'] ?: $faq['answer_fr']) : htmlspecialchars($faq['answer_fr']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- WAVE: FAQ → app teaser -->
<svg class="wave-divider wave-divider-inv" viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,60 C360,0 1080,0 1440,60 L1440,0 L0,0 Z"/></svg>

<!-- APP TEASER -->
<section class="app-section" style="background:var(--bg-2);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-md-6 text-center fade-left">
                <div class="app-mockup mx-auto">
                    <div class="phone-frame">
                        <div class="phone-notch"></div>
                        <div class="phone-screen">
                            <img src="https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=400&q=80" alt="" loading="lazy">
                            <div class="phone-screen-overlay">
                                <i class="fas fa-shield-halved"></i>
                                <h5><?= __('app.name') ?></h5>
                                <p><?= __('landing.app_badge') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 fade-right">
                <div class="section-badge"><i class="fas fa-mobile-screen-button"></i> <?= $isRtl ? 'تطبيق الهاتف' : 'Application mobile' ?></div>
                <h2 class="section-title mb-3"><?= __('landing.app_title') ?></h2>
                <p class="section-subtitle mb-4" style="max-width:100%;"><?= __('landing.app_subtitle') ?></p>
                <ul class="app-features">
                    <li><i class="fas fa-camera" style="background:rgba(99,102,241,0.15);color:var(--primary-light);"></i> <?= __('landing.app_feature1') ?></li>
                    <li><i class="fas fa-location-crosshairs" style="background:rgba(16,185,129,0.15);color:var(--green);"></i> <?= __('landing.app_feature2') ?></li>
                    <li><i class="fas fa-bell" style="background:rgba(245,158,11,0.15);color:var(--amber);"></i> <?= __('landing.app_feature3') ?></li>
                    <li><i class="fas fa-wifi" style="background:rgba(6,182,212,0.15);color:var(--accent);"></i> <?= __('landing.app_feature4') ?></li>
                </ul>
                <div class="app-badges">
                    <div class="app-badge"><i class="fab fa-apple"></i> <?= __('landing.app_store') ?></div>
                    <div class="app-badge"><i class="fab fa-google-play"></i> <?= __('landing.google_play') ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WAVE: app teaser → testimonials -->
<svg class="wave-divider" viewBox="0 0 1440 60" preserveAspectRatio="none"><path d="M0,0 C480,60 960,60 1440,0 L1440,60 L0,60 Z"/></svg>

<!-- TESTIMONIALS -->
<section>
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge fade-up"><i class="fas fa-comments"></i> <?= $isRtl ? 'شهادات' : 'Témoignages' ?></div>
            <div class="glow-line fade-up delay-1"></div>
            <h2 class="section-title fade-up delay-1"><?= $isRtl ? 'ماذا يقول المواطنين' : 'Ce que disent les citoyens' ?></h2>
        </div>
        <div class="row g-4 stagger-children">
            <?php foreach ($landingTestimonials as $t): ?>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars"><?= str_repeat('<i class="fas fa-star"></i>', (int)$t['rating']) ?><?php if ($t['rating'] < 5): ?><i class="fas fa-star-half-stroke"></i><?php endif; ?></div>
                    <div class="testimonial-quote">"</div>
                    <p class="testimonial-text"><?= $isRtl ? htmlspecialchars($t['text_ar']) : htmlspecialchars($t['text_fr']) ?></p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar" style="background:<?= htmlspecialchars($t['avatar_gradient']) ?>;"><?= htmlspecialchars($t['avatar_letter']) ?></div>
                        <div><div class="testimonial-name"><?= $isRtl ? htmlspecialchars($t['author_name']) : htmlspecialchars($t['author_name']) ?></div><div class="testimonial-role"><?= $isRtl ? htmlspecialchars($t['author_role']) : htmlspecialchars($t['author_role']) ?></div></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section>
    <div class="container">
        <div class="cta-section fade-up">
            <h2><?= __('landing.cta_title') ?></h2>
            <p><?= __('landing.cta_subtitle') ?></p>
            <a href="/register" class="btn-cta"><i class="fas fa-flag"></i> <?= __('landing.cta_button') ?></a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="landing-footer">
    <div class="container text-center">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-3" style="font-weight:700;font-size:1rem;">
            <div class="nav-brand-icon" style="width:32px;height:32px;font-size:0.8rem;border-radius:8px;"><i class="fas fa-shield-halved"></i></div>
            <?= __('app.name') ?>
        </div>
        <div class="footer-social mb-3">
            <a href="<?= htmlspecialchars($landingSettings['facebook_url'] ?? '#') ?>"><i class="fab fa-facebook-f"></i></a>
            <a href="<?= htmlspecialchars($landingSettings['twitter_url'] ?? '#') ?>"><i class="fab fa-twitter"></i></a>
            <a href="<?= htmlspecialchars($landingSettings['instagram_url'] ?? '#') ?>"><i class="fab fa-instagram"></i></a>
            <a href="<?= htmlspecialchars($landingSettings['youtube_url'] ?? '#') ?>"><i class="fab fa-youtube"></i></a>
        </div>
        <div class="footer-links mb-3">
            <a href="/login"><?= __('auth.login_button') ?></a>
            <a href="/register"><?= __('auth.create_account') ?></a>
            <a href="/suivi"><?= __('landing.tracking') ?></a>
            <a href="#how-it-works"><?= __('landing.how_it_works') ?></a>
            <a href="#features"><?= __('landing.features') ?></a>
        </div>
        <p class="mb-0" style="font-size:0.72rem;color:var(--text-muted);">&copy; <?= date('Y') ?> <?= __('app.full_name') ?> — <?= __('app.wilaya') ?>. <?= $isRtl ? 'جميع الحقوق محفوظة.' : 'Tous droits réservés.' ?></p>
    </div>
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    'use strict';

    // ===== PARTICLES =====
    var canvas = document.getElementById('particles-canvas');
    var ctx = canvas.getContext('2d');
    var particles = [];
    function resizeCanvas() { canvas.width = canvas.parentElement.offsetWidth; canvas.height = canvas.parentElement.offsetHeight; }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
    function Particle() { this.x = Math.random() * canvas.width; this.y = Math.random() * canvas.height; this.vx = (Math.random() - 0.5) * 0.5; this.vy = (Math.random() - 0.5) * 0.5; this.size = Math.random() * 2 + 0.5; this.alpha = Math.random() * 0.4 + 0.1; }
    for (var i = 0; i < 50; i++) particles.push(new Particle());
    function drawParticles() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (var i = 0; i < particles.length; i++) {
            var p = particles[i]; p.x += p.vx; p.y += p.vy;
            if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
            if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
            ctx.beginPath(); ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2); ctx.fillStyle = 'rgba(99,102,241,' + p.alpha + ')'; ctx.fill();
            for (var j = i + 1; j < particles.length; j++) { var p2 = particles[j]; var dx = p.x - p2.x; var dy = p.y - p2.y; var dist = Math.sqrt(dx * dx + dy * dy); if (dist < 150) { ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(p2.x, p2.y); ctx.strokeStyle = 'rgba(99,102,241,' + (0.08 * (1 - dist / 150)) + ')'; ctx.lineWidth = 0.5; ctx.stroke(); } }
        }
        requestAnimationFrame(drawParticles);
    }
    drawParticles();

    // ===== NAVBAR =====
    var nav = document.getElementById('landingNav');
    window.addEventListener('scroll', function() { nav.classList.toggle('scrolled', window.scrollY > 60); });

    // ===== SCROLL REVEAL =====
    var observer = new IntersectionObserver(function(entries) { entries.forEach(function(e) { if (e.isIntersecting) e.target.classList.add('visible'); }); }, { threshold: 0.08, rootMargin: '0px 0px -50px 0px' });
    document.querySelectorAll('.fade-up, .fade-left, .fade-right, .scale-in, .stagger-children').forEach(function(el) { observer.observe(el); });

    // ===== ANIMATED COUNTERS =====
    var counterObs = new IntersectionObserver(function(entries) { entries.forEach(function(entry) { if (entry.isIntersecting) { var el = entry.target; var target = parseInt(el.getAttribute('data-count')); var start = 0; var startTime = null; function animate(time) { if (!startTime) startTime = time; var progress = Math.min((time - startTime) / 2000, 1); var eased = 1 - Math.pow(1 - progress, 3); el.textContent = Math.floor(eased * target).toLocaleString(); if (progress < 1) requestAnimationFrame(animate); else el.textContent = target.toLocaleString(); } requestAnimationFrame(animate); counterObs.unobserve(el); } }); }, { threshold: 0.5 });
    document.querySelectorAll('[data-count]').forEach(function(el) { counterObs.observe(el); });

    // ===== LIVE COUNTERS =====
    function pulseEl(el) { if (el) { el.classList.remove('pulse'); void el.offsetWidth; el.classList.add('pulse'); } }
    function fetchLiveStats() {
        fetch('/api/landing-stats').then(function(r) { return r.json(); }).then(function(d) {
            var t = document.getElementById('liveToday');
            var p = document.getElementById('liveProgress');
            var r = document.getElementById('liveResolved');
            if (t && t.textContent != d.today) { t.textContent = d.today; pulseEl(t); }
            if (p && p.textContent != d.in_progress) { p.textContent = d.in_progress; pulseEl(p); }
            if (r && r.textContent != d.resolved_month) { r.textContent = d.resolved_month; pulseEl(r); }
        }).catch(function(){});
    }
    setInterval(fetchLiveStats, 30000);

    // ===== LANGUAGE TOGGLE =====
    window.toggleLang = function() {
        fetch('/api/set-lang', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'lang=<?= $isRtl ? "fr" : "ar" ?>'
        }).then(function() { location.reload(); });
    };

    // ===== SMOOTH SCROLL =====
    document.querySelectorAll('a[href^="#"]').forEach(function(a) {
        a.addEventListener('click', function(e) { var target = document.querySelector(this.getAttribute('href')); if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); document.getElementById('navLinks').classList.remove('show'); } });
    });

    // ===== TILT EFFECT =====
    document.querySelectorAll('.anomaly-card, .feature-card, .step-card').forEach(function(card) {
        card.addEventListener('mousemove', function(e) { var rect = card.getBoundingClientRect(); var x = e.clientX - rect.left; var y = e.clientY - rect.top; var cx = rect.width / 2; var cy = rect.height / 2; card.style.transform = 'perspective(1000px) rotateX(' + ((y - cy) / 20) + 'deg) rotateY(' + ((cx - x) / 20) + 'deg) translateY(-5px)'; });
        card.addEventListener('mouseleave', function() { card.style.transform = ''; });
    });

    // ===== BEFORE/AFTER SLIDER =====
    document.querySelectorAll('[data-ba]').forEach(function(container) {
        var afterImg = container.querySelector('.ba-after');
        var slider = container.querySelector('.ba-slider');
        var isDragging = false;
        function setPosition(x) {
            var rect = container.getBoundingClientRect();
            var pos = Math.max(0, Math.min(1, (x - rect.left) / rect.width));
            afterImg.style.clipPath = 'inset(0 0 0 ' + (pos * 100) + '%)';
            slider.style.left = (pos * 100) + '%';
        }
        container.addEventListener('mousedown', function(e) { isDragging = true; setPosition(e.clientX); });
        container.addEventListener('touchstart', function(e) { isDragging = true; setPosition(e.touches[0].clientX); });
        window.addEventListener('mousemove', function(e) { if (isDragging) setPosition(e.clientX); });
        window.addEventListener('touchmove', function(e) { if (isDragging) setPosition(e.touches[0].clientX); });
        window.addEventListener('mouseup', function() { isDragging = false; });
        window.addEventListener('touchend', function() { isDragging = false; });
    });

    // ===== RIPPLE EFFECT =====
    document.querySelectorAll('.btn-hero, .btn-cta, .btn-nav-primary').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            var rect = btn.getBoundingClientRect();
            var ripple = document.createElement('span');
            ripple.className = 'ripple-circle';
            var size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
            btn.appendChild(ripple);
            setTimeout(function() { ripple.remove(); }, 650);
        });
    });

    // ===== SHIMMER ON IMAGES =====
    document.querySelectorAll('.anomaly-img, .gallery-item img, .hero-showcase-img').forEach(function(img) {
        var wrap = document.createElement('div');
        wrap.className = 'shimmer-wrap';
        img.parentNode.insertBefore(wrap, img);
        wrap.appendChild(img);
        img.addEventListener('load', function() { wrap.classList.add('img-loaded'); });
        if (img.complete) wrap.classList.add('img-loaded');
    });

    // ===== LEAFLET MAP =====
    var mapEl = document.getElementById('dairaMap');
    if (mapEl) {
        var isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        var map = L.map('dairaMap', { scrollWheelZoom: false }).setView([36.7538, 3.0588], 12);
        L.tileLayer(isDark
            ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
            : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
            { attribution: '&copy; OpenStreetMap', maxZoom: 18 }).addTo(map);
        var dairas = <?= json_encode(array_map(function($d) use ($isRtl) { return ['name' => $d['name'], 'count' => (int)$d['report_count']]; }, $dairas)) ?>;
        var coords = { 'Alger Centre': [36.7538,3.0588], 'Sidi M\'Hamed': [36.7625,3.0519], 'El Biar': [36.7650,3.0495], 'Bouzareah': [36.7800,3.0100], 'Hussein Dey': [36.7450,3.1000], 'El Harrach': [36.7200,3.1350], 'Bir Mourad Raïs': [36.7400,3.0500], 'Bab Ezzouar': [36.7200,3.1900], 'Dely Ibrahim': [36.7600,2.9800], 'Draria': [36.7300,2.9600], 'El Addaïa': [36.7000,3.1200], 'Rouiba': [36.7700,3.2800], 'Baraki': [36.6700,3.1000], 'Birtouta': [36.6500,3.0000], 'Zeralda': [36.7100,2.8400], 'Reghaia': [36.7700,3.3400], 'Kouba': [36.7550,3.0850], 'Hammoudi Boumediene': [36.7300,3.1400], 'Ben Aknoun': [36.7700,3.0200], 'Beni Messous': [36.7800,2.9600], 'Bouchareb': [36.7100,3.0200], 'Bourj El Kiffan': [36.7400,3.2000], 'Harrouch Badi Sidi': [36.6900,3.1500], 'Mohamed Belouizdad': [36.7400,3.0800] };
        dairas.forEach(function(d) {
            var c = coords[d.name] || [36.75 + (Math.random()-0.5)*0.1, 3.05 + (Math.random()-0.5)*0.3];
            var cls = d.count > 5 ? 'high' : d.count > 0 ? 'low' : 'zero';
            var icon = L.divIcon({
                className: '',
                html: '<div class="map-marker-badge ' + cls + '">' + d.count + '</div>',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });
            var marker = L.marker(c, { icon: icon }).addTo(map);
            marker.bindPopup('<b>' + d.name + '</b><br>' + d.count + ' <?= $isRtl ? 'بلاغ' : 'signalements' ?>');
        });
    }

})();
</script>
</body>
</html>
