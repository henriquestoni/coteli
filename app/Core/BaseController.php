<?php

namespace App\Core;

/**
 * Controlador base.
 * - Usa render() para carregar views dentro do layout principal.
 * - Para adicionar um novo controlador:
 *   1) Crie a classe em app/Controllers/NovoController.php extendendo BaseController.
 *   2) Adicione a rota em config/routes.php.
 *   3) Crie a view em app/Views/... conforme necessidade.
 */
class BaseController
{
    protected string $layout = __DIR__ . '/../Views/layouts/main.php';

    protected function render(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo "View {$view} não encontrada.";
            return;
        }

        extract($data);

        // Torna variável $content disponível no layout
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        include $this->layout;
    }
}
