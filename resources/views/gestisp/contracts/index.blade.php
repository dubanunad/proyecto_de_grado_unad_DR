@extends('adminlte::page')

@section('title', 'Clientes')

@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{asset('/css/gestisp/styles.css')}}">
@endsection

@section('content_header')
    <div class="card p-3">
        <h2>LISTADO DE CLIENTES</h2>
    </div>
@endsection
@section('content')

    @if(session('success-delete'))
        <div class="alert alert-danger">
            {{ session('success-delete') }}
        </div>
    @endif

    <!-- Formulario para filtrar -->
    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('contracts.index') }}">
                <div class="row align-items-center">
                    <!-- Select para el campo a buscar -->
                    <div class="col-md-3">
                        <select id="filterField" class="form-control" name="filter_field">
                            <option value="client.identity_number" {{ request('filter_field') == 'client.identity_number' ? 'selected' : '' }}>Número de identidad</option>
                            <option value="contract.id" {{ request('filter_field') == 'contract.id' ? 'selected' : '' }}>Número de contrato</option>
                            <option value="client.type_client" {{ request('filter_field') == 'client.type_client' ? 'selected' : '' }}>Tipo de cliente</option>
                            <option value="client.name" {{ request('filter_field') == 'client.name' ? 'selected' : '' }}>Nombre</option>
                            <option value="client.last_name" {{ request('filter_field') == 'client.last_name' ? 'selected' : '' }}>Apellido</option>
                            <option value="client.number_phone" {{ request('filter_field') == 'client.number_phone' ? 'selected' : '' }}>Teléfono</option>
                            <option value="client.email" {{ request('filter_field') == 'client.email' ? 'selected' : '' }}>Correo electrónico</option>
                            <option value="contract.address" {{ request('filter_field') == 'contract.address' ? 'selected' : '' }}>Dirección</option>
                            <option value="contract.cpe_sn" {{ request('filter_field') == 'contract.cpe_sn' ? 'selected' : '' }}>CPE SN</option>
                            <option value="contract.user_pppoe" {{ request('filter_field') == 'contract.user_pppoe' ? 'selected' : '' }}>Usuario PPPoE</option>
                            <option value="contract.status" {{ request('filter_field') == 'contract.status' ? 'selected' : '' }}>Estado</option>
                            <option value="contract.social_stratum" {{ request('filter_field') == 'contract.social_stratum' ? 'selected' : '' }}>Estrato social</option>
                            <option value="contract.activation_date" {{ request('filter_field') == 'contract.activation_date' ? 'selected' : '' }}>Fecha de activación</option>
                            <option value="plan.name" {{ request('filter_field') == 'plan.name' ? 'selected' : '' }}>Plan de servicio</option>
                        </select>
                    </div>

                    <!-- Input dinámico -->
                    <div class="col-md-3 mt-1 mb-1">
                        <input
                            type="text"
                            id="filterInput"
                            name="filter_value"
                            class="form-control"
                            placeholder="Ingrese un valor"
                            value="{{ request('filter_value') }}">
                    </div>

                    <!--Formulario de paginación-->
                    <div class="col-md-2 mt-1 mb-1">
                        <form method="GET" action="{{ route('contracts.index') }}" class="form-inline">
                            <select name="per_page" id="per_page" class="form-control mr-2" onchange="this.form.submit()">
                                <option value="">Resultados por página</option>
                                <option value="8" {{ request('per_page') == 8 ? 'selected' : '' }}>8</option>
                                <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="25" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </form>
                    </div>


                    <!-- Botones -->
                    <div class="col-md-4 text-center text-md-right">
                        <button type="submit" class="btn btn-primary" title="Aplicar filtro">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('contracts.index') }}" class="btn btn-secondary" title="Limpiar filtros">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#columnModal" title="Configurar columnas">
                            <i class="fas fa-check-square"></i>
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="columnModal" tabindex="-1" role="dialog" aria-labelledby="columnModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Seleccionar columnas</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_contract" checked>
                                            <label class="form-check-label" for="col_contract">Número de contrato</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_document" checked>
                                            <label class="form-check-label" for="col_document">Número de documento</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_name" checked>
                                            <label class="form-check-label" for="col_name">Nombre</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_lastname" checked>
                                            <label class="form-check-label" for="col_lastname">Apellido</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_phone" checked>
                                            <label class="form-check-label" for="col_phone">Teléfono</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_email" checked>
                                            <label class="form-check-label" for="col_email">Correo electrónico</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_address" checked>
                                            <label class="form-check-label" for="col_address">Dirección</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_pppoe" checked>
                                            <label class="form-check-label" for="col_pppoe">Usuario PPPoE</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_status" checked>
                                            <label class="form-check-label" for="col_status">Estado</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_activation" checked>
                                            <label class="form-check-label" for="col_activation">Fecha de activación</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_plan" checked>
                                            <label class="form-check-label" for="col_plan">Plan</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="saveColumns">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Hasta aquí Modal -->

                        <a href="{{ route('contracts.export') }}" class="btn btn-success" title="Exportar contratos a Excel">
                            <i class="fas fa-file-excel"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <!-- Tabla para listar los clientes -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Número de contrato</th>
                    <th>Número de documento</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Teléfono</th>
                    <th>Correo electrónico</th>
                    <th>Dirección</th>
                    <th>Usuario PPPoE</th>
                    <th>Estado</th>
                    <th>Fecha de activación</th>
                    <th>Plan</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($contracts as $contract)
                    <tr>
                        <td>{{ $contract->id }}</td>
                        <td>{{ $contract->client->identity_number }}</td>
                        <td>{{ $contract->client->name }}</td>
                        <td>{{ $contract->client->last_name }}</td>
                        <td>{{ $contract->client->number_phone }}</td>
                        <td>{{ $contract->client->email }}</td>
                        <td>{{ $contract->address }}</td>
                        <td>{{ $contract->user_pppoe }}</td>
                        <td>{{ $contract->status }}</td>
                        <td>{{ $contract->activation_date ? $contract->activation_date : 'N/A' }}</td>
                        <td>{{ $contract->plan->name }}</td>
                        <td>
                            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-info btn-sm" title="Ver"><i class="fas fa-eye"></i></a>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>


        </div>
    </div>
    <div class="text-center mt-3">
        {{ $contracts->links() }}
    </div>
