<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\AmostraModel;
use App\Models\PregaoModel;

class AmostrasController extends BaseController
{
    public function index(): void
    {
        Auth::requireLevel(2);
        $pregaoId = isset($_GET['id_base_amostra']) ? (int)$_GET['id_base_amostra'] : 0;

        $pregoesModel = new PregaoModel();
        $amostrasModel = new AmostraModel();

        $pregoesR0 = $pregoesModel->getPregoesParaRepeticao();
        $amostras = $pregaoId ? $amostrasModel->listarAmostrasPorPregao($pregaoId) : [];

        $this->render('amostras/index', [
            'pageTitle' => 'Amostras',
            'pregoesR0' => $pregoesR0,
            'amostras' => $amostras,
            'pregaoSelecionado' => $pregaoId,
        ]);
    }

    public function nova(): void
    {
        Auth::requireLevel(3);
        $amostras = new AmostraModel();
        $pregoes = new PregaoModel();

        $this->render('amostras/form', [
            'pageTitle' => 'Nova amostra',
            'pregoesR0' => $pregoes->getPregoesParaRepeticao(),
            'tiposItem' => $amostras->listarTiposLicitados(),
            'tiposParecer' => $amostras->listarTiposParecer(),
            'responsaveis' => $amostras->listarResponsaveis(),
            'empresas' => $amostras->listarEmpresas(),
            'formData' => ['id_momento_cadastro' => $this->gerarMomentoCadastro()],
        ]);
    }

    public function salvar(): void
    {
        Auth::requireLevel(3);
        $amostras = new AmostraModel();

        $dados = $this->coletarDados($_POST);
        $resultado = $amostras->inserirAmostra($dados);

        if (!empty($resultado['erro']) && $resultado['erro'] === 'duplicidade') {
            $this->render('amostras/form', [
                'pageTitle' => 'Nova amostra',
                'errorDuplicate' => $resultado['existente'] ?? null,
                'formData' => $dados,
                'pregoesR0' => (new PregaoModel())->getPregoesParaRepeticao(),
                'tiposItem' => $amostras->listarTiposLicitados(),
                'tiposParecer' => $amostras->listarTiposParecer(),
                'responsaveis' => $amostras->listarResponsaveis(),
                'empresas' => $amostras->listarEmpresas(),
            ]);
            return;
        }

        header('Location: ' . url('amostras?id_base_amostra=' . (int)$dados['id_base_amostra']));
        exit;
    }

    public function criarEmpresa(): void
    {
        Auth::requireLevel(3);
        header('Content-Type: application/json');
        $payload = json_decode(file_get_contents('php://input') ?: '', true);
        $nome = trim((string)($payload['nome'] ?? ''));
        $email = trim((string)($payload['email'] ?? ''));
        $telefone = trim((string)($payload['telefone'] ?? ''));
        $cnpj = trim((string)($payload['cnpj'] ?? ''));

        if ($nome === '' || $email === '' || $cnpj === '') {
            http_response_code(400);
            echo json_encode(['status' => 'erro', 'message' => 'Nome, e-mail e CNPJ são obrigatórios.']);
            return;
        }

        try {
            $model = new AmostraModel();
            $id = $model->criarEmpresa($nome, $email, $telefone ?: null, $cnpj);
            echo json_encode(['status' => 'ok', 'id' => $id, 'nome' => $nome, 'email' => $email, 'telefone' => $telefone, 'cnpj' => $cnpj]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'erro', 'message' => 'Não foi possível cadastrar a empresa.']);
        }
    }

    private function coletarDados(array $input): array
    {
        return [
            'id_base_amostra'     => (int)($input['id_base_amostra'] ?? 0),
            'id_momento_cadastro' => $input['id_momento_cadastro'] ?? $this->gerarMomentoCadastro(),
            'item_licitado'       => trim((string)($input['item_licitado'] ?? '')),
            'id_tipos_licitados'  => (int)($input['id_tipos_licitados'] ?? 0),
            'total_unidades'      => $input['total_unidades'] === '' ? null : (float)$input['total_unidades'],
            'id_responsavel'      => (int)($input['id_responsavel'] ?? 0),
            'id_empresa'          => (int)($input['id_empresa'] ?? 0),
            'observacoes'         => trim((string)($input['observacoes'] ?? '')),
            'entregue_coteli'     => !empty($input['entregue_coteli']) ? 1 : 0,
            'chegada_coteli'      => $input['chegada_coteli'] ?? null,
            'saida_coteli'        => $input['saida_coteli'] ?? null,
            'id_tipos_parecer'    => (int)($input['id_tipos_parecer'] ?? 0),
            'data_parecer'        => $input['data_parecer'] ?? null,
        ];
    }

    private function gerarMomentoCadastro(): string
    {
        return date('YmdHis');
    }
}
