<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Models\UserModel;

class UsuariosController extends BaseController
{
    public function index(): void
    {
        Auth::requireLevel(4);
        $model = new UserModel();
        $usuarios = $model->listAll();

        $this->render('usuarios/index', [
            'pageTitle' => 'Usuários',
            'usuarios' => $usuarios,
        ]);
    }

    public function novo(): void
    {
        Auth::requireLevel(4);
        $this->render('usuarios/form', [
            'pageTitle' => 'Novo pré-cadastro',
            'formData' => ['ativo' => 1],
        ]);
    }

    public function editar(): void
    {
        Auth::requireLevel(4);
        $id = (int)($_GET['id'] ?? 0);
        $model = new UserModel();
        $usuario = $id ? $model->findById($id) : null;

        if (!$usuario) {
            header('Location: ' . url('usuarios'));
            exit;
        }

        $this->render('usuarios/form', [
            'pageTitle' => 'Editar pré-cadastro',
            'formData' => $usuario,
        ]);
    }

    public function salvar(): void
    {
        Auth::requireLevel(4);
        $id = isset($_POST['id_usuarios']) ? (int)$_POST['id_usuarios'] : null;
        $data = [
            'nome_completo' => trim((string)($_POST['nome_completo'] ?? '')),
            'email' => trim((string)($_POST['email'] ?? '')),
            'is_pregoeiro' => !empty($_POST['is_pregoeiro']) ? 1 : 0,
            'is_responsavel_coteli' => !empty($_POST['is_responsavel_coteli']) ? 1 : 0,
            'ativo' => !empty($_POST['ativo']) ? 1 : 0,
        ];

        $erros = [];
        if ($data['nome_completo'] === '') {
            $erros[] = 'Nome completo é obrigatório.';
        }
        if ($data['email'] === '') {
            $erros[] = 'E-mail é obrigatório.';
        }

        $model = new UserModel();

        if ($erros) {
            $this->render('usuarios/form', [
                'pageTitle' => $id ? 'Editar pré-cadastro' : 'Novo pré-cadastro',
                'formData' => $data + ['id_usuarios' => $id],
                'errors' => $erros,
            ]);
            return;
        }

        if ($id) {
            $model->updatePreCadastro($id, $data);
        } else {
            $model->createPreCadastro($data);
        }

        header('Location: ' . url('usuarios'));
        exit;
    }

    public function gerarAcesso(): void
    {
        Auth::requireLevel(4);
        $model = new UserModel();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $usuario = $id ? $model->findById($id) : null;
        $resultado = null;
        $erros = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postId = isset($_POST['id_usuarios']) ? (int)$_POST['id_usuarios'] : null;
            $email = trim((string)($_POST['email'] ?? ''));
            $nome = trim((string)($_POST['nome_completo'] ?? ''));

            if ($email === '') {
                $erros[] = 'E-mail é obrigatório.';
            }
            if ($nome === '') {
                $erros[] = 'Nome completo é obrigatório.';
            }
            $nivel = (int)($_POST['nivel_acesso'] ?? 1);
            $currentUser = Auth::user();
            $nivelAtual = (int)($currentUser['nivel_acesso'] ?? 1);
            if ($nivelAtual === 4) {
                $nivel = 4; // nível 4 só define 4
            }
            if ($nivel < 1 || $nivel > 5) {
                $erros[] = 'Nível de acesso deve estar entre 1 e 5.';
            }

            if (!$erros) {
                $dados = [
                    'email' => $email,
                    'nome_completo' => $nome,
                    'nivel_acesso' => $nivel,
                    'login' => trim((string)($_POST['login'] ?? '')),
                    'is_pregoeiro' => !empty($_POST['is_pregoeiro']) ? 1 : 0,
                    'is_responsavel_coteli' => !empty($_POST['is_responsavel_coteli']) ? 1 : 0,
                ];
                if ($nivelAtual === 5) {
                    $dados['nivel_acesso'] = $nivel;
                } else {
                    $dados['nivel_acesso'] = 4;
                }
                $resultado = $model->gerarAcesso($dados + ['id' => $postId]);
                $usuario = $resultado['usuario'] ?? null;
            }
        }

