<?php
/**
 * Détection local vs production pour choisir les identifiants MySQL.
 *
 * Forcer explicitement (Apache SetEnv, .env chargé avant ce fichier, etc.) :
 *   APP_ENV=local  ou  USE_LOCAL_DB=1   → config XAMPP
 *   APP_ENV=production  ou  USE_PRODUCTION_DB=1  → config hébergeur
 */
$appEnv = strtolower((string) getenv('APP_ENV'));
if ($appEnv === 'local' || getenv('USE_LOCAL_DB') === '1') {
    $isLocal = true;
} elseif ($appEnv === 'production' || getenv('USE_PRODUCTION_DB') === '1') {
    $isLocal = false;
} else {
    $rawHost = (string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '');
    $httpHost = strtolower(trim($rawHost));
    if ($httpHost !== '') {
        $httpHost = explode(':', $httpHost, 2)[0];
    }

    $isLocal = false;

    if ($httpHost !== '') {
        $isLocal = in_array($httpHost, ['localhost', '127.0.0.1', '::1'], true)
            || preg_match('/\.(?:local|test|localhost)$/', $httpHost) === 1;
    }

    if (!$isLocal) {
        $serverAddr = (string) ($_SERVER['SERVER_ADDR'] ?? '');
        $isLocal = ($serverAddr === '127.0.0.1' || $serverAddr === '::1');
    }

    if (!$isLocal) {
        $remoteAddr = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        $isLocal = ($remoteAddr === '127.0.0.1' || $remoteAddr === '::1');
    }
}

if ($isLocal) {
    // Configuration locale (XAMPP)
    return [
        'host'     => 'localhost',
        'dbname'   => 'min_transport',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ];
}

// Configuration en ligne (Production)
return [
    'host'     => 'localhost',
    'dbname'   => 'u421683743_transport',
    'username' => 'u421683743_transport',
    'password' => 'GRACEsoliste1234@!!',
    'charset'  => 'utf8mb4',
];
