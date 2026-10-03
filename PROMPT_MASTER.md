```markdown
# PROMPT MASTER — Sistema ADG
## Instruções Completas para a IA

**Versão:** 3.0  
**Data:** Outubro de 2026  
**Destinatário:** IA de desenvolvimento  
**Projecto:** ADG — Ferramenta de Gestão Comercial Pessoal  
**Cliente:** Adailson Nogueira  
**Empresa:** Otimize

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

---

## 1. CONTEXTO DO PROJECTO

### O que é

Ferramenta web de gestão comercial **pessoal**, utilizada exclusivamente por **um único utilizador: Adailson Nogueira**.

### Empresa que desenvolve

Otimize — Business Intelligence

### Princípio fundamental

> **O sistema faz e o utilizador confirma. Automação acima de tudo.**

### Modelo de negócio

```
Nova Oportunidade
    ↓
[Importar Excel] ou [Cadastrar Manual]
    ↓
Pipeline (fases de aprovação)
    ↓
Cliente Aprovado → torna-se Cliente
OU
Cliente Não Aprovado → perdido
```

**Regras críticas:**
- Não há módulo separado de clientes — o cliente nasce da oportunidade aprovada
- Todo o registo começa em Nova Oportunidade
- A aprovação acontece no pipeline

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

### Tecnologias do protótipo (IMPORTANTE)

O protótipo **NÃO é Tailwind puro**. É uma mistura:

| Tecnologia | Uso no protótipo |
|---|---|
| **Bootstrap 5.2** | Framework CSS principal |
| **Tailwind CSS** | Usado pontualmente em algumas classes utilitárias |
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

### Mapa protótipo → módulos

| Página do protótipo | Módulo Laravel | Acção |
|---|---|---|
| `index.html` | Dashboard | Copiar + ligar dados reais |
| `leads.html` | Pipeline | Copiar + renomear colunas |
| `leads-create.html` | Nova Oportunidade | Copiar + adaptar campos |
| `leads-view.html` | Detalhe da Oportunidade | Copiar + adicionar Relatório de Visita |
| `apps-calendar.html` | Agenda (calendário) | Copiar |
| `apps-tasks.html` | Agenda (actividades) | Copiar |
| `apps-notes.html` | Agenda (registo) | Copiar |
| `payment.html` | Conciliações | Adaptar (mudar conteúdo) |
| `projects.html` | Contratos | Adaptar (mudar conteúdo) |
| `projects-create.html` | Criar Contrato | Adaptar |
| `projects-view.html` | Detalhe de Contrato | Adaptar |
| `alerts.html` | Alertas | Copiar + actualizar |
| `analytics.html` | Painel Executivo | Copiar + actualizar |
| `customers.html` | — | **Remover** |
| `customers-create.html` | — | **Remover** |
| `customers-view.html` | — | **Remover** |
| `customers-distribution.html` | — | **Remover** |
| `auth-login-cover.html` | Login | Copiar |
| `auth-login-minimal.html` | — | **Remover** |
| `auth-register-cover.html` | — | **Remover** (utilizador único) |

### Páginas novas (não existem no protótipo)

- `products.html` — Catálogo de produtos (criar com o estilo do protótipo)
- `conciliactions.html` — Lista de conciliações (adaptar de `payment.html`)
- `contracts.html` — Lista de contratos (adaptar de `projects.html`)

---

## 3. IDENTIDADE VISUAL

### Nome do sistema

**ADG**

### Logotipo

- Ficheiro: `logo-d.png`
- Localização: raiz do protótipo (`crm-prototipo/logo-d.png`)
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
| Fundo da app | `#eff0f6` |
| Sucesso | `#17c666` |
| Perigo | `#ea4d4d` |
| Aviso | `#ffa21d` |
| Info | `#3dc7be` |

### Tipografia

- Fonte: **Inter** (400/500/600/700)
- Ícones: Feather Icons, Font Awesome, Bootstrap Icons

### Layout

