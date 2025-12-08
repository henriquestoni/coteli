<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\PregaoModel;
use App\Models\PregaoDuplicadoException;

class PregoesController extends BaseController
{
    public function index(): void
    {
        header('Location: ' . url('pregoes/novo-base'));
        exit;
    }

    public function verificarChave(): void
    {
        $dados = [
            'id_tipo_pregao'      => (int)($_GET['id_tipo_pregao'] ?? 0),
            'ano_pregao'          => (int)($_GET['ano_pregao'] ?? 0),
            'id_pregao'           => trim((string)($_GET['id_pregao'] ?? '')),
            'id_pregao_repeticao' => (int)($_GET['id_pregao_repeticao'] ?? 0),
        ];
        $ignoreId = isset($_GET['ignore_id']) ? (int)$_GET['ignore_id'] : null;

        $model = new PregaoModel();
        $existente = $model->buscarPorChave($dados, $ignoreId);

        header('Content-Type: application/json');
        if (!$existente) {
            echo json_encode(['status' => 'livre']);
            exit;
        }

        $sigla = (string)($existente['sigla_tipos_pregao'] ?? '');
        $numFmt = str_pad((string)$existente['id_pregao'], 3, '0', STR_PAD_LEFT);
        $ano = $existente['ano_pregao'];
        $repFmt = str_pad((string)$existente['id_pregao_repeticao'], 2, '0', STR_PAD_LEFT);

        if ((int)$existente['id_pregao_repeticao'] === 0) {
            $maxBase = $model->getMaxNumeroBase((int)$dados['id_tipo_pregao'], (int)$dados['ano_pregao']);
            $proximoBaseNum = $maxBase + 1;
            $proximoBaseFmt = $sigla . ' ' . str_pad((string)$proximoBaseNum, 3, '0', STR_PAD_LEFT) . '/' . $ano;

            $maxRep = $model->getMaxRepeticao((int)$dados['id_tipo_pregao'], (int)$dados['ano_pregao'], $existente['id_pregao']);
            $sugRep = $maxRep + 1;
            $sugRepFmt = $sigla . ' ' . $numFmt . '/' . $ano . ' (R-' . str_pad((string)$sugRep, 2, '0', STR_PAD_LEFT) . ')';

            echo json_encode([
                'status' => 'duplicado_base',
                'pregao' => [
                    'id_base_pregoes' => $existente['id_base_pregoes'],
                    'descricao_resumida' => $sigla . ' ' . $numFmt . '/' . $ano . ' (R-00)',
                    'proximo_base' => $proximoBaseFmt,
                    'proxima_repeticao_sugerida' => $sugRepFmt,
                    'proxima_repeticao_num' => $sugRep,
                    'sigla' => $sigla,
                    'ano_pregao' => $ano,
                    'id_pregao' => $existente['id_pregao'],
                ],
            ]);
            exit;
        }

        $maxRep = $model->getMaxRepeticao((int)$dados['id_tipo_pregao'], (int)$dados['ano_pregao'], $existente['id_pregao']);
        $sugRep = $maxRep + 1;
        $sugRepFmt = 'R-' . str_pad((string)$sugRep, 2, '0', STR_PAD_LEFT);
        $desc = $sigla . ' ' . $numFmt . '/' . $ano . ' (R-' . $repFmt . ')';

        echo json_encode([
            'status' => 'duplicado_repeticao',
            'pregao' => [
                'id_base_pregoes' => $existente['id_base_pregoes'],
                'descricao_resumida' => $desc,
                'repeticao_informada' => 'R-' . $repFmt,
                'proxima_repeticao_sugerida' => $sugRepFmt,
                'proxima_repeticao_num' => $sugRep,
            ],
        ]);
        exit;
    }

    public function novoBase(): void
    {
        Auth::requireLevel(4);
        $model = new PregaoModel();
        $this->render('pregoes/form_base', array_merge(
            $this->combos($model),
            [
                'pageTitle' => 'Cadastre novo pregao base',
                'formData' => [
                    'lancado_site_uerj' => 0,
                    'ano_pregao' => (int)date('Y'),
                    'data_pregao' => '',
                    'hora_pregao' => '',
                ],
                'ultimosR0' => $model->getUltimosBaseR0(),
            ]
        ));
    }

    public function salvarBase(): void
    {
        Auth::requireLevel(4);
        $model = new PregaoModel();

        $dados = $this->coletarDadosBase($_POST);
        $formData = $dados;
        $formData['lancado_site_uerj'] = $dados['lancado_site_uerj'] ?? 0;

        if (empty($dados['data_do'])) {
            $errorMessage = 'Publicacao no D.O. e obrigatoria.';
            $this->render('pregoes/form_base', array_merge(
                $this->combos($model),
                [
                    'pageTitle' => 'Cadastre novo pregao base',
                    'formData' => $formData,
                    'errorMessage' => $errorMessage,
                    'ultimosR0' => $model->getUltimosBaseR0(),
                ]
            ));
            return;
        }

        try {
            $model->createBaseR0($dados);
            header('Location: ' . url('pregoes'));
            exit;
        } catch (PregaoDuplicadoException $e) {
            $errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            $errorMessage = 'Erro ao salvar pregao base: ' . $e->getMessage();
        }

        $this->render('pregoes/form_base', array_merge(
            $this->combos($model),
            [
                'pageTitle' => 'Cadastre novo pregao base',
                'formData' => $formData,
                'errorMessage' => $errorMessage ?? null,
                'ultimosR0' => $model->getUltimosBaseR0(),
            ]
        ));
    }

