
# resposta.md
## Sistema ADG — Instruções Completas para a IA

**Versão:** 4.0  
**Data:** Outubro de 2026  
**Destinatário:** IA de desenvolvimento  
**Projecto:** ADG — Ferramenta de Gestão Comercial Pessoal  
**Cliente:** Adailson Nogueira  
**Empresa:** Otimize  
**Base:** Especificação Funcional actualizada + suposições analisadas

---

## FILOSOFIA DE TRABALHO

Este projecto tem uma abordagem específica:

> **Construir → Testar → Cliente valida → Ajustar**

O cliente **não gosta de ser questionado constantemente**. A melhor forma de trabalhar é:

1. **Construir** com base na análise e nas suposições documentadas
2. **Testar** internamente
3. **Apresentar ao cliente** o sistema funcional
4. **Cliente testa** e dá sugestões de melhoria
5. **Ajustar** conforme o feedback real

**Não bloquear o desenvolvimento à espera de respostas do cliente.** As suposições estão documentadas e são facilmente alteráveis. Quando o cliente testar, ele dirá o que quer mudar.

---

## REGRA ZERO — LEIA ANTES DE TUDO

**Este projecto NÃO é para criar um sistema do zero.**

Existe um **protótipo HTML/CSS/JS já aprovado pelo cliente** na pasta `crm-prototipo/` que é a **referência visual obrigatória**.

**A tua função é:**

1. **Migrar o HTML do protótipo para Blade** — copiar o markup tal como está
2. **Preservar o visual exacto** — cores, tipografia, espaçamentos, ícones, tudo igual
3. **Adicionar backend Laravel** — models, migrations, services, lógica de negócio
4. **Ligar o front ao back** — substituir dados fictícios por dados reais
5. **Adicionar novas funcionalidades** — só as que não existem no protótipo

**É PROIBIDO:**

- Inventar design novo
- Criar componentes UI que não existam no protótipo
- Mudar cores, tipografia ou layout
- Reconstruir o visual "porque é mais limpo"
- Assumir decisões que não estão documentadas
- Bloquear o desenvolvimento à espera do cliente

---

## 1. CONTEXTO DO PROJECTO

### O que é

Ferramenta web de gestão comercial **pessoal**, utilizada exclusivamente por **um único utilizador: Adailson Nogueira**.

### Empresa que desenvolve

Otimize — Business Intelligence

### Princípio fundamental

> **O sistema faz e o utilizador confirma. Automação acima de tudo. Mas nunca toma decisões comerciais pelo utilizador.**

### Modelo de negócio

```
Nova Oportunidade
    ↓
[Importar Excel] ou [Cadastrar Manual]
    ↓
Pipeline (9 fases)
    ↓
Cliente Aprovado → cliente
OU
Cliente Não Aprovado → perdido
```

**Regras críticas:**
- Não há módulo separado de clientes — o cliente nasce da oportunidade aprovada
- Todo o registo começa em Nova Oportunidade
- A aprovação acontece no pipeline
- O sistema **NÃO** move leads automaticamente — apenas notifica

---

## 2. PROTÓTIPO — A REFERÊNCIA OBRIGATÓRIA

### Localização

```
crm-prototipo/
├── *.html                 → 20 páginas navegáveis
├── assets/
│   ├── css/               → bootstrap.min.css, theme.min.css
│   ├── js/                → ficheiros *-init.min.js
│   ├── vendors/           → libs 3ª parte
│   └── images/            → logos, ícones, avatares
├── logo-d.png             → logotipo do sistema
└── doc.md, plan.md        → documentação do protótipo
```

### Tecnologias do protótipo

