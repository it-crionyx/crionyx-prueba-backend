<?php
require_once 'config/Database.php';
require_once 'models/Documento.php';

$db = Database::getConnection();
$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {
    case 'listar':
        $modelo = new Documento($db);
        $datos = $modelo->listarConCalculo();
        
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
        break;

    case 'procesar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents("php://input"), true);
            if (isset($input['documento_id'])) {
                $modelo = new Documento($db);
                $aplica = $input['aplica_retencion'] ? 1 : 0;
                $exito = $modelo->guardarProcesado($input['documento_id'], $aplica);
                echo json_encode(["status" => $exito ? "success" : "error"]);
            }
        }
        break;

    default:
        echo json_encode(["error" => "Accion no reconocida"]);
        break;
}