<?php
declare(strict_types=1);

// ============================================================
// 1. AUTOLOADER PSR-4 COMPATIBLE (Sin dependencias externas)
// ============================================================
spl_autoload_register(function (string $clase): void {
    // Convierte el namespace en una ruta directa (ej: Models\DocumentoModel -> Models/DocumentoModel)
    $partes = explode('\\', $clase);
    
    if (count($partes) > 1) {
        // Forzamos la primera carpeta (el espacio de nombres raíz) a minúsculas por convención de directorios
        $partes[0] = strtolower($partes[0]);
    }
    
    $archivo = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $partes) . '.php';

    if (file_exists($archivo)) {
        require_once $archivo;
    }
});

// ============================================================
// 2. CARGA ULTRA-RÁPIDA DE VARIABLES DE ENTORNO (.env)
// ============================================================
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envVars = parse_ini_file($envFile);
    if ($envVars !== false) {
        foreach ($envVars as $clave => $valor) {
            putenv("{$clave}={$valor}");
            $_ENV[$clave] = $valor;
        }
    }
}

// ============================================================
// 3. CABECERAS GLOBALES DE SEGURIDAD (HTTP Hardening)
// ============================================================
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// ============================================================
// 4. DESPACHO DE LA PETICIÓN AL ENRUTADOR (API)
// ============================================================
require_once __DIR__ . '/routes/api.php';

$uri    = $_SERVER['REQUEST_URI']    ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

routeRequest($uri, $method);