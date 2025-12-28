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
        }
    }


    private function isDatabaseInitialized(): bool
    {
        try {
            // Check if 'organizations' table exists (newest addition)
            $stmt = $this->pdo->query("SELECT 1 FROM information_schema.tables WHERE table_name = 'organizations' LIMIT 1");
            $orgTableExists = $stmt !== false && $stmt->fetch() !== false;

            if (!$orgTableExists)
                return false;

            // Check if 'users' table exists
            $stmt = $this->pdo->query("SELECT 1 FROM information_schema.tables WHERE table_name = 'users' LIMIT 1");
            $tableExists = $stmt !== false && $stmt->fetch() !== false;

            if (!$tableExists)
                return false;

            // Check if 'organization_id' column exists in 'users'
            $stmt = $this->pdo->query("SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'organization_id' LIMIT 1");
            $orgColumnExists = $stmt !== false && $stmt->fetch() !== false;

            return $orgColumnExists;

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
}
