<?php
/**
 * Script d'initialisation de la base de données
 * Crée la base, les tables et insère les utilisateurs par défaut.
 *
 * Usage : php seed.php   (en ligne de commande)
 *     ou  http://localhost/min-transport-php/database/seed.php
 */

$host     = 'localhost';
$username = 'root';
$password = '';
$dbname   = 'min_transport';
$charset  = 'utf8mb4';

try {
    // Connexion sans sélectionner de base
    $pdo = new PDO("mysql:host={$host};charset={$charset}", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Créer la base de données
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbname}`");

    echo "✔ Base de données '{$dbname}' prête.\n<br>";

    // Exécuter le schéma SQL (sans les lignes CREATE DATABASE / USE)
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    // Retirer les lignes CREATE DATABASE et USE pour éviter les doublons
    $schema = preg_replace('/^(CREATE DATABASE|USE ).*/m', '', $schema);
    $pdo->exec($schema);

    echo "✔ Tables créées.\n<br>";

    // Insérer les utilisateurs par défaut (ignore si déjà existants)
    $users = [
        [
            'nom'       => 'Admin',
            'prenom'    => 'System',
            'email'     => 'admin@mintransport.gov',
            'telephone' => '+243810000000',
            'password'  => password_hash('admin123', PASSWORD_BCRYPT),
            'role'      => 'admin',
            'statut'    => 'actif',
        ],
        [
            'nom'       => 'Agent',
            'prenom'    => 'Transport',
            'email'     => 'agent@mintransport.gov',
            'telephone' => '+243810000001',
            'password'  => password_hash('agent123', PASSWORD_BCRYPT),
            'role'      => 'agent',
            'statut'    => 'actif',
        ],
    ];

    $stmt = $pdo->prepare(
        "INSERT IGNORE INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role, statut)
         VALUES (:nom, :prenom, :email, :telephone, :password, :role, 'actif')"
    );

    foreach ($users as $user) {
        $stmt->execute($user);
    }

    echo "✔ Utilisateurs par défaut insérés.\n<br>";
    echo "\n<br><strong>Comptes disponibles :</strong>\n<br>";
    echo "  admin@mintransport.gov / admin123  (rôle: admin)\n<br>";
    echo "  agent@mintransport.gov / agent123  (rôle: agent)\n<br>";
    echo "\n<br>✅ Initialisation terminée avec succès !\n<br>";

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n<br>";
    exit(1);
}
