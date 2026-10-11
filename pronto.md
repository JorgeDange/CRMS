# PRONTO PARA PRODUÇÃO — Sistema Lead Inteligente (CS & AN)

> Guia completo do que vai para produção e do que a VPS deve ter.
> Data da auditoria: 11/10/2026 · Código auditado: commit `6680cbc`

---

## 1. O QUE É O SISTEMA

**Lead Inteligente** — CRM B2B para gestão comercial de combustíveis (oil & gas), do grupo CS & AN. Utilizador único (Adailson Nogueira, KAM comercial).

### 1.1 Módulos em produção

| Módulo | Rota | Descrição |
|---|---|---|
| Login | `/login` | Auth por username/email, tela split-screen, "lembrar de mim" |
| Dashboard | `/dashboard` | KPIs, gráfico "Pipeline por Mês" (contagem de abertas), 4 mini-cards |
| Pipeline | `/pipeline` | Kanban 9 fases com drag-and-drop, setas de navegação, valores compactos |
| Nova Oportunidade | `/pipeline/nova` | Formulário manual |
| Importação Excel | `/pipeline/importar` | Upload + preview + validação + relatório |
| Detalhe da Opp | `/pipeline/{id}` | 6 abas: Perfil, Proposta, Actividades, Notas, Relatório de Visita |
| Agenda | `/agenda` | Lista + formulário + calendário (6 semanas), tipo "Outros" com texto livre |
| Clientes | `/clientes` | Tabela de acompanhamento (10 colunas, filtros, 20/pág) |
| Conciliações | `/conciliações` | CRUD com estados |
| Contratos | `/contratos` | CRUD + expiração automática |
| Produtos | `/produtos` | CRUD de produtos activos |
| Comerciais | `/comerciais` | Gestão de KAMs |
| Alertas | `/alertas` | 6 tipos automáticos, marcação como lida |
| Painel Executivo | `/analytics` | KPIs + gráficos executivos |
| Perfil | `/profile` | Dados + password |

### 1.2 Automações (agendadas)

| Hora (Luanda) | Comando | O que faz |
|---|---|---|
| 02:30 | `backup:run --only-db` | Backup diário da BD (gzip) |
| 03:00 | `backup:clean` | Aplica retenção de backups |
| 04:00 | `backup:monitor` | Verifica saúde do backup |
| 05:30 | `contracts:expire` | Contratos vencidos → estado Expirado |
| 06:00 | `alerts:generate` | Gera alertas (opp parada 7d, lead parado 7d, cliente 60d, contrato 90d) |
| 20:00 | `agenda:reminder` | Email com as actividades de amanhã |

Alertas e lembretes são enviados por **email (SMTP) + notificação na BD** — a notificação é enfileirada (`ShouldQueue`), logo **exige queue worker**.

---

## 2. STACK TÉCNICA

| Componente | Versão |
|---|---|
| Laravel | 12.69 |
| Livewire | 3.6 (+ Volt) |
| PHP | **≥ 8.2** (desenvolvido em 8.4) |
| MySQL | 8.x |
| Bootstrap | 5.3 |
| Tailwind CSS | 4 (via Vite) |
| Vite / Node | Node 20+ (build dos assets) |
| Composer | 2.x |
| Maatwebsite/Excel | 4 (importação) |
| Spatie/Laravel-Backup | 10.3 |
| ApexCharts | gráficos (via CDN estático no bundle) |
| Servidor de fila | Laravel database queue (sem Redis) |
| Sessões / Cache | database (sem Redis/Memcached) |

**Tamanho real do código:** 44 rotas · 24 migrations · 174 testes (899 assertions, todos verdes) · pint OK · build OK.

---

## 3. REQUISITOS DA VPS

### 3.1 Hardware (mínimo recomendado para 1 utilizador)

| Recurso | Mínimo | Recomendado | Porquê |
|---|---|---|---|
| vCPU | 1 | **2** | PHP-FPM + MySQL + queue worker em paralelo |
| RAM | 2 GB | **4 GB** | MySQL é o maior consumidor; backups em memória |
| Disco | 20 GB | **40 GB SSD** | Backups diários da BD (gzip) + logs + anexos de notas crescem |
| Banda | 1 Mbps | 2 Mbps | Uso interactivo, tráfego irrelevante |

> Uma VPS tipo Contabo VPS 20 / Hostinger KVM 2 / DigitalOcean 4GB cobre folgado.

### 3.2 Software obrigatório

