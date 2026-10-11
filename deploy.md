# PROMPT DE AUDITORIA PRÉ-DEPLOY
## Verificação de Requisitos para Produção

**Objectivo:** Avaliar se o sistema Lead Inteligente está pronto para ser colocado em produção no VPS.

**Instruções para a IA:** Responde a cada secção com base no estado actual do projecto. Sê rigoroso — não assumas nada que não esteja comprovado. Se algo estiver incompleto, identifica-o claramente.

---

## 1. CONTEXTO DO PROJECTO

- **Sistema:** Lead Inteligente (CS & AN)
- **Stack:** Laravel 12 + Livewire 3 + Bootstrap 5.3 + Tailwind 4 + MySQL 8
- **Utilizador:** Adailson Nogueira (utilizador único)
- **Fase actual:** Pré-deploy para VPS CloudPanel
- **Memória do projecto:** `MEMORIA_DO_PROJECTO.md`
- **Plano de sprints:** `plano.md`

---

## 2. AUDITORIA — FUNCIONALIDADES IMPLEMENTADAS

Para cada módulo, responde com:

- **Estado:** Completo / Parcial / Em falta
- **Testes:** Quantos testes cobrem o módulo
- **Observações:** O que está pendente, se algo

### 2.1 Módulos

| Módulo | Estado | Testes | Observações |
|---|---|---|---|
| Autenticação (login, logout, perfil) | ? | ? | ? |
| Dashboard (KPIs, gráficos) | ? | ? | ? |
| Pipeline (9 fases, drag-and-drop) | ? | ? | ? |
| Nova Oportunidade (manual) | ? | ? | ? |
| Importação Excel | ? | ? | ? |
| Detalhe da Oportunidade (tabs) | ? | ? | ? |
| Relatório de Visita | ? | ? | ? |
| Aba Actividade (modo Agenda) | ? | ? | ? |
| Aba Nota (com anexos) | ? | ? | ? |
| Aba Proposta (9 campos) | ? | ? | ? |
| Agenda Comercial (lista + calendário) | ? | ? | ? |
| Lembrete diário às 20h | ? | ? | ? |
| Clientes (tabela de acompanhamento) | ? | ? | ? |
| Conciliações | ? | ? | ? |
| Contratos | ? | ? | ? |
| Produtos | ? | ? | ? |
| Comerciais (KAMs) | ? | ? | ? |
| Alertas (6 tipos) | ? | ? | ? |
| Painel Executivo | ? | ? | ? |
| Ícones alinhados (Bloco 6) | ? | ? | ? |

### 2.2 Requisitos do cliente (da reunião)

| # | Pedido | Implementado? |
|---|---|---|
| 1 | Dashboard: gráfico com dados do pipeline | ? |
| 2 | Dashboard: 4 mini-cards do pipeline | ? |
| 3 | Dashboard: "Produtos Activos" em vez de "Encomendas" | ? |
| 4 | Aba Proposta com 9 campos | ? |
| 5 | Aba Actividade modo cópia da Agenda | ? |
| 6 | Aba Nota com nova estrutura | ? |
| 7 | Agenda: tipo "Outros" | ? |
| 8 | Conciliações: ícones alinhados | ? |
| 9 | Contratos: ícones alinhados | ? |
| 10 | Produtos: ícones alinhados | ? |
| 11 | PWA (app no telemóvel) | ? |

---

## 3. AUDITORIA — QUALIDADE TÉCNICA

### 3.1 Testes

| Métrica | Valor |
|---|---|
| Total de testes | **174** |
| Total de assertions | **899** |
| Todos a passar? | **Sim** (verificado 11/10/2026) |
| Cobertura estimada | Alta — 15 módulos + auth cobertos |
| Módulos sem testes | Nenhum módulo funcional sem testes. Só falta teste unitário directo para o model `Setting` (exercitado indirectamente por `AlertService` e `AgendaReminder`, ambos testados) e para a PWA (validada por E2E em Chrome, não por PHPUnit) |

**Mapa de cobertura:**

