<?php use App\Core\Auth; ?>
<style>
.first-access-btn {
    background: #f1f3f5;
    border: 1px solid #ced4da;
    color: #2c3e50;
    border-radius: 0 6px 6px 0;
    padding: 0.45rem 0.9rem;
    font-size: 14px;
    line-height: 1.3;
    height: 100%;
}
.first-access-btn:hover {
    background: #e9ecef;
    color: #1f2933;
}
.first-access-btn:focus {
    box-shadow: none;
}
.first-access-group .form-control {
    border-radius: 6px 0 0 6px;
}
</style>
<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h5 text-center mb-3">Acesso ao COTELI</h1>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['status']) && $_GET['status'] === 'senha-criada'): ?>
                    <div class="alert alert-success py-2">Senha definida com sucesso. Faça login com a nova senha.</div>
                <?php endif; ?>
                <?php if (isset($_GET['status']) && $_GET['status'] === 'primeiro-acesso-ok'): ?>
                    <div class="alert alert-success py-2">Verificação realizada. Crie sua senha na próxima tela.</div>
                <?php endif; ?>
                <?php if (isset($_GET['status']) && $_GET['status'] === 'primeiro-acesso-erro'): ?>
                    <div class="alert alert-warning py-2">Não encontramos um acesso elegível para troca de senha.</div>
                <?php endif; ?>
                <form method="post" action="<?= url('autenticar') ?>">
                    <div class="mb-3">
                        <label for="login" class="form-label">Login ou e-mail</label>
                        <input type="text" class="form-control" id="login" name="login" placeholder="usuario ou email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="********" required>
                        <div class="form-text">Se seu acesso estiver marcado para troca de senha, use a senha provisória informada pelo sistema.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
                <hr class="my-3">
                <div class="small text-center mb-2">Primeiro acesso ou esqueceu a senha?</div>
                <form method="post" action="<?= url('login/primeiro-acesso') ?>">
                    <div class="input-group mb-2 first-access-group">
                        <input type="text" class="form-control" name="login_primeiro" placeholder="Informe seu login ou e-mail" required>
                        <button class="btn first-access-btn" type="submit">Criar nova senha</button>
                    </div>
                    <div class="form-text">Usaremos essa verificação para permitir que você crie uma nova senha com segurança.</div>
                </form>
            </div>
        </div>
    </div>
</div>
