<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\PregaoModel;

class HomeController extends BaseController
{
    public function index(): void
    {
        Auth::requireLogin();

        $currentUser = Auth::user();
        $userId      = (int)($currentUser['id_usuarios'] ?? 0);
        $agendaScope = ($_GET['agenda'] ?? 'meus') === 'todos' ? 'todos' : 'meus';

        $pregaoModel = new PregaoModel();
        $agenda      = $pregaoModel->getAgendaProximos(8, $userId, $agendaScope === 'meus');
<<<<<<< HEAD
        $pregoeiros  = $pregaoModel->getPregoeiros();
=======
>>>>>>> 99e3d7fbbbc5fdcfa5e4bd8d2744761b3c0623b7

        $sections = [
            [
                'title' => 'Cadastros',
                'cards' => [
                    ['title' => 'Pregões', 'link' => url('pregoes'), 'description' => 'Registrar e gerenciar pregões.'],
                    ['title' => 'Repetições', 'link' => url('pregoes/nova-repeticao'), 'description' => 'Cadastrar repetições R-X.'],
                    ['title' => 'Amostras', 'link' => url('amostras'), 'description' => 'Controle de entrada e parecer.'],
                    ['title' => 'mais ...', 'link' => url('cadastros'), 'description' => ''],
                ],
            ],
            [
                'title' => 'Relatórios',
                'cards' => [
                    ['title' => 'Relatórios', 'link' => url('relatorios'), 'description' => 'Indicadores e consultas.'],
                    ['title' => 'Auditoria', 'link' => url('auditoria'), 'description' => 'Acompanhar trilha de auditoria.'],
                    ['title' => 'Resumo de pregões', 'link' => url('relatorios'), 'description' => 'Painel resumido de pregões.'],
                    ['title' => 'mais ...', 'link' => url('relatorios'), 'description' => ''],
                ],
            ],
            [
                'title' => 'Gestão do Sistema',
                'cards' => [
                    ['title' => 'Usuários', 'link' => url('usuarios'), 'description' => 'Gestão de acessos e perfis.'],
                    ['title' => 'Pregoeiros', 'link' => url('usuarios'), 'description' => 'Definir pregoeiros (usuários).'],
                    ['title' => 'Configurações', 'link' => '', 'description' => 'Ajustes gerais (em breve).'],
                    ['title' => 'mais ...', 'link' => url('usuarios'), 'description' => ''],
                ],
            ],
        ];

        $this->render('home/index', [
            'pageTitle'   => 'Dashboard',
            'sections'    => $sections,
            'agenda'      => $agenda,
            'agendaScope' => $agendaScope,
<<<<<<< HEAD
            'pregoeiros'  => $pregoeiros,
=======
>>>>>>> 99e3d7fbbbc5fdcfa5e4bd8d2744761b3c0623b7
        ]);
    }
}
