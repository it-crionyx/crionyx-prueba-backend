<?php
namespace Controllers;

use Config\Database;
use Model\DocumentModel;
use PDOException;
use Exception;

class DocumentController
{
    private DocumentModel $model;

    public function __construct()
    {
        try {
            $db = Database::getInstance();
            $this->model = new DocumentModel($db);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Error de configuración de base de datos.']);
        }
    }

    
    public function getDocuments(): void
    {
        try {
            $documents = $this->model->getDocumentsWithWithholdings();
            $this->sendResponse(200, $documents);
        } catch (PDOException $e) {
            $this->sendResponse(500, ['error' => 'Error al consultar los documentos contables.']);
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