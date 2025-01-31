@extends('adminlte::page')

@section('title', 'Órdenes Técnicas')

@section('content_header')
    <div class="card p-3">
        <h2>ADMINISTRAR ÓRDENES TÉCNICAS</h2>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-head p-3">
            <!-- Formulario para filtrar -->
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="{{ route('technicals_orders.index') }}">
                        <div class="row align-items-center">
                            <!-- Select para el campo a buscar -->
                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Criterio</label>
                                <select id="filterField" class="form-control" name="filter_field">
                                    <option value="status" {{ request('filter_field') == 'status' ? 'selected' : '' }}>Estado</option>
                                    <option value="client_name" {{ request('filter_field') == 'client_name' ? 'selected' : '' }}>Nombre de cliente</option>
                                    <option value="order_number" {{ request('filter_field') == 'order_number' ? 'selected' : '' }}>Número de orden</option>
                                    <option value="contract_number" {{ request('filter_field') == 'contract_number' ? 'selected' : '' }}>Número de contrato</option>
                                    <option value="type" {{ request('filter_field') == 'type' ? 'selected' : '' }}>Tipo de orden</option>
                                    <option value="detail" {{ request('filter_field') == 'detail' ? 'selected' : '' }}>Detalle de orden</option>
                                    <option value="assigned_user" {{ request('filter_field') == 'assigned_user' ? 'selected' : '' }}>Técnico asignado</option>
                                </select>
                            </div>

                            <!-- Input dinámico -->
                            <div class="col-md-2 mt-1 mb-1" id="filterValueContainer">
                                <label for="filterValue" class="form-label">Valor</label>
                                <input
                                    type="text"
                                    id="filterInput"
                                    name="filter_value"
                                    class="form-control"
                                    placeholder="Ingrese un valor"
                                    value="{{ request('filter_value') }}">
                            </div>

                            <!-- Filtros por rango de fechas -->
                            <div class="col-md-1 mt-1 mb-1">
                                <label for="start_date" class="form-label">Fecha Inicial</label>
                                <input
                                    type="date"
                                    id="start_date"
                                    name="start_date"
                                    class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>

                            <div class="col-md-1 mt-1 mb-1">
                                <label for="end_date" class="form-label">Fecha Final</label>
                                <input
                                    type="date"
                                    id="end_date"
                                    name="end_date"
                                    class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>

                            <!--Formulario de paginación-->
                            <div class="col-md-2 mt-1 mb-1">
                                <label for="start_date" class="form-label">Cantidad de resultados</label>
                                <form method="GET" action="{{ route('technicals_orders.index') }}" class="form-inline">
                                    <select name="per_page" id="per_page" class="form-control mr-2" onchange="this.form.submit()">
                                        <option value="">Resultados por página</option>
                                        <option value="8" {{ request('per_page') == 12 ? 'selected' : '' }}>12</option>
                                        <option value="15" {{ request('per_page') == 20 ? 'selected' : '' }}>15</option>
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
                                <a href="{{ route('technicals_orders.index') }}" class="btn btn-secondary" title="Limpiar filtros">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>

                                <a href="{{ route('movements.excel') }}" class="btn btn-success" title="Exportar todos los movimientos a Excel">
                                    <i class="fas fa-file-excel"></i>
                                </a>
                                <a href="{{ route('movements.pdf', [
                            'filter_field' => request('filter_field'),
                            'filter_value' => request('filter_value'),
                            'start_date' => request('start_date'),
                            'end_date' => request('end_date'),
                        ]) }}" class="btn btn-danger" title="Reporte en PDF">
                                    <i class="far fa-file-pdf"></i>
                                </a>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <th>Número de orden</th>
                        <th>Número de contrato</th>
                        <th>Cliente</th>
                        <th>Tipo de orden</th>
                        <th>Detalle</th>
                        <th>Comentario inicial</th>
                        <th>Estado</th>
                        <th>Fecha de creación</th>
                        <th>Técnico asignado</th>
                        <th></th>
                    </tr>
                    @foreach($technical_orders as $technical_order)
                        <tr>
                            <td>{{ $technical_order->id }}</td>
                            <td>{{ $technical_order->contract->id }}</td>
                            <td>{{ $technical_order->contract->client->name }} {{ $technical_order->contract->client->last_name }}</td>
                            <td>{{ $technical_order->type }}</td>
                            <td>{{ $technical_order->detail }}</td>
                            <td>{{ $technical_order->initial_comment }}</td>
                            <td>{{ $technical_order->status }}</td>
                            <td>{{ $technical_order->created_at }}</td>
                            <td>{{ $technical_order->assignedUser->name ?? 'N/A' }} {{ $technical_order->assignedUser->last_name ?? 'N/A' }}</td>
                            <td>
                                @if($technical_order->status === 'Pendiente')
                                    <button class="btn btn-info" data-toggle="modal" data-target="#assignOrderModal{{ $technical_order->id }}">
                                        Asignar Orden
                                    </button>
                                @elseif($technical_order->status === 'Asignada')
                                    <button class="btn btn-warning" data-toggle="modal" data-target="#assignOrderModal{{ $technical_order->id }}">
                                        Reasignar Orden
                                    </button>
                                @endif
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#detailModal{{ $technical_order->id }}">
                                        Ver detalles
                                    </button>

                                    <div class="modal fade" id="detailModal{{ $technical_order->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Detalles de orden {{ $technical_order->id }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="">
                                                        <div class="mt-2">
                                                            <p><strong>Datos del cliente</strong></p>
                                                        </div>
                                                        <div>Cliente: {{ $technical_order->contract->client->name }} {{ $technical_order->contract->client->last_name }}</div>
                                                        <div>Barrio y dirección: {{ $technical_order->contract->neighborhood }} {{ $technical_order->contract->address }}</div>
                                                        <div>Detalles de plan: {{ $technical_order->contract->plan->name }}</div>
                                                        <div class="mt-2">
                                                            <p><strong>Datos de orden</strong></p>
                                                        </div>
                                                        <div>Tipo de orden: {{ $technical_order->type }}</div>
                                                        <div>Detalle: {{ $technical_order->detail }}</div>
                                                        <div>Detalle: {{ $technical_order->detail }}</div>
                                                        <div>Comentario inicial: {{ $technical_order->initial_comment }}</div>
                                                        <div class="mt-2">
                                                            <p><strong>Datos de solución</strong></p>
                                                        </div>
                                                        <div>Observaciones técnicas: {{ $technical_order->observations_technical }}</div>
                                                        <div>Observaciones del cliente: {{ $technical_order->client_observation }}</div>
                                                        <div>Solución: {{ $technical_order->solution }}</div>
                                                        <div>Fecha de creación: {{ $technical_order->created_at }}</div>
                                                        <div>Última acción: {{ $technical_order->updated_at }}</div>
                                                        <div class="mt-2">
                                                            <p><strong>Fotos</strong></p>
                                                            <div class="card">
                                                                <div id="carouselExample" class="carousel slide">
                                                                    <div class="carousel-inner">
                                                                        <div class="carousel-item active">
                                                                            <img src="https://i.blogs.es/351454/conector/650_1200.jpg" class="d-block w-100" alt="...">
                                                                        </div>
                                                                        <div class="carousel-item">
                                                                            <img src="https://www.adslzone.net/app/uploads-adslzone.net/2023/01/ONT-2.jpg" class="d-block w-100" alt="...">
                                                                        </div>
                                                                        <div class="carousel-item">
                                                                            <img src="..." class="d-block w-100" alt="...">
                                                                        </div>
                                                                    </div>
                                                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                                                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                        <span class="visually-hidden">Previous</span>
                                                                    </button>
                                                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                                                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                        <span class="visually-hidden">Next</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <tr>
                                                                <th>Material</th>
                                                                <th>Cantidad</th>
                                                                <th>SN</th>
                                                            </tr>
                                                            @foreach($technical_order->materials as $material_to_order)
                                                                <td>{{ $material_to_order->material->name }}</td>
                                                                <td>{{ $material_to_order->quantity }}</td>
                                                                <td>{{ $material_to_order->serial_number }}</td>
                                                            @endforeach
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </td>
                        </tr>

                        <!-- Modal para asignar/reasignar orden -->
                        <div class="modal fade" id="assignOrderModal{{ $technical_order->id }}" tabindex="-1" role="dialog" aria-labelledby="assignOrderModalLabel{{ $technical_order->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="assignOrderModalLabel{{ $technical_order->id }}">
                                            {{ $technical_order->status === 'Pendiente' ? 'Asignar Orden' : 'Reasignar Orden' }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('technicals_orders.update', $technical_order->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-group">
                                                <label for="assigned_user_id">Seleccione un técnico:</label>
                                                <select name="assigned_user_id" id="assigned_user_id" class="form-control" required>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                {{ $technical_order->status === 'Pendiente' ? 'Asignar' : 'Reasignar' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-center">
            {{ $technical_orders->links() }}
        </div>
    </div>
@endsection
@section('js')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterField = document.getElementById('filterField');
            const filterValueContainer = document.getElementById('filterValueContainer');
            const users = @json($users); // Pasar los usuarios desde PHP a JavaScript

            filterField.addEventListener('change', () => {
                const selectedField = filterField.value;

                // Limpiar el contenedor
                filterValueContainer.innerHTML = '';

                let inputElement;

                switch (selectedField) {
                    case 'status':
                    case 'type':
                    case 'detail':
                        inputElement = document.createElement('select');
                        inputElement.id = 'filterInput';
                        inputElement.name = 'filter_value';
                        inputElement.className = 'form-control';

                        // Agregar opciones según el campo seleccionado
                        let options = [];
                        if (selectedField === 'status') {
                            options = ['Pendiente', 'Asignada', 'Rechazada', 'Prefinalizada', 'Cerrada'];
                        } else if (selectedField === 'type') {
                            options = ['Servicio', 'Incidencia'];
                        } else if (selectedField === 'detail') {
                            options = [
                                'Instalación de servicio', 'Retiro de servicio', 'Corte de servicio',
                                'Traslado de servicio', 'Adición de servicio', 'Sin servicio de TV',
                                'Sin servicio de Internet', 'Sin servicio', 'Configuraciones', 'Otros'
                            ];
                        }

                        options.forEach(option => {
                            const optionElement = document.createElement('option');
                            optionElement.value = option;
                            optionElement.textContent = option;
                            inputElement.appendChild(optionElement);
                        });

                        break;

                    case 'assigned_user':
                        inputElement = document.createElement('select');
                        inputElement.id = 'filterInput';
                        inputElement.name = 'filter_value';
                        inputElement.className = 'form-control';

                        // Agregar opciones de usuarios
                        users.forEach(user => {
                            const optionElement = document.createElement('option');
                            optionElement.value = user.id; // Usar el ID del usuario
                            optionElement.textContent = user.name; // Mostrar el nombre del usuario
                            inputElement.appendChild(optionElement);
                        });

                        break;

                    default:
                        inputElement = document.createElement('input');
                        inputElement.type = 'text';
                        inputElement.id = 'filterInput';
                        inputElement.name = 'filter_value';
                        inputElement.className = 'form-control';
                        inputElement.placeholder = 'Ingrese un valor';
                }

                // Crear el label
                const labelElement = document.createElement('label');
                labelElement.htmlFor = 'filterValue';
                labelElement.className = 'form-label';
                labelElement.textContent = 'Valor';

                // Agregar el label y el input/select al contenedor
                filterValueContainer.appendChild(labelElement);
                filterValueContainer.appendChild(inputElement);
            });

            // Inicializar el campo de filtro al cargar la página
            filterField.dispatchEvent(new Event('change'));
        });
    </script>
@endsection
