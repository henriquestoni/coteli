<?php /** @var array $usuario */ ?>
<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h5 text-center mb-3">Primeiro acesso</h1>
                <p class="text-muted small text-center">Defina sua senha definitiva para continuar usando o sistema.</p>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= url('usuarios/primeiro-acesso') ?>" class="row g-3">
                    <?php if (empty($usuario['login']) || ($usuario['login'] === $usuario['email'])): ?>
                        <div class="col-12">
                            <label class="form-label">Login</label>
                            <input type="text" name="login" class="form-control" value="<?= htmlspecialchars((string)($usuario['login'] ?? '')) ?>" placeholder="sugerido: antes do @ do e-mail">
                        </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <label class="form-label">Nova senha</label>
                        <input type="password" name="nova_senha" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Confirmar nova senha</label>
                        <input type="password" name="confirmar_senha" class="form-control" required>
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-success w-100">Salvar e continuar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
