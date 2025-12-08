<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>404 - Não encontrado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="text-center">
            <h1 class="display-4">404</h1>
            <p class="lead mb-1">Página não encontrada.</p>
            <?php if (!empty($message)): ?>
                <p class="text-muted"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
            <?php if (!empty($path)): ?>
                <p class="text-muted small"><?= htmlspecialchars($path) ?></p>
            <?php endif; ?>
            <a href="<?= url('') ?>" class="btn btn-primary mt-3">Voltar para a home</a>
        </div>
    </div>
</body>
</html>
