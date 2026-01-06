<?php
declare(strict_types=1);

namespace App\Database;

use PDO;
use Exception;

class MigrationManager
{
    private PDO $pdo;
    private string $migrationsPath;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->migrationsPath = __DIR__ . '/../../migrations';
    }

    public function getPendingMigrations(): array
    {
        $this->ensureMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $migrationFiles = $this->getMigrationFiles();

        $pending = [];
        foreach ($migrationFiles as $file) {
            $migrationName = basename($file, '.sql');
            if (!in_array($migrationName, $appliedMigrations)) {
                $pending[] = [
                    'name' => $migrationName,
                    'path' => $file
                ];
            }
        }
        return $pending;
    }

    public function applyNextMigration(): ?string
    {
        $pending = $this->getPendingMigrations();
        if (empty($pending)) {
            return null;
        }

        $next = $pending[0];
        $this->applyMigration($next['path'], $next['name']);
        return $next['name'];
    }

    public function migrate(): void
    {
        $this->ensureMigrationsTable();

        $appliedMigrations = $this->getAppliedMigrations();
        $migrationFiles = $this->getMigrationFiles();

        foreach ($migrationFiles as $file) {
            $migrationName = basename($file, '.sql');
            if (!in_array($migrationName, $appliedMigrations)) {
                $this->applyMigration($file, $migrationName);
            }
        }
    }

    private function ensureMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id SERIAL PRIMARY KEY,
            migration VARCHAR(255) UNIQUE NOT NULL,
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sql);
    }

    private function getAppliedMigrations(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT migration FROM migrations");
            return $stmt !== false ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getMigrationFiles(): array
    {
        $files = glob($this->migrationsPath . '/*.sql');
        sort($files);
        return $files;
    }

    private function applyMigration(string $filePath, string $migrationName): void
    {
        $sql = file_get_contents($filePath);
        if ($sql === false) {
            throw new Exception("Could not read migration file: " . $filePath);
        }

        try {
            $this->pdo->beginTransaction();

            // Execute SQL (might contain multiple statements)
            $this->pdo->exec($sql);

            $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
            $stmt->execute([$migrationName]);

            $this->pdo->commit();
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new Exception("Error applying migration " . $migrationName . ": " . $e->getMessage());
        }
    }
}
