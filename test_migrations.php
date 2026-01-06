<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Database\Connection;
use App\Database\MigrationManager;

echo "--- DÉBUT DE LA VALIDATION DES MIGRATIONS ---\n";

try {
    // Tentative de connexion
    echo "Connexion à la base de données... ";
    $pdo = Connection::get();
    echo "OK.\n";

    $manager = new MigrationManager($pdo);

    // On récupère les migrations en attente
    $pending = $manager->getPendingMigrations();
    echo "Nombre de migrations à appliquer : " . count($pending) . "\n";

    foreach ($pending as $migration) {
        echo "Application de {$migration['name']}... ";
        $manager->applyNextMigration();
        echo "OK.\n";
    }

    echo "--- TOUTES LES MIGRATIONS ONT ÉTÉ APPLIQUÉES AVEC SUCCÈS ---\n";
    exit(0);
} catch (Exception $e) {
    echo "\x1b[31mERREUR FATALE :\x1b[0m " . $e->getMessage() . "\n";
    exit(1);
}
