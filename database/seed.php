<?php
/**
 * Script d'initialisation de la base de données PostgreSQL.
 * Exécute database/schema.sql (idempotent : IF NOT EXISTS / ON CONFLICT) puis
 * insère les comptes de démonstration par défaut.
 *
 * Prérequis : la base de données elle-même doit déjà exister (createdb, ou
 * panneau d'hébergement) et les identifiants doivent être configurés dans .env
 * (voir .env.example) — ce script ne crée pas la base, seulement les tables.
 *
 * CLI uniquement : ce script ne doit jamais être accessible via le navigateur
 * (il peut réinitialiser le schéma et recréer les comptes admin/agent par
 * défaut avec des mots de passe connus).
 *
 * Usage : php database/seed.php
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Accès interdit : ce script ne peut être exécuté qu\'en ligne de commande (CLI).');
}

require __DIR__ . '/../app/Core/Env.php';

use App\Core\Env;

Env::load(__DIR__ . '/../.env');

$host     = Env::get('DB_HOST', '127.0.0.1');
$port     = Env::get('DB_PORT', '5432');
$dbname   = Env::get('DB_DATABASE', 'min_transport');
$username = Env::get('DB_USERNAME', 'postgres');
$password = Env::get('DB_PASSWORD', '');

try {
    $pdo = new PDO("pgsql:host={$host};port={$port};dbname={$dbname}", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "✔ Connecté à la base '{$dbname}'.\n<br>";

    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schema);

    echo "✔ Tables créées/à jour.\n<br>";

    $users = [
        [
            'nom'       => 'Admin',
            'prenom'    => 'System',
            'email'     => 'admin@transport.dev',
            'telephone' => '+243810000000',
            'password'  => password_hash('admin123', PASSWORD_BCRYPT),
            'role'      => 'admin',
        ],
        [
            'nom'       => 'Agent',
            'prenom'    => 'Transport',
            'email'     => 'agent@mintransport.gov',
            'telephone' => '+243810000001',
            'password'  => password_hash('agent123', PASSWORD_BCRYPT),
            'role'      => 'agent',
        ],
    ];

    $stmt = $pdo->prepare(
        "INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role, statut)
         VALUES (:nom, :prenom, :email, :telephone, :password, :role, 'actif')
         ON CONFLICT (email) DO NOTHING"
    );

    foreach ($users as $user) {
        $stmt->execute($user);
    }

    echo "✔ Utilisateurs par défaut insérés.\n<br>";
    echo "\n<br><strong>Comptes disponibles :</strong>\n<br>";
    echo "  admin@transport.dev / admin123  (rôle: admin)\n<br>";
    echo "  agent@mintransport.gov / agent123  (rôle: agent)\n<br>";
    echo "\n<br>✅ Initialisation terminée avec succès !\n<br>";
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n<br>";
    exit(1);
}