- **Sidebar fixa à esquerda** (256px em desktop, escura)
- **Header branco fixo** (64px, sticky)
- **Conteúdo** em cartões brancos sobre fundo cinza-azulado

---

## 4. ESTRATÉGIA DE MIGRAÇÃO CSS/JS

Esta é a parte mais crítica. O protótipo usa **Bootstrap + Tailwind + jQuery**. O Laravel vai usar **Tailwind + Alpine + Livewire**. Precisamos de uma estratégia clara para não partir o visual.

### Regra geral

> **Manter Bootstrap no Laravel.** Não converter para Tailwind só por converter.

### Porquê

- O protótipo foi construído em Bootstrap
- Se converteres tudo para Tailwind, o visual **vai mudar** em detalhes subtis
- O cliente aprovou o visual Bootstrap — não queremos surpresas
- Coexistência Bootstrap + Tailwind é possível e comum

### Como fazer

**1. Instalar Bootstrap no Laravel**

```bash
npm install bootstrap @popperjs/core
```

**2. Importar Bootstrap no `resources/css/app.css`**

```css
@import 'bootstrap/dist/css/bootstrap.min.css';
@import 'tailwindcss';
```

**3. Manter Bootstrap para:**

- Grid (`row`, `col-*`)
- Componentes (`btn`, `card`, `modal`, `dropdown`, `nav`, `form-control`)
- Utilitários (`d-flex`, `mb-3`, `text-center`)

**4. Usar Tailwind para:**

- Classes que não existem no Bootstrap
- Ajustes específicos
- Componentes novos que não existem no protótipo

**5. Não usar jQuery no Laravel**

- Substituir por Alpine.js (interacções simples)
- Substituir por Livewire (interacções com backend)

**6. Manter o `theme.min.css` do protótipo?**

**Não.** O `theme.min.css` é específico do template NEXEL e pode entrar em conflito com a estrutura Laravel. Em vez disso:

- Extrair as variáveis CSS do `theme.min.css` (cores, espaçamentos, sombras)
- Recriar essas variáveis no `app.css` do Laravel
- Não importar o `theme.min.css` directamente

### Componentes que devem ser mantidos como estão

| Componente | Manter Bootstrap? |
|---|---|
| Botões | Sim (`btn btn-primary`) |
| Cards | Sim (`card`, `card-body`) |
| Modais | Sim, mas controlados por Alpine |
| Dropdowns | Sim, mas controlados por Alpine |
| Tabelas | Sim (`table`, `table-hover`) |
| Formulários | Sim (`form-control`, `form-select`) |
| Grid | Sim (`row`, `col-*`) |
| Badges | Sim (`badge`) |
| Alertas | Sim (`alert`) |

### Componentes que devem ser substituídos

| Componente | Substituir por |
|---|---|
| jQuery (interacções) | Alpine.js |
| DataTables | Livewire (tabela com paginação) |
| Select2 | Livewire + select nativo estilizado |
| ApexCharts | Chart.js |
| TUI Calendar | Livewire + calendário simples |
| Quill | textarea estilizado |
| Daterangepicker | input type="date" estilizado |

**Nota:** as substituições devem **manter o mesmo visual**. Se o DataTables mostrava uma tabela com pesquisa e paginação, o Livewire deve mostrar a mesma tabela com a mesma pesquisa e paginação — só que em vez de ser jQuery, é Livewire.

---

## 5. STACK TÉCNICA FINAL

| Camada | Tecnologia | Versão |
|---|---|---|
| Backend | Laravel | 12 |
| PHP | PHP | 8.4 (Herd) |
| Base de Dados | MySQL | 8 |
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

## 6. NOMENCLATURA OBRIGATÓRIA

| Termo no sistema | Significado |
|---|---|
| **Lead** | Fase inicial do pipeline |
| **Visita** | Fase intermédia (com relatório) |
| **Aprovação** | Fase de decisão |
| **Cliente Aprovado** | Estado final positivo |
| **Cliente Não Aprovado** | Estado final negativo |
| **Conciliações** | Substitui o antigo "Crédito" |
| **Contratos** | Substitui o antigo "Encomendas" |
| **Produtos** | Catálogo de produtos |

