<?php
/**
 * Connexion PostgreSQL — configuration exclusivement via variables d'environnement.
 *
 * En local, copier .env.example vers .env et ajuster les valeurs (voir App\Core\Env,
 * chargé depuis index.php). Les valeurs par défaut ci-dessous ne sont que des
 * conventions de développement local, jamais de vrais identifiants de production.
 */
use App\Core\Env;

return [
    'driver'   => Env::get('DB_CONNECTION', 'pgsql'),
    'host'     => Env::get('DB_HOST', '127.0.0.1'),
    'port'     => Env::get('DB_PORT', '5432'),
    'dbname'   => Env::get('DB_DATABASE', 'min_transport'),
    'username' => Env::get('DB_USERNAME', 'postgres'),
    'password' => Env::get('DB_PASSWORD', ''),
    'charset'  => Env::get('DB_CHARSET', 'utf8'),
];
