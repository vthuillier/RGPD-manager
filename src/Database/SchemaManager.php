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
        $migrationManager = new MigrationManager($this->pdo);
        $migrationManager->migrate();
    }
}