@endsection

@section('js')
    <script>
        document.getElementById('saveColumns').addEventListener('click', () => {
            const checkboxes = document.querySelectorAll('.form-check-input'); // Todos los checkboxes
            const tableHeaders = document.querySelectorAll('.table thead th'); // Cabeceras de la tabla
            const tableRows = document.querySelectorAll('.table tbody tr'); // Filas de la tabla

            checkboxes.forEach((checkbox, index) => {
                const displayValue = checkbox.checked ? '' : 'none'; // Mostrar u ocultar columna

                // Ocultar o mostrar la cabecera
                tableHeaders[index].style.display = displayValue;

                // Ocultar o mostrar las celdas en cada fila
                tableRows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells[index]) {
                        cells[index].style.display = displayValue;
                    }
                });
            });

            // Cerrar el modal
            $('#columnModal').modal('hide');
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterField = document.getElementById('filterField');
            const filterInput = document.getElementById('filterInput');

            filterField.addEventListener('change', () => {
                const selectedField = filterField.value;

                // Cambia el placeholder o tipo del input según el campo seleccionado
                switch (selectedField) {
                    case 'client.identity_number':
                        filterInput.placeholder = 'Número de identidad';
                        filterInput.type = 'number';
                        break;
                    case 'client.type_client':
                        filterInput.placeholder = 'Tipo de cliente';
                        filterInput.type = 'text';
                        break;
                    case 'client.name':
                        filterInput.placeholder = 'Nombre del cliente';
                        filterInput.type = 'text';
                        break;
                    case 'client.last_name':
                        filterInput.placeholder = 'Apellido del cliente';
                        filterInput.type = 'text';
                        break;
                    case 'client.number_phone':
                        filterInput.placeholder = 'Teléfono del cliente';
                        filterInput.type = 'tel'; // Teléfono puede usar el tipo "tel"
                        break;
                    case 'client.email':
                        filterInput.placeholder = 'Correo electrónico';
                        filterInput.type = 'email'; // Tipo "email" para validación
                        break;
                    case 'contract.id':
                        filterInput.placeholder = 'Número de contrato';
                        filterInput.type = 'text';
                        break;
                    case 'contract.address':
                        filterInput.placeholder = 'Dirección';
                        filterInput.type = 'text';
                        break;
                    case 'contract.cpe_sn':
                        filterInput.placeholder = 'CPE SN';
                        filterInput.type = 'text';
                        break;
                    case 'contract.user_pppoe':
                        filterInput.placeholder = 'Usuario PPPoE';
                        filterInput.type = 'text';
                        break;
                    case 'contract.status':
                        filterInput.placeholder = 'Estado del contrato';
                        filterInput.type = 'text';
                        break;
                    case 'contract.social_stratum':
                        filterInput.placeholder = 'Estrato social';
                        filterInput.type = 'number';
                        break;
                    case 'contract.activation_date':
                        filterInput.placeholder = 'Fecha de activación';
                        filterInput.type = 'date'; // Tipo "date" para fechas
                        break;
                    case 'plan.name':
                        filterInput.placeholder = 'Nombre del plan';
                        filterInput.type = 'text';
                        break;
                    default:
                        filterInput.placeholder = 'Ingrese un valor';
                        filterInput.type = 'text';
                }
            });
        });
    </script>
@endsection

