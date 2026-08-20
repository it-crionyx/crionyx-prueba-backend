<?php
namespace Controllers;

use Config\Database;
use Model\ProcessModel;
use Exception;

class ProcessController
{
    private ProcessModel $model;

    public function __construct()
    {
        try {
            $db = Database::getInstance();
            $this->model = new ProcessModel($db);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Error de configuración de base de datos.']);
        }
    }

    public function processDocuments(): void
    {
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(405, ['error' => 'Método no permitido. Use POST.']);
        }

        $bodyRaw = file_get_contents('php://input');
        $body = json_decode($bodyRaw, true);

        if (empty($body['documentos']) || !is_array($body['documentos'])) {
            $this->sendResponse(400, ['error' => 'Se requiere un array "documentos" válido y no vacío.']);
        }

        
        $documentsToProcess = [];
        foreach ($body['documentos'] as $document) {
            if (!isset($document['id_documento'], $document['aplica_retencion'], $document['base_pesos'])) {
                $this->sendResponse(422, ['error' => 'Estructura de documento inválida. Faltan campos obligatorios.']);
            }

            $documentsToProcess[] = [
                'id_documento'     => (int)$document['id_documento'],
                'aplica_retencion' => (int)(bool)$document['aplica_retencion'],
                'valor_base'       => (float)$document['base_pesos']
            ];
        }
        
        try {
            $result = $this->model->processBatch($documentsToProcess);
            $this->sendResponse(201, ['success' => true, 'mensaje' => 'Documentos procesados exitosamente.', 'detalle' => $result]);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Error al guardar los documentos procesados.']);
        }
    }

    private function sendResponse(int $statusCode, array $data): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}