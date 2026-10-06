# PLANO DE EXECUÇÃO — Sistema ADG

**Base:** `resposta.md` v4.0 (Outubro 2026)  
**Estado actual:** Sprint 5 concluído (commit `49c2225` + `2b342a4`)  
**Próximo:** Ajustes Sprint 2/3 → Sprint 6 (Conciliações)

---

## FASES / SPRINTS

| Fase | Sprint | Módulo | Estado | Prioridade | Observações |
|------|--------|--------|--------|------------|-------------|
| 0 | Sprint 0 | Preparação | ✅ Concluído | — | Laravel 12 + MySQL 8 + Breeze Livewire + CSS extraído |
| 1 | Sprint 1 | Fundação | ✅ Concluído | — | Enums, auth username, layouts, extração CSS, menu agrupado |
| 1.1 | Sprint 1.1 | UI/UX Base | ✅ Concluído | — | 10 componentes Blade + galeria `/ui` |
| 2 | Sprint 2 | Pipeline | ✅ Concluído* | **ALTA** | *Ajustes: 9 fases, novos campos, commercials, pivot |
| 3 | Sprint 3 | Nova Oportunidade + Detalhe | ✅ Concluído* | **ALTA** | *Ajustes: novos campos, comercial, produto |
| 4 | Sprint 4 | Importação Excel | ✅ Concluído* | **MÉDIA** | *Ajustes: template com novas colunas |
| 5 | Sprint 5 | Agenda Comercial | ✅ Concluído* | **MÉDIA** | *Ajustes: command `agenda:reminder` 20h |
| 6 | Sprint 6 | Conciliações | ✅ Concluído | `cbdde3d` | **ALTA** | Enum ConciliactionClassificacao, migration, Model/Factory/Seeder, Service, Livewire List/Form, views payment.html, rotas, sidebar acordeão |
| 7 | Sprint 7 | Contratos | ✅ Concluído | `abda4e2` | **ALTA** | Enum ContractStatus, migration contracts (FK opportunity/commercial/product), Model/Factory/Seeder, Service, Livewire List/Form/View (overview/history/documents), views projects*.html, rotas, sidebar acordeão |
| 8 | Sprint 8 | Produtos | ✅ Concluído | `fc419f8` | **ALTA** | Livewire ProductList/ProductForm, views baseadas em customers.html, rotas produtos/produtos.nova/produtos.{produto}, sidebar acordeão; Model/Factory/Seeder já existiam |
| 9 | Sprint 9 | Comerciais | ✅ Concluído | `75f06b2` | **MÉDIA** | Livewire CommercialList/CommercialForm, views baseadas no padrão, rotas comerciais/comerciais.nova/comerciais.{commercial}, sidebar accordion; Model/Factory/Seeder já existiam |
| 10 | Sprint 10 | Alertas | ✅ Concluído | `5ea436a` | **ALTA** | Enum AlertType, migration alerts, Model/Factory/Seeder, AlertService (5 tipos, não move leads), command alerts:generate (06:00), NewAlert notification (email+database), Livewire AlertList, header badge+dropdown, sidebar accordion |
| 11 | Sprint 11 | Dashboard + Painel Executivo | ✅ Concluído | `9531418` | **ALTA** | Dashboard (4 KPIs + Chart.js), Analytics (Painel Executivo), Chart.js, migration user_id, rotas dashboard/analytics, sidebar accordion; 83 testes, pint, build OK |
| 12 | Sprint 12 | Testes e Lançamento | ⛔ A implementar | — | Testes com Adailson, feedback, ajustes, deploy, SSL, backup |

---

## ORDEM DE EXECUÇÃO IMEDIATA

### 1. SPRINT 12 — Testes e Lançamento
- [ ] Testes manuais com Adailson
- [ ] Feedback → ajustes
- [ ] Deploy produção
- [ ] SSL
- [ ] Configurar backups (`spatie/laravel-backup`)
- [x] Enum `OpportunityStage` → 9 valores (lead → cliente_nao_aprovado)
- [x] Migration `opportunities` → adicionar colunas: `pessoa_contacto`, `tipo_relacao`, `comercial_id`, `produto_id`, `sector` (ex-segmento), `origem`
- [x] Migration `commercials` (nova tabela)
- [x] Migration `opportunity_product` (pivot nova)
- [x] Models `Opportunity`, `Commercial`, relações + pivot
- [x] Service `OpportunityService::changeStage` → actualizar para 9 fases
- [x] Livewire `Pipeline` → 9 colunas, classes CSS por fase
- [x] Views → Kanban 9 colunas (copiar de `leads.html`)
- [x] Seeders → `CommercialSeeder` (3 registos)
- [x] Testes → PipelineTest actualizado

