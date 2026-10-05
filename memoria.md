# MEMÓRIA DO PROJECTO — Sistema ADG

**Documento de acompanhamento e preservação de contexto.**
Actualizado ao fim de cada sprint. Fonte de verdade técnica: `PROMPT_MASTER.md` (v3.0).

- **Projecto:** ADG — Ferramenta de Gestão Comercial Pessoal
- **Cliente:** Adailson Nogueira (utilizador único) · **Empresa:** Otimize
- **Repositório:** `crm-prototipo/` (git, branch `main`) — histórico **reiniciado** em 2026-10-03
- **Código Laravel:** pasta **`adg/`** (conforme PROMPT_MASTER §11 e Sprint 0)

> ⚠️ **Histórico anterior apagado por decisão do utilizador (2026-10-03).**
> O projecto anterior (`crm-pessoal/`, sprints 0-9 feitos em Tailwind puro) foi **eliminado**
> por não obedecer ao PROMPT_MASTER (nome errado, sem Bootstrap, SQLite, branding errado).
> Recomeço do zero, seguindo o MD à letra.

---

## 1. ONDE ESTAMOS (Mapa sprint PROMPT_MASTER ↔ commits)

| Sprint PROMPT_MASTER | Estado | Commit | Observação |
|---|---|---|---|
| Sprint 0 — Preparação | ✅ Concluído | `3f93c5e` + `56072b6` | `adg/` criado, stack conforme §5 |
| Sprint 1 — Fundação | ✅ Concluído | `0aa7f8d` | Enums, auth username, seeders, layouts do protótipo, CSS extraído |
| Sprint 1.1 — UI/UX Base | ✅ Concluído | `9066d59` | 10 componentes Blade + página de validação `/ui` |
| Sprint 2 — Pipeline | ✅ Concluído | `839329b` | Kanban 9 fases, drag&drop, stats, novos campos (pessoa_contacto, tipo_relacao, comercial_id, produto_id, sector, origem) + commercials + pivot |
| Sprint 3 — Nova Oportunidade + Detalhe | ✅ Concluído | `839329b` | Create/View/VisitReport actualizados c/ novos campos, comercial, produto; relações many-to-many produto |
| Sprint 4 — Importação Excel | ✅ Concluído | `839329b` | Template/preview/import actualizados p/ 9 fases; labels novos |
| Sprint 5 — Agenda Comercial | ✅ Concluído | `c6ba443` | Agenda completa + **command `agenda:reminder` 20h (email + log), schedule daily 20:00** |
| Sprint 6 — Conciliações | ✅ Concluído | `cbdde3d` | Enum ConciliactionClassificacao, migration conciliactions, Model+Factory+Seeder, Service, Livewire List/Form, views baseadas em payment.html, rotas, sidebar acordeão, 83 testes suite |
| Sprint 7 — Contratos | ✅ Concluído | `abda4e2` | Enum ContractStatus, migration contracts, Model/Factory/Seeder, Service, Livewire List/Form/View, views baseadas em projects*.html, rotas, sidebar acordeão |
| Sprint 8 — Produtos | ⛔ A implementar | — | Tabela `products`, list+form, view `customers.html` |
| Sprint 9 — Comerciais | ⛔ A implementar | — | Tabela `commercials` (já no Sprint 2), list+form, vista carteira |
| Sprint 10 — Alertas | ⛔ A implementar | — | Tabela `alerts`, `AlertService`, command `alerts:generate`, email |
| Sprint 11 — Dashboard + Painel Executivo | ⛔ A implementar | — | 6 blocos, Chart.js, `index.html` + `analytics.html` |
| Sprint 12 — Testes e Lançamento | ⛔ A implementar | — | Testes com Adailson, feedback, ajustes, deploy, SSL, backup |

**Próximo sprint a executar:** Sprint 8 — Produtos.
**Conforme resposta.md §13:** não bloquear à espera do cliente — seguir suposições documentadas.
**Ficheiro de plano:** `plano.md` (criado na raiz do projecto).

---

## 2. STACK IMPLEMENTADA (Sprint 0 — conforme PROMPT_MASTER §5)

| Camada | Tecnologia | Versão instalada | Conforme §5? |
|---|---|---|---|
| Backend | Laravel | **12.69.3** | ✅ |
| PHP | PHP (Laravel Herd) | **8.4.26** | ✅ |
| Base de dados | **MySQL 8** (`DB_DATABASE=adg`) | 8.0 (serviço Windows) | ✅ |
| Autenticação | Laravel Breeze (stack **livewire**) | 2.4.2 | ✅ |
| Frontend | Blade + **Livewire 3** | 3.8.10 (+ volt 1.11.2) | ✅ |
| CSS Framework | **Bootstrap 5.3** (npm `bootstrap@5.3.8`) | 5.3 | ✅ |
| CSS Utilitário | **Tailwind CSS 4** (`@tailwindcss/vite`) | 4.3.3 | ✅ |
| Interacções | **Alpine.js** (sem jQuery) | 3.4.2 | ✅ |
| Build | **Vite 7** (`laravel-vite-plugin@2`) | 7.3.6 | ✅ |
| Testes | **PHPUnit** | 11.5.56 | ✅ |
| Queue/Session/Cache | database | — | ✅ |
| Excel / Gráficos / Backup | **maatwebsite/excel** 4.0 (Sprint 4) | 4.0 | ⏳ gráficos/backup nos sprints seguintes (§5) |

### Notas de ambiente (importante para não perder contexto)