| Software | Versão | Notas |
|---|---|---|
| **Ubuntu Server** | 22.04 LTS ou 24.04 LTS | Recomendado; CloudPanel suporta ambas |
| **PHP** | 8.2+ (recomendo 8.3 ou 8.4) | Via CloudPanel (instala php-fpm + extensões) |
| **MySQL Server** | 8.0+ | Pode ser o do CloudPanel |
| **mysqldump** (cliente) | incluído com MySQL | **Obrigatório** — o Spatie Backup usa `mysqldump` para exportar a BD |
| **Composer** | 2.x | |
| **Node.js** | 20 LTS+ | Só para `npm run build` no deploy (pode buildar local e subir `public/build`) |
| ** Supervisor** | latest | Para o queue worker (`queue:work`) |
| **Cron** | standard | `artisan schedule:run` a cada minuto |
| **Certbot / Let's Encrypt** | via CloudPanel | SSL para `csangroup.tech` |

### 3.3 Extensões PHP obrigatórias

```
bcmath ctype curl dom fileinfo filter gd intl json libxml mbstring
mysqlnd openssl pdo pdo_mysql session tokenizer xml xmlwriter zip
opcache
```

Verificação rápida após instalar:
```bash
php -m | tr '\n' ' '   # confirmar que todas aparecem
```

> `zip` e `gd` são exigidos pelo Maatwebsite/Excel (importação) e por uploads.
> `opcache` deve estar activo em produção (`opcache.enable=1`).

### 3.4 Domínio e DNS

| Item | Valor |
|---|---|
| Domínio | `csangroup.tech` (+ `www.csangroup.tech`) |
| A record | IP público da VPS |
| SSL | Let's Encrypt (CloudPanel cria e renova automaticamente) |
| APP_URL | `https://csangroup.tech` |

---

## 4. CONFIGURAÇÃO NO CLOUDPANEL

1. **Criar Site** → tipo "PHP App", domínio `csangroup.tech`, PHP 8.3/8.4
2. **Criar MySQL DB** → nome `adg`, user, password (guardar)
3. **SSL** → botão "SSL" → Let's Encrypt → activar redirect HTTP→HTTPS
4. **Cron** → adicionar:
   ```
   * * * * * cd /home/csangroup/htdocs/csangroup.tech/csangroup.tech/web/artisan && php artisan schedule:run >> /dev/null 2>&1
   ```
   (ajustar o caminho ao root real do site no CloudPanel — típico: `~/htdocs/<domínio>/web`)
5. **Supervisor** → criar processo:
   ```
   Command: php artisan queue:work --sleep=3 --tries=3 --max-time=3600
   Directory: <root do projecto>
   User: dono do site
   Autostart: ✓ · Auto-restart: ✓
   ```
6. **PHP-FPM** → confirmar `memory_limit = 256M` e `upload_max_filesize = 20M` (anexos de notas máx 10 MB)

---

## 5. DEPLOY — PASSO A PASSO

### 5.1 No VPS (uma vez)

```bash
# Código (git ou upload)
cd ~ && git clone https://github.com/JorgeDange/CRMS.git
cd CRMS/adg

# Dependências
composer install --optimize-autoloader --no-dev
npm ci && npm run build      # ou subir public/build já compilado

# Ambiente
cp .env.production.example .env
php artisan key:generate
# → editar .env: BD, SMTP, ADMIN_PASSWORD, ALERTS_EMAIL

# Base de dados
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder --force

# Optimização
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache   # se aplicável (feathericons)

# Permissões
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5.2 Variáveis do `.env` obrigatórias

Do template `.env.production.example`:

| Variável | Valor |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` (nunca `true` em produção) |
| `APP_URL` | `https://csangroup.tech` |
| `APP_KEY` | gerada pelo `key:generate` |
| `SESSION_SECURE_COOKIE` | `true` |
| `DB_*` | credenciais do CloudPanel |
| `ADMIN_PASSWORD` | password forte do utilizador (o seeder usa-a) |
| `ALERTS_EMAIL` | email real para receber alertas |
| `MAIL_*` | SMTP real (Hostinger/SMTP2GO/Zoho) |
| `LOG_CHANNEL` | `daily` |

### 5.3 Comando único resumido (updates futuros)

```bash
cd <root> && git pull \
  && composer install --optimize-autoloader --no-dev \
  && npm ci && npm run build \
  && php artisan migrate --force \
  && php artisan config:cache && php artisan route:cache && php artisan view:cache \
  && php artisan queue:restart
```

---

## 6. BACKUPS

- **Ferramenta:** Spatie Backup 10 (já configurado)
- **Agendado:** diário às 02h30 (só BD, gzip) + retenção automática
- **Retenção:** 7 dias (todos) · 16 diários · 8 semanais · 6 mensais · 2 anuais
- **Destino local:** `storage/app/private/ADG/` (dentro da VPS)
- **Destino externo:** **opcional e já implementado** — pôr `BACKUP_CLOUD_DISK=s3` no `.env` (+ endpoint/bucket/credenciais) para enviar cópia também para Wasabi, Backblaze B2, Cloudflare R2, MinIO ou S3. Sem a variável, o backup é só local (como sempre).

