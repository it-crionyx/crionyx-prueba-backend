<?php
namespace Config;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    // Almacena la única instancia de la conexión (Singleton)
    private static ?PDO $instance = null;

    // Previene la instanciación externa y clonación
    private function __construct() {}
    private function __clone() {}

    // Retorna la instancia única de PDO
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $envPath = dirname(__DIR__) . '/.env';

            if (!file_exists($envPath)) {
                throw new RuntimeException("Archivo .env no encontrado.");
            }

            // Carga las variables de configuración
            $env = parse_ini_file($envPath);
            $dbPath = $env['DB_PATH'] ?? 'database.sqlite';

            // Resuelve la ruta absoluta de la base de datos
            if (!str_starts_with($dbPath, '/')) {
                $dbPath = dirname(__DIR__) . '/' . ltrim($dbPath, './');
            }

            try {
                // Inicializa PDO con SQLite y aplica configuraciones de seguridad
                self::$instance = new PDO("sqlite:" . $dbPath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$instance->exec("PRAGMA foreign_keys = ON;"); // Fuerza la integridad referencial
            } catch (PDOException $e) {
                throw new PDOException("Error de conexión SQLite: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}