| Módulo | Ficheiro de teste |
|---|---|
| Login / Auth (5 fluxos) | `Auth/AuthenticationTest`, `EmailVerificationTest`, `PasswordConfirmationTest`, `PasswordResetTest`, `PasswordUpdateTest` |
| Dashboard | `DashboardTest` |
| Pipeline | `PipelineTest` |
| Nova oportunidade | `OpportunityCreateTest` |
| Detalhe da opp (6 abas) | `OpportunityViewTest` |
| Importação Excel | `OpportunityImportTest` |
| Agenda + lembrete | `AgendaTest` |
| Clientes | `ClientTableTest` |
| Conciliações | `ConciliactionsTest` |
| Contratos + expiração | `ContractsTest` |
| Produtos | `ProductsTest` |
| Comerciais | `CommercialsTest` |
| Alertas + job | `AlertsTest` |
| Notas + anexos | `NoteTest` |
| Perfil | `ProfileTest` |
| UI (modal global, etc.) | `UiComponentsTest` |

### 3.2 Formatação e build

| Item | Estado |
|---|---|
| Laravel Pint | ✅ passa sem correcções |
| Build Vite (npm run build) | ✅ sem erros |
| Erros de consola no browser | ✅ nenhum (verificado em E2E) |
| Testes E2E (Chrome) | ✅ 5 fluxos: dashboard cards, pipeline, proposta, notas, login novo, PWA |

### 3.3 Código

| Item | Estado |
|---|---|
| Código morto | ? |
| Rotas quebradas | ? |
| Views quebradas | ? |
| Componentes Livewire não utilizados | ? |
| Ficheiros órfãos | ? |

---

## 4. AUDITORIA — PRODUÇÃO

### 4.1 Configuração do Laravel

| Item | Configurado? | Valor |
|---|---|---|
| APP_ENV | ? | production |
| APP_DEBUG | ? | false |
| APP_URL | ? | https://csangroup.tech |
| APP_KEY | ? | gerada |
| LOG_CHANNEL | ? | stack / daily |
| SESSION_DRIVER | ? | database |
| SESSION_SECURE_COOKIE | ? | true |
| QUEUE_CONNECTION | ? | database |
| CACHE_STORE | ? | database |

### 4.2 Base de dados

| Item | Estado |
|---|---|
| Base de dados criada no CloudPanel | ? |
| Utilizador MySQL criado | ? |
| Migrations correm sem erros | ? |
| Seeders prontos para produção | ? |
| Dados de demonstração identificados para limpeza | ? |

### 4.3 Scheduler e Queue

| Item | Estado |
|---|---|
| Cron configurado no servidor | ? |
| `alerts:generate` agendado | ? |
| `agenda:reminder` agendado (20h) | ? |
| `contracts:expire` agendado | ? |
| Queue worker configurado | ? |
| Supervisor configurado | ? |

### 4.4 Email

| Item | Estado |
|---|---|
| SMTP configurado | ? |
| Email de destino definido | ? |
| Templates de email testados | ? |
| Notificações de alerta a funcionar | ? |

### 4.5 Backups

| Item | Estado |
|---|---|
| spatie/laravel-backup instalado | ? |
| Backup diário agendado | ? |
| Backup mensal agendado | ? |
| Destino externo configurado | ? |
| Teste de restauro feito | ? |

### 4.6 SSL

| Item | Estado |
|---|---|
| Let's Encrypt configurado | ? |
| Redirect HTTP → HTTPS | ? |
| `APP_URL` com https | ? |

### 4.7 Segurança

| Item | Estado |
|---|---|
| Firewall (UFW) configurado | ? |
| Rate limiting no login | ? |
| Password forte | ? |
| Registo fechado (utilizador único) | ? |
| 2FA (opcional) | ? |
| Registo de auditoria | ? |

### 4.8 Performance

| Item | Estado |
|---|---|
| Cache de configuração (`config:cache`) | ? |
| Cache de rotas (`route:cache`) | ? |
| Cache de views (`view:cache`) | ? |
| Cache de ícones (`icon:cache`) | ? |
| Otimização do Composer (`--optimize-autoloader`) | ? |
| Assets compilados (não em modo dev) | ? |

### 4.9 Monitorização

| Item | Estado |
|---|---|
| Sentry configurado | ? |
| Laravel Horizon (se aplicável) | ? |
| UptimeRobot | ? |
| Logs a serem escritos | ? |

---

## 5. AUDITORIA — REQUISITOS DO CLIENTE

