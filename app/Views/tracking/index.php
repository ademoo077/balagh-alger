<?php
\App\Helpers\I18n::init();
$lang = \App\Helpers\I18n::getLang();
$isRtl = \App\Helpers\I18n::isRtl();
$dir = \App\Helpers\I18n::getDir();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi — <?= __('app.name') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root, [data-bs-theme="light"] {
            --accent: #6366f1; --accent-light: #818cf8; --cyan: #0891b2;
            --bg: #f8f9fc; --surface: #ffffff; --card-bg: #ffffff;
            --card-border: #e2e5ef; --text: #111827; --text-muted: #9ca3af;
            --input-bg: #f4f5f9; --input-border: #e2e5ef;
        }
        [data-bs-theme="dark"] {
            --bg: #0a0e1a; --surface: #151c2c; --card-bg: rgba(21,28,44,0.9);
            --card-border: rgba(255,255,255,0.06); --text: #e2e8f0; --text-muted: #64748b;
            --input-bg: rgba(255,255,255,0.04); --input-border: rgba(255,255,255,0.06);
        }
        * { box-sizing: border-box; }
        body {
            background: var(--bg); min-height: 100vh; display: flex;
            align-items: center; justify-content: center;
            font-family: 'Inter', system-ui, sans-serif; padding: 20px;
            transition: background 0.3s ease;
        }
        body::before {
            content: ''; position: fixed; top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(99,102,241,0.06) 0%, transparent 50%),
                        radial-gradient(circle at 70% 60%, rgba(8,145,178,0.04) 0%, transparent 50%);
            animation: bgPulse 10s ease-in-out infinite alternate; pointer-events: none;
        }
        @keyframes bgPulse { from { transform: translate(0,0) scale(1); } to { transform: translate(-3%,-3%) scale(1.03); } }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .wrapper { position: relative; z-index: 1; width: 100%; max-width: 440px; }
        .brand { text-align: center; margin-bottom: 28px; }
        .brand .icon {
            width: 56px; height: 56px; background: linear-gradient(135deg, var(--accent), #4f46e5);
            border-radius: 16px; display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 14px; font-size: 1.3rem; color: white;
            box-shadow: 0 4px 16px rgba(99,102,241,0.3); animation: fadeInUp 0.5s ease-out;
        }
        .brand h1 { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.03em; margin: 0;
            background: linear-gradient(135deg, var(--text), var(--accent-light));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: fadeInUp 0.5s ease-out 0.1s both;
        }
        .brand p { color: var(--text-muted); font-size: 0.82rem; margin-top: 4px; animation: fadeInUp 0.5s ease-out 0.15s both; }
        .card-box {
            background: var(--card-bg); border: 1px solid var(--card-border);
            border-radius: 16px; padding: 28px; backdrop-filter: blur(20px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.08); animation: fadeInUp 0.5s ease-out 0.2s both;
        }
        .form-floating > .form-control {
            background: var(--input-bg); border: 1px solid var(--input-border);
            border-radius: 12px; color: var(--text); font-size: 0.92rem;
        }
        .form-floating > .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }
        .form-floating > label { color: var(--text-muted); }
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label { color: var(--accent-light); }
        .btn-search {
            background: linear-gradient(135deg, var(--accent), #4f46e5); border: none; color: white;
            padding: 11px; font-weight: 700; font-size: 0.92rem; border-radius: 12px; transition: all 0.25s ease;
        }
        .btn-search:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(99,102,241,0.35); }
        .top-actions {
            position: fixed; top: 16px; right: 16px; z-index: 10;
            display: flex; gap: 8px;
        }
        [dir="rtl"] .top-actions { right: auto; left: 16px; }
        .top-actions button {
            width: 36px; height: 36px; border-radius: 8px;
            border: 1px solid var(--card-border); background: var(--card-bg);
            color: var(--text-muted); display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s ease; backdrop-filter: blur(12px); padding: 0;
        }
        .top-actions button:hover { border-color: var(--accent); color: var(--accent); }
        [data-bs-theme="dark"] .icon-sun-auth { display: none; }
        [data-bs-theme="light"] .icon-moon-auth { display: none; }
        .help-text { color: var(--text-muted); font-size: 0.82rem; text-align: center; margin-top: 16px; }
    </style>
    <script>
    window.__translations = <?= file_get_contents(__DIR__ . '/../../../lang/' . $lang . '.json') ?>;
    window.__lang = '<?= $lang ?>';
    </script>
</head>
<body>
    <div class="top-actions">
        <button onclick="var l=window.__lang==='fr'?'ar':'fr';fetch('/api/set-lang',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({lang:l})}).then(()=>location.reload());" title="Langue">
            <?= $lang === 'ar' ? '🇫🇷' : '🇩🇿' ?>
        </button>
        <button onclick="var h=document.documentElement,c=h.getAttribute('data-bs-theme'),n=c==='dark'?'light':'dark';h.setAttribute('data-bs-theme',n);localStorage.setItem('balagh-theme',n);">
            <i class="fas fa-sun icon-sun-auth" style="font-size:0.85rem;"></i>
            <i class="fas fa-moon icon-moon-auth" style="font-size:0.85rem;"></i>
        </button>
    </div>

    <div class="wrapper">
        <div class="brand">
            <div class="icon"><i class="fas fa-bullhorn"></i></div>
            <h1><?= __('app.name') ?></h1>
            <p><?= __('tracking.subtitle') ?></p>
        </div>

        <div class="card-box">
            <form method="GET" action="/suivi">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="code" name="code" placeholder="BA-2026-XXXXXX" required autofocus
                           pattern="BA-[0-9]{4}-[0-9]{6}" style="font-family:'JetBrains Mono',monospace;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;">
                    <label for="code"><i class="fas fa-search me-1"></i> <?= __('tracking.code_label') ?></label>
                </div>
                <button type="submit" class="btn btn-search w-100">
                    <i class="fas fa-magnifying-glass me-2"></i> <?= __('tracking.search_button') ?>
                </button>
            </form>
            <p class="help-text"><i class="fas fa-info-circle me-1"></i> <?= __('tracking.help_text') ?></p>
        </div>

        <div class="text-center mt-4" style="animation:fadeInUp 0.5s ease-out 0.4s both;">
            <a href="/login" style="color:var(--accent-light);text-decoration:none;font-size:0.82rem;font-weight:600;">
                <i class="fas fa-arrow-right-to-bracket me-1"></i> <?= __('tracking.login_link') ?>
            </a>
        </div>
    </div>

    <script>
    (function() {
        var saved = localStorage.getItem('balagh-theme');
        if (saved) document.documentElement.setAttribute('data-bs-theme', saved);
    })();
    </script>
</body>
</html>
