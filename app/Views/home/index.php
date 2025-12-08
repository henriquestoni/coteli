<?php
/** @var array $sections */
/** @var array|null $agenda */
/** @var string|null $agendaScope */

use App\Core\Auth;

$scope = $agendaScope ?? 'meus';
$currentUser = Auth::user();
$nivelAcesso = (int)($currentUser['nivel_acesso'] ?? 0);
?>

<div class="home-hero">
    <div class="hero-text">
        <p class="eyebrow">Painel de pregões</p>
        <h2>O que você está procurando?</h2>
        <p class="lead">Acesse cadastros, relatórios e os principais atalhos do fluxo de pregões em poucos cliques.</p>
        <div class="hero-actions">
            <a class="btn btn-primario" href="<?= url('pregoes/novo-base') ?>">
                <i class="bi bi-plus-lg"></i> Novo pregão base
            </a>
            <a class="btn btn-neutro" href="<?= url('pregoes/nova-repeticao') ?>">
                <i class="bi bi-arrow-repeat"></i> Nova repetição
            </a>
            <a class="btn btn-neutro" href="<?= url('relatorios') ?>">
                <i class="bi bi-graph-up"></i> Relatórios
            </a>
        </div>
    </div>
    <div class="hero-search">
        <form class="search-card" action="<?= url('') ?>" method="get">
            <label class="visually-hidden" for="q">Buscar no sistema</label>
            <input type="search" id="q" name="q" placeholder="Busque pregões, grupos, contratos ou pessoas" />
            <button class="btn btn-primario" type="submit"><i class="bi bi-search"></i> Buscar</button>
        </form>
        <div class="hero-status">
            <div class="status-icon success"></div>
            <div>
                <strong>Pastas sincronizadas</strong>
                <div class="text-muted">Nenhum alerta pendente</div>
            </div>
        </div>
    </div>
</div>

