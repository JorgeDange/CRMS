# PROMPT — RESOLVER PENDÊNCIAS PRÉ-DEPLOY
## Sistema Lead Inteligente (CS & AN)

**Versão:** 1.0  
**Data:** Outubro de 2026  
**Objectivo:** Resolver todas as pendências identificadas na auditoria pré-deploy antes de avançar para produção.

**Instruções para a IA:** Executar cada secção pela ordem definida. Não avançar para a secção seguinte sem completar a anterior. Reportar o resultado de cada passo antes de prosseguir.

---

## CONTEXTO

O sistema **Lead Inteligente** está prestes a ser colocado em produção num VPS CloudPanel. Foi feita uma auditoria pré-deploy (`PRONTO_PARA_PRODUCAO.md`) que identificou 7 pendências a resolver antes do lançamento.

**Stack:** Laravel 12 + Livewire 3 + Bootstrap 5.3 + Tailwind 4 + MySQL 8  
**Repositório:** `JorgeDange/CRMS` (a confirmar)  
**Domínio:** `csangroup.tech`  
**VPS:** Hostinger KVM 2, Ubuntu, CloudPanel

---

## PENDÊNCIA 1 — DECISÃO SOBRE PWA

**Problema:** O cliente pediu a plataforma em modo app (PWA). O sistema ainda não tem PWA implementada.

**Tarefa:**
1. Avaliar o esforço de implementar PWA agora
2. Apresentar duas opções ao utilizador:
   - **Opção A:** Implementar PWA antes do deploy (mais trabalho, mas cumpre o requisito)
   - **Opção B:** Lançar sem PWA e implementar em fase 2 (mais rápido, mas não cumpre o requisito no lançamento)
3. Recomendar uma das opções com base no estado actual

**Perguntas a responder:**
- Quanto tempo demora implementar PWA num sistema Laravel + Livewire?
- Que pacote usar? (`devrabiul/laravel-pwa-kit`, `erag/laravel-pwa`, outro)
- Existem riscos de quebrar funcionalidades existentes?
- O cliente aceita esperar mais uma semana para ter PWA no lançamento?

**Output esperado:** Recomendação clara + estimativa de tempo.

---

## PENDÊNCIA 2 — VALIDAÇÃO DO CLIENTE

**Problema:** Os ajustes dos Blocos 1–6 (pós-reunião) ainda não foram validados pelo Adailson.

**Tarefa:**
1. Listar todos os ajustes feitos nos Blocos 1–6
2. Preparar um documento de validação para o cliente
3. Definir o que precisa de ser testado
4. Definir critérios de aceitação

**Documento a criar:**
`VALIDACAO_CLIENTE.md` com:
- Lista de ajustes por bloco
- Como testar cada um
- O que o cliente deve confirmar
- Formulário de feedback

**Ajustes a validar:**

**Bloco 1 — Ícones alinhados:**
- Conciliações: ícones directos na linha
- Contratos: ícones directos na linha
- Produtos: ícones directos na linha

**Bloco 2 — Dashboard:**
- Gráfico com dados do pipeline
- 4 mini-cards do pipeline
- KPI "Produtos Activos"

**Bloco 3 — Agenda Tipo Outros:**
- Campo de texto aparece quando "Outros" é seleccionado
- Texto personalizado aparece na lista/calendário

**Bloco 4 — Aba Actividade:**
- Modo cópia da Agenda
- Filtro por oportunidade
- Botão "Nova Actividade"

**Bloco 5 — Aba Nota:**
- Estrutura cronológica
- Tipos (Informação, Decisão, Lembrete, Reunião)
- Anexos privados
- Fixar no topo
- Pesquisa e filtro

**Bloco 6 — Aba Proposta:**
- 9 campos (Produto, Preço, Volume, Período, Crédito, Facturação, Non-oil)

**Output esperado:** Documento de validação pronto para enviar ao cliente.

---

## PENDÊNCIA 3 — CONFIRMAR NÚMERO DE TESTES

**Problema:** Existe divergência entre a memória do projecto (140 testes / 730 assertions) e o documento de auditoria (173 testes / 886 assertions).

**Tarefa:**
1. Correr `php artisan test` no ambiente local
2. Registar o número real de testes e assertions
3. Verificar se todos passam
4. Identificar módulos sem testes
5. Actualizar a documentação

**Comando:**
```bash
php artisan test --compact
```

**Output esperado:** números reais documentados e divergência resolvida.

---

## PENDÊNCIA 4 — BACKUP COM DESTINO EXTERNO

