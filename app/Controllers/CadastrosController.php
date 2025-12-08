<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;

class CadastrosController extends BaseController
{
    public function index(): void
    {
        Auth::requireLogin();

        $sections = [
            [
                'title' => 'Cadastros',
                'cards' => [
                    ['title' => 'Pregoes', 'link' => url('pregoes'), 'description' => 'Registrar pregoes R-0.'],
                    ['title' => 'Repeticoes', 'link' => url('pregoes/nova-repeticao'), 'description' => 'Cadastrar repeticoes R-X.'],
                    ['title' => 'Amostras', 'link' => url('amostras'), 'description' => 'Controle de entrada e parecer.'],
                    ['title' => 'Adiamentos/Suspensoes', 'link' => url('pregoes'), 'description' => 'Gerir adiamentos e suspensoes.'],
                    ['title' => 'Empresas', 'link' => url('empresas'), 'description' => 'Cadastro de fornecedores.'],
                    ['title' => 'Pregoeiros', 'link' => url('usuarios'), 'description' => 'Designar pregoeiros (usuarios).'],
                ],
            ],
        ];

        $this->render('cadastros/index', [
            'pageTitle' => 'Cadastros',
            'sections' => $sections,
        ]);
    }
}