        $this->render('usuarios/gerar_acesso', [
            'pageTitle' => 'Gerar acesso',
            'usuario' => $usuario,
            'resultado' => $resultado,
            'errors' => $erros,
        ]);
    }

    public function primeiroAcesso(): void
    {
        $pending = Auth::pendingUser();
        $usuario = $_SESSION['pending_code_user'] ?? (Auth::user() ?? $pending);

        if (!$usuario) {
            header('Location: ' . url('login'));
            exit;
        }

        $mustChange = (!empty($usuario['trocar_senha']) && (int)$usuario['trocar_senha'] === 1)
            || empty($usuario['senha_hash']);

        if (!$mustChange) {
            header('Location: ' . url(''));
            exit;
        }

        $erros = [];
        $model = new UserModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $novaSenha = (string)($_POST['nova_senha'] ?? '');
            $confirmaSenha = (string)($_POST['confirmar_senha'] ?? '');
            $novoLogin = isset($_POST['login']) ? trim((string)$_POST['login']) : null;
            $codigoInformado = isset($_POST['codigo']) ? trim((string)$_POST['codigo']) : '';

            if ($novaSenha === '' || $confirmaSenha === '') {
                $erros[] = 'Informe a nova senha e a confirmação.';
            } elseif (strlen($novaSenha) < 8) {
                $erros[] = 'A senha deve ter pelo menos 8 caracteres.';
            } elseif ($novaSenha !== $confirmaSenha) {
                $erros[] = 'A confirmação de senha não confere.';
            }

            if (!$erros) {
                if ($codigoInformado === '') {
                    $erros[] = 'Informe o código enviado ao seu e-mail.';
                } elseif (!$model->validarCodigoPrimeiroAcesso((int)$usuario['id_usuarios'], $codigoInformado)) {
                    $erros[] = 'Código inválido ou expirado.';
                }
            }

            if (!$erros) {
                $model->atualizarSenhaDefinitiva((int)$usuario['id_usuarios'], $novaSenha, $novoLogin);
                if (isset($_SESSION['pending_user'])) unset($_SESSION['pending_user']);
                if (isset($_SESSION['pending_code_user'])) unset($_SESSION['pending_code_user']);
                Auth::logout();
                header('Location: ' . url('login?status=senha-criada'));
                exit;
            }
        }

        $this->render('usuarios/primeiro_acesso', [
            'pageTitle' => 'Primeiro acesso',
            'usuario' => $usuario,
            'errors' => $erros,
        ]);
    }

    public function perfil(): void
    {
        Auth::requireLogin();
        $usuario = Auth::user();
        $erros = [];
        $ok = false;
        $model = new UserModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim((string)($_POST['nome_completo'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $login = trim((string)($_POST['login'] ?? ''));
            $senhaAtual = (string)($_POST['senha_atual'] ?? '');
            $novaSenha = (string)($_POST['nova_senha'] ?? '');
            $confirma = (string)($_POST['confirmar_senha'] ?? '');

            if ($nome === '') $erros[] = 'Nome é obrigatório.';
            if ($email === '') $erros[] = 'E-mail é obrigatório.';
            if ($login === '') $erros[] = 'Login é obrigatório.';

            if (!$erros) {
                $model->updatePerfil((int)$usuario['id_usuarios'], [
                    'nome_completo' => $nome,
                    'email' => $email,
                    'login' => $login,
                ]);
                $usuario['nome_completo'] = $nome;
                $usuario['email'] = $email;
                $usuario['login'] = $login;
                Auth::login($usuario);
                $ok = true;
            }

            if ($novaSenha !== '' || $confirma !== '') {
                if (strlen($novaSenha) < 8) {
                    $erros[] = 'A nova senha deve ter pelo menos 8 caracteres.';
                } elseif ($novaSenha !== $confirma) {
                    $erros[] = 'Confirmação da nova senha não confere.';
                } elseif (empty($usuario['senha_hash']) || password_verify($senhaAtual, $usuario['senha_hash'])) {
                    $model->atualizarSenha((int)$usuario['id_usuarios'], $novaSenha);
                    $ok = true;
                } else {
                    $erros[] = 'Senha atual incorreta.';
                }
            }

            if ($ok && !$erros) {
                header('Location: ' . url('perfil?status=ok'));
                exit;
            }
        }

        $this->render('usuarios/perfil', [
            'pageTitle' => 'Meu perfil',
            'usuario' => $usuario,
            'errors' => $erros,
        ]);
    }
}