<div class="home-layout">
    <div class="home-col-principal">
        <?php foreach ($sections as $section): ?>
            <?php
                $requiredLevel = (int)($section['required_level'] ?? 0);
                if (!$requiredLevel && isset($section['title'])) {
                    $tituloSecao = (string)($section['title'] ?? '');
                    if (strpos($tituloSecao, 'Administração') === 0 || strpos($tituloSecao, 'Gestão') === 0) {
                        $requiredLevel = 4;
                    }
                }
                if ($nivelAcesso < $requiredLevel) {
                    continue;
                }

                $cards = $section['cards'] ?? [];
                $chunks = array_chunk($cards, 3);
            ?>
            <section class="secao">
                <div class="secao-header">
                    <h2><?= htmlspecialchars($section['title']) ?></h2>
                    <span class="pill">Atalhos</span>
                </div>
                <?php foreach ($chunks as $linha): ?>
                    <div class="tile-grid">
                        <?php foreach ($linha as $card): ?>
                            <?php $disabled = empty($card['link']); ?>
                            <?php if ($disabled): ?>
                                <div class="tile tile-disabled">
                                    <div class="tile-icon"><i class="bi bi-lock-fill"></i></div>
                                    <div class="tile-body">
                                        <h3><?= htmlspecialchars($card['title']) ?></h3>
                                        <p><?= htmlspecialchars($card['description']) ?></p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php $isMais = stripos($card['title'], 'mais') === 0; ?>
                                <a href="<?= htmlspecialchars($card['link']) ?>" class="tile<?= $isMais ? ' tile-more' : '' ?>">
                                    <div class="tile-icon"><i class="bi bi-folder-fill"></i></div>
                                    <div class="tile-body">
                                        <?php if ($isMais): ?>
                                            <h3>Mais</h3>
                                            <p>Continue explorando a área</p>
                                        <?php else: ?>
                                            <h3><?= htmlspecialchars($card['title']) ?></h3>
                                            <p><?= htmlspecialchars($card['description']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>
    </div>

    <aside class="home-col-agenda">
        <section class="agenda-wrapper">
            <div class="agenda-header">
                <div>
                    <p class="eyebrow">Agenda</p>
                    <h2>Próximos leilões</h2>
                    <div class="agenda-meta">Próximos 7 dias ou os 5 eventos futuros mais próximos.</div>
                </div>
                <div class="agenda-filtros">
                    <a href="<?= url('') ?>?agenda=meus"
                       class="btn <?= $scope === 'todos' ? 'btn-neutro' : 'btn-primario' ?> btn-compact">
                        Meus
                    </a>
                    <a href="<?= url('') ?>?agenda=todos"
                       class="btn <?= $scope === 'todos' ? 'btn-primario' : 'btn-neutro' ?> btn-compact">
                        Todos
                    </a>
                </div>
            </div>

            <?php if (!empty($agenda)): ?>
                <div class="agenda-lista">
                    <?php foreach ($agenda as $item): ?>
                        <?php
                            $rep = (int)($item['id_pregao_repeticao'] ?? 0);
                            $tipoSigla    = trim((string)($item['tipo_sigla'] ?? ''));
                            $numeroPregao = trim((string)($item['id_pregao'] ?? ''));
                            $anoPregao    = trim((string)($item['ano_pregao'] ?? ''));

                            $numero = $tipoSigla;
                            if ($numeroPregao !== '') {
                                $numero .= ($numero !== '' ? ' ' : '') . str_pad($numeroPregao, 3, '0', STR_PAD_LEFT);
                            }
                            if ($anoPregao !== '') {
                                $numero .= '/' . $anoPregao;
                            }
                            if ($rep > 0) {
                                $numero .= ' (R-' . str_pad((string)$rep, 2, '0', STR_PAD_LEFT) . ')';
                            }

                            $data = !empty($item['data_pregao'])
                                ? date('d/m/Y', strtotime($item['data_pregao']))
                                : '--';

                            $hora = !empty($item['hora_pregao'])
                                ? date('H:i', strtotime($item['hora_pregao']))
                                : '';

                            $pregoeiro   = trim((string)($item['nome_pregoeiro'] ?? ''));
                            $responsavel = trim((string)($item['nome_responsavel'] ?? ''));
                            $processo    = trim((string)($item['processo_sei'] ?? ''));
                        ?>
                        <div class="agenda-item">
                            <div class="agenda-date-card">
                                <div class="agenda-dia"><?= htmlspecialchars($data) ?></div>
                                <?php if ($hora): ?><div class="agenda-hora">às <?= htmlspecialchars($hora) ?></div><?php endif; ?>
                            </div>
                            <div class="agenda-objeto">
                                <div class="agenda-numero"><?= htmlspecialchars($numero !== '' ? $numero : 'Pregão s/número') ?></div>
                                <?php if ($processo !== ''): ?>
                                    <div class="agenda-meta">Processo SEI: <?= htmlspecialchars($processo) ?></div>
                                <?php endif; ?>
                                <div class="agenda-objeto-texto"><?= htmlspecialchars($item['objeto_licitado'] ?? 'Sem objeto informado') ?></div>
                                <div class="agenda-atributos">
                                    <?php if ($pregoeiro !== ''): ?>
                                        <span class="pill pill-muted">Pregoeiro: <?= htmlspecialchars($pregoeiro) ?></span>
                                    <?php else: ?>
                                        <span class="pill pill-alerta tag-alerta" data-id="<?= (int)($item['id_base_pregoes'] ?? 0) ?>">Pregoeiro não designado</span>
                                    <?php endif; ?>
                                    <?php if ($responsavel !== ''): ?>
                                        <span class="pill pill-muted">Responsável: <?= htmlspecialchars($responsavel) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="agenda-vazia">Nenhum leilão encontrado para este filtro.</div>
            <?php endif; ?>
        </section>
    </aside>
</div>
