<?php use App\Core\Auth; ?>
<div class="d-flex align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Trilha de Auditoria</h1>
        <small class="text-muted">Leituras e alterações em entidades sensíveis.</small>
    </div>
</div>

<div class="alert alert-info">
    Apenas níveis &ge; 4 visualizam esta tela. Leituras sensíveis também são registradas.
</div>

<form class="row g-2 mb-3">
    <div class="col-md-3">
        <label class="form-label mb-0">Usuário</label>
        <input type="text" class="form-control form-control-sm" placeholder="ID ou nome (filtro futuro)">
    </div>
    <div class="col-md-3">
        <label class="form-label mb-0">Entidade</label>
        <input type="text" class="form-control form-control-sm" placeholder="pregoes, amostras, ...">
    </div>
    <div class="col-md-3">
        <label class="form-label mb-0">Ação</label>
        <input type="text" class="form-control form-control-sm" placeholder="LOGIN, EDITAR_AMOSTRA...">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-sm btn-primary">Filtrar (placeholder)</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-sm table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Ação</th>
                <th>Entidade</th>
                <th>ID Ent.</th>
                <th>IP</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['id']) ?></td>
                        <td><?= htmlspecialchars($log['nome_completo'] ?? $log['id_usuario'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($log['acao']) ?></td>
                        <td><?= htmlspecialchars($log['entidade']) ?></td>
                        <td><?= htmlspecialchars((string)$log['id_entidade']) ?></td>
                        <td><?= htmlspecialchars($log['ip'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($log['criado_em'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">Sem registros de auditoria (placeholder).</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
