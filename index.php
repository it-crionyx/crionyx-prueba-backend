<?php
declare(strict_types=1);


spl_autoload_register(function (string $className): void {
    $namespaceDirectories = [
        'Config' => 'Backend/config',
        'Controllers' => 'Backend/controllers',
        'Model' => 'Backend/Model'
    ];
    $parts = explode('\\', $className);
    $rootNamespace = array_shift($parts);

    if (!isset($namespaceDirectories[$rootNamespace])) {
        return;
    }

    $file = __DIR__ . DIRECTORY_SEPARATOR . $namespaceDirectories[$rootNamespace]
        . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});


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


header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');


require_once __DIR__ . '/Backend/Routes/Api.php';

$uri    = $_SERVER['REQUEST_URI']    ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

handleRouteRequest($uri, $method);