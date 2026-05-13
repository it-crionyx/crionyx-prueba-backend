<?php
require_once 'config/Database.php';
try {
    $db = Database::getConnection();
    $res = $db->query("PRAGMA table_info(documentos_procesados)");
    $columnas = $res->fetchAll();

    echo "<b>Columnas en 'documentos_procesados':</b><br>";
    foreach ($columnas as $col) {
        echo "- " . $col['name'] . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}