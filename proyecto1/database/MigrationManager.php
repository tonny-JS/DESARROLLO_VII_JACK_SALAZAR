<?php

class MigrationManager {
    private $pdo;
    private $migrationsPath;

    public function __construct(PDO $pdo, string $migrationsPath) {
        $this->pdo = $pdo;
        $this->migrationsPath = $migrationsPath;
    }

    public function migrate() {
        $this->createMigrationsTableIfNotExists();
        $appliedMigrations = $this->getAppliedMigrations();
        $files = $this->getMigrationFiles();
        $newMigrations = array_diff($files, $appliedMigrations);

        if (empty($newMigrations)) {
            echo "No new migrations to apply.\n";
            return;
        }

        foreach ($newMigrations as $migration) {
            try {
                $this->applyMigration($migration);
                echo "Applied migration: $migration\n";
            } catch (Exception $e) {
                echo "Error applying migration $migration: " . $e->getMessage() . "\n";
                break; // detener si falla una migración
            }
        }

        echo count($newMigrations) . " nuevas migraciones aplicadas.\n";
    }

    private function createMigrationsTableIfNotExists() {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    private function getAppliedMigrations(): array {
        $statement = $this->pdo->query("SELECT migration FROM migrations");
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    private function getMigrationFiles(): array {
        $files = scandir($this->migrationsPath);
        $migrations = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'sql';
        });
        sort($migrations);
        return $migrations;
    }

    private function applyMigration(string $migration) {
        $sql = file_get_contents($this->migrationsPath . '/' . $migration);
        if ($sql === false || trim($sql) === '') {
            throw new Exception("Archivo vacío o no encontrado: $migration");
        }

        // Ejecutar dentro de transacción
        $this->pdo->beginTransaction();
        try {
            // Permitir múltiples sentencias separadas por ;
            $queries = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($queries as $query) {
                if ($query !== '') {
                    $this->pdo->exec($query);
                }
            }

            $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
            $stmt->execute(['migration' => $migration]);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
?>