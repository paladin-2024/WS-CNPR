<?php
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Accès interdit : ce script ne peut être exécuté qu\'en ligne de commande (CLI).');
}

echo password_hash('admin123', PASSWORD_BCRYPT), "\n";