**Problema:** O backup diário (`backup:run --only-db`) guarda em `storage/app/backups/`, ou seja, **na mesma VPS**. Uma falha de disco ou perda do servidor significa perda total dos dados e dos backups.

**Tarefa:**
1. Adicionar ao `config/backup.php` um destino **S3-compatível opcional** (Wasabi, Backblaze B2, Cloudflare R2, MinIO), activado só por variável de ambiente
2. Acrescentar o disco S3 em `config/filesystems.php`
3. Documentar as variáveis no `.env.production.example`
4. Garantir que, com a variável desligada, o backup continua a funcionar só em local (regressão)

**Variáveis novas:**
```
BACKUP_CLOUD_DISK=            # vazio = só local; "s3" activa o destino externo
BACKUP_CLOUD_ENDPOINT=        # ex.: s3.wasabisys.com
BACKUP_CLOUD_REGION=          # ex.: us-east-1
BACKUP_CLOUD_BUCKET=
BACKUP_CLOUD_KEY=
BACKUP_CLOUD_SECRET=
```

**Riscos a evitar:** não pode quebrar o backup actual; não pode exigir credenciais para correr em local.

**Output esperado:** backup com destino externo opcional + regressão verde.

---

## PENDÊNCIA 5 — VARIÁVEIS DE PRODUÇÃO

**Problema:** O `.env` de produção (base de dados real, SMTP real, password do admin) ainda não foi criado. O template `.env.production.example` existe mas falta garantir que está **completo** e que bate certo com tudo o que o código lê.

**Tarefa:**
1. Conferir que `.env.production.example` cobre **todas** as variáveis lidas por `env()` no projecto (incluindo as novas da Pendência 4)
2. Listar as variáveis que **exigem** valor real (BD, SMTP, `ADMIN_PASSWORD`)
3. Deixar instruções de verificação pós-preenchimento (`artisan about`, `config:show`)

**Output esperado:** template completo e conferido, com checklist de preenchimento.

---

## PENDÊNCIA 6 — PASSWORD DEFINITIVA DO UTILIZADOR

**Problema:** O utilizador foi criado com a password `password` (desenvolvimento). Em produção isso é inaceitável — e o sistema não tem forma segura de a mudar sem aceder à BD.

**Tarefa:**
1. Criar comando artisan `user:password` que redefine a password do utilizador único (pede password nova, confirmação, valida força mínima)
2. Testar o comando
3. Documentar o uso no VPS

**Regras da password:** mínimo 12 caracteres, com letra maiúscula, minúscula, número e símbolo.

**Output esperado:** comando testado + instrução no `pronto.md`.

---

## PENDÊNCIA 7 — CHECKLIST DE CONFIGURAÇÃO NO VPS

**Problema:** O sistema não arranca em produção sem 4 peças de infraestrutura que só existem no servidor: fila (Supervisor), agendador (cron), SSL e firewall.

**Tarefa:** Garantir que o `pronto.md` contém o passo-a-passo exacto para:
1. **Supervisor** → `php artisan queue:work` (sem ele, alertas/lembretes nunca são enviados)
2. **Cron** → `php artisan schedule:run` a cada minuto (sem ele, nenhum backup/alerta/contrato corre)
3. **SSL** Let's Encrypt + redirect HTTP→HTTPS
4. **Firewall** UFW (só SSH, HTTP, HTTPS)

**Output esperado:** checklist completa no `pronto.md` (secção 4) — verificação incluída no lançamento.

---

## ORDEM DE EXECUÇÃO

1. ~~PENDÊNCIA 1 — PWA~~ ✅ (`7f6d8e2`)
2. ~~PENDÊNCIA 2 — Validação do cliente~~ ✅ (`5013ce0`)
3. ~~PENDÊNCIA 3 — Nº de testes~~ ✅ (`e836113`) — 174/899 confirmados
4. ~~PENDÊNCIA 4 — Backup destino externo~~ ✅ (`f2e6547`) — disco `backup-cloud` + teste
5. ~~PENDÊNCIA 5 — Variáveis de produção~~ ✅ (`d61b8a8`) — template conferido (138 env auditadas)
6. ~~PENDÊNCIA 6 — Password definitiva~~ ✅ (`4678c53`) — comando `user:password` + 8 testes
7. ~~PENDÊNCIA 7 — Checklist VPS~~ ✅ (`71d716b`) — firewall, mysqldump, estados

**TODAS AS PENDÊNCIAS DO `fase.md` CONCLUÍDAS** (11/10/2026). Suíte final: **186 testes / 940 assertions, todos verdes**.