**Nunca usar:** Ganho, Perdido, Crédito, Encomendas

---

## 7. MENU LATERAL FINAL

```
Dashboard
Pipeline
Nova Oportunidade
Agenda Comercial
Conciliações
Contratos
Produtos
Alertas
Painel Executivo
```

### Ordem das sub-opções de Oportunidades

1. **Pipeline** (primeiro)
2. **Nova Oportunidade** (segundo)

---

## 8. MODELO DE DADOS

### 8.1 Tabela `users`

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint unsigned | PK |
| name | varchar(255) | "Adailson Nogueira" |
| username | varchar(50) | unique |
| email | varchar(255) | unique |
| password | varchar(255) | hashed |
| email_verified_at | timestamp | nullable |
| remember_token | varchar(100) | nullable |
| timestamps | | |

**Nota:** apenas 1 registo.

### 8.2 Tabela `opportunities`

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint unsigned | PK |
| uuid | char(36) | unique |
| nome | varchar(255) | Nome da pessoa ou empresa |
| empresa | varchar(255) | nullable |
| telefone | varchar(50) | nullable |
| email | varchar(255) | nullable |
| nif | varchar(50) | nullable |
| codigo_sap | varchar(50) | nullable |
| segmento | varchar(100) | nullable |
| localizacao | varchar(255) | nullable |
| origem | varchar(100) | nullable |
| interesse | text | nullable |
| valor_estimado | decimal(15,2) | nullable |
| notas | text | nullable |
| fase_atual | varchar(50) | lead, visita, aprovacao, aprovado, nao_aprovado |
| motivo_nao_aprovacao | text | nullable |
| data_aprovacao | timestamp | nullable |
| ultima_actividade_em | timestamp | nullable |
| ultimo_contacto_em | timestamp | nullable |
| deleted_at | timestamp | nullable |
| timestamps | | |

**Índices:** uuid, fase_atual, email, telefone, ultima_actividade_em, ultimo_contacto_em

### 8.3 Tabela `opportunity_stage_history`

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint unsigned | PK |
| opportunity_id | bigint unsigned | FK |
| fase_anterior | varchar(50) | nullable |
| fase_nova | varchar(50) | |
| user_id | bigint unsigned | FK |
| observacao | text | nullable |
| created_at | timestamp | |

### 8.4 Tabela `activities`

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint unsigned | PK |
| uuid | char(36) | unique |
| opportunity_id | bigint unsigned | FK |
| user_id | bigint unsigned | FK |
| tipo | varchar(50) | visita, chamada, reuniao, proposta, cobranca |
| descricao | text | |
| resultado | text | nullable |
| data | date | |
| hora | time | nullable |
| proxima_accao | text | nullable |
| proxima_accao_prazo | date | nullable |
| status | varchar(50) | pendente, concluida, cancelada |
| timestamps | | |

### 8.5 Tabela `visit_reports`

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint unsigned | PK |
| uuid | char(36) | unique |
| opportunity_id | bigint unsigned | FK |
| user_id | bigint unsigned | FK |
| data_visita | date | |
| objectivo | text | nullable |
| pessoas_presentes | text | nullable |
| assuntos_tratados | text | |
| resultado | text | nullable |
| proxima_accao | text | nullable |
| anexos | json | nullable |
| timestamps | | |

### 8.6 Tabela `products`

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint unsigned | PK |
| uuid | char(36) | unique |
| nome | varchar(255) | |
| numero_serie | varchar(100) | nullable |
| codigo | varchar(50) | unique |
| valor | decimal(15,2) | nullable |
| activo | boolean | default true |
| timestamps | | |

