<?php
// index.php
require_once 'config/Database.php';
require_once 'models/Documento.php';

// Obtener la conexión a la DB
$db = Database::getConnection();

// Leer la acción de la URL (por ejemplo: index.php?action=listar)
$action = $_GET['action'] ?? '';

// Definir que la respuesta siempre será JSON para la API
header('Content-Type: application/json');

if ($action === 'listar') {
    $modelo = new Documento($db);
    $datos = $modelo->listarConCalculo();
    
    // Procesar la lógica de negocio (UVT) antes de enviar al frontend
    $resultado = array_map(function($doc) {
        $baseEnPesos = $doc['base_uvt'] * $doc['uvt_actual'];
        
        return [
            "id" => $doc['id'],
            "proveedor" => $doc['proveedor'],
            "valor_total" => (float)$doc['valor_total'],
            "aplica_retencion" => $doc['valor_total'] > $baseEnPesos
        ];
    }, $datos);

    echo json_encode($resultado);
    exit;
}

// Mensaje por defecto si no se reconoce la acción
echo json_encode(["error" => "Acción no permitida o no especificada"]);