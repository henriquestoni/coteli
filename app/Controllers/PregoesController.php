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

        $erroCampos = $this->validarObrigatoriosBase($dados);
        if ($erroCampos !== null) {
            $errorMessage = $erroCampos;
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
            $idCriado = $model->createBaseR0($dados);
            $pregaoCriado = $model->getPregaoCompletoById($idCriado);
            $sigla = trim((string)($pregaoCriado['sigla_tipos_pregao'] ?? ''));
            $numFmt = str_pad((string)($pregaoCriado['id_pregao'] ?? ''), 3, '0', STR_PAD_LEFT);
            $descPregao = trim($sigla . ' ' . $numFmt . '/' . ($pregaoCriado['ano_pregao'] ?? '') . ' (R-00)');

            $proxBase = $model->getMaxNumeroBase((int)$dados['id_tipo_pregao'], (int)$dados['ano_pregao']) + 1;
            $formPrefill = [
                'id_tipo_pregao' => $dados['id_tipo_pregao'],
                'ano_pregao' => $dados['ano_pregao'],
                'id_pregao' => str_pad((string)$proxBase, 3, '0', STR_PAD_LEFT),
                'id_pregao_repeticao' => 0,
                'data_pregao' => '',
                'hora_pregao' => '',
                'id_status' => 0,
                'id_origem_pedido' => $dados['id_origem_pedido'],
                'processo_sei' => '',
                'objeto_licitado' => '',
                'data_do' => '',
                'id_pregoeiro' => $dados['id_pregoeiro'],
                'id_responsavel_coteli' => $dados['id_responsavel_coteli'],
                'lancado_site_uerj' => 0,
            ];

            $this->render('pregoes/form_base', array_merge(
                $this->combos($model),
                [
                    'pageTitle' => 'Cadastre novo pregao base',
                    'formData' => $formPrefill,
                    'successMessage' => 'Pregao base salvo com sucesso.',
                    'successPregao' => $descPregao,
                    'successProximoBase' => $proxBase,
                    'ultimosR0' => $model->getUltimosBaseR0(),
                ]
            ));
            return;
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
        $lockBase = $prefillBase > 0;
        $lockRep = $prefillBase > 0 && $prefillRep > 0;

        $formDataDefault = [
            'id_base_pregao_r0' => $prefillBase,
            'id_pregao_repeticao' => $prefillRep ?: 1,
            'lancado_site_uerj' => 0,
            'data_pregao' => '',
            'hora_pregao' => '',
        ];

        if ($lockBase) {
            $baseSel = $model->getBaseById($prefillBase);
            if ($baseSel) {
                $formDataDefault = array_merge($formDataDefault, [
                    'id_tipo_pregao' => (int)$baseSel['id_tipo_pregao'],
                    'ano_pregao' => (int)$baseSel['ano_pregao'],
                    'id_pregao' => (string)$baseSel['id_pregao'],
                    'id_origem_pedido' => (int)$baseSel['id_origem_pedido'],
                    'processo_sei' => (string)$baseSel['processo_sei'],
                    'objeto_licitado' => (string)$baseSel['objeto_licitado'],
                    'id_status' => (int)($baseSel['id_status'] ?? 0),
                    'id_pregoeiro' => 0,
                    'id_responsavel_coteli' => 0,
                ]);
            }
        }

        $this->render('pregoes/form_repeticao', array_merge(
            $this->combos($model),
            [
                'pageTitle' => 'Nova repeticao (R-X)',
                'bases' => $model->getPregoesParaRepeticao(),
                'ultimasReps' => $model->getUltimasRepeticoes(),
                'formData' => $formDataDefault,
                'lockBaseSelecionada' => $lockBase,
                'lockRepeticaoCampo' => $lockRep,
            ]
        ));
    }

    public function salvarRepeticao(): void
    {
        Auth::requireLevel(4);
        $model = new PregaoModel();

        $baseId = (int)($_POST['id_base_pregao_r0'] ?? 0);
        $base = $baseId ? $model->getBaseById($baseId) : null;

        if (!$base) {
            $errorMessage = 'Pregao nao encontrado.';
            $this->render('pregoes/form_repeticao', array_merge(
                $this->combos($model),
                [
                    'pageTitle' => 'Nova repeticao (R-X)',
                    'bases' => $model->getPregoesParaRepeticao(),
                    'formData' => $_POST,
                    'errorMessage' => $errorMessage,
                    'lockBaseSelecionada' => !empty($_POST['lock_base']),
                    'lockRepeticaoCampo' => !empty($_POST['lock_rep']),
                ]
            ));
            return;
        }

        $dados = $this->coletarDadosRepeticao($_POST, $base, $model);
        $formData = $dados + ['id_base_pregao_r0' => $baseId];
        $editId = (int)($_POST['id_base_pregoes_edit'] ?? 0);

        $erroCampos = $this->validarObrigatoriosRepeticao($dados);
        if ($erroCampos !== null) {
            $this->render('pregoes/form_repeticao', array_merge(
                $this->combos($model),
                [
                    'pageTitle' => 'Nova repeticao (R-X)',
                    'bases' => $model->getPregoesParaRepeticao(),
                    'ultimasReps' => $model->getUltimasRepeticoes(),
                    'formData' => $formData,
                    'errorMessage' => $erroCampos,
                    'lockBaseSelecionada' => !empty($_POST['lock_base']),
                    'lockRepeticaoCampo' => !empty($_POST['lock_rep']),
                ]
            ));
            return;
        }

        $duplicado = $model->buscarPorChave($dados, $editId ?: null);
        if ($duplicado) {
            $prox = $model->getProximaRepeticao((int)$dados['id_tipo_pregao'], (int)$dados['ano_pregao'], (string)$dados['id_pregao']);
            $errorMessage = 'Ja existe repeticao com este Tipo/Ano/Numero/Repeticao. Sugestao livre: R-' . str_pad((string)$prox, 2, '0', STR_PAD_LEFT) . '.';
            $this->render('pregoes/form_repeticao', array_merge(
                $this->combos($model),
                [
                    'pageTitle' => 'Nova repeticao (R-X)',
                    'bases' => $model->getPregoesParaRepeticao(),
                    'ultimasReps' => $model->getUltimasRepeticoes(),
                    'formData' => $formData,
                    'errorMessage' => $errorMessage,
                    'lockBaseSelecionada' => !empty($_POST['lock_base']),
                    'lockRepeticaoCampo' => !empty($_POST['lock_rep']),
                ]
            ));
            return;
        }

        try {
            if ($editId > 0) {
                $model->updatePregao($editId, $dados);
                $idCriado = $editId;
            } else {
                $idCriado = $model->createRepeticao($dados);
            }

            $proxRep = $model->getProximaRepeticao((int)$dados['id_tipo_pregao'], (int)$dados['ano_pregao'], (string)$dados['id_pregao']);
            $pregaoCriado = $model->getPregaoCompletoById($idCriado);
            $repFmt = str_pad((string)($dados['id_pregao_repeticao'] ?? ''), 2, '0', STR_PAD_LEFT);
            $descPregao = '';
            if ($pregaoCriado) {
                $sigla = trim((string)($pregaoCriado['sigla_tipos_pregao'] ?? ''));
                $numFmt = str_pad((string)($pregaoCriado['id_pregao'] ?? ''), 3, '0', STR_PAD_LEFT);
                $descPregao = trim($sigla . ' ' . $numFmt . '/' . ($pregaoCriado['ano_pregao'] ?? '') . ' (R-' . $repFmt . ')');
            }

            $formPrefill = array_merge(
                [
                    'id_base_pregao_r0' => $baseId,
                    'id_tipo_pregao' => $dados['id_tipo_pregao'],
                    'ano_pregao' => $dados['ano_pregao'],
                    'id_pregao' => $dados['id_pregao'],
                    'id_origem_pedido' => $dados['id_origem_pedido'],
                    'processo_sei' => $dados['processo_sei'],
                    'objeto_licitado' => $dados['objeto_licitado'],
                    'lancado_site_uerj' => 0,
                ],
                $editId > 0
                    ? [
                        'id_base_pregoes_edit' => $editId,
                        'id_pregao_repeticao' => $dados['id_pregao_repeticao'],
                        'data_pregao' => $dados['data_pregao'],
                        'hora_pregao' => $dados['hora_pregao'],
                        'id_status' => $dados['id_status'],
                        'id_pregoeiro' => $dados['id_pregoeiro'],
                        'id_responsavel_coteli' => $dados['id_responsavel_coteli'],
                        'data_do' => $dados['data_do'],
                    ]
                    : [
                        'id_pregao_repeticao' => $proxRep,
                        'data_pregao' => '',
                        'hora_pregao' => '',
                        'id_status' => 0,
                        'id_pregoeiro' => 0,
                        'id_responsavel_coteli' => 0,
                        'data_do' => '',
                    ]
            );

            $this->render('pregoes/form_repeticao', array_merge(
                $this->combos($model),
                [
                    'pageTitle' => 'Nova repeticao (R-X)',
                    'bases' => $model->getPregoesParaRepeticao(),
                    'ultimasReps' => $model->getUltimasRepeticoes(),
                    'formData' => $formPrefill,
                    'successMessage' => 'Repeticao salva com sucesso.',
                    'successPregao' => $descPregao,
                    'successProximaRep' => $proxRep,
                    'lockBaseSelecionada' => false,
                    'lockRepeticaoCampo' => false,
                ]
            ));
            return;
        } catch (PregaoDuplicadoException $e) {
            $errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            $errorMessage = 'Erro ao salvar repeticao: ' . $e->getMessage();
        }

        $this->render('pregoes/form_repeticao', array_merge(
            $this->combos($model),
            [
                'pageTitle' => 'Nova repeticao (R-X)',
                'bases' => $model->getPregoesParaRepeticao(),
                'ultimasReps' => $model->getUltimasRepeticoes(),
                'formData' => $formData,
                'errorMessage' => $errorMessage ?? null,
                'lockBaseSelecionada' => !empty($_POST['lock_base']),
                'lockRepeticaoCampo' => !empty($_POST['lock_rep']),
            ]
        ));
    }

    public function buscarPregao(): void
    {
        Auth::requireLevel(4);
        $id = (int)($_GET['id'] ?? 0);
        header('Content-Type: application/json');
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'erro', 'message' => 'ID inválido']);
            return;
        }
        $model = new PregaoModel();
        $pregao = $model->getPregaoCompletoById($id);
        if (!$pregao) {
            http_response_code(404);
            echo json_encode(['status' => 'erro', 'message' => 'Pregão não encontrado']);
            return;
        }
        echo json_encode(['status' => 'ok', 'pregao' => $pregao]);
    }

    public function definirPregoeiro(): void
    {
        Auth::requireLevel(3);

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw ?: '', true);
        $input = is_array($payload) ? $payload : $_POST;

        $idBase = (int)($input['id_base_pregao'] ?? $input['id_base'] ?? 0);
        $idPregoeiro = (int)($input['id_pregoeiro'] ?? 0);

        header('Content-Type: application/json');

        if ($idBase <= 0 || $idPregoeiro <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'erro', 'message' => 'Dados insuficientes para designar o pregoeiro.']);
            return;
        }

        $model = new PregaoModel();
        $base = $model->getBaseById($idBase);
        if (!$base) {
            http_response_code(404);
            echo json_encode(['status' => 'erro', 'message' => 'Pregão não encontrado.']);
            return;
        }

        $pregoeiro = $model->getPregoeiroById($idPregoeiro);
        if (!$pregoeiro) {
            http_response_code(400);
            echo json_encode(['status' => 'erro', 'message' => 'Pregoeiro inválido ou inativo.']);
            return;
        }

        try {
            $model->definirPregoeiro($idBase, $idPregoeiro);
            echo json_encode([
                'status' => 'ok',
                'pregoeiro_nome' => $pregoeiro['nome_completo'] ?? '',
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'erro', 'message' => 'Não foi possível salvar o pregoeiro.']);
        }
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

    private function validarObrigatoriosBase(array $dados): ?string
    {
        $faltando = [];
        if (empty($dados['id_tipo_pregao'])) {
            $faltando[] = 'Tipo de Pregão';
        }
        if (empty($dados['ano_pregao'])) {
            $faltando[] = 'Ano do Pregão';
        }
        if (trim((string)$dados['id_pregao']) === '') {
            $faltando[] = 'Número do Pregão';
        }
        if (empty($dados['data_pregao'])) {
            $faltando[] = 'Data da Abertura';
        }
        if (empty($dados['id_origem_pedido'])) {
            $faltando[] = 'Origem do Processo';
        }
        if (trim((string)$dados['processo_sei']) === '') {
            $faltando[] = 'Processo SEI';
        }
        if (empty($dados['data_do'])) {
            $faltando[] = 'Publicação no D.O.';
        }
        if (empty($dados['id_responsavel_coteli'])) {
            $faltando[] = 'Responsável na COTELI';
        }

        if (!empty($faltando)) {
            return 'Preencha os campos obrigatórios: ' . implode(', ', $faltando) . '.';
        }
        return null;
    }

    private function validarObrigatoriosRepeticao(array $dados): ?string
    {
        $faltando = [];
        if (empty($dados['id_tipo_pregao'])) {
            $faltando[] = 'Tipo de Pregão';
        }
        if (empty($dados['ano_pregao'])) {
            $faltando[] = 'Ano do Pregão';
        }
        if (trim((string)$dados['id_pregao']) === '') {
            $faltando[] = 'Número do Pregão';
        }
        $rep = (int)($dados['id_pregao_repeticao'] ?? 0);
        if ($rep <= 0) {
            $faltando[] = 'Repetição do Pregão';
        }
        if (empty($dados['data_pregao'])) {
            $faltando[] = 'Data da Abertura';
        }
        if (empty($dados['id_origem_pedido'])) {
            $faltando[] = 'Origem do Processo';
        }
        if (trim((string)$dados['processo_sei']) === '') {
            $faltando[] = 'Processo SEI';
        }
        if (empty($dados['data_do'])) {
            $faltando[] = 'Publicação no D.O.';
        }
        if (empty($dados['id_responsavel_coteli'])) {
            $faltando[] = 'Responsável na COTELI';
        }

        if (!empty($faltando)) {
            return 'Preencha os campos obrigatórios: ' . implode(', ', $faltando) . '.';
        }
        return null;
    }
}
