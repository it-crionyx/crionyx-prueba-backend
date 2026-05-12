<?php
// config/Database.php

class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            // En un entorno real usarías una librería para leer .env
            // Para la prueba, simularemos la lectura de la ruta:
            $dbPath = __DIR__ . '/../database.sqlite'; 

            try {
                self::$instance = new PDO("sqlite:" . $dbPath);
                // Activamos excepciones para ver errores de SQL
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // Resultados como array asociativo
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}