| Tecnologia | Uso no protótipo |
|---|---|
| **Bootstrap 5.2** | Framework CSS principal |
| **Tailwind CSS** | Usado pontualmente |
| **jQuery** | Interacções (modais, dropdowns, tabs) |
| **ApexCharts** | Gráficos |
| **DataTables** | Tabelas com pesquisa/paginação |
| **Select2** | Selects avançados |
| **Quill** | Editor rich-text |
| **TUI Calendar** | Calendário |
| **Feather Icons** | Ícones |
| **Font Awesome** | Ícones |
| **Bootstrap Icons** | Ícones |

### Como usar o protótipo

**Antes de criar qualquer view Blade:**

1. Abre o HTML correspondente em `crm-prototipo/`
2. Copia a estrutura HTML
3. Substitui apenas o que for necessário:
   - Dados fictícios → variáveis Blade/Livewire
   - Links estáticos → rotas Laravel (`route('nome')`)
   - Scripts jQuery → substituir por Livewire/Alpine **apenas onde for indispensável**
4. **Mantém as classes Bootstrap** — não convertas para Tailwind só por converter
5. **Mantém os ícones** — Feather, Font Awesome ou Bootstrap Icons

---

## 3. IDENTIDADE VISUAL

### Nome do sistema

**ADG**

### Logotipo

- Ficheiro: `logo-d.png`
- Localização: `crm-prototipo/logo-d.png`
- Copiar para: `adg/public/images/logo-d.png`
- Usar no sidebar (topo) e no login

### Cores (extraídas do protótipo — não alterar)

| Elemento | Valor |
|---|---|
| Primária | `#3454d1` |
| Primária (hover) | `#2b46b5` |
| Primária (suave) | `#eaeefb` |
| Sidebar (fundo) | `#283c50` |
| Sidebar (hover) | `#1b2436` |
| Sidebar (texto) | `#91a1b6` |
| Fundo da app | `#f3f4f6` |
| Sucesso | `#17c666` |
| Perigo | `#ea4d4d` |
| Aviso | `#ffa21d` |
| Info | `#3dc7be` |

### Tipografia

- Fonte: **Inter** (400/500/600/700)
- Ícones: Feather Icons, Font Awesome, Bootstrap Icons

### Layout

- **Sidebar fixa à esquerda** (conforme protótipo real — branca, 280px)
- **Header branco fixo** (80px, sticky)
- **Conteúdo** em cartões brancos sobre fundo `#f3f4f6`

---

## 4. STACK TÉCNICA

| Camada | Tecnologia | Versão |
|---|---|---|
| Backend | Laravel | 12 |
| PHP | PHP | 8.4 (Herd) |
| Base de Dados | MySQL | 8 |
| Base de Dados (testes) | SQLite | `:memory:` |
| Autenticação | Laravel Breeze | Última |
| Sessão | Database | — |
| Frontend | Blade + Livewire | 3 |
| CSS Framework | Bootstrap | 5.3 |
| CSS Utilitário | Tailwind CSS | 4 |
| Interacções | Alpine.js | Incluído |
| Build | Vite | 7 |
| Gráficos | Chart.js | Última |
| Ícones | Feather + Font Awesome | — |
| Testes | PHPUnit | 11.5 |
| Queue | Database | — |
| Excel | maatwebsite/excel | Última |
| Backup | spatie/laravel-backup | Última |

### Regras de stack

- **Nunca** usar Livewire 2 (é Livewire 3)
- **Nunca** usar jQuery no Laravel
- **Nunca** usar Vue ou React
- **Nunca** adicionar pacotes fora desta lista
- **Sempre** manter Bootstrap para o visual
- **Sempre** usar Tailwind para ajustes

---

## 5. PIPELINE — 9 FASES

### Estrutura final

```
1. Lead
2. Qualificação
3. Visita
4. Proposta/Negociação
5. Aprovação
6. Bilhete
7. Resultado
8. Cliente Aprovado
9. Cliente Não Aprovado
```

### Regras do Kanban

- **9 colunas** no Kanban
- Ao mover para **"Cliente Aprovado"**:
  - Grava na base de dados
  - Estado muda para **cliente**
  - O card **permanece** na coluna onde foi movido
