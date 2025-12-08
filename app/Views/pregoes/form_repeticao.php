<?php
/** @var array $bases */
/** @var array $formData */
/** @var array $tiposRepeticao */
/** @var string|null $errorMessage */
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

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger mb-0"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= url('pregoes/salvar-repeticao') ?>" class="row g-4">
                <div class="col-12">
                    <div class="fieldset-title"><i class="bi bi-list-check"></i> Escolha a base</div>
                    <div class="input-grid">
                        <div>
                            <label class="form-label">Pregão base</label>
                            <select name="id_base_pregao_r0" class="form-select" required>
                                <option value="">Selecione o pregão base (R-0)</option>
                                <?php foreach ($bases as $base): ?>
                                    <?php
                                        $value = (int)$base['id_base_pregoes'];
                                        $numero = trim(($base['sigla_tipos_pregao'] ?? '') . ' ' . str_pad((string)($base['id_pregao'] ?? ''), 3, '0', STR_PAD_LEFT) . '/' . ($base['ano_pregao'] ?? ''));
                                    ?>
                                    <option value="<?= $value ?>" <?= ((int)($formData['id_base_pregao_r0'] ?? 0) === $value) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($numero) ?> — <?= htmlspecialchars((string)($base['objeto_licitado'] ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Tipo da repetição</label>
                            <select name="id_tipo_repeticao" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($tiposRepeticao as $tipo): ?>
                                    <option value="<?= (int)$tipo['id_tipo_repeticao'] ?>" <?= ((int)($formData['id_tipo_repeticao'] ?? 0) === (int)$tipo['id_tipo_repeticao']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo['descricao_tipo_repeticao'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Número da repetição</label>
                            <input type="number" min="1" name="id_pregao_repeticao" class="form-control" value="<?= htmlspecialchars((string)($formData['id_pregao_repeticao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="fieldset-title"><i class="bi bi-calendar3"></i> Datas e processo</div>
                    <div class="input-grid">
                        <div>
                            <label class="form-label">Data do pregão</label>
                            <input type="date" name="data_pregao" class="form-control" value="<?= htmlspecialchars((string)($formData['data_pregao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Hora do pregão</label>
                            <?php
                                $horaSelecionada = !empty($formData['hora_pregao']) ? $formData['hora_pregao'] : '10:00';
                            ?>
                            <select name="hora_pregao" class="form-select" required>
                                <?php foreach (['09:00','10:00','11:00','13:00','14:00','15:00'] as $hora): ?>
                                    <option value="<?= $hora ?>" <?= ($horaSelecionada === $hora) ? 'selected' : '' ?>><?= $hora ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Processo SEI</label>
                            <input type="text" name="processo_sei" class="form-control" placeholder="SEI-000000/000000/AAAA" pattern="^SEI-[0-9]{6}/[0-9]{6}/[0-9]{4}$" maxlength="22" value="<?= htmlspecialchars((string)($formData['processo_sei'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div>
                            <label class="form-label">Publicação no D.O.</label>
                            <input type="date" name="data_do" class="form-control" value="<?= htmlspecialchars((string)($formData['data_do'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="fieldset-title"><i class="bi bi-journal-text"></i> Objeto licitado</div>
                    <textarea name="objeto_licitado" class="form-control" rows="3" required><?= htmlspecialchars((string)($formData['objeto_licitado'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="col-12 text-end">
                    <a href="<?= url('pregoes') ?>" class="btn btn-neutro">Cancelar</a>
                    <button class="btn btn-primario ms-2" type="submit">Salvar repetição</button>
                </div>
            </form>
        </div>
    </div>
</div>
