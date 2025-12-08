<?php
// Detecta automaticamente o subdiretório do sistema, ex.: /coteli
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseUrl = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

if ($baseUrl === '/' || $baseUrl === '\\') {
    $baseUrl = '';
}

define('BASE_URL', $baseUrl);

/**
 * Monta URL para rotas internas, ex.: url('login') => /coteli/login
 */
function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return BASE_URL . $path;
}

/**
 * Monta URL para assets, ex.: asset('assets/css/main.css') => /coteli/assets/css/main.css
 */
function asset(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return BASE_URL . $path;
}