- Sempre que se move, o card fica na coluna de destino
- O Kanban é **cumulativo** — vai acumulando histórico visual
- **NÃO há movimentação automática** — só manual

---

## 6. NOMENCLATURA OBRIGATÓRIA

| Termo no sistema | Significado |
|---|---|
| **Lead** | Fase inicial do pipeline |
| **Qualificação** | Fase de qualificação |
| **Visita** | Fase de visita (com relatório) |
| **Proposta/Negociação** | Fase de proposta e negociação |
| **Aprovação** | Fase de aprovação |
| **Bilhete** | Fase de bilhete |
| **Resultado** | Fase de resultado |
| **Cliente Aprovado** | Estado final positivo |
| **Cliente Não Aprovado** | Estado final negativo |
| **Conciliações** | Substitui o antigo "Crédito" |
| **Contratos** | Substitui o antigo "Encomendas" |
| **Produtos** | Catálogo de produtos |
| **Comerciais** | Categorias de organização da carteira |

**Nunca usar:** Ganho, Perdido, Crédito, Encomendas

---

## 7. MENU LATERAL FINAL

```
Dashboard
Oportunidades
  ├── Nova Oportunidade
  └── Pipeline
Agenda Comercial
Conciliações
Contratos
Produtos
Comerciais
Alertas
Painel Executivo
```

### Ordem das sub-opções de Oportunidades

1. **Nova Oportunidade** (primeiro)
2. **Pipeline** (segundo)

---

## 8. MODELO DE DADOS

### 8.1 Tabela `users`

| Coluna | Tipo | Nullable | Notas |
|---|---|---|---|
| id | bigint unsigned | Não | PK |
| name | varchar(255) | Não | "Adailson Nogueira" |
| username | varchar(50) | Não | unique |
| email | varchar(255) | Não | unique |
| password | varchar(255) | Não | hashed |
| email_verified_at | timestamp | Sim | |
| remember_token | varchar(100) | Sim | |
| timestamps | | | |

**Nota:** apenas 1 registo.

### 8.2 Tabela `commercials`

| Coluna | Tipo | Nullable | Default | Notas |
|---|---|---|---|---|
| id | bigint unsigned | Não | auto | PK |
| uuid | char(36) | Não | | unique |
| nome | varchar(100) | Não | | "Comercial 01" |
| descricao | text | Sim | null | |
| activo | boolean | Não | true | |
| timestamps | | | | |

**Seeder inicial:** 3 registos (Comercial 01, 02, 03)

### 8.3 Tabela `opportunities`

| Coluna | Tipo | Nullable | Default | Notas |
|---|---|---|---|---|
| id | bigint unsigned | Não | auto | PK |
| uuid | char(36) | Não | | unique |
| nome | varchar(255) | Não | | Nome da Empresa |
| pessoa_contacto | varchar(255) | Sim | null | Nome da pessoa |
| empresa | varchar(255) | Sim | null | Deprecated |
| telefone | varchar(50) | Sim | null | |
| email | varchar(255) | Sim | null | |
| nif | varchar(50) | Sim | null | |
| codigo_sap | varchar(50) | Sim | null | |
| sector | varchar(100) | Sim | null | Renomeado de segmento |
| localizacao | varchar(255) | Sim | null | |
| origem | varchar(100) | Sim | null | |
| tipo_relacao | varchar(50) | Sim | null | Lista |
| interesse | text | Sim | null | |
| valor_estimado | decimal(15,2) | Sim | null | |
| probabilidade | tinyint | Sim | 30 | 0-100 |
| data_fecho | date | Sim | null | |
| comercial_id | bigint unsigned | Sim | null | FK commercials |
| produto_id | bigint unsigned | Sim | null | FK products |
| notas | text | Sim | null | |
| fase_atual | varchar(50) | Não | 'lead' | 9 valores |
| motivo_nao_aprovacao | text | Sim | null | |
| data_aprovacao | timestamp | Sim | null | |
| ultima_actividade_em | timestamp | Sim | null | |
| ultimo_contacto_em | timestamp | Sim | null | |
| deleted_at | timestamp | Sim | null | |
| timestamps | | | | |

