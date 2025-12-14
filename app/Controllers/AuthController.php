<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\UserModel;
use App\Services\AuditLogger;
use App\Services\Mailer;

class AuthController extends BaseController
{
    public function login(): void
    {
        $this->render('auth/login', [
            'pageTitle' => 'Login',
        ]);
    }

    public function registrar(): void
    {
        $errors = [];
        $form = [
            'nome_completo' => trim((string)($_POST['nome_completo'] ?? '')),
            'email' => trim((string)($_POST['email'] ?? '')),
            'login' => trim((string)($_POST['login'] ?? '')),
            'senha' => (string)($_POST['senha'] ?? ''),
            'confirmar' => (string)($_POST['confirmar'] ?? ''),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($form['nome_completo'] === '') $errors[] = 'Nome é obrigatório.';
            if ($form['email'] === '') $errors[] = 'E-mail é obrigatório.';
            if ($form['login'] === '') $errors[] = 'Login é obrigatório.';
            if (strlen($form['senha']) < 8) $errors[] = 'Senha deve ter ao menos 8 caracteres.';
            if ($form['senha'] !== $form['confirmar']) $errors[] = 'Confirmação de senha não confere.';

            if (!$errors) {
                $model = new UserModel();
                $model->createUser($form);
                header('Location: ' . url('login?status=senha-criada'));
                exit;
            }
        }

        $this->render('auth/registrar', [
            'pageTitle' => 'Criar conta',
            'errors' => $errors,
            'form' => $form,
        ]);
    }

    public function solicitarPrimeiroAcesso(): void
    {
        $loginOuEmail = trim((string)($_POST['login_primeiro'] ?? ''));
        if ($loginOuEmail === '') {
            header('Location: ' . url('login?status=primeiro-acesso-erro'));
            exit;
        }

        $model = new UserModel();
        $user = $model->findForAuth($loginOuEmail);

        if (!$user || empty($user['trocar_senha'])) {
            header('Location: ' . url('login?status=primeiro-acesso-erro'));
            exit;
        }

        $codigo = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expira = new \DateTimeImmutable('+30 minutes');
        $userId = (int)($user['id_usuarios'] ?? $user['id'] ?? 0);
        $model->registrarCodigoPrimeiroAcesso($userId, $codigo, $expira);

        $mailer = new Mailer('smtp.uerj.br', 465, 'oliveira.toni@uerj.br', '8W!c6zQMZPrb!Zs', true);
        $assunto = 'COTELI - Código para troca de senha';
        $mensagem = "Olá, {$user['nome_completo']}.\n\nUse o código abaixo para criar/alterar sua senha. Ele expira em 30 minutos.\n\nCódigo: {$codigo}\n\nSe não foi você, ignore este e-mail.";
        $mailer->send($user['email'], $assunto, $mensagem);

        // Armazena apenas os dados necessários para o fluxo de troca + exige código
        $_SESSION['pending_code_user'] = [
            'id_usuarios' => $userId,
            'id' => $userId,
            'nome_completo' => $user['nome_completo'] ?? '',
            'login' => $user['login'] ?? '',
            'email' => $user['email'] ?? '',
            'trocar_senha' => 1,
            'senha_hash' => $user['senha_hash'] ?? null,
        ];

        header('Location: ' . url('usuarios/primeiro-acesso'));
        exit;
    }