### 8.7 Tabela `conciliactions` (EM DEFINIÇÃO)

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint unsigned | PK |
| uuid | char(36) | unique |
| opportunity_id | bigint unsigned | FK nullable |
| ... | ... | a definir |
| status | varchar(50) | pendente, concluida |
| timestamps | | |

### 8.8 Tabela `contracts` (EM DEFINIÇÃO)

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint unsigned | PK |
| uuid | char(36) | unique |
| opportunity_id | bigint unsigned | FK |
| numero_contrato | varchar(50) | unique |
| tipo | varchar(50) | nullable |
| data_inicio | date | |
| data_fim | date | caducidade |
| valor | decimal(15,2) | nullable |
| estado | varchar(50) | activo, expirado, renovado, cancelado |
| documento | varchar(255) | nullable |
| notas | text | nullable |
| deleted_at | timestamp | nullable |
| timestamps | | |

### 8.9 Tabela `alerts`

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint unsigned | PK |
| uuid | char(36) | unique |
| tipo | varchar(50) | oportunidade, cliente, contrato, lead_inativo, conciliacao |
| entidade | varchar(100) | opportunities, contracts, conciliactions |
| entidade_id | bigint unsigned | |
| mensagem | text | |
| user_id | bigint unsigned | FK |
| prazo | date | nullable |
| lido | boolean | default false |
| resolvido | boolean | default false |
| resolvido_em | timestamp | nullable |
| timestamps | | |

### 8.10 Tabela `settings`

| Coluna | Tipo | Notas |
|---|---|---|
| id | bigint unsigned | PK |
| key | varchar(100) | unique |
| value | text | nullable |
| timestamps | | |

**Exemplos:**
- `alertas.oportunidades_dias` = 7
- `alertas.clientes_dias` = 30
- `alertas.contratos_dias` = 90
- `alertas.leads_inativos_dias` = a definir

---

## 9. ENUMS

```php
enum OpportunityStage: string {
    case LEAD = 'lead';
    case VISITA = 'visita';
    case APROVACAO = 'aprovacao';
    case APROVADO = 'aprovado';
    case NAO_APROVADO = 'nao_aprovado';
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

## 10. ALERTAS — REGRAS CONFIRMADAS

| Tipo | Prazo | Base |
|---|---|---|
| Oportunidades | 7 dias | Última actividade |
| Clientes | 30 dias | Último contacto |
| Contratos | 90 dias | Antes da caducidade |
| Leads inativos | X dias | Parado em fase Leads |

**Regra de automação confirmada:**
> Lead parado → notificação → se continuar sem avançar após X notificações → move automaticamente para "Não Aprovado".

---

## 11. ESTRUTURA DE PASTAS

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

**Nota:** copiar a pasta `assets/` do protótipo para `public/assets/` para reutilizar ícones, fontes, etc.

---

## 12. METODOLOGIA — COMO CONSTRUIR CADA MÓDULO

### Passo 1 — Analisar o protótipo

Abre o HTML da página correspondente em `crm-prototipo/` e estuda:
- Estrutura HTML
- Classes usadas (Bootstrap e Tailwind)
- Componentes visuais (cards, tabelas, formulários, botões)
- Ícones
- Layout

### Passo 2 — Criar o backend

1. Enum (se aplicável)
2. Migration
3. Model com relações
4. Factory
5. Seeder (se aplicável)
6. Service (se aplicável)

### Passo 3 — Migrar o HTML para Blade

1. Copia o HTML do protótipo
2. Substitui dados fictícios por variáveis Blade/Livewire
3. Substitui links estáticos por `route()`
4. **Mantém as classes Bootstrap**
5. Usa Tailwind apenas onde necessário
6. Substitui jQuery por Alpine onde for interacção simples
7. Substitui scripts jQuery de plugins por Livewire (DataTables → Livewire, etc.)

### Passo 4 — Adicionar lógica Livewire

1. Cria o componente Livewire
2. Liga as acções (criar, editar, eliminar)
3. Validação com `$this->validate()`
4. Eventos com `$this->dispatch()`

### Passo 5 — Rotas

1. Define as rotas em `web.php`
2. Aplica middleware `auth`

### Passo 6 — Testes

1. Feature tests para CRUD
2. Feature tests para permissões
3. Unit tests para Services

### Passo 7 — Validação

1. Corre `php artisan test`
2. Corre `./vendor/bin/pint`
3. Abre no browser e compara com o protótipo
4. **O visual tem de ser igual**

---

## 13. CONVENÇÕES DE CÓDIGO

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

## 14. REGRAS PARA A IA

### Regras absolutas

1. **Nunca** gerar código fora do módulo actual
2. **Nunca** adicionar dependências fora da stack definida
3. **Nunca** usar Livewire 2
4. **Nunca** usar jQuery no Laravel
5. **Nunca** usar Vue ou React
6. **Nunca** inventar visual novo
7. **Nunca** mudar cores, tipografia ou layout do protótipo
8. **Nunca** assumir decisões que não estão documentadas
9. **Nunca** avançar para o próximo módulo sem validação
10. **Nunca** converter Bootstrap para Tailwind sem necessidade

### Regras de acção

11. **Sempre** basear as views no HTML do protótipo
12. **Sempre** abrir o ficheiro HTML correspondente antes de criar a view
13. **Sempre** copiar a estrutura visual do protótipo
14. **Sempre** manter as classes Bootstrap
15. **Sempre** substituir dados fictícios por variáveis reais
16. **Sempre** usar `$this->validate()` para validação
17. **Sempre** usar `#[Locked]` quando apropriado
18. **Sempre** criar testes para cada componente
19. **Sempre** documentar métodos complexos com PHPDoc
20. **Sempre** perguntar antes de avançar