**⚠️ `mysqldump` tem de estar no PATH.** Se faltar, o comando **não falha rápido — "trava" ~10 minutos** (3 tentativas × 5 min de espera) antes de desistir. Verificar no VPS:
```bash
which mysqldump      # deve responder, ex.: /usr/bin/mysqldump
php artisan backup:run --only-db   # deve terminar em segundos
```

**⚠️ Backup externo recomendado:** um backup só na mesma VPS não protege contra falha do disco ou perda do servidor. Activar `BACKUP_CLOUD_DISK=s3` no lançamento.

---

## 7. SEGURANÇA (aplicado no código)

| Medida | Estado |
|---|---|
| Rate limiting no login (5 tentativas) | ✅ no código |
| Registo fechado (sem rota de registo) | ✅ |
| Sessões em BD com cookie seguro | ✅ (activar `SESSION_SECURE_COOKIE`) |
| APP_DEBUG=false | ✅ (configurar no .env) |
| Anexos de notas em disco **privado** (fora de `/public`) | ✅ |
| Firewall UFW no VPS | 🔲 por configurar: `ufw allow OpenSSH && ufw allow 'Nginx Full' && ufw enable` |
| 2FA | ❌ não implementado (opcional, fase futura) |
| Log de auditoria | ❌ não implementado (opcional, fase futura) |

---

## 8. MONITORIZAÇÃO MÍNIMA

| Item | Accão |
|---|---|
| UptimeRobot (gratuito) | Monitorizar `https://csangroup.tech/login` a cada 5 min |
| Logs | `storage/logs/laravel-daily.log` — verificar semanalmente |
| Backups | `php artisan backup:monitor` (ou alerta do Spatie por email) |
| Fila | Supervisor mostra o worker; se parar, notificações acumulam na tabela `jobs` |

---

## 9. O QUE FALTA / DECISÕES PENDENTES

| # | Item | Impacto | Estado |
|---|---|---|---|
| 1 | **PWA** (app no telemóvel) | Requisito #11 do cliente | ❌ não implementado — decidir com o cliente |
| 2 | Validação do cliente | Blocos 1–6 + novo login | ⏳ aguarda revisão do Adailson |
| 3 | Backup destino externo | Resiliência | 🔲 fase 2 |
| 4 | 2FA / auditoria | Segurança extra | 🔲 fase 2 (utilizador único, risco baixo) |

---

## 10. CHECKLIST FINAL ANTES DE LANÇAR

- [ ] VPS criada (4 GB / 2 vCPU / 40 GB) com Ubuntu 24.04 + CloudPanel
- [ ] PHP 8.3+ com as extensões da §3.3
- [ ] Domínio `csangroup.tech` a apontar para a VPS
- [ ] Site criado no CloudPanel + SSL Let's Encrypt activo
- [ ] MySQL `adg` criado
- [ ] Código clonado + `composer install` + `npm run build`
- [ ] `.env` preenchido (BD, SMTP, ADMIN_PASSWORD, ALERTS_EMAIL) + `key:generate`
- [ ] `php artisan migrate --force` sem erros
- [ ] `ProductionSeeder` corrigido (1 utilizador, sem demo)
- [ ] Caches (`config/route/view`)
- [ ] Cron `schedule:run` activo (confirmar com `schedule:list`)
- [ ] Supervisor com `queue:work` activo
- [ ] Teste manual: login com password forte criada pelo seeder
- [ ] Teste manual: enviar email de teste (criar alerta ou correr `agenda:reminder --dry-run` se existir)
- [ ] Teste manual: `php artisan backup:run --only-db` e confirmar ficheiro em `storage/app/backups`
- [ ] UptimeRobot a monitorizar
- [ ] Ficheiros demo do protótipo **não** commitados (`DatabaseSeeder` com demo nunca correr)

---

## 11. COMANDOS ÚTEIS (pós-deploy)

```bash
php artisan schedule:list                  # ver tarefas agendadas
php artisan queue:work --tries=3           # worker manual (se precisar testar)
php artisan backup:run --only-db           # backup manual
php artisan backup:monitor                 # saúde dos backups
php artisan alerts:generate --dry-run      # simular alertas sem criar
php artisan about                          # diagnóstico da app
tail -f storage/logs/laravel-*.log         # logs em tempo real
```

---

*Documento gerado após auditoria pré-deploy (ver `deploy.md` para o questionário completo).*
