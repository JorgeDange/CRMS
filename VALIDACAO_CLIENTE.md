# VALIDAÇÃO DO CLIENTE — Sistema Lead Inteligente (CS & AN)

> Documento preparado para **Adailson Nogueira** revisar e aprovar os ajustes
> feitos após a reunião. Cada bloco indica **o que mudou**, **como testar** e
> **o que confirmar**.
>
> **Acesso (ambiente de demonstração):** utilizador `adailson` · password `password`
> *(password definitiva será definida no servidor de produção)*

---

## Como usar este documento

1. Entre no sistema e percorra os 6 blocos pela ordem.
2. Em cada bloco, faça o teste descrito.
3. Marque no **Formulário de Feedback** (secção final): ✅ Aprovado · ⚠️ Com ajustes · ❌ Reprovado.
4. Escreva observações livres para qualquer ponto que queira mudar.

---

## BLOCO 1 — Ícones de acção alinhados

**O que mudou:** nos menus de acção de **Conciliações**, **Contratos** e **Produtos**, os botões passaram a aparecer **directamente na linha** (em vez de um menu de 3 pontos escondido).

**Acções por tabela:**

| Tela | Acções visíveis na linha |
|---|---|
| Conciliações | Concordar · Discordar · Pendente · Eliminar |
| Contratos | Activar · Cancelar · Renovar · Eliminar |
| Produtos | Activar · Desactivar · Eliminar |

**Como testar:**
1. Abra **Conciliações** → confirme os 4 ícones na linha de cada registo.
2. Abra **Contratos** → confirme os 4 ícones.
3. Abra **Produtos** → confirme os 3 ícones.
4. Clique em **Eliminar** num registo → deve aparecer uma janela de confirmação do sistema (não a do navegador).

**Confirmar:**
- [ ] Os ícones estão visíveis sem precisar de abrir um menu
- [ ] Estão alinhados e do mesmo tamanho
- [ ] A confirmação de eliminação funciona
- [ ] As acções fazem o que dizem (activar/cancelar/renovar/concordar…)

---

## BLOCO 2 — Dashboard com dados reais da pipeline

**O que mudou:**
1. O gráfico principal passou de "Facturação Mensal" para **"Pipeline por Mês"** — conta as oportunidades **em aberto** criadas em cada mês (janeiro a dezembro).
2. Abaixo do gráfico, os 4 cards passaram a ser os do funil comercial:
   **Pipeline Aberto** (valor total em aberto) · **Aprovado (mês)** (facturado no mês) · **Ponderado** (valor × probabilidade) · **Taxa Conversão** (aprovadas ÷ finalizadas).
3. O KPI **"Encomendas em Curso"** passou a **"Produtos Activos"** (quantos produtos estão activos no catálogo).
4. *(Novo)* Os 4 cards sob o gráfico mostram agora as **fases principais** com a quantidade de oportunidades: **Lead**, **Qualificação**, **Proposta/Negociação**, **Cliente Aprovado** — cada um clicável.

**Como testar:**
1. Abra o **Dashboard**.
2. Confirme o título **"Pipeline por Mês"** no gráfico grande (não deve dizer "Facturação Mensal").
3. Confirme os 4 mini-cards: Pipeline Aberto (Kz) · Aprovado (mês) · Ponderado · Taxa Conversão.
4. Confirme o card **"Produtos Activos"** no topo.
5. Confirme os 4 cards de fases com números (ex.: Lead → 6 oportunidades).
6. Clique num card de fase → deve levar à Pipeline (ou aos Clientes, no caso de "Cliente Aprovado").

**Confirmar:**
- [ ] O gráfico mostra o pipeline (contagem), não facturação
- [ ] Os 4 mini-cards são os do funil comercial
- [ ] "Produtos Activos" mostra o nº de produtos
- [ ] Os cards de fases mostram quantidades correctas
- [ ] Clicar num card navega correctamente

---

## BLOCO 3 — Agenda: tipo "Outros"

**O que mudou:** foi criado um novo tipo de actividade chamado **"Outros"**, com um **campo de texto livre** para descrever a actividade quando não se encaixa nos tipos normais (Ligação, Reunião, Visita, etc.).

**Como testar:**
1. Abra **Agenda** → **Nova Actividade**.
2. No campo Tipo, seleccione **"Outros"** → deve aparecer um campo de texto a pedir a descrição.
3. Escreva algo (ex.: "Enviar catálogo actualizado") e guarde.
4. Confirme que a descrição aparece **na lista da agenda** e **no calendário**, no lugar do tipo.

**Confirmar:**
- [ ] O tipo "Outros" existe na lista de tipos
- [ ] O campo de texto aparece só quando "Outros" é escolhido
- [ ] O texto escrito aparece na lista e no calendário
- [ ] Não é possível guardar "Outros" sem escrever o texto

---

## BLOCO 4 — Oportunidade: aba "Actividade"

**O que mudou:** a aba **Actividade** dentro de uma oportunidade passou a mostrar o **histórico de actividades** daquela oportunidade (mesmo formato da Agenda), com:
- Filtro por oportunidade
- Botão **"Nova Actividade"** que já abre com o **cliente pré-seleccionado**

