<?php
/**
 * Definição centralizada de rotas amigáveis.
 *
 * Para adicionar nova rota:
 * - Defina o caminho (ex.: '/usuarios')
 * - Informe controlador (classe em App\Controllers) e método
 * - Ambos precisam existir
 */
return [
    '/'           => ['App\\Controllers\\HomeController', 'index'],
    '/login'      => ['App\\Controllers\\AuthController', 'login'],
    '/autenticar' => ['App\\Controllers\\AuthController', 'autenticar'],
    '/logout'     => ['App\\Controllers\\AuthController', 'logout'],
    '/pregoes'    => ['App\\Controllers\\PregoesController', 'index'],
    '/pregoes/novo-base'      => ['App\\Controllers\\PregoesController', 'novoBase'],
    '/pregoes/salvar-base'    => ['App\\Controllers\\PregoesController', 'salvarBase'],
    '/pregoes/nova-repeticao' => ['App\\Controllers\\PregoesController', 'novaRepeticao'],
    '/pregoes/salvar-repeticao' => ['App\\Controllers\\PregoesController', 'salvarRepeticao'],
    '/pregoes/verificar-chave' => ['App\\Controllers\\PregoesController', 'verificarChave'],
    '/amostras'   => ['App\\Controllers\\AmostrasController', 'index'],
    '/amostras/nova' => ['App\\Controllers\\AmostrasController', 'nova'],
    '/amostras/salvar' => ['App\\Controllers\\AmostrasController', 'salvar'],
    '/empresas'   => ['App\\Controllers\\EmpresasController', 'index'],
    '/relatorios' => ['App\\Controllers\\RelatoriosController', 'index'],
    '/auditoria'  => ['App\\Controllers\\AuditoriaController', 'index'],
    '/usuarios'   => ['App\\Controllers\\UsuariosController', 'index'],
    '/usuarios/novo' => ['App\\Controllers\\UsuariosController', 'novo'],
    '/usuarios/editar' => ['App\\Controllers\\UsuariosController', 'editar'],
    '/usuarios/salvar' => ['App\\Controllers\\UsuariosController', 'salvar'],
    '/usuarios/gerar-acesso' => ['App\\Controllers\\UsuariosController', 'gerarAcesso'],
    '/usuarios/primeiro-acesso' => ['App\\Controllers\\UsuariosController', 'primeiroAcesso'],
    '/cadastros' => ['App\\Controllers\\CadastrosController', 'index'],
];
