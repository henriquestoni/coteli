<?php
/**
 * Front controller para o sistema COTELI.
 * - Normaliza a URI
 * - Carrega rotas e roteador
 * - Encaminha para o controlador/ação correspondente
 */

declare(strict_types=1);

// Exibir erros em ambiente de desenvolvimento (ajuste em produção).
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('BASE_PATH', __DIR__);
require BASE_PATH . '/config/app.php';


// Sessão para autenticação/controle de acesso
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader simples para classes em app/
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    if (str_starts_with($class, $prefix)) {
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});

// Carrega rotas
$routes = require BASE_PATH . '/config/routes.php';

// ------------------------------------------------------------------
// Normaliza URI (já com basePath) – esse é o que montamos antes
// ------------------------------------------------------------------
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Só o path, sem query string
$uriPath = parse_url($requestUri, PHP_URL_PATH) ?? '/';

// Caminho base onde o projeto está montado, ex.: /coteli
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath   = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

// Se a URI começar com o caminho base, remove esse prefixo
if ($basePath !== '' && $basePath !== '/') {
    if (str_starts_with($uriPath, $basePath)) {
        $uriPath = substr($uriPath, strlen($basePath));
    }
}

// Normaliza: tira barra final; se ficar vazio, usa /
$uri = rtrim($uriPath, '/') ?: '/';

// Se alguém acessar /index.php diretamente, trata como /
if ($uri === '/index.php') {
    $uri = '/';
}

// Faz o dispatch
$router = new App\Core\Router($routes);
$router->dispatch($uri);
