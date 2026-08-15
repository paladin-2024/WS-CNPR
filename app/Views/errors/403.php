<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Accès refusé</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #F0F4F8; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .container { text-align: center; padding: 40px; }
        .code { font-size: 120px; font-weight: 800; color: #E2E8F0; line-height: 1; }
        h1 { font-size: 24px; color: #1A2744; margin: 16px 0 8px; }
        p { color: #64748B; margin-bottom: 24px; }
        a { display: inline-block; padding: 12px 24px; background: #007FFF; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s; }
        a:hover { background: #0050CC; }
    </style>
</head>
<body>
    <div class="container">
        <div class="code">403</div>
        <h1>Accès refusé</h1>
        <p>Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
        <a href="<?= defined('BASE_PATH') ? BASE_PATH : '' ?>/admin">Retour au tableau de bord</a>
    </div>
</body>
</html>