- **PHP/composer:** usar o Herd, **não** o PHP 8.2 do XAMPP:
  ```powershell
  $env:PATH = "$env:USERPROFILE\.config\herd\bin;$env:PATH"
  # ou: & "$env:USERPROFILE\.config\herd\bin\herd.bat" php artisan ...
  ```
  `breeze:install` chama o `composer` do PATH — sem o Herd à frente usa PHP 8.2 e **falha**.
- **MySQL:** o serviço `MySQL80` está **DISABLED** e não arranca sem admin
  (erro "Acesso negado"). Workaround usado: arrancar o mysqld directamente —
  `mysqld --defaults-file="C:\ProgramData\MySQL\MySQL Server 8.0\my.ini"` (fica a correr na 3306).
- **Servidor:** `php artisan serve` **falha** ("Failed to listen") sem a flag
  `--no-reload` (o Windows não suporta o fork dos `PHP_CLI_SERVER_WORKERS`).
  ```powershell
  php artisan serve --host=127.0.0.1 --port=8099 --no-reload   # → 200 OK
  ```
- **Login:** páginas Breeze/Volt; validar via `php artisan test` (AuthenticationTest),
  não via POST directo (formulário Livewire).
- **BD de testes:** `phpunit.xml` define `DB_DATABASE=adg_test` — o `RefreshDatabase`
  faz `migrate:fresh` e **apagava os dados da `adg`** (utilizador seedado sumiu em
  2026-10-03 e o login falhava com "credenciais erradas"). Criada `adg_test` via mysql.
  Se correr `php artisan test` sem esse env, repovar depois com `php artisan migrate:fresh --seed`.
- **Novas migrations:** os testes só criam tabelas na `adg_test`; a BD dev `adg` precisa
  de `php artisan migrate` à mão (Sprint 3: `visit_reports` deu 500 em `/pipeline/{id}`
  até correr a migration na `adg`).
- **Servidor com config obsoleta:** se o `artisan serve` foi arrancado antes de mudar
  `.env`/config (ex.: `APP_NAME`), o processo pode continuar a servir valores antigos
  ("Entrar || Laravel" no `<title>`, cookie `laravel_session`) — **reiniciar o serve**.
