<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signalement non trouvé — Balagh Alger</title>
    <meta name="theme-color" content="#6366f1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #0a0e1a; --surface: #111827; --accent: #6366f1; --text: #f1f5f9; --muted: #64748b; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); }
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 24px; text-align: center; }
        .nf-icon { font-size: 4rem; color: var(--muted); opacity: 0.3; margin-bottom: 16px; }
        h1 { font-size: 1.4rem; font-weight: 800; margin-bottom: 8px; }
        p { font-size: 0.88rem; color: var(--muted); margin-bottom: 24px; }
        .nf-code { font-family: monospace; font-size: 0.82rem; color: var(--accent); background: var(--surface); padding: 6px 14px; border-radius: 8px; display: inline-block; margin-bottom: 20px; }
        .nf-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 12px; background: var(--accent); color: #fff; text-decoration: none; font-size: 0.88rem; font-weight: 600; transition: all 0.2s; }
        .nf-btn:active { transform: scale(0.95); }
    </style>
</head>
<body>
    <div>
        <div class="nf-icon"><i class="fas fa-file-circle-xmark"></i></div>
        <h1>Signalement introuvable</h1>
        <div class="nf-code"><?= htmlspecialchars($code ?? '') ?></div>
        <p>Ce signalement n'existe pas ou a été supprimé.</p>
        <a href="/" class="nf-btn"><i class="fas fa-home"></i> Retour à l'accueil</a>
    </div>
</body>
</html>
