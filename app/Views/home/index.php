<?php
/** @var array $sections */
/** @var array|null $agenda */
/** @var string|null $agendaScope */
/** @var array|null $pregoeiros */

use App\Core\Auth;

$scope = $agendaScope ?? 'meus';
$currentUser = Auth::user();
$nivelAcesso = (int)($currentUser['nivel_acesso'] ?? 0);
$pregoeiros = $pregoeiros ?? [];
?>

<div class="home-layout">
    <div class="home-col-principal">
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
        </div>
    </div>

    <aside class="home-col-agenda">
        <section class="agenda-wrapper">
            <div class="agenda-header agenda-header-centered">
                <div>
                    <p class="eyebrow text-center">Agenda</p>
                    <h2 class="text-center mb-2">Próximos Pregões</h2>
                </div>
                <div class="agenda-filtros agenda-filtros-center">
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
                                    <div class="agenda-meta"><strong>Processo:</strong> <?= htmlspecialchars($processo) ?></div>
                                <?php endif; ?>
                                <div class="agenda-objeto-texto"><strong>Objeto:</strong> <?= htmlspecialchars($item['objeto_licitado'] ?? 'Sem objeto informado') ?></div>
                                <div class="agenda-atributos">
                                    <?php if ($pregoeiro !== ''): ?>
                                        <span class="pill pill-muted">Pregoeiro: <?= htmlspecialchars($pregoeiro) ?></span>
                                    <?php else: ?>
                                        <button type="button"
                                                class="pill pill-alerta tag-alerta"
                                                data-id="<?= (int)($item['id_base_pregoes'] ?? 0) ?>"
                                                data-numero="<?= htmlspecialchars($numero !== '' ? $numero : 'Pregão s/número') ?>">
                                            Pregoeiro não designado
                                        </button>
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

<div class="modal fade" id="modalPregoeiro" tabindex="-1" aria-labelledby="modalPregoeiroLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" id="formModalPregoeiro">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPregoeiroLabel">Designar pregoeiro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="modalPregoeiroErro"></div>
                <p class="mb-3">Selecione um pregoeiro para <strong id="modalPregoeiroTitulo">este pregão</strong>.</p>
                <div class="mb-3">
                    <label class="form-label" for="campoModalPregoeiro">Pregoeiro</label>
                    <select class="form-select" id="campoModalPregoeiro" name="id_pregoeiro" required>
                        <?php if (!empty($pregoeiros)): ?>
                            <option value="">Escolha um pregoeiro</option>
                            <?php foreach ($pregoeiros as $p): ?>
                                <option value="<?= (int)($p['id_usuarios'] ?? 0) ?>"><?= htmlspecialchars((string)($p['nome_completo'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Nenhum pregoeiro ativo cadastrado</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-neutro" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primario">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.addEventListener('load', () => {
        const modalEl = document.getElementById('modalPregoeiro');
        const form = document.getElementById('formModalPregoeiro');
        const select = document.getElementById('campoModalPregoeiro');
        const tituloEl = document.getElementById('modalPregoeiroTitulo');
        const erroEl = document.getElementById('modalPregoeiroErro');
        if (!modalEl || !form || !select || !tituloEl || !window.bootstrap) {
            return;
        }

        const modal = new bootstrap.Modal(modalEl);
        let triggerEl = null;
        let idBase = null;

        const semOpcoesDisponiveis = () => {
            return !Array.from(select.options || []).some((opt) => opt.value && !opt.disabled);
        };

        const resetErro = () => {
            if (!erroEl) return;
            erroEl.classList.add('d-none');
            erroEl.textContent = '';
        };

        const mostrarErro = (msg) => {
            if (!erroEl) return;
            erroEl.textContent = msg || 'Não foi possível salvar.';
            erroEl.classList.remove('d-none');
        };

        document.addEventListener('click', (ev) => {
            const alvo = ev.target.closest('.tag-alerta');
            if (!alvo) return;
            ev.preventDefault();
            triggerEl = alvo;
            idBase = parseInt(alvo.dataset.id || '0', 10);
            const numero = alvo.dataset.numero || 'este pregão';
            tituloEl.textContent = numero;
            select.value = '';
            resetErro();
            if (semOpcoesDisponiveis()) {
                mostrarErro('Cadastre um pregoeiro ativo antes de designar.');
            }
            modal.show();
        });

        form.addEventListener('submit', async (ev) => {
            ev.preventDefault();
            resetErro();
            if (!idBase) {
                mostrarErro('Pregão inválido para atualização.');
                return;
            }
            if (semOpcoesDisponiveis()) {
                mostrarErro('Cadastre um pregoeiro ativo antes de designar.');
                return;
            }
            const idPregoeiro = parseInt(select.value || '0', 10);
            if (!idPregoeiro) {
                mostrarErro('Selecione um pregoeiro.');
                return;
            }

            try {
                const resp = await fetch('<?= url('pregoes/definir-pregoeiro') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_base_pregao: idBase,
                        id_pregoeiro: idPregoeiro,
                    }),
                });
                const data = await resp.json();
                if (!resp.ok || !data || data.status !== 'ok') {
                    mostrarErro((data && data.message) ? data.message : 'Não foi possível salvar.');
                    return;
                }

                const nome = data.pregoeiro_nome || '';
                if (triggerEl) {
                    const novo = document.createElement('span');
                    novo.className = 'pill pill-muted';
                    novo.textContent = nome ? `Pregoeiro: ${nome}` : 'Pregoeiro designado';
                    triggerEl.replaceWith(novo);
                }
                modal.hide();
            } catch (e) {
                mostrarErro('Não foi possível salvar.');
            }
        });
    });
</script>