**Valores de `fase_atual`:**
```
lead, qualificacao, visita, proposta_negociacao,
aprovacao, bilhete, resultado, cliente_aprovado, cliente_nao_aprovado
```

**Valores de `tipo_relacao`:**
```
novo_contacto, potencial_cliente, cliente_recorrente, parceiro, fornecedor
```

### 8.4 Tabela `opportunity_stage_history`

| Coluna | Tipo | Nullable | Notas |
|---|---|---|---|
| id | bigint unsigned | Não | PK |
| opportunity_id | bigint unsigned | Não | FK |
| fase_anterior | varchar(50) | Sim | null |
| fase_nova | varchar(50) | Não | |
| user_id | bigint unsigned | Não | FK |
| observacao | text | Sim | null |
| created_at | timestamp | Não | |

### 8.5 Tabela `opportunity_product` (pivot)

| Coluna | Tipo | Nullable | Notas |
|---|---|---|---|
| id | bigint unsigned | Não | PK |
| opportunity_id | bigint unsigned | Não | FK |
| product_id | bigint unsigned | Não | FK |
| created_at | timestamp | Sim | null |

### 8.6 Tabela `activities`

| Coluna | Tipo | Nullable | Default | Notas |
|---|---|---|---|---|
| id | bigint unsigned | Não | auto | PK |
| uuid | char(36) | Não | | unique |
| opportunity_id | bigint unsigned | Não | | FK |
| user_id | bigint unsigned | Não | | FK |
| tipo | varchar(50) | Não | | visita, chamada, reuniao, proposta, cobranca |
| descricao | text | Não | | |
| resultado | text | Sim | null | |
| data | date | Não | | |
| hora | time | Sim | null | |
| proxima_accao | text | Sim | null | |
| proxima_accao_prazo | date | Sim | null | |
| status | varchar(50) | Não | 'pendente' | pendente, concluida, cancelada |
| timestamps | | | | |

### 8.7 Tabela `visit_reports`

| Coluna | Tipo | Nullable | Notas |
|---|---|---|---|
| id | bigint unsigned | Não | PK |
| uuid | char(36) | Não | unique |
| opportunity_id | bigint unsigned | Não | FK |
| user_id | bigint unsigned | Não | FK |
| data_visita | date | Não | |
| objectivo | text | Sim | null |
| pessoas_presentes | text | Sim | null |
| assuntos_tratados | text | Não | |
| resultado | text | Sim | null |
| proxima_accao | text | Sim | null |
| anexos | json | Sim | null |
| timestamps | | | |

### 8.8 Tabela `products`

| Coluna | Tipo | Nullable | Default | Notas |
|---|---|---|---|---|
| id | bigint unsigned | Não | auto | PK |
| uuid | char(36) | Não | | unique |
| nome | varchar(255) | Não | | |
| numero_serie | varchar(100) | Sim | null | |
| codigo | varchar(50) | Não | | unique |
| valor | decimal(15,2) | Sim | null | |
| comercial_id | bigint unsigned | Sim | null | FK commercials |
| activo | boolean | Não | true | |
| timestamps | | | | |

### 8.9 Tabela `conciliactions`

| Coluna | Tipo | Nullable | Default | Notas |
|---|---|---|---|---|
| id | bigint unsigned | Não | auto | PK |
| uuid | char(36) | Não | | unique |
| opportunity_id | bigint unsigned | Sim | null | FK |
| comercial_id | bigint unsigned | Sim | null | FK |
| data | date | Não | | |
| valor | decimal(15,2) | Sim | null | |
| classificacao | varchar(50) | Não | 'pendente' | concordante, discordante, pendente |
| observacoes | text | Sim | null | |
| resolvido | boolean | Não | false | |
| resolvido_em | timestamp | Sim | null | |
| timestamps | | | | |

