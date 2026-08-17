<?php 
use App\Core\Csrf;
use App\Controllers\ConfigController;
$appName = ConfigController::get('app_name', 'Ministère des Transports');
$appLogo = ConfigController::get('app_logo', '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars(Csrf::token()) ?>">
    <title><?= htmlspecialchars($pageTitle ?? '') ?> - <?= htmlspecialchars($appName) ?></title>
    <?php if (!empty($appLogo)): ?>
    <link rel="icon" type="image/<?= pathinfo($appLogo, PATHINFO_EXTENSION) ?: 'png' ?>" href="<?= BASE_PATH ?>/public/<?= htmlspecialchars($appLogo) ?>">
    <?php else: ?>
    <link rel="icon" type="image/png" href="<?= BASE_PATH ?>/public/assets/icons/favicon-32.png">
    <?php endif; ?>
    <link rel="manifest" href="<?= BASE_PATH ?>/public/manifest.json">
    <meta name="theme-color" content="#007FFF">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/globals.css">
</head>
<body>
    <!-- Top Progress Bar -->
    <div id="top-progress-bar" style="position:fixed;top:0;left:0;width:0%;height:3px;background:linear-gradient(90deg,#007FFF,#00D4FF);z-index:99999;transition:width 0.3s ease;box-shadow:0 2px 8px rgba(0,127,255,0.4);"></div>
    
    <!-- Page Loader -->
    <div class="page-loader">
        <div class="page-loader-inner">
            <?php if (!empty($appLogo)): ?>
            <img src="<?= BASE_PATH ?>/public/<?= htmlspecialchars($appLogo) ?>" alt="Logo" class="loader-logo" style="width:60px;height:60px;object-fit:contain;">
            <?php else: ?>
            <div class="loader-logo">MT</div>
            <?php endif; ?>
            <div class="loader-bar"><div class="loader-bar-fill"></div></div>
            <div class="loader-text">Chargement…</div>
        </div>
    </div>
    <?= $content ?>
    <script src="<?= ASSETS_PATH ?>/js/vendor/lucide.min.js"></script>
    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });

        // Top Progress Bar
        const progressBar = document.getElementById('top-progress-bar');
        
        // Start progress when page starts loading
        document.addEventListener('DOMContentLoaded', function() {
            if (progressBar) {
                progressBar.style.width = '30%';
            }
        });
        
        // Complete progress when page is fully loaded
        window.addEventListener('load', function() {
            if (progressBar) {
                progressBar.style.width = '100%';
                setTimeout(function() {
                    progressBar.style.opacity = '0';
                    setTimeout(function() {
                        progressBar.style.display = 'none';
                    }, 300);
                }, 200);
            }
            
            // Hide page loader
            const loader = document.querySelector('.page-loader');
            if (loader) {
                loader.style.opacity = '0';
                loader.style.transition = 'opacity 0.3s ease';
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 300);
            }
        });
    </script>
    <script src="<?= ASSETS_PATH ?>/js/app.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?= BASE_PATH ?>/public/sw.js').catch(function() {});
            });
        }
    </script>
</body>
</html>
