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
| Sprint 8 — Produtos | ✅ Concluído | `fc419f8` | Livewire ProductList/ProductForm, views baseadas em customers.html, rotas produtos/produtos.nova/produtos.{produto}, sidebar acordeão; Model/Factory/Seeder já existiam |
| Sprint 9 — Comerciais | ✅ Concluído | `75f06b2` | Livewire CommercialList/CommercialForm, views baseadas no padrão, rotas comerciais/comerciais.nova/comerciais.{commercial}, sidebar accordion; Model/Factory/Seeder já existiam |
| Sprint 10 — Alertas | ✅ Concluído | `5ea436a` | Enum AlertType, migration alerts, Model/Factory/Seeder, AlertService (gera 5 tipos, não move leads), command alerts:generate (daily 06:00), NewAlert notification, Livewire AlertList, header badge + dropdown, sidebar accordion; 83 testes suite |
| Sprint 11 — Dashboard + Painel Executivo | ✅ Concluído | `9531418` | Dashboard (4 KPIs + charts Chart.js), Analytics (Painel Executivo com gráficos e tabelas), Chart.js, migration user_id, rotas dashboard/analytics, sidebar accordion; 83 testes suite |
| Sprint 12 — Testes e Lançamento | ✅ Concluído | `hash` | spatie/laravel-backup instalado e configurado, schedule de backup/cleanup/monitor, log channel backup, SSL preparado, testes finais 83 passed |

**Próximo sprint a executar:** Projecto concluído — pronto para deploy em produção.
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
| Interacções | **Alpine.js** (sem jQuery; instância única — a do Livewire) | 3.4.2 | ✅ |
| Build | **Vite 7** (`laravel-vite-plugin@2`) | 7.3.6 | ✅ |
| Testes | **PHPUnit** | 11.5.56 | ✅ |
| Queue/Session/Cache | database | — | ✅ |
| Excel / Gráficos / Backup | **maatwebsite/excel** 4 (S4) + **ApexCharts** 7.8 (protótipo) + **spatie/laravel-backup** (S12) | 4.0 / 7.8 | ✅ (gráficos: ApexCharts, §4 sobreposto pelo protótipo §22) |

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
- **`npm run build` logo após `artisan view:clear` gera CSS incompleto** — o app.css tem
  `@source '../../storage/framework/views/*.php'` e, sem views compiladas, o Tailwind
  perde ~18 KB de utilitários (639 KB em vez de 657 KB). Ordem segura: compilar views
  primeiro (correr a app/tests) e build no fim; se acontecer, repetir o build.

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

`adg/resources/js/app.js`: `bootstrap` (bundle c/ Popper) + `./apexcharts-init` +
`Alpine.data('sidebar')` registado **em `document.addEventListener('alpine:init')`**
(só pode haver UMA instância de Alpine — é a do Livewire; um `Alpine.start()` próprio
causava "Detected multiple instances of Alpine running" e directivos avaliados 2×;
substitui o jQuery `minimenu`/`mob-navigation-active` do protótipo; localStorage igual:
key `nexel-classic-dashboard-menu-mini-theme` = `menu-mini-theme`).
Os layouts app/guest declaram `@livewireStyles`/`@livewireScripts` explícitos (a
auto-inject do Livewire só injecta quando a página tem componente Livewire — `/dashboard`
e `/ui` não têm e ficariam sem Alpine).
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
- ~~Opcional: confirmar com utilizador que a BD `adg` substitui a antiga `crm_pessoal`~~
  — **confirmado 2026-10-09**: `adg` é a BD oficial; `crm_pessoal` pode ser eliminada
- ~~`.env.example` ajustado a MySQL~~ — **feito 2026-10-09** (`DB_CONNECTION=mysql`, `DB_DATABASE=adg`)

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

## REVISÃO GERAL — ÍCONES, ERROS E ESTRUTURA (2026-10-06)

Pedido: *"tem muitos erros... procure pelos erros em todas as áreas"*, *"o sistema
não carrega nenhum ícone"*, *"a estrutura está desorganizada e desestruturada,
verifique bem com o protótipo"*.

### Ícones (bug raiz)
- **`tools/extract-prototype-css.ps1`** tinha 2 bugs: (1) `Parse-Css` não guardava
  `Body` nos nós `@` → todos os `@font-face` saíam vazios (`@font-face{}`); (2)
  `$vendorsKeep` não casava o selector `i` → a regra base `i{...font-family:feather}`
  era descartada, deixando as classes `.feather-*` só com `content`.
- Também corrigido: `Trim(" '", '"')` → `Trim([char[]]@(' ', "'", '"'))`; novos
  `Rewrite-Urls($text, $srcDir)` + `Resolve-WebPath` (o `../` não era capturado e
  produzia `/assets/vendors/css/img/...`); `$vendorsKeep` ganhou `^i$`.
- Extração re-executada: 12 `@font-face` preenchidos, 271–282 `.feather-*:before`,
  urls `/fonts/...` e `/assets/vendors/img/flags/...`. `npm run build` → CSS com
  `@font-face{font-family:feather;src:url(/fonts/...)}` e `i{...font-family:feather}`;
  `public/build/fonts` (hack antigo) já não é preciso — caminhos absolutos `/fonts/`.
- HTTP: `/fonts/feather.woff`, `/fonts/bootstrap-icons.woff2`,
  `/fonts/fa-solid-900.woff2` e `/assets/vendors/img/flags/4x3/bi.svg` → **200**.

### Erros de runtime encontrados e corrigidos
- `/analytics` rebentava com **7 × Undefined variable** (`$vendasYTD`, `$ponderado`,
  …) — a view usava variáveis nunca passadas. Criado **`app/Services/DashboardService.php`**
  (exigido pelo PROMPT_MASTER §10) com `kpis()/charts()/tables()` partilhados; `Dashboard`
  e `Analytics` ficaram finos (`view([...$service->kpis(), 'charts' => …, …$service->tables()])`).
- `Vendas YTD` lia `$valorPipeline` e `Pipeline Ponderado` lia `$ponderado` →
  passaram a `$vendasYTD` / `$pipelinePonderado`.
- **Gráficos**: começou com `resources/js/charts.js` (Chart.js, resposta.md §4) mas o
  utilizador exigiu fidelidade ao protótipo (§22) → **Chart.js removido, ApexCharts 7.8
  instalado**; `resources/js/apexcharts-init.js` lê `data-adg-chart='{type:"area"|"donut"|
  "small-area",...}'` das divs (idênticas ao `index.html`/`analytics.html`) e destrói
  instâncias órfãs em `livewire:navigated`; os small charts usam `window.adgSmallCharts`
  (JSON no blade); `app.js` importa `./apexcharts-init`; `charts.js` apagado (código morto).
- **`route('contracts.edit')` não existia** → `/contratos` dava 500 assim que havia
  ≥ 1 contrato; criada a rota `contratos/{contract}/editar`.
- **`Route::view('contratos/{contract}')` passava uma string** →
  `contracts/show.blade.php` rebentava com `Attempt to read property "numero_contrato"
  on string`; convertida para `Route::get` com binding implícito de `Contract`.
- **4 formulários rebentavam** com `Undefined variable $statusFilter`/`$classificacaoFilter`:
  `product-form`, `commercial-form`, `contract-form` e `conciliaction-form` traziam os
  blocos de filtro copiados da lista; `customers-create.html`/`projects-create.html`
  **não têm** `content-sidebar` com filtros → removidos (crash + fidelidade ao protótipo).
- **"Editar" não preenchia o formulário**: `*/edit.blade.php` não passava o registo ao
  componente — o `mount()` lia `?produto=`/`?contract=` do query string, que as rotas
  nunca geram. Agora as views passam `['produto' => $produto]` (etc.) e os `mount()`
  aceitam o parâmetro opcional, mantendo o fallback ao query string.
- **Sino do header**: `href="{{ route('alerts') }}"` com `data-bs-toggle="dropdown"`
  fazia a página navegar antes de abrir (o protótipo usa `href="#"`) → `href="javascript:void(0);"`.
- **"Marcar todas como lidas"** só navegava para `/alertas` → nova rota
  `POST alertas/marcar-todas` (`alerts.markAll`) com `<form>` que marca `lido = true`
  apenas dos alertas do utilizador autenticado.
- `x-cloak"` malformado ×3 no `sidebar.blade.php`; `wire:key=\"..."` dentro do atributo
  `class` em 5 `*-list.blade.php` (contract/alert/commercial/conciliaction/product)
  → `wire:key` como atributo separado.

### Estrutura / código morto
- Auditoria automática de **todas as `route()` usadas** (views + app + routes) contra
  `route:list` → **todas resolvem**.
- Removido código morto do Breeze que apontava para rotas inexistentes
  (`password.email`, `profile.update`, `profile.edit`, `verification.send`):
  7 `App\Http\Controllers\Auth\*` (fica `VerifyEmailController`), `ProfileController`,
  `resources/views/auth/*.blade.php`, `resources/views/profile/edit.blade.php`,
  `resources/views/profile/partials/*` e `resources/views/layouts/navigation.blade.php`.
- Sidebar confirmada contra `resposta.md` §7 (8 acordeões, "Nova Oportunidade" antes de
  "Pipeline") — estava correcta; `PROMPT_MASTER` §7 diverge e foi sobreposto.
- Mantidos **4 KPI cards** (decisão registada em `plano.md:115`) apesar dos 6 blocos
  do Sprint 11.

### Testes
- **+30 testes** nos módulos que não tinham nenhum — foi por isso que os crashes
  passaram desperbidos: `DashboardTest` (dashboard/analytics, KPIs, payload JSON dos 8
  gráficos), `AlertsTest` (lista, marcação global, dropdown do header), `ContractsTest`,
  `ProductsTest`, `CommercialsTest`, `ConciliactionsTest` (auth, lista, create,
  show/edit com prefill, sidebar).
- **Validação:** `php artisan test` → **113 testes, 580 assertions, 0 falhas**
  (era 83/399); `vendor/bin/pint --test` → **passed**; `npm run build` → OK
  (`app-*.css` 657 KB / `app-*.js` 1117 KB com ApexCharts); HTTP 8099 → `/login` 200,
  `/dashboard` 302→login, CSS/JS/fontes 200.

### Pipeline Drag & Drop + Loading State (2026-10-08)
- **Problema (consola)**: `Alpine Expression Error: Unexpected token ')'` em todos os
  `x-on:drop.prevent` dos cartões + `Detected multiple instances of Alpine running`.
  **Causa raiz**: `resources/js/app.js` importava e arrancava um Alpine próprio
  (`Alpine.start()`) enquanto o `livewire.js` traz o seu — o DOM era avaliado DUAS vezes
  (handlers de drop duplicados, `move` disparado 2× por drop) e as expressões do drop
  interpolavam Blade dentro de JS inline (`id: {{ $opportunity->id }}`, `stage: '{{ $stage }}'`).
- **Correcção (estado final)**:
  - `resources/js/app.js`: Alpine próprio **removido**; o componente `sidebar` passou a
    registar-se em `document.addEventListener('alpine:init')` sobre `window.Alpine`
    (o do Livewire), disparado ANTES de o Alpine percorrer o DOM.
  - `layouts/app.blade.php` + `guest.blade.php`: `@livewireStyles` (head) e
    `@livewireScripts` (fim do body) **explícitos** — a auto-inject do Livewire só
    injecta se a página renderizou ≥ 1 componente Livewire; `/dashboard` e `/ui` não têm
    e ficariam sem Alpine (sidebar/submenus `x-cloak` presos). Com as tags, todas as
    páginas servem **exactamente 1× livewire.js + 1× app.js**.
  - `pipeline.blade.php` reescrito **sem interpolação JS em expressões Alpine**:
    - board: `x-data` com `loadingId` + `async drop(event, stage)` →
      `await $wire.$call('move', id, stage)` dentro de `try/finally` (loading limpo
      mesmo com erro); id lido do `dataTransfer`, guardado como string;
    - coluna: `x-on:drop.prevent="$el.classList.remove('drag-over'); drop($event, $el.dataset.dropzone)"`
      (único handler de drop — o cartão deixou de ter um, eliminando o dispatch duplo);
    - cartão: `data-opp="{{ $opportunity->id }}"` (Blade só em atributos HTML) +
      `x-on:dragstart` via `$el.dataset.opp` + `:class="{ 'opacity-50': loadingId === $el.dataset.opp, … }"`;
    - spinner `x-show="loadingId === $el.dataset.opp"` com `data-opp` próprio → "A mover…"
      só no cartão exacto enquanto o pedido está em curso.
  - `Pipeline.php`: removidos `$listeners = ['kanbanMove' => 'move']` e `$movingId`
    (mortos — nada despacha `kanban-move`); `move()` continua a mudar a fase via
    `OpportunityService::changeStage()` e a despachar `opportunity-moved`.
- **Verificação (mecânica, não visual)**:
  - 113 testes / 580 assertions a passar; pint passed; build OK (`app-*.js` 1117 KB).
  - Páginas servidas (teste temporário que fez dump e foi apagado): 1× livewire.js,
    1× app.js, 1× estilos Livewire, `x-data="sidebar"` presente.
  - 20 expressões Alpine extraídas do HTML servido → **parse OK** com o wrapper exacto
    do Alpine (`new AsyncFunction('__self','scope','with (scope) { __self.result = … }')`).
  - **Teste jsdom + Alpine real** (`alpinetest/test-dnd.mjs`, 12 asserções, ALL PASS):
    1 chamada `$wire.$call('move', 7, 'qualificacao')` por drop (sem duplicação),
    spinner/opacity durante o pedido e limpos na resposta, `drag-over` removido,
    drop sem id ignorado. Requer `npm install jsdom` numa pasta temporária.

### Correcção: kanban em vertical + loading preso nos cartões (2026-10-08)
- **Sintomas reportados**: (1) "loop no loading nos card" (spinner "A mover…"
  permanente); (2) "todos eles estão alinhados na vertical" (colunas em vez de
  estarem lado a lado).
