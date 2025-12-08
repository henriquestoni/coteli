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
        $pregaoId = isset($_GET['id_base_pregao']) ? (int)$_GET['id_base_pregao'] : 0;

        $pregoesModel = new PregaoModel();
        $amostrasModel = new AmostraModel();

        $pregoesR0 = $pregoesModel->getBasesR0();
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
            'pregoesR0' => $pregoes->getBasesR0(),
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
                'pregoesR0' => (new PregaoModel())->getBasesR0(),
                'tiposItem' => $amostras->listarTiposLicitados(),
                'tiposParecer' => $amostras->listarTiposParecer(),
                'responsaveis' => $amostras->listarResponsaveis(),
                'empresas' => $amostras->listarEmpresas(),
            ]);
            return;
        }

        header('Location: ' . url('amostras?id_base_pregao=' . (int)$dados['id_base_pregao']));
        exit;
    }

    private function coletarDados(array $input): array
    {
        return [
            'id_base_pregao'      => (int)($input['id_base_pregao'] ?? 0),
            'id_momento_cadastro' => $input['id_momento_cadastro'] ?? $this->gerarMomentoCadastro(),
            'item_licitado'       => trim((string)($input['item_licitado'] ?? '')),
            'id_tipo_item'        => (int)($input['id_tipo_item'] ?? 0),
            'total_unidades'      => $input['total_unidades'] === '' ? null : (float)$input['total_unidades'],
            'id_responsavel'      => (int)($input['id_responsavel'] ?? 0),
            'id_empresa'          => (int)($input['id_empresa'] ?? 0),
            'observacoes'         => trim((string)($input['observacoes'] ?? '')),
            'entregue_coteli'     => !empty($input['entregue_coteli']) ? 1 : 0,
            'chegada_coteli'      => $input['chegada_coteli'] ?? null,
            'saida_coteli'        => $input['saida_coteli'] ?? null,
            'id_tipo_parecer'     => (int)($input['id_tipo_parecer'] ?? 0),
            'data_parecer'        => $input['data_parecer'] ?? null,
        ];
    }

    private function gerarMomentoCadastro(): string
    {
        return date('YmdHis');
    }
}
