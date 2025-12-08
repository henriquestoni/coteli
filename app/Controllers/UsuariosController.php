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
        Auth::requireLogin(true);
        $usuario = Auth::user();

        if (empty($usuario['precisa_trocar_senha'])) {
            header('Location: ' . url(''));
            exit;
        }

        $erros = [];
        $model = new UserModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $novaSenha = (string)($_POST['nova_senha'] ?? '');
            $confirmaSenha = (string)($_POST['confirmar_senha'] ?? '');
            $novoLogin = isset($_POST['login']) ? trim((string)$_POST['login']) : null;

            if ($novaSenha === '' || $confirmaSenha === '') {
                $erros[] = 'Informe a nova senha e a confirmação.';
            } elseif ($novaSenha !== $confirmaSenha) {
                $erros[] = 'A confirmação de senha não confere.';
            }

            if (!$erros) {
                $model->atualizarSenhaDefinitiva((int)$usuario['id_usuarios'], $novaSenha, $novoLogin);
                $atualizado = $model->findById((int)$usuario['id_usuarios']);
                if ($atualizado) {
                    $atualizado['id'] = $atualizado['id_usuarios'];
                    Auth::login($atualizado);
                }
                header('Location: ' . url(''));
                exit;
            }
        }

        $this->render('usuarios/primeiro_acesso', [
            'pageTitle' => 'Primeiro acesso',
            'usuario' => $usuario,
            'errors' => $erros,
        ]);
    }
}
