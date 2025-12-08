<?php
/** @var array $sections */
/** @var array|null $agenda */
/** @var string|null $agendaScope */

use App\Core\Auth;

$scope = $agendaScope ?? 'meus';
$currentUser = Auth::user();
$nivelAcesso = (int)($currentUser['nivel_acesso'] ?? 0);
?>

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
                $chunks = array_chunk($cards, 4);
            ?>
            <section class="secao">
                <h2><?= htmlspecialchars($section['title']) ?></h2>
                <?php foreach ($chunks as $linha): ?>
                    <div class="grade-cards" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
                        <?php foreach ($linha as $card): ?>
                            <?php $disabled = empty($card['link']); ?>
                            <?php if ($disabled): ?>
                                <div class="card card-desabilitado">
                                    <h3><?= htmlspecialchars($card['title']) ?></h3>
                                    <p><?= htmlspecialchars($card['description']) ?></p>
                                </div>
                            <?php else: ?>
                                <?php $isMais = stripos($card['title'], 'mais') === 0; ?>
                                <a href="<?= htmlspecialchars($card['link']) ?>" class="card<?= $isMais ? ' card-mais' : '' ?>">
                                    <?php if ($isMais): ?>
                                        <h3>mais<br>...</h3>
                                    <?php else: ?>
                                        <h3><?= htmlspecialchars($card['title']) ?></h3>
                                    <?php endif; ?>
                                    <p><?= htmlspecialchars($card['description']) ?></p>
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
                <h2>Agenda de próximos leilões</h2>
                <div class="agenda-header-row">
                    <div class="agenda-meta">Próximos 7 dias (ou até 5 futuros se vazio)</div>
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
                            <div class="agenda-data">
                                <?= htmlspecialchars($data) ?>
                                <?= $hora ? ' às ' . htmlspecialchars($hora) : '' ?>
                            </div>
                            <div class="agenda-objeto">
                                <div class="agenda-numero"><strong><?= htmlspecialchars($numero !== '' ? $numero : 'Pregão s/número') ?></strong></div>
                                <?php if ($processo !== ''): ?>
                                    <div class="agenda-meta">Processo SEI: <?= htmlspecialchars($processo) ?></div>
                                <?php endif; ?>
                                <div class="agenda-objeto-texto"><?= htmlspecialchars($item['objeto_licitado'] ?? 'Sem objeto informado') ?></div>
                            </div>
                            <div class="agenda-meta">
                                <?php if ($pregoeiro !== ''): ?>
                                    <div>Pregoeiro: <?= htmlspecialchars($pregoeiro) ?></div>
                                <?php else: ?>
                                    <div>Pregoeiro: <a href="#" class="tag-alerta" data-id="<?= (int)($item['id_base_pregoes'] ?? 0) ?>">NÃO DESIGNADO</a></div>
                                <?php endif; ?>
                                <?php if ($responsavel !== ''): ?>
                                    <div>Responsável: <?= htmlspecialchars($responsavel) ?></div>
                                <?php endif; ?>
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
