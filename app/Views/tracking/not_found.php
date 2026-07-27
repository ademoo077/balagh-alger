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
    <title><?= __('tracking.not_found_title') ?> — <?= __('app.name') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --accent: #6366f1; --accent-light: #818cf8; --bg: #0b0f19; }
        [data-bs-theme="dark"] { --bg: #0b0f19; }
        [data-bs-theme="light"] { --bg: #f8f9fc; }
        body { background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Inter', system-ui, sans-serif; }
        body::before { content: ''; position: fixed; inset: 0; background: radial-gradient(circle at 50% 50%, rgba(99,102,241,0.06) 0%, transparent 50%); }
        .error-wrapper { position: relative; z-index: 1; text-align: center; max-width: 400px; padding: 20px; }
        .error-code { font-size: 6rem; font-weight: 800; line-height: 1; background: linear-gradient(135deg, rgba(220,38,38,0.3), rgba(217,119,6,0.3)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .error-msg { color: #94a3b8; font-size: 1.1rem; margin: 16px 0 8px; }
        .error-detail { color: #64748b; font-size: 0.85rem; margin-bottom: 24px; }
        .error-code-display { font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--accent-light); background: rgba(99,102,241,0.1); padding: 4px 12px; border-radius: 8px; display: inline-block; margin-bottom: 16px; }
        .btn-home { background: var(--accent); color: white; border: none; padding: 10px 28px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.25s ease; display: inline-block; }
        .btn-home:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); color: white; }
        .btn-outline { background: transparent; color: var(--accent-light); border: 1px solid rgba(99,102,241,0.3); padding: 10px 28px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.25s ease; display: inline-block; margin-left: 12px; }
        .btn-outline:hover { border-color: var(--accent); background: rgba(99,102,241,0.08); color: var(--accent-light); }
    </style>
    <script>
    window.__translations = <?= file_get_contents(__DIR__ . '/../../../lang/' . $lang . '.json') ?>;
    window.__lang = '<?= $lang ?>';
    </script>
</head>
<body>
    <div class="error-wrapper">
        <div class="error-code"><i class="fas fa-file-circle-exclamation"></i></div>
        <div class="error-code-display"><?= htmlspecialchars($code ?? '') ?></div>
        <p class="error-msg"><?= __('tracking.not_found_message') ?></p>
        <p class="error-detail"><?= __('tracking.not_found_detail') ?></p>
        <div>
            <a href="/suivi" class="btn-home"><i class="fas fa-arrow-left me-2"></i><?= __('tracking.try_again') ?></a>
            <a href="/" class="btn-outline"><i class="fas fa-home me-2"></i><?= __('tracking.go_home') ?></a>
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
