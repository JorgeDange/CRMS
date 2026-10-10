# CRM ADG

Protótipo de CRM desenvolvido para **Adailson Nogueira** — gestão de clientes,
oportunidades, agenda comercial, conciliações, contratos, produtos e alertas.

O projecto nasceu de um protótipo HTML estático (o CRM "Antigo" de Luanda) e foi
construído seguindo a **Regra Zero**: nenhum visual é inventado — tudo é copiado
do protótipo estático incluído neste repositório.

---

## Stack

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 12 (PHP ^8.2) |
| Frontend | Livewire 3 + Volt, Blade |
| Auth | Laravel Breeze (login por username ou email; registo fechado) |
| UI | Bootstrap 5.3 + Tailwind CSS 4 + Alpine.js 3 |
| Gráficos | ApexCharts |
| Extras | maatwebsite/excel (importação), spatie/laravel-backup |
| Base de dados | MySQL (nome `adg`) |
| Build | Vite 7 |

## Estrutura do repositório

```
├── adg/                    # Aplicação Laravel (o CRM)
├── *.html                  # Protótipo estático de referência (visual base)
├── assets/                 # Assets do protótipo (CSS/JS/fonts/imagens)
├── tools/                  # Scripts de extração do CSS/ícones do protótipo
├── memoria.md              # Decisões técnicas, bugs raiz e histórico detalhado
├── plano.md                # Plano de sprints e checklists (inclui deploy)
├── realizado.md            # Relatório do que foi feito
├── teste-manual.md         # Percurso de QA manual no browser
├── PROMPT_MASTER.md        # Especificação original do projecto
├── resposta.md             # Respostas do cliente (ordem do menu, etc.)
└── seguir.md               # Spec da Sprint 13 (Tabela de Clientes)
```

## Requisitos

- PHP 8.2+ (em dev usado PHP 8.4 do Herd)
- Composer
- Node.js 18+ / npm
- MySQL 8

## Instalação

```powershell
cd adg

composer install
copy .env.example .env      # Windows: copy | Unix: cp
php artisan key:generate

# criar a base de dados "adg" no MySQL e depois:
php artisan migrate --seed

npm install
npm run build
```

## Executar

```powershell
cd adg
php artisan serve
```

Abrir http://127.0.0.1:8000 (em dev foi usado a porta **8099**:
`php artisan serve --no-reload --port=8099`).

**Credenciais de desenvolvimento:** `adailson` / `password`

## Testes e qualidade

```powershell
cd adg
php artisan test          # 140 testes / 730 assertions
vendor/bin/pint           # formatação PSR-12 (pint --test para verificar)
```

## Módulos

- **Dashboard** — 4 KPIs + gráficos (facturação, pipeline, segmento, estado)
- **Oportunidades** — pipeline kanban com 9 fases (drag & drop, setas de
  navegação, auto-scroll), nova oportunidade, detalhe com tabs (dados,
  relatório de visita, histórico de fases), importação Excel com validação,
  estatísticas
- **Clientes** — tabela-espelho da folha de cálculo do cliente (código, KAM,
  compras, assinatura, conciliação, contratos) com pesquisa, filtros e ordenação
- **Agenda** — actividades agrupadas por data, filtros, calendário de 6 semanas
  com modal de evento, lembrete diário às 20h
- **Conciliações** — lista com filtros de classificação/estado e CRUD
- **Contratos** — lista, detalhe (resumo/histórico/documentos), edição com
  prefill, expiração automática às 05h30
- **Produtos / Comerciais** — listas com filtros + CRUD
- **Alertas** — filtros tipo/estado, sino com contador, modal de detalhe,
  marcar lido/resolvido, "marcar todas"
- **Painel Executivo** — vista analítica de topo (somente leitura)
- **Perfil** — edição de nome/email e logout

## Agendador (scheduler)

Tarefas definidas em `adg/routes/console.php` (timezone `Africa/Luanda`) — em
produção correr `php artisan schedule:work` ou definir o cron
(`* * * * * php artisan schedule:run`):

| Hora | Comando | Descrição |
|---|---|---|
| 03:00 | `backup:clean` | Remove backups antigos (retenção) |
| 04:00 | `backup:monitor` | Verifica a saúde do último backup |
| 05:30 | `contracts:expire` | Muda o estado de contratos expirados |
| 06:00 | `alerts:generate` | Gera os alertas do dia |
| 20:00 | `agenda:reminder` | Lembrete de actividades do dia seguinte |

> Nota: o `plano.md` prevê ainda `backup:run` às 02h (correr o backup em si) —
> está na checklist de deploy.

## Documentação de acompanhamento

- **`realizado.md`** — relatório do que já está feito (começar por aqui)
- **`memoria.md`** — histórico técnico: bugs raiz, decisões, padrões a manter
- **`plano.md`** — planos de sprint + checklist de deploy para produção
- **`teste-manual.md`** — percurso de validação manual
