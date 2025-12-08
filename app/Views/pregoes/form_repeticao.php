<?php
/** @var array $tiposPregao */
/** @var array $origensPedido */
/** @var array $statusPregao */
/** @var array $pregoeiros */
/** @var array $responsaveis */
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