- **maatwebsite/excel 4.0 (Sprint 4):** o trait `Exportable` está em
  `Maatwebsite\Excel\Concerns` (não `Maatwebsite\Excel\`); as constantes `XLSX`/`CSV`
  são da classe `Maatwebsite\Excel\Excel` — **não existem no Facade** (`Excel::XLSX`
  dá "Undefined constant"); `Excel::toCollection(null, $path)` lê linhas cruas sem
  classe de import; num teste, `BinaryFileResponse::getContent()` devolve `false` —
  ler `baseResponse->getFile()->getPathname()`.
- **Uploads em testes Livewire:** `Testable::upload` exige `$file->name` → usar
  `UploadedFile::fake()->createWithContent(nome, binario)` (a classe `Testing\File`
  tem `public $name`); `new UploadedFile(...)` cru rebenta. O fileinfo detecta
  `.xlsx` correctamente nesta máquina → regra `mimes:xlsx` funciona.
- **PowerShell `Set-Content -Encoding UTF8` (PS 5.1) mete BOM** em ficheiros sem BOM —
  remover com `[IO.File]::WriteAllText($p, $t, (New-Object Text.UTF8Encoding $false))`.

---

## 3. VALIDAÇÃO DO SPRINT 0 (§16 — "serve, login, Bootstrap e Tailwind carregam")

- ✅ `php artisan serve --no-reload` → `/login` responde **200**
- ✅ Login funcional (26 testes Breeze a passar, incl. `AuthenticationTest`)
- ✅ CSS compilado (`public/build/assets/app-*.css`, ~271KB) contém:
  `--bs-*` (Bootstrap) + `--tw-` (Tailwind) + `Inter` + `#3454d1` (primária do protótipo)
- ✅ `public/images/logo-d.png` servido (200, 160KB)
- ✅ `public/assets/` copiado do protótipo (css/, js/, vendors/, images/) — §11
- ✅ Título da app: **ADG** (`APP_NAME=ADG`)
- ✅ `php artisan migrate:fresh` corre em **MySQL** (`adg`)
- ✅ `./vendor/bin/pint --test` → **passed**
- ✅ `npm run build` → sem erros (Vite 7 + Tailwind 4 + Bootstrap)

---

## 4. CONFIGURAÇÃO DO FRONTEND (§4 — estratégia CSS) — actualizado no Sprint 1

`adg/resources/css/app.css` (cascata por ordem):
1. `@import url(...Inter...)` — Google Fonts
2. `@import 'bootstrap/dist/css/bootstrap.min.css'` — **framework principal (§4.2)**, npm 5.3.8
3. `@import './prototype/bootstrap-diff.css'` — **diff de cores** proto-vs-5.3 (403 regras):
   `:root{--bs-primary:#3454d1, --bs-light:#eff0f6, --bs-dark:#283c50, success/warning/danger/info}`,
   `.btn-primary`, focus rings `rgba(52,84,209)`, etc.
4. `@import 'tailwindcss'` + `@plugin '@tailwindcss/forms'` + `@source ...` (Tailwind 4)
5. `@import './prototype/theme-extract.css'` — 1107 regras do `theme.min.css`
   (whitelist `.nxl-`, page-header, auth-cover, custom-control, utilitários…); body `#f3f4f6`
6. `@import './prototype/icons-extract.css'` — 4652 regras (`@font-face` + `.feather-*`, `.bi-*`, `.fa-*`)
7. `@theme` tokens (Tailwind) → `:root{--bs-table-accent-bg}` → fixes do `<style>` inline do protótipo
   (`.nxl-header{top:0}`, `.nxl-content{padding-top:0}`, `.footer{z-index:10}`) → `[x-cloak]`

> ⚠️ Corpo real do protótipo = `#f3f4f6` (theme.min.css), **não** `#eff0f6` como diz o §3 do MD
> (`#eff0f6` = `.bg-gray-100`/`--bs-light`). Prevalece o protótipo (§22).

`adg/resources/js/app.js`: `bootstrap` (bundle c/ Popper) + Alpine + `Alpine.data('sidebar')`
(substitui o jQuery `minimenu`/`mob-navigation-active` do protótipo; localStorage igual:
key `nexel-classic-dashboard-menu-mini-theme` = `menu-mini-theme`).
`vite.config.js`: plugins `laravel` + `tailwindcss`.

### Extração §4.6 (concluída no Sprint 1)

- **Script reprodutível:** `tools/extract-prototype-css.ps1` (PowerShell, ASCII+BOM;
  re-executar se o protótipo mudar). Whitelists `$themeKeep`/`$vendorsKeep`/`$brandRe`.
- **Saídas em `adg/resources/css/prototype/`:** `theme-extract.css` (144.9KB),
  `icons-extract.css` (194.7KB), `bootstrap-diff.css` (73.2KB, 403 regras).
- **Fonts:** 14 ficheiros → `adg/public/fonts/` (feather, bootstrap-icons, fontawesome).
- **Validações:** 0 classes de `index.html`+`auth-login-cover.html` sem estilo;
  todas as 99 classes de ícones usadas cobertas; URLs `../fonts/` → `/fonts/`.
- `theme.min.css` **não** é importado directamente (§4.6) — só as regras extraídas.
- `bootstrap.min.css` do protótipo = Bootstrap **5.2.0-beta1 recolorido por cor**;
  o diff cobre as diferenças face ao 5.3 npm.

---

## 5. VALIDAÇÃO DO SPRINT 1 (§16)

- ✅ `php artisan test` → **26 testes, 79 assertions, 0 falhas**
  (incl. login por **username**, login por email, registo fechado 404, logout via POST `/logout`,
  dashboard com `nxl-navigation`/`nxl-header`/`Conciliações`)
- ✅ `vendor/bin/pint` → **passed**
- ✅ `npm run build` → OK: CSS **645.71KB** (gzip 101.82KB) + JS 189.12KB
- ✅ Bundle contém: `--bs-primary:#3454d1`, `.nxl-navigation{`, `font-family:feather`,
  `#f3f4f6`, `.auth-cover-wrapper{`, `.btn-primary{`
- ✅ HTTP (`php artisan serve --port=8099 --no-reload`):
  `/login` **200** com `auth-cover-wrapper` + `images/logo-d.png` + link CSS Vite;
  `/` **200**; CSS servido 645KB
- ✅ `migrate:fresh --seed` (MySQL `adg`) — `UserSeeder` + `SettingSeeder`
- ⚠️ **Pendente:** validação visual lado a lado com o protótipo (§11) — utilizador deve rever
  `/login` e `/dashboard` no browser (`http://127.0.0.1:8099`)
- ⚠️ Warnings benignos do build: `a::hover` (typo herdado do theme.min), flag svgs
  do language dropdown (dropdown removido — nunca será resolvido)

### Pendências gerais
- MySQL como serviço persistente (pedir admin para `sc config MySQL80 start= demand`)
- Opcional: confirmar com utilizador que a BD `adg` substitui a antiga `crm_pessoal`
- `.env.example` ajustado a MySQL (vem com sqlite por omissão)

### Problema conhecido (Sprint 0) — corrigido no Sprint 1
O teste `AuthenticationTest:34` falhava com `form.email` → corrigido para `form.login`
(`LoginForm` recebe `login` email OU username).

---

## 5A. DECISÕES DO UTILIZADOR (Sprint 1 — Q&A 2026-10-03)

1. **Menu §7 agrupado como no protótipo** (Oportunidades ▸ sub-opções), com ordem
   **Nova Oportunidade 1º, Pipeline 2º** (utilizador sobrepois ao §7 do MD, que dizia Pipeline primeiro).
2. **Os 9 itens do menu estão todos visíveis** desde o Sprint 1; os não criados ficam
   inactivos com badge **"Em breve"** (cursor-not-allowed, sem link).
3. **Sidebar/header seguem o protótipo**, não o §3 do MD: o CSS real tem sidebar **branca
   280px** e header **80px** (pele clara em todas as páginas) — o §3 descreve sidebar escura
   `#283c50`/256px/64px que **não existe** no protótipo. §22/§4.6 mandam copiar o protótipo.
4. **Header simplificado** — removidos por não serem features documentadas em §7-§10:
   mega menu, search, language, fullscreen, dark/light mode, timesheets;
   notifications (bell) **volta no Sprint 9**; badge "GESTOR"/status do utilizador removido
   (não há roles); "Definições da conta" removido (sem rota). Header mantém: hamburger,
   botões de colapso, dropdown utilizador (avatar + Meu perfil + Terminar sessão).
5. **Registo fechado** (utilizador único) — rota/view/`RegisteredUserController`/`RegistrationTest`
   removidos; welcome navigation sem link Register; teste `GET /register` → 404.
 6. **`auth-login-minimal.html` eliminado** (§2: "Remover").
 7. **Schema `opportunities`** (2026-10-03): acrescentar **`titulo`, `probabilidade`
    (tinyint 0-100) e `data_fecho`** ao §8.2 — o protótipo mostra título, badge de %
    e data nos cartões e o formulário leads-create (Sprint 3) tem esses campos.
 8. **Fases do pipeline** (2026-10-03): Leads → Visitas → Aprovação → Aprovado /
    Não Aprovado (confirmado pelo utilizador; coluna "Estado" e kanban renomeados;
    rótulo "Ganho (mês)" do protótipo renomeado para **"Aprovado (mês)"** — §6 proíbe
    "Ganho/Perdido").

---

## 5C. SPRINT 2 — PIPELINE (§8.2, §8.3, Sprint 2)

- **Migrations:** `opportunities` (§8.2 + decisão 7; índices uuid/fase/email/telefone/…)
  e `opportunity_stage_history` (§8.3, tabela singular — `protected $table` no model).
- **Models:** `Opportunity` (SoftDeletes, cast `fase_atual`→enum, uuid automático,
  `scopeStage`) e `OpportunityStageHistory` (`UPDATED_AT = null`).
- **`OpportunityService::changeStage`** — grava histórico (`user_id` = auth ou único
  utilizador) e estados finais: Aprovado → probabilidade 100 + `data_aprovacao`;
  Não Aprovado → probabilidade 0. `formatKz()` = formato do protótipo (`Kz 110.400.000`).
- **Livewire:** `Opportunities\Pipeline` (constante `COLUMNS` com classes por fase,
  filtros search/estado, `move()` via drag&drop Alpine→`$wire.move`, evento
  `opportunity-moved`) e `Opportunities\Stats` (4 cartões do collapseOne: Pipeline
  Aberto, Aprovado (mês), Ponderado = Σ valor×%, Taxa Conversão 30 dias).
- **Página `/pipeline`** (rota `pipeline`): copiada de `leads.html` — page-header com
  botão de estatísticas (collapse), dropdown de fases, "Nova Oportunidade"
  (placeholder `javascript:void(0)` até Sprint 3), filtro de kanban e board;
  CSS inline do kanban em `app.css` (data-stage renomeados). Nova slot de layout
  **`pageHeaderBelow`** para o accordion `collapseOne`. Dropdown de gestores do
  protótipo **removido** (schema sem responsável — não inventar pessoas).
- **Sidebar:** item **Pipeline activo** (`wire:navigate`); submenu abre automaticamente.
- **`OpportunitySeeder`:** os 11 cartões do leads.html (contas por coluna exactas:
  23,3M / 33,6M / 53,5M / 14,8M / 15M).
- **Testes:** `PipelineTest` (9) → suite **37 testes, 142 assertions**; pint; build 649KB.
- **Validação visual pendente:** abrir `http://127.0.0.1:8099/pipeline` e comparar com
  `leads.html` (colunas renomeadas).

---

## 5B. SPRINT 1.1 — COMPONENTES (§9)

Em `adg/resources/views/components/ui/` — markup exacto do protótipo com props Blade:

| Componente | Origem no protótipo | Props principais |
|---|---|---|
| `button` | `btn btn-*` (index, leads-create) | variant, size (xs/sm/lg), icon, icon-only, href, type |
| `badge` | variants soft/solid/gray/border (customers, index) | variant, color, header (nxl-h-badge) |
| `form-input` / `form-select` / `form-textarea` | leads-create (select usa `form-control`, não `form-select`) | label, required, error; resto via `$attributes` (wire:model…) |
| `modal` | apps-notes (fade, data-bs-keyboard=false, header/body/footer) | id, title, size, centered, scrollable; slot `footer` |
| `stat-card` | index (card stretch + avatar-text + progress) | icon, value, title, footer-*, progress |
| `page-header` | index (h5 m-b-10 + breadcrumb Início/…) | title, breadcrumb; slot `right` |
| `data-table` | customers (`table-responsive` + `table table-hover`) | id; slot `thead`; slot default = linhas |
| `alert-badge` | header (`badge bg-* nxl-h-badge`) | variant (cor sólida) |

- **Layout:** `sidebar`/`header` concluídos no Sprint 1 (checklist §9 fechada).
- **Validação:** página **`/ui`** (rota `ui.demo`, auth) mostra todos os componentes —
  comparar lado a lado com `index.html`/`leads-create.html`/`customers.html`.
- **Testes:** `UiComponentsTest` (2) → suite total **28 testes, 103 assertions, pint passed**.

---

## 5D. SPRINT 3 — NOVA OPORTUNIDADE + DETALHE (Sprint 3, commit `91d3514`)

- **Migration `visit_reports` (§8.5):** uuid, opportunity_id/user_id (cascade),
  data_visita, objectivo, pessoas_presentes, assuntos_tratados*, resultado,
  proxima_accao, anexos (json). **Model** `VisitReport` (+`HasFactory`, casts date/array,
  uuid no `creating`) e **`VisitReportFactory`**. Relação `Opportunity::visitReports()`.
  Campo `anexos` **sem UI** (sem definição de upload — pendente).
- **`OpportunityService::create(array)`:** aplica estados finais na criação (Aprovado →
  prob 100 + `data_aprovacao`; Não Aprovado → 0) e grava histórico com `fase_anterior = null`.
- **Livewire `Opportunities\OpportunityCreate`** + view copiada de `leads-create.html`:
  Cliente (obrigatório), Título, Valor, Estado (5 fases via `Pipeline::COLUMNS`), Probabilidade
  10-100%, Data de fecho, Descrição; cards laterais "Pipeline" (fases renomeadas) e "Dicas";
  `id="oppForm"` + `wire:submit="save"` → redirect `/pipeline`. Header: "Guardar rascunho"
  (placeholder) e "Criar Oportunidade" (`requestSubmit()` via `onclick`, pois o header fica
  fora do DOM do componente Livewire).
- **Livewire `Opportunities\OpportunityView`** + view de `leads-view.html`:
  - Tabs **Resumo / Proposta / Actividades / Notas / Relatório de Visita** (nova, última).
    A barra de tabs vive no slot **`pageHeaderBelow`** (posição exacta do protótipo) e
    sincroniza com o componente via script `setOppTab()` → `Livewire.find(wire:id).set('activeTab', …)`;
    o componente re-renderiza os `tab-pane` com a classe `active` correcta (mantém a aba
    após eventos como `visit-report-added`).
  - Resumo: dados reais (valor/ponderado via `formatKz`, responsável = utilizador logado,
    criada em). Subtítulo "Descrição" do protótipo colapsou no card **Descrição** (`interesse`).
  - Dropdown do header (Editar/Agendar/Marcar/Eliminar) = **placeholders** `javascript:void(0)`
    (fiel ao protótipo; moves fazem-se no kanban — Sprint 2).
- **Livewire `Activities\VisitReportForm`:** formulário §8.5 (valida `data_visita` e
  `assuntos_tratados`), `user_id` = auth, dispatch `visit-report-added`; lista de relatórios
  no `OpportunityView` (`#[On]` re-render).
- **Rotas:** `pipeline/nova` (`opportunities.create`, **antes** do binding) e
  `pipeline/{opportunity}` (closure view, `opportunities.show`, 404 para id inexistente).
- **Breadcrumb:** layout passa `:pageHeaderBreadcrumb` (array via atributo PHP do componente)
  ao `x-ui.page-header` — create = `['Nova']`, show = `['Oportunidades', titulo]`, default `[title]`.
- **Sidebar:** "Nova Oportunidade" deixou de ser "Em breve" → link activo, **1ª opção**
  (corrige ordem que ficou invertida no Sprint 1); submenu abre também em `opportunities.*`.
- **Kanban:** título do cartão é agora `<a wire:navigate>` para o detalhe.
- **Decisões de mapeamento (sem inventar dados):** campo "Responsável" do formulário
  **removido** (schema sem responsável); "Cliente" = input + `<datalist>` com empresas
  existentes (não há módulo de clientes); "Descrição" → coluna `interesse`; `nome` e `empresa`
  recebem o mesmo valor do Cliente.
- **Testes:** `OpportunityCreateTest` (7) + `OpportunityViewTest` (8) → **52 testes,
  228 assertions**; pint passed; build OK (sem mudanças de CSS); validação HTTP manual:
  `/pipeline/nova` 200 (form, breadcrumb, datalist), `/pipeline` 200 (link do cartão),
  `/pipeline/{id}` 200 (tabs, resumo, form de relatório, `setOppTab`, `Kz 12.000.000`/
  `Kz 3.600.000`, empty states), `/pipeline/99999` **404**.

---

## 5E. SPRINT 4 — IMPORTAÇÃO EXCEL (Sprint 4, commit `6fb0330`)

- **`maatwebsite/excel ^4.0`** instalado via composer (PHP 8.3+, Laravel 12 ✓).
- **Template (`app/Exports/OpportunityTemplateExport`):** só cabeçalhos, 1ª linha a
  negrito (`WithStyles`), auto-largura; descarregável em
  `GET /pipeline/importar/template` (`Excel::download`) → `template-oportunidades.xlsx`.
  Colunas = `ExcelImportService::HEADINGS`: Cliente, Título, Valor (Kz), Estado,
  Probabilidade, Data de fecho, Descrição (**sem linha de exemplo**, para não importar lixo).
- **`ExcelImportService` (checklist "Service"):**
  - `parse()`: `Excel::toCollection(null, path)` (linhas cruas); mapeia cabeçalhos
    normalizados (sem acentos/maiúsculas; aceita aliases `Valor (Kz)`/`Montante`,
    `Data de fecho`/`Data`, `Descrição`/`Interesse`…) e **falha se faltarem
    Cliente/Título/Valor**; ignora linhas vazias; cap de **1000 linhas**.
  - Validação por linha (mesmas regras do formulário): Cliente/Título ≤255;
    Valor aceita `12000000`, `12.000.000`, `12,000,000`, `12,5`, texto `Kz`;
    Estado aceita labels do pipeline (`Pipeline::COLUMNS`), valores do enum ou vazio
    (= Leads; **termos proibidos §6 não são aceites**); Probabilidade 0–100 (`30%` ok,
    vazio = 30 por omissão); Data de fecho: serial de Excel, `DD/MM/AAAA`, `YYYY-MM-DD`.
  - **Duplicados:** mesmo `Título` + `Cliente` — contra a BD (query única com
    `whereIn`, SoftDeletes excluídos) e dentro do ficheiro (linha repetida → indica
    a 1ª linha). Linhas com erro não entram na checagem.
  - `import()`: **`DB::transaction`**; por linha: erro → `erros++`; duplicada → `duplicadas++`;
    re-verifica a BD (estado pode mudar depois do preview); senão cria via
    **`OpportunityService::create`** (histórico inicial + estados finais) → `criadas++`.
    Devolve `rows` (com `estado_importacao`) + `report{total,criadas,duplicadas,erros}`.
- **Livewire `Opportunities\OpportunityImport`** (checklist "Componente"):
  `WithFileUploads` (`file` validado em `preview()`: `required|mimes:xlsx,xls|max:10240`);
  fluxo `updatedFile` (limpa estado) → `preview()` → `import()` (apaga o temporário,
  null o ficheiro) → `startOver()` (não usar `reset` — é método do Component).
- **Página `GET /pipeline/importar`** (`opportunities.import`, **antes** do binding):
  header com "Nova Oportunidade" + "Descarregar template"; breadcrumb
  `['Nova Oportunidade', 'Importar Excel']`. Montada só com primitivas do protótipo
  (cards, `form-control`, `btn`, badges, `alert alert-warning` de projects-create.html,
  tabela `table-hover align-middle` + `thead.bg-light` de analytics.html) — **não há
  página de importação no protótipo** (§22: não inventar, reaproveitar).
- **Fluxo na UI:** card "Importar de Excel" (ficheiro + Pré-visualizar + template);
  **Pré-visualização** (tabela: Linha/Cliente/Título/Valor/Estado/Prob./Fecho/Situação
  com badges Pronta·Duplicada·Erro + motivo/erros por linha) + botões
  "Importar N oportunidade(s)" (desactivado se N=0) e "Descartar"; após confirmar,
  card **"Relatório de importação"** (badges Criadas/Duplicadas/Erros/Total +
  "Nova importação"). Coluna lateral "Como importar" (5 passos) + "Pipeline" (copiado
  do create).
- **Entrada no fluxo do §1:** botão **"Importar Excel"** no header de `/pipeline/nova`
  (1º, antes de "Guardar rascunho"/"Criar Oportunidade"); sidebar **sem item novo**
  (menu = 9 itens fixos do §7) mas o submenu Oportunidades abre também em
  `opportunities.import`.
- **Testes:** `OpportunityImportTest` (11) → **63 testes, 300 assertions**; pint passed.
  Validação HTTP manual (cookie de sessão existente):
  `/pipeline/importar` **200** (título, form, "Como importar", "Pré-visualizar",
  "Descarregar template"), `/pipeline/importar/template` **200** (zip `PK`, >5KB,
  cabeçalhos = HEADINGS), `/pipeline/nova` **200** (link `/pipeline/importar`),
  sem cookie → **302**.

---

## 6. PONTOS EM ABERTO (PROMPT §19 — NÃO AVANÇAR SEM RESPOSTA)

- [x] ~~Lista completa e ordem das fases do pipeline~~ → **respondido 2026-10-03:**
  Leads → Visitas → Aprovação → Aprovado / Não Aprovado (fases do protótipo)
- [x] ~~Se "Aprovado/Não Aprovado" são fases ou estados finais~~ → **fases do enum
  `OpportunityStage`** (coluna `fase_atual`), estados finais com regras próprias
  (Aprovado: probabilidade 100 + data_aprovacao; Não Aprovado: probabilidade 0)
- [ ] Se uma oportunidade pode voltar atrás de fase (em aberto; **por omissão está
  livre** — o drag&drop do protótipo deixa arrastar para qualquer coluna)
- [ ] Definição do módulo **Conciliações** → Sprint 6 bloqueado
- [ ] Definição do módulo **Contratos** → Sprint 7 bloqueado
- [ ] Relação dos **Produtos** com oportunidades e contratos
- [ ] Número exacto de dias para lead inativo
- [ ] Comportamento detalhado dos alertas de contratos
- [ ] Automação restante

---

## 7. CHECKLIST POR MÓDULO (PROMPT §18)

Enums → migrations → models → factories → seeders → services → Livewire →
**views copiadas do protótipo** → **Bootstrap mantido** → **visual igual ao protótipo** →
rotas → testes → `./vendor/bin/pint` → teste manual → commit.

---

## 8. COMANDOS

```powershell
# PHP do Herd (obrigatório — o PATH do sistema é PHP 8.2)
$env:PATH = "$env:USERPROFILE\.config\herd\bin;$env:PATH"

php artisan serve --host=127.0.0.1 --port=8099 --no-reload   # servidor (portas 8000/8888 falharam)
npm run dev                                                    # Vite
npm run build                                                  # build produção

php artisan migrate:fresh --seed
php artisan test                                               # 63 testes (Sprint 4)
php vendor/bin/pint                                            # formatação
php vendor/bin/pint --test                                     # verificar sem alterar
php artisan config:clear

# Extração CSS do protótipo (Sprint 1, §4.6 — re-executar se o protótipo mudar)
powershell -ExecutionPolicy Bypass -File ..\tools\extract-prototype-css.ps1

# MySQL (serviço DISABLED — arrancar directamente)
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqld.exe" --defaults-file="C:\ProgramData\MySQL\MySQL Server 8.0\my.ini"
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -e "..."
```

---

## 5F. SPRINT 5 — AGENDA COMERCIAL (Sprint 5, commit `<hash>`)

- **Objetivo:** migrar `apps-tasks.html`, `apps-notes.html`, `apps-calendar.html` do
  protótipo para Blade/Livewire **sem alterar o visual** (§4.4: grelha própria no
  calendário a substituir TUI Calendar).
- **Enums:** `ActivityType` (visita/chamada/reuniao/proposta/cobrança — 5 tipos; labels,
  plurais, cores da legenda `#5485e4`/`#25b865`/`#d13b4c`/`#17a2b8`/`#e49e3d`,
  badges `bg-soft-sky/purple/indigo/teal/danger`, ícones `feather-map-pin/phone/users/file-text/dollar-sign`)
  e `ActivityStatus` (pendente/concluida/cancelada — labels, badges
  `bg-soft-warning/success/secondary`).
- **Migration `activities` (§8.4 + decisões):**
  - `opportunity_id` **não nullable** (regra de negócio: actividade tem de ter cliente).
  - Sem `SoftDeletes` (esquema 8.4); `hora` como `string` `H:i`; uuid automático no `creating`.
- **Model `Activity`:** casts `tipo/status`→enum, `data/proxima_accao_prazo`→date, `hora`→string;
  `horaCurta()` → `08:00`; scopes `pending`/`overdue`/`upcoming`.
- **`ActivityService`:** `create` (user=auth, status default Pendente), `update`, `toggleStatus`
  (Pendente↔Concluida), `setStatus`, `remove` (hard delete).
- **Livewire `Activities\ActivityForm`** + view `activity-form.blade.php` (cópia de `apps-notes.html`):
  - Props: `tipo`, `opportunity_id`, `descricao`, `resultado`, `proxima_accao`,
    `proxima_accao_prazo`, `status`, `data`.
  - `mount()` lê `?atividade={uuid}` para edição (UUID → model via `where('uuid')`).
  - Regras: obrigatórios `tipo`, `opportunity_id`, `descricao`, `proxima_accao`,
    `proxima_accao_prazo`, `data`; `tipo/status` in enum values.
  - `save()` → valida → `ActivityService::create/update` → `redirect(route('agenda'), navigate: true)`.
  - View: estrutura completa `content-sidebar` (apps-notes: "Nova Actividade" +
    filtros Tipo/Estado) + `content-area` (header com dropdown "Nova Actividade"
    → Ver todas/Calendário, botões Lista/Calendário) + body `row` col-lg-8 formulário
    (card "Próxima acção" obrigatório com badge) + col-lg-4 legenda de tipos
    + regras.
- **Livewire `Activities\ActivityList`** + view `activity-list.blade.php` (cópia de `apps-tasks.html`):
  - Filtros `statusFilter`/`typeFilter` via query string (`mount` valida com
    `array_column`); `search` (descricao/proxima_accao/opportunity.nome);
  - `sort` asc/desc; `toggle(id)` → `ActivityService::toggleStatus`;
    `remove(id)` → service + `dispatch('activity-removed')`.
  - `render()` → agrupamento por data com rótulos "Hoje"/"Ontem"/"d M, Y".
  - View: raiz única `d-flex flex-grow-1 x-data={sidebarOpen}`; sidebar Estado
    (Todas/Pendentes/Concluídas/Canceladas + ícones) + Tipo (5); header
    (open-trigger, dropdown Estado, search Alpine `x-show`+`x-cloak`, sort);
    cards colapsáveis por data; itens `single-task-list` com checkbox `wire:click=toggle`,
    link para editar, badge tipo/prazo, dropdown Editar/Eliminar (confirm JS +
    `$wire.remove`); estado vazio com botão Nova Actividade; `<x-ui.footer />`.
- **Livewire `Activities\ActivityCalendar`** + view `activity-calendar.blade.php`
  (cópia de `apps-calendar.html`, grelha própria):
  - Views estáticas PT: Diária/Semanal/Semanas (2)/Semanas (3)/Mensal com ícones
    `feather-list/umbrella/sliders/framer/grid`; `DAY_NAMES` = Dom..Sáb.
  - Estado: `$view`, `$cursor`, `$allVisible` (checkbox "Ver todas"),
    `$types[]` (checkboxes legenda); `updatedAllVisible`/`updatedTypes`
    sincronizam sem ciclos.
  - `range()`: day/week/weeks2/weeks3 = addDays; **month = 6 semanas fixas**
    (`isAlways6Week` do TUI: `startOfMonth()->startOfWeek(Sunday)` + 41 dias).
  - `days()`, `rangeLabel()` com formatos TUI exactos: `d.m.y` (day/month) e
    `d/m/Y ~ d/m/Y` (week/weeks2/weeks3).
  - Eventos filtrados por `$types` no range; `upcoming` = próximas 4 (sem filtro
    de tipo, igual ao protótipo).
  - View: sidebar `lnb-calendars` (checkbox allVisible + legenda tipos com
    `tui-full-calendar-checkbox-round` + `background-color` dynamic via
    `in_array($tipo->value, $types)` + eventos próximos `schedule-item`); header
    (open-trigger, dropdown views com ícones, botão Hoje, `#renderRange`,
    prev/next); grid CSS `agenda-calendar` (`--agenda-cols`), chips `agenda-event`
    (`--agenda-event-color: $tipo->color()`, `border-left:3px solid rgba(0,0,0,.2)`,
    tempo + título), `+N` (`.weekday-grid-more-schedules`); footer.
- **CSS nova agenda** (`adg/resources/css/app.css`): `.content-area` flex col
  (header sticky, body `flex:1 1 auto`, footer shrink) + grelha
  `.agenda-calendar*` + `.agenda-event` + `.agenda-day-num` + utilitários
  `.text-teal/.text-purple/.text-indigo` (extraídos do theme.min.css).
- **Extracção CSS §4.6 (actualizada):** `$themeKeep` alargado
  (`\.text-teal|\.text-purple|\.text-indigo`, `\.calendar-`, `\.menu-navi`,
  `\.single-task-list`, `(^a\b|a:hover|a:not\(|^:focus$|^b$|^strong$)`) para
  capturar regras de links globais `a{color:#283c50...}` e utilitários `b,strong`.
  Novo `$tuiKeep` para `tui-theme.min.css` (`\.lnb-|#lnb\b|#renderRange|\.calendar-icon|...`
  `\.tui-full-calendar-checkbox-round`) → gera `tui-theme-extract.css` importado
  em `app.css` depois de `theme-extract.css`.
- **Vistas Blade:**
  - `agenda/index.blade.php` → `@livewire('activities.activity-list')`
    (`containerClass="apps-container apps-tasks" contentClass="without-header nxl-full-content" mainClass="d-flex" hidePageHeader hideFooter`).
  - `agenda/create.blade.php` → `@livewire('activities.activity-form')` (mesmo container `apps-notes`).
  - `agenda/calendar.blade.php` → `@livewire('activities.activity-calendar')` (container `apps-calendar`).
- **Rotas** (`web.php`): `agenda` (GET), `agenda/nova` (GET — **antes** de qualquer
  rota parametrizada), `agenda/calendario` (GET); middleware `auth,verified`.
- **Sidebar:** item "Agenda Comercial" transformado em **acordeão** (igual a
  Oportunidades) com sub-opções: Calendário, Actividades, Actividade (ordem do
  protótipo); abre automaticamente nas rotas `agenda*`.
- **`ActivitySeeder`:** 10 actividades de demo baseadas nos cards de `apps-tasks.html`
  (Videoconferência Kinaxixi, Reunião objectivos, Análise tendências, Enviar proposta,
  Marcar demonstração, Visita técnica, Proposta cimento, Cobrança 2.ª prestação,
  Visita acompanhamento, Reunião fecho obra) distribuídas em Hoje/Ontem/−2/−1/+1/+3/+5.
- **Testes:** `AgendaTest` (20) — auth redirects, sidebar acordeão, list structure,
  empty state, filtros status/tipo via query string, inválidos ignorados, search,
  toggle/remove, create page structure, edição via `?atividade=`, validação
  obrigatórios (cliente, descrição, próxima acção), criação pendente, calendar
  structure, grelha 42 células (6 semanas), troca de views (day=1, week=7,
  weeks2=14, month=42), fallback bogus→month, eventos no grid, filtros tipo
  (`set('types')`/`set('allVisible')` — não se chama `updatedTypes` directamente
  por bloqueio Livewire 3).
- **Validação:** `pint --test` **passed**; `npm run build` **OK** (warnings
  benignos `a::hover` do theme.min + svgs flags); `php artisan test` → **83 testes,
  399 assertions, 0 falhas**; HTTP manual (8099): `/agenda`/`agenda/nova`/`agenda/calendario`
  302 sem auth (correto), 200 com auth (validado via test suite).

---

## HISTÓRICO DESTE DOCUMENTO

| Data | Alteração |
|---|---|
| 2026-10-03 | **Reescrito do zero** — histórico git apagado, `crm-pessoal/` eliminado, Sprint 0 cumprido em `adg/` conforme PROMPT_MASTER |
| 2026-10-03 | **Sprint 1 concluído** (`0aa7f8d`) — enums, auth por username, layouts do protótipo, extração CSS (§4.6), menu agrupado, header simplificado; 26 testes a passar |
| 2026-10-03 | **Sprint 1.1 concluído** (`9066d59`) — 10 componentes Blade extraídos do protótipo + galeria `/ui` + `UiComponentsTest`; 28 testes |
| 2026-10-03 | **Sprint 2 concluído** (`b27ba30`) — pipeline kanban 5 fases com drag&drop, stats, schema +titulo/probabilidade/data_fecho (decisão do utilizador), fases confirmadas no §19; 37 testes |
| 2026-10-04 | **Sprint 3 concluído** (`91d3514`) — create/view do leads-create/leads-view, tabela `visit_reports` + relatório de visita, rotas `opportunities.create/show`, sidebar "Nova Oportunidade" activa, cartões do kanban clicáveis; 52 testes |
| 2026-10-04 | **Sprint 4 concluído** (`6fb0330`) — importação Excel (maatwebsite/excel 4): template, preview com validação, duplicados (BD+ficheiro), relatório; entrada "Importar Excel" na Nova Oportunidade; 63 testes |
| 2026-10-04 | **Sprint 5 concluído** (`49c2225`) — Agenda Comercial: apps-tasks/apps-notes/apps-calendar migrados para Livewire; ActivityType/Status enums, Activity model/service, ActivityList/ActivityForm/ActivityCalendar + views, rotas agenda/agenda.nova/agenda.calendario, sidebar acordeão, grelha agenda-calendar (6 semanas), CSS + extração tui-theme, ActivitySeeder, 20 testes AgendaTest; suite 83 testes, pint, build OK |
| 2026-10-05 | **Plano criado** (`plano.md`) — Roadmap actualizado conforme `resposta.md` v4.0: ajustes Sprint 2/3 (9 fases, novos campos), Sprint 6 Conciliações, Sprint 7 Contratos, Sprint 8 Produtos, Sprint 9 Comerciais, Sprint 10 Alertas, Sprint 11 Dashboard, Sprint 12 Lançamento. |
| 2026-10-05 | **Sprints 2/3 ajustados** (`839329b`) — Pipeline 9 fases (lead→qualificação→visita→proposta/negociação→aprovação→bilhete→resultado→cliente_aprovado/nao_aprovado), OpportunityStage enum actualizado, novos campos Opportunity (pessoa_contacto, tipo_relacao, comercial_id, produto_id, sector, origem), tabelas commercials + products + pivot, OpportunityService estados finais CLIENTE_APROVADO/NAO_APROVADO, Pipeline 9 colunas, Create form expandido, Stats actualizado, ExcelImportService 9 fases; 83 testes, 410 assertions, pint, build OK |
| 2026-10-05 | **Sprint 6 concluído** (`cbdde3d`) — Conciliações: enum ConciliactionClassificacao, migration conciliactions, Model/Factory/Seeder (10 registos), Service, Livewire List/Form, views baseadas em payment.html, rotas conciliações/nova/{uuid}, sidebar acordeão; 83 testes, 410 assertions, pint, build OK |
| 2026-10-05 | **Sprint 7 concluído** (`abda4e2`) — Contratos: enum ContractStatus, migration contracts (FK opportunity/commercial/product), Model/Factory/Seeder (10 registos), Service, Livewire List/Form/View (overview/history/documents tabs), views baseadas em projects*.html, rotas contratos/nova/{uuid}, sidebar acordeão; 83 testes, 410 assertions, pint, build OK |