### 5.1 Funcionalidades essenciais (do briefing original)

| # | Requisito | Estado |
|---|---|---|
| 1 | Cadastro de oportunidades (manual + Excel) | ? |
| 2 | Pipeline com 9 fases | ? |
| 3 | Detalhe da oportunidade | ? |
| 4 | Relatório de visita | ? |
| 5 | Agenda comercial | ? |
| 6 | Lembrete diário às 20h | ? |
| 7 | Conciliações | ? |
| 8 | Contratos | ? |
| 9 | Produtos | ? |
| 10 | Comerciais (KAMs) | ? |
| 11 | Clientes (tabela) | ? |
| 12 | Alertas automáticos | ? |
| 13 | Dashboard | ? |
| 14 | Painel executivo | ? |
| 15 | PWA | ? |

### 5.2 Requisitos de automação

| # | Automação | Estado |
|---|---|---|
| 1 | Alertas de oportunidade parada (7 dias) | ? |
| 2 | Alertas de lead parado (7 dias) | ? |
| 3 | Alertas de cliente sem contacto (60 dias) | ? |
| 4 | Alertas de contrato a expirar (90 dias) | ? |
| 5 | Sem movimentação automática de leads | ? |
| 6 | Lembrete diário de agenda | ? |
| 7 | Expiração automática de contratos | ? |

---

## 6. AUDITORIA — PONTOS EM ABERTO

Lista de tudo o que **ainda não está fechado** com o cliente e que pode afectar o deploy:

| # | Ponto em aberto | Impacto no deploy |
|---|---|---|
| 1 | Definição do módulo Conciliações (campos) | ? |
| 2 | Definição do módulo Contratos (campos) | ? |
| 3 | Relação dos Produtos com oportunidades | ? |
| 4 | Número exacto de dias para lead inativo | ? |
| 5 | Comportamento detalhado dos alertas | ? |
| 6 | Estrutura da aba Nota (confirmada?) | ? |
| 7 | Estrutura da aba Proposta (confirmada?) | ? |
| 8 | Ícones alinhados (confirmados?) | ? |
| 9 | Cliente já validou os ajustes? | ? |

---

## 7. VEREDICTO

Com base na auditoria acima, responde:

### 7.1 O sistema está pronto para produção?

- [ ] **Sim** — todos os requisitos estão implementados, testados e configurados
- [ ] **Sim, com ressalvas** — a maioria está pronta, mas há pontos que precisam de ser resolvidos antes
- [ ] **Não** — ainda há funcionalidades críticas em falta ou bugs a corrigir
- [ ] **Não, precisa de mais testes** — funcionalidades implementadas mas não testadas com o cliente

### 7.2 Quais são os bloqueadores críticos?

Lista os pontos que **impedem** o deploy:

1. ?
2. ?
3. ?

### 7.3 Quais são as recomendações antes do deploy?

Lista o que **deve ser feito** antes de enviar para o VPS:

1. ?
2. ?
3. ?

### 7.4 Estimativa

Se estiver pronto, quanto tempo para o deploy completo?
- Configuração do servidor: ? horas
- Deploy do código: ? horas
- Testes em produção: ? horas
- **Total:** ? horas

---

## 8. RESUMO EXECUTIVO

| Categoria | Estado | Nota (0-10) |
|---|---|---|
| Funcionalidades | ? | ? |
| Testes | ? | ? |
| Qualidade de código | ? | ? |
| Configuração de produção | ? | ? |
| Segurança | ? | ? |
| Performance | ? | ? |
| Requisitos do cliente | ? | ? |
| **Média final** | ? | ? |

---

## 9. PRÓXIMOS PASSOS

Com base no veredicto, define:

1. **Se pronto:** comando exactos para o deploy
2. **Se quase pronto:** lista de tarefas a completar
3. **Se não pronto:** o que falta e estimativa

---

## 10. INSTRUÇÕES FINAIS PARA A IA

- Sê honesto — não digas que está pronto se não estiver
- Baseia-te apenas no que está documentado e testado
- Se não souberes algo, escreve "não consigo avaliar" em vez de assumir
- Distingue entre "implementado" e "testado"
- Distingue entre "funciona localmente" e "pronto para produção"

**Responde ao questionário completo e dá o veredicto final.**