- **Causa raiz**: `<div class="kanban-card">` (linha 67) **nunca tinha fecho** — a
  correcção anterior que envolveu o conteúdo do cartão em `<div class="position-relative">`
  (para servir de contexto de posicionamento ao overlay) + overlay +1 abertura mas o
  `</div>` final fechava só o `position-relative`. Faltava **um `</div>` por cartão**:
  - cartões aninhavam uns nos outros → as colunas seguintes acabavam por nascer
    DENTRO dos cartões → toda a estrutura colapsava numa única cadeia vertical;
  - o DOM malformado fazia o morph do Livewire falhar/travar no `move()` → a promise
    de `$wire.$call` nunca assentava → o `finally` nunca corria → spinner preso.
- **Correcção**: `pipeline.blade.php` — acrescentado o `</div>` de fecho do cartão
  depois do `position-relative` (estrutura: `card > position-relative > [card-body,
  overlay]`).
- **Validação (jsdom + Alpine real sobre o HTML renderizado com 30 oportunidades —
  `alpinetest/validate-real.mjs`, 14 asserções, ALL PASS)**: div balanceado no crudo
  (346/346), 0 cartões aninhados, 30 cartões filhos directos de `.kanban-col-body`,
  9 colunas filhas directas do board, board `d-flex gap-3`, 30 spinners escondidos no
  arranque, 1× `$wire.$call('move')` por drop, spinner visível só no cartão arrastado
  e limpo na resposta. 113 testes/580 assertions, pint, build estável (`app-DRY1tIMU.css`
  657.33 KB).

### Correcções de consola + sparklines do dashboard (2026-10-08)
- **Aviso `undefined color - ApexCharts` (fillPath)**: `DashboardService` construía
  `faturacaoMensalPayload` **sem `color`** → `salesAreaConfig` passava
  `colors: [undefined]`. Correcção: payload com `'color' => self::SALES_COLOR`
  (`#3454d1`, igual ao protótipo `dashboard-init.min.js`) + default defensivo
  `payload.color || '#3454d1'` em `apexcharts-init.js`.
- **3 sparklines do dashboard não renderizavam** (`taskCompletedChart`,
  `newTasksChart`, `projectDoneChart`): `dashboard.blade.php` fazia
  `@push('scripts')` mas **não existia nenhum `@stack()`** no projecto → script nunca
  era emitido (`window.adgSmallCharts` indefinido). Acrescentado
  `@stack('scripts')` em `layouts/app.blade.php` (antes de `@livewireScripts`;
  os inline scripts correm no parse, o `app.js` (module) depois → ordem garantida).
- **`Unexpected token '&'`**: o script pushado usava `{{ json_encode(...) }}` que
  escapa `"` para `&quot;` dentro de `<script>` → trocado por `@json(...)`.
  (Nos atributos `data-adg-chart="{{ json_encode(...) }}"` o escaping é correcto —
  o browser decodifica as entidades no `dataset`.)
- **Validação E2E (Chrome real + puppeteer-core, `browsertest/e2e-*.mjs`)**: login →
  dashboard 4 gráficos com SVG, analytics 4 gráficos, **0 avisos `undefined color`,
  0 erros de consola**; fluxo completo (`e2e-flow.mjs`): wire:navigate, 2 drags reais,
  filtro, pesquisa — sempre 0 spinners visíveis e loading limpo. 113 testes/580
  assertions; build `app-DMLx1amv.js` / `app-DRY1tIMU.css` 657.33 KB.

### Loading dos cartões do kanban REMOVIDO (2026-10-08 — pedido do utilizador)
- **Pedido**: "em pipeline os cards estão com loop de loading, remova o loading nos
  card" — apesar de o problema não se reproduzir em browser limpo (validado E2E),
  o utilizador pediu a remoção total.
- **Removido** de `pipeline.blade.php`: overlay spinner "A mover…", `loadingId` +
  `async/try/finally` do board, binding `:class` com `opacity-50` e o wrapper
  `position-relative` (só existia para o overlay) → estrutura do cartão volta ao
  original do protótipo (`kanban-card > card-body`). O drop ficou simples:
  `drop(event, stage)` → valida id → `$wire.$call('move', id, stage)`.
- **Validação**: jsdom `validate-real.mjs` **16/16 PASS** (divs 196/196, 0 aninhados,
  0 elementos `x-show`, 1× `$call('move')` por drop); 113 testes/580 assertions;
  Chrome real `e2e-noloading.mjs` **10/10 PASS** (0 x-show/0 spinners/0 "A mover"
  no kanban, drag muda a coluna com `maxVisibleDuring=0`, cartão reposto, **consola
  com 0 mensagens**); build `app-Bb8PbBCS.css` 657.27 KB + `app-DMLx1amv.js`.

### Dashboard: 3 mini-cards em PT + gráficos finalmente visíveis (2026-10-08)
- **Pedido do utilizador**: títulos/descrições em português, "não aparece nenhum
  gráfico", descrição dos cards a aparecer FORA do card, e o Task Completed separado
  dos outros dois → alinhar os 3 juntos. Decisões: Facturação full-width; prioridade
  = descrição dentro do card.
- **Layout** (`dashboard.blade.php`): Facturação Mensal passou a `col-xxl-12`; o Task
  Completed saiu da row da Facturação; nova row única com **3 × `col-lg-4`** iguais
  ao protótipo `[Mini]` (cartões `mb-4 stretch stretch-full`).
- **Estrutura PT do protótipo**: `card-header` com ícone (`feather-star` /
  `feather-file-text` / `feather-airplay`) + título + subtítulo X/Y à esquerda e
  contador grande à direita; títulos **"Actividades Concluídas" / "Novas
  Actividades" / "Encomendas Entregues"**; descrição "28%/34%/42% mais" +
  **"desde a semana passada"** (antes "vs mês anterior").
- **Descrição dentro do card**: os divs dos charts tinham `style="width: 100%"` que,
  somado ao `text-nowrap` da descrição, fazia overflow de ~54px para fora do card.
  Substituído por `flex: 1 1 auto; min-width: 0` → o chart ocupa só o espaço
  restante e a descrição fica sempre dentro (`descDentroDoCard: true` nos 3).
- **BUG RAIZ dos gráficos vazios** (pré-existente desde o Sprint 11): em
  `apexcharts-init.js`, `salesAreaConfig`/`smallAreaConfig` faziam
  `series: [{name, data: payload.series}]`, mas `payload.series` do
  `DashboardService` **já era** `[{name, data:[…]}]` → o Apex recebia
  `data = [object]` = **1 ponto** → y-axis default 0–6, uma única label
  ("Nov/2025") e caminho degenerado `M 0 230` — o SVG existia mas o gráfico era
  invisível (o mesmo acontecia à Facturação). Corrigido com helper `toSeries()`
  (aceita `[{name,data}]` do `dashboardData` **e** `[n1,n2,…]` do `analyticsData`,
  que eram formatos inconsistentes) + `yaxis.labels.formatter` compacto M/K.
- **Contadores X/Y** (`DashboardService`, chave `minis`): concluídas/total,
  criadas-30-dias/total, entregues (`data_fim < hoje`, não cancelados)/total. O
  gráfico "Encomendas Entregues" passou a contar **entregues por mês (`data_fim`)**
  em vez de renovados por `updated_at` — alinhado com o título do protótipo.
- **Dados demo históricos** (era a 2ª causa de gráficos vazios): novo
  `DashboardDemoSeeder` (idempotente, marca `[demo-hist]`): 25 actividades
  distribuídas pelos últimos 6 meses (17 concluídas, `proxima_accao_prazo` NULL para
  não poluir "Pendências Hoje") + 12 aprovações com `data_aprovacao` mensal
  (facturação com 12 pontos de 6M a 71M). `ContractSeeder` corrigido: os nomes de
  produto já não existiam no catálogo (fallback para qualquer produto) → 9 contratos
  (5 com `data_fim` no passado = entregues, estados expirado/renovado/cancelado) —
  footer da Facturação passou a mostrar 15M/33.5M/2.2M/50.7M em vez de zeros.
- **Validação**: `e2e-measure.mjs` — 3 cards **mesma linha** (y=973),
  `descDentroDoCard: true` ×3, títulos PT ✓, séries 17/35/7, Facturação
  `col-xxl-12` ✓; `e2e-charts.mjs` — dashboard 4 + analytics 4 gráficos, 0 avisos/0
  erros de consola; probe DOM — paths reais (facturação `dLen=1274` w=1109; minis
  w=164); screenshot final confirma visual do protótipo; DashboardTest 4/4 (títulos
  PT), suite **113 testes/580 assertions**, pint OK, build `app-Bb8PbBCS.css`
  657.27 KB + `app-DMLx1amv.js`.

### Painel Executivo: header único, KPIs sem overflow, charts reordenados (2026-10-08)
- **Pedido do utilizador**: (1) remover o header duplicado com o badge "Somente
  leitura"; (2) valores em dinheiro vazavam dos KPI cards quando maiores; (3) repetir
  a correcção do dashboard — "Estado das Encomendas" para a row de baixo e "Vendas
  Mensais" full-width.
- **Header**: `analytics.blade.php` tinha um `content-area-header sticky-top` próprio
  (igual ao do layout `x-ui.page-header` → 2 headers "Painel Executivo"). Removido o
  bloco da blade; o badge **"Somente leitura"** passou para `<x-slot
  name="pageHeaderRight">` em `analytics/index.blade.php` (padrão já usado em
  `opportunities/*`) → **1 header** com título + breadcrumb + badge (igual ao
  protótipo, que só tem um `.page-header`).
- **KPIs**: `formatKz` completo ("Kz 305.200.000", 14 chars) não cabe em `col-xxl-3`
  a ~1400–1600px (avatar-lg + gap-4 + padding ≈ 120px de chrome num card de 232–282px)
  e fazia wrap feio ("Kz" numa linha). Correcção dupla: novo
  `OpportunityService::formatKzCompact()` — `<1M` completo, `1–999M` com 1 decimal
  ("Kz 305,2M"), `≥1G` em M inteiro ("Kz 1.503M"; precedente: footer da Facturação já
  usava "15M/33.5M") — e classe `.kpi-money` no `app.css` com
  `clamp(15px, 1.35vw, 22px)` + `white-space: nowrap` (fonte adaptativa ao viewport).
  "Encomendas em Curso" ("6") mantém `fs-4`.
- **Charts**: `execSalesChart` passou de `col-xxl-8 col-xl-7` → **`col-xxl-12`**;
  "Estado das Encomendas" saiu da row de cima e juntou-se a "Pipeline por Estado" +
  "Clientes por Segmento" numa row única **3 × `col-xxl-4 col-md-6`**.
- **Bónus — donuts com % erradas (741%/123%/554%)**: o `dataLabels.formatter` dividia
  o valor pelo `seriesTotals` outra vez, mas o Apex 7.x **já passa a percentagem**
  (6/9=66,67 → 66,67/9×100=741). Passou a `Math.round(val)+'%'` → 67/11/11/11,
  20/20/16/28/4/4/8 e 94/6 (somas 100%).
- **Validação E2E** (`e2e-analytics-after.mjs`, 1600 **e** 1400px): 1 `.page-header` +
  0 `.content-area-header` + badge ×1; KPIs `valorVazaDoCard:false`,
  `bodyOverflowX:false` (280/280 e 230/230) nos 4 cards; `execSalesChart` `col-xxl-12`
  (w=1148/948); `donutsY=[847,847,847]`/`[863,863,863]` (mesmo y); consola **0
  mensagens**; screenshot `analytics-after.png` confirma visual. DashboardTest 4/4,
  suite **113 testes/580 assertions**, pint OK, build `app-BZlEFgV4.css`
  657.33 KB + `app-CqMpB9BH.js` 1,117.60 kB.

### Agenda Comercial: 3 bugs raiz, menu e filtros no header (2026-10-08)
- **Pedido do utilizador**: (1) menu com 2 opções redundantes "Actividades"/"Actividade";
  (2) "ao criar não salva nada" (nunca via o registo na lista); (3) filtros por tipo/estado
  "não funcionam"; (4) substituir o painel de filtros esquerdo de 300px (sobrepunha-se ao
  conteúdo/footer em <1200px) por dropdowns no header — só na Agenda.
- **BUG RAIZ #1 — itens da lista INVISÍVEIS (o utilizador tinha razão sem o saber)**:
  o Tailwind 4 gera a utility `.collapse{visibility:collapse}` porque a classe `collapse`
  aparece nas views, **colidindo com a `.collapse` do Bootstrap** (accordion/collapse).
  Todos os `.card-body.collapse.show` ficavam com `visibility: collapse` → os 36 itens
  ocupavam espaço mas não se viam (`innerText` vazio, `innerHTML` com o texto) — por isso
  parecia que "criar não guardava" e que "os filtros não faziam nada" (a lógica funcionava).
  O registo de prova era o id 11 "fdgdg" (gravado na BD às 02:57, invisível na lista).
  Fix em `app.css` **fora de `@layer`** (ganha na cascata sobre a utility): `.collapse
  { visibility: visible; }` — o `display:none` do Bootstrap não é tocado. Efeito lateral
  positivo: accordion do Pipeline (`pipeline.blade.php:29`) também estava invisível.
- **BUG RAIZ #2 — cada cartão de grupo com 7514px**: `.stretch.stretch-full` do
  theme-extract tem `height: calc(100% - 24px)`; com N grupos o 100% resolvia contra a
  soma do próprio contentor → 29 cartões × 7514px = 218.597px de scroll (29 grupos).
  No protótipo os cartões são ~896px (contentor com altura definida). Fix escopado:
  `.apps-tasks .content-area-body > .card.stretch { height: auto; }` → alturas naturais
  (214/305/397px), scroll 7731px.
- **Menu** (`sidebar.blade.php:63`): 3º item "Actividade" → **"Nova Actividade"**
  (Calendário / Actividades / Nova Actividade).
