<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;

class EmpresasController extends BaseController
{
    public function index(): void
    {
        Auth::requireLevel(2);
        $this->render('empresas/index', [
            'pageTitle' => 'Empresas',
        ]);
    }
}
