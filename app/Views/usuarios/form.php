<?php /** @var array $formData */ ?>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="d-flex align-items-center mb-3">
            <div>
                <h1 class="h4 mb-0"><?= isset($formData['id_usuarios']) ? 'Editar pré-cadastro' : 'Novo pré-cadastro' ?></h1>
                <small class="text-muted">Cadastro de pessoas que podem ser pregoeiros e/ou responsáveis COTELI.</small>
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

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?= url('usuarios/salvar') ?>" class="row g-3">
                    <?php if (!empty($formData['id_usuarios'])): ?>
                        <input type="hidden" name="id_usuarios" value="<?= (int)$formData['id_usuarios'] ?>">
                    <?php endif; ?>

                    <div class="col-12">
                        <label class="form-label">Nome completo</label>
                        <input type="text" name="nome_completo" class="form-control" value="<?= htmlspecialchars((string)($formData['nome_completo'] ?? '')) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars((string)($formData['email'] ?? '')) ?>" required>
                    </div>

                    <div class="col-md-3 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_pregoeiro" id="isPregoeiro" value="1" <?= !empty($formData['is_pregoeiro']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isPregoeiro">Pregoeiro</label>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_responsavel_coteli" id="isResp" value="1" <?= !empty($formData['is_responsavel_coteli']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isResp">Resp. COTELI</label>
                        </div>
                    </div>

                    <div class="col-md-3 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" <?= (isset($formData['ativo']) ? (int)$formData['ativo'] === 1 : true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="ativo">Ativo</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-secondary small mb-2">
                            Se marcar "Responsável COTELI", a flag de "Pregoeiro" será mantida automaticamente.
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <a href="<?= url('usuarios') ?>" class="btn btn-outline-secondary">Cancelar</a>
                        <button class="btn btn-success ms-2">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