### 8.10 Tabela `contracts`

| Coluna | Tipo | Nullable | Default | Notas |
|---|---|---|---|---|
| id | bigint unsigned | Não | auto | PK |
| uuid | char(36) | Não | | unique |
| opportunity_id | bigint unsigned | Não | | FK |
| comercial_id | bigint unsigned | Não | | FK |
| produto_id | bigint unsigned | Não | | FK |
| numero_contrato | varchar(50) | Não | | unique |
| tipo | varchar(50) | Sim | null | |
| data_inicio | date | Não | | |
| data_fim | date | Não | | caducidade |
| valor | decimal(15,2) | Sim | null | |
| estado | varchar(50) | Não | 'activo' | activo, expirado, renovado, cancelado |
| documento | varchar(255) | Sim | null | |
| notas | text | Sim | null | |
| deleted_at | timestamp | Sim | null | |
| timestamps | | | | |

### 8.11 Tabela `alerts`

| Coluna | Tipo | Nullable | Default | Notas |
|---|---|---|---|---|
| id | bigint unsigned | Não | auto | PK |
| uuid | char(36) | Não | | unique |
| tipo | varchar(50) | Não | | oportunidade, cliente, contrato, lead_inativo, conciliacao |
| entidade | varchar(100) | Não | | opportunities, contracts, conciliactions |
| entidade_id | bigint unsigned | Não | | |
| mensagem | text | Não | | |
| user_id | bigint unsigned | Não | | FK |
| prazo | date | Sim | null | |
| lido | boolean | Não | false | |
| resolvido | boolean | Não | false | |
| resolvido_em | timestamp | Sim | null | |
| timestamps | | | | |

### 8.12 Tabela `settings`

| Coluna | Tipo | Nullable | Notas |
|---|---|---|---|
| id | bigint unsigned | Não | PK |
| key | varchar(100) | Não | unique |
| value | text | Sim | null |
| timestamps | | | |

**Valores previstos:**
- `alertas.oportunidades_dias` = 7
- `alertas.leads_inativos_dias` = 7
- `alertas.clientes_dias` = 60
- `alertas.contratos_dias` = 90
- `alertas.email_destino` = email do Adailson
- `agenda.lembrete_hora` = 20:00

---

## 9. ENUMS

```php
enum OpportunityStage: string {
    case LEAD = 'lead';
    case QUALIFICACAO = 'qualificacao';
    case VISITA = 'visita';
    case PROPOSTA_NEGOCIACAO = 'proposta_negociacao';
    case APROVACAO = 'aprovacao';
    case BILHETE = 'bilhete';
    case RESULTADO = 'resultado';
    case CLIENTE_APROVADO = 'cliente_aprovado';
    case CLIENTE_NAO_APROVADO = 'cliente_nao_aprovado';
}

enum TipoRelacao: string {
    case NOVO_CONTACTO = 'novo_contacto';
    case POTENCIAL_CLIENTE = 'potencial_cliente';
    case CLIENTE_RECORRENTE = 'cliente_recorrente';
    case PARCEIRO = 'parceiro';
    case FORNECEDOR = 'fornecedor';
}

enum ActivityType: string {
    case VISITA = 'visita';
    case CHAMADA = 'chamada';
    case REUNIAO = 'reuniao';
    case PROPOSTA = 'proposta';
    case COBRANCA = 'cobranca';
}

enum ActivityStatus: string {
    case PENDENTE = 'pendente';
    case CONCLUIDA = 'concluida';
    case CANCELADA = 'cancelada';
}

enum ConciliactionClassificacao: string {
    case CONCORDANTE = 'concordante';
    case DISCORDANTE = 'discordante';
    case PENDENTE = 'pendente';
}

enum ContractStatus: string {
    case ACTIVO = 'activo';
    case EXPIRADO = 'expirado';
    case RENOVADO = 'renovado';
    case CANCELADO = 'cancelado';
}

enum AlertType: string {
    case OPORTUNIDADE = 'oportunidade';
    case CLIENTE = 'cliente';
    case CONTRATO = 'contrato';
    case LEAD_INATIVO = 'lead_inativo';
    case CONCILIACAO = 'conciliacao';
}
```

