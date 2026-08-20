<?php
namespace Config;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $projectRoot = dirname(__DIR__, 2);
            $envPath = $projectRoot . '/.env';

            if (!file_exists($envPath)) {
                throw new RuntimeException("Archivo .env no encontrado.");
            }

            $env = parse_ini_file($envPath);
            $dbPath = $env['DB_PATH'] ?? 'database.sqlite';

            $isAbsolutePath = preg_match('/^(?:[A-Za-z]:[\\\\\/]|[\\\\\/]{1,2})/', $dbPath) === 1;
            if (!$isAbsolutePath) {
                $dbPath = $projectRoot . '/' . ltrim($dbPath, './\\');
            }

            try {
                
                self::$instance = new PDO("sqlite:" . $dbPath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$instance->exec("PRAGMA foreign_keys = ON;"); 
            } catch (PDOException $e) {
                throw new PDOException("Error de conexión SQLite: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}