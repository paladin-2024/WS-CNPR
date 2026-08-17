<?php
use App\Core\Auth;
use App\Core\Csrf;
use App\Controllers\ConfigController;

$user = Auth::user();
$appName = ConfigController::get('app_name', 'Ministère des Transports');
$appLogo = ConfigController::get('app_logo', '');
$userRole = $user['role'] ?? '';
$userPrenom = $user['prenom'] ?? '';
$userNom = $user['nom'] ?? '';
$userInitials = mb_strtoupper(mb_substr($userPrenom, 0, 1) . mb_substr($userNom, 0, 1));

$roleLabels = [
    'admin'                => 'Administrateur',
    'minister_admin'       => 'Admin Ministère',
    'agent'                => 'Agent',
    'inspecteur'           => 'Inspecteur',
    'gestionnaire_parking' => 'Gest. Parking',
    'transporteur'         => 'Transporteur',
    'conducteur'           => 'Conducteur',
    'citoyen'              => 'Citoyen',
    'imprimeur'            => 'Imprimeur',
    'receptionnaire'       => 'Réceptionnaire',
    'operateur_saisie'     => 'Opérateur de saisie',
    'validateur'           => 'Validateur',
    'receveur'             => 'Receveur',
    'instructeur'          => 'Instructeur',
];

$roleColors = [
    'admin'                => '#DC2626',
    'minister_admin'       => '#7C3AED',
    'agent'                => '#0369A1',
    'inspecteur'           => '#D97706',
    'gestionnaire_parking' => '#059669',
    'imprimeur'            => '#6366F1',
    'receptionnaire'       => '#0891B2',
    'operateur_saisie'     => '#0369A1',
    'validateur'           => '#7C3AED',
    'receveur'             => '#0891B2',
    'conducteur'           => '#3B82F6',
    'transporteur'         => '#8B5CF6',
    'citoyen'              => '#64748B',
    'instructeur'          => '#D97706',
];

$roleColor = $roleColors[$userRole] ?? '#005FCC';
$roleLabel = $roleLabels[$userRole] ?? $userRole;

$menuItems = [
    ['path' => '/admin',               'label' => 'Tableau de bord',   'icon' => 'layout-dashboard', 'roles' => ['admin','minister_admin','agent','operateur_saisie','validateur','imprimeur','receveur','receptionnaire','inspecteur','gestionnaire_parking','conducteur','transporteur','citoyen']],
    ['path' => '/admin/utilisateurs',  'label' => 'Utilisateurs',      'icon' => 'users',            'roles' => ['admin','minister_admin']],
    ['path' => '/admin/conducteurs',   'label' => 'Conducteurs',       'icon' => 'user-check',       'roles' => ['admin','minister_admin','agent','operateur_saisie']],
    ['path' => '/admin/paiement',      'label' => 'Paiement Brevet',   'icon' => 'check-circle',     'roles' => ['admin','minister_admin','agent','validateur']],
    ['path' => '/admin/statistiques',  'label' => 'Statistiques',      'icon' => 'bar-chart-3',      'roles' => ['admin','minister_admin','agent']],
    ['path' => '/admin/imprimeur',     'label' => 'Imprimeur Brevets', 'icon' => 'printer',          'roles' => ['admin','minister_admin','imprimeur']],
    ['path' => '/admin/receptionnaire','label' => 'Réception Brevets', 'icon' => 'clipboard-check',  'roles' => ['admin','minister_admin','receveur','receptionnaire']],
    ['path' => '/admin/config',        'label' => 'Configuration',     'icon' => 'settings',         'roles' => ['admin']],
];

$filteredMenu = array_filter($menuItems, function($item) use ($userRole) {
    return $userRole === 'admin' || in_array($userRole, $item['roles']);
});

$activePageLabel = 'Tableau de bord';
foreach ($filteredMenu as $item) {
    if ($item['path'] === ($currentPage ?? '')) {
        $activePageLabel = $item['label'];
        break;
    }
}