    public function novaRepeticao(): void
    {
        Auth::requireLevel(4);
        $model = new PregaoModel();

        $prefillBase = (int)($_GET['id_base_pregao_r0'] ?? 0);
        $prefillRep = (int)($_GET['rep_sugerida'] ?? 1);

        $this->render('pregoes/form_repeticao', array_merge(
            $this->combos($model),
            [
                'pageTitle' => 'Nova repeticao (R-X)',
                'basesR0' => $model->getBasesR0(),
                'formData' => [
                    'id_base_pregao_r0' => $prefillBase,
                    'id_pregao_repeticao' => $prefillRep ?: 1,
                    'lancado_site_uerj' => 0,
                    'data_pregao' => '',
                    'hora_pregao' => '',
                ],
            ]
        ));
    }

    public function salvarRepeticao(): void
    {
        Auth::requireLevel(4);
        $model = new PregaoModel();

        $baseId = (int)($_POST['id_base_pregao_r0'] ?? 0);
        $base = $baseId ? $model->getBaseById($baseId) : null;

        if (!$base || (int)$base['id_pregao_repeticao'] !== 0) {
            $errorMessage = 'Pregao base (R-0) nao encontrado ou invalido.';
            $this->render('pregoes/form_repeticao', array_merge(
                $this->combos($model),
                [
                    'pageTitle' => 'Nova repeticao (R-X)',
                    'basesR0' => $model->getBasesR0(),
                    'formData' => $_POST,
                    'errorMessage' => $errorMessage,
                ]
            ));
            return;
        }

        $dados = $this->coletarDadosRepeticao($_POST, $base, $model);
        $formData = $dados + ['id_base_pregao_r0' => $baseId];

        try {
            $model->createRepeticao($dados);
            header('Location: ' . url('pregoes'));
            exit;
        } catch (PregaoDuplicadoException $e) {
            $errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            $errorMessage = 'Erro ao salvar repeticao: ' . $e->getMessage();
        }

        $this->render('pregoes/form_repeticao', array_merge(
            $this->combos($model),
            [
                'pageTitle' => 'Nova repeticao (R-X)',
                'basesR0' => $model->getBasesR0(),
                'formData' => $formData,
                'errorMessage' => $errorMessage ?? null,
            ]
        ));
    }

    private function combos(PregaoModel $model): array
    {
        return [
            'tiposPregao' => $model->getTiposPregao(),
            'origensPedido' => $model->getOrigensPedido(),
            'statusPregao' => $model->getStatusPregao(),
            'pregoeiros' => $model->getPregoeiros(),
            'responsaveis' => $model->getResponsaveisCoteli(),
        ];
    }

    private function coletarDadosBase(array $input): array
    {
        return [
            'id_tipo_pregao'        => (int)($input['id_tipo_pregao'] ?? 0),
            'ano_pregao'            => (int)($input['ano_pregao'] ?? 0),
            'id_pregao'             => trim((string)($input['id_pregao'] ?? '')),
            'id_pregao_repeticao'   => 0,
            'data_pregao'           => trim((string)($input['data_pregao'] ?? '')),
            'hora_pregao'           => trim((string)($input['hora_pregao'] ?? '')),
            'id_status'             => (int)($input['id_status'] ?? 0),
            'id_origem_pedido'      => (int)($input['id_origem_pedido'] ?? 0),
            'processo_sei'          => trim((string)($input['processo_sei'] ?? '')),
            'objeto_licitado'       => trim((string)($input['objeto_licitado'] ?? '')),
            'data_do'               => trim((string)($input['data_do'] ?? '')),
            'id_pregoeiro'          => (int)($input['id_pregoeiro'] ?? 0),
            'id_responsavel_coteli' => (int)($input['id_responsavel_coteli'] ?? 0),
            'lancado_site_uerj'     => isset($input['lancado_site_uerj']) ? (int)$input['lancado_site_uerj'] : 0,
        ];
    }

    private function coletarDadosRepeticao(array $input, array $base, PregaoModel $model): array
    {
        $idTiposPregao = (int)$base['id_tipo_pregao'];
        $anoPregao = (int)$base['ano_pregao'];
        $idPregao = (string)$base['id_pregao'];

        $repInformado = isset($input['id_pregao_repeticao']) ? (int)$input['id_pregao_repeticao'] : null;
        $rep = $repInformado !== null && $repInformado > 0
            ? $repInformado
            : $model->getProximaRepeticao($idTiposPregao, $anoPregao, $idPregao);

        return [
            'id_tipo_pregao'        => $idTiposPregao,
            'ano_pregao'            => $anoPregao,
            'id_pregao'             => $idPregao,
            'id_pregao_repeticao'   => $rep,
            'data_pregao'           => trim((string)($input['data_pregao'] ?? '')),
            'hora_pregao'           => trim((string)($input['hora_pregao'] ?? '')),
            'id_status'             => (int)($input['id_status'] ?? 0),
            'id_origem_pedido'      => (int)($input['id_origem_pedido'] ?? 0),
            'processo_sei'          => trim((string)($input['processo_sei'] ?? $base['processo_sei'] ?? '')),
            'objeto_licitado'       => trim((string)($input['objeto_licitado'] ?? $base['objeto_licitado'] ?? '')),
            'data_do'               => trim((string)($input['data_do'] ?? '')),
            'id_pregoeiro'          => (int)($input['id_pregoeiro'] ?? 0),
            'id_responsavel_coteli' => (int)($input['id_responsavel_coteli'] ?? 0),
            'lancado_site_uerj'     => isset($input['lancado_site_uerj']) ? (int)$input['lancado_site_uerj'] : 0,
        ];
    }
}
