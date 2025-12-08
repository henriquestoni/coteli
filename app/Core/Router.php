<?php

namespace App\Core;

/**
 * Router simples para o sistema COTELI.
 *
 * - Recebe a URI normalizada (ex.: '/', '/login', '/pregoes')
 * - Busca na tabela de rotas
 * - Instancia o controlador e executa a ação
 * - Em caso de erro, mostra 404 com informações úteis em ambiente de desenvolvimento
 */
class Router
{
    /**
     * @var array<string, array{0:string,1:string}>
     *
     * Exemplo:
     * [
     *   '/'      => ['App\\Controllers\\HomeController', 'index'],
     *   '/login' => ['App\\Controllers\\AuthController', 'login'],
     * ]
     */
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Faz o despacho da rota.
     *
     * @param string $uri Caminho normalizado, ex.: '/', '/login', '/pregoes'
     */
    public function dispatch(string $uri): void
    {
        // Normaliza: garante barra inicial, remove barra final
        $path = '/' . ltrim($uri, '/');
        $path = rtrim($path, '/') ?: '/';

        if (!isset($this->routes[$path])) {
            $this->handleNotFound($path, 'Rota não encontrada na tabela de rotas.');
            return;
        }

        [$controllerClass, $action] = $this->routes[$path];

        if (!class_exists($controllerClass)) {
            $this->handleNotFound(
                $path,
                "Controlador {$controllerClass} não encontrado (verifique namespace e nome do arquivo)."
            );
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            $this->handleNotFound(
                $path,
                "Ação {$action} não encontrada em {$controllerClass}."
            );
            return;
        }

        // Tudo certo: chama a ação
        $controller->{$action}();
    }

    /**
     * Trata 404 / erros de rota.
     */
    private function handleNotFound(string $path, string $detalhe = ''): void
    {
        http_response_code(404);

        // Se existir um controlador/ação específica para erros, usa
        $errorsControllerClass = '\\App\\Controllers\\ErrorsController';
        if (class_exists($errorsControllerClass)) {
            $controller = new $errorsControllerClass();
            if (method_exists($controller, 'notFound')) {
                $controller->notFound($path, $detalhe);
                return;
            }
        }

        // Fallback simples
        echo '<h1>404 - Página não encontrada</h1>';
        echo '<p>Rota: <code>' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '</code></p>';

        if ($detalhe !== '') {
            echo '<p>Detalhe: ' . htmlspecialchars($detalhe, ENT_QUOTES, 'UTF-8') . '</p>';
        }
    }
}
