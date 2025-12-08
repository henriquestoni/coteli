## COTELI - Sistema mínimo (PHP puro)

Estrutura pensada para ser clara e fácil de manter sem frameworks.

- `index.php` — Front controller; lê `config/routes.php`, normaliza URI e despacha para o controlador.
- `config/` — Configurações de infraestrutura:
  - `database.php` (PDO) e `roles.php` (perfis básicos).
  - `routes.php` — Rotas amigáveis para `Router`.
- `app/` — Código de domínio.
  - `Core/` — Infraestrutura (roteador, controlador base, modelo base).
  - `Controllers/` — Controladores HTTP; use `BaseController::render`.
  - `Models/` — Camada de acesso a dados; estenda `BaseModel` para obter PDO.
  - `Views/` — Templates PHP simples. `layouts/main.php` injeta Bootstrap/jQuery e modal reutilizável.
- `assets/` — CSS/JS/IMG estáticos (`main.css`, `app.js`).
- `database/schema.sql` — Script de criação de tabelas com chaves únicas solicitadas.
- `storage/logs/` — Pasta para logs de aplicação/auditoria.

### Como adicionar um controlador
1. Criar `app/Controllers/NomeController.php` estendendo `BaseController`.
2. Implementar método público (ex.: `index`).
3. Registrar a rota em `config/routes.php`.
4. Criar view correspondente em `app/Views/...`.

### Como adicionar um modelo
1. Criar `app/Models/NomeModel.php` estendendo `BaseModel`.
2. Usar `$this->db` (PDO) para queries preparadas.

### Como adicionar uma view
1. Criar `app/Views/caminho/arquivo.php`.
2. Chamar `render('caminho/arquivo', ['chave' => 'valor'])` no controlador.

### Rotas padrão
- `/` → `HomeController@index`
- `/login` → `AuthController@login`
- `/pregoes` → `PregoesController@index`
- `/amostras` → `AmostrasController@index`
- `/empresas` → `EmpresasController@index`
- `/relatorios` → `RelatoriosController@index`

### Banco de dados
Execute `database/schema.sql` no MySQL. As tabelas de pregões e amostras possuem chaves únicas para garantir:
- Pregões: `UNIQUE(id_tipo_pregao, ano_pregao, id_pregao, id_pregao_repeticao)` (R-0 e R-X).
- Amostras: `UNIQUE(id_base_amostra, item_licitado, id_tipo_parecer)` com regra de modal de conflito.

### Autenticação e níveis
- Usuários: campos `login`, `email`, `senha_hash` (`password_hash/password_verify`), `nivel_acesso` (1 a 5), `ativo`.
- Constantes de nível em `config/roles.php`:
  1. Básico (relatórios limitados)
  2. Operador (cadastros restritos)
  3. Gestor (aprovação/status)
  4. Admin funcional (cadastros de apoio, usuários)
  5. Admin sistema (quase tudo, exceto apagar auditoria ou regras centrais)

### Auditoria
- Serviço `App\Services\AuditLogger::logAction($idUsuario, $acao, $entidade, $idEntidade, $dadosAntes, $dadosDepois)`.
- Registra CRUD e leituras sensíveis; tela de listagem em `/auditoria` (nível ≥ 4).

### Como obter os arquivos atualizados
- **Enviar para o GitHub**: crie um repositório vazio e execute `git remote add origin <url>` seguido de `git push -u origin work` (ou `main`, conforme o nome do seu branch) para publicar todo o histórico.
- **Gerar um zip localmente**: rode `scripts/export_latest.sh` para criar `dist/coteli-AAAAmmdd-HHMMSS.zip` com o conteúdo do commit atual. Use `scripts/export_latest.sh /caminho` para escolher outra pasta de saída.