### Regras de comunicação

21. **Sempre** resumir o que foi feito no fim de cada módulo
22. **Sempre** listar ficheiros criados/modificados
23. **Sempre** mostrar resultado dos testes
24. **Sempre** perguntar se pode avançar

---

## 15. ORDEM DE DESENVOLVIMENTO

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

## 16. SPRINTS

### Sprint 0 — Preparação (2-3 dias)

- [ ] Instalar Laravel Herd
- [ ] Criar projecto Laravel 12 com nome `adg`
- [ ] Instalar Laravel Breeze (Blade + Livewire)
- [ ] Instalar Livewire 3
- [ ] Instalar Bootstrap 5.3 + Popper
- [ ] Configurar Tailwind CSS 4 + Vite 7
- [ ] Configurar MySQL
- [ ] Criar repositório Git
- [ ] Copiar `logo-d.png` para `public/images/`
- [ ] Copiar pasta `assets/` do protótipo para `public/assets/`
- [ ] Configurar `app.css` com Bootstrap + Tailwind
- [ ] Configurar `app.js` com Alpine

**Validação:** `php artisan serve` corre, login funciona, Bootstrap e Tailwind carregam.

---

### Sprint 1 — Fundação (1 semana)

- [ ] Criar Enums
- [ ] Criar layouts base (app, guest, navigation) — copiando do protótipo
- [ ] Configurar autenticação (username + email)
- [ ] Criar UserSeeder com Adailson Nogueira
- [ ] Criar SettingSeeder
- [ ] Aplicar cores do protótipo
- [ ] Configurar tipografia Inter
- [ ] Testes de autenticação

**Validação:** Login funcional, cores correctas, tipografia carregada.

---

### Sprint 1.1 — UI/UX Base (3-4 dias)

**NOTA:** Aqui o objectivo **não é criar componentes do zero**. É:

1. **Identificar componentes visuais no protótipo** que se repetem
2. **Criar componentes Blade** que reproduzam exactamente esses visuais
3. **Não inventar novos componentes** — só os que existem no protótipo

**Componentes a extrair do protótipo:**

