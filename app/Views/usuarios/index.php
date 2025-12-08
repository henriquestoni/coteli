<?php /** @var array $usuarios */ ?>
<div class="d-flex align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Usuários / Pessoas</h1>
        <small class="text-muted">Pré-cadastre pessoas e gere acesso quando necessário.</small>
    </div>
    <div class="ms-auto">
        <a href="<?= url('usuarios/novo') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Novo pré-cadastro</a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Login</th>
                    <th>Nível</th>
                    <th>Pregoeiro</th>
                    <th>Resp. COTELI</th>
                    <th>Ativo</th>
                    <th>Acesso</th>
                    <th class="text-nowrap">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Nenhum registro encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= (int)$u['id_usuarios'] ?></td>
                            <td><?= htmlspecialchars((string)$u['nome_completo']) ?></td>
                            <td><?= htmlspecialchars((string)$u['email']) ?></td>
                            <td><?= htmlspecialchars((string)($u['login'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($u['nivel_acesso'] ?? '')) ?></td>
                            <td><?= !empty($u['is_pregoeiro']) ? 'Sim' : 'Não' ?></td>
                            <td><?= !empty($u['is_responsavel_coteli']) ? 'Sim' : 'Não' ?></td>
                            <td><?= !empty($u['ativo']) ? 'Sim' : 'Não' ?></td>
                            <td><?= !empty($u['senha_hash']) ? 'Com acesso' : 'Pré-cadastro' ?></td>
                            <td class="text-nowrap">
                                <a href="<?= url('usuarios/editar?id=' . (int)$u['id_usuarios']) ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <?php if (empty($u['senha_hash']) || empty($u['login'])): ?>
                                    <a href="<?= url('usuarios/gerar-acesso?id=' . (int)$u['id_usuarios']) ?>" class="btn btn-sm btn-success ms-1">Gerar acesso</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
