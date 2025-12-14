<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h5 text-center mb-3">Recuperar senha</h1>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form method="post" action="<?= url('recuperar-senha') ?>" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Login ou e-mail</label>
                        <input type="text" name="login_recuperar" class="form-control" required>
                        <div class="form-text">Enviaremos um código de 6 dígitos para redefinir sua senha.</div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary w-100">Enviar código</button>
                    </div>
                    <div class="col-12 text-center">
                        <a href="<?= url('login') ?>">Voltar ao login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
