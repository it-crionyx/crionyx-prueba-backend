<?php
// routes/api.php

require_once dirname(__DIR__) . '/controllers/DocumentController.php';
require_once dirname(__DIR__) . '/controllers/ProcessController.php';

use Controllers\DocumentController;
use Controllers\ProcessController;

function handleRouteRequest(string $uri, string $method): void
{
    
    $uriPath = rtrim(parse_url($uri, PHP_URL_PATH), '/');
    $method = strtoupper($method);

    
    $uriPath = str_replace('/crionyx-prueba-backend', '', $uriPath);
    
    if (empty($uriPath)) {
        $uriPath = '/';
    }

    if ($method === 'GET' && preg_match('/^\/(css|js)\/([a-zA-Z0-9._-]+)$/', $uriPath, $matches)) {
        $assetPath = dirname(__DIR__, 2) . '/Frontend/' . $matches[1] . '/' . $matches[2];
        if (!file_exists($assetPath)) {
            http_response_code(404);
            exit;
        }

        $contentTypes = ['css' => 'text/css; charset=UTF-8', 'js' => 'application/javascript; charset=UTF-8'];
        header('Content-Type: ' . $contentTypes[$matches[1]]);
        readfile($assetPath);
        exit;
    }

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $route = "{$method} {$uriPath}";

    
    try {
        match ($route) {
            
            'GET /' => (function() {
                $view = dirname(__DIR__, 2) . '/Frontend/index.html';
                
                if (file_exists($view)) {
                    header('Content-Type: text/html; charset=UTF-8');
                    readfile($view);
                } else {
                    http_response_code(500);
                    header('Content-Type: application/json; charset=UTF-8');
                    echo json_encode([
                        'success' => false,
                        'error'   => "Error del servidor: El archivo index.html no fue encontrado."
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                }
            })(),
            
            'GET /api/documentos' => (new DocumentController())->getDocuments(),
            'POST /api/procesar'  => (new ProcessController())->processDocuments(),
            default               => throw new Exception("Ruta no encontrada")
        };
    } catch (Exception $e) {
        http_response_code(404);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => false,
            'error'   => "Recurso no encontrado: {$route}"
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}