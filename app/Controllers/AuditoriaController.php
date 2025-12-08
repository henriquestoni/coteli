<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditLogger;
use PDOException;

class AuditoriaController extends BaseController
{
    public function index(): void
    {
        Auth::requireLevel(4); // apenas admins funcionais ou superiores

        $model = new AuditLogger();
        $logs = $model->fetchLatest(50);

        $this->render('auditoria/index', [
            'pageTitle' => 'Auditoria',
            'logs' => $logs,
        ]);
    }
}
