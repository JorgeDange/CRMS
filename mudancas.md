```markdown
# SPRINT DE AJUSTES — PÓS-REUNIÃO COM CLIENTE

**Versão:** 1.0  
**Data:** Outubro de 2026  
**Projecto:** Lead Inteligente — CS & AN  
**Cliente:** Adailson Nogueira  
**Base:** Reunião de validação com o cliente

---

## 1. CONTEXTO

Após a reunião com o Sr. Adailson, foram apresentadas várias sugestões de ajuste ao sistema. Este documento organiza todas as alterações por blocos, define a ordem de implementação e serve de guia para a IA.

**Filosofia:** Implementar bloco a bloco, com validação após cada um. Não avançar sem confirmar que o anterior está correcto.

---

## 2. DECISÕES TOMADAS

| # | Decisão | Resposta |
|---|---|---|
| 1 | Proposta — uma por oportunidade | Campos directos em `opportunities` |
| 2 | Nota — anexos | Sim, privado (`storage/app/private/notes/`) |
| 3 | Ordem | Bloco a bloco, com validação |

---

## 3. BLOCOS DE IMPLEMENTAÇÃO (POR ORDEM)

### BLOCO 1 — ÍCONES ALINHADOS (Conciliações, Contratos, Produtos)

**Objectivo:** Remover o dropdown de 3 pontos e colocar ícones directos na coluna Acções, alinhados horizontalmente.

**Módulos afectados:**
- Conciliações
- Contratos
- Produtos

**O que muda:**
- **Antes:** coluna Acções com dropdown de 3 pontos + subopções dentro
- **Agora:** ícones directos na linha, alinhados
- Os ícones **já existem** — apenas reorganizar

**Layout por módulo:**

**Conciliações:**
```
[✓ Concordante] [✗ Discordante] [⏸ Pendente] [🗑 Eliminar]
```

**Contratos:**
```
[✓ Activar] [✗ Cancelar] [🔄 Renovar] [🗑 Eliminar]
```

**Produtos:**
```
[✓ Activar] [✗ Desactivar] [🗑 Eliminar]
```

**Ficheiros a alterar:**
- `resources/views/livewire/conciliactions/conciliaction-list.blade.php`
- `resources/views/livewire/contracts/contract-list.blade.php`
- `resources/views/livewire/products/product-list.blade.php`
- `resources/css/app.css` (alinhamento dos ícones)

**CSS sugerido:**
```css
.action-icons {
    display: flex;
    gap: 4px;
    align-items: center;
    justify-content: flex-end;
}
.action-icons .btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
```

**Testes:**
- `ConciliactionsTest.php` — verificar que ícones aparecem, sem dropdown
- `ContractsTest.php` — idem
- `ProductsTest.php` — idem

**Validação:** abrir cada lista no browser e verificar alinhamento + funcionalidade dos ícones.

---

### BLOCO 2 — DASHBOARD

**Objectivo:** Reorganizar o dashboard conforme pedido do cliente.

**Alterações:**

**2.1 — Gráfico principal: mudar dados de facturação para pipeline**

- **Antes:** "Facturação Mensal" — soma das oportunidades aprovadas, mês a mês
- **Agora:** "Pipeline por Mês" — soma das oportunidades abertas, mês a mês
- **Formato:** mantém gráfico de área
- **Título:** "Pipeline por Mês"
- **Definição de "abertas":** todas excepto `cliente_aprovado` e `cliente_nao_aprovado`

**2.2 — Mini-cards de baixo: trocar 3 por 4 do pipeline**

- **Antes:** Actividades Concluídas, Novas Actividades, Encomendas Entregues
- **Agora:** Pipeline Aberto, Aprovado (mês), Ponderado, Taxa Conversão
- Reutilizar os valores do `Opportunities\Stats` (já existentes)

**2.3 — KPI "Encomendas em Curso" → "Produtos Activos"**

- **Antes:** contagem de encomendas em curso
- **Agora:** contagem de produtos activos (`products.activo = true`)

**Ficheiros a alterar:**
- `app/Services/DashboardService.php` — `faturacaoMensalPayload()` → `pipelineMensalPayload()`, `kpis()` actualizado
- `resources/views/dashboard.blade.php` — título do gráfico, mini-cards, KPIs
- `app/Livewire/Dashboard/Index.php` (se necessário)

**Testes:**
- `DashboardTest.php` — verificar novos valores, gráfico, títulos

**Validação:** abrir `/dashboard`, verificar gráfico + 4 mini-cards + KPIs.

---

### BLOCO 3 — AGENDA — TIPO "OUTROS"

**Objectivo:** Permitir ao utilizador inserir um tipo personalizado quando seleciona "Outros".

**Alterações:**

**3.1 — Enum `ActivityType`**
- Adicionar `OUTROS = 'outros'`

**3.2 — Migration**
- Adicionar campo `tipo_outros` (varchar 100, nullable) na tabela `activities`

**3.3 — Formulário**
- Quando "Outros" é selecionado no campo Tipo, aparece um campo de texto "Especifique"
- O texto é guardado em `tipo_outros`

**3.4 — Lista e Calendário**
- Quando o tipo é "Outros", mostrar o valor de `tipo_outros` em vez de "Outros"

**Ficheiros a alterar:**
- `app/Enums/ActivityType.php`
- Nova migration `add_tipo_outros_to_activities_table`
- `app/Models/Activity.php` — fillable + casts
- `app/Livewire/Activities/ActivityForm.php` — campo condicional
- `resources/views/livewire/activities/activity-form.blade.php`
- `resources/views/livewire/activities/activity-list.blade.php`
- `resources/views/livewire/activities/activity-calendar.blade.php`

**Testes:**
- `AgendaTest.php` — criar actividade com "Outros" + texto personalizado

**Validação:** criar actividade com tipo "Outros", verificar que aparece o texto na lista/calendário.

---

### BLOCO 4 — OPORTUNIDADE — ABA ACTIVIDADE

**Objectivo:** A aba "Actividades" do detalhe da oportunidade passa a usar a lógica da Agenda (modo cópia).

**Alterações:**

**4.1 — Copiar o `ActivityList` para dentro da aba**
- Reutilizar a view `activity-list.blade.php` mas filtrada por oportunidade
- Ou criar uma versão compacta dentro da aba

**4.2 — Filtrar por oportunidade**
- O componente filtra `where('opportunity_id', $oportunidade->id)`
- Sem filtros globais (só as actividades daquela oportunidade)

**4.3 — Botão "Nova Actividade"**
- Abre o form com cliente pré-selecionado
- Redireciona para `/agenda/nova?oportunidade={id}`

**Ficheiros a alterar:**
- `app/Livewire/Opportunities/OpportunityView.php` — carregar actividades
- `resources/views/livewire/opportunities/opportunity-view.blade.php` — substituir conteúdo da aba Actividades
- Possivelmente criar `app/Livewire/Opportunities/OpportunityActivities.php`

**Testes:**
- `OpportunityViewTest.php` — verificar que actividades da oportunidade aparecem

**Validação:** abrir uma oportunidade, ver a aba Actividades com histórico + botão para criar nova.

---

### BLOCO 5 — OPORTUNIDADE — ABA NOTA

**Objectivo:** Implementar a aba "Nota" com estrutura cronológica, tipos e anexos.

**Estrutura da Nota:**
- **Título** (opcional)
- **Conteúdo** (obrigatório, texto longo)
- **Tipo:** Informação / Decisão / Lembrete / Reunião
- **Data:** automática
- **Fixar no topo:** sim/não
- **Anexos:** sim (guardados em `storage/app/private/notes/{id}/`)

**Funcionalidades:**
- Adicionar nota
- Editar nota
- Eliminar nota
- Fixar/desafixar nota
- Pesquisar em notas
- Filtrar por tipo
- Anexar ficheiros

**Ficheiros a criar:**
- Nova migration `create_notes_table`
- Model `Note` + relação com `Opportunity` e `User`
- Model `NoteAttachment` (ou usar relação polimórfica)
- `app/Livewire/Notes/NoteList.php`
- `app/Livewire/Notes/NoteForm.php`
- `resources/views/livewire/notes/note-list.blade.php`
- `resources/views/livewire/notes/note-form.blade.php`
- Actualizar `opportunity-view.blade.php` para incluir a aba
- Actualizar `OpportunityView.php`

**Estrutura da tabela `notes`:**
| Campo | Tipo | Nullable |
|---|---|---|
| id | bigint unsigned | Não |
| uuid | char(36) | Não |
| opportunity_id | bigint unsigned | Não |
| user_id | bigint unsigned | Não |
| titulo | varchar(255) | Sim |
| conteudo | text | Não |
| tipo | varchar(50) | Não |
| fixada | boolean | Não |
| timestamps | | |

**Estrutura da tabela `note_attachments`:**
| Campo | Tipo | Nullable |
|---|---|---|
| id | bigint unsigned | Não |
| note_id | bigint unsigned | Não |
| nome_original | varchar(255) | Não |
| caminho | varchar(255) | Não |
| tamanho | int | Não |
| mime_type | varchar(100) | Não |
| timestamps | | |

**Enum `NoteType`:**
- `INFORMACAO = 'informacao'`
- `DECISAO = 'decisao'`
- `LEMBRETE = 'lembrete'`
- `REUNIAO = 'reuniao'`

**Testes:**
- `NoteTest.php` — CRUD, fixar, filtrar, pesquisar, anexos

**Validação:** abrir oportunidade, criar nota, anexar ficheiro, fixar, verificar persistência.

---

### BLOCO 6 — OPORTUNIDADE — ABA PROPOSTA

**Objectivo:** Adicionar campos de proposta à oportunidade.

**Decisão:** uma proposta por oportunidade → campos directos na tabela `opportunities`.

**Campos a adicionar:**

| Campo | Tipo | Nullable |
|---|---|---|
| `proposta_produto_id` | bigint unsigned | Sim |
| `proposta_preco` | decimal(15,2) | Sim |
| `proposta_volume` | decimal(15,2) | Sim |
| `proposta_volume_unidade` | varchar(10) | Sim (L ou m³) |
| `proposta_periodo_inicio` | date | Sim |
| `proposta_periodo_fim` | date | Sim |
| `proposta_credito` | decimal(15,2) | Sim |
| `proposta_facturacao` | decimal(15,2) | Sim |
| `proposta_non_oil` | decimal(15,2) | Sim |

**Contexto:** o Adailson trabalha com combustíveis (oil & gas). Explicação dos campos:
- **Volume:** litros ou m³ de combustível
- **Non-oil:** loja de conveniência (produtos não-combustível)
- **Crédito:** vendas a prazo
- **Facturação:** valor bruto/líquido das vendas

**Ficheiros a alterar:**
- Nova migration `add_proposta_fields_to_opportunities_table`
- `app/Models/Opportunity.php` — fillable + casts + relação `propostaProduto()`
- `app/Livewire/Opportunities/OpportunityView.php` — guardar proposta
- `resources/views/livewire/opportunities/opportunity-view.blade.php` — aba Proposta com formulário

**Formulário da proposta:**
- Produto (select com produtos activos)
- Preço (input numérico Kz)
- Volume (input numérico + unidade L/m³)
- Período (date range: início até fim)
- Crédito (input numérico Kz)
- Facturação (input numérico Kz)
- Non-oil (input numérico Kz)

**Testes:**
- `OpportunityViewTest.php` — guardar proposta, verificar persistência

**Validação:** abrir oportunidade, preencher proposta, guardar, verificar valores guardados.

---

## 4. ORDEM DE IMPLEMENTAÇÃO

| Ordem | Bloco | Esforço | Prioridade |
|---|---|---|---|
| 1 | BLOCO 1 — Ícones alinhados | Baixo | Alta |
| 2 | BLOCO 2 — Dashboard | Baixo | Alta |
| 3 | BLOCO 3 — Agenda Tipo Outros | Baixo | Média |
| 4 | BLOCO 4 — Aba Actividade | Baixo | Alta |
| 5 | BLOCO 5 — Aba Nota | Médio | Média |
| 6 | BLOCO 6 — Aba Proposta | Médio | Alta |

**Regra:** validar após cada bloco antes de avançar.

---

## 5. CHECKLIST POR BLOCO

### BLOCO 1 — Ícones alinhados

- [x] Conciliações: ícones directos na linha
- [x] Contratos: ícones directos na linha (+ `remove()` no componente para o novo Eliminar)
- [x] Produtos: ícones directos na linha
- [x] CSS de alinhamento
- [x] Testes actualizados
- [x] Validação no browser
- [x] Commit

### BLOCO 2 — Dashboard

- [x] `DashboardService::pipelineMensalPayload()` (antes `faturacaoMensal`)
- [x] `DashboardService::kpis()` actualizado (`produtosActivos` no lugar de `encomendasCurso`)
- [x] Mini-cards: 4 do pipeline (Pipeline Aberto, Aprovado (mês), Ponderado, Taxa Conversão)
- [x] KPI Produtos Activos
- [x] View actualizada
- [x] Testes actualizados (+2 novos: soma do pipeline aberto, contagem de produtos)
- [x] Validação no browser
- [x] Commit

### BLOCO 3 — Agenda Tipo Outros

- [x] Enum `ActivityType::OUTROS`
- [x] Migration `tipo_outros`
- [x] Form com campo condicional
- [x] Lista/calendário com texto personalizado
- [x] Testes (+5 novos)
- [x] Validação no browser
- [x] Commit

### BLOCO 4 — Aba Actividade

- [ ] Componente/lista de actividades da oportunidade
- [ ] View da aba actualizada
- [ ] Botão "Nova Actividade" com cliente pré-selecionado
- [ ] Testes
- [ ] Validação no browser
- [ ] Commit

### BLOCO 5 — Aba Nota

- [ ] Migration `notes` + `note_attachments`
- [ ] Enum `NoteType`
- [ ] Models `Note` e `NoteAttachment`
- [ ] Componentes `NoteList` e `NoteForm`
- [ ] Views
- [ ] Upload de anexos (privado)
- [ ] Fixar/desafixar
- [ ] Pesquisa e filtro
- [ ] Testes
- [ ] Validação no browser
- [ ] Commit

### BLOCO 6 — Aba Proposta

- [ ] Migration com 9 campos
- [ ] Model `Opportunity` actualizado
- [ ] Formulário da proposta
- [ ] Lógica de guardar
- [ ] Testes
- [ ] Validação no browser
- [ ] Commit

---

## 6. PROMPT PARA A IA

```
CONTEXTO