### 2. AJUSTES SPRINT 3 — Nova Oportunidade + Detalhe
- [x] Formulário → adicionar campos: `pessoa_contacto`, `tipo_relacao`, `comercial_id`, `produto_id`, `sector`, `origem`, `codigo_sap`, `nif`
- [x] View create → copiar de `leads-create.html` com novos campos
- [x] View show → copiar de `leads-view.html`, tabs actualizadas
- [x] Relação `opportunity_product` no create/view
- [x] Testes → OpportunityCreateTest + OpportunityViewTest actualizados

### 3. AJUSTES SPRINT 4 — Importação Excel
- [x] Template → novas colunas no `OpportunityTemplateExport`
- [x] `ExcelImportService` → mapear novas colunas (aliases)
- [x] Testes → OpportunityImportTest actualizado

### 4. AJUSTES SPRINT 5 — Agenda Comercial
- [x] Command `agenda:reminder` → envia agenda do dia seguinte às 20h (email + log)
- [x] Schedule no `routes/console.php` → dailyAt('20:00') timezone Africa/Luanda

### 5. SPRINT 6 — Conciliações
- [x] Enum `ConciliactionClassificacao`
- [x] Migration `conciliactions`
- [x] Model `Conciliaction` + Factory + Seeder
- [x] Service `ConciliactionService`
- [x] Livewire `Conciliactions\ConciliactionList` + `ConciliactionForm`
- [x] Views baseadas em `payment.html`
- [x] Rotas + sidebar
- [x] Testes (suíte existente passa)

### 6. SPRINT 7 — Contratos
- [x] Enum `ContractStatus`
- [x] Migration `contracts` (FK opportunity, commercial, product)
- [x] Model `Contract` + Factory + Seeder
- [x] Service `ContractService`
- [x] Livewire `Contracts\ContractList` + `ContractForm` + `ContractView`
- [x] Views baseadas em `projects.html`, `projects-create.html`, `projects-view.html`
- [x] Rotas + sidebar
- [x] Testes (suíte existente passa)

### 7. SPRINT 8 — Produtos
- [x] Migration `products` (FK commercial) — já existia
- [x] Model `Product` + Factory + Seeder — já existiam
- [x] Service `ProductService` — usa OpportunityService::formatKz
- [x] Livewire `Products\ProductList` + `ProductForm`
- [x] Views baseadas em `customers.html`
- [x] Rotas + sidebar
- [x] Testes (suíte existente passa)

### 8. SPRINT 9 — Comerciais
- [x] Migration `commercials` (já criada no Sprint 2)
- [x] Model `Commercial` + Factory + Seeder — já existiam
- [x] Livewire `Commercials\CommercialList` + `CommercialForm`
- [x] Views baseadas no padrão existente
- [x] Rotas + sidebar
- [x] Testes (suíte existente passa)
- [x] Vista de carteira por comercial

### 9. SPRINT 10 — Alertas
- [x] Enum `AlertType`
- [x] Migration `alerts`
- [x] Model `Alert` + Factory + Seeder
- [x] Service `AlertService` (gera alertas, **não move leads**)
- [x] Command `alerts:generate` (agendado)
- [x] Notification email (Laravel Notification)
- [x] Livewire `Alerts\AlertList`
- [x] Badge no header/dashboard
- [x] Testes (suíte existente passa)

### 10. SPRINT 11 — Dashboard + Painel Executivo
- [x] Dashboard → 4 KPI cards + charts (copiar de `index.html`)
- [x] Painel Executivo → gráficos Chart.js (copiar de `analytics.html`)
- [x] Stats cards, pipeline funnel, etc.
- [x] Testes (suíte existente passa)

### 11. SPRINT 12 — Testes e Lançamento
- [ ] Testes manuais com Adailson
- [ ] Feedback → ajustes
- [ ] Deploy produção
- [ ] SSL
- [ ] Configurar backups (`spatie/laravel-backup`)

---

## CHECKLIST POR SPRINT (conforme resposta.md §18)

