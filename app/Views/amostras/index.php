<?php /** @var array $pregoesR0 */ ?>
<div class="d-flex align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Amostras</h1>
        <small class="text-muted">Controle de entrada, parecer e impressão.</small>
    </div>
    <div class="ms-auto">
        <a href="<?= url('amostras/nova') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Nova amostra</a>
    </div>
</div>

<form method="get" class="card mb-3">
    <div class="card-body row g-3 align-items-end">
        <div class="col-md-6">
<<<<<<< HEAD
            <label class="form-label mb-1">Pregão (R-0 ou R-X)</label>
            <select name="id_base_pregao" class="form-select">
                <option value="">Selecione...</option>
                <?php foreach ($pregoesR0 as $p): ?>
                    <?php
                        $sigla = trim((string)($p['sigla_tipos_pregao'] ?? ''));
                        $num = str_pad((string)($p['id_pregao'] ?? ''), 3, '0', STR_PAD_LEFT);
                        $ano = (string)($p['ano_pregao'] ?? '');
                        $rep = (int)($p['id_pregao_repeticao'] ?? 0);
                        $repLabel = 'R-' . str_pad((string)$rep, 2, '0', STR_PAD_LEFT);
                        $labelNumero = ($sigla !== '' ? $sigla . ' ' : '') . $num . '/' . $ano . ' (' . $repLabel . ')';
                        $processo = trim((string)($p['processo_sei'] ?? ''));
                        $label = $labelNumero;
                        if ($processo !== '') {
                            $label .= ' - ' . $processo;
                        }
                    ?>
=======
            <label class="form-label mb-1">Pregão (R-0)</label>
            <select name="id_base_pregao" class="form-select">
                <option value="">Selecione...</option>
                <?php foreach ($pregoesR0 as $p): ?>
                    <?php $label = ($p['sigla_tipos_pregao'] ?? '') . ' ' . ($p['id_pregao'] ?? '') . '/' . ($p['ano_pregao'] ?? '') . ' (R-0)'; ?>
>>>>>>> 99e3d7fbbbc5fdcfa5e4bd8d2744761b3c0623b7
                    <option value="<?= (int)$p['id_base_pregoes'] ?>" <?= ((int)($pregaoSelecionado ?? 0) === (int)$p['id_base_pregoes']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-primary">Filtrar</button>
        </div>
    </div>
</form>

<div class="alert alert-warning">
    Regra de unicidade: pregão + item licitado + tipo de parecer. Em caso de conflito, mostrar opção de alterar o parecer existente ou cadastrar outro item.
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Item</th>
                    <th>Empresa</th>
                    <th>Parecer</th>
                    <th>Responsável</th>
                    <th>Cadastro</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($amostras)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Selecione um pregão para ver as amostras.</td></tr>
                <?php else: ?>
                    <?php foreach ($amostras as $a): ?>
                        <tr>
                            <td><?= (int)$a['id_base_amostras'] ?></td>
                            <td><?= htmlspecialchars((string)$a['item_licitado']) ?></td>
                            <td><?= htmlspecialchars((string)($a['nome_empresa'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($a['nome_tipos_parecer'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($a['nome_responsavel'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($a['data_cadastro'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