---

## 10. ALERTAS — REGRAS DEFINITIVAS

| Tipo | Prazo | Base | Acção |
|---|---|---|---|
| Oportunidades | 7 dias | Última actividade | Notificar |
| Leads inativos | 7 dias | Parado em Leads | Notificar |
| Clientes | 60 dias | Último contacto | Notificar |
| Contratos | 90 dias | Antes da caducidade | Notificar |
| Conciliações | — | Pendentes | Notificar |

### Regra crítica

> **O sistema NÃO move leads automaticamente.**  
> Apenas notifica. O utilizador decide manualmente.

### Canais

- **Dentro do sistema** — área de notificações no Dashboard
- **Por email** — para o email do Adailson

---

## 11. AGENDA — LEMBRETE DIÁRIO

- **Todos os dias às 20h** → enviar agenda do dia seguinte
- **Canais:** email + notificação no sistema
- **Conteúdo:** lista de actividades do dia seguinte (hora, tipo, cliente)
- **Configurável** em `settings`

---

## 12. ESTRUTURA DE PASTAS

```
adg/
├── app/
│   ├── Enums/
│   ├── Livewire/
│   │   ├── Dashboard/
│   │   ├── Opportunities/
│   │   ├── Activities/
│   │   ├── Conciliactions/
│   │   ├── Contracts/
│   │   ├── Products/
│   │   ├── Commercials/
│   │   └── Alerts/
│   ├── Models/
│   ├── Services/
│   └── Notifications/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── components/
│   │   └── livewire/
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
├── public/
│   ├── images/
│   │   └── logo-d.png
│   └── assets/
│       ├── css/
│       ├── js/
│       ├── vendors/
│       └── images/
└── routes/
    └── web.php
```

---

## 13. SPRINTS — PLANO ACTUALIZADO

### Sprint 0 — Preparação ✅ Concluído

### Sprint 1 — Fundação ✅ Concluído

### Sprint 1.1 — UI/UX Base ✅ Concluído

### Sprint 2 — Pipeline ✅ Concluído (precisa actualização)

**Ajustes necessários:**
- Actualizar de 5 → 9 fases
- Adicionar campos novos (`pessoa_contacto`, `tipo_relacao`, `comercial_id`, `produto_id`)
- Criar tabela `commercials`
- Criar pivot `opportunity_product`

### Sprint 3 — Nova Oportunidade + Detalhe ✅ Concluído (precisa actualização)

**Ajustes necessários:**
- Adicionar campos novos ao formulário
- Adicionar relação com comercial
- Adicionar relação com produto

### Sprint 4 — Importação Excel ✅ Concluído

**Ajustes necessários:**
- Adicionar colunas novas ao template

### Sprint 5 — Agenda Comercial ✅ Concluído

**Ajustes necessários:**
- Adicionar lembrete diário às 20h
- Criar command `agenda:reminder`

### Sprint 6 — Conciliações ⛔ A implementar

**Baseado nas suposições:**
- Tabela `conciliactions` com classificacao Concordante/Discordante/Pendente
- Livewire list + form
- View adaptada de `payment.html`

### Sprint 7 — Contratos ⛔ A implementar

**Baseado nas suposições:**
- Tabela `contracts` com relação a produto, comercial, cliente
- Livewire list + form + view
- Views adaptadas de `projects.html`, `projects-create.html`, `projects-view.html`

### Sprint 8 — Produtos ⛔ A implementar

- Tabela `products`
- Livewire list + form
- View nova (adaptar de `customers.html`)

