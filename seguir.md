```markdown
# SPRINT 13 — TABELA DE CLIENTES
## Documento Completo para Implementação

**Versão:** 1.0  
**Data:** Outubro de 2026  
**Projecto:** ADG  
**Cliente:** Adailson Nogueira  
**Base:** Imagem da tabela de acompanhamento fornecida pelo cliente

---

## 1. OBJECTIVO

Criar uma vista de **Tabela de Clientes** que mostra todos os clientes (oportunidades aprovadas) numa única tabela com as colunas que o Adailson usa actualmente.

Esta vista é o **espelho digital** da tabela que ele mantém em Excel/sistema legado.

---

## 2. ESTRUTURA DA TABELA

### Colunas a implementar

| # | Coluna | Origem | Tipo |
|---|---|---|---|
| 1 | Nº | Sequencial gerado | int |
| 2 | CÓDIGO | `opportunities.codigo` | string |
| 3 | CLIENTE | `opportunities.nome` | string |
| 4 | KAM | `commercials.nome` | relação |
| 5 | COMPRAS | `opportunities.tem_compras` | SIM/NÃO |
| 6 | ASSINATURA DO DIRECTOR | `opportunities.assinatura_director` | Sim/Não |
| 7 | ENVIADO/ENTREGUE | `opportunities.enviado_entregue` | Sim/Não |
| 8 | CONCILIAÇÃO | `opportunities.conciliacao_estado` | Concordante/Discordante |
| 9 | CONTRATOS | `opportunities.contrato_estado` | SIM/CADUCIDADE/NÃO |

---

## 3. CAMPOS NOVOS EM `opportunities`

### Migration a criar

```
adicionar_colunas_tabela_clientes_em_opportunities
```

### Colunas

| Coluna | Tipo | Nullable | Default | Notas |
|---|---|---|---|---|
| `codigo` | varchar(50) | Sim | null | Código do cliente |
| `tem_compras` | boolean | Não | false | SIM/NÃO |
| `assinatura_director` | boolean | Não | false | Sim/Não |
| `enviado_entregue` | boolean | Não | false | Sim/Não |
| `conciliacao_estado` | varchar(50) | Sim | null | Concordante/Discordante/Pendente |
| `contrato_estado` | varchar(50) | Sim | null | SIM/CADUCIDADE/NÃO |

### Índices

- INDEX (`codigo`)
- INDEX (`tem_compras`)
- INDEX (`contrato_estado`)
- INDEX (`conciliacao_estado`)

---

## 4. ENUMS

### 4.1 Enum `ConciliacaoEstado`

```php
enum ConciliacaoEstado: string {
    case CONCORDANTE = 'concordante';
    case DISCORDANTE = 'discordante';
    case PENDENTE = 'pendente';

    public function label(): string {
        return match($this) {
            self::CONCORDANTE => 'Concordante',
            self::DISCORDANTE => 'Discordante',
            self::PENDENTE => 'Pendente',
        };
    }

    public function badgeClass(): string {
        return match($this) {
            self::CONCORDANTE => 'bg-soft-success',
            self::DISCORDANTE => 'bg-soft-danger',
            self::PENDENTE => 'bg-soft-warning',
        };
    }
}
```

### 4.2 Enum `ContratoEstado`

```php
enum ContratoEstado: string {
    case SIM = 'sim';
    case CADUCIDADE = 'caducidade';
    case NAO = 'nao';

    public function label(): string {
        return match($this) {
            self::SIM => 'SIM',
            self::CADUCIDADE => 'CADUCIDADE',
            self::NAO => 'NÃO',
        };
    }

    public function badgeClass(): string {
        return match($this) {
            self::SIM => 'bg-soft-success',
            self::CADUCIDADE => 'bg-soft-warning',
            self::NAO => 'bg-soft-secondary',
        };
    }
}
```

---

## 5. COMPONENTE LIVEWIRE

### 5.1 `Clients\ClientTable`

**Localização:** `app/Livewire/Clients/ClientTable.php`

**Props:**
- Nenhuma (carrega dados no `mount`)

**Propriedades:**
- `$search` — pesquisa por nome/código
- `$kamFilter` — filtro por KAM
- `$comprasFilter` — filtro SIM/NÃO
- `$conciliacaoFilter` — filtro Concordante/Discordante
- `$contratoFilter` — filtro SIM/CADUCIDADE/NÃO
- `$sortField` — campo de ordenação
- `$sortDirection` — asc/desc
- `$perPage` — registos por página (default 20)

**Métodos:**
- `mount()` — inicializa
- `updatedSearch()` — reaplica filtros
- `updatedKamFilter()` — reaplica filtros
- `resetFilters()` — limpa todos os filtros
- `sortBy($field)` — ordena

**Query base:**
```
Opportunity::where('fase_atual', 'cliente_aprovado')
    ->with(['comercial'])
    ->orderBy(...)
    ->paginate(...)
