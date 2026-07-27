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
    <title>404 - <?= $isRtl ? 'صفحة غير موجودة' : 'Page non trouvée' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Noto+Sans+Arabic:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --accent: #6366f1; --accent-light: #818cf8; --bg: #0b0f19; }
        body { background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Inter', 'Noto Sans Arabic', system-ui, sans-serif; }
        body::before { content: ''; position: fixed; inset: 0; background: radial-gradient(circle at 50% 50%, rgba(99,102,241,0.06) 0%, transparent 50%); }
        .error-wrapper { position: relative; z-index: 1; text-align: center; }
        .error-code { font-size: 8rem; font-weight: 800; line-height: 1; background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(34,211,238,0.2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: pulse 3s ease-in-out infinite; }
        .error-msg { color: #94a3b8; font-size: 1.1rem; margin: 16px 0 32px; }
        .btn-home { background: var(--accent); color: white; border: none; padding: 10px 28px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.25s ease; }
        .btn-home:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
    </style>
</head>
<body>
    <div class="error-wrapper">
        <div class="error-code">404</div>
        <p class="error-msg"><?= $isRtl ? 'صفحة غير موجودة' : 'Page non trouvée' ?></p>
        <a href="/dashboard" class="btn-home"><i class="fas fa-home me-2"></i><?= $isRtl ? 'العودة للوحة التحكم' : 'Retour au tableau de bord' ?></a>
    </div>
</body>
</html>