- **Lista** (`activity-list.blade.php`): removido o `content-sidebar` + gatilho hambúrguer;
  header com **dropdown "Filtros"** (padrão `product-list`) com grupos etiquetados
  **Estado** ("Todos os estados" + Pendentes/Concluídas/Canceladas) e **Tipo** ("Todos os
  tipos" + Visitas/Chamadas/Reuniões/Propostas/Cobranças) e botão **"Nova Actividade"**;
  pesquisa/ordenação mantidos. Não existe "subcategoria" no projecto — a confusão vinha do
  painel sem legenda no grupo de estados; agora todos os grupos têm etiqueta.
- **Formulário** (`activity-form.blade.php`): removido o `content-sidebar` (os links
  "Todas/Tipo/Estado" navegavam para fora do form e perdiam dados preenchidos); adicionado
  **resumo de erros de validação** no topo (`.alert-danger`) + botão com `wire:loading`
  ("A guardar…"); header Lista/Calendário passou de `page-header-right-items` (painel
  off-canvas do protótipo que aqui renderizava empilhado) para `hstack gap-2` (lado a lado).
- **Feedback de criação**: `ActivityForm::save()` faz `session()->flash('actividade_criada')`
  antes do redirect → a lista mostra aviso "Actividade guardada com sucesso" (dismissável).
- **Calendário** (`activity-calendar.blade.php`): removido o `content-sidebar`; filtros de
  tipo → **dropdown "Tipos"** (checkbox "Ver todas as actividades" + legenda com checkboxes
  `wire:model.live="types"`, `data-bs-auto-close="outside"`); "Eventos e Agendamentos" →
  **dropdown "Próximos"** (lista scrollável com data/cliente/próxima acção); botão
  "Nova Actividade" no header.
- **Testes**: `AgendaTest` actualizado (menu `>Nova Actividade</a>`, lista
  `assertDontSee('content-sidebar')` + legendas "Todos os estados/tipos", calendário
  `id="viewAllSchedules"`/`Próximos`, flash `actividade_criada`).
- **Validação E2E** (`e2e-agenda-after.mjs`, Chrome real, 0 erros de consola): lista com
  `visibility: visible`, 36 itens, fdgdg/Benguela no `innerText`; filtros Visitas → 2 itens
  (fdgdg esconde) → "Todos os tipos" → 36; criar "TESTE E2E" → redirect `/agenda` + alerta
  de sucesso + registo visível (37) → eliminar → 36; form sem sidebar, 44 clientes, datas
  OK; validação sem required → resumo "Não foi possível guardar:…"; calendário 42 células,
  15 eventos, dropdowns Tipos (6 opções)/Próximos (3 itens). Suite **113 testes/585
  assertions**, pint OK, build `app-CJdTR_nW.css` + `app-CqMpB9BH.js`.

---

### Calendário da Agenda: modal de detalhes do evento (2026-10-08)

**Pedido**: clicar num evento do Calendário abria o formulário "Nova Actividade" em modo
edição (`wire:navigate` para `agenda.create?atividade=` com header "Nova Actividade" —
parecia uma criação); o certo era **mostrar os detalhes**. Decisão do utilizador: **modal no
calendário** (não página própria). O protótipo nem trata o clique (`clickSchedule` só fazia
`console.log`).

- **Estado Livewire** (`ActivityCalendar`): `$detalheId` + `abrirDetalhe()` (ignora id
  inválido), `fecharDetalhe()`, `concluirDetalhe()` (`ActivityService::toggleStatus`) e
  `eliminarDetalhe()` (`remove` + fecha); `render()` passa `detalhe`
  (`Activity::with('opportunity')`).
- **Evento na grelha**: `href="javascript:void(0);"` + `wire:click="abrirDetalhe(id)"`
  (antes navegava para o form de edição); `title="Ver detalhes: …"`.
- **Modal** (`#modalDetalheActividade`, markup puro — sem JS do Bootstrap): visibilidade
  controlada pelo estado (`modal fade show d-block` / `display:none` + backdrop), fecha com
  `wire:click.self` no backdrop, `.btn-close` e botão "Fechar". Conteúdo: badge de tipo,
  descrição, Cliente, Estado (badge), Tipo, Data, Hora, Prazo, Próxima acção e Resultado;
  footer com **Editar** (`wire:navigate` → form de edição — único caminho que abre o form),
  **Concluir** (só se pendente), **Eliminar** (confirm do Alpine) e **Fechar**.
- **Travão do body** (`modal-open`): 1.ª tentativa com `$wire.entangle('detalheId').live` +
  `x-effect` **falhou** — `.live` devolve um objecto (sempre truthy) → a classe entrava no
  page load e nunca saía; solução final: o PHP faz `$this->dispatch('modal-actividade',
  aberto: bool)` em abrir/fechar/eliminar e a raiz do componente tem
  `x-on:modal-actividade.window` com o toggle; `app.js` remove a classe em
  `livewire:navigating`/`livewire:navigated` (caso "Editar": navega com o modal aberto e o
  listener Alpine morre com o componente).
- **Modal "opacitada" (bug visual corrigido)**: `theme-extract.css:625` tem
  `body.modal-open .nxl-container,...{filter:blur(3px)}` — no protótipo os modais ficam
  **depois de `</main>`** (fora do contentor) e o blur atinge só o chrome; aqui o modal vive
  **dentro** de main (renderizado pelo Livewire) e o filtro borrava também o próprio modal.
  Fix em `app.css`: `filter: none` nos 4 seletores da regra + mesmo blur no
  `.modal-backdrop.show` via `backdrop-filter` — mas o minifier do build **dropa a
  propriedade standard e só deixa o `-webkit-`** (Chrome ignora) → o blur do backdrop do
  calendário está em **inline-style na view** (não passa pelo minifier). Resultado: fundo
  borrado (igual ao protótipo), modal nítido.
- **Testes**: 3 novos em `AgendaTest` (abre modal com detalhes/acções + `assertDispatched`,
  id inválido ignorado, fecha; concluir/eliminar pelo modal) → **115 testes/607 assertions**
  (+1 em DashboardTest para o modal de confirmação → **116/610**).
- **E2E** (`e2e-calendario-detalhe.mjs`, Chrome real, 0 erros de consola): cria actividade →
  clica evento → modal abre SEM navegar (detalhes completos, body travado, backdrop) →
  Editar → form com prefill + body libertado → Concluir (badge "Concluída", botão some) →
  Fechar (modal+backdrop escondidos, body libertado) → reabrir e fechar pelo backdrop →
  Eliminar (abre modal de confirmação → confirma → evento sai da grelha) → lista volta ao
  baseline (0 restos, 0 dialogs nativos); pint OK, build `app-CQXN0hf4.css` + `app-DZulwGZq.js`.

---

### Modal de confirmação global — substitui o window.confirm (2026-10-08)

**Pedido**: os 6 fluxos de eliminação usavam o `confirm()` nativo do browser — substituir
por um modal de confirmação com o estilo do sistema.

- **Store Alpine** `confirmar` (registado em `alpine:init` no `app.js`): método
  `abrir({ titulo, mensagem, confirmar, cancelar })` que devolve uma **Promise** e
  `responder(bool)` que a resolve; trava/destrava o `modal-open` do body.
- **Markup global** em `layouts/app.blade.php`, **depois de `</main>`** (mesma posição do
  protótipo — nunca apanha blur do contentor): `#modalConfirmar` com `:class="{ 'show
  d-block': $store.confirmar.visivel }"`, `x-text` nos títulos/botões, fecha por
  `btn-close`, clique no backdrop, **ESC** (`@keydown.escape.window`) e botão Cancelar;
  backdrop com `backdrop-filter: blur(3px)` em `:style` (inline, anti-minifier).
- **Substituídos os 6 `confirm()`**: `activity-list`, `activity-calendar` (Eliminar do
  modal de detalhes), `alert-list`, `product-list`, `conciliaction-list`,
  `commercial-list` — todos com o padrão
  `$store.confirmar.abrir({...}).then(ok => ok && $wire.remove(id))`.
- **Teste**: `DashboardTest::test_layout_renders_global_confirm_modal_instead_of_native_alerts`
  — assert do `#modalConfirmar` + varredura recursiva de TODAS as views garantindo zero
  `confirm(` (regex `[^.\w]confirm\(` que não apanha `password.confirm`).
- **E2E** (`e2e-confirmar.mjs`, Chrome real): **0 dialogs nativos** (contados no handler);
  lista: cancelar por botão (título/mensagem/CANCELAR-ELIMINAR preenchidos, body travado,
  backdrop `blur(3px)`, main sem blur) + cancelar por **ESC** + confirmar eliminação
  (item some, body libertado); calendário: confirmação **empilhada sobre o modal de
  detalhes** (elementFromPoint no confirm, blur nos dois backdrops, detalhes nítido) →
   confirma → ambos fecham, evento some; baseline 34/34 e 0 restos. Suite **116 testes/610
   assertions**, pint OK, build `app-CQXN0hf4.css` + `app-DZulwGZq.js`.

---

### Conciliações: sem sidebar vertical + tabela sem corte (2026-10-08)

**Pedido**: aplicar à Conciliações o que fizemos na Agenda (remover o menu lateral
vertical e passar os filtros para o header) e corrigir a tabela "desajustada".

- **Causa raiz medida (Chrome, 1600×1000)**: o `content-sidebar` de 300px empurrava
  `.content-area` para 580..1756 → 156px cortados pelo viewport (a página não tem scroll
  horizontal) e a tabela (1218px naturais, células `white-space:nowrap` do tema) não
  cabia no wrap de 1114px → scroll interno + coluna Acções cortada. Escondido o sidebar
  por JS, `.content-area` encaixava em 280..1600 e a tabela cabia sem overflow.
- **`conciliaction-list.blade.php`**: removidos `x-data sidebarOpen`, o bloco
  `content-sidebar` (filtros + botão "Nova") e o gatilho hamburger; header ganhou
  botão primário **`+ Nova Conciliação`** e o dropdown "Filtros" passou a ter grupos
  `<h6>Classificação</h6>` ("Todas as classificações" + loop) e `<h6>Estado</h6>`
  ("Todos os estados", Resolvidas, Pendentes) — igual à Agenda.
- **`conciliaction-form.blade.php`**: removidos a sidebar (quase vazia) e o hamburger;
  o dropdown do header já tinha "Ver todas / Nova Conciliação".
- **CSS de contingência (`app.css`)**: em 1366px até sem sidebar havia corte de ~194px
  (o min-content da tabela nowrap impedia os itens flex de encolher) → `min-width: 0`
  em `.d-flex.flex-grow-1` e `.content-area` dentro de `apps-container + without-header`;
  agora a tabela faz scroll interno normal (`table-responsive`).
- **Testes**: `ConciliactionsTest` espelha o AgendaTest — `assertDontSee('content-sidebar')`
  + `assertSee('Filtros'/'Todas as classificações'/'Todos os estados'/'Nova Conciliação')`.
- **E2E** (`e2e-conciliacoes.mjs`): 1600px — 0 erros, sidebar ausente, `wrapRight 1569 ≤
  1600`, sem overflow, dropdown com grupos, filtro "Resolvidas" → 4 linhas todas "Sim",
  menu fecha, "Nova Conciliação" navega para o form (também sem sidebar); 1366px —
  `clipado:false` (área = 1366) + scroll interno da tabela. Screenshots
  `conc-lista-1600.png`/`conc-lista-1366.png`. Suite **116 testes/615 assertions**,
  pint OK, build `app-DQdxitU4.css` + `app-DZulwGZq.js`.
- **Nota**: o mesmo padrão vertical ainda existe em Produtos, Alertas, Comerciais e
  Contratos (6 views) — pendente de pedido.

---

### Produtos, Alertas, Comerciais e Contratos: sem sidebar vertical (2026-10-08)

**Pedido**: aplicar a todas as páginas restantes com `content-sidebar` o mesmo padrão da
Agenda/Conciliações ("aplica a mesma mudança nessas").

- **7 views afectadas**: `product-list`, `product-form`, `alert-list`, `commercial-list`,
  `commercial-form`, `contract-list`, `contract-form` — removidos o bloco `content-sidebar`
  (300px), o `x-data sidebarOpen` e o gatilho hamburger; cada ficheiro ganhou comentário
  `sem content-sidebar` no topo (o Blade elimina comentários, logo o `assertDontSee`
  continua a passar). Com isto **nenhuma view** do protótipo usa `content-sidebar`.
- **Listas**: botão primário no header — `+ Novo Produto` / `+ Novo Comercial` /
  `+ Novo Contrato` (Alertas não tem acção de criação); dropdown "Filtros" com grupos
  `<h6>` — Produtos: **Estado** + **Comercial**; Alertas: **Tipo** + **Estado**;
  Comerciais/Contratos: **Estado**; em Comerciais "Todos" passou a "Todos os estados".
- **Formulários**: sidebar (quase vazia) + hamburger removidos; o dropdown do header já
  continha "Ver todos / Novo X".
- **Testes** (+20 assertions): nos 4 testes de lista, `assertDontSee('content-sidebar')`,
  `assertSee('content-area-header')`, `Filtros`, `Novo X`, `Todos os estados`
  (+ `Todos os comerciais` em Produtos); asserções antigas (feather-plus, rotas,
  "Novo X" nos forms) mantêm-se válidas.
- **E2E** (`e2e-listas-sem-sidebar.mjs`): 4 listas × [1600, 1366] — todas com
  `semSidebar:true`, sem hamburger, `clipado:false` (área = viewport), dropdowns com os
  grupos certos e botões Nova correctos; em 1366 Alertas/Contratos fazem scroll interno
  normal da tabela; 3 formulários sem sidebar com `wire:submit="save"`; **0 erros de
  consola**, `criticoOk:true`. Suite **116 testes/635 assertions**, pint OK,
  build `app-DQdxitU4.css` + `app-DZulwGZq.js`.
- **Nota**: a leitura de screenshots voltou a devolver PNGs trocados (gotcha da
  plataforma) — verificação feita por medições DOM (objectivas: botões/grupos/medidas
  por página) + hashes dos ficheiros.

---

### Coluna Observações desalinhada + varredura de tabelas (2026-10-08)

**Pedido**: "a coluna Observações em Conciliações está mais em cima e não está dentro da
tabela — verificar" + "verifica também em outras áreas que possuem tabelas, tem essa distorção".

- **Causa raiz** (`theme-extract.css:163`): `.text-truncate-1-line` tem
  `display:-webkit-box!important` + `-webkit-line-clamp:1`; aplicada **directamente ao
  `<td>`** destruía o `table-cell` (computado `flow-root`) → célula com altura 53px vs
  61px das restantes, conteúdo colado ao topo e tira branca no fundo das linhas vermelhas.
- **Fix (3 views)**: classe sai do `<td>` e passa para um `<div class="text-truncate-1-line">`
  interno (mesmo padrão da coluna Cliente) — `conciliaction-list` (Observações, 300px),
  `commercial-list` (Descrição, 300px), `alert-list` (Mensagem, 400px).
- **Verificação pós-fix** (`probe-obs.mjs`): nas 3 páginas, `display:table-cell`, alturas
  iguais (`61=61`), centros do conteúdo alinhados (±4px), 0 erros de consola.
- **Varredura global pedido "outras áreas"**: grep em todas as views → zero `<td>/<th>` com
  classes que mudam `display` (text-truncate-1-line, d-block/d-flex/d-inline-block, styles
  inline); runtime `probe-tabelas.mjs` em **13 páginas** (analytics, dashboard, pipeline,
  pipeline/importar, agenda, calendário, contratos, produtos, comerciais, alertas,
  conciliações, /ui, profile) → **346 `<td>` + 63 `<th>`, todas `table-cell`, 0 células
  erradas, 0 erros**; preview da Importação verificado estaticamente (`<td>`s sem classes).
  Cobertura completa: as 9 tabelas do protótipo.
- **Validação**: 116 testes/635 assertions, pint OK, E2E `e2e-conciliacoes.mjs` +
  `e2e-listas-sem-sidebar.mjs` verdes (`criticoOk:true`, 0 erros), build
  `app-DQdxitU4.css` + `app-DZulwGZq.js` (inalterado — só mudaram Blades).

---

### Pendências resolvidos: notificações, alertas, contratos e docs (2026-10-09)

**Pedido**: "vamos resolver os pendentes" (trabalho por commitar + TODOs + validações)
+ "a modal de notificação está mal estruturada — aparecem em grid horizontal".

- **Commits pendentes**: 143 ficheiros do trabalho 06–08/10 commitados em 2
  (`eeefe7c` código, `d56259e` docs).
- **Notificações (bug de estrutura)**: `header.blade.php` envolvia **todos** os alertas
  num único `.notifications-item` (que é `display:flex` no protótipo) → os
  `.notifications-desc` ficavam irmãos flex **na horizontal (grid)**. Fix: `@foreach`
  **por fora** — 1 `.notifications-item` por notificação, empilhados com separador
  tracejado (`.notifications-item + .notifications-item`). E2E `e2e-notificacoes.mjs`:
  3 itens, tops 211→309→409, mesmo `left`, 1 `desc`/item, `criticoOk:true`.
- **`agenda:reminder` cria notificação no sistema (§11)**: novo `AlertType::AGENDA`
  (label "Agenda", `feather-calendar`, badge success — sem migration de enum, coluna
  string); migration `2026_10_09_000001` torna `entidade`/`entidade_id` nullable
  (alertas de sistema); `AgendaReminder::createSystemNotifications` cria `Alert` com
  guard anti-duplicado por data; email próprio do lembrete mantém o canal de email
  (sem 2.º email via `NewAlert`). Filtro de tipos da lista é enum-driven → "Agenda"
  aparece automaticamente.
- **BUG LATENTE GRAVE apanhado pelos testes**: `NewAlert` viaja por
  `['mail','database']` mas **a tabela `notifications` nunca existiu** (nem em `adg`!)
  → `alerts:generate` rebentaria sempre no 1.º alerta em produção. Migration
  `2026_10_09_000002_create_notifications_table` + `php artisan migrate`.
- **`alerts:generate --dry-run` implementado**: `AlertService::generate(bool $dryRun)`
  conta os que *seriam* criados sem persistir nem notificar (antes executava a
  normal mesmo em dry-run); mensagem "SERIAM criados".
- **Suposição §19.12 implementada**: novo comando `contracts:expire [--dry-run]`
  (activo + `data_fim` passada → `EXPIRADO` via `ContractService::setStatus`),
  agendado 05h30 Africa/Luanda (antes dos alertas 06h) em `routes/console.php`.
- **Docs/env**: `.env.example` → MySQL (`DB_DATABASE=adg`); suposições do `plano.md`
  14/14 ✅; novo **CHECKLIST DE DEPLOY** no `plano.md` (cron de 6 entradas, SMTP,
  SSL, APP_KEY, pós-deploy); pendência "BD adg substitui crm_pessoal" **confirmada**
  pelo utilizador (registada); novo **`teste-manual.md`** (percurso passo-a-passo
  para o Adailson, 10 secções + espaço de feedback).
- **Gráficos com dados reais (pendência Revisão Geral)**: E2E `e2e-graficos.mjs`
  lê `window.adgCharts.chartInstances` e valida series/labels/soma de cada um —
  Dashboard 4/4 e Painel Executivo 4/4 válidos; donut "Segmento" tinha **1 fatia**
  (todos os seeders metiam `sector='Distribuição'`) → sectores variados em
  `DashboardDemoSeeder` (6 sectores) e `OpportunitySeeder` (alternado) + reseed;
  percentagens ≤100, 0 erros de consola, `criticoOk:true`.
- **Validação**: **121 testes / 659 assertions** (+5 novos: reminder×2, filtro
  Agenda, dry-run, contracts:expire), pint OK, E2E `e2e-notificacoes.mjs` +
  `e2e-graficos.mjs` verdes, build `app-DQdxitU4.css` + `app-DZulwGZq.js`
  (inalterado — só mudaram Blades/PHP).

---

### Modal de detalhe do alerta: sino + página Alertas (2026-10-09)

**Pedido**: "ao clicar num alerta deve abrir uma modal com as informações
desse alerta e os seus botões de acção" (âmbito confirmado: **ambos** — sino
e página `/alertas`).

