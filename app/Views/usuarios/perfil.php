<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h5 mb-3">Meu perfil</h1>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] === 'ok'): ?>
                    <div class="alert alert-success">Dados atualizados.</div>
                <?php endif; ?>

                <form method="post" action="<?= url('perfil') ?>" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nome completo</label>
                        <input type="text" name="nome_completo" class="form-control" required value="<?= htmlspecialchars((string)($usuario['nome_completo'] ?? '')) ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars((string)($usuario['email'] ?? '')) ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Login</label>
                        <input type="text" name="login" class="form-control" required value="<?= htmlspecialchars((string)($usuario['login'] ?? '')) ?>">
                    </div>

                    <div class="col-12">
                        <hr>
                        <h2 class="h6">Alterar senha</h2>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Senha atual</label>
                        <input type="password" name="senha_atual" class="form-control">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Nova senha</label>
                        <input type="password" name="nova_senha" class="form-control" minlength="8">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Confirmar nova senha</label>
                        <input type="password" name="confirmar_senha" class="form-control" minlength="8">
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary">Salvar</button>
                        <a class="btn btn-outline-secondary ms-2" href="<?= url('') ?>">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
