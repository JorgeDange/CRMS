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
| Sprint 0 — Preparação | ✅ Concluído | `3f93c5e` | `adg/` criado, stack conforme §5 |
| Sprint 1 — Fundação | ⛔ Não iniciado | — | Próximo |
| Sprint 1.1 — UI/UX Base | ⛔ Não iniciado | — | |
| Sprint 2 — Pipeline | ⛔ Não iniciado | — | |
| Sprint 3 — Nova Oportunidade + Detalhe | ⛔ Não iniciado | — | |
| Sprint 4 — Importação Excel | ⛔ Não iniciado | — | |
| Sprint 5 — Agenda Comercial | ⛔ Não iniciado | — | |
| Sprint 6 — Conciliações | ⛔ Bloqueado | — | Aguarda definição do cliente (§19) |
| Sprint 7 — Contratos | ⛔ Bloqueado | — | Aguarda definição do cliente (§19) |
| Sprint 8 — Produtos | ⛔ Não iniciado | — | |
| Sprint 9 — Alertas | ⛔ Não iniciado | — | |
| Sprint 10 — Dashboard + Painel Executivo | ⛔ Não iniciado | — | |
| Sprint 11 — Testes e Lançamento | ⛔ Não iniciado | — | |

**Próximo sprint a executar:** Sprint 1 — Fundação.
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

## 4. CONFIGURAÇÃO DO FRONTEND (§4 — estratégia CSS)

`adg/resources/css/app.css`:
1. `@import url(...Inter...)` — Google Fonts (igual ao `theme.min.css` do protótipo)
2. `@import 'bootstrap/dist/css/bootstrap.min.css'` — **framework principal (§4.2)**
3. `@import 'tailwindcss'` + `@plugin '@tailwindcss/forms'` + `@source ...` (Tailwind 4)
4. Bloco `@theme` com as **cores exactas do protótipo (§3)**:
   `--color-primary #3454d1`, `#2b46b5`, `#eaeefb`, sidebar `#283c50`/`#1b2436`/`#91a1b6`,
   superfície `#eff0f6`, `#17c666`, `#ea4d4d`, `#ffa21d`, `#3dc7be`
5. `body` → fonte Inter + `bg #eff0f6`

`adg/resources/js/app.js`: `bootstrap` (JS bundle c/ Popper) + Alpine.
`vite.config.js`: plugins `laravel` + `tailwindcss`.
**Removidos:** `postcss.config.js`, `tailwind.config.js`, `autoprefixer`, `postcss` (Tailwind 4 não os usa).

**Decisão §4.6 (theme.min.css):** o ficheiro **não tem variáveis CSS próprias**
(apenas 7 overrides `--bs-*` minúsculos) — não há nada para extrair; tokens recriados no `@theme`.
`theme.min.css` **não** é importado (conforme §4.6). As 7 sobrescritas `--bs-*` ficam
por investigar quando se migrarem as views (Sprint 1.1).

---

## 5. O QUE FALTA DO SPRINT 0 / PENDENTE

- [ ] **Commit inicial** deste estado (feito no final do Sprint 0)
- [ ] MySQL como serviço persistente (pedir admin para `sc config MySQL80 start= demand`)
- [ ] Opcional: confirmar com utilizador que a BD `adg` substitui a antiga `crm_pessoal`
- [ ] `.env.example` ajustado a MySQL (vem com sqlite por omissão)

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
php artisan test                                               # 26 testes (Sprint 0)
php vendor/bin/pint                                            # formatação
php vendor/bin/pint --test                                     # verificar sem alterar
php artisan config:clear

# MySQL (serviço DISABLED — arrancar directamente)
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqld.exe" --defaults-file="C:\ProgramData\MySQL\MySQL Server 8.0\my.ini"
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -e "..."
```

---

## HISTÓRICO DESTE DOCUMENTO

| Data | Alteração |
|---|---|
| 2026-10-03 | **Reescrito do zero** — histórico git apagado, `crm-pessoal/` eliminado, Sprint 0 cumprido em `adg/` conforme PROMPT_MASTER |