    public function recuperarSenha(): void
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginOuEmail = trim((string)($_POST['login_recuperar'] ?? ''));
            if ($loginOuEmail === '') {
                $errors[] = 'Informe seu login ou e-mail.';
            } else {
                $model = new UserModel();
                $user = $model->findForAuth($loginOuEmail);
                if (!$user) {
                    $errors[] = 'Usuário não encontrado.';
                } else {
                    $codigo = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $expira = new \DateTimeImmutable('+30 minutes');
                    $userId = (int)($user['id_usuarios'] ?? $user['id'] ?? 0);
                    $model->registrarCodigoPrimeiroAcesso($userId, $codigo, $expira);
                    $mailer = new Mailer('smtp.uerj.br', 465, 'oliveira.toni@uerj.br', '8W!c6zQMZPrb!Zs', true);
                    $assunto = 'COTELI - Código para redefinir senha';
                    $mensagem = "Olá, {$user['nome_completo']}.\n\nUse o código abaixo para redefinir sua senha. Ele expira em 30 minutos.\n\nCódigo: {$codigo}\n\nSe não foi você, ignore este e-mail.";
                    $mailer->send($user['email'], $assunto, $mensagem);
                    $_SESSION['pending_reset_user'] = [
                        'id' => $userId,
                        'nome_completo' => $user['nome_completo'] ?? '',
                        'email' => $user['email'] ?? '',
                        'login' => $user['login'] ?? '',
                    ];
                    header('Location: ' . url('resetar-senha'));
                    exit;
                }
            }
        }

        $this->render('auth/recuperar', [
            'pageTitle' => 'Recuperar senha',
            'errors' => $errors,
        ]);
    }

    public function resetarSenha(): void
    {
        $pending = $_SESSION['pending_reset_user'] ?? null;
        if (!$pending) {
            header('Location: ' . url('recuperar-senha'));
            exit;
        }

        $errors = [];
        $form = [
            'codigo' => '',
            'senha' => '',
            'confirmar' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $form['codigo'] = trim((string)($_POST['codigo'] ?? ''));
            $form['senha'] = (string)($_POST['senha'] ?? '');
            $form['confirmar'] = (string)($_POST['confirmar'] ?? '');

            if ($form['codigo'] === '') $errors[] = 'Informe o código.';
            if (strlen($form['senha']) < 8) $errors[] = 'Senha deve ter pelo menos 8 caracteres.';
            if ($form['senha'] !== $form['confirmar']) $errors[] = 'Confirmação não confere.';

            if (!$errors) {
                $model = new UserModel();
                if (!$model->validarCodigoPrimeiroAcesso((int)$pending['id'], $form['codigo'])) {
                    $errors[] = 'Código inválido ou expirado.';
                } else {
                    $model->atualizarSenha((int)$pending['id'], $form['senha']);
                    unset($_SESSION['pending_reset_user']);
                    header('Location: ' . url('login?status=senha-criada'));
                    exit;
                }
            }
        }

        $this->render('auth/resetar', [
            'pageTitle' => 'Redefinir senha',
            'errors' => $errors,
            'form' => $form,
            'usuario' => $pending,
        ]);
    }

    public function autenticar(): void
    {
        $login = $_POST['login'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $model = new UserModel();
        $user = $model->findForAuth($login);

        $audit = new AuditLogger();

        $needsChange = false;
        if ($user) {
            $needsChange = (!empty($user['precisa_trocar_senha'])) || (isset($user['trocar_senha']) && (int)$user['trocar_senha'] === 1);
            if (empty($user['senha_hash'])) {
                $needsChange = true;
            }
        }

        // Se não tem hash e também não precisa trocar, falha
        if ($user && empty($user['senha_hash']) && !$needsChange) {
            $user = null;
        }

        $senhaValida = $user && !empty($user['senha_hash']) && password_verify($senha, $user['senha_hash']);
        // Permite acesso temporário com senha padrão "0" quando trocar_senha = 1
        if ($needsChange && $senha === '0') {
            $senhaValida = true;
        }

        if ($senhaValida) {
            if (!isset($user['id']) && isset($user['id_usuarios'])) {
                $user['id'] = $user['id_usuarios'];
            }
            $audit->logAction($user['id_usuarios'] ?? $user['id'] ?? null, 'LOGIN_SUCESSO', 'usuarios', $user['id_usuarios'] ?? null, null, null);
            if ($needsChange) {
                // Não loga ainda; guarda pending_user para forçar troca de senha
                $_SESSION['pending_user'] = $user;
                header('Location: ' . url('usuarios/primeiro-acesso'));
                exit;
            }
            Auth::login($user);
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