$french_months = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
$todayDate = date('j') . ' ' . $french_months[date('n')] . ' ' . date('Y');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars(Csrf::token()) ?>">
    <title><?= htmlspecialchars($pageTitle ?? 'Administration') ?> - <?= htmlspecialchars($appName) ?></title>
    <?php if (!empty($appLogo)): ?>
    <link rel="icon" type="image/<?= pathinfo($appLogo, PATHINFO_EXTENSION) ?: 'png' ?>" href="<?= BASE_PATH ?>/public/<?= htmlspecialchars($appLogo) ?>">
    <?php else: ?>
    <link rel="icon" type="image/png" href="<?= BASE_PATH ?>/public/assets/icons/favicon-32.png">
    <?php endif; ?>
    <link rel="manifest" href="<?= BASE_PATH ?>/public/manifest.json">
    <meta name="theme-color" content="#007FFF">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/globals.css">
    <style>
        * { box-sizing: border-box; }

        .admin-root {
            display: flex;
            min-height: 100vh;
            background: #F0F4F8;
        }

        /* ── Sidebar ── */
        .admin-sidebar {
            width: 250px;
            flex-shrink: 0;
            background: linear-gradient(180deg, #0A1628 0%, #0F2041 100%);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 200;
            transition: transform 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        .admin-sidebar-inner {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        /* ── Corps principal ── */
        .admin-body {
            flex: 1;
            margin-left: 250px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

        /* ── Topbar ── */
        .admin-topbar {
            background: white;
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #E5EAF2;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            gap: 12px;
        }

        .admin-topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex: 1;
        }

        .admin-topbar-title {
            min-width: 0;
        }

        .admin-topbar-title h1 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #1A2744;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .admin-topbar-date {
            font-size: 12px;
            color: #8A99B4;
        }

        .admin-topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        /* ── Contenu ── */
        .admin-content {
            flex: 1;
            padding: 24px;
        }

        /* ── Overlay mobile ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 199;
        }

        /* ── Boutons mobile ── */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-close-btn {
            display: none;
            background: none;
            border: none;
            color: rgba(255,255,255,0.6);
            cursor: pointer;
            padding: 4px;
        }

        /* ── Navigation sidebar ── */
        .sidebar-nav {
            flex: 1;
            padding: 4px 8px;
            overflow-y: auto;
        }

        .sidebar-nav-label {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,0.3);
            letter-spacing: 1px;
            padding: 8px 12px 4px;
            text-transform: uppercase;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 2px;
            text-decoration: none;
            transition: all 0.15s;
            font-size: 14px;
            border-left: 3px solid transparent;
            color: rgba(255,255,255,0.65);
        }

        .sidebar-nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.85);
        }

        .sidebar-nav-link.active {
            background: rgba(0,127,255,0.25);
            color: #60AFFF;
            font-weight: 600;
            border-left-color: #007FFF;
        }

        .sidebar-footer {
            padding: 12px 8px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-footer-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: rgba(255,255,255,0.5);
            font-size: 14px;
            transition: all 0.15s;
        }

        .sidebar-footer-link:hover {
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.8);
        }

        .sidebar-logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: #FC8181;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.15s;
        }

        .sidebar-logout-btn:hover {
            background: rgba(252,129,129,0.1);
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .admin-sidebar { width: 220px; }
            .admin-body { margin-left: 220px; }
        }

        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); width: 260px; }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-body { margin-left: 0; }
            .sidebar-overlay.active { display: block; }
            .mobile-menu-btn { display: flex; }
            .sidebar-close-btn { display: flex; }
            .admin-content { padding: 16px; }
            .admin-topbar { padding: 0 16px; height: 56px; }
            .admin-topbar-date { display: none; }
        }

        @media (max-width: 480px) {
            .admin-content { padding: 12px; }
            .admin-topbar { padding: 0 12px; }
            .topbar-icon-btn { display: none !important; }
        }
    </style>
