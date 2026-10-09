# Teste manual — Sistema ADG (para Adailson)

**Data:** ______________  **Browser:** ______________

**Acesso (servidor de demonstração):** URL ______________  ·  utilizador `adailson` · palavra-passe `password`

**Como reportar:** para cada ponto, assinalar ✅ (correcto), ❌ (errado — descrever o problema) ou ⚠️ (parcial).

---

## 1. Login e navegação geral

- [ ] Abrir o URL → aparece o ecrã de login (fundo com imagem, cartão central)
- [ ] Entrar com `adailson` / `password` → chega ao Dashboard
- [ ] Menu lateral esquerdo: 8 grupos (Dashboard, Oportunidades, Agenda Comercial, Conciliações, Contratos, Produtos, Comerciais, Alertas) abrem/fecham ao clicar
- [ ] Todo o texto está em português correcto (sem "Ganho", "Perdido", "Crédito", "Encomendas")
- [ ] Ícones visíveis em todo o lado (setas, filtros, sino, botões) — nenhum quadrado vazio
- [ ] Sino (topo direito) mostra contador quando há notificações
- [ ] Clicar no sino → dropdown "Notificações" com itens **empilhados na vertical** (um por linha), botão "Marcar todas como lidas" e link "Todas as notificações"

## 2. Dashboard

- [ ] 4 cartões de topo com valores (Facturação, Actividades, Encomendas…)
- [ ] Gráficos visíveis com dados (não vazios, não todos a zero)
- [ ] Secção de pipeline / estado das encomendas com donuts legíveis (% somam 100%)

## 3. Painel Executivo (`/analytics`)

- [ ] Cabeçalho único com badge "Somente leitura"
- [ ] KPIs em formato compacto ("Kz 305,2M") **sem** sair dos cartões
- [ ] 4 gráficos com dados reais (Vendas Mensais, Pipeline, Segmento, Estado das Encomendas)

## 4. Oportunidades

- [ ] **Nova Oportunidade**: formulário completo (nome, empresa, contacto, valor, fase, comercial, produtos…); guardar cria o registo e aparece no Pipeline
- [ ] **Pipeline**: 9 fases em colunas; **arrastar um cartão** de fase → muda de coluna e mantém-se lá após recarregar
- [ ] Cartões mostram valor, % de probabilidade e data de fecho
- [ ] Botões de estatísticas (topo) abrem os 4 cartões de resumo
- [ ] **Detalhe da oportunidade**: tabs (dados, relatório de visita, histórico de fases)
- [ ] **Importar Excel**: descarregar template, submeter → pré-visualização com validação de erros/duplicados

## 5. Agenda Comercial

- [ ] **Actividades**: lista agrupada por data; filtros (Estado/Tipo) funcionam; criar actividades sem erros
- [ ] Marcar como concluída ✔ e eliminar (pede confirmação) funcionam
- [ ] **Calendário**: grelha de 6 semanas, eventos nos dias correctos; **clicar num evento** abre modal com detalhes; Editar/Concluir/Fechar/Eliminar funcionam
- [ ] **Nova Actividade**: validação (descrição e próxima acção obrigatórias), flash "Actividade guardada com sucesso"

## 6. Conciliações

- [ ] Lista sem barra lateral; botão **+ Nova Conciliação** e dropdown **Filtros** (Classificação + Estado) no topo
- [ ] Filtro "Resolvidas" reduz a lista correctamente; coluna Observações alinhada ao centro com as restantes
- [ ] Criar/editar conciliação guarda e volta à lista

## 7. Contratos

- [ ] Lista sem barra lateral; filtros por Estado; botão **+ Novo Contrato**
- [ ] Detalhe do contrato: 3 tabs (resumo, histórico, documentos)
- [ ] Editar contrato mostra os dados preenchidos (prefill)
- [ ] Contratos com data de fecho passada ficam "Expirados" (corre de madrugada — validar apenas se existir algum)

## 8. Produtos, Comerciais, Alertas

- [ ] **Produtos**: filtros Estado + Comercial; criar/editar funciona
- [ ] **Comerciais**: filtros Estado; criar/editar funciona
- [ ] **Alertas**: filtros Tipo (inclui "Agenda") + Estado; marcar lido/resolvido; "Marcar todas" no sino limpa o contador

## 9. Perfil e sessão

- [ ] Dropdown do avatar → Meu perfil → alterar nome/email guarda
- [ ] Terminar sessão volta ao login

## 10. Revisão em ecrã pequeno (portátil 1366px)

- [ ] Nenhuma página tem a tabela cortada à direita (faz scroll interno se precisar)
- [ ] Nenhuma barra lateral branca de 300px a "roubar" espaço às listas

---

## Feedback geral

**O que está bom:**

**O que está errado (prioridade 1):**

**O que falta / queria ver diferente:**
