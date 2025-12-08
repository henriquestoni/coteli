# COTELI – Sistema de Pregões e Amostras

## Visão geral
- Sistema web PHP com MVC caseiro (Controllers em `app/Controllers`, Views em `app/Views`, Models em `app/Models`).
- Autenticação via `App\Core\Auth`; layout principal em `app/Views/layouts/main.php`.
- Estilos em `assets/css/main.css` (padrão fornecido pelo usuário, não alterar sem solicitação).
- JavaScript consolidado em `assets/js/app.js` e `assets/js/agenda-modal.js` (agenda/links de pregoeiro).
- Dependências externas: Bootstrap 5.3 (CDN) e jQuery 3.7 (CDN).

## Navegação / Layout
- `app/Views/layouts/main.php` define topo com marca "Sistema de Pregões e Amostras", links: Cadastros (`/cadastros`), Relatórios (`/relatorios`), Gestão do Sistema (`/usuarios`), info do usuário e botão Sair.
- Conteúdo principal rendido em `<main><div class="conteudo">...</div></main>`; modal genérico `#modalInfo` para mensagens.

## Home (dashboard)
- Controller: `app/Controllers/HomeController.php`.
- View: `app/Views/home/index.php`.
- Seções com 3 cards + card "mais ..." por linha:
  - Cadastros: Pregões, Repetições, Amostras, mais ... (`/cadastros`).
  - Relatórios: Relatórios, Auditoria, Resumo de pregões, mais ... (`/relatorios`).
  - Gestão do Sistema: Usuários, Pregoeiros, Configurações (disabled), mais ... (`/usuarios`).
- Agenda à direita: próximos 7 dias (ou até 5 futuros se não houver), filtro Meus/Todos via query `?agenda=meus|todos`.
  - Cada item: data/hora, número do pregão (R-0 sem sufixo, R-X com sufixo), Processo SEI, objeto licitado (line clamp 2), Pregoeiro (link "NÃO DESIGNADO" em vermelho quando vazio), Responsável.
  - Click em "NÃO DESIGNADO" deve abrir/ir para edição do pregão (handled em `assets/js/agenda-modal.js`).

## Agenda (model)
- `app/Models/PregaoModel.php` (função `getAgendaProximos`) retorna agenda priorizando próximos 7 dias; se vazio, retorna até 5 próximos futuros. Inclui processo SEI, pregoeiro, responsável.

## Formulário Pregão Base (R-0)
- View: `app/Views/pregoes/form_base.php`.
- Título: "Cadastre novo pregão base"; botão salvar: "Salvar".
- Campos obrigatórios: todos exceto Pregoeiro (opcional). "Publicação no D.O." é obrigatório.
- Rótulos corrigidos com acentuação; listagem "Últimos 5 pregões cadastrados".
- Ação POST para `/pregoes/salvar-base`; dup-check via JS (ver abaixo).

## Formulário Repetição (R-X)
- View: `app/Views/pregoes/form_repeticao.php`.
- Título: "Nova repetição (R-X)" e subtítulo orientativo.
- Botão: "Salvar repetição"; campos com acentuação corrigida.
- Ação POST para `/pregoes/salvar-repeticao`.

## JavaScript consolidado (`assets/js/app.js`)
- `window.coteliModal(title, bodyHtml)`: usa o modal genérico `#modalInfo` para mensagens.
- `applySeiMask`: máscara/normalização SEI (SEI-000000/000000/0000) para inputs `processo_sei` (base e repetição).
- Pregão base:
  - Normaliza número (3 dígitos com zero à esquerda).
  - Checa duplicidade via endpoint `pregoes/verificar-chave` (GET com `id_tipo_pregao`, `ano_pregao`, `id_pregao`, `id_pregao_repeticao=0`).
  - Se duplicado: abre modal com próximo pregão sugerido e próxima repetição sugerida; permite ajustar número ou seguir para nova repetição (`pregoes/nova-repeticao` com params `id_base_pregao_r0`, `rep_sugerida`).
  - Impede submit se chave marcada como duplicada.
- Repetição:
  - Máscara SEI e validação simples do número de repetição (mínimo 1).

## Agenda modal JS (`assets/js/agenda-modal.js`)
- Tornou clicável o link "NÃO DESIGNADO" para redirecionar a edição do pregão em falta de pregoeiro (ajuste pode ser necessário conforme rota real).

## CSS (não alterar sem pedido)
- Padrão em `assets/css/main.css` fornecido pelo usuário; inclui `.card-mais` e remoção de hover visível nas caixas.
- `line-clamp: 2` aplicado em `.agenda-objeto-texto`.

## Pontos pendentes / observações
- Validar a rota usada em `assets/js/agenda-modal.js` para abrir/editar pregão sem pregoeiro.
- "Configurações" em Gestão do Sistema está desabilitado (link vazio).
- Sistema pode ter mais JS/CSS embutido em outras páginas não revisadas aqui.
