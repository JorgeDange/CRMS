<x-app-layout>
    <x-slot name="header">Componentes</x-slot>

    {{-- Demonstração dos componentes extraídos do protótipo (Sprint 1.1, PROMPT_MASTER §9). --}}
    {{-- Comparar lado a lado com index.html / leads-create.html / customers.html. --}}

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Botões</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <x-ui.button>Primary</x-ui.button>
                        <x-ui.button variant="light-brand">light-brand</x-ui.button>
                        <x-ui.button variant="danger">danger</x-ui.button>
                        <x-ui.button variant="outline-primary">outline-primary</x-ui.button>
                        <x-ui.button variant="outline-secondary">outline-secondary</x-ui.button>
                        <x-ui.button size="sm">btn-sm</x-ui.button>
                        <x-ui.button size="xs">btn-xs</x-ui.button>
                        <x-ui.button size="lg">btn-lg</x-ui.button>
                        <x-ui.button icon="save">Guardar</x-ui.button>
                        <x-ui.button icon="plus" icon-only variant="light-brand" aria-label="Novo"></x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Badges</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <x-ui.badge color="primary">Distribuição</x-ui.badge>
                        <x-ui.badge color="success">Activo</x-ui.badge>
                        <x-ui.badge color="danger">Perdido</x-ui.badge>
                        <x-ui.badge color="warning">Pendente</x-ui.badge>
                        <x-ui.badge color="info">Info</x-ui.badge>
                        <x-ui.badge color="secondary">secondary</x-ui.badge>
                        <x-ui.badge variant="solid" color="success">solid</x-ui.badge>
                        <x-ui.badge variant="gray">5</x-ui.badge>
                        <x-ui.badge variant="border">N <i class="feather-command ms-1 fs-10"></i></x-ui.badge>
                        <span class="fs-13 fw-normal text-gray-600 mb-0">
                            Recnet <x-ui.badge variant="gray">3</x-ui.badge>
                        </span>
                    </div>
                    <hr>
                    <div class="d-flex gap-3 align-items-center">
                        <span class="fs-13 text-muted">Alert badges (header):</span>
                        <x-ui.alert-badge>2</x-ui.alert-badge>
                        <x-ui.alert-badge variant="danger">3</x-ui.alert-badge>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3 col-md-6">
            <x-ui.stat-card icon="users" value="45/76" title="Clientes Activos" href="#" footer-label="Carteira de clientes" footer-value="45 activos" footer-percent="56%" />
        </div>
        <div class="col-xxl-3 col-md-6">
            <x-ui.stat-card icon="bar-chart-2" value="23/40" title="Oportunidades Abertas" href="#" footer-label="Pipeline" footer-value="Kz 48.500.000" footer-percent="57%" progress="warning" />
        </div>
        <div class="col-xxl-3 col-md-6">
            <x-ui.stat-card icon="dollar-sign" value="12" title="Contratos Activos" href="#" footer-label="Carteira" footer-value="Kz 96.400.000" footer-percent="72%" progress="success" />
        </div>
        <div class="col-xxl-3 col-md-6">
            <x-ui.stat-card icon="bell" value="7" title="Alertas" href="#" footer-label="Resolver hoje" footer-value="3" footer-percent="43%" progress="danger" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title mb-0">Formulários</h5>
                </div>
                <div class="card-body">
                    <form>
                        <x-ui.form-input label="Título" required placeholder="Ex.: Renovação contrato 2026" />
                        <x-ui.form-input label="Valor (Kz)" required type="number" min="0" step="0.01" placeholder="0,00" />
                        <x-ui.form-select label="Cliente" required>
                            <option value="">Seleccionar cliente.</option>
                            <option>SIL - Sociedade Importadora</option>
                            <option>Metalúrgica Kilamba</option>
                        </x-ui.form-select>
                        <x-ui.form-select label="Estado">
                            <option value="identificacao">Identificação</option>
                            <option value="proposta">Proposta</option>
                            <option value="negociacao">Negociação</option>
                        </x-ui.form-select>
                        <x-ui.form-textarea label="Descrição" placeholder="Contexto da oportunidade (opcional)" />
                        <x-ui.form-input label="Campo com erro" required value="" error="Este campo é obrigatório." />
                        <div class="d-flex gap-2">
                            <x-ui.button type="submit" icon="save">Criar Oportunidade</x-ui.button>
                            <x-ui.button variant="light-brand">Cancelar</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title mb-0">Modal</h5>
                </div>
                <div class="card-body">
                    <p class="fs-12 text-muted">Abre com Bootstrap 5 puro (sem jQuery), igual ao protótipo:</p>
                    <x-ui.button data-bs-toggle="modal" data-bs-target="#demoModal">Nova Actividade</x-ui.button>
                </div>
            </div>

            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tabela de dados</h5>
                </div>
                <div class="card-body">
                    <x-ui.data-table id="customerList">
                        <x-slot name="thead">
                            <tr>
                                <th>Nome</th>
                                <th>NIF</th>
                                <th>Segmento</th>
                                <th>Localização</th>
                                <th class="text-end">Acções</th>
                            </tr>
                        </x-slot>
                        <tr class="single-item">
                            <td><a href="#" class="fw-semibold text-truncate-1-line">SIL - Sociedade Importadora</a></td>
                            <td>5417123456</td>
                            <td><x-ui.badge color="primary">Distribuição</x-ui.badge></td>
                            <td>Luanda</td>
                            <td class="text-end"><x-ui.button size="xs" variant="light-brand" icon="edit-2" aria-label="Editar"></x-ui.button></td>
                        </tr>
                        <tr class="single-item">
                            <td><a href="#" class="fw-semibold text-truncate-1-line">Metalúrgica Kilamba</a></td>
                            <td>5417987654</td>
                            <td><x-ui.badge color="info">Indústria</x-ui.badge></td>
                            <td>Benguela</td>
                            <td class="text-end"><x-ui.button size="xs" variant="light-brand" icon="edit-2" aria-label="Editar"></x-ui.button></td>
                        </tr>
                    </x-ui.data-table>
                </div>
            </div>
        </div>
    </div>

    <x-ui.modal id="demoModal" title="Nova Actividade" scrollable>
        <form>
            <x-ui.form-select label="Tipo">
                <option>Visita</option>
                <option>Chamada</option>
                <option>Reunião</option>
                <option>Proposta</option>
                <option>Cobrança</option>
            </x-ui.form-select>
            <x-ui.form-textarea label="Descrição" placeholder="Descrição" />
            <x-ui.form-input label="Próxima acção" required placeholder="Ex.: Enviar proposta revisada" />
            <x-ui.form-input label="Prazo" type="date" required />
        </form>
        <x-slot name="footer">
            <x-ui.button>Guardar</x-ui.button>
            <x-ui.button variant="light-brand" data-bs-dismiss="modal">Cancelar</x-ui.button>
        </x-slot>
    </x-ui.modal>
</x-app-layout>
