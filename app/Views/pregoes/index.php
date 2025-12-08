<?php use App\Core\Auth; $user = Auth::user(); ?>
<div class="d-flex align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Pregões</h1>
        <small class="text-muted">R-0 e repetições R-X.</small>
    </div>
    <div class="ms-auto">
        <?php if ($user && $user['nivel_acesso'] >= 4): ?>
            <a href="<?= url('pregoes/novo-base') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Novo pregão base (R-0)</a>
            <a href="<?= url('pregoes/nova-repeticao') ?>" class="btn btn-outline-primary ms-2"><i class="bi bi-arrow-repeat me-1"></i>Nova repetição (R-X)</a>
        <?php endif; ?>
    </div>
</div>

<div class="alert alert-light border mb-3">
    Listagem oficial dos pregões cadastrados no sistema.
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-2">
                <label class="form-label">Ano</label>
                <input type="number" class="form-control" placeholder="Ex.: 2025">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Tipo de pregão</label>
                <input type="text" class="form-control" placeholder="RP, SRP...">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Status</label>
                <input type="text" class="form-control" placeholder="Publicado, Rascunho...">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label">Busca</label>
                <input type="text" class="form-control" placeholder="Origem, objeto, processo SEI...">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Ano</th>
                    <th>Nº</th>
                    <th>Repetição</th>
                    <th>Data do pregão</th>
                    <th>Publicação D.O.</th>
                    <th>Origem</th>
                    <th>Objeto licitado</th>
                    <th>Status</th>
                    <th>Lançado no site?</th>
                    <th>Pregoeiro</th>
                    <th>Resp. COTELI</th>
                    <th class="text-nowrap">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pregoes)): ?>
                    <tr>
                        <td colspan="14" class="text-center text-muted py-4">Nenhum pregão encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pregoes as $pregao): ?>
                        <?php
                            $repLabel = 'R-' . (int)$pregao['id_pregao_repeticao'];
                            $dataPregao = !empty($pregao['data_pregao']) ? date('d/m/Y', strtotime($pregao['data_pregao'])) : '-';
                            $dataDo = !empty($pregao['data_do']) ? date('d/m/Y', strtotime($pregao['data_do'])) : '-';
                            $objeto = htmlspecialchars(mb_strimwidth((string)($pregao['objeto_licitado'] ?? ''), 0, 80, '...'), ENT_QUOTES, 'UTF-8');
                            $status = htmlspecialchars((string)($pregao['nome_status_pregao'] ?? ''), ENT_QUOTES, 'UTF-8');
                            $badgeClass = 'bg-secondary';
                            if (stripos($status, 'public') !== false) {
                                $badgeClass = 'bg-success';
                            } elseif (stripos($status, 'rascun') !== false) {
                                $badgeClass = 'bg-secondary';
                            } elseif (stripos($status, 'susp') !== false) {
                                $badgeClass = 'bg-warning text-dark';
                            }
                        ?>
                        <tr>
                            <td><?= (int)$pregao['id_base_pregoes'] ?></td>
                            <td><?= htmlspecialchars((string)($pregao['tipo_sigla'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)($pregao['ano_pregao'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)($pregao['id_pregao'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $repLabel ?></td>
                            <td><?= $dataPregao ?></td>
                            <td><?= $dataDo ?></td>
                            <td><?= htmlspecialchars((string)($pregao['unidade_origem'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $objeto ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $status ?></span></td>
                            <td><?= !empty($pregao['lancado_site_uerj']) ? 'Sim' : 'Não' ?></td>
                            <td><?= htmlspecialchars((string)($pregao['nome_pregoeiro'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)($pregao['nome_responsavel_coteli'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-nowrap">
                                <a href="<?= url('pregoes/detalhes/' . (int)$pregao['id_base_pregoes']) ?>" class="btn btn-sm btn-outline-secondary">Detalhes</a>
                                <a href="<?= url('pregoes/editar/' . (int)$pregao['id_base_pregoes']) ?>" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