```

**Nota:** A tabela mostra apenas **clientes aprovados** (fase_atual = `cliente_aprovado`).

---

## 6. VIEW

### 6.1 Localização

`resources/views/livewire/clients/client-table.blade.php`

### 6.2 Base visual

**Copiar literalmente de:** `crm-prototipo/customers.html`

Adaptar:
- Colunas (as 9 colunas do cliente, não as do protótipo)
- Filtros (os específicos desta tabela)
- Botão "Ver" que abre o detalhe da oportunidade

### 6.3 Estrutura

```html
<div>
    <!-- Page Header -->
    <x-ui.page-header title="Clientes" :breadcrumb="['Clientes']">
        <x-slot name="right">
            <!-- Botão Exportar (opcional) -->
        </x-slot>
    </x-ui.page-header>

    <!-- Content Area -->
    <div class="content-area">
        <!-- Header com filtros -->
        <div class="content-area-header sticky-top">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Clientes</h5>
                <div class="d-flex gap-2">
                    <!-- Dropdown Filtros -->
                    <div class="dropdown">
                        <button class="btn btn-light" data-bs-toggle="dropdown">
                            <i class="feather-filter me-1"></i> Filtros
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 280px;">
                            <!-- Grupos de filtros -->
                        </div>
                    </div>
                    <!-- Pesquisa -->
                    <input type="text" class="form-control" placeholder="Pesquisar..." wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="content-area-body">
            <div class="card stretch stretch-full">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>CÓDIGO</th>
                                    <th>CLIENTE</th>
                                    <th>KAM</th>
                                    <th>COMPRAS</th>
                                    <th>ASSINATURA DO DIRECTOR</th>
                                    <th>ENVIADO/ENTREGUE</th>
                                    <th>CONCILIAÇÃO</th>
                                    <th>CONTRATOS</th>
                                    <th class="text-end">ACÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $index => $client)
                                    <tr wire:key="client-{{ $client->id }}">
                                        <td>{{ ($clients->currentPage() - 1) * $clients->perPage() + $index + 1 }}</td>
                                        <td>{{ $client->codigo ?? '—' }}</td>
                                        <td>{{ $client->nome }}</td>
                                        <td>{{ $client->comercial?->nome ?? '—' }}</td>
                                        <td>
                                            @if($client->tem_compras)
                                                <span class="badge bg-soft-success">SIM</span>
                                            @else
                                                <span class="badge bg-soft-secondary">NÃO</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($client->assinatura_director)
                                                <span class="badge bg-soft-success">Sim</span>
                                            @else
                                                <span class="badge bg-soft-secondary">Não</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($client->enviado_entregue)
                                                <span class="badge bg-soft-success">Sim</span>
                                            @else
                                                <span class="badge bg-soft-secondary">Não</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($client->conciliacao_estado)
                                                <span class="badge {{ \App\Enums\ConciliacaoEstado::from($client->conciliacao_estado)->badgeClass() }}">
                                                    {{ \App\Enums\ConciliacaoEstado::from($client->conciliacao_estado)->label() }}
                                                </span>
                                            @else
                                                <span class="badge bg-soft-secondary">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($client->contrato_estado)
                                                <span class="badge {{ \App\Enums\ContratoEstado::from($client->contrato_estado)->badgeClass() }}">
                                                    {{ \App\Enums\ContratoEstado::from($client->contrato_estado)->label() }}
                                                </span>
                                            @else
                                                <span class="badge bg-soft-secondary">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('opportunities.show', $client) }}" wire:navigate class="btn btn-sm btn-light">
                                                <i class="feather-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">
                                            <x-ui.empty-state 
                                                title="Nenhum cliente encontrado"
                                                description="Ainda não existem clientes aprovados no sistema."
                                                icon="feather-users"
                                            />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($clients->hasPages())
                    <div class="card-footer">
                        {{ $clients->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
```

---

## 7. FILTROS

### 7.1 Pesquisa

- Campo de texto que pesquisa em `nome` e `codigo`
- `wire:model.live.debounce.300ms="search"`

### 7.2 KAM

- Dropdown com lista de comerciais activos
- Opção "Todos os KAMs"
- Filtro por `comercial_id`

### 7.3 Compras

- Dropdown: "Todas", "Sim", "Não"
- Filtro por `tem_compras`

### 7.4 Conciliação

- Dropdown: "Todas", "Concordante", "Discordante", "Pendente"
- Filtro por `conciliacao_estado`

### 7.5 Contratos

- Dropdown: "Todos", "SIM", "CADUCIDADE", "NÃO"
- Filtro por `contrato_estado`

---

## 8. ROTAS

```php
// routes/web.php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/clientes', \App\Livewire\Clients\ClientTable::class)
        ->name('clients.index');
});
```

---

## 9. SIDEBAR

Adicionar item **"Clientes"** ao menu lateral, entre "Oportunidades" e "Agenda Comercial".

**Ícone:** `feather-users` (o mesmo usado no protótipo para Clientes)

**Estado:** activo quando a rota é `clients.*`

---

## 10. SEEDER

### 10.1 Actualizar `OpportunitySeeder`

Adicionar aos clientes existentes:
- Alguns com `codigo` preenchido (formato "CLI001", "CLI002")
- Alguns com `tem_compras = true`
- Alguns com `assinatura_director = true`
- Alguns com `enviado_entregue = true`
- Alguns com `conciliacao_estado` variado
- Alguns com `contrato_estado` variado

### 10.2 Contas esperadas

Dos clientes aprovados existentes no seeder:
- 60% com compras
- 40% com assinatura do director
- 50% com enviado/entregue
- 30% concordante, 20% discordante, 50% pendente
- 40% SIM, 20% CADUCIDADE, 40% NÃO

---

## 11. TESTES

### 11.1 `ClientTableTest`

**Localização:** `tests/Feature/ClientTableTest.php`

**Casos a testar:**

1. **Autenticação:**
   - Utilizador não autenticado é redireccionado para login

2. **Estrutura:**
   - Página carrega sem erros
   - Título "Clientes" presente
   - Colunas esperadas presentes (Nº, CÓDIGO, CLIENTE, KAM, COMPRAS, etc.)
   - Não tem `content-sidebar`

3. **Dados:**
   - Apenas clientes aprovados aparecem
   - Oportunidades em outras fases não aparecem
   - Colunas mostram valores correctos (SIM/NÃO, badges)

4. **Filtros:**
   - Pesquisa por nome funciona
   - Pesquisa por código funciona
   - Filtro por KAM funciona
   - Filtro por compras funciona
   - Filtro por conciliação funciona
   - Filtro por contrato funciona
   - Reset de filtros funciona

5. **Paginação:**
   - Com mais de 20 registos, paginação aparece
   - Navegação entre páginas funciona

6. **Acções:**
   - Botão "Ver" abre o detalhe da oportunidade

7. **Sidebar:**
   - Item "Clientes" activo quando na rota

8. **Estado vazio:**
   - Quando não há clientes, mensagem apropriada aparece

---

## 12. CHECKLIST DE IMPLEMENTAÇÃO

- [ ] Criar enums `ConciliacaoEstado` e `ContratoEstado`
- [ ] Criar migration com os 6 campos novos em `opportunities`
- [ ] Actualizar Model `Opportunity` com casts e fillable
- [ ] Actualizar Factory `OpportunityFactory` com valores para os novos campos
- [ ] Actualizar Seeder `OpportunitySeeder` com dados variados
- [ ] Criar componente Livewire `Clients\ClientTable`
- [ ] Criar view `livewire/clients/client-table.blade.php` (copiada de `customers.html`)
- [ ] Adicionar rota `/clientes`
- [ ] Adicionar item ao sidebar
- [ ] Criar teste `ClientTableTest`
- [ ] Executar `php artisan test`
- [ ] Executar `./vendor/bin/pint`
- [ ] Executar `npm run build`
- [ ] Validar visual lado a lado com `customers.html`
- [ ] Commit

---

## 13. VALIDAÇÃO VISUAL

Após implementação, validar:

1. **Abrir** `http://adg.test/clientes`
2. **Comparar** com `crm-prototipo/customers.html`
3. **Verificar:**
   - Layout da tabela igual ao protótipo
   - Cores dos badges iguais
   - Header com filtros no mesmo sítio
   - Sidebar sem `content-sidebar` vertical
   - Responsividade OK