Estamos a implementar o Sprint de Ajustes do sistema Lead Inteligente (CS & AN). 
O cliente (Adailson Nogueira) fez várias sugestões após a reunião de validação.

O sistema está em Laravel 12 + Livewire 3 + Bootstrap 5.3 + Tailwind 4 + MySQL 8.
Já tem 140 testes a passar. Deves manter todos os testes a passar após cada bloco.

O projecto segue regras rigorosas:
- Views copiadas do protótipo (não inventar)
- Bootstrap mantido (não converter para Tailwind só por converter)
- Livewire 3 (nunca Livewire 2)
- Sem jQuery
- Sem Vue/React

---

TAREFA

Implementar os 6 blocos de ajustes, um de cada vez, na ordem definida:

BLOCO 1 — Ícones alinhados em Conciliações, Contratos e Produtos
BLOCO 2 — Dashboard (gráfico + mini-cards + KPI Produtos)
BLOCO 3 — Agenda Tipo "Outros"
BLOCO 4 — Aba Actividade (modo cópia da Agenda)
BLOCO 5 — Aba Nota (nova estrutura)
BLOCO 6 — Aba Proposta (9 campos novos)

---

REGRAS

1. Um bloco de cada vez — só avançar após validação
2. Testes após cada bloco (php artisan test)
3. Pint após cada bloco (./vendor/bin/pint)
4. Build após mudanças de frontend (npm run build)
5. Não inventar design — copiar do protótipo quando aplicável
6. Manter Bootstrap
7. Manter todos os 140 testes a passar (ou mais)
8. Marcar suposições com // SUPOSIÇÃO: quando não houver definição

