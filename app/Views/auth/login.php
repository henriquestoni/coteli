<?php use App\Core\Auth; ?>
<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h5 text-center mb-3">Acesso ao COTELI</h1>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" action="<?= url('autenticar') ?>">
                    <div class="mb-3">
                        <label for="login" class="form-label">Login ou e-mail</label>
                        <input type="text" class="form-control" id="login" name="login" placeholder="usuario ou email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="********" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
                <p class="text-muted small mt-3 mb-0">
                    Preparado para extensão futura: OAuth2 (Google) e WebAuthn/biometria.
                </p>
            </div>
        </div>
    </div>
</div>
