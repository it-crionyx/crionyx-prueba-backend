<?php
require_once 'config/Database.php';

try {
    $db = Database::getConnection();
    echo "✅ Conexión exitosa a SQLite.<br>";

    // Vamos a ver qué columnas tiene realmente la tabla variables_sistema
    $res = $db->query("PRAGMA table_info(variables_sistema)");
    $columnas = $res->fetchAll();

    echo "<b>Columnas encontradas en 'variables_sistema':</b><br>";
    foreach ($columnas as $col) {
        echo "- " . $col['name'] . " (" . $col['type'] . ")<br>";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}