<?php
<<<<<<< HEAD
/** @var array $bases */
/** @var array $formData */
/** @var array $tiposRepeticao */
=======
>>>>>>> 99e3d7fbbbc5fdcfa5e4bd8d2744761b3c0623b7
/** @var array $tiposPregao */
/** @var array $origensPedido */
/** @var array $statusPregao */
/** @var array $pregoeiros */
/** @var array $responsaveis */
<<<<<<< HEAD
/** @var string|null $errorMessage */
/** @var string|null $successMessage */
/** @var string|null $successPregao */
/** @var int|null $successProximaRep */
/** @var bool $lockBaseSelecionada */
/** @var bool $lockRepeticaoCampo */
?>
<?php
$lockBaseSelecionada = $lockBaseSelecionada ?? false;
$lockRepeticaoCampo = $lockRepeticaoCampo ?? false;
?>
<div class="form-shell">
    <div class="form-header">
        <div>
            <p class="eyebrow mb-1">Pregões</p>
            <h1 class="h4 mb-2">Criar repetição de pregão</h1>
            <p class="text-muted mb-0">Aplique o novo esquema de cores, mantenha a numeração e aproveite os atalhos rápidos.</p>
        </div>
        <div class="d-flex flex-column gap-2 align-items-end">
            <span class="pill">Fluxo R-X</span>
            <a href="<?= url('pregoes') ?>" class="btn btn-neutro btn-compact"><i class="bi bi-arrow-left"></i> Voltar</a>
        </div>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success mb-0">
            <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
            <?php if (!empty($successPregao)): ?>
                <div class="small text-muted mt-1">Pregão salvo: <?= htmlspecialchars($successPregao, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger mb-0"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form
                method="post"
                action="<?= url('pregoes/salvar-repeticao') ?>"
                class="row g-4"
                data-dup-url="<?= url('pregoes/verificar-chave') ?>"
                data-fetch-pregao="<?= url('pregoes/buscar') ?>"
                data-lock-base="<?= !empty($lockBaseSelecionada) ? '1' : '0' ?>"
                data-lock-rep="<?= !empty($lockRepeticaoCampo) ? '1' : '0' ?>"
            >
                <input type="hidden" name="id_base_pregoes_edit" id="campoEditId" value="<?= htmlspecialchars((string)($formData['id_base_pregoes_edit'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="lock_base" value="<?= !empty($lockBaseSelecionada) ? '1' : '0' ?>">
                <input type="hidden" name="lock_rep" value="<?= !empty($lockRepeticaoCampo) ? '1' : '0' ?>">
                <div class="col-12">
                    <div class="fieldset-title"><i class="bi bi-list-check"></i> Escolha a base</div>
                    <div class="input-grid">
                        <div>
                            <label class="form-label">Escolha o pregão para repetição</label>
                            <select name="id_base_pregao_r0" class="form-select<?= !empty($lockBaseSelecionada) ? ' input-locked' : '' ?>" required <?= !empty($lockBaseSelecionada) ? 'disabled' : '' ?>>
                                <option value="">Selecione o pregão (R-0 ou R-X)</option>
                                <?php foreach ($bases as $base): ?>
                                    <?php
                                        $value = (int)$base['id_base_pregoes'];
                                        $sigla = trim((string)($base['sigla_tipos_pregao'] ?? ''));
                                        $numero = str_pad((string)($base['id_pregao'] ?? ''), 3, '0', STR_PAD_LEFT);
                                        $ano = (string)($base['ano_pregao'] ?? '');
                                        $rep = (int)($base['id_pregao_repeticao'] ?? 0);
                                        $processo = trim((string)($base['processo_sei'] ?? ''));
                                        $idTipo = (int)($base['id_tipo_pregao'] ?? 0);
                                        $idOrigem = (int)($base['id_origem_pedido'] ?? 0);
                                        $idStatus = (int)($base['id_status'] ?? 0);
                                        $dataAbertura = trim((string)($base['data_pregao'] ?? ''));
                                        $horaAbertura = trim((string)($base['hora_pregao'] ?? ''));
                                        $objeto = trim((string)($base['objeto_licitado'] ?? ''));
                                        $idPregoeiro = (int)($base['id_pregoeiro'] ?? 0);
                                        $idResp = (int)($base['id_responsavel_coteli'] ?? 0);
                                        $lancado = (int)($base['lancado_site_uerj'] ?? 0);
                                        $labelNumero = ($sigla !== '' ? $sigla . '-' : '') . $numero . '/' . $ano;
                                        $repLabel = 'R-' . str_pad((string)$rep, 2, '0', STR_PAD_LEFT);
                                        $label = $labelNumero . ' (' . $repLabel . ')';
                                        if ($processo !== '') {
                                            $label .= ' - ' . $processo;
                                        }
                                    ?>
                                    <option
                                        value="<?= $value ?>"
                                        data-tipo="<?= htmlspecialchars($sigla) ?>"
                                        data-idtipo="<?= $idTipo ?>"
                                        data-ano="<?= htmlspecialchars($ano) ?>"
                                        data-numero="<?= htmlspecialchars($numero) ?>"
                                        data-rep="<?= $rep ?>"
                                        data-sei="<?= htmlspecialchars($processo) ?>"
                                        data-origem="<?= $idOrigem ?>"
                                        data-status="<?= $idStatus ?>"
                                        data-data="<?= htmlspecialchars($dataAbertura) ?>"
                                        data-hora="<?= htmlspecialchars($horaAbertura) ?>"
                                        data-objeto="<?= htmlspecialchars($objeto) ?>"
                                        data-pregoeiro="<?= $idPregoeiro ?>"
                                        data-resp="<?= $idResp ?>"
                                        data-lancado="<?= $lancado ?>"
                                        <?= ((int)($formData['id_base_pregao_r0'] ?? 0) === $value) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($lockBaseSelecionada)): ?>
                                <input type="hidden" name="id_base_pregao_r0" value="<?= htmlspecialchars((string)($formData['id_base_pregao_r0'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="input-grid mt-3 linha-identificacao-rep">
                        <div>
                            <label class="form-label">Tipo de Pregão</label>
                            <select name="id_tipo_pregao" id="campoTipoPregao" class="form-select input-locked" required disabled>
                                <option value="">Selecione...</option>
                                <?php foreach ($tiposPregao as $tipo): ?>
                                    <option value="<?= (int)$tipo['id_tipos_pregao'] ?>" <?= ((int)($formData['id_tipo_pregao'] ?? 0) === (int)$tipo['id_tipos_pregao']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo['sigla_tipos_pregao'] ?? '') ?> - <?= htmlspecialchars($tipo['nome_tipos_pregao'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="id_tipo_pregao" id="hiddenTipoPregao" value="<?= (int)($formData['id_tipo_pregao'] ?? 0) ?>">
                        </div>
                        <div>
                            <label class="form-label">Ano</label>
                            <input type="number" name="ano_pregao" id="campoAnoPregao" class="form-control input-locked" value="<?= htmlspecialchars((string)($formData['ano_pregao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required disabled>
                            <input type="hidden" name="ano_pregao" id="hiddenAnoPregao" value="<?= htmlspecialchars((string)($formData['ano_pregao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div>
                            <label class="form-label">Número</label>
                            <input type="text" name="id_pregao" id="campoNumeroPregao" class="form-control input-locked" maxlength="3" pattern="^[0-9]{3}$" value="<?= htmlspecialchars((string)($formData['id_pregao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required disabled>
                            <input type="hidden" name="id_pregao" id="hiddenNumeroPregao" value="<?= htmlspecialchars((string)($formData['id_pregao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div>
                            <label class="form-label">Repetição</label>
                            <input
                                type="text"
                                name="id_pregao_repeticao"
                                id="campoRepeticao"
                                class="form-control"
                                maxlength="2"
                                pattern="^[0-9]{2}$"
                                placeholder="00"
                                value="<?= htmlspecialchars((string)($formData['id_pregao_repeticao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                <?= !empty($lockRepeticaoCampo) ? 'readonly disabled' : '' ?>
                                required>
                            <?php if (!empty($lockRepeticaoCampo)): ?>
                                <input type="hidden" name="id_pregao_repeticao" value="<?= htmlspecialchars((string)($formData['id_pregao_repeticao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="form-label">Data da Abertura</label>
                            <input type="date" name="data_pregao" id="campoDataPregao" class="form-control" value="<?= htmlspecialchars((string)($formData['data_pregao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Hora</label>
                            <?php $horaSelecionada = !empty($formData['hora_pregao']) ? $formData['hora_pregao'] : '10:00'; ?>
                            <select name="hora_pregao" id="campoHoraPregao" class="form-select" required>
                                <?php foreach (['09:00','10:00','11:00','13:00','14:00','15:00'] as $hora): ?>
                                    <option value="<?= $hora ?>" <?= ($horaSelecionada === $hora) ? 'selected' : '' ?>><?= $hora ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Status</label>
                            <?php $statusSelecionado = isset($formData['id_status']) ? (int)$formData['id_status'] : 1; ?>
                            <select name="id_status" id="campoStatusPregao" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($statusPregao as $status): ?>
                                    <?php $idStatus = (int)$status['id_status_pregao']; ?>
                                    <option value="<?= $idStatus ?>" <?= ($statusSelecionado === $idStatus) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($status['nome_status_pregao'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="fieldset-title"><i class="bi bi-diagram-3"></i> Processo</div>
                    <div class="input-grid">
                        <div>
                            <label class="form-label">Origem do processo</label>
                            <select name="id_origem_pedido" id="campoOrigemPregao" class="form-select input-locked" required disabled>
                                <option value="">Selecione...</option>
                                <?php foreach ($origensPedido as $origem): ?>
                                    <option value="<?= (int)$origem['id_origens_pedido'] ?>" <?= ((int)($formData['id_origem_pedido'] ?? 0) === (int)$origem['id_origens_pedido']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($origem['unidade_origem'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="id_origem_pedido" id="hiddenOrigemPregao" value="<?= (int)($formData['id_origem_pedido'] ?? 0) ?>">
                        </div>
                        <div>
                            <label class="form-label">Processo SEI</label>
                            <input type="text" name="processo_sei" id="campoProcessoSei" class="form-control input-locked" placeholder="SEI-000000/000000/AAAA" pattern="^SEI-[0-9]{6}/[0-9]{6}/[0-9]{4}$" maxlength="22" value="<?= htmlspecialchars((string)($formData['processo_sei'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" readonly disabled>
                            <input type="hidden" name="processo_sei" id="hiddenProcessoSei" value="<?= htmlspecialchars((string)($formData['processo_sei'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="fieldset-title"><i class="bi bi-journal-text"></i> Objeto licitado</div>
                    <textarea name="objeto_licitado" id="campoObjeto" class="form-control input-locked" rows="3" required readonly disabled><?= htmlspecialchars((string)($formData['objeto_licitado'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                    <input type="hidden" name="objeto_licitado" id="hiddenObjeto" value="<?= htmlspecialchars((string)($formData['objeto_licitado'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="col-12">
                    <div class="fieldset-title"><i class="bi bi-people"></i> Publicação e equipe</div>
                    <div class="input-grid">
                        <div>
                            <label class="form-label">Publicação no D.O.</label>
                            <input type="date" name="data_do" class="form-control" value="<?= htmlspecialchars((string)($formData['data_do'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div>
                            <label class="form-label">Pregoeiro</label>
                            <select name="id_pregoeiro" id="campoPregoeiro" class="form-select">
                                <option value="">[indefinido]</option>
                                <?php foreach ($pregoeiros as $p): ?>
                                    <option value="<?= (int)$p['id_usuarios'] ?>" <?= ((int)($formData['id_pregoeiro'] ?? 0) === (int)$p['id_usuarios']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nome_completo'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Responsavel na COTELI</label>
                            <select name="id_responsavel_coteli" id="campoResponsavel" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($responsaveis as $r): ?>
                                    <option value="<?= (int)$r['id_usuarios'] ?>" <?= ((int)($formData['id_responsavel_coteli'] ?? 0) === (int)$r['id_usuarios']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nome_completo'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label d-block">Lançado no site da UERJ?</label>
                            <div class="d-flex gap-3 align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="lancado_site_uerj" id="lancadoSim" value="1" <?= !empty($formData['lancado_site_uerj']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="lancadoSim">Sim</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="lancado_site_uerj" id="lancadoNao" value="0" <?= empty($formData['lancado_site_uerj']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="lancadoNao">Não</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <a href="<?= url('pregoes') ?>" class="btn btn-neutro">Cancelar</a>
                    <button class="btn btn-primario ms-2" type="submit">Salvar repetição</button>
                </div>
            </form>
        </div>
    </div>
</div>                    

<?php if (!empty($ultimasReps)): ?>
    <div class="recent-table-card">
        <div class="recent-table-title">
            <i class="bi bi-calendar-week"></i>
            <span>Últimas 5 repetições cadastradas</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Pregão</th>
                        <th>Origem</th>
                        <th>Data</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimasReps as $p): ?>
                        <?php
                            $repLabel = 'R-' . str_pad((string)($p['id_pregao_repeticao'] ?? ''), 2, '0', STR_PAD_LEFT);
                            $pregaoLabel = trim(($p['sigla_tipos_pregao'] ?? '') . ' ' . str_pad((string)($p['id_pregao'] ?? ''), 3, '0', STR_PAD_LEFT) . '/' . ($p['ano_pregao'] ?? '') . ' (' . $repLabel . ')');
                            $dataHora = '-';
                            if (!empty($p['data_pregao'])) {
                                $dataFormat = date('d/m/Y', strtotime($p['data_pregao']));
                                $horaFormat = !empty($p['hora_pregao']) ? date('H:i', strtotime($p['hora_pregao'])) : '00:00';
                                $dataHora = $dataFormat . ' ' . $horaFormat;
                            }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($pregaoLabel) ?></td>
                            <td><?= htmlspecialchars((string)($p['unidade_origem'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($dataHora) ?></td>
                            <td><?= htmlspecialchars((string)($p['nome_status_pregao'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectBase = document.querySelector('select[name="id_base_pregao_r0"]');
        const campoTipo = document.getElementById('campoTipoPregao');
        const campoAno = document.getElementById('campoAnoPregao');
        const campoNumero = document.getElementById('campoNumeroPregao');
        const campoProcesso = document.getElementById('campoProcessoSei');
        const campoRep = document.getElementById('campoRepeticao');
        const campoData = document.getElementById('campoDataPregao');
        const campoHora = document.getElementById('campoHoraPregao');
        const campoStatus = document.getElementById('campoStatusPregao');
        const campoOrigem = document.getElementById('campoOrigemPregao');
        const campoObjeto = document.getElementById('campoObjeto');
        const campoPregoeiro = document.getElementById('campoPregoeiro');
        const campoResp = document.getElementById('campoResponsavel');
        const hiddenTipo = document.getElementById('hiddenTipoPregao');
        const hiddenAno = document.getElementById('hiddenAnoPregao');
        const hiddenNumero = document.getElementById('hiddenNumeroPregao');
        const hiddenOrigem = document.getElementById('hiddenOrigemPregao');
        const hiddenProcesso = document.getElementById('hiddenProcessoSei');
        const hiddenObjeto = document.getElementById('hiddenObjeto');
        const campoLancadoSim = document.getElementById('lancadoSim');
        const campoLancadoNao = document.getElementById('lancadoNao');
        const campoDataDo = document.querySelector('input[name="data_do"]');
        const formRepeticao = document.querySelector('form[action*="pregoes/salvar-repeticao"]');
        const dupUrl = formRepeticao?.dataset?.dupUrl || '';
        const fetchPregaoUrl = formRepeticao?.dataset?.fetchPregaoUrl || '';
        const campoEditId = document.getElementById('campoEditId');
        const lockBase = (formRepeticao?.dataset?.lockBase || '0') === '1';
        const lockRepCampo = (formRepeticao?.dataset?.lockRep || '0') === '1';

        const limparBackdrop = () => {
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
        };

        const fecharModalInfo = () => {
            const modalEl = document.getElementById('modalInfo');
            const modalInstance = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
            if (modalInstance) {
                modalInstance.hide();
                setTimeout(limparBackdrop, 150);
            } else {
                limparBackdrop();
            }
        };

        const travarSelect = (selectEl, value) => {
            if (!selectEl) return;
            if (value !== undefined && value !== null) {
                selectEl.value = String(value);
            }
            selectEl.classList.add('input-locked');
            selectEl.style.pointerEvents = 'none';
            selectEl.tabIndex = -1;
            selectEl.setAttribute('aria-readonly', 'true');
        };

        const travarInput = (el, valor) => {
            if (!el) return;
            if (valor !== undefined && valor !== null) {
                el.value = valor;
            }
            el.readOnly = true;
            el.classList.add('input-locked');
            el.tabIndex = -1;
            el.setAttribute('aria-readonly', 'true');
            el.setAttribute('tabindex', '-1');
            el.style.pointerEvents = 'none';
        };

        const aplicarValorBloqueado = (el, valor, hiddenEl) => {
            if (!el) return;
            const eraDisabled = el.disabled;
            if (eraDisabled) el.disabled = false;
            el.value = valor;
            if (hiddenEl) hiddenEl.value = valor;
            el.classList.add('input-locked');
            el.style.pointerEvents = 'none';
            el.disabled = eraDisabled;
        };

        const calcularProximaRepeticao = (tipo, ano, numero) => {
            if (!selectBase) return null;
            let maxRep = 0;
            Array.from(selectBase.options || []).forEach((op) => {
                const t = op.dataset.idtipo || '';
                const a = op.dataset.ano || '';
                const n = op.dataset.numero || '';
                if (String(t) === String(tipo) && String(a) === String(ano) && String(n) === String(numero)) {
                    const rep = parseInt(op.dataset.rep || '0', 10);
                    if (rep > maxRep) maxRep = rep;
                }
            });
            return maxRep + 1;
        };

        const limparCamposEditaveis = () => {
            if (campoData) campoData.value = '';
            if (campoHora) campoHora.value = '10:00';
            if (campoStatus) campoStatus.value = '';
            if (campoPregoeiro) campoPregoeiro.value = '';
            if (campoResp) campoResp.value = '';
            if (campoLancadoSim) campoLancadoSim.checked = false;
            if (campoLancadoNao) campoLancadoNao.checked = false;
            if (campoDataDo) campoDataDo.value = '';
            if (campoEditId) campoEditId.value = '';
        };

        const preencherCamposBase = (dados) => {
            console.debug('[repeticao] preenchendo campos base', dados);
            aplicarValorBloqueado(campoTipo, dados.id_tipo_pregao || '', hiddenTipo);
            aplicarValorBloqueado(campoAno, dados.ano_pregao || '', hiddenAno);
            aplicarValorBloqueado(campoNumero, dados.id_pregao || '', hiddenNumero);
            aplicarValorBloqueado(campoProcesso, dados.processo_sei || '', hiddenProcesso);
            aplicarValorBloqueado(campoObjeto, dados.objeto_licitado || '', hiddenObjeto);
            aplicarValorBloqueado(campoOrigem, dados.id_origem_pedido || '', hiddenOrigem);
            if (campoRep) {
                const repBase = parseInt(dados.id_pregao_repeticao || '0', 10);
                const prox = calcularProximaRepeticao(dados.id_tipo_pregao, dados.ano_pregao, dados.id_pregao);
                const sugestao = prox && prox > 0 ? prox : repBase + 1;
                if (!lockRepCampo) {
                    campoRep.value = sugestao > 0 ? sugestao.toString().padStart(2, '0') : '';
                } else {
                    aplicarValorBloqueado(campoRep, sugestao > 0 ? sugestao.toString().padStart(2, '0') : '', null);
                }
                console.debug('[repeticao] sugestao repeticao', { repBase, sugestao });
            }
        };

        const aplicarInfo = async (opt) => {
            if (!opt) return;

            limparCamposEditaveis();

            const dadosAttr = {
                id_tipo_pregao: opt.dataset.idtipo || '',
                ano_pregao: opt.dataset.ano || '',
                id_pregao: opt.dataset.numero || '',
                id_pregao_repeticao: parseInt(opt.dataset.rep || '0', 10),
                id_origem_pedido: opt.dataset.origem || '',
                processo_sei: opt.dataset.sei || '',
                objeto_licitado: opt.dataset.objeto || '',
            };

            // preenche imediatamente com data attributes
            preencherCamposBase(dadosAttr);

            const usarFetch = fetchPregaoUrl && opt.value;
            if (usarFetch) {
                try {
                    const url = new URL(fetchPregaoUrl, window.location.origin);
                    url.searchParams.set('id', opt.value);
                    const resp = await fetch(url.toString(), { method: 'GET' });
                    const data = await resp.json();
                    if (resp.ok && data && data.status === 'ok' && data.pregao) {
                        console.debug('[repeticao] dados fetch pregao', data.pregao);
                        preencherCamposBase(data.pregao);
                    }
                } catch (e) {
                    console.warn('[repeticao] falha ao buscar pregao completo', e);
                }
            }
        };

        
        const abrirModalDuplicado = (proxNum, existenteId) => {
            const proxFmt = String(proxNum).padStart(2, '0');
            const body = `
                <p>J&aacute; existe uma repeti&ccedil;&atilde;o com este n&uacute;mero.</p>
                <p>Escolha uma op&ccedil;&atilde;o:</p>
                <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-primario" id="btnUsarProximaRep">Usar pr&oacute;xima repeti&ccedil;&atilde;o (R-${proxFmt})</button>
                    <button type="button" class="btn btn-neutro" id="btnEditarRepExistente">Editar repeti&ccedil;&atilde;o existente</button>
                </div>
            `;
            window.coteliModal('Repeti&ccedil;&atilde;o duplicada', body);
            setTimeout(() => {
                const btnProx = document.getElementById('btnUsarProximaRep');
                const btnEdit = document.getElementById('btnEditarRepExistente');
                const modalEl = document.getElementById('modalInfo');
                const modalInstance = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
                if (btnProx) {
                    btnProx.onclick = () => {
                        if (campoRep) campoRep.value = proxFmt;
                        modalInstance && modalInstance.hide();
                        fecharModalInfo();
                    };
                }
                if (btnEdit && existenteId && fetchPregaoUrl) {
                    btnEdit.onclick = async () => {
                        try {
                            const url = new URL(fetchPregaoUrl, window.location.origin);
                            url.searchParams.set('id', existenteId);
                            const resp = await fetch(url.toString(), { method: 'GET' });
                            const data = await resp.json();
                            if (resp.ok && data && data.status === 'ok' && data.pregao) {
                                aplicarEdicao(data.pregao);
                                modalInstance && modalInstance.hide();
                                fecharModalInfo();
                            }
                        } catch (e) {
                            modalInstance && modalInstance.hide();
                            fecharModalInfo();
                        }
                    };
                }
            }, 50);
        };

        const aplicarEdicao = (pregao) => {
            if (!pregao) return;
            if (campoEditId) campoEditId.value = pregao.id_base_pregoes || '';
            travarSelect(campoTipo, pregao.id_tipo_pregao || '');
            aplicarValorBloqueado(campoTipo, pregao.id_tipo_pregao || '', hiddenTipo);
            aplicarValorBloqueado(campoAno, pregao.ano_pregao || '', hiddenAno);
            aplicarValorBloqueado(campoNumero, pregao.id_pregao || '', hiddenNumero);
            if (campoRep) {
                campoRep.value = String(pregao.id_pregao_repeticao || '').padStart(2, '0');
            }
            if (campoData) campoData.value = pregao.data_pregao || '';
            if (campoHora) campoHora.value = pregao.hora_pregao || '10:00';
            if (campoStatus) campoStatus.value = pregao.id_status || '';
            aplicarValorBloqueado(campoOrigem, pregao.id_origem_pedido || '', hiddenOrigem);
            aplicarValorBloqueado(campoProcesso, pregao.processo_sei || '', hiddenProcesso);
            aplicarValorBloqueado(campoObjeto, pregao.objeto_licitado || '', hiddenObjeto);
            if (campoPregoeiro) campoPregoeiro.value = pregao.id_pregoeiro || '';
            if (campoResp) campoResp.value = pregao.id_responsavel_coteli || '';
            if (campoLancadoSim && campoLancadoNao) {
                if (String(pregao.lancado_site_uerj || '0') === '1') {
                    campoLancadoSim.checked = true;
                } else {
                    campoLancadoNao.checked = true;
                }
            }
            if (campoDataDo) campoDataDo.value = pregao.data_do || '';
        };

        const checarDuplicidadeRep = async () => {
            if (!dupUrl || !campoTipo || !campoAno || !campoNumero || !campoRep) return;
            const id_tipo_pregao = campoTipo.value || '';
            const ano_pregao = campoAno.value || '';
            const id_pregao = (campoNumero.value || '').padStart(3, '0');
            const id_pregao_repeticao = (campoRep.value || '').replace(/\D/g, '');
            if (!id_tipo_pregao || !ano_pregao || id_pregao.length !== 3 || id_pregao_repeticao === '') return;
            try {
                const url = new URL(dupUrl, window.location.origin);
                url.searchParams.set('id_tipo_pregao', id_tipo_pregao);
                url.searchParams.set('ano_pregao', ano_pregao);
                url.searchParams.set('id_pregao', id_pregao);
                url.searchParams.set('id_pregao_repeticao', id_pregao_repeticao);
                const resp = await fetch(url.toString(), { method: 'GET' });
                const data = await resp.json();
                if (resp.ok && data && data.status === 'duplicado_repeticao') {
                    const prox = data.pregao?.proxima_repeticao_num ?? (parseInt(id_pregao_repeticao, 10) + 1);
                    const idExistente = data.pregao?.id_base_pregoes ?? null;
                    abrirModalDuplicado(prox, idExistente);
                }
            } catch (e) {
                // ignora
            }
        };

        if (selectBase) {
            selectBase.addEventListener('change', () => {
                const opt = selectBase.selectedOptions[0];
                aplicarInfo(opt);
            });
            // aplica dados se já vier pré-selecionado
            const optInicial = selectBase.selectedOptions[0];
            if (optInicial && optInicial.value) {
                aplicarInfo(optInicial);
            }
            if (lockBase) {
                selectBase.disabled = true;
                selectBase.classList.add('input-locked');
                selectBase.style.pointerEvents = 'none';
                selectBase.tabIndex = -1;
            }
        }

        if (campoRep) {
            ['blur', 'change', 'input'].forEach((ev) => {
                campoRep.addEventListener(ev, checarDuplicidadeRep);
            });
            if (lockRepCampo) {
                travarInput(campoRep, campoRep.value);
                campoRep.style.pointerEvents = 'none';
            }
        }

        const sucessoCadastro = <?= !empty($successMessage) ? 'true' : 'false' ?>;
        const sucessoDescricao = <?= json_encode($successPregao ?? '') ?>;
        const sucessoProxRep = <?= json_encode($successProximaRep ?? null) ?>;
        
        if (sucessoCadastro) {
            const proxFmt = sucessoProxRep ? String(sucessoProxRep).padStart(2, '0') : '';
            const corpo = `
                <p class="mb-2">Repeti&ccedil;&atilde;o salva com sucesso.</p>
                ${sucessoDescricao ? `<div class="alert alert-light border mb-3"><strong>Preg&atilde;o:</strong> ${sucessoDescricao}</div>` : ''}
                ${proxFmt ? `<p class="mb-3">Pr&oacute;xima repeti&ccedil;&atilde;o sugerida: <strong>R-${proxFmt}</strong>.</p>` : ''}
                <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-primario" id="btnNovaRepeticaoSucesso">Cadastrar nova repeti&ccedil;&atilde;o</button>
                    <button type="button" class="btn btn-neutro" id="btnCancelarSucesso">Cancelar</button>
                </div>
            `;
            window.coteliModal('Repeti&ccedil;&atilde;o salva', corpo);
            setTimeout(() => {
                const modalEl = document.getElementById('modalInfo');
                const instance = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
                const btnNova = document.getElementById('btnNovaRepeticaoSucesso');
                const btnCancelar = document.getElementById('btnCancelarSucesso');
                if (btnNova) {
                    btnNova.onclick = () => {
                        if (instance) instance.hide();
                        fecharModalInfo();
                        campoRep && campoRep.focus();
                    };
                }
                if (btnCancelar) {
                    btnCancelar.onclick = () => {
                        window.location.href = '<?= url('') ?>';
                        fecharModalInfo();
                    };
                }
            }, 50);
        }
    });
</script>
=======
/** @var array $basesR0 */
/** @var array $formData */
/** @var string|null $errorMessage */
?>
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h1 class="h4 mb-0">Nova repetição (R-X)</h1>
                <small class="text-muted">Selecione um pregão base (R-0) e cadastre a repetição.</small>
            </div>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?= url('pregoes/salvar-repeticao') ?>" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Pregão base (R-0)</label>
                        <select name="id_base_pregao_r0" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($basesR0 as $base): ?>
                                <?php
                                    $labelBase = sprintf(
                                        '%s - %s/%s (R-0)',
                                        $base['sigla_tipos_pregao'] ?? $base['id_tipo_pregao'] ?? '',
                                        $base['id_pregao'] ?? '',
                                        $base['ano_pregao'] ?? ''
                                    );
                                ?>
                                <option value="<?= (int)$base['id_base_pregoes'] ?>" <?= ((int)($formData['id_base_pregao_r0'] ?? 0) === (int)$base['id_base_pregoes']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($labelBase) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Repetição (R-X)</label>
                        <input
                            type="number"
                            name="id_pregao_repeticao"
                            class="form-control"
                            value="<?= htmlspecialchars((string)($formData['id_pregao_repeticao'] ?? 1), ENT_QUOTES, 'UTF-8') ?>"
                            min="1"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Processo SEI</label>
                        <input
                            type="text"
                            name="processo_sei"
                            class="form-control"
                            placeholder="SEI-000000/000000/AAAA"
                            pattern="^SEI-[0-9]{6}/[0-9]{6}/[0-9]{4}$"
                            maxlength="22"
                            value="<?= htmlspecialchars((string)($formData['processo_sei'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <?php $statusSelecionado = isset($formData['id_status']) ? (int)$formData['id_status'] : 1; ?>
                        <select name="id_status" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($statusPregao as $status): ?>
                                <?php $idStatus = (int)$status['id_status_pregao']; ?>
                                <option value="<?= $idStatus ?>" <?= ($statusSelecionado === $idStatus) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($status['nome_status_pregao'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Origem do processo</label>
                        <select name="id_origem_pedido" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($origensPedido as $origem): ?>
                                <option value="<?= (int)$origem['id_origens_pedido'] ?>" <?= ((int)($formData['id_origem_pedido'] ?? 0) === (int)$origem['id_origens_pedido']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($origem['unidade_origem'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data do pregão</label>
                        <input type="date" name="data_pregao" class="form-control" value="<?= htmlspecialchars((string)($formData['data_pregao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Hora do pregão</label>
                        <?php
                            $horaSelecionada = !empty($formData['hora_pregao'])
                                ? $formData['hora_pregao']
                                : '10:00';
                        ?>
                        <select name="hora_pregao" class="form-select" required>
                            <?php foreach (['09:00','10:00','11:00','13:00','14:00','15:00'] as $hora): ?>
                                <option value="<?= $hora ?>" <?= ($horaSelecionada === $hora) ? 'selected' : '' ?>><?= $hora ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pregoeiro</label>
                        <select name="id_pregoeiro" class="form-select">
                            <option value="">Selecione...</option>
                            <?php foreach ($pregoeiros as $p): ?>
                                <option value="<?= (int)$p['id_usuarios'] ?>" <?= ((int)($formData['id_pregoeiro'] ?? 0) === (int)$p['id_usuarios']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nome_completo'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Responsável na COTELI</label>
                        <select name="id_responsavel_coteli" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($responsaveis as $r): ?>
                                <option value="<?= (int)$r['id_usuarios'] ?>" <?= ((int)($formData['id_responsavel_coteli'] ?? 0) === (int)$r['id_usuarios']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['nome_completo'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block">Lançado no site da UERJ?</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="lancado_site_uerj" id="lancadoSim" value="1" <?= !empty($formData['lancado_site_uerj']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="lancadoSim">Sim</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="lancado_site_uerj" id="lancadoNao" value="0" <?= empty($formData['lancado_site_uerj']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="lancadoNao">Não</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <a href="<?= url('pregoes') ?>" class="btn btn-neutro">Cancelar</a>
                        <button class="btn btn-sucesso ms-2" type="submit">Salvar repetição</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
>>>>>>> 99e3d7fbbbc5fdcfa5e4bd8d2744761b3c0623b7
