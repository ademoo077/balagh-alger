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
    <title><?= __('auth.forgot_title') ?> - Balagh Alger</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --accent: #6366f1; --accent-light: #818cf8; --cyan: #22d3ee; --bg: #0b0f19; }
        body { background: var(--bg); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', 'Noto Sans Arabic', system-ui, sans-serif; }
        body::before { content: ''; position: fixed; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle at 30% 40%, rgba(99,102,241,0.08) 0%, transparent 50%); }
        .wrapper { position: relative; z-index: 1; width: 100%; max-width: 400px; padding: 20px; }
        .card-modern { background: rgba(21,28,44,0.9); border: 1px solid rgba(255,255,255,0.06); border-radius: 20px; padding: 36px; backdrop-filter: blur(20px); box-shadow: 0 8px 32px rgba(0,0,0,0.4); animation: fadeInUp 0.5s ease-out; }
        .card-modern h3 { font-weight: 700; font-size: 1.3rem; text-align: center; margin-bottom: 24px; background: linear-gradient(135deg, #f1f5f9, var(--accent-light)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .form-floating > .form-control { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; color: #f1f5f9; }
        .form-floating > .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
        .form-floating > label { color: #94a3b8; }
        .form-floating > .form-control:focus ~ label, .form-floating > .form-control:not(:placeholder-shown) ~ label { color: var(--accent-light); }
        html[dir="rtl"] .form-floating > label { transform-origin: right top; }
        .btn-submit { background: linear-gradient(135deg, var(--accent), #4f46e5); border: none; color: white; padding: 12px; font-weight: 700; border-radius: 12px; transition: all 0.25s ease; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(99,102,241,0.4); }
        .link { color: var(--accent-light); text-decoration: none; }
        .alert { border-radius: 10px; border: none; font-size: 0.85rem; }
        .alert-danger { background: rgba(248,113,113,0.1); color: #f87171; border-inline-start: 3px solid #f87171; }
        .alert-success { background: rgba(52,211,153,0.1); color: #34d399; border-inline-start: 3px solid #34d399; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card-modern">
            <h3><i class="fas fa-key me-2"></i><?= __('auth.forgot_title') ?></h3>
            <?php if ($msg = \App\Helpers\Session::getFlash('error')): ?>
            <div class="alert alert-danger mb-3"><?= $msg ?></div>
            <?php endif; ?>
            <?php if ($msg = \App\Helpers\Session::getFlash('success')): ?>
            <div class="alert alert-success mb-3"><?= $msg ?></div>
            <?php endif; ?>
            <form method="POST" action="/forgot-password">
                <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                <div class="form-floating mb-4">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                    <label><i class="fas fa-envelope me-1"></i> <?= __('auth.forgot_subtitle') ?></label>
                </div>
                <button type="submit" class="btn btn-submit w-100"><i class="fas fa-paper-plane me-2"></i><?= __('auth.forgot_submit') ?></button>
            </form>
            <div class="text-center mt-3"><a href="/login" class="link" style="font-size:0.85rem;"><i class="fas fa-arrow-<?= $isRtl ? 'right' : 'left' ?> me-1"></i> <?= __('auth.forgot_back') ?></a></div>
        </div>
    </div>
</body>
</html>
