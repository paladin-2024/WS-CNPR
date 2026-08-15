<?php
/**
 * Router pour `php -S` (serveur de développement intégré).
 * Reproduit le comportement de .htaccess : sert les fichiers existants tels
 * quels, route tout le reste vers index.php avec $_GET['url'].
 *
 * Usage : php -S localhost:8000 router.php
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

$_GET['url'] = ltrim($path, '/');
require __DIR__ . '/index.php';