Para cada sprint:
- [ ] Enums criados
- [ ] Migrations executam sem erro
- [ ] Models com relações correctas
- [ ] Factories funcionam
- [ ] Seeders populam dados
- [ ] Services implementados
- [ ] Componentes Livewire funcionais
- [ ] **Views copiadas do protótipo (não inventadas)**
- [ ] **Bootstrap mantido nas views**
- [ ] **Visual igual ao protótipo (comparado lado a lado)**
- [ ] Rotas configuradas
- [ ] Testes passam
- [ ] Pint sem erros
- [ ] Manual test no browser
- [ ] Commit com mensagem descritiva

---

## SUPOSIÇÕES DOCUMENTADAS (resposta.md §19)

| # | Suposição | Onde | Status |
|---|-----------|------|--------|
| 1 | Pipeline tem 9 fases | `OpportunityStage` | ✅ Sprint 2 |
| 2 | Comerciais são categorias, não pessoas | `commercials` | ⏳ Sprint 2/9 |
| 3 | Tipo de relação = 5 opções | `TipoRelacao` | ⏳ Sprint 2/3 |
| 4 | Produto relacionado = relação com tabela | `opportunities.produto_id` | ⏳ Sprint 2/3 |
| 5 | Nome e pessoa_contacto = campos separados | `opportunities` | ⏳ Sprint 2/3 |
| 6 | Produto ↔ Oportunidades = many-to-many | `opportunity_product` | ⏳ Sprint 2/3 |
| 7 | Produto ↔ Contratos = um por contrato | `contracts.produto_id` | ⏳ Sprint 7 |
| 8 | Alerta cliente = 60 dias | `settings` | ⏳ Sprint 10 |
| 9 | Email de alertas = email do Adailson | `settings` | ⏳ Sprint 10 |
| 10 | Lembrete 20h = email + sistema | Agenda | ⏳ Sprint 5 |
| 11 | Conciliação = Concordante/Discordante/Pendente | `conciliactions` | ⏳ Sprint 6 |
| 12 | Contrato expirado = muda estado | `contracts` | ⏳ Sprint 7 |
| 13 | Ordem Aprovação → Bilhete | Pipeline | ⏳ Sprint 2 |
| 14 | Alertas sem movimentação automática | `AlertService` | ⏳ Sprint 10 |

---

## COMANDOS ÚTEIS

```bash
# PHP do Herd (obrigatório)
export PATH="$HOME/.config/herd/bin:$PATH"

# Desenvolvimento
php artisan serve --no-reload --port=8099
npm run dev

# Base de dados
php artisan migrate
php artisan migrate:fresh --seed

# Testes
php artisan test
php artisan test --filter=NomeTest

# Formatação
./vendor/bin/pint
./vendor/bin/pint --test

# Cache
php artisan optimize:clear

# Comandos específicos (quando criados)
php artisan alerts:generate
php artisan agenda:reminder
```

---

## REGISTO DE PROGRESSO

| Data | Sprint | Acção | Commit |
|------|--------|-------|--------|
| 2026-10-03 | 0 | Preparação Laravel + stack | `3f93c5e` |
| 2026-10-03 | 1 | Fundação + extração CSS | `0aa7f8d` |
| 2026-10-03 | 1.1 | UI/UX Base + 10 componentes | `9066d59` |
| 2026-10-03 | 2 | Pipeline 5 fases (antigo) | `b27ba30` |
| 2026-10-04 | 3 | Nova Oportunidade + Detalhe | `91d3514` |
| 2026-10-04 | 4 | Importação Excel | `6fb0330` |
| 2026-10-05 | 5 | Agenda Comercial | `49c2225` |
| — | — | **PRÓXIMO: Ajustes Sprint 2/3** | — |

---

## NOTAS IMPORTANTES

1. **Regra Zero:** Nunca inventar visual — copiar do protótipo em `crm-prototipo/`
2. **Pipeline antigo:** Tinha 5 fases (Leads → Visitas → Aprovação → Aprovado/Não Aprovado). **Actualizar para 9 fases.**
3. **Oportunidades antigas:** Faltam campos novos (`pessoa_contacto`, `tipo_relacao`, `comercial_id`, `produto_id`, `sector`, etc.)
4. **Menu lateral:** Ordem fixa (resposta.md §7). Sub-opções Oportunidades: 1ª Nova Oportunidade, 2ª Pipeline.
5. **Nomenclatura:** Nunca usar "Ganho", "Perdido", "Crédito", "Encomendas" (resposta.md §6).
6. **Não bloquear:** Seguir suposições documentadas. Cliente valida depois.
7. **Visual:** Sidebar branca 280px, header branco 80px, fundo `#f3f4f6` (protótipo real — não o que diz o MD antigo).