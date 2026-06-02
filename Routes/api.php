<?php
// routes/api.php

require_once dirname(__DIR__) . '/controllers/DocumentoController.php';
require_once dirname(__DIR__) . '/controllers/ProcesarController.php';

use Controllers\DocumentoController;
use Controllers\ProcesarController;

function routeRequest(string $uri, string $method): void
{
    // 1. Obtener la ruta limpia inicial
    $uriPath = rtrim(parse_url($uri, PHP_URL_PATH), '/');
    if (strpos($uriPath, '.css') !== false) {
        return; 
    }
    $method = strtoupper($method);

    // 2. Remover el subdirectorio físico de XAMPP/Apache
    $uriPath = str_replace('/crionyx-prueba-backend', '', $uriPath);
    
    // Si queda vacío (raíz), normalizamos a "/"
    if (empty($uriPath)) {
        $uriPath = '/';
    }

    // Configuración global de cabeceras CORS
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $ruta = "{$method} {$uriPath}";

    // 3. Tabla de rutas evaluadas
    try {
        match ($ruta) {
            // Carga y sirve físicamente el frontend interactivo al acceder a la raíz
            'GET /' => (function() {
                $vista = dirname(__DIR__) . '/Views/index.html';
                
                if (file_exists($vista)) {
                    header('Content-Type: text/html; charset=UTF-8');
                    readfile($vista);
                } else {
                    http_response_code(500);
                    header('Content-Type: application/json; charset=UTF-8');
                    echo json_encode([
                        'success' => false,
                        'error'   => "Error del servidor: El archivo Views/index.html no fue encontrado."
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                }
            })(),
            
            'GET /api/documentos' => (new DocumentoController())->index(),
            'POST /api/procesar'  => (new ProcesarController())->procesar(),
            default               => throw new Exception("Ruta no encontrada")
        };
    } catch (Exception $e) {
        http_response_code(404);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => false,
            'error'   => "Recurso no encontrado: {$ruta}"
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}