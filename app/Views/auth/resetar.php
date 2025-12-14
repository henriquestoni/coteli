<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h5 text-center mb-3">Redefinir senha</h1>
                <p class="text-muted small text-center mb-3">Informe o código recebido no e-mail de <?= htmlspecialchars((string)($usuario['email'] ?? '')) ?> e defina uma nova senha.</p>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form method="post" action="<?= url('resetar-senha') ?>" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Código</label>
                        <input type="text" name="codigo" class="form-control" maxlength="6" pattern="\d{6}" required value="<?= htmlspecialchars((string)($form['codigo'] ?? '')) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nova senha</label>
                        <input type="password" name="senha" class="form-control" minlength="8" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Confirmar nova senha</label>
                        <input type="password" name="confirmar" class="form-control" minlength="8" required>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-success w-100">Salvar e continuar</button>
                    </div>
                    <div class="col-12 text-center">
                        <a href="<?= url('login') ?>">Voltar ao login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