---

DETALHES POR BLOCO

BLOCO 1 — ÍCONES ALINHADOS
- Conciliações: Concordante, Discordante, Pendente, Eliminar
- Contratos: Activar, Cancelar, Renovar, Eliminar
- Produtos: Activar, Desactivar, Eliminar
- Remover dropdown de 3 pontos
- Ícones directos na linha, alinhados horizontalmente
- CSS: .action-icons com flex, gap, botões quadrados de 32x32

BLOCO 2 — DASHBOARD
- Gráfico principal: dados de pipeline por mês (não facturação)
- Pipeline = oportunidades excepto cliente_aprovado e cliente_nao_aprovado
- Mini-cards: Pipeline Aberto, Aprovado (mês), Ponderado, Taxa Conversão
- KPI "Encomendas em Curso" → "Produtos Activos" (count de products.activo = true)

BLOCO 3 — AGENDA TIPO OUTROS
- Enum ActivityType: adicionar OUTROS
- Migration: campo tipo_outros (varchar 100, nullable)
- Form: campo de texto aparece quando "Outros" selecionado
- Lista/calendário: mostrar tipo_outros quando tipo = OUTROS

BLOCO 4 — ABA ACTIVIDADE
- Reutilizar lógica da Agenda
- Filtrar actividades por opportunity_id
- Botão "Nova Actividade" que abre form com cliente pré-selecionado
- Redirect para /agenda/nova?oportunidade={id}

