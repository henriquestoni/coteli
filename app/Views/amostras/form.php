<?php /** @var array $formData */ ?>
<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h1 class="h4 mb-0">Nova amostra</h1>
                <small class="text-muted">Registre a amostra vinculada a um pregão.</small>
            </div>
        </div>

        <?php if (!empty($errorDuplicate)): ?>
            <div class="alert alert-warning">
                Já existe amostra para este pregão + item + parecer.
                <div class="small mt-2">
                    ID existente: <?= (int)($errorDuplicate['id_base_amostras'] ?? 0) ?> -
                    Item: <?= htmlspecialchars((string)($errorDuplicate['item_licitado'] ?? '')) ?> -
                    Parecer: <?= htmlspecialchars((string)($errorDuplicate['id_tipo_parecer'] ?? '')) ?>
                </div>
                <div class="small mt-2">Opções: alterar o parecer do registro existente ou cadastrar outra combinação.</div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?= url('amostras/salvar') ?>" class="row g-3">
                    <input type="hidden" name="id_momento_cadastro" value="<?= htmlspecialchars((string)($formData['id_momento_cadastro'] ?? '')) ?>">

                    <div class="col-12">
                        <label class="form-label">Selecione o Pregão:</label>
                        <select name="id_base_pregao" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($pregoesR0 as $p): ?>
                                <?php
                                    $sigla = trim((string)($p['sigla_tipos_pregao'] ?? ''));
                                    $num = str_pad((string)($p['id_pregao'] ?? ''), 3, '0', STR_PAD_LEFT);
                                    $ano = (string)($p['ano_pregao'] ?? '');
                                    $rep = (int)($p['id_pregao_repeticao'] ?? 0);
                                    $repLabel = 'R-' . str_pad((string)$rep, 2, '0', STR_PAD_LEFT);
                                    $processo = trim((string)($p['processo_sei'] ?? ''));
                                    $labelNumero = ($sigla !== '' ? $sigla . ' ' : '') . $num . '/' . $ano . ' (' . $repLabel . ')';
                                    $label = $labelNumero . ($processo !== '' ? ' - ' . $processo : '');
                                ?>
                                <option value="<?= (int)$p['id_base_pregoes'] ?>" <?= ((int)($formData['id_base_pregao'] ?? 0) === (int)$p['id_base_pregoes']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label">Tipo:</label>
                                <select name="id_tipo_item" class="form-select">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($tiposItem as $t): ?>
                                        <option value="<?= (int)$t['id_tipos_licitados'] ?>" <?= ((int)($formData['id_tipo_item'] ?? 0) === (int)$t['id_tipos_licitados']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($t['nome_tipos_licitados'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Identificação:</label>
                                <input type="text" name="item_licitado" maxlength="3" pattern="\d{3}" class="form-control" value="<?= htmlspecialchars((string)($formData['item_licitado'] ?? '')) ?>" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Unidades:</label>
                                <input type="number" step="0.01" name="total_unidades" class="form-control" value="<?= htmlspecialchars((string)($formData['total_unidades'] ?? '1')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Recebido na COTELI por:</label>
                                <select name="id_responsavel" class="form-select">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($responsaveis as $r): ?>
                                        <option value="<?= (int)$r['id_usuarios'] ?>" <?= ((int)($formData['id_responsavel'] ?? 0) === (int)$r['id_usuarios']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($r['nome_completo'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Empresa:</label>
                        <input list="listaEmpresas" id="inputEmpresaNome" class="form-control" placeholder="Digite para buscar ou cadastrar" value="<?= htmlspecialchars((string)($formData['empresa_nome'] ?? '')) ?>">
                        <datalist id="listaEmpresas">
                            <?php foreach ($empresas as $e): ?>
                                <option
                                    value="<?= htmlspecialchars((string)($e['nome'] ?? '')) ?>"
                                    data-id="<?= (int)$e['id'] ?>"
                                    data-email="<?= htmlspecialchars((string)($e['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-telefone="<?= htmlspecialchars((string)($e['telefone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-cnpj="<?= htmlspecialchars((string)($e['cnpj'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                ><?= htmlspecialchars((string)($e['nome'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </datalist>
                        <input type="hidden" name="id_empresa" id="idEmpresaHidden" value="<?= htmlspecialchars((string)($formData['id_empresa'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Observações:</label>
                        <textarea name="observacoes" class="form-control" rows="3"><?= htmlspecialchars((string)($formData['observacoes'] ?? '')) ?></textarea>
                    </div>

                    <div class="col-12">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label d-block">Entregue na COTELI?</label>
                                <?php $entregue = isset($formData['entregue_coteli']) ? (int)$formData['entregue_coteli'] : 1; ?>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="entregue_coteli" id="entregueSim" value="1" <?= $entregue === 1 ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="entregueSim">Sim</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="entregue_coteli" id="entregueNao" value="0" <?= $entregue === 0 ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="entregueNao">Não</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 js-bloco-datas">
                                <label class="form-label">Chegada na COTELI:</label>
                                <input type="date" name="chegada_coteli" id="campoChegadaCoteli" class="form-control" value="<?= htmlspecialchars((string)($formData['chegada_coteli'] ?? '')) ?>">
                            </div>
                            <div class="col-md-4 js-bloco-datas">
                                <label class="form-label">Saída da COTELI:</label>
                                <input type="date" name="saida_coteli" id="campoSaidaCoteli" class="form-control" value="<?= htmlspecialchars((string)($formData['saida_coteli'] ?? '')) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Status do Parecer:</label>
                                <select name="id_tipo_parecer" class="form-select" required>
                                    <option value="">[aguardando]</option>
                                    <?php foreach ($tiposParecer as $tp): ?>
                                        <option value="<?= (int)$tp['id_tipos_parecer'] ?>" <?= ((int)($formData['id_tipo_parecer'] ?? 0) === (int)$tp['id_tipos_parecer']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tp['nome_tipos_parecer'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Chave única: pregão + item + parecer.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data do Parecer:</label>
                                <input type="date" name="data_parecer" class="form-control" value="<?= htmlspecialchars((string)($formData['data_parecer'] ?? '')) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <a href="<?= url('amostras') ?>" class="btn btn-outline-secondary">Cancelar</a>
                        <button class="btn btn-success ms-2">Salvar amostra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputEmpresa = document.getElementById('inputEmpresaNome');
    const hiddenEmpresa = document.getElementById('idEmpresaHidden');
    const datalist = document.getElementById('listaEmpresas');
    const criarEmpresaUrl = '<?= url('amostras/criar-empresa') ?>';

    const acharOpcao = (texto) => {
        const opts = Array.from(datalist?.options || []);
        return opts.find((o) => (o.value || '').trim().toLowerCase() === (texto || '').trim().toLowerCase()) || null;
    };

        const abrirModalEmpresa = (nomeInicial) => {
            const body = `
                <div class="mb-2">
                    <label class="form-label">Empresa</label>
                    <input type="text" class="form-control" id="novaEmpresaNome" value="${nomeInicial || ''}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="novaEmpresaEmail" placeholder="email@empresa.com" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">CNPJ</label>
                    <input type="text" class="form-control" id="novaEmpresaCnpj" placeholder="00.000.000/0000-00" maxlength="18" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="novaEmpresaTelefone" placeholder="(xx) xxxxx-xxxx">
                </div>
                <div class="text-end">
                <button type="button" class="btn btn-secondary me-2" id="btnCancelarNovaEmpresa">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarNovaEmpresa">Cadastrar</button>
            </div>
        `;
        window.coteliModal('Cadastrar nova empresa', body);
        setTimeout(() => {
            const modalEl = document.getElementById('modalInfo');
            const instance = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
            const btnCanc = document.getElementById('btnCancelarNovaEmpresa');
            const btnConf = document.getElementById('btnConfirmarNovaEmpresa');
            const campoNome = document.getElementById('novaEmpresaNome');
            const campoEmail = document.getElementById('novaEmpresaEmail');
            const campoCnpj = document.getElementById('novaEmpresaCnpj');
            const campoTel = document.getElementById('novaEmpresaTelefone');
            if (campoCnpj) {
                campoCnpj.addEventListener('input', () => {
                    campoCnpj.value = aplicarMascaraCnpj(campoCnpj.value);
                });
            }
            if (campoNome) campoNome.focus();
            if (btnCanc) {
                btnCanc.onclick = () => instance && instance.hide();
            }
            if (btnConf) {
                btnConf.onclick = async () => {
                    const nome = (campoNome?.value || '').trim();
                    const email = (campoEmail?.value || '').trim();
                    const cnpj = (campoCnpj?.value || '').trim();
                    const telefone = (campoTel?.value || '').trim();
                    if (!nome || !email || !cnpj) return;
                    try {
                        const resp = await fetch(criarEmpresaUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ nome, email, telefone, cnpj }),
                        });
                        const data = await resp.json();
                        if (resp.ok && data && data.status === 'ok') {
                            const opt = document.createElement('option');
                            opt.value = data.nome;
                            opt.dataset.id = data.id;
                            opt.dataset.email = data.email || '';
                            opt.dataset.cnpj = data.cnpj || '';
                            opt.dataset.telefone = data.telefone || '';
                            datalist?.appendChild(opt);
                            inputEmpresa.value = data.nome;
                            hiddenEmpresa.value = data.id;
                            instance && instance.hide();
                        }
                    } catch (e) {
                        instance && instance.hide();
                    }
                };
            }
        }, 50);
    };

    const blocoDatas = document.querySelectorAll('.js-bloco-datas');
    const radioSim = document.getElementById('entregueSim');
    const radioNao = document.getElementById('entregueNao');
    const campoChegada = document.getElementById('campoChegadaCoteli');
    const campoSaida = document.getElementById('campoSaidaCoteli');

    const aplicarMascaraCnpj = (valor) => {
        const digits = (valor || '').replace(/\D/g, '').slice(0, 14);
        const partes = [
            digits.slice(0, 2),
            digits.slice(2, 5),
            digits.slice(5, 8),
            digits.slice(8, 12),
            digits.slice(12, 14),
        ];
        let out = '';
        if (partes[0]) out = partes[0];
        if (partes[1]) out += '.' + partes[1];
        if (partes[2]) out += '.' + partes[2];
        if (partes[3]) out += '/' + partes[3];
        if (partes[4]) out += '-' + partes[4];
        return out;
    };

    const atualizarDatas = () => {
        const entregue = radioSim?.checked;
        blocoDatas.forEach((el) => {
            if (!el) return;
            el.style.display = entregue ? '' : 'none';
            const inputs = el.querySelectorAll('input');
            inputs.forEach((inp) => {
                if (!entregue) {
                    inp.value = '';
                    inp.removeAttribute('required');
                } else if (inp === campoChegada) {
                    inp.setAttribute('required', 'required');
                }
            });
        });
    };

    if (inputEmpresa) {
        inputEmpresa.addEventListener('change', () => {
            const txt = inputEmpresa.value || '';
            const opt = acharOpcao(txt);
            if (opt) {
                hiddenEmpresa.value = opt.dataset.id || '';
            } else {
                hiddenEmpresa.value = '';
                if (txt.trim() !== '') {
                    abrirModalEmpresa(txt.trim());
                }
            }
        });
        inputEmpresa.addEventListener('input', () => {
            const txt = inputEmpresa.value || '';
            const opt = acharOpcao(txt);
            if (!opt) {
                hiddenEmpresa.value = '';
            }
        });
    }

    if (radioSim) {
        radioSim.addEventListener('change', atualizarDatas);
    }
    if (radioNao) {
        radioNao.addEventListener('change', atualizarDatas);
    }
    atualizarDatas();
});
</script>