### Sprint 9 — Comerciais ⛔ A implementar (NOVO)

- Tabela `commercials`
- Livewire list + form
- Vista de carteira por comercial

### Sprint 10 — Alertas ⛔ A implementar

- Tabela `alerts`
- Service `AlertService` (sem movimentação automática)
- Command `alerts:generate` (agendado)
- Notification email
- Livewire list + badge

### Sprint 11 — Dashboard + Painel Executivo ⛔ A implementar

- 6 blocos no Dashboard
- Chart.js
- View adaptada de `index.html` e `analytics.html`

### Sprint 12 — Testes e Lançamento ⛔ A implementar

- Testes com Adailson
- Feedback
- Ajustes
- Deploy
- SSL
- Backups

---

## 14. CONVENÇÕES DE CÓDIGO

### Nomenclatura

- Classes: `PascalCase`
- Métodos: `camelCase`
- Variáveis: `camelCase`
- Tabelas: `snake_case` plural
- Colunas: `snake_case`
- Enums: `PascalCase` com casos em `SCREAMING_SNAKE_CASE`

### Livewire 3

- `wire:model.live` para reactividade imediata
- `wire:model.blur` para validação ao sair
- `#[Locked]` para propriedades protegidas
- `$this->validate()` para validação
- `$this->dispatch()` para eventos
- `wire:key` em loops
- Nunca usar `$this->emit()`

### Base de Dados

- Todas as tabelas têm `timestamps`
- Soft deletes em `opportunities` e `contracts`
- Valores monetários: `decimal(15,2)`
- Índices em colunas de filtro frequente

### Testes

- Feature tests para cada componente Livewire
- Unit tests para Enums e Services
- Cobertura mínima: 70%
- `RefreshDatabase` trait
- Factories para todos os models

---

## 15. REGRAS PARA A IA

### Regras absolutas

1. **Nunca** gerar código fora do módulo actual
2. **Nunca** adicionar dependências fora da stack definida
3. **Nunca** usar Livewire 2
4. **Nunca** usar jQuery no Laravel
5. **Nunca** usar Vue ou React
6. **Nunca** inventar visual novo
7. **Nunca** mudar cores, tipografia ou layout do protótipo
8. **Nunca** avançar para o próximo módulo sem validação
9. **Nunca** converter Bootstrap para Tailwind sem necessidade
10. **Nunca** bloquear o desenvolvimento à espera do cliente
11. **Nunca** mover leads automaticamente

### Regras de acção

12. **Sempre** basear as views no HTML do protótipo
13. **Sempre** abrir o ficheiro HTML correspondente antes de criar a view
14. **Sempre** copiar a estrutura visual do protótipo
15. **Sempre** manter as classes Bootstrap
16. **Sempre** substituir dados fictícios por variáveis reais
17. **Sempre** usar `$this->validate()` para validação
18. **Sempre** usar `#[Locked]` quando apropriado
19. **Sempre** criar testes para cada componente
20. **Sempre** documentar métodos complexos com PHPDoc
21. **Sempre** seguir as suposições documentadas quando não há resposta do cliente
22. **Sempre** marcar código com `// SUPOSIÇÃO:` quando implementar algo não confirmado

### Regras de comunicação

23. **Sempre** resumir o que foi feito no fim de cada módulo
24. **Sempre** listar ficheiros criados/modificados
25. **Sempre** mostrar resultado dos testes
26. **Sempre** perguntar se pode avançar

---

## 16. ORDEM DE DESENVOLVIMENTO

Para cada módulo, seguir sempre esta ordem:

1. Enums necessários
2. Migrations
3. Models com relações
4. Factories
5. Seeders
6. Services (se aplicável)
7. Componentes Livewire
8. Views Blade (baseadas no protótipo)
9. Rotas
10. Testes

---

## 17. COMANDOS ÚTEIS