**Usar Chrome DevTools:**
- Comparar lado a lado (split view)
- Medir alturas, cores, fontes
- Tirar screenshots

---

## 14. OBSERVAÇÕES

### 14.1 Sobre os campos desnormalizados

Os campos `conciliacao_estado` e `contrato_estado` são **desnormalizados** em `opportunities` para rapidez de leitura da tabela.

**Razão:** a tabela precisa mostrar o estado de cada cliente numa coluna, e fazer joins a cada render seria mais lento.

**Manutenção:** sempre que uma conciliação ou contrato muda de estado, o serviço correspondente actualiza também estes campos em `opportunities`.

**Alternativa futura:** se houver inconsistências, migrar para cálculo em tempo real via relação.

### 14.2 Sobre o código

O campo `codigo` fica **null** por defeito. O Adailson pode preenchê-lo manualmente na ficha do cliente.

**Futuro:** se quiser código automático, cria-se uma regra no `OpportunityService`.

### 14.3 Sobre a fase

A tabela mostra **apenas** `fase_atual = 'cliente_aprovado'`. Se o Adailson quiser ver também `cliente_nao_aprovado`, adiciona-se um filtro.

---

## 15. PRÓXIMOS PASSOS

1. Implementar conforme checklist
2. Validar visualmente
3. Apresentar ao Adailson
4. Recolher feedback
5. Ajustar conforme necessário

---

**Documento:** SPRINT_13_TABELA_CLIENTES.md  
**Versão:** 1.0  
**Data:** Outubro de 2026
```