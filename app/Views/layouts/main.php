<?php 
use App\Core\Auth;
use App\Core\Csrf;
use App\Controllers\ConfigController;
$appName = ConfigController::get('app_name', 'Ministère des Transports');
$appLogo = ConfigController::get('app_logo', '');
$appSlogan = ConfigController::get('app_slogan', 'Portail Numérique');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars(Csrf::token()) ?>">
    <title><?= htmlspecialchars($pageTitle ?? 'Accueil') ?> - <?= htmlspecialchars($appName) ?></title>
    <?php if (!empty($appLogo)): ?>
    <link rel="icon" type="image/<?= pathinfo($appLogo, PATHINFO_EXTENSION) ?: 'png' ?>" href="<?= BASE_PATH ?>/public/<?= htmlspecialchars($appLogo) ?>">
    <?php else: ?>
    <link rel="icon" type="image/png" href="<?= BASE_PATH ?>/public/assets/icons/favicon-32.png">
    <?php endif; ?>
    <link rel="manifest" href="<?= BASE_PATH ?>/public/manifest.json">
    <meta name="theme-color" content="#007FFF">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
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

    <!-- Back to Top -->
    <button id="back-to-top" style="display:none; position:fixed; bottom:24px; right:24px; width:46px; height:46px; border-radius:50%; background:#007FFF; color:white; border:none; cursor:pointer; z-index:1000; box-shadow:0 4px 12px rgba(0,127,255,0.3); align-items:center; justify-content:center;">
        <i data-lucide="chevron-up" style="width:22px;height:22px;"></i>
    </button>

    <!-- Search Modal -->
    <div id="search-modal" onclick="closeSearchModal()" style="position:fixed; inset:0; background-color:rgba(0,0,0,0.5); z-index:50000; display:none; align-items:flex-start; justify-content:center; padding-top:12vh; backdrop-filter:blur(4px);">
        <div onclick="event.stopPropagation()" style="background:white; border-radius:14px; width:100%; max-width:560px; box-shadow:0 24px 64px rgba(0,0,0,0.2); overflow:hidden; animation:fadeInUp 0.2s ease; margin:0 16px;">
            <!-- Input -->
            <div style="display:flex; align-items:center; gap:12px; padding:14px 18px; border-bottom:1px solid #eee;">
                <i data-lucide="search" style="width:18px;height:18px;color:#aaa;flex-shrink:0;"></i>
                <input id="search-input" type="text" placeholder="Rechercher un service, une page…" oninput="filterSearchResults()" style="flex:1; border:none; outline:none; font-size:16px; color:#333; background:transparent; font-family:Poppins,sans-serif;">
                <button onclick="closeSearchModal()" style="background:none; border:none; cursor:pointer; color:#aaa; display:flex;">
                    <i data-lucide="x" style="width:18px;height:18px;"></i>
                </button>
            </div>
            <!-- Results -->
            <ul id="search-results" style="list-style:none; max-height:360px; overflow-y:auto; padding:8px 0;"></ul>
            <!-- Footer hint -->
            <div style="padding:10px 18px; border-top:1px solid #eee; display:flex; gap:16px;">
                <span style="font-size:11px; color:#aaa; display:flex; align-items:center; gap:4px;">
                    <kbd style="background:#f5f5f5; border:1px solid #ddd; border-radius:4px; padding:1px 5px; font-size:10px; font-family:monospace;">↩</kbd> Sélectionner
                </span>
                <span style="font-size:11px; color:#aaa; display:flex; align-items:center; gap:4px;">
                    <kbd style="background:#f5f5f5; border:1px solid #ddd; border-radius:4px; padding:1px 5px; font-size:10px; font-family:monospace;">↑↓</kbd> Naviguer
                </span>
                <span style="font-size:11px; color:#aaa; display:flex; align-items:center; gap:4px;">
                    <kbd style="background:#f5f5f5; border:1px solid #ddd; border-radius:4px; padding:1px 5px; font-size:10px; font-family:monospace;">Esc</kbd> Fermer
                </span>
            </div>
        </div>
    </div>

    <div style="min-height:100vh; display:flex; flex-direction:column;">
        <!-- Flag Band -->
        <div class="flag-band">
            <div class="flag-blue"></div>
            <div class="flag-yellow"></div>
            <div class="flag-red"></div>
        </div>

        <!-- Header -->
        <header class="header">
            <div class="header-container">
                <a href="<?= BASE_PATH ?>/" class="logo header-logo">
                    <?php if (!empty($appLogo)): ?>
                    <img src="<?= BASE_PATH ?>/public/<?= htmlspecialchars($appLogo) ?>" alt="<?= htmlspecialchars($appName) ?>" style="width:40px;height:40px;object-fit:contain;">
                    <?php else: ?>
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#007FFF,#005FCC);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:16px;font-family:Poppins,sans-serif;letter-spacing:0.5px;box-shadow:0 2px 8px rgba(0,127,255,0.3);">MT</div>
                    <?php endif; ?>
                    <span class="logo-text"><?= htmlspecialchars($appName) ?></span>
                </a>

                <nav class="main-nav" id="main-nav">
                    <ul class="nav-menu">
                        <li><a href="<?= BASE_PATH ?>/" class="nav-link<?= ($currentPage ?? '') === '/' ? ' nav-link-active' : '' ?>"><i data-lucide="home" style="width:14px;height:14px;margin-right:5px;vertical-align:middle;"></i> Accueil</a></li>
                        <li><a href="<?= BASE_PATH ?>/services" class="nav-link<?= ($currentPage ?? '') === '/services' ? ' nav-link-active' : '' ?>"><i data-lucide="layers" style="width:14px;height:14px;margin-right:5px;vertical-align:middle;"></i> Services</a></li>
                        <li><a href="<?= BASE_PATH ?>/about" class="nav-link<?= ($currentPage ?? '') === '/about' ? ' nav-link-active' : '' ?>"><i data-lucide="info" style="width:14px;height:14px;margin-right:5px;vertical-align:middle;"></i> À propos</a></li>
                        <li><a href="<?= BASE_PATH ?>/contact" class="nav-link<?= ($currentPage ?? '') === '/contact' ? ' nav-link-active' : '' ?>"><i data-lucide="phone" style="width:14px;height:14px;margin-right:5px;vertical-align:middle;"></i> Contact</a></li>

                        <?php if (Auth::check()): ?>
                            <li><a href="<?= BASE_PATH ?>/admin" class="nav-link<?= strpos($currentPage ?? '', '/admin') === 0 ? ' nav-link-active' : '' ?>"><i data-lucide="layout-dashboard" style="width:14px;height:14px;margin-right:5px;vertical-align:middle;"></i> Tableau de bord</a></li>
                            <li><a href="<?= BASE_PATH ?>/logout" class="btn btn-ghost" style="color:#CE1021;gap:6px;font-size:14px;"><i data-lucide="log-out" style="width:14px;height:14px;"></i> Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="<?= BASE_PATH ?>/login" class="btn btn-primary" style="gap:6px;font-size:14px;"><i data-lucide="log-in" style="width:14px;height:14px;"></i> Connexion</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div class="header-actions">
                    <button onclick="openSearchModal()" class="header-icon-btn" title="Rechercher (Ctrl+K)">
                        <i data-lucide="search" style="width:18px;height:18px;"></i>
                        <span class="search-hint">Ctrl K</span>
                    </button>
                    <button class="nav-toggle" aria-label="Menu" id="nav-toggle">
                        <i data-lucide="menu" style="width:22px;height:22px;color:#444;"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?? 'info' ?>" style="max-width:1200px;margin:16px auto;padding:12px 24px;">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- Main Content -->
        <main style="flex:1;">
            <?= $content ?>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-container">
                <div class="footer-section">
                    <h4><?= htmlspecialchars($appName) ?></h4>
                    <p class="text-muted"><?= htmlspecialchars($appSlogan) ?> du Ministère Provincial des Transports, Voies de Communication et Désenclavement</p>
                </div>
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_PATH ?>/services">Services en ligne</a></li>
                        <li><a href="<?= BASE_PATH ?>/about">À propos du ministère</a></li>
                        <li><a href="<?= BASE_PATH ?>/contact">Nous contacter</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Informations</h4>
                    <ul class="footer-links">
                        <li><a href="#">Textes réglementaires</a></li>
                        <li><a href="#">Appels d'offres</a></li>
                        <li><a href="#">Actualités</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 Ministère des Transports. Tous droits réservés.</p>
            </div>
        </footer>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="<?= ASSETS_PATH ?>/js/app.js"></script>
    <script>
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

        // Initialize Lucide icons on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
        // Search modal
        const searchItems = [
            { label: 'Accueil', icon: 'home', href: '<?= BASE_PATH ?>/' },
            { label: 'Enregistrement des conducteurs', icon: 'id-card', href: '<?= BASE_PATH ?>/services' },
            { label: 'Immatriculation des véhicules', icon: 'car', href: '<?= BASE_PATH ?>/services' },
            { label: 'Paiement des taxes', icon: 'credit-card', href: '<?= BASE_PATH ?>/services' },
            { label: 'Vérification en ligne', icon: 'shield-check', href: '<?= BASE_PATH ?>/services' },
            { label: 'À propos du ministère', icon: 'info', href: '<?= BASE_PATH ?>/about' },
            { label: 'Nous contacter', icon: 'phone', href: '<?= BASE_PATH ?>/contact' },
            { label: 'Connexion', icon: 'search', href: '<?= BASE_PATH ?>/login' },
            { label: 'Créer un compte', icon: 'search', href: '<?= BASE_PATH ?>/register' },
        ];

        function openSearchModal() {
            const modal = document.getElementById('search-modal');
            modal.style.display = 'flex';
            filterSearchResults();
            setTimeout(() => document.getElementById('search-input').focus(), 50);
        }

        function closeSearchModal() {
            document.getElementById('search-modal').style.display = 'none';
            document.getElementById('search-input').value = '';
        }

        function filterSearchResults() {
            const query = document.getElementById('search-input').value.toLowerCase().trim();
            const filtered = query ? searchItems.filter(i => i.label.toLowerCase().includes(query)) : searchItems;
            const list = document.getElementById('search-results');

            if (filtered.length === 0) {
                list.innerHTML = '<li style="padding:20px;text-align:center;color:#aaa;font-size:14px;">Aucun résultat trouvé</li>';
                return;
            }

            list.innerHTML = filtered.map(item => `
                <li>
                    <a href="${item.href}" style="width:100%;display:flex;align-items:center;gap:12px;padding:11px 18px;background:none;text-decoration:none;font-size:15px;color:#333;font-family:Poppins,sans-serif;transition:background 0.15s;" onmouseenter="this.style.background='#F0F7FF'" onmouseleave="this.style.background='none'">
                        <span style="width:34px;height:34px;border-radius:8px;background:#007FFF18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i data-lucide="${item.icon}" style="width:16px;height:16px;color:#007FFF;"></i>
                        </span>
                        <span style="flex:1;">${item.label}</span>
                        <i data-lucide="chevron-right" style="width:16px;height:16px;color:#bbb;"></i>
                    </a>
                </li>
            `).join('');

            if (window.lucide) lucide.createIcons();
        }

        // Ctrl+K shortcut
        document.addEventListener('keydown', function(e) {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                openSearchModal();
            }
            if (e.key === 'Escape') closeSearchModal();
        });

        // Mobile nav toggle
        document.getElementById('nav-toggle').addEventListener('click', function() {
            document.getElementById('main-nav').classList.toggle('open');
        });

        // Back to top
        (function() {
            var btn = document.getElementById('back-to-top');
            window.addEventListener('scroll', function() {
                btn.style.display = window.scrollY > 300 ? 'flex' : 'none';
            });
            btn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        })();
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?= BASE_PATH ?>/public/sw.js').catch(function() {});
            });
        }
    </script>
</body>
</html>
