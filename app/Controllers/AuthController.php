<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\UserModel;
use App\Services\AuditLogger;

class AuthController extends BaseController
{
    public function login(): void
    {
        $this->render('auth/login', [
            'pageTitle' => 'Login',
        ]);
    }

    public function autenticar(): void
    {
        $login = $_POST['login'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $model = new UserModel();
        $user = $model->findForAuth($login);

        $audit = new AuditLogger();

        if ($user && !empty($user['senha_hash']) && password_verify($senha, $user['senha_hash'])) {
            if (!isset($user['id']) && isset($user['id_usuarios'])) {
                $user['id'] = $user['id_usuarios'];
            }
            Auth::login($user);
            $audit->logAction($user['id_usuarios'] ?? $user['id'] ?? null, 'LOGIN_SUCESSO', 'usuarios', $user['id_usuarios'] ?? null, null, null);
            if (!empty($user['precisa_trocar_senha'])) {
                header('Location: ' . url('usuarios/primeiro-acesso'));
                exit;
            }
            header('Location: ' . url(''));
            exit;
        }

        // fallback de usuário demo (senha: 123456) para ambiente inicial sem banco
        $demoHash = password_hash('123456', PASSWORD_DEFAULT);
        if ($login === 'demo' && password_verify($senha, $demoHash)) {
            $demoUser = [
                'id' => 0,
                'id_usuarios' => 0,
                'nome_completo' => 'Usuário Demo',
                'login' => 'demo',
                'email' => 'demo@example.com',
                'nivel_acesso' => 4,
            ];
            Auth::login($demoUser);
            $audit->logAction(null, 'LOGIN_SUCESSO', 'usuarios', null, null, ['demo' => true]);
            header('Location: ' . url(''));
            exit;
        }

        $audit->logAction(null, 'LOGIN_FALHA', 'usuarios', null, null, ['login' => $login]);

        $this->render('auth/login', [
            'pageTitle' => 'Login',
            'error' => 'Credenciais inválidas.',
        ]);
    }

    public function logout(): void
    {
        $user = Auth::user();
        $audit = new AuditLogger();
        $userId = $user['id_usuarios'] ?? $user['id'] ?? null;
        $audit->logAction($userId, 'LOGOUT', 'usuarios', $userId, null, null);

        Auth::logout();
        header('Location: ' . url('login'));
        exit;
    }
}