- **Novo componente `Livewire\Alerts\AlertBell`** substitui o bloco estático
  do sino em `header.blade.php`: badge e dropdown (5 recentes) passam a
  Livewire → **badge actualiza-se em tempo real** após acções; item clicável
  (`wire:click="abrirAlerta(id)"`); removido `data-bs-auto-close="outside"`
  (o dropdown fecha sozinho ao escolher); placeholders mortos saem (o ponto
  "não lido" vira `<span>` indicador, o X "remover" desaparece — as acções
  vivem na modal). Markup do `<a class="nxl-head-link…">` preservado
  **byte-a-byte** (regex de `AlertsTest` intacta).
- **Partial partilhada `livewire/alerts/detail-modal.blade.php`** — padrão
  exacto do modal da Agenda (markup-puro `show d-block`, backdrop com blur
  inline, `wire:click.self`, travão do body via `dispatch('modal-alerta',
  aberto:…)` + `x-on:modal-alerta.window` na raiz de cada componente):
  badge do tipo + chip de estado (Não lido/Lido/Resolvido), **mensagem
  completa** (`pre-wrap`), entidade com link "Ver registo"
  (`opportunities.show`/`contracts.show`/`conciliactions.edit` — resolvido no
  render, apaga-se se a entidade não existir), criado por/criado em
  (`d/m/Y H:i` + `diffForHumans`), prazo; rodapé: **Marcar como lido** (se
  `!lido`), **Marcar como resolvido** (se `!resolvido`), **Eliminar**
  ($store.confirmar) e **Fechar**. IDs distintos por componente
  (`modalDetalheAlertaSino`/`…Lista` — coexistem na página `/alertas`).
- **`AlertList`**: `$alertaActivoId` + `abrirAlerta`/`fecharDetalhe`
  (mesmos nomes do bell → partial reutilizável sem adaptador); `remove()`
  fecha a modal se o alerta activo for o eliminado; célula **Mensagem**
  clicável (`cursor:pointer` + `wire:click`).
- **Testes +3**: bell abre modal com mensagem completa e executa as 3 acções
  (e fecha ao eliminar), id desconhecido ignorado, lista abre a mesma modal —
  **124 testes / 668 assertions**.
- **E2E novo `e2e-alerta-modal.mjs`**: marcador criado por script PHP e
  apagado pelo próprio teste (zero toque nos dados demo). Fluxo sino → item →
  modal com mensagem+3 botões, menu fechado, body travado → marcar lido
  (botão some, chip muda) → fechar; `/alertas` → clicar Mensagem → Eliminar →
  `#modalConfirmar` → confirmar → marcador removido, modal fechada, body
  livre; **0 erros de consola, `criticoOk:true`** (bug próprio apanhado:
  `clicouFechar` calculado mas nunca lançado ao objecto → `&&` devolvia
  `undefined` e o `criticoOk` nem saía no JSON). `e2e-notificacoes.mjs`
  re-verde (3 itens empilhados).
- **Validação**: 124/668, pint OK, 3 E2E verdes, build `app-CDSVpqV-.css` +
  `app-DZulwGZq.js` (o CSS muda porque os novos views entram no safelist
  `@source '*.php'`).

---

### Tabela de Clientes (Sprint 13) (2026-10-09)

**Pedido**: `seguir.md` §1-14 — vista `/clientes` com a tabela de clientes
aprovados (9 campos do Excel do Adailson + acções), filtros, ordenação e
paginação.