BLOCO 5 — ABA NOTA
- Nova tabela notes (id, uuid, opportunity_id, user_id, titulo, conteudo, tipo, fixada, timestamps)
- Nova tabela note_attachments (id, note_id, nome_original, caminho, tamanho, mime_type, timestamps)
- Enum NoteType: INFORMACAO, DECISAO, LEMBRETE, REUNIAO
- Anexos guardados em storage/app/private/notes/{note_id}/
- Componentes Livewire: NoteList + NoteForm
- Funcionalidades: CRUD, fixar, pesquisar, filtrar por tipo, anexos

BLOCO 6 — ABA PROPOSTA
- Migration: adicionar 9 campos à tabela opportunities:
  - proposta_produto_id (bigint, nullable, FK products)
  - proposta_preco (decimal 15,2)
  - proposta_volume (decimal 15,2)
  - proposta_volume_unidade (varchar 10: L ou m³)
  - proposta_periodo_inicio (date)
  - proposta_periodo_fim (date)
  - proposta_credito (decimal 15,2)
  - proposta_facturacao (decimal 15,2)
  - proposta_non_oil (decimal 15,2)
- Model Opportunity: fillable + casts + relação propostaProduto()
- Formulário na aba Proposta com todos os campos
- Lógica de guardar no OpportunityView

