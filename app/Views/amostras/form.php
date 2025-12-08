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

                    <div class="col-md-6">
                        <label class="form-label">Pregão (R-0)</label>
                        <select name="id_base_pregao" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($pregoesR0 as $p): ?>
                                <?php
                                    $label = ($p['sigla_tipos_pregao'] ?? '') . ' ' . ($p['id_pregao'] ?? '') . '/' . ($p['ano_pregao'] ?? '') . ' (R-0)';
                                ?>
                                <option value="<?= (int)$p['id_base_pregoes'] ?>" <?= ((int)($formData['id_base_pregao'] ?? 0) === (int)$p['id_base_pregoes']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Item licitado (3 dígitos)</label>
                        <input type="text" name="item_licitado" maxlength="3" pattern="\\d{3}" class="form-control" value="<?= htmlspecialchars((string)($formData['item_licitado'] ?? '')) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Total unidades</label>
                        <input type="number" step="0.01" name="total_unidades" class="form-control" value="<?= htmlspecialchars((string)($formData['total_unidades'] ?? '' )) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipo do item</label>
                        <select name="id_tipo_item" class="form-select">
                            <option value="">Selecione...</option>
                            <?php foreach ($tiposItem as $t): ?>
                                <option value="<?= (int)$t['id_tipos_licitados'] ?>" <?= ((int)($formData['id_tipo_item'] ?? 0) === (int)$t['id_tipos_licitados']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['nome_tipos_licitados'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Responsável</label>
                        <select name="id_responsavel" class="form-select">
                            <option value="">Selecione...</option>
                            <?php foreach ($responsaveis as $r): ?>
                                <option value="<?= (int)$r['id_usuarios'] ?>" <?= ((int)($formData['id_responsavel'] ?? 0) === (int)$r['id_usuarios']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['nome_completo'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Empresa</label>
                        <select name="id_empresa" class="form-select">
                            <option value="">Selecione...</option>
                            <?php foreach ($empresas as $e): ?>
                                <option value="<?= (int)$e['id'] ?>" <?= ((int)($formData['id_empresa'] ?? 0) === (int)$e['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($e['nome'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipo de parecer</label>
                        <select name="id_tipo_parecer" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($tiposParecer as $tp): ?>
                                <option value="<?= (int)$tp['id_tipos_parecer'] ?>" <?= ((int)($formData['id_tipo_parecer'] ?? 0) === (int)$tp['id_tipos_parecer']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tp['nome_tipos_parecer'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Chave única: pregão + item + parecer.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data do parecer</label>
                        <input type="date" name="data_parecer" class="form-control" value="<?= htmlspecialchars((string)($formData['data_parecer'] ?? '')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">Entregue na COTELI?</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="entregue_coteli" value="1" <?= !empty($formData['entregue_coteli']) ? 'checked' : '' ?>>
                            <label class="form-check-label">Sim</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Chegada na COTELI</label>
                        <input type="datetime-local" name="chegada_coteli" class="form-control" value="<?= htmlspecialchars((string)($formData['chegada_coteli'] ?? '')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Saída da COTELI</label>
                        <input type="datetime-local" name="saida_coteli" class="form-control" value="<?= htmlspecialchars((string)($formData['saida_coteli'] ?? '')) ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-control" rows="2"><?= htmlspecialchars((string)($formData['observacoes'] ?? '')) ?></textarea>
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