- [ ] `button` — copiar do protótipo (`btn btn-primary`, etc.)
- [ ] `badge` — copiar do protótipo
- [ ] `form-input`, `form-select`, `form-textarea` — copiar do protótipo
- [ ] `modal` — copiar do protótipo
- [ ] `stat-card` — copiar do protótipo (`index.html` tem exemplos)
- [ ] `page-header` — copiar do protótipo
- [ ] `data-table` — copiar do protótipo (`customers.html` tem exemplo)
- [ ] `alert-badge` — copiar do protótipo (header)

**Layout:**

- [ ] `sidebar` — copiar de qualquer página do protótipo
- [ ] `header` — copiar de qualquer página do protótipo

**Validação:** Componentes renderizam, comparar com protótipo lado a lado.

---

### Sprint 2 — Pipeline (1 semana)

- [ ] Migration `opportunities`
- [ ] Migration `opportunity_stage_history`
- [ ] Model `Opportunity`
- [ ] Model `OpportunityStageHistory`
- [ ] Factory `OpportunityFactory`
- [ ] Service `OpportunityService`
- [ ] Componente `Pipeline`
- [ ] View copiada de `crm-prototipo/leads.html`
- [ ] Actualizar colunas: Leads, Visitas, Aprovação, Aprovado, Não Aprovado
- [ ] Rotas
- [ ] Testes

---

### Sprint 3 — Nova Oportunidade + Detalhe (1 semana)

- [ ] Componente `OpportunityCreate`
- [ ] View copiada de `crm-prototipo/leads-create.html`
- [ ] Componente `OpportunityView`
- [ ] View copiada de `crm-prototipo/leads-view.html`
- [ ] Adicionar aba **Relatório de Visita**
- [ ] Migration `visit_reports`
- [ ] Model `VisitReport`
- [ ] Componente `VisitReportForm`
- [ ] Rotas
- [ ] Testes

---

### Sprint 4 — Importação Excel (1 semana)

- [ ] Instalar `maatwebsite/excel`
- [ ] Service `ExcelImportService`
- [ ] Componente `OpportunityImport`
- [ ] Template de importação
- [ ] Pré-visualização
- [ ] Detecção de duplicados
- [ ] Relatório de importação
- [ ] Testes

---

### Sprint 5 — Agenda Comercial (1 semana)

- [ ] Migration `activities`
- [ ] Model `Activity`
- [ ] Factory `ActivityFactory`
- [ ] Service `ActivityService`
- [ ] Componente `ActivityList`
- [ ] Componente `ActivityCalendar`
- [ ] Componente `ActivityForm`
- [ ] Views copiadas de `crm-prototipo/apps-calendar.html`, `apps-tasks.html`, `apps-notes.html`
- [ ] Rotas
- [ ] Testes

---

### Sprint 6 — Conciliações (1 semana)

*(Aguarda definição do cliente)*

- [ ] Migration `conciliactions`
- [ ] Model `Conciliaction`
- [ ] Componente `ConciliactionList`
- [ ] Componente `ConciliactionForm`
- [ ] View adaptada de `crm-prototipo/payment.html`
- [ ] Rotas
- [ ] Testes

---

### Sprint 7 — Contratos (1 semana)

*(Aguarda definição do cliente)*

- [ ] Migration `contracts`
- [ ] Model `Contract`
- [ ] Componente `ContractList`
- [ ] Componente `ContractForm`
- [ ] Componente `ContractView`
- [ ] Views adaptadas de `crm-prototipo/projects.html`, `projects-create.html`, `projects-view.html`
- [ ] Rotas
- [ ] Testes

---

### Sprint 8 — Produtos (1 semana)

- [ ] Migration `products`
- [ ] Model `Product`
- [ ] Factory `ProductFactory`
- [ ] Seeder `ProductSeeder`
- [ ] Componente `ProductList`
- [ ] Componente `ProductForm`
- [ ] View nova (criar com estilo do protótipo)
- [ ] Rotas
- [ ] Testes

---

### Sprint 9 — Alertas (1 semana)

