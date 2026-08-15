<?php
/**
 * Point d'entrée unique - Portail Numérique du Ministère des Transports
 *
 * Architecture MVC avec point d'entrée à la racine du projet.
 * Les assets (CSS, JS, images) sont servis depuis le dossier public/.
 */

// Chemins absolus - Détection automatique du chemin de base
define('ROOT_PATH', __DIR__);  // Chemin serveur pour les includes
define('ROOT_DIR', __DIR__);    // Alias pour compatibilité avec le code existant

require ROOT_PATH . '/vendor/autoload.php';

// Charger les variables d'environnement (.env, non versionné) avant toute
// décision qui en dépend (affichage des erreurs, cookies de session, etc.)
App\Core\Env::load(ROOT_PATH . '/.env');

$appEnv = App\Core\Env::get('APP_ENV', 'production');
$isProduction = $appEnv === 'production';

if ($isProduction) {
    // En production : ne jamais afficher les erreurs (fuite de chemins/stack
    // traces), les journaliser à la place.
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT_PATH . '/storage/logs/php-error.log');
} else {
    ini_set('display_errors', '1');
    ini_set('log_errors', '1');
}
error_reporting(E_ALL);

// Cookies de session : httponly + samesite systématiques, secure dès que la
// requête arrive en HTTPS (terminaison TLS faite par le reverse proxy en
// production - voir deploy/nginx.conf.example pour le header transmis).
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');

ob_start();
session_start();

// Détecter automatiquement le chemin URL
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptName === '\\' || $scriptName === '/') {
    $scriptName = '';
}
define('BASE_PATH', $scriptName);  // URL pour les redirections (vide si racine)
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('ASSETS_PATH', BASE_PATH . '/public/assets');

// Charger la configuration
$config = require ROOT_PATH . '/config/database.php';

// Instancier le routeur
$router = new App\Core\Router();

// ── Routes publiques ─────────────────────────────────────────────────────
$router->get('/', 'HomeController@index');
$router->get('/services', 'ServicesController@index');
$router->get('/about', 'AboutController@index');
$router->get('/contact', 'ContactController@index');
$router->post('/contact', 'ContactController@send');

// ── Vérification publique brevet ──────────────────────────────────────────
$router->get('/verification/{id}', 'VerificationController@show');
$router->post('/verification/signaler', 'VerificationController@signalerFraude');

// ── Auth ─────────────────────────────────────────────────────────────────
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// ── Admin (auth + rôle requis) ─────────────────────────────────────────
$router->get('/admin', 'AdminController@dashboard', ['auth']);
$router->get('/admin/utilisateurs', 'AdminController@utilisateurs', ['auth', 'role:admin,minister_admin']);
$router->get('/admin/conducteurs/ajouter', 'AdminController@conducteurForm', ['auth', 'role:admin,minister_admin,agent,operateur_saisie']);
$router->get('/admin/conducteurs/modifier/{id}', 'AdminController@conducteurForm', ['auth', 'role:admin,minister_admin,agent,operateur_saisie']);
$router->get('/admin/conducteurs', 'AdminController@conducteurs', ['auth', 'role:admin,minister_admin,agent,operateur_saisie']);
$router->post('/admin/conducteurs/save', 'AdminController@saveConducteur', ['auth', 'role:admin,minister_admin,agent,operateur_saisie']);
$router->get('/admin/vehicules', 'AdminController@vehicules', ['auth', 'role:admin,minister_admin,agent']);
$router->get('/admin/taxes', 'AdminController@taxes', ['auth', 'role:admin,minister_admin,agent']);
$router->get('/admin/paiement', 'AdminController@paiement', ['auth', 'role:admin,minister_admin,agent,validateur']);
$router->post('/admin/paiement/valider', 'AdminController@validerPaiement', ['auth', 'role:admin,minister_admin,agent,validateur']);
$router->get('/admin/paiement/export-excel', 'AdminController@exportPaiementExcel', ['auth', 'role:admin,minister_admin,agent,validateur']);
$router->get('/admin/parkings', 'AdminController@parkings', ['auth', 'role:admin,minister_admin,gestionnaire_parking']);
$router->get('/admin/statistiques', 'AdminController@statistiques', ['auth', 'role:admin,minister_admin,agent']);
$router->get('/admin/config', 'ConfigController@index', ['auth', 'role:admin']);
$router->post('/admin/config/save', 'ConfigController@save', ['auth', 'role:admin']);

