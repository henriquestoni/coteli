<?php /** @var array|null $usuario */ ?>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h1 class="h4 mb-0">Gerar acesso</h1>
                <small class="text-muted">Gere login e senha provisória para um usuário.</small>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($resultado['senha_provisoria']) && !empty($resultado['usuario'])): ?>
            <div class="alert alert-success">
                <p class="mb-1">Acesso gerado com sucesso.</p>
                <div class="small">
                    Login: <strong><?= htmlspecialchars($resultado['usuario']['login']) ?></strong><br>
                    Senha provisória: <strong><?= htmlspecialchars($resultado['senha_provisoria']) ?></strong>
                </div>
                <p class="mb-0 mt-2 small text-muted">Os dados também foram registrados em storage/logs/convites_usuarios.log.</p>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?= url('usuarios/gerar-acesso' . (!empty($usuario['id_usuarios']) ? '?id=' . (int)$usuario['id_usuarios'] : '')) ?>" class="row g-3">
                    <?php if (!empty($usuario['id_usuarios'])): ?>
                        <input type="hidden" name="id_usuarios" value="<?= (int)$usuario['id_usuarios'] ?>">
                    <?php endif; ?>

                    <div class="col-12">
                        <label class="form-label">Nome completo</label>
                        <input type="text" name="nome_completo" class="form-control" value="<?= htmlspecialchars((string)($usuario['nome_completo'] ?? '')) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars((string)($usuario['email'] ?? '')) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Login sugerido</label>
                        <input type="text" name="login" class="form-control" value="<?= htmlspecialchars((string)($usuario['login'] ?? '')) ?>" placeholder="se vazio, usa parte do e-mail">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nível de acesso</label>
                        <select name="nivel_acesso" class="form-select" required>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= ((int)($usuario['nivel_acesso'] ?? 1) === $i) ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_pregoeiro" id="isPregoeiro" value="1" <?= !empty($usuario['is_pregoeiro']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isPregoeiro">Pregoeiro</label>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_responsavel_coteli" id="isResp" value="1" <?= !empty($usuario['is_responsavel_coteli']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isResp">Resp. COTELI</label>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <a href="<?= url('usuarios') ?>" class="btn btn-outline-secondary">Voltar</a>
                        <button class="btn btn-success ms-2">Gerar acesso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
