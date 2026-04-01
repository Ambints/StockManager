<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>404 — Page introuvable</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; display: flex; align-items: center;
               justify-content: center; min-height: 100vh; background: #f5f4f0; margin: 0; text-align: center; }
        h1 { font-size: 72px; font-weight: 700; color: #e2e0d8; margin: 0; }
        h2 { font-size: 20px; font-weight: 600; margin: 8px 0 16px; }
        a  { color: #2a5bd7; font-size: 14px; }
    </style>
</head>
<body>
    <div>
        <h1>404</h1>
        <h2>Page introuvable</h2>
        <a href="<?= defined('APP_URL') ? APP_URL : '/' ?>/dashboard">← Retour au tableau de bord</a>
    </div>
</body>
</html>
