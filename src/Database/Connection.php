<?php
declare(strict_types=1);

namespace App\Database;

use PDO;

class Connection
{
    public static function get(): PDO
    {
        $config = require __DIR__ . '/../../config/config.php';

        $db = $config['db'];

        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s',
            $db['host'],
            $db['port'],
            $db['name']
        );

        return new PDO(
            $dsn,
            $db['user'],
            $db['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}