</head>
<body>
    <!-- Top Progress Bar -->
    <div id="top-progress-bar" style="position:fixed;top:0;left:0;width:0%;height:3px;background:linear-gradient(90deg,#007FFF,#00D4FF);z-index:99999;transition:width 0.3s ease;box-shadow:0 2px 8px rgba(0,127,255,0.4);"></div>
    
    <div class="admin-root">

        <!-- Overlay mobile -->
        <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

        <!-- Sidebar -->
        <aside class="admin-sidebar" id="admin-sidebar">
            <div class="admin-sidebar-inner">

                <!-- Logo -->
                <div style="padding:20px 20px 16px; border-bottom:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
                    <a href="<?= BASE_PATH ?>/" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
                        <?php if (!empty($appLogo)): ?>
                        <img src="<?= BASE_PATH ?>/public/<?= htmlspecialchars($appLogo) ?>" alt="Logo" style="width:40px;height:40px;object-fit:contain;border-radius:8px;">
                        <?php else: ?>
                        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#007FFF,#0050CC);display:flex;align-items:center;justify-content:center;font-weight:800;color:white;font-size:14px;letter-spacing:1px;box-shadow:0 2px 8px rgba(0,127,255,0.4);flex-shrink:0;">MT</div>
                        <?php endif; ?>
                        <div>
                            <div style="color:white;font-weight:700;font-size:13px;line-height:1.2;"><?= htmlspecialchars($appName) ?></div>
                            <div style="color:rgba(255,255,255,0.5);font-size:11px;">Administration</div>
                        </div>
                    </a>
                    <button class="sidebar-close-btn" onclick="closeSidebar()">
                        <i data-lucide="x" style="width:20px;height:20px;"></i>
                    </button>
                </div>

                <!-- Profil utilisateur -->
                <div style="margin:16px 12px; background:rgba(255,255,255,0.07); border-radius:10px; padding:12px; display:flex; align-items:center; gap:10px; flex-shrink:0;">
                    <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,<?= $roleColor ?>,<?= $roleColor ?>99);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:14px;flex-shrink:0;">
                        <?= htmlspecialchars($userInitials) ?>
                    </div>
                    <div style="min-width:0;">
                        <div style="color:white;font-weight:600;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?= htmlspecialchars($userPrenom . ' ' . $userNom) ?>
                        </div>
                        <div style="display:inline-flex;align-items:center;gap:4px;background:<?= $roleColor ?>30;border-radius:20px;padding:2px 8px;margin-top:2px;">
                            <i data-lucide="shield" style="width:10px;height:10px;color:<?= $roleColor ?>;"></i>
                            <span style="font-size:10px;color:<?= $roleColor ?>;font-weight:600;"><?= htmlspecialchars($roleLabel) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="sidebar-nav">
                    <div class="sidebar-nav-label">Navigation</div>
                    <?php foreach ($filteredMenu as $item):
                        $isActive = ($currentPage ?? '') === $item['path'];
                    ?>
                        <a href="<?= BASE_PATH . $item['path'] ?>" class="sidebar-nav-link<?= $isActive ? ' active' : '' ?>" style="<?= $isActive ? 'color:#60AFFF;' : '' ?>">
                            <i data-lucide="<?= $item['icon'] ?>" style="width:18px;height:18px;"></i>
                            <span style="flex:1;"><?= htmlspecialchars($item['label']) ?></span>
                            <?php if ($isActive): ?>
                                <i data-lucide="chevron-right" style="width:14px;height:14px;"></i>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <!-- Pied de sidebar -->
                <div class="sidebar-footer">
                    <a href="<?= BASE_PATH ?>/" class="sidebar-footer-link">
                        <i data-lucide="home" style="width:16px;height:16px;"></i>
                        <span>Retour au site</span>
                    </a>
                    <a href="<?= BASE_PATH ?>/profile" class="sidebar-footer-link">
                        <i data-lucide="user" style="width:16px;height:16px;"></i>
                        <span>Mon Profil</span>
                    </a>
                    <a href="<?= BASE_PATH ?>/logout" class="sidebar-logout-btn">
                        <i data-lucide="log-out" style="width:16px;height:16px;"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>

            </div>
        </aside>

        <!-- Corps principal -->
        <div class="admin-body">

            <!-- Topbar -->
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button class="mobile-menu-btn" onclick="openSidebar()">
                        <i data-lucide="menu" style="width:22px;height:22px;color:#333;"></i>
                    </button>
                    <div class="admin-topbar-title">
                        <h1><?= htmlspecialchars($activePageLabel) ?></h1>
                        <div class="admin-topbar-date"><?= htmlspecialchars($todayDate) ?></div>
                    </div>
                </div>

                <div class="admin-topbar-right">
                    <button class="topbar-icon-btn" style="width:38px;height:38px;border-radius:8px;border:1px solid #E5EAF2;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;position:relative;">
                        <i data-lucide="bell" style="width:18px;height:18px;color:#555;"></i>
                        <span style="position:absolute;top:6px;right:6px;width:8px;height:8px;background:#EF4444;border-radius:50%;border:2px solid white;"></span>
                    </button>
                    <button class="topbar-icon-btn" style="width:38px;height:38px;border-radius:8px;border:1px solid #E5EAF2;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="settings" style="width:18px;height:18px;color:#555;"></i>
                    </button>
                    <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,<?= $roleColor ?>,<?= $roleColor ?>99);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:13px;flex-shrink:0;">
                        <?= htmlspecialchars($userInitials) ?>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] ?? 'info' ?>" style="margin:16px 24px 0;padding:12px 24px;">
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <!-- Contenu de la page -->
            <main class="admin-content">
                <?= $content ?>
            </main>

        </div>
    </div>

    <script src="<?= ASSETS_PATH ?>/js/vendor/lucide.min.js"></script>
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
        });
    </script>
    <script>
        // Initialiser les icônes Lucide
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
    <script src="<?= ASSETS_PATH ?>/js/app.js"></script>
    <script>
        function openSidebar() {
            document.getElementById('admin-sidebar').classList.add('open');
            document.getElementById('sidebar-overlay').classList.add('active');
        }
        function closeSidebar() {
            document.getElementById('admin-sidebar').classList.remove('open');
            document.getElementById('sidebar-overlay').classList.remove('active');
        }
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