// ── Profil utilisateur (auth requise) ─────────────────────────────────────
$router->get('/profile', 'ProfileController@index', ['auth']);
$router->get('/profile/edit', 'ProfileController@edit', ['auth']);
$router->post('/profile/update', 'ProfileController@update', ['auth']);

// ── Brevets - Imprimeur (auth + rôle requis) ──────────────────────────
$router->get('/admin/imprimeur', 'BrevetController@imprimeur', ['auth', 'role:admin,minister_admin,imprimeur']);
$router->get('/admin/imprimeur/download-excel', 'BrevetController@downloadExcel', ['auth', 'role:admin,minister_admin,imprimeur']);
$router->get('/admin/imprimeur/download-photos', 'BrevetController@downloadPhotos', ['auth', 'role:admin,minister_admin,imprimeur']);
$router->get('/admin/imprimeur/download-qrcodes', 'BrevetController@downloadQrcodes', ['auth', 'role:admin,minister_admin,imprimeur']);
$router->get('/admin/imprimeur/carte/{id}', 'BrevetController@carteBrevet', ['auth', 'role:admin,minister_admin,imprimeur,agent,validateur']);
$router->get('/admin/imprimeur/download-photo/{id}', 'BrevetController@downloadSinglePhoto', ['auth', 'role:admin,minister_admin,imprimeur']);
$router->get('/admin/imprimeur/download-qrcode/{id}', 'BrevetController@downloadSingleQrcode', ['auth', 'role:admin,minister_admin,imprimeur']);
$router->post('/admin/imprimeur/marquer-impression', 'BrevetController@marquerEnCoursImpression', ['auth', 'role:admin,minister_admin,imprimeur']);
$router->get('/admin/api/imprimeur', 'BrevetController@apiImprimeur', ['auth', 'role:admin,minister_admin,imprimeur']);

// ── Brevets - Réceptionnaire (auth + rôle requis) ─────────────────────
$router->get('/admin/receptionnaire', 'BrevetController@receptionnaire', ['auth', 'role:admin,minister_admin,receveur,receptionnaire']);
$router->get('/admin/api/receptionnaire', 'BrevetController@apiReceptionnaire', ['auth', 'role:admin,minister_admin,receveur,receptionnaire']);
$router->post('/admin/receptionnaire/confirmer', 'BrevetController@confirmerImpression', ['auth', 'role:admin,minister_admin,receveur,receptionnaire']);
$router->post('/admin/receptionnaire/confirmer-lot', 'BrevetController@confirmerImpressionLot', ['auth', 'role:admin,minister_admin,receveur,receptionnaire']);

// ── Admin API (AJAX, auth + rôle requis) ──────────────────────────────
$router->post('/admin/api/conducteurs', 'AdminController@apiConducteurs', ['auth', 'role:admin,minister_admin,agent,operateur_saisie']);
$router->get('/admin/api/conducteurs', 'AdminController@apiConducteurs', ['auth', 'role:admin,minister_admin,agent,operateur_saisie']);
$router->put('/admin/api/conducteurs', 'AdminController@apiConducteurs', ['auth', 'role:admin,minister_admin,agent,operateur_saisie']);
$router->delete('/admin/api/conducteurs', 'AdminController@apiConducteurs', ['auth', 'role:admin,minister_admin,agent,operateur_saisie']);
$router->post('/admin/api/vehicules', 'AdminController@apiVehicules', ['auth', 'role:admin,minister_admin,agent']);
$router->get('/admin/api/utilisateurs', 'AdminController@apiUtilisateurs', ['auth', 'role:admin,minister_admin']);
$router->post('/admin/api/utilisateurs', 'AdminController@apiUtilisateurs', ['auth', 'role:admin,minister_admin']);
$router->put('/admin/api/utilisateurs', 'AdminController@apiUtilisateurs', ['auth', 'role:admin,minister_admin']);
$router->delete('/admin/api/utilisateurs', 'AdminController@apiUtilisateurs', ['auth', 'role:admin,minister_admin']);

// ── Dispatch ───────────────────────────────────────────────────────────
$url = $_GET['url'] ?? '';
$url = '/' . ltrim(trim($url, '/'), '/');
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($url, $method);
