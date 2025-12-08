<?php
/** @var array $tiposPregao */
/** @var array $origensPedido */
/** @var array $statusPregao */
/** @var array $pregoeiros */
/** @var array $responsaveis */
/** @var array $formData */
/** @var array $ultimosR0 */
/** @var string|null $errorMessage */
?>
<div class="form-shell">
    <div class="form-header">
        <div>
            <p class="eyebrow mb-1">Pregões</p>
            <h1 class="h4 mb-2">Cadastrar pregão base</h1>
            <p class="text-muted mb-3">Preencha os dados do pregão R-0 com o novo visual inspirado no painel Swift.</p>
            <div class="form-summary">
                <?php $numeroBase = str_pad((string)($formData['id_pregao'] ?? ''), 3, '0', STR_PAD_LEFT); ?>
                <div class="summary-card">
                    <strong><?= $numeroBase !== '000' ? htmlspecialchars($numeroBase) : 'R-0' ?></strong>
                    <span>Número de referência</span>
                </div>
                <div class="summary-card">
                    <strong><?= htmlspecialchars((string)($formData['data_pregao'] ?? 'Agende a data')) ?></strong>
                    <span>Data prevista do pregão</span>
                </div>
                <div class="summary-card">
                    <strong><?= htmlspecialchars((string)($formData['ano_pregao'] ?? date('Y'))) ?></strong>
                    <span>Ano do exercício</span>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column gap-2 align-items-end">
            <span class="pill">Fluxo R-0</span>
            <a href="<?= url('pregoes') ?>" class="btn btn-neutro btn-compact"><i class="bi bi-arrow-left"></i> Voltar</a>
        </div>
    </div>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger mb-0"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form
                method="post"
                action="<?= url('pregoes/salvar-base') ?>"
                class="row g-4"
                data-dup-url="<?= url('pregoes/verificar-chave') ?>"
                data-nova-rep-url="<?= url('pregoes/nova-repeticao') ?>"
            >
                <div class="col-12">
                    <div class="fieldset-title"><i class="bi bi-layout-text-window-reverse"></i> Identificação do pregão</div>
                    <div class="input-grid">
                        <div>
                            <label class="form-label">Tipo de pregão</label>
                            <select name="id_tipo_pregao" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($tiposPregao as $tipo): ?>
                                    <option value="<?= (int)$tipo['id_tipos_pregao'] ?>" <?= ((int)($formData['id_tipo_pregao'] ?? 0) === (int)$tipo['id_tipos_pregao']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo['sigla_tipos_pregao'] ?? '') ?> - <?= htmlspecialchars($tipo['nome_tipos_pregao'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Ano</label>
                            <input type="number" name="ano_pregao" class="form-control" value="<?= htmlspecialchars((string)($formData['ano_pregao'] ?? date('Y')), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Número do pregão</label>
                            <input
                                type="text"
                                name="id_pregao"
                                class="form-control"
                                maxlength="3"
                                pattern="^[0-9]{3}$"
                                placeholder="000"
                                value="<?= htmlspecialchars((string)($formData['id_pregao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                required>
                        </div>
                        <div>
                            <label class="form-label">Data do pregão</label>
                            <input type="date" name="data_pregao" class="form-control" value="<?= htmlspecialchars((string)($formData['data_pregao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div>
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
                    </div>
                </div>

                <div class="col-12">
                    <div class="fieldset-title"><i class="bi bi-diagram-3"></i> Situação e processo</div>
                    <div class="input-grid">
                        <div>
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
                        <div>
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
                        <div class="stretch">
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
                    </div>
                </div>

                <div class="col-12">
                    <div class="fieldset-title"><i class="bi bi-journal-text"></i> Objeto licitado</div>
                    <textarea name="objeto_licitado" class="form-control" rows="3" required><?= htmlspecialchars((string)($formData['objeto_licitado'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="col-12">
                    <div class="fieldset-title"><i class="bi bi-calendar2-event"></i> Publicação e equipe</div>
                    <div class="input-grid">
                        <div>
                            <label class="form-label">Publicação no D.O.</label>
                            <input type="date" name="data_do" class="form-control" required value="<?= htmlspecialchars((string)($formData['data_do'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div>
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
                        <div>
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
                    <button class="btn btn-primario ms-2" type="submit">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($ultimosR0)): ?>
        <div class="card">
            <div class="card-header">Últimos 5 pregões cadastrados</div>
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
                        <?php foreach ($ultimosR0 as $p): ?>
                            <?php
                                $pregaoLabel = trim(($p['sigla_tipos_pregao'] ?? '') . ' ' . ($p['id_pregao'] ?? '') . '/' . ($p['ano_pregao'] ?? ''));
                                $dataHora = '-';
                                if (!empty($p['data_pregao'])) {
                                    $dataFormat = date('d/m/Y', strtotime($p['data_pregao']));
                                    $horaFormat = !empty($p['hora_pregao']) ? $p['hora_pregao'] : '00:00';
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
</div>
