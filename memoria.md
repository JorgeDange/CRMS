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
| Sprint 2 — Pipeline | ⛔ Não iniciado | — | Próximo |
| Sprint 3 — Nova Oportunidade + Detalhe | ⛔ Não iniciado | — | |
| Sprint 4 — Importação Excel | ⛔ Não iniciado | — | |
| Sprint 5 — Agenda Comercial | ⛔ Não iniciado | — | |
| Sprint 6 — Conciliações | ⛔ Bloqueado | — | Aguarda definição do cliente (§19) |
| Sprint 7 — Contratos | ⛔ Bloqueado | — | Aguarda definição do cliente (§19) |
| Sprint 8 — Produtos | ⛔ Não iniciado | — | |
| Sprint 9 — Alertas | ⛔ Não iniciado | — | |
| Sprint 10 — Dashboard + Painel Executivo | ⛔ Não iniciado | — | |
| Sprint 11 — Testes e Lançamento | ⛔ Não iniciado | — | |

**Próximo sprint a executar:** Sprint 2 — Pipeline.
**Bloqueados à espera do cliente (§19):** Sprint 6 (Conciliações), Sprint 7 (Contratos).

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
| Excel / Gráficos / Backup | — | — | ⏳ sprints seguintes (§5) |

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

## 6. PONTOS EM ABERTO (PROMPT §19 — NÃO AVANÇAR SEM RESPOSTA)

- [ ] Lista completa e ordem das fases do pipeline
- [ ] Se "Aprovado/Não Aprovado" são fases ou estados finais
- [ ] Se uma oportunidade pode voltar atrás de fase
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
php artisan test                                               # 28 testes (Sprint 1.1)
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

## HISTÓRICO DESTE DOCUMENTO

| Data | Alteração |
|---|---|
| 2026-10-03 | **Reescrito do zero** — histórico git apagado, `crm-pessoal/` eliminado, Sprint 0 cumprido em `adg/` conforme PROMPT_MASTER |
| 2026-10-03 | **Sprint 1 concluído** (`0aa7f8d`) — enums, auth por username, layouts do protótipo, extração CSS (§4.6), menu agrupado, header simplificado; 26 testes a passar |
| 2026-10-03 | **Sprint 1.1 concluído** (`9066d59`) — 10 componentes Blade extraídos do protótipo + galeria `/ui` + `UiComponentsTest`; 28 testes |