```bash
# Desenvolvimento
php artisan serve --no-reload
npm run dev
php artisan pail

# Base de dados
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed

# Testes
php artisan test
php artisan test --filter=NomeTest

# Formatação
./vendor/bin/pint

# Cache
php artisan optimize:clear

# Comandos específicos
php artisan alerts:generate
php artisan agenda:reminder
```

---

## 18. CHECKLIST POR MÓDULO

Antes de declarar um módulo concluído:

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

## 19. SUPOSIÇÕES DOCUMENTADAS

Todas as decisões tomadas sem confirmação do cliente estão marcadas para validação futura:

| # | Suposição | Onde |
|---|---|---|
| 1 | Pipeline tem 9 fases | `OpportunityStage` |
| 2 | Comerciais são categorias, não pessoas | `commercials` |
| 3 | Tipo de relação = 5 opções | `TipoRelacao` |
| 4 | Produto relacionado = relação com tabela | `opportunities.produto_id` |
| 5 | Nome e pessoa_contacto = campos separados | `opportunities` |
| 6 | Produto ↔ Oportunidades = many-to-many | `opportunity_product` |
| 7 | Produto ↔ Contratos = um por contrato | `contracts.produto_id` |
| 8 | Alerta cliente = 60 dias | `settings` |
| 9 | Email de alertas = email do Adailson | `settings` |
| 10 | Lembrete 20h = email + sistema | Agenda |
| 11 | Conciliação = Concordante/Discordante/Pendente | `conciliactions` |
| 12 | Contrato expirado = muda estado | `contracts` |
| 13 | Ordem Aprovação → Bilhete | Pipeline |
| 14 | Alertas sem movimentação automática | `AlertService` |

**Quando o cliente testar, ele dirá o que está correcto e o que quer mudar.**

---

## 20. COMUNICAÇÃO APÓS CADA MÓDULO

Após cada módulo, resumir:

```
## Sprint X — [Nome] concluído

### Ficheiros criados
- ...

### Ficheiros modificados
- ...

### Testes
- X testes, Y asserções, todos a passar

### Suposições implementadas
- ...

### Comparação com o protótipo
- Visual: igual / diferente (explicar)

### Como testar
1. ...
2. ...

### Próximo sprint
Sprint X+1 — [Nome]

Posso avançar?
```

---

## 21. COMECE AQUI

**Passo 1:** Verificar que Sprint 5 está concluído

**Passo 2:** Aplicar os ajustes dos Sprints 2 e 3 (novas fases, novos campos)

**Passo 3:** Avançar para Sprint 6 (Conciliações)

**Passo 4:** Continuar na ordem dos sprints

**Aguarda confirmação do utilizador para iniciar.**

---

## 22. NOTAS FINAIS PARA A IA

### O que este projecto NÃO é

- Não é um exercício de design
- Não é uma oportunidade de "melhorar" o protótipo
- Não é um projecto de demonstração
- Não é uma reconstrução do zero

### O que este projecto É

- Uma migração do protótipo HTML para Laravel
- Uma preservação do visual aprovado
- Uma adição de backend funcional
- Uma adição de novas funcionalidades
- Uma construção iterativa com validação do cliente

### A regra de ouro

> Se tiveres dúvida sobre como algo deve parecer, **abre o HTML do protótipo e copia**.  
> Se tiveres dúvida sobre como algo deve funcionar, **segue as suposições documentadas**.  
> Quando o cliente testar, ele dirá o que quer mudar.

### Sobre Bootstrap vs Tailwind

> O protótipo usa **Bootstrap como framework principal** e Tailwind pontualmente.  
> **Mantém o Bootstrap.** Não convertas para Tailwind só por converter.  
> O visual foi aprovado em Bootstrap — não vamos mudá-lo.

---

**Documento:** resposta.md  
**Versão:** 4.0  
**Data:** Outubro de 2026  
**Projecto:** ADG  
**Próxima revisão:** Após feedback do cliente
```