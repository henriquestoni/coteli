<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;

class RelatoriosController extends BaseController
{
    public function index(): void
    {
        // Nível 1 já consegue ver relatórios específicos
        Auth::requireLevel(1);
        $this->render('relatorios/index', [
            'pageTitle' => 'Relatórios',
        ]);
    }
}