- **Campos novos denormalizados em `opportunities`** (migration
  `2026_10_09_000003_adicionar_colunas_tabela_clientes_em_opportunities`,
  `after('codigo_sap')`): `codigo` (CLI###, 4 índices), `tem_compras`,
  `assinatura_director`, `enviado_entregue` (bool default false) e
  `conciliacao_estado`/`contrato_estado` (string nullable, índice composto);
  `Opportunity` com fillable+casts, factory com as probabilidades do §10.2.
- **Novos enums** `ConciliacaoEstado` (concordante/discordante/pendente) e
  `ContratoEstado` (sim/caducidade/nao), ambos com `badgeClass()` no padrão
  `bg-soft-X text-X` (labels NÃO/…/CADUCIDADE maiúsculas iguais ao protótipo).
- **Seeder**: 10 clientes `cliente_aprovado` `CLI001`–`CLI010` com as
  percentagens do §10.2 (6 compras / 4 assinatura / 5 enviado; conc 3/2/5;
  contrato 4 sim / 2 caducidade / 4 nao).
- **Componente `Livewire\Clients\ClientTable`**: pesquisa (`debounce.300ms`
  em nome/código), dropdown Filtros com grupos KAM (comerciais activos) /
  Compras / Conciliação / Contratos + badge de contagem + "Limpar filtros",
  ordenação CÓDIGO/CLIENTE, 20 por página; só `fase_atual=cliente_aprovado`;
  relação real é **`commercial()`** (o doc diz `comercial`).
- **Vista/rota**: wrapper `clients/index.blade.php` no padrão alerts
  (`hidePageHeader`/`hideFooter` + header slot "Clientes"), rota
  `Route::view('clientes', …)` com nome `clients.index` (padrão do projecto em
  vez do `Route::get` full-page do doc); sidebar com item standalone
  `feather-users` entre Oportunidades e Agenda (activo via `routeIs`, não
  partilha CSS de acordeão — verificado no E2E). Tabela `#customerList` com
  10 colunas, empty state inline (`x-ui.empty-state` não existe), acção olho
  `wire:navigate` → `opportunities.show`.
- **Sincronização §14.1 nos serviços**: `ConciliactionService`
  (create/update/classificar) e `ContractService`
  (create/update/setStatus/renovar/cancelar) escrevem
  `conciliacao_estado`/`contrato_estado` na opportunity via
  `Opportunity::whereKey()->update()` com o valor raw (bypass de casts);
  fallbacks: classificação ausente→`PENDENTE`, estado ausente→`ACTIVO`;
  mapeamento ACTIVO|RENOVADO→SIM, EXPIRADO→CADUCIDADE, CANCELADO→NAO.
- **Testes +15 `ClientTableTest`** (§11): auth, estrutura (10 cabeçalhos,
  sem `content-sidebar`), só aprovados, badges+KAM na linha, pesquisa,
  5 filtros individuais, reset, ordenação, paginação (25→20/pág.), link da
  acção, sidebar activa, empty state — **139 testes / 725 assertions**.
- **E2E novo `e2e-clientes.mjs`**: sidebar → `/clientes`, headers exactos,
  12 linhas, badges todos `bg-soft-*`, 12 botões "Ver" com pathname
  `/pipeline/` (o `route()` devolve URL absoluta — assert corrigido para
  `new URL(a.href).pathname`), ordenação (chevron up→down), dropdown completo
  (KAM/Compras/Conciliação/Contratos/Limpar), filtro Compras=Não → 6 linhas
  todas NÃO, limpar/restaurar, pesquisa "Distribuidora"→1/restaurar,
  **0 erros de consola — `criticoOk:true` ×3 pós-build**; re-verde de
  `e2e-listas-sem-sidebar`, `e2e-notificacoes` e `e2e-alerta-modal` (esta
  exige que o marcador seja criado primeiro com `criar-alerta-e2e.php`).
- **Validação**: 139/725, pint OK, 4 E2E verdes, build `app-CDSVpqV-.css` +
  `app-DZulwGZq.js`.

---

### Pipeline: valores em formato compacto (2026-10-09)

**Pedido**: os cartões "Pipeline Aberto / Aprovado (mês) / Ponderado" não
usavam a conversão M/K do dashboard e os valores vazavam dos cartões.

- `Stats` passa a usar `OpportunityService::formatKzCompact()` nos 3 valores
  ("Kz 339.720.000" → "Kz 339,7M"), mantendo o valor completo em `title`
  (hover) via `…Completo`.
- Kanban do Pipeline alinhado: total da coluna e valor do cartão também
  compactos, com o valor completo no `title`.
- Validação em Chrome real (`e2e-pipeline-valores.mjs`, 1600+1366):
  0 overflow em 4 stats + 9 totais + 50 cartões, formato M presente, 0 erros
  de consola — `criticoOk:true` ×2; 17 testes (PipelineTest+OpportunityViewTest)
  e pint OK. Sem mudança de CSS/JS → sem novo build.

---

### Kanban do Pipeline: navegação sem scroll lateral (2026-10-09)

**Pedido**: para mover cartões entre colunas distantes era preciso descer a
página até à scrollbar horizontal do board (9 colunas × 270px ≈ 2.430px).

- **Setas ← → na FilterBar** (espaço vazio da grelha, junto a "Limpar"):
  scroll suave de 2 colunas (564px), desactivadas nas extremidades, estado
  recalculado no `scroll`, no `livewire:morphed` e antes de cada clique.
- **Auto-scroll durante o drag** (padrão Trello): enquanto se arrasta, rato
  a <72px das bordas do board → scroll automático de 14px/frame; para ao
  centro, em `drop`/`dragend`/`dragleave` para fora — nunca há scroll
  fantasma depois do arrasto.
- `x-data` subiu para o wrapper `col-lg-12` (filter bar + board partilham
  scope; `$refs.board`), `drop()` preservado.
- E2E novo `e2e-kanban-navegacao.mjs` (1366px): estado inicial das setas,
  navegar 0→564→0 com disabled correctos, auto-scroll direita e esquerda,
  paragem no centro e pós-dragend, 0 erros — `criticoOk:true` ×2;
  PipelineTest 9/9.

---

### Agenda 500 + "apagar não funciona": activities órfãs (2026-10-09)

**Pedido**: "as funções de apagar não funcionam em todo o painel".

- **Diagnóstico**: varredura de todos os pontos de apagar (produtos,
  comerciais, conciliações, agenda, alertas) — todos abrem a modal e
  confirmam; **excepto a Agenda, que dava 500** (`/agenda` e
  `/agenda/calendario` inacessíveis).
- **Bug raiz**: `Opportunity` usa `SoftDeletes` e o `DashboardDemoSeeder`
  cria actividades com `pluck('id')` que **inclui as opps `[demo-hist]`** e
  depois soft-deleta essas opps no mesmo run — o `cascadeOnDelete` das FKs
  não corre em soft delete → 4 activities órfãs →
  `Attempt to read property "nome" on null` em `activity-list`.
- **Correcções**:
  - `DashboardDemoSeeder`: actividades só para opps não-demo
    (`not like [demo-hist]`) + apaga (físico) as activities das opps demo
    **antes** do soft delete;
  - `ActivityList`/`ActivityCalendar` com `whereHas('opportunity')`
    (órfãs nunca aparecem) + guards `?->nome ?? '—'` também em
    `conciliaction-list`, `contract-list`, `contract-view` (6 views);
  - 4 activities órfãs removidas da BD dev; seeders de reposição
    (`updateOrCreate`) re-executados após os testes E2E.
- **Teste regressão**: `test_list_page_survives_soft_deleted_opportunity`
  (lista + calendário com opp soft-deleted → 200 e sem a activity).
- **Validação**: E2E `e2e-apagar-todos.mjs` — 4 listas apagam de verdade
  (produtos 7→6, comerciais 1→0, conciliações 10→9, agenda 30→29) +
  alertas, **0 erros de consola**; suite **140/729**, pint OK.

---

### Fix: `drop is not defined` no kanban (2026-10-09)

**Pedido**: erro de consola `Alpine Expression Error: drop is not defined`
ao soltar cartões no pipeline.

- **Causa**: na tarefa das setas, o `x-data` subiu do `#kanbanBoard` para o
  wrapper `col-lg-12` mas o método `drop()` (que chama
  `$wire.$call('move', …)`) ficou para trás — as zonas continuavam a chamar
  `drop($event, …)` num scope sem a função.
- **Fix**: `drop()` reposto no objecto Alpine do wrapper.
- **Validação**: E2E novo `e2e-kanban-drop.mjs` — dragstart → drop na
  coluna seguinte → cartão movido (`move` real), reposição à coluna
  original, `drag-over` limpo, **0 erros de consola** (`criticoOk:true` ×4);
  navegação `e2e-kanban-navegacao` ×2, PipelineTest 9/9, pint OK.

---

### Raiz `/` → login (2026-10-09)

**Pedido**: *"ao iniciar o projecto ele vem em uma tela, devia vir apenas na tela de login"*.

- **Antes:** `Route::view('/', 'welcome')` — a página de boas-vindas do Laravel.
- **Fix:** `Route::redirect('/', '/login')` em `routes/web.php`.
- **Comportamento verificado no browser:** guest `/` → `/login` (200);
  autenticado `/` → `/login` → middleware `guest` → `/dashboard` (o
  `RedirectIfAuthenticated` do framework escolhe a rota `dashboard` por
  omissão); logout → `/` → login.
- `ExampleTest` actualizado para `assertRedirect('/login')`.
- Validação: suíte **140 testes / 730 assertions**, pint OK, E2Es não abrem
  `/` directamente (sempre `/login`).

---

### Sprint Ajustes BLOCO 1 — Ícones alinhados (2026-10-10)

**Pedido** (`mudancas.md`): remover o dropdown de 3 pontos das colunas Acções
de Conciliações/Contratos/Produtos e mostrar os ícones directos na linha.

- **Views:** as 3 listas passam a usar `<div class="action-icons">` com
  botões `.btn-icon` 32×32 (CSS novo no fim do `app.css`); ícones por
  módulo: conciliações ✓/✗/⚠ classificação + 🗑, contratos ✓ activar /
  🔄 renovar / ✗ cancelar / 🗑 (condicionados pelo estado), produtos
  ▶/⏸ toggle + 🗑; manter Editar/Ver já directos.
- **Contratos:** novo `ContractList::remove()` (soft delete +
  `dispatch('contract-removed')`) — Eliminar não existia, só saía do
  dropdown de outros módulos.
- **Testes:** +4 (`test_list_shows_direct_action_icons_without_dropdown`
  ×3 com `assertSee('action-icons')`/`assertDontSee('feather-more-*')` e
  `test_remove_deletes_contract` com `assertSoftDeleted`); suíte
  **144/749**, pint OK, build OK.
- **Browser (E2E `e2e-bloco1-icoes.mjs`):** nas 3 listas `.action-icons`
  com `display:flex`, `gap:4px`, botões 32×32 alinhados, **0 dropdowns de
  3 pontos**, 0 erros de consola; toggle de produto funcionando
  (Activar↔Desactivar).

---

### Sprint Ajustes BLOCO 2 — Dashboard (2026-10-10)

**Pedido** (`mudancas.md`): reorganizar o dashboard conforme pedido do cliente.

- **2.1 Gráfico principal:** "Facturação Mensal" → **"Pipeline por Mês"** —
  **contagem** de oportunidades abertas (excepto `cliente_aprovado`/
  `cliente_nao_aprovado`) entradas por mês (`created_at`), escala 0..N
  (feedback do cliente: não somar valores em Kz; payload com
  `moeda => false`, tooltip "N oportunidades", eixo Y sem "Kz" e sem
  abreviações M/K); `faturacaoMensal` → `pipelineMensal` no payload,
  série chamada "Pipeline".
- **2.2 Mini-cards:** 3 (actividades/encomendas) → **4 do pipeline**
  (Pipeline Aberto, Aprovado (mês), Ponderado, Taxa Conversão) — mesmos
  cálculos do `Opportunities\Stats`, valores com `formatKzCompact` e
  `title` com o valor completo; os 3 mini-charts sparkline saíram
  (`window.adgSmallCharts` removido — `renderSmallCharts` no JS tem
  guarda `if (!data) return`, sem erros).
- **2.3 KPI:** "Encomendas em Curso" → **"Produtos Activos"**
  (`products.activo = true`, ligação para `/produtos`).
- **Testes:** DashboardTest actualizado + 2 novos
  (`test_pipeline_chart_counts_open_opportunities_by_month` — payload
  conta só abertas por mês, `moeda=false`; `test_kpi_produtos_activos_counts_active_products`);
  suíte **146/757**, pint OK, build OK.
- **Browser:** E2Es `e2e-bloco2-dashboard.mjs`, `e2e-eixo-y-reais.mjs`
  (eixo Y com contagem inteira 0..N, sem Kz/M/K) e `e2e-graficos.mjs`
  verdes (dashboard 1 instância válida, analytics 4, 0 erros).

---

### Sprint Ajustes BLOCO 3 — Agenda tipo "Outros" (2026-10-10)

**Pedido** (`mudancas.md`): permitir tipo personalizado na Agenda.

- **Enum:** `ActivityType::OUTROS = 'outros'` (label "Outros", badge
  `bg-soft-gray text-muted`, cor `#6c757d`, ícone `feather-more-horizontal`).
- **Migration:** `tipo_outros` (varchar 100, nullable) em `activities`.
- **Model `Activity`:** `tipo_outros` em fillable + helper `tipoDisplay()`
  (devolve `tipo_outros` quando tipo = OUTROS, senão `tipo->label()`).
- **Form (`ActivityForm`):** propriedade `tipo_outros`, regra
  `nullable|nullable string|max:100|required_if:tipo,outros`; `save()`
  limpa o campo quando o tipo não é "outros"; view mostra campo
  "Especifique" condicionalmente com `wire:model.live="tipo"`.
- **Lista:** badge mostra `tipoDisplay()` quando tipo = OUTROS.
- **Calendário:** modal de detalhe usa `tipoDisplay()` (badge + linha Tipo).
- **Testes:** +5 em `AgendaTest` (validação required_if, guardar e mostrar
  na lista, limpar quando tipo ≠ outros, modal calendário, condicional);
  suíte **151/772**, pint OK, build OK.
- **Browser:** E2E `e2e-bloco3-outros.mjs` verde (campo condicional,
  texto personalizado na lista, 0 erros).

---

### Sprint Ajustes BLOCO 4 — Aba Actividade da oportunidade (2026-10-10)

**Pedido** (`mudancas.md`): aba "Actividades" do detalhe da oportunidade
passa a usar a lógica da Agenda (modo cópia).

- **Componente:** `Opportunities\OpportunityActivities` — filtra
  `where('opportunity_id', ...)`, ordena data desc; `toggle()`/`remove()`
  **escopados à oportunidade** (ids de outras opps são ignorados).
- **View:** `opportunity-activities.blade.php` — cabeçalho com contagem +
  botão "Nova Actividade"; empty state "Ainda sem actividades!" (mantém
  o assert do teste de página); lista `single-task-list` (checkbox,
  concluir, badge tipo, prazo, dropdown editar/eliminar com modal).
- **Form:** `ActivityForm` lê `?oportunidade={id}` no mount
  (`opportunity_id` pré-seleccionado + flag `fromOpportunity`); `save()`
  com `fromOpportunity` redireciona para o detalhe da oportunidade.
- **Testes:** +4 em `OpportunityViewTest` (filtragem por opp, empty state,
  toggle/remove escopados, pré-selecção + redirect); suíte
  **155/789**, pint OK, build OK.
- **Browser:** E2E `e2e-bloco4-aba-actividades.mjs` verde (aba filtra
  correcto, link Nova Actividade com `?oportunidade=`, 0 erros).

---

### Sprint Ajustes BLOCO 5 — Aba Nota (2026-10-11)

**Pedido** (`mudancas.md`): aba "Nota" com estrutura cronológica, tipos e
anexos privados.

- **Migrations:** `notes` (id, uuid, opportunity_id, user_id, titulo
  nullable, conteudo, tipo, fixada, timestamps) e `note_attachments`
  (note_id FK cascade, nome_original, caminho, tamanho, mime_type).
- **Enum `NoteType`:** INFORMACAO/DECISAO/LEMBRETE/REUNIAO com
  label/badge/icon.
- **Models:** `Note` (casts enum+boolean, uuid, relations user/opportunity/
  attachments, factory) e `NoteAttachment`.
- **Componentes:** `Notes\NoteForm` (titulo opcional → null se vazio,
  conteudo required, tipo, upload múltiplo com `WithFileUploads` →
  `storage/app/private/notes/{note_id}/` via disco `local`, max 10 MB);
  `Notes\NoteList` (fixadas no topo, pesquisa titulo/conteudo, filtro por
  tipo, toggleFix/remove escopados à opp, remove apaga anexos físicos,
  form abre/fecha via eventos `note-saved`/`note-form-closed`,
  `flashSuccess` propriedade — session flash não atravessa requests
  Livewire).
- **Bug corrigido durante E2E:** `{!! nl2br(e(...)) !!}` estava dentro do
  atributo `class` do `<p>`; flash de sessão substituído por propriedade.
- **Aba Notas** no `opportunity-view.blade.php` → `@livewire
  ('notes.note-list', ...)` (placeholder "Ainda sem notas!" substituído).
- **Testes:** `NoteTest` novo (12 testes: CRUD, required, titulo vazio→null,
  edição, escopo opp, fixadas primeiro, toggle/remove escopados, pesquisa
  e filtro, anexo em disco fake, remoção física de anexos, fluxo form);
  suíte **167/835**, pint OK, build OK.
- **Browser:** E2E `e2e-bloco5-notas.mjs` verde (lista, form, criação com
  badge tipo, fixar, flash sucesso, 0 erros).

---

### Sprint Ajustes BLOCO 6 — Aba Proposta (2026-10-11)

**Pedido** (`mudancas.md`): aba "Proposta" da oportunidade com 9 campos
directos (uma proposta por oportunidade).

- **Migration** `add_proposta_fields_to_opportunities_table`: 9 campos
  nullable em `opportunities` — `proposta_produto_id` (FK products,
  nullOnDelete), `proposta_preco`, `proposta_volume` (decimals 15,2),
  `proposta_volume_unidade` (varchar 10: L ou m³), `proposta_periodo_inicio`
  / `proposta_periodo_fim` (dates), `proposta_credito`,
  `proposta_facturacao`, `proposta_non_oil` (decimals 15,2).
- **Model `Opportunity`:** 9 campos em fillable + casts (decimals e dates)
  + relação `propostaProduto()` (belongsTo Product).
- **Componente** `Opportunities\OpportunityProposal`: mount carrega valores
  existentes, valida (`exists:products`, `min:0`, `in:L,m³`,
  `after_or_equal` no período), `save()` normaliza strings vazias → null e
  persiste; propriedade `saved` (session flash não atravessa Livewire).
- **View** `opportunity-proposal.blade.php`: form com os 9 campos (produto
  = select de activos, unidade = L/m³), alerta de sucesso com botão
  fechar, validações inline, resumo leitura abaixo quando preenchida
  (Kz formatado pt-PT, datas dd/mm/yyyy).
- **Aba Proposta** no `opportunity-view.blade.php` → `@livewire
  ('opportunities.opportunity-proposal', ...)` (placeholder "Ainda sem
  propostas!" substituído).
- **Testes:** `OpportunityViewTest` +6 (render dos 9 campos, guardar e
  persistir, edição com valores pré-existentes, validação com erros,
  campos vazios → null, relação propostaProduto); suíte **173/884**, pint
  OK, build OK.
- **Browser:** E2E `e2e-bloco6-proposta.mjs` verde (aba abre com form e
  9 campos, guardar com flash+resumo+badge Preenchida, reload mantém
  todos os valores, validação de período rejeita, 0 erros). Setup/cleanup
  em `setup-e2e-bloco6.php`/`cleanup-e2e-bloco6.php` (product `codigo`
  é NOT NULL — corrigido durante debugging).

---

### Segurança dos formulários + diagnóstico do 405 em produção (2026-10-12)

**Pedido/sintoma**: em produção (`https://csangroup.tech/login`) o login respondia
**405 Method Not Allowed**; verificar segurança dos forms.

- **Diagnóstico do 405 (cadeia completa)**: `APP_DEBUG=false` → o script do Livewire
  vem como `/livewire/livewire.min.js` → o **nginx bloqueia** esse caminho (404) → o JS
  não carrega → `wire:submit` nunca se liga → o form submete **nativamente** a
  `POST /login`, rota que só aceita GET → **405**. O 405 é o sintoma seguro (ver abaixo).
- **Fix no VPS (pendente de executar pelo utilizador, por esta ordem):**
  ```bash
  composer install --no-dev --optimize-autoloader
  php artisan vendor:publish --tag=livewire:assets --force   # passa a apontar ao estático /vendor/livewire/livewire.min.js (já 200)
  npm ci && npm run build
  php artisan optimize:clear && php artisan config:cache && php artisan route:cache
  # reiniciar PHP-FPM
  ```
  Verificação: F12/Network `<script src="/vendor/livewire/livewire.min.js?id=…">` a **200**.
- **Decisão de segurança (NÃO inverter)**: manter `method="POST"` + `@csrf` no login —
  é a salvaguarda que, se o JS falhar, faz o browser submeter como **POST** (405) em vez
  de **GET**, que exporia o password na query string (URL, histórico, logs).
- **`FormsSecurityTest`** (novo, regressão permanente): (1) TODOS os `<form>` de todas
  as views têm de declarar `method="POST"` (varredura recursiva); (2) o form de login
  publica para `route('login')` com `@csrf`.
- **Aplicado a todos os forms**: `method="POST"` + `@csrf` em todos os formulários do
  systema (login, logout, perfis, listas, etc.).
- Commit `c7af0ff`.

---

### Limpeza do repositório (2026-10-12)

- Protótipo estático (`prototipo/`) **removido do repositório** — decisão do utilizador:
  o repo fica só com o projecto Laravel (`adg/`) + documentação `.md`.
- `.gitignore` na raiz: `*.rar`, `*.zip`, `*.7z` (nunca mais lixo de backup no git).
- Regra de commits: **só ficheiros necessários** ao projecto; `coreto.md`/`problema.md`
  (docs do utilizador) nunca são commitados. Commit `68d9235`.

---

### Dashboard mini-cards + detalhe da oportunidade (2026-10-12)

- **Mini-cards do pipeline transbordavam** (`896fbd2`): o tema força
  `.card .card-header{display:flex}` (`theme-extract.css` L549) e os valores em Kz
  saíam para fora do cabeçalho. Fix com override escopado **no blade** (sem rebuild
  Vite): `.card .card-header.mini-stat-stack{display:block}` (empilhar valor sob o
  título) + `.kpi-money` com `clamp(15px…22px)`. E2E em **8 larguras** verde;
  suíte 188/944.
- **Detalhe da oportunidade** (`15c915e`): removidos o botão **"Editar"** e o menu de
  3 pontos do header de `opportunities/show.blade.php` — eram `javascript:void(0)`,
  **mortos** (edits fazem-se por outra via; fase mantém-se em badge, o kebab funcional
  é o da aba Actividades). Testes 35/35.

---

### Login em PT-PT + perfil reconstruído com criação de utilizadores (2026-10-12)

**Pedido**: mensagens de erro do login em português de Portugal; perfil no tema do
sistema, funções a funcionar, tudo em PT-PT e **com capacidade de criar contas**.

- **Locale PT**: nova pasta `adg/lang/pt/` (`auth.php`, `validation.php`,
  `passwords.php`) + `APP_LOCALE=pt` no `.env.example` e default `'pt'` em
  `config/app.php` (`'locale' => env('APP_LOCALE', 'pt')`).
  - Login errado → *"As credenciais não correspondem aos nossos registos."*;
    campo vazio → *"O campo email ou username é obrigatório."*
  - Atenção: se o `.env` de produção tiver `APP_LOCALE=en` explicitamente, obriga a `en`
    — garantir `APP_LOCALE=pt` no `.env` de produção.
- **Perfil reconstruído** (`resources/views/profile.blade.php`) no tema do sistema
  (x-app-layout, 4 cartões): **Os meus dados** (nome, username com regex
  `.`/`-`/`_`, email) · **Alterar palavra-passe** · **Criar conta de utilizador** ·
  **Eliminar conta** (form directo com password — modal Breeze removido).
- **NOVO componente** `livewire/profile/create-user-form.blade.php`: cria utilizadores
  a partir do perfil (username/email/password com regras de força).
- Componentes Volt existentes reescritos mantendo nomes (compatibilidade com testes):
  `update-profile-information-form`, `update-password-form`, `delete-user-form`.
- `ProfileTest` actualizado + testes de criação de utilizador → **suíte 191/954 verdes**.
  E2E login PT + perfil PT/tema/criar-utilizador verdes. Commit `4e9fceb`.

---

### PWA: barra de instalação no mobile (2026-10-13)

**Sintoma**: "no celular o PWA não mostra para instalar".

- **Diagnóstico**: manifesto (`manifest.webmanifest` — standalone, start_url
  `/dashboard`, ícones 192/512/maskable), ícones e SW estavam **correctos** e ligados
  nos 2 layouts; o que faltava era **capturar `beforeinstallprompt`** — não havia
  QUALQUER UI de instalação no projecto (grep = 0 ocorrências). A banner automática do
  Chrome depende de "engajamento" e é suprimível; e em **iOS/Safari** o evento nem
  existe (só "Partilhar → Adicionar ao Ecrã Inicial").
- **Novo componente `components/ui/install-pwa.blade.php`** (`<x-ui.install-pwa />`,
  incluído em `layouts/app.blade.php` e `layouts/guest.blade.php`):
  - **Android/Chrome/Edge/desktop**: captura `beforeinstallprompt` → barra fixa no
    fundo "Instalar aplicação" (ícone + botão **INSTALAR** + ✕) → `deferredPrompt.prompt()`;
  - **iOS/Safari**: mostra a barra logo de início e, ao tocar em INSTALAR, abre guia PT
    (*"1. Toque no botão Partilhar 2. Toque em 'Adicionar ao Ecrã Inicial'…"*);
  - esconde se já instalada (`display-mode: standalone`/`appinstalled`) e o fecho
    **persiste** (`localStorage['pwa-install-dismissed']`).
- **`layouts/guest.blade.php` ganhou o registo do SW** (`/sw.js`) — faltava no login,
  deixando a página de login instalável em qualquer versão do Chrome.
- **E2E `e2e-pwa-install.mjs` verde**: banner visível no login (UA iPhone), instruções
  PT no diálogo, fecho persistente após reload, manifesto `200` com
  `content-type: application/manifest+json`, banner no DOM do dashboard. Suíte 191/954.
  Commit `9c1d7a1`.

---

### Recuperar/redefinir senha no design do sistema (2026-10-13)

**Pedido**: *"em recuperar senha a tela não segue o design — melhore"*.

- **Antes**: páginas `forgot-password`/`reset-password` com markup Breeze por omissão —
  texto em inglês, botão **preto** `bg-gray-800`, inputs `border-gray-300`/`indigo-500`,
  sem título e sem forma de voltar atrás.
- **CSS novo no `layouts/guest.blade.php`** (escopado, **sem rebuild Vite**):
  `.auth-title` (22px/700), `.auth-subtitle` (14px, `--text-muted`), variante
  `.subactions-row.is-center` + ajustes mobile (20px/13.5px).
- **`forgot-password` reescrito**: título "Recuperar senha" + subtítulo PT, campo
  `.field-input`, botão azul `.btn-sign-in` **"Enviar link de redefinição"** (estado
  `wire:loading` "A enviar…"), alerta verde `.guest-status-alert` no sucesso
  (*"Enviamos por email o link…"*), erro por campo PT, link **"← Voltar ao login"**.
- **`reset-password` reescrito**: título "Redefinir senha", email pré-preenchido do
  link, 3 campos (**Email / Nova senha / Confirmar nova senha**) cada um com **olho**
  mostrar/ocultar (onclick auto-contido, ícones `.eye-open`/`.eye-off`), botão
  **"Redefinir senha"** ("A guardar…") + "← Voltar ao login". Lógica PHP **inalterada**.
- Ambos mantêm `method="POST"` + `@csrf` (`FormsSecurityTest`) e `novalidate`
  (validação Livewire → mensagens PT, não as do browser).
- **Verificação**: `PasswordResetTest`+`FormsSecurityTest`+`PasswordConfirmationTest`
  9/9; E2E desktop+mobile (`e2e-recuperar-senha.mjs`): títulos PT, botão
  `rgb(52,84,209)`, **0 marcas Breeze**, olho a alternar `password→text`, erros PT
  (*"O campo email é obrigatório."*, *"A confirmação de palavra-passe não
  corresponde."*), links `/login` — verde; **suíte completa 191/954 verdes**.
  Commit `679c2a0`.
- **Nota**: as páginas *"Confirmar senha"* e *"Verificar email"* continuam no estilo
  Breeze antigo (inglês) — não fazem parte do fluxo de recuperar senha; ficam pendentes
  de pedido.

---

### ESTADO ACTUAL (2026-10-13)

- **Commits pushed** (main = `origin/main`, último `679c2a0`): `c7af0ff` segurança forms,
  `68d9235` limpeza repo, `896fbd2` mini-cards, `15c915e` detalhe oportunidade,
  `4e9fceb` login PT + perfil + criar utilizador, `9c1d7a1` PWA instalação,
  `679c2a0` recuperar/redefinir senha.
- **Suíte: 191 testes / 954 assertions, todos verdes**; pint OK; E2Es de regressão verdes.
- **Pendências em aberto:**
  1. **VPS (bloqueante, acção do utilizador)**: executar os comandos do 405 acima e
     subir os ficheiros dos commits `4e9fceb`, `9c1d7a1` e `679c2a0`;
     garantir `APP_LOCALE=pt` no `.env` de produção; `php artisan view:clear`/`optimize:clear`.
  2. "Confirmar senha" e "Verificar email" ainda em estilo Breeze/inglês (aguarda OK).
  3. PWA: em Android a barra só aparece quando o Chrome disparar o evento
     (critério de engajamento do browser); no iPhone aparece sempre.

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
| 2026-10-06 | **Sprint 8 concluído** (`fc419f8`) — Produtos: Livewire ProductList/ProductForm, views baseadas em customers.html, rotas produtos/produtos.nova/produtos.{produto}, sidebar acordeão; Model/Factory/Seeder já existiam; 83 testes, 410 assertions, pint, build OK |
| 2026-10-06 | **Sprint 9 concluído** (`75f06b2`) — Comerciais: Livewire CommercialList/CommercialForm, views baseadas no padrão, rotas comerciais/comerciais.nova/comerciais.{commercial}, sidebar accordion; Model/Factory/Seeder já existiam; 83 testes, 410 assertions, pint, build OK |
| 2026-10-06 | **Sprint 10 concluído** (`5ea436a`) — Alertas: enum AlertType, migration alerts, Model/Factory/Seeder (5 registos), AlertService (5 tipos de alerta, não move leads), command alerts:generate (daily 06:00), NewAlert notification (email+database), Livewire AlertList (filtros, paginação, marcar lido/resolvido), header badge + dropdown notificações, sidebar accordion; 83 testes, 410 assertions, pint, build OK |
| 2026-10-06 | **Sprint 12 concluído** (`hash`) — Testes e Lançamento: spatie/laravel-backup instalado e configurado (backup diário 02:00, cleanup 03:00, monitor 04:00), log channel backup, schedule de backup/cleanup/monitor, SSL preparado, testes finais 83 passed, projecto pronto para produção |
| 2026-10-06 | **Revisão geral concluída** — bug raiz dos ícones no extractor de CSS (`@font-face` vazios + selector `i` descartado), crashes de `/analytics`, `/contratos`, 4 formulários e "editar" sem prefill, Chart.js ligado, sino/"marcar todas" do header, `wire:key`/`x-cloak` malformados, código morto Breeze removido, auditoria de `route()`; 113 testes (83→113), pint, build OK |
| 2026-10-08 | **Pipeline drag & drop + loading state** — fix Alpine `$parent is not defined` via `CustomEvent` + `$listeners = ['kanbanMove' => 'move']`; adicionado `$movingId` + loading overlay no card (spinner "A mover...") via Alpine `x-show="loading"`; eventos `CustomEvent('kanban-move')` + listener Livewire; 113 testes passam. |
| 2026-10-08 | **Drag & drop refeito + Alpine único (fase final)** — causa raiz do `Unexpected token ')'`/`Detected multiple instances of Alpine running`: `app.js` arrancava um 2.º Alpine; removido, `sidebar` passou a registar-se em `alpine:init`, `@livewireStyles`/`@livewireScripts` explícitos nos layouts (`/dashboard` e `/ui` não têm Livewire); drop do kanban sem interpolação JS (`data-opp`/`data-dropzone`) com `await $wire.$call('move', ...)` + loading `loadingId` (spinner "A mover..." só no cartão em curso); removidos `CustomEvent('kanban-move')`, `$listeners`, `$movingId` e `charts.js` (Chart.js morto — gráficos são ApexCharts); verificado: 113 testes/580 assertions, pint, build, 20 expressões Alpine parseadas, teste jsdom+Alpine 12/12 PASS. |
| 2026-10-08 | **Fix kanban vertical + loading preso** — `<div class="kanban-card">` sem `</div>` de fecho (faltava 1 por cartão desde a adição do `position-relative`/overlay): cartões aninhavam → colunas nasciam dentro de cartões → kanban todo em vertical; morph do Livewire falhava no `move()` → spinner preso. Correcção: fecho do cartão; validado com `validate-real.mjs` (jsdom+Alpine sobre HTML real, 14/14 PASS, divs 346/346, 0 aninhados, 9 colunas planas, spinners escondidos e limpos); 113 testes/580 assertions, pint, build estável. |
| 2026-10-08 | **Consola limpa + sparklines do dashboard** — fix aviso `undefined color - ApexCharts` (payload `faturacaoMensal` do DashboardService sem `color` → `colors: ['#3454d1']`, igual ao protótipo); 3 sparklines não renderizavam (`@push('scripts')` em dashboard.blade sem nenhum `@stack` no projecto → `@stack('scripts')` adicionado a `layouts/app.blade.php`); `Unexpected token '&'` no script pushado (`{{ json_encode }}` → `@json`); validado E2E com Chrome real (`browsertest/e2e-*.mjs`): dashboard 4 gráficos, analytics 4, 0 avisos/0 erros, fluxo wire:navigate+drags+filtro+pesquisa limpo; 113 testes/580 assertions, build `app-DMLx1amv.js`. |
| 2026-10-08 | **Loading dos cartões do kanban removido** (pedido do utilizador) — removidos overlay "A mover…", `loadingId`+`async/finally`, `:class opacity-50` e wrapper `position-relative` de `pipeline.blade.php`; cartão volta à estrutura do protótipo (`kanban-card > card-body`); drop = `$wire.$call('move', id, stage)` directo; validado jsdom 16/16 + Chrome real 10/10 (0 x-show/0 spinners, drag OK, consola 0 mensagens), 113 testes/580 assertions, build `app-Bb8PbBCS.css`. |
| 2026-10-08 | **Dashboard mini-cards PT + gráficos visíveis** — Facturação `col-xxl-12`, 3 mini-cards juntos numa row (`col-lg-4`) com títulos PT do protótipo (Actividades Concluídas/Novas Actividades/Encomendas Entregues), contadores X/Y do `DashboardService`, descrição "desde a semana passada" **dentro** do card (removido `width:100%` → `flex:1 1 auto; min-width:0`); fix raiz dos gráficos vazios em `apexcharts-init.js` (`data: payload.series` aninhava a série → Apex via 1 ponto → eixo 0-6; helper `toSeries()` + formatter M/K); `DashboardDemoSeeder` (actividades 6 meses + 12 aprovações) e `ContractSeeder` corrigido (produtos do catálogo antigo → fallback; 9 contratos com entregues/estados variados); DashboardTest títulos PT 4/4, 113 testes/580 assertions, pint, build `app-Bb8PbBCS.css` + `app-DMLx1amv.js`. |
| 2026-10-08 | **Painel Executivo: header único + KPIs sem overflow + charts reordenados** — removido o `content-area-header` duplicado de `analytics.blade.php` (badge "Somente leitura" passou a `pageHeaderRight` em `analytics/index.blade.php` → 1 header igual ao protótipo); KPIs em dinheiro com novo `OpportunityService::formatKzCompact()` ("Kz 305,2M", precedente do footer da Facturação) + classe `.kpi-money` (`clamp(15px,1.35vw,22px)`, nowrap) — antes vazavam do card a ~1400px e faziam wrap; Vendas Mensais → `col-xxl-12` e "Estado das Encomendas" juntou-se a Pipeline/Segmento numa row de 3 donuts; **bónus**: `dataLabels.formatter` dos donuts corrigido (Apex 7.x já passa % — mostrava 741%/123%/554%, agora soma 100%); E2E 1600+1400px: 1 header, sem overflow, donuts no mesmo y (847/863), consola 0 mensagens; 113 testes/580 assertions, pint, build `app-BZlEFgV4.css` + `app-CqMpB9BH.js`. |
| 2026-10-08 | **Agenda Comercial: 3 bugs raiz + menu/filtros no header** — **bug #1**: Tailwind gera `.collapse{visibility:collapse}` (utility) que colide com a `.collapse` do Bootstrap → **todos os itens da lista eram invisíveis** (o registo id 11 "fdgdg" estava na BD mas nunca se via → "criar não salva nada"/"filtros não funcionam"); fix `.collapse{visibility:visible}` fora de `@layer` em `app.css` (corrige também o accordion do Pipeline); **bug #2**: `.stretch-full{height:calc(100%-24px)}` dava 7514px por grupo de data (29 cartões, 218k de scroll) → `.apps-tasks .content-area-body > .card.stretch{height:auto}`; menu 3º item → "Nova Actividade"; painel esquerdo removido nas 3 páginas da Agenda → lista com dropdown "Filtros" (grupos Estado/Tipo etiquetados + "Todos os…") e botão Nova, calendário com dropdowns "Tipos" e "Próximos"; form sem sidebar (links saíam do form), resumo de erros + `wire:loading` + flash "Actividade guardada com sucesso" na lista; header Lista/Calendário em `hstack` (antes empilhado); E2E criar→visível→eliminar + filtros + validação OK, 113 testes/585 assertions, pint, build `app-CJdTR_nW.css` + `app-CqMpB9BH.js`. |
| 2026-10-08 | **Calendário da Agenda: modal de detalhes do evento** — clique no evento deixou de abrir "Nova Actividade" (`wire:click="abrirDetalhe"` em vez de `wire:navigate` para `agenda.create?atividade=`); modal Bootstrap markup-puro controlado por estado Livewire (`$detalheId`, classes `show d-block`), com Cliente/Estado/Tipo/Data/Hora/Prazo/Próxima acção/Resultado + acções Editar (→ form com prefill), Concluir (só pendente), Eliminar (confirm Alpine) e Fechar; travão do body via `$this->dispatch('modal-actividade', aberto: …)` + listener Alpine na raiz (entangle `.live` descartado — devolve objecto truthy e prendia `modal-open` no page load) + limpeza global em `app.js` (`livewire:navigating/navigated`); 2 testes novos (115/607), pint, E2E `e2e-calendario-detalhe.mjs` 100% verde com 0 erros de consola (abrir/editar/concluir/fechar/backdrop/eliminar + baseline limpo), build `app-CJdTR_nW.css` + `app-CjzK-USo.js`. |
| 2026-10-08 | **Modal nítido (fix "opacitada") + modal de confirmação global** — bug: `theme-extract` tem `body.modal-open .nxl-container{filter:blur(3px)}` e, como o nosso modal vive DENTRO de main (Livewire), o filtro borrava também o próprio modal (no protótipo os modais estão depois de `</main>`); fix: `filter:none` nos 4 seletores em `app.css` + blur passou para o `.modal-backdrop` (`backdrop-filter` em inline-style porque o minifier dropa a propriedade standard e só deixa `-webkit-`, que o Chrome ignora) → fundo borrado, modal nítido. Novo **modal de confirmação global** `#modalConfirmar` (store Alpine `confirmar` com Promise, `abrir()/responder()`, fecha por botão/ESC/backdrop, markup no layout depois de `</main>`) a substituir os 6 `confirm()` nativos (activity-list/calendar, alert-list, product-list, conciliaction-list, commercial-list) no padrão `$store.confirmar.abrir({...}).then(ok => ok && $wire.remove(id))`; teste DashboardTest com varredura recursiva de views proibindo `confirm(`; E2E `e2e-confirmar.mjs` (0 dialogs nativos, cancelar por botão/ESC, confirmar na lista, confirmação empilhada sobre o modal de detalhes com blur em ambos os backdrops) + `e2e-calendario-detalhe.mjs` actualizado; 116 testes/610 assertions, pint, build `app-CQXN0hf4.css` + `app-DZulwGZq.js`. |
| 2026-10-08 | **Painel "Regras" removido da Nova Actividade** (pedido do utilizador — "a estragar o desempenho da interface") — removido o card lateral com as frases "Descrição, Próxima acção e Prazo são obrigatórios / Status inicia sempre em Pendente / Prazo vencido gera alerta PRAZO" de `activity-form.blade.php`; a coluna direita fica só com "Legenda de tipos" (stretch-full); assert `assertSee('Regras')` removido do AgendaTest; verificado em Chrome (0 erros, form completo, legenda presente); 116 testes/609 assertions, pint, build `app-CQXN0hf4.css` + `app-DZulwGZq.js`. |
| 2026-10-08 | **Conciliações: sidebar vertical removida (padrão Agenda) + tabela desajustada corrigida** — `conciliaction-list` sem `content-sidebar` (botão `+ Nova Conciliação` e grupos "Classificação/Estado" no dropdown Filtros), `conciliaction-form` sem sidebar/hamburger; causa raiz: sidebar 300px cortava 156px do `.content-area` e a tabela nowrap não cabia no wrap; CSS `min-width:0` na cadeia flex corrige o corte em 1366px (passa a scroll interno); `ConciliactionsTest` espelha o AgendaTest; E2E 1600+1366 verdes (0 erros); 116 testes/615 assertions, pint, build `app-DQdxitU4.css` + `app-DZulwGZq.js`. |
| 2026-10-08 | **Produtos, Alertas, Comerciais e Contratos sem sidebar vertical (padrão Agenda/Conciliações)** — 7 views (`product-list/form`, `alert-list`, `commercial-list/form`, `contract-list/form`): removidos `content-sidebar`+`sidebarOpen`+hamburger; botões `+ Novo X` no header (Alertas sem criação), dropdowns Filtros com grupos `<h6>` (Produtos: Estado+Comercial; Alertas: Tipo+Estado; Comerciais/Contratos: Estado, "Todos"→"Todos os estados"); 4 testes de lista com novas asserções (+20); E2E `e2e-listas-sem-sidebar.mjs` 1600+1366 verdes (sem clip, 0 erros, forms sem sidebar); zero views com `content-sidebar`; 116 testes/635 assertions, pint, build `app-DQdxitU4.css` + `app-DZulwGZq.js`. |
| 2026-10-08 | **Coluna Observações desalinhada + varredura de tabelas** — causa raiz: `theme-extract.css:163` mete `display:-webkit-box!important` (`.text-truncate-1-line`) directo no `<td>` → `flow-root` em vez de `table-cell` (altura 53 vs 61, conteúdo ao alto, tira branca nas linhas vermelhas); fix em 3 views (conciliaction-list Observações, commercial-list Descrição, alert-list Mensagem): classe movida para `<div>` interno; varredura global (grep + runtime em 13 páginas: 346 tds + 63 ths) confirma **0 células distorcidas no protótipo inteiro**; 116 testes/635 assertions, pint, 2 E2E verdes, build `app-DQdxitU4.css` + `app-DZulwGZq.js`. |
| 2026-10-09 | **Pendências resolvidos** — commit dos 143 ficheiros pendentes (`eeefe7c`+`d56259e`); fix notificações (1 `.notifications-item` por alerta — estava 1 wrapper flex com todos os desc's em grid horizontal); `agenda:reminder` cria alerta `AGENDA` (§11, entidade nullable, anti-duplicado); **bug latente**: tabela `notifications` inexistente rebentava `alerts:generate` → migration criada; `alerts:generate --dry-run` real; `contracts:expire` 05h30 (suposição §19.12); `.env.example` MySQL, suposições 14/14 ✅, CHECKLIST DE DEPLOY, `teste-manual.md`; E2E gráficos (8/8 com dados reais) + sectores variados no seed (donut Segmento 1→6 fatias); 121 testes/659 assertions, pint, 2 E2E verdes, build `app-DQdxitU4.css` + `app-DZulwGZq.js`. |
| 2026-10-09 | **Modal de detalhe do alerta (sino + página Alertas)** — novo `AlertBell` (sino Livewire com badge live, item clicável, dropdown fecha sozinho, markup do `<a>` preservado para o regex de `AlertsTest`) + partial `detail-modal` (padrão Agenda: tipo/estado, mensagem completa, entidade com "Ver registo", criado por/em, prazo, acções lido/resolvido/eliminar-com-confirmação/fechar, IDs distintos Sino/Lista); `AlertList` ganha `abrirAlerta`/`fecharDetalhe` + Mensagem clicável, `remove` fecha a modal activa; 124 testes/668 assertions, pint, E2E novo `e2e-alerta-modal.mjs` (marcador auto-apagável) + re-verde de notificações, build `app-CDSVpqV-.css` + `app-DZulwGZq.js`. |
| 2026-10-09 | **Sprint 13 concluído — Tabela de Clientes** (`seguir.md`) — 6 campos novos denormalizados em `opportunities` (codigo/3 booleans/2 estados, migration+fillable+casts+factory), enums `ConciliacaoEstado`/`ContratoEstado` com `badgeClass()`, seeder 10 clientes CLI001–CLI010 (percentagens §10.2), componente `ClientTable` (pesquisa, filtros KAM/Compras/Conciliação/Contratos + contagem + reset, ordenação, 20/pág., só aprovados, relação `commercial()`), view `#customerList` 10 colunas + empty state + olho→`opportunities.show`, rota `clients.index` (`Route::view`), sidebar item standalone `feather-users`, sincronização §14.1 em `ConciliactionService`+`ContractService` (raw `update`, fallbacks PENDENTE/ACTIVO); 139 testes/725 assertions, pint, E2E novo `e2e-clientes.mjs` ×3 + re-verde de 3 E2Es, build `app-CDSVpqV-.css` + `app-DZulwGZq.js`. |
| 2026-10-09 | **Pipeline: valores compactos (fix overflow)** — cartões "Pipeline Aberto/Aprovado (mês)/Ponderado" passam a `formatKzCompact()` (igual ao Painel Executivo, ex. "Kz 339,7M") com valor completo no `title`; kanban alinhado (total da coluna + valor do cartão, ambos com `title` completo); E2E `e2e-pipeline-valores.mjs` 1600+1366 sem overflow (4 stats + 9 totais + 50 cartões), 17 testes + pint OK, sem mudança de CSS/JS. |
| 2026-10-09 | **Kanban: navegação sem scrollbar lateral** — setas ← → na FilterBar (scroll suave 2 colunas, disabled nas extremidades, estado via scroll/morphed) + auto-scroll durante o drag nas bordas do board (14px/frame, pára em drop/dragend/fora do board, sem scroll fantasma); `x-data` unificado no wrapper com `$refs.board`; E2E novo `e2e-kanban-navegacao.mjs` ×2 verde (0→564→0, auto-scroll direita/esquerda, 0 erros), PipelineTest 9/9. |
| 2026-10-09 | **"Apagar não funciona" → Agenda 500 por activities órfãs** — raiz: `DashboardDemoSeeder` ligava actividades às opps `[demo-hist]` e soft-deletava-as no mesmo run (cascade das FKs não corre em soft delete); `/agenda` dava 500 em `$activity->opportunity->nome`. Fixes: seeder exclui demo do pluck + apaga activities antes do soft delete, `whereHas('opportunity')` em ActivityList/Calendar, guards `?->?? '—'` em 6 views, 4 órfãs removidas da BD, teste de regressão; E2E `e2e-apagar-todos.mjs` valida apagar em 4 listas + alertas (0 erros), suite 140/729, pint OK. |
| 2026-10-09 | **Fix `drop is not defined` no kanban** — o método `drop()` ficou para trás quando o `x-data` subiu do board para o wrapper (tarefa das setas); reposto no objecto Alpine. E2E novo `e2e-kanban-drop.mjs` valida drop real + reposição + `drag-over` limpo com 0 erros (×4), navegação ×2, PipelineTest 9/9, pint OK. |
| 2026-10-09 | **Raiz `/` → login** — `Route::view('/', 'welcome')` (tela do Laravel) trocada por `Route::redirect('/', '/login')`; guest vê o login, autenticado cai no `/dashboard` (middleware `guest`), logout volta ao login. `ExampleTest` → `assertRedirect('/login')`; 140 testes / 730 assertions, pint OK. |
| 2026-10-10 | **Sprint Ajustes BLOCO 1 — ícones alinhados** — dropdown de 3 pontos removido das Acções de Conciliações/Contratos/Produtos; `.action-icons` (flex, gap 4px, `.btn-icon` 32×32) nas 3 listas; novo `ContractList::remove()` (soft delete); +4 testes → 144/749, pint/build OK; E2E `e2e-bloco1-icoes.mjs` valida alinhamento, 0 dropdowns, toggle a funcionar. |
| 2026-10-10 | **Sprint Ajustes BLOCO 2 — Dashboard** — gráfico "Facturação Mensal"→"Pipeline por Mês" (contagem de abertas/mês, escala 0..N sem Kz — feedback do cliente), 3 mini-cards→4 do pipeline (aberto/aprovado mês/ponderado/conversão, valores do Stats), KPI "Encomendas em Curso"→"Produtos Activos"; +2 testes → 146/757, pint/build OK; E2Es `e2e-bloco2-dashboard`, `e2e-eixo-y-reais` e `e2e-graficos` verdes. |
| 2026-10-10 | **Sprint Ajustes BLOCO 3 — Agenda tipo Outros** — enum `ActivityType::OUTROS`, migration `tipo_outros`, campo "Especifique" condicional no form (`required_if`), `tipoDisplay()` na lista e no modal do calendário; +5 testes → 151/772, pint/build OK; E2E `e2e-bloco3-outros` verde. |
| 2026-10-10 | **Sprint Ajustes BLOCO 4 — Aba Actividade** — componente `OpportunityActivities` (filtra por opp, toggle/remove escopados), aba substitui placeholder, form com `?oportunidade={id}` pré-seleccionado + redirect ao detalhe; +4 testes → 155/789, pint/build OK; E2E `e2e-bloco4-aba-actividades` verde. |
| 2026-10-11 | **Sprint Ajustes BLOCO 5 — Aba Nota** — tabelas `notes`/`note_attachments`, enum `NoteType`, models+factory, componentes `NoteList`/`NoteForm` (fixadas, pesquisa, filtro, anexos privados em `local`, toggle/remove escopados); +12 testes → 167/835, pint/build OK; E2E `e2e-bloco5-notas` verde. |
| 2026-10-11 | **Sprint Ajustes BLOCO 6 — Aba Proposta** — migration com 9 campos em `opportunities` (produto FK, preço, volume+unidade L/m³, período, crédito, facturação, non-oil), model fillable/casts/reção `propostaProduto()`, componente `OpportunityProposal` (validação `in:L,m³`, `after_or_equal`, vazios→null), aba substitui placeholder com form + resumo; +6 testes → 173/884, pint/build OK; E2E `e2e-bloco6-proposta` verde. **Sprint de Ajustes (BLOCOS 1–6) concluído.** |
| 2026-10-11 | **Login redesenhado (frontend)** — `guest.blade.php` e `login.blade.php` reescritos a partir de `prototipo/ui/index.html`: split-screen, coluna esquerda com form + showcase azul #3454d1 com SVG geométrico, hero card `avatar/1.png`, carrossel 3 slides com dots/swipe, toggle de senha olho SVG, checkbox "lembrar" custom, loading `wire:loading`; bindings Livewire `form.login`/`form.password`/`form.remember` intactos; CSS isolado sob `.auth-guest-page`, botão `.btn-sign-in` (evita colisão Bootstrap); `AuthenticationTest` actualizado; 20 testes auth ✅; E2E `e2e-login-novo.mjs` verde. |
| 2026-10-11 | **Correcções pré-deploy de código** — agendado `backup:run --only-db` 02h30 (antes do clean 03h — sem run, clean/monitor nunca tinham backups); `ProductionSeeder` novo (só User + Settings via `ADMIN_PASSWORD`/`ALERTS_EMAIL`, sem dados demo); `.env.production.example` (APP_ENV=production, DEBUG=false, URL https://csangroup.tech, SESSION_SECURE_COOKIE, timezone Africa/Luanda); pint ✅, DashboardTest ✅; commit `6680cbc`. |
| 2026-10-11 | **`pronto.md`** — guia de produção completo (11 secções): 15 módulos + 6 automações, stack, requisitos da VPS (2 vCPU/4GB/40GB SSD, Ubuntu 24.04, PHP 8.3+ com 18 extensões, MySQL 8 **com `mysqldump`** obrigatório p/ Spatie Backup, Supervisor, cron, Let's Encrypt), configuração CloudPanel, deploy passo-a-passo, backups, segurança, checklist 18 itens. |
| 2026-10-11 | **Dashboard: cards do gráfico → fases da pipeline** — `DashboardService::$statsCards` substitui cards de facturação (Pendente/Recebido/Cancelado/Total em Kz, resto do protótipo) por contagem de `Opportunity` nas 4 fases escolhidas pelo cliente (Lead, Qualificação, Proposta/Negociação, Cliente Aprovado); cada card com `link` (pipeline/clientes), plural correcto e barra de % sobre o total; view clicável; +1 teste → 174/899; pint ✅; E2E `e2e-dash-cards.mjs` verde; commit `fc8849b`. |
| 2026-10-11 | **Pendência 1 (fase.md) — PWA leve (Opção B)** — `public/manifest.webmanifest` (standalone, pt, theme #3454d1, start_url /dashboard), ícones 192/512/maskable + apple-touch 180 gerados com GD do logo horizontal (canvas quadrado branco), `public/sw.js` cache-first **só assets estáticos** (build/assets/images/fonts) — nunca HTML nem POST (Livewire intacto, revalidação em background); meta tags nos layouts `app` + `guest`; E2E `e2e-pwa.mjs` verde (manifest, SW activo, dashboard renderiza com SW); commit `7f6d8e2`. |
| 2026-10-11 | **Pendência 2 (fase.md) — `VALIDACAO_CLIENTE.md`** — documento para o Adailson: os 6 blocos + login novo + analytics, com o que mudou, como testar, o que confirmar, 6 critérios de aceitação e formulário de feedback (✅/⚠️/❌ por bloco); commit `5013ce0`. |
| 2026-10-11 | **Pendência 3 (fase.md) — nº de testes confirmado** — suíte completa: **174 testes / 899 assertions, todos verdes** (resolvida a divergência 140/730 vs 173/886); mapa de cobertura dos 15 módulos + 5 fluxos de auth em `deploy.md` §3.1/3.2; nenhum módulo funcional sem testes (falta só unitário directo de `Setting`, exercitado indirectamente, e PWA via E2E); números accionados em `pronto.md`, `README.md`, `realizado.md`; commit `e836113`. |
| 2026-10-11 | **Pendência 4 (fase.md) — backup com destino externo** — disco `backup-cloud` em `filesystems.php` (Wasabi/B2/R2/MinIO/S3, path-style, credenciais `BACKUP_CLOUD_*` próprias), ligado ao `config/backup.php` só se `BACKUP_CLOUD_DISK=s3`; sem a variável o backup é só local (regressão verificada); `BackupConfigTest` 4/4; `backup:run` manual verificado — **achado:** `mysqldump` fora do PATH faz o comando "travar" 10 min (tries 3 × retry 300s), documentado no `pronto.md`; commit `f2e6547`. |
| 2026-10-11 | **Pendência 5 (fase.md) — variáveis de produção conferidas** — auditadas as 138 `env()` do projecto (maioria defaults do framework não usados: Redis/SQS/DynamoDB/Postmark/Papertrail); acrescentadas 4 lacunas reais ao template: `APP_MAINTENANCE_STORE=database`, `LOG_DAILY_DAYS=30`, `SESSION_SAME_SITE=lax`, `BACKUP_NOTIFICATION_EMAIL`; cabeçalho com checklist do que é obrigatório preencher + verificação; commit `d61b8a8`. |
| 2026-10-11 | **Pendência 6 (fase.md) — comando `user:password`** — redefine a password do utilizador único sem aceder à BD; regras 12+ chars, maiúscula, minúscula, número, símbolo; `--generate` cria password forte (20 chars) e mostra-a uma vez; alvo por email ou o utilizador único; +8 testes (validação de força, prompts, mismatch, generate, email inexistente); documentado no `pronto.md` §5/§11; commit `4678c53`. |
| 2026-10-11 | **Pendência 7 (fase.md) — checklist VPS completa** — `pronto.md` ganhou §4.7 firewall UFW (só SSH/HTTP/HTTPS), item de `mysqldump` no PATH e de activação do backup externo no checklist, caminho do backup corrigido (`storage/app/private/ADG/`, não `app/backups`) e tabela §9 com estados actualizados (PWA leve ✅, backup externo ✅, password ✅, 2FA fase 2); commit `71d716b`. |
| 2026-10-11 | **`fase.md` completo + todas as pendências concluídas** — o ficheiro original cortava na Pendência 3; escrito o resto (4–7) e executado tudo pela ordem. Suíte final **186 testes / 940 assertions, todos verdes**. |
| 2026-10-11 | **Limpeza pós-validação (alterações do cliente)** — cards "Dicas" removidos de Nova Oportunidade/Contratos/Conciliações, card "Painel Executivo" removido do analytics, sidebar "CS & AN / Lead Inteligente", imagens substituídas (logo, favicon, avatar), scripts `dev.bat`/`dev.ps1` apagados; `OpportunityCreateTest` ajustado (assert "Dicas" removido); commit `a6f99ec`. |
| 2026-10-12 | **Segurança dos formulários + diagnóstico do 405 de produção** — todos os `<form>` com `method="POST"` + `@csrf` (antes: forms do protótipo estático sem method submetiam GET e expunham dados na query string); novo `FormsSecurityTest` (varredura recursiva de todas as views + login com route+csrf); **405 do login em produção** diagnosticado: `APP_DEBUG=false` → `/livewire/livewire.min.js` bloqueado pelo nginx (404) → JS não carrega → `wire:submit` não liga → POST nativo a rota GET-only → 405; fix no VPS = `vendor:publish --tag=livewire:assets --force` (fica no estático `/vendor/livewire/livewire.min.js`, já 200) + optimize:clear/restart; **decisão: NÃO remover method=POST** (405 é o modo seguro, GET exporia a password); commit `c7af0ff`. |
| 2026-10-12 | **Limpeza do repositório** — protótipo estático removido (repo = só `adg/` + `.md`), `.gitignore` raiz para `*.rar/zip/7z`; regra: commits só com ficheiros do projecto, `coreto.md`/`problema.md` nunca; commit `68d9235`. |
| 2026-10-12 | **Dashboard mini-cards sem overflow** — tema força `.card .card-header{display:flex}` (`theme-extract.css` L549) e os valores Kz saíam do cabeçalho; override escopado no blade `.mini-stat-stack{display:block}` + `.kpi-money{clamp(15-22px)}` (sem rebuild Vite); E2E 8 larguras verde; commit `896fbd2`. |
| 2026-10-12 | **Detalhe da oportunidade: botões mortos removidos** — "Editar" e menu 3-pontos do header de `opportunities/show.blade.php` eram `javascript:void(0)`; removidos, mantendo badge de fase e kebab funcional da aba Actividades; testes 35/35; commit `15c915e`. |
| 2026-10-12 | **Login em PT-PT + perfil reconstruído + criação de utilizadores** — `lang/pt/` (auth/validation/passwords) + `APP_LOCALE=pt` (config default `'pt'`); erros do login em PT ("As credenciais não correspondem aos nossos registos."); perfil reescrito no tema do sistema com 4 cartões (Os meus dados / Alterar palavra-passe / **Criar conta de utilizador** / Eliminar conta), novo componente `create-user-form`, username com regex `.`/`-`/`_`, delete sem modal Breeze; E2E login+perfil PT verdes; suíte **191/954**; commit `4e9fceb`. |
| 2026-10-13 | **PWA: barra de instalação no mobile** — manifesto/ícones/SW já estavam correctos mas **não havia `beforeinstallprompt` em lado nenhum** (0 UI de instalação); novo componente `<x-ui.install-pwa />` nos 2 layouts: Android/desktop captura o prompt → barra "Instalar aplicação" no fundo com INSTALAR/✕, iOS mostra guia PT ("Partilhar → Adicionar ao Ecrã Inicial"), esconde se instalada e o fecho persiste (localStorage); registo do SW adicionado ao layout do login; E2E `e2e-pwa-install.mjs` verde (manifesto `application/manifest+json`); suíte 191/954; commit `9c1d7a1`. |
| 2026-10-13 | **Recuperar/redefinir senha no design do sistema** — as 2 páginas estavam em markup Breeze (inglês, botão preto `bg-gray-800`, inputs indigo); CSS novo no `guest.blade` (`.auth-title`/`.auth-subtitle`/`.subactions-row.is-center` + mobile, sem rebuild Vite); `forgot-password` = título PT + campo `.field-input` + botão azul "Enviar link de redefinição" + alerta verde + "← Voltar ao login"; `reset-password` = email pré-preenchido, 3 campos com olho mostrar/ocultar, botão "Redefinir senha"; lógica PHP inalterada, `method=POST`+`@csrf` mantidos; E2E desktop+mobile verde (0 marcas Breeze, erros PT), PasswordResetTest+FormsSecurityTest 9/9, suíte **191/954**; commit `679c2a0`. Pendente: "Confirmar senha"/"Verificar email" continuam em Breeze. |