**Como testar:**
1. Abra a **Pipeline** → clique numa oportunidade.
2. Vá à aba **Actividade**.
3. Confirme que lista as actividades dessa oportunidade.
4. Clique em **"Nova Actividade"** → o formulário deve abrir com o cliente já escolhido.
5. Guarde uma actividade → deve aparecer na lista da aba.

**Confirmar:**
- [ ] A aba lista as actividades da oportunidade (não de outras)
- [ ] O botão "Nova Actividade" funciona
- [ ] O cliente vem pré-seleccionado
- [ ] A nova actividade aparece imediatamente na lista

---

## BLOCO 5 — Oportunidade: aba "Nota" (nova)

**O que mudou:** foi criada do zero a aba **Nota** para registar o histórico da oportunidade.

**Funcionalidades:**
- **Tipos de nota:** Informação · Decisão · Lembrete · Reunião
- **Cronológico:** as notas mais recentes no topo
- **Fixar no topo:** notas importantes ficam fixadas
- **Anexos privados:** anexar ficheiros (até 10 MB cada) — visíveis **apenas** para o utilizador do sistema, fora da área pública do site
- **Pesquisa** por texto e **filtro** por tipo

**Como testar:**
1. Abra uma oportunidade → aba **Nota**.
2. Clique em **Nova Nota** → preencha título, conteúdo e tipo → guarde.
3. Crie outra nota com outro tipo → confirme a ordem cronológica (mais recente no topo).
4. **Fixe** a nota mais antiga → deve saltar para o topo.
5. **Anexe** um ficheiro (ex.: PDF) a uma nota → confirme que o download funciona.
6. Use a **pesquisa** e o **filtro por tipo** → devem funcionar.

**Confirmar:**
- [ ] Crio/edito/elimino notas
- [ ] Os 4 tipos existem
- [ ] A ordem cronológica está correcta
- [ ] Fixar/desafixar funciona
- [ ] Anexos fazem upload e download
- [ ] Pesquisa e filtro funcionam

---

## BLOCO 6 — Oportunidade: aba "Proposta" (nova)

**O que mudou:** foi criada a aba **Proposta** com **9 campos** para registar a proposta comercial da oportunidade:

| Campo | Descrição |
|---|---|
| Produto | Produto da proposta (do catálogo) |
| Preço | Preço proposto (Kz) |
| Volume | Volume proposto |
| Unidade | Litros (L) ou Metro cúbico (m³) |
| Período — início | Data de início do fornecimento |
| Período — fim | Data de fim |
| Crédito | Valor do crédito concedido |
| Facturação | Valor de facturação |
| Non-oil | Valor de produtos não óleo |

**Como testar:**
1. Abra uma oportunidade → aba **Proposta**.
2. Preencha os 9 campos e guarde.
3. Saia e volte a abrir a oportunidade → os dados devem permanecer.
4. Teste validações: deixe o Volume vazio (deve exigir), escolha início posterior ao fim (deve recusar), escreva volume negativo (deve recusar).

**Confirmar:**
- [ ] Os 9 campos existem e guardam
- [ ] Os dados persistem após sair
- [ ] Unidade aceita apenas L ou m³
- [ ] Fim do período tem de ser posterior ao início
- [ ] Campos vazios são tratados correctamente

---

## ALTERAÇÕES ADICIONAIS (pós-blocos)

### Novo ecrã de login
- Ecrã dividido: lado esquerdo com o formulário, lado direito com a apresentação do produto (carrossel).
- **Testar:** entrar com `adailson` / `password`; testar o olho de mostrar/ocultar password; testar "Lembrar de mim"; usar uma password errada (deve recusar com mensagem).
- **Confirmar:** visual correcto também no telemóvel.

### Painel executivo (/analytics)
- KPIs e gráficos executivos — **confirmar** que os números batem certo com a pipeline.

---

## CRITÉRIOS DE ACEITAÇÃO GERAL

| # | Critério | Estado |
|---|---|---|
| 1 | Todas as telas abrem sem erros | |
| 2 | Não existem mensagens técnicas de erro em nenhuma acção | |
| 3 | Os números do dashboard batem certo com a pipeline | |
| 4 | Todas as acções confirmadas antes de executar | |
| 5 | O sistema responde bem no telemóvel | |
| 6 | Não há passos confusos no fluxo habitual de trabalho | |

**Regra de aprovação:** o sistema é aprovado quando **todos os 6 blocos** estiverem marcados ✅ ou ⚠️ sem pendências críticas.

---

## FORMULÁRIO DE FEEDBACK

> Preencher e devolver. Marcar com ✅ (aprovado), ⚠️ (com ajustes) ou ❌ (reprovado).

| Bloco | Aprovação | Observações |
|---|---|---|
| 1 — Ícones alinhados | | |
| 2 — Dashboard | | |
| 3 — Agenda "Outros" | | |
| 4 — Aba Actividade | | |
| 5 — Aba Nota | | |
| 6 — Aba Proposta | | |
| Ecrã de login novo | | |
| Painel executivo | | |

### Observações gerais do cliente

```
___________________________________________________________________________

___________________________________________________________________________

___________________________________________________________________________
```

**Nome:** ____________________________  **Data:** ____/____/________

**Assinatura:** ______________________

---

*Documentos técnicos de referência: `fase.md` (pendências), `mudancas.md` (detalhe por bloco), `pronto.md` (produção).*
