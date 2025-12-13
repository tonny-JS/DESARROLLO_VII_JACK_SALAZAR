<?php
// ejecuta todas las migraciones en /database/migrations
require_once __DIR__ . '/database/MigrationManager.php';

try {
    // Configuración de la BD
    $cfg = require __DIR__ . '/config.php';
    $db  = $cfg['db'];

    // Conexión PDO
    $pdo = new PDO(
        "mysql:host={$db['host']};dbname={$db['name']};charset={$db['charset']}",
        $db['user'],
        $db['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Ejecutar migraciones
    $migrationsPath   = __DIR__ . '/database/migrations';
    $migrationManager = new MigrationManager($pdo, $migrationsPath);
    $migrationManager->migrate();

    echo " Migraciones ejecutadas correctamente.\n";

} catch (PDOException $e) {
    die(" DB ERROR: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("ERROR: " . $e->getMessage() . "\n");
}
?>