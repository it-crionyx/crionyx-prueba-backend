<?php
namespace Controllers;

use Config\Database;
use Models\ProcesadoModel;
use Exception;

class ProcesarController
{
    private ProcesadoModel $model;

    // Inicializa el modelo inyectando la conexión Singleton de la base de datos
    public function __construct()
    {
        try {
            $db = Database::getInstance();
            $this->model = new ProcesadoModel($db);
        } catch (Exception $e) {
            $this->responder(500, ['error' => 'Error de configuración de base de datos.']);
        }
    }

    // Maneja el endpoint POST /api/procesar
    public function procesar(): void
    {
        // Validaciones básicas de la petición HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responder(405, ['error' => 'Método no permitido. Use POST.']);
        }

        $bodyRaw = file_get_contents('php://input');
        $body = json_decode($bodyRaw, true);

        if (empty($body['documentos']) || !is_array($body['documentos'])) {
            $this->responder(400, ['error' => 'Se requiere un array "documentos" válido y no vacío.']);
        }

        // Estructuración de datos estrictamente requeridos para la base de datos
        $documentosProcesar = [];
        foreach ($body['documentos'] as $doc) {
            if (!isset($doc['id_documento'], $doc['aplica_retencion'], $doc['base_pesos'])) {
                $this->responder(422, ['error' => 'Estructura de documento inválida. Faltan campos obligatorios.']);
            }

            $documentosProcesar[] = [
                'id_documento'     => (int)$doc['id_documento'],
                'aplica_retencion' => (int)(bool)$doc['aplica_retencion'], // Convierte a 1 o 0 para SQLite
                'valor_base'       => (float)$doc['base_pesos']
            ];
        }

        // Envío de lote estructurado al modelo para persistencia
        try {
            $resultado = $this->model->insertarLote($documentosProcesar);
            $this->responder(201, ['success' => true, 'mensaje' => 'Documentos procesados exitosamente.', 'detalle' => $resultado]);
        } catch (Exception $e) {
            $this->responder(500, ['error' => 'Error al guardar los documentos procesados.']);
        }
    }

    // Centraliza y estructura el envío de respuestas JSON
    private function responder(int $codigo, array $datos): void
    {
        http_response_code($codigo);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}