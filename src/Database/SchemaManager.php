<?php
declare(strict_types=1);

namespace App\Database;

use PDO;
use Exception;

class SchemaManager
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function init(): void
    {
        if (!$this->isDatabaseInitialized()) {
            $this->runInitSql();
            $this->createDefaultAdmin();
        }
    }

    private function isDatabaseInitialized(): bool
    {
        try {
            // Check if 'users' table exists
            $stmt = $this->pdo->query("SELECT 1 FROM information_schema.tables WHERE table_name = 'users' LIMIT 1");
            $tableExists = $stmt !== false && $stmt->fetch() !== false;

            if (!$tableExists)
                return false;

            // Check if 'role' column exists in 'users' (for backward compatibility during this update)
            $stmt = $this->pdo->query("SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'role' LIMIT 1");
            $columnExists = $stmt !== false && $stmt->fetch() !== false;

            // Check if 'rights_exercises' table exists
            $stmt = $this->pdo->query("SELECT 1 FROM information_schema.tables WHERE table_name = 'rights_exercises' LIMIT 1");
            $rightsTableExists = $stmt !== false && $stmt->fetch() !== false;

            return $columnExists && $rightsTableExists;
        } catch (Exception $e) {

            return false;
        }
    }


    private function runInitSql(): void
    {
        $sqlPath = __DIR__ . '/../../init.sql';
        if (!file_exists($sqlPath)) {
            throw new Exception("init.sql file not found at " . $sqlPath);
        }

        $sql = file_get_contents($sqlPath);
        if ($sql === false) {
            throw new Exception("Could not read init.sql");
        }

        $this->pdo->exec($sql);
    }

    private function createDefaultAdmin(): void
    {
        // Check if any admin exists
        $stmt = $this->pdo->query("SELECT 1 FROM users WHERE role = 'admin' LIMIT 1");
        if ($stmt->fetch() === false) {
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare(
                "INSERT INTO users (email, password, name, role) VALUES ('admin@rgpd.fr', :password, 'Administrateur', 'admin')"
            );
            $stmt->execute(['password' => $password]);
        }
    }
}
