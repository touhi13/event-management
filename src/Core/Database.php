<?php
namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private \PDO $connection;

    private function __construct()
    {
        $config = require_once __DIR__ . '/../../config/database.php';

        try {
            $dsn              = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            if (isset($config['debug']) && $config['debug']) {
                die("Connection failed: " . $e->getMessage());
            }
            die("Connection failed. Please try again later.");
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO
    {
        return $this->connection;
    }
}