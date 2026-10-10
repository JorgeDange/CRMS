# REALIZADO — Projecto ADG (CRM protótipo)

**Actualizado:** 2026-10-09
**Estado:** funcional em desenvolvimento — 140 testes / 729 assertions ✅ · pint ✅ · 44 commits

---

## 1. Visão geral

CRM construído em **Laravel 12 + Livewire 3 + Bootstrap 5.3 + Tailwind 4 + MySQL**, com
**Regra Zero de visual**: tudo copiado do protótipo estático (`crm-prototipo/*.html`) — nada
inventado. Utilizador dev: `adailson` / `password`.

## 2. Sprints concluídos

| Sprint | Entrega | Estado |
|---|---|---|
| 0 | Fundação Laravel 12 + Breeze + Livewire + Bootstrap/Tailwind + MySQL + protótipo | ✅ |
| 1 | Enums, auth por username, seeders, layouts do protótipo, cores/ícones extraídos | ✅ |
| 1.1 | Componentes blade extraídos (button, badge, forms, modal, stat-card, page-header, data-table, alert-badge) | ✅ |
| 2 | Pipeline kanban (9 fases) + novos campos `Opportunity` | ✅ |
| 3 | Nova Oportunidade + detalhe (tabs, visit_reports, rotas) | ✅ |
| 4 | Importação de oportunidades via Excel (template + pré-validação) | ✅ |
| 5 | Agenda Comercial + command `agenda:reminder` (20h) | ✅ |
| 6 | Conciliações | ✅ |
| 7 | Contratos (+ `contracts:expire` 05h30) | ✅ |
| 8 | Produtos | ✅ |
| 9 | Comerciais | ✅ |
| 10 | Alertas (+ notificações/sino) | ✅ |
| 11 | Dashboard + Painel Executivo (gráficos ligados) | ✅ |
| 12 | Testes e Lançamento (suíte base) | ✅ |
| 13 | **Tabela de Clientes** (`seguir.md`) — 6 campos novos, enums, componente `ClientTable`, filtros, sync §14.1 | ✅ |

## 3. Correcções e melhorias recentes (2026-10-09)

| Commit | Descrição |
|---|---|
| `ea3b8f7` | Modal de detalhe do alerta (sino + página) com acções lido/resolvido/eliminar |
| `b75668d` | **Sprint 13 — Tabela de Clientes** (migration 6 colunas, seeder CLI001–CLI010, sync em `ConciliactionService`/`ContractService`) |
| `2c253ab` | Valores do pipeline em formato compacto (`Kz 339,7M`) — stats, totais e cartões sem overflow |
| `665e238` | Kanban com setas de navegação + auto-scroll durante o drag |
| `577d452` | Fix `/agenda` 500 — activities órfãs (seeder demo, `whereHas('opportunity')`, guards `?->?? '—'` em 6 views) |
| `f6b2a98` | Fix `drop is not defined` no kanban — método `drop()` reposto no `x-data` do wrapper |

Histórico completo: `git log --oneline` (44 commits).

## 4. Qualidade

- **Testes:** 140 testes / 729 assertions — todos verdes (`php artisan test`).
- **Código:** `vendor/bin/pint --test` sem observações.
- **E2E (browser, Chrome headless):** clientes, pipeline (valores + navegação + drop real),
  apagar em 4 listas, alertas, notificações, gráficos, listas sem sidebar — **0 erros de consola**.
- **Build:** `app-CDSVpqV-.css` + `app-DZulwGZq.js` (vite build).

## 5. Funcionalidades por módulo

- **Dashboard** — 4 KPIs, gráficos reais (mensual, pipeline, segmento, estado), donuts.
- **Oportunidades** — pipeline kanban 9 fases (drag&drop, setas, auto-scroll), nova oportunidade,
  detalhe com tabs (dados / relatório de visita / histórico de fases), importação Excel, estatísticas.
- **Agenda** — actividades agrupadas por data, filtros, calendário 6 semanas com modal de evento,
  comando de lembrete 20h.
- **Conciliações** — lista sem sidebar, filtros classificação/estado, CRUD.
- **Contratos** — lista, detalhe (resumo/histórico/documentos), edição com prefill,
  expiração automática 05h30.
- **Produtos / Comerciais** — listas com filtros + CRUD.
- **Alertas** — filtros tipo/estado, sino com contador, modal de detalhe, "marcar todas".
- **Clientes (Sprint 13)** — tabela 10 colunas (Nº, código, cliente, KAM, compras, assinatura,
  enviado/entregue, conciliação, contratos, acções), pesquisa + 4 filtros, paginação, 20/pág.

## 6. Infraestrutura

- Scheduler: 6 entradas (agenda 20h, contratos 05h30, alertas 06h, backups 02h/03h/04h).
- Backup diário (mysqldump) com retenção 7 dias.
- `MAIL_MAILER=log` em dev; docs de deploy em `docs/`.

## 7. Pendências (fora do código)

1. Teste manual no browser — percurso em `teste-manual.md` (~40 itens por validar).
2. Respostas PROMPT §19 em aberto (`memoria.md` §6): voltar atrás de fase, relação
   Produtos↔oportunidades/contratos, dias de lead inativo, alertas de contratos, automação.
3. Deploy produção — checklist em `plano.md` (SSL, cron do scheduler, SMTP real, MySQL persistente).
4. `seguir.md` (spec Sprint 13) por commitar.

---

**Documentos de acompanhamento:** `plano.md` (plano/checklists) · `memoria.md` (decisões e
histórico detalhado) · `teste-manual.md` (percurso de QA manual).