- [ ] Migration `alerts`
- [ ] Model `Alert`
- [ ] Service `AlertService`
- [ ] Command `alerts:generate`
- [ ] Scheduler
- [ ] Notification `AlertNotification`
- [ ] Componente `AlertList`
- [ ] Componente `AlertBadge`
- [ ] View copiada de `crm-prototipo/alerts.html`
- [ ] Rotas
- [ ] Testes

---

### Sprint 10 — Dashboard + Painel Executivo (1 semana)

- [ ] Service `DashboardService`
- [ ] Componente `Dashboard`
- [ ] Componente `Analytics`
- [ ] Configurar Chart.js
- [ ] Views copiadas de `crm-prototipo/index.html` e `analytics.html`
- [ ] Actualizar KPIs
- [ ] Rotas
- [ ] Testes

---

### Sprint 11 — Testes e Lançamento (1-1,5 semanas)

- [ ] Testes com Adailson
- [ ] Correcções de bugs
- [ ] Ajustes de UX
- [ ] Configurar VPS
- [ ] Deploy
- [ ] SSL
- [ ] Backups
- [ ] Formação
- [ ] Documentação

---

## 17. COMANDOS ÚTEIS

```bash
# Desenvolvimento
php artisan serve
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

# Alertas
php artisan alerts:generate
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

## 19. PONTOS EM ABERTO

Aguarda resposta do cliente:

- Lista completa e ordem das fases do pipeline
- Se "Aprovado/Não Aprovado" são fases ou estados finais
- Se uma oportunidade pode voltar atrás de fase
- Definição do módulo Conciliações
- Definição do módulo Contratos
- Relação dos Produtos com oportunidades e contratos
- Número exacto de dias para lead inativo
- Comportamento detalhado dos alertas de contratos
- Automação restante

**Enquanto estes pontos não estiverem fechados, não avançar para os módulos correspondentes.**

---

## 20. COMUNICAÇÃO APÓS CADA SPRINT

Após cada sprint, resumir:

```
## Sprint X — [Nome] concluído

### Ficheiros criados
- ...

### Ficheiros modificados
- ...

### Testes
- X testes, Y asserções, todos a passar

### Comparação com o protótipo
- Visual: igual / diferente (explicar)
- Estrutura: igual / diferente (explicar)

### Como testar
1. ...
2. ...

### Próximo sprint
Sprint X+1 — [Nome]

Posso avançar?
```

---

## 21. COMECE AQUI

**Passo 1:** Confirmar Laravel Herd instalado
**Passo 2:** Executar Sprint 0 (Preparação) — criar projecto `adg`
**Passo 3:** Validar Sprint 0
**Passo 4:** Avançar para Sprint 1

**Aguarda confirmação do utilizador para iniciar o Sprint 0.**

---

## 22. NOTAS FINAIS PARA A IA

### O que este projecto NÃO é

- Não é um exercício de design
- Não é uma oportunidade de "melhorar" o protótipo
- Não é um projecto de demonstração de Tailwind
- Não é uma reconstrução do zero

### O que este projecto É

- Uma migração do protótipo HTML para Laravel
- Uma preservação do visual aprovado
- Uma adição de backend funcional
- Uma adição de novas funcionalidades

### A regra de ouro

> Se tiveres dúvida sobre como algo deve parecer, **abre o HTML do protótipo e copia**. Não inventes. Não assumes. Não "melhores". Copia.

### Sobre Bootstrap vs Tailwind

> O protótipo usa **Bootstrap como framework principal** e Tailwind pontualmente. 
> **Mantém o Bootstrap.** Não convertas para Tailwind só por converter. 
> O visual foi aprovado em Bootstrap — não vamos mudá-lo.

---

**Documento:** PROMPT_MASTER.md  
**Versão:** 3.0  
**Data:** Outubro de 2026  
**Projecto:** adg  
**Próxima revisão:** Após Sprint 0

crie memoria.md e documenta toda implementação realizada para melhor acompanhemento e não perda de contexto

