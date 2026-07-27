<?php require_once __DIR__ . '/../../Helpers/I18n.php';
\App\Helpers\I18n::init();
$lang = \App\Helpers\I18n::getLang();
$dir = \App\Helpers\I18n::getDir();
$isRtl = \App\Helpers\I18n::isRtl();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('auth.register_title') ?> — Balagh Alger</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            background: var(--bg); min-height: 100vh; display: flex; align-items: center;
            justify-content: center; font-family: 'Inter', 'Noto Sans Arabic', system-ui, sans-serif;
            padding: 20px; transition: background 0.3s ease;
        }
        body::before {
            content: ''; position: fixed; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(99,102,241,0.06) 0%, transparent 50%),
                        radial-gradient(circle at 70% 60%, rgba(8,145,178,0.04) 0%, transparent 50%);
            pointer-events: none;
        }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .register-wrapper { position: relative; z-index: 1; width: 100%; max-width: 460px; }
        .brand { text-align: center; margin-bottom: 24px; animation: fadeInUp 0.5s ease-out; }
        .brand h1 {
            font-size: 1.5rem; font-weight: 800; letter-spacing: -0.03em;
            background: linear-gradient(135deg, var(--text), var(--accent-light));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .brand p { color: var(--text-muted); font-size: 0.82rem; margin-top: 4px; }
        .register-card {
            background: var(--card-bg); border: 1px solid var(--card-border);
            border-radius: 16px; padding: 28px;
            backdrop-filter: blur(20px); box-shadow: 0 10px 15px rgba(0,0,0,0.08);
            animation: fadeInUp 0.5s ease-out 0.15s both;
        }
        .form-floating > .form-control, .form-floating > .form-select {
            background: var(--input-bg); border: 1px solid var(--input-border);
            border-radius: 12px; color: var(--text); font-size: 0.88rem;
        }
        .form-floating > .form-control:focus, .form-select:focus {
            border-color: var(--accent); box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }
        .form-floating > label { color: var(--text-muted); }
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label { color: var(--accent-light); }
        html[dir="rtl"] .form-floating > label { transform-origin: right top; }
        .btn-register {
            background: linear-gradient(135deg, var(--accent), #4f46e5);
            border: none; color: white; padding: 11px; font-weight: 700;
            border-radius: 12px; transition: all 0.25s ease;
        }
        .btn-register:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(99,102,241,0.35); }
        .link { color: var(--accent-light); text-decoration: none; font-size: 0.82rem; }
        .link:hover { text-decoration: underline; }
        .alert { border-radius: 10px; border: none; font-size: 0.82rem; padding: 11px 14px; }
        .alert-danger { background: rgba(220,38,38,0.08); color: #dc2626; border-inline-start: 3px solid #dc2626; }
        .theme-toggle-auth {
            position: fixed; top: 16px; right: 16px; z-index: 10;
            width: 36px; height: 36px; border-radius: 8px;
            border: 1px solid var(--card-border); background: var(--card-bg);
            color: var(--text-muted); display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s ease; backdrop-filter: blur(12px);
        }
        .theme-toggle-auth:hover { border-color: var(--accent); color: var(--accent); }
        [data-bs-theme="dark"] .icon-sun-auth { display: none; }
        [data-bs-theme="light"] .icon-moon-auth { display: none; }
    </style>
</head>
<body>
    <button class="theme-toggle-auth" onclick="
        var h=document.documentElement,c=h.getAttribute('data-bs-theme'),n=c==='dark'?'light':'dark';
        h.setAttribute('data-bs-theme',n);localStorage.setItem('balagh-theme',n);
    ">
        <i class="fas fa-sun icon-sun-auth" style="font-size:0.85rem;"></i>
        <i class="fas fa-moon icon-moon-auth" style="font-size:0.85rem;"></i>
    </button>

    <div class="register-wrapper">
        <div class="brand">
            <h1><i class="fas fa-bullhorn me-2" style="-webkit-text-fill-color:var(--accent);"></i><?= __('app.brand') ?></h1>
            <p><?= __('auth.register_subtitle') ?></p>
        </div>
        <div class="register-card">
            <?php if ($errors = \App\Helpers\Session::getFlash('errors')): ?>
            <div class="alert alert-danger mb-3"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul></div>
            <?php endif; ?>
            <form method="POST" action="/register">
                <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="first_name" placeholder="<?= __('users.first_name') ?>" value="<?= htmlspecialchars($_POST['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                            <label>Prénom</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="last_name" placeholder="<?= __('users.last_name') ?>" value="<?= htmlspecialchars($_POST['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                            <label>Nom</label>
                        </div>
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" placeholder="<?= __('users.email') ?>" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <label><i class="fas fa-envelope me-1"></i> <?= __('users.email') ?></label>
                </div>
                <div class="form-floating mb-3">
                    <input type="tel" class="form-control" name="phone" placeholder="<?= __('users.phone') ?>" value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <label><i class="fas fa-phone me-1"></i> <?= __('users.phone') ?> (<?= __('reports.optional') ?>)</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" name="password" placeholder="<?= __('users.password') ?>" required minlength="6">
                    <label><i class="fas fa-lock me-1"></i> <?= __('users.password') ?></label>
                </div>
                <button type="submit" class="btn btn-register w-100"><i class="fas fa-user-plus me-2"></i><?= __('users.create') ?></button>
            </form>
            <div class="text-center mt-3">
                <span style="font-size:0.82rem;color:var(--text-muted);"><?= __('auth.already_have_account') ?></span>
                <a href="/login" class="link fw-semibold"><?= __('auth.login') ?></a>
            </div>
        </div>
    </div>

    <script src="/assets/js/i18n.js"></script>
    <script>
    (function() {
        var saved = localStorage.getItem('balagh-theme');
        if (saved) document.documentElement.setAttribute('data-bs-theme', saved);
    })();
    </script>
</body>
</html>
