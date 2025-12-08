<?php /** @var array $sections */ ?>
<div class="mb-4">
    <h1 class="h5 mb-1">Cadastros</h1>
    <p class="text-muted mb-0">Selecione um tipo de cadastro para continuar.</p>
</div>

<?php foreach ($sections as $section): ?>
    <section class="secao">
        <h2><?= htmlspecialchars($section['title']) ?></h2>
        <div class="grade-cards">
            <?php foreach ($section['cards'] as $card): ?>
                <?php $disabled = empty($card['link']); ?>
                <?php if ($disabled): ?>
                    <div class="card card-desabilitado">
                        <h3><?= htmlspecialchars($card['title']) ?></h3>
                        <p><?= htmlspecialchars($card['description']) ?></p>
                    </div>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($card['link']) ?>" class="card">
                        <h3><?= htmlspecialchars($card['title']) ?></h3>
                        <p><?= htmlspecialchars($card['description']) ?></p>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>
