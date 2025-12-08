<?php
// Layout principal. Inclua somente variáveis seguras já escapadas nos conteúdos.
use App\Core\Auth;
$currentUser = Auth::user();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>COTELI | Sistema de Pregões e Amostras</title>

    <!-- Bootstrap 5 via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Seu CSS -->
    <link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">
</head>
<body>
    <header class="topo">
        <div class="topo-esq">
            <a class="topo-marca" href="<?= url('') ?>">
                <span class="marca-nome">Sistema de Pregões e Amostras</span>
                <span class="marca-sub">COTELI / DEPLICON</span>
            </a>
            <nav class="menu-principal" aria-label="Navegação principal">
                <a class="menu-link" href="<?= url('cadastros') ?>">Cadastros</a>
                <a class="menu-link" href="<?= url('relatorios') ?>">Relatórios</a>
                <a class="menu-link" href="<?= url('usuarios') ?>">Gestão do Sistema</a>
            </nav>
        </div>
        <div class="topo-dir">
            <?php if ($currentUser): ?>
                <div class="usuario-info">
                    <div class="usuario-nome"><?= htmlspecialchars($currentUser['nome_completo'] ?? $currentUser['login'] ?? 'Usuário') ?></div>
                    <div class="usuario-meta">Nível <?= htmlspecialchars((string)($currentUser['nivel_acesso'] ?? '?')) ?></div>
                </div>
                <a href="<?= url('logout') ?>" class="btn btn-neutro btn-compact">Sair</a>
            <?php else: ?>
                <a href="<?= url('login') ?>" class="btn btn-primario btn-compact">Entrar</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <div class="conteudo">
            <?= $content ?? '' ?>
        </div>
    </main>

    <!-- Modal genérico para reuso em telas -->
    <div class="modal fade" id="modalInfo" tabindex="-1" aria-labelledby="modalInfoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInfoLabel">Título</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Conteúdo aqui.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-neutro" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery 3.x -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS (bundle com Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= asset('assets/js/app.js') ?>"></script>
    <script src="<?= asset('assets/js/agenda-modal.js') ?>"></script>
</body>
</html>
