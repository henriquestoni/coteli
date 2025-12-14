<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h5 text-center mb-3">Criar conta</h1>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form method="post" action="<?= url('registrar') ?>" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nome completo</label>
                        <input type="text" name="nome_completo" class="form-control" required value="<?= htmlspecialchars((string)($form['nome_completo'] ?? '')) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars((string)($form['email'] ?? '')) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Login</label>
                        <input type="text" name="login" class="form-control" required value="<?= htmlspecialchars((string)($form['login'] ?? '')) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha" class="form-control" required minlength="8">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Confirmar senha</label>
                        <input type="password" name="confirmar" class="form-control" required minlength="8">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary w-100">Criar conta</button>
                    </div>
                    <div class="col-12 text-center">
                        <a href="<?= url('login') ?>">Já tenho conta</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
 </div>
