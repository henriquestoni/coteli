<?php
// Layout principal. Inclua somente variáveis seguras já escapadas nos conteúdos.
use App\Core\Auth;
$currentUser = Auth::user();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>COTELI | Sistema de Pregões e Amostras</title>

    <!-- Bootstrap 5 via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Seu CSS -->
    <link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">
</head>
<body class="app-body">
    <div class="app-shell">
        <aside class="sidebar" aria-label="Navegação principal">
            <div class="sidebar-brand">
                <button class="btn sidebar-toggle brand-toggle" id="sidebarToggle" type="button" aria-label="Alternar menu lateral">
                    <i class="bi bi-list"></i>
                </button>
                <a href="<?= url('') ?>" class="brand-link">
                    <div class="brand-text">
                        <span class="brand-title">COTELI/DEPLICON</span>
                        <span class="brand-sub">Pregões e Amostras</span>
                    </div>
                </a>
            </div>
            <nav class="sidebar-nav">
                <a class="nav-item" href="<?= url('') ?>">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-item" href="<?= url('cadastros') ?>">
                    <i class="bi bi-collection-fill"></i>
                    <span>Cadastros</span>
                </a>
                <a class="nav-item" href="<?= url('relatorios') ?>">
                    <i class="bi bi-bar-chart-fill"></i>
                    <span>Relatórios</span>
                </a>
                <a class="nav-item" href="<?= url('usuarios') ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span>Gestão</span>
                </a>
            </nav>
            <form class="sidebar-search" action="<?= url('') ?>" method="get">
                <label class="visually-hidden" for="searchTermSidebar">Buscar</label>
                <input id="searchTermSidebar" type="search" name="q" class="form-control" placeholder="Pesquisar">
                <button class="btn btn-icon" type="submit" aria-label="Buscar">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            <div class="sidebar-footer">
                <?php if ($currentUser): ?>
                    <div class="sidebar-user">
                        <div class="avatar-circle" aria-hidden="true">
                            <?= strtoupper(substr(trim((string)($currentUser['nome_completo'] ?? $currentUser['login'] ?? 'U')), 0, 1)) ?>
                        </div>
                        <div class="sidebar-user-info">
                            <div class="sidebar-user-name"><?= htmlspecialchars($currentUser['nome_completo'] ?? $currentUser['login'] ?? 'Usuário') ?></div>
                            <div class="sidebar-user-meta">Nível <?= htmlspecialchars((string)($currentUser['nivel_acesso'] ?? '?')) ?></div>
                        </div>
                    </div>
                    <a href="<?= url('logout') ?>" class="nav-item nav-item-ghost">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Sair</span>
                    </a>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="nav-item nav-item-ghost">
                        <i class="bi bi-person-fill"></i>
                        <span>Entrar</span>
                    </a>
                <?php endif; ?>
            </div>
        </aside>
        <div class="sidebar-overlay" aria-hidden="true"></div>

        <div class="main-pane">
            <div class="content-toolbar">
                <button class="btn sidebar-toggle d-lg-none" type="button" aria-label="Alternar menu lateral">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <main class="content-shell">
                <div class="conteudo">
                    <?= $content ?? '' ?>
                </div>
            </main>
        </div>
    </div>

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
            </div>
        </div>
    </div>

    <!-- jQuery 3.x -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS (bundle com Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
