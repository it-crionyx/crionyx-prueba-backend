<?php
namespace Controllers;

use Config\Database;
use Models\DocumentoModel;
use PDOException;
use Exception;

class DocumentoController
{
    private DocumentoModel $model;

    // Inicializa el modelo inyectando la conexión Singleton de la base de datos
    public function __construct()
    {
        try {
            $db = Database::getInstance();
            $this->model = new DocumentoModel($db);
        } catch (Exception $e) {
            $this->responder(500, ['error' => 'Error de configuración de base de datos.']);
        }
    }

    // Maneja el endpoint GET /api/documentos
    public function index(): void
    {
        try {
            $documentos = $this->model->obtenerConRetenciones();
            $this->responder(200, $documentos);
        } catch (PDOException $e) {
            $this->responder(500, ['error' => 'Error al consultar los documentos contables.']);
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