---

VALIDAÇÃO POR BLOCO

Após cada bloco:
1. php artisan test — todos os testes a passar
2. ./vendor/bin/pint — sem erros
3. npm run build — se houver mudanças de frontend
4. Verificação manual no browser
5. Commit com mensagem descritiva

---

COMUNICAÇÃO

Após cada bloco, resumir:

## Bloco X — [Nome] concluído

### Ficheiros criados/modificados
- ...

### Testes
- X testes, Y asserções, todos a passar

### Como testar
1. ...
2. ...

### Próximo bloco
Bloco X+1 — [Nome]

Posso avançar?

---

COMEÇA POR

BLOCO 1 — Ícones alinhados em Conciliações, Contratos e Produtos.

Aguarda confirmação do utilizador antes de avançar para o Bloco 2.
```

---

## 7. VALIDAÇÃO FINAL

Após todos os 6 blocos:

- [ ] Todos os testes a passar
- [ ] Pint sem erros
- [ ] Build OK
- [ ] Verificação manual de cada bloco no browser
- [ ] Documentar alterações no `MEMORIA_DO_PROJECTO.md`
- [ ] Apresentar ao cliente para validação final
- [ ] Recolher feedback adicional

---

## 8. NOTAS FINAIS

### O que mudou do plano original

- O gráfico não é substituído — apenas muda a fonte de dados
- Os ícones já existem — apenas reorganizar
- A aba Proposta usa campos directos (não tabela separada)
- A aba Nota é nova (não existia)

### Suposições registadas

- "Non-oil" = loja de conveniência (contexto oil & gas)
- Volume em litros ou m³
- Crédito, Facturação e Non-oil em Kz
- Uma proposta por oportunidade
- Anexos de notas em storage privado

### Próximo passo

1. Implementar BLOCO 1
2. Validar com o utilizador (tu)
3. Avançar para BLOCO 2
4. Repetir até ao BLOCO 6
5. Apresentar ao cliente

---

**Documento:** SPRINT_AJUSTES.md  
**Versão:** 1.0  
**Data:** Outubro de 2026  
**Próxima revisão:** Após BLOCO 1
```