@extends('adminlte::page')

@section('title', 'Historial de movimientos')

@section('content_header')
    <div class="card p-3">
        <h2>HISTORIAL DE MOVIMIENTOS DE ALMACÉN</h2>
    </div>

@endsection

@section('content')
    <!-- Formulario para filtrar -->
    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('movements.history') }}">
                <div class="row align-items-center">
                    <!-- Select para el campo a buscar -->
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Criterio</label>
                        <select id="filterField" class="form-control" name="filter_field">
                            <option value="type" {{ request('filter_field') == 'type' ? 'selected' : '' }}>Tipo de Movimiento</option>
                            <option value="warehouse_origin" {{ request('filter_field') == 'warehouse_origin' ? 'selected' : '' }}>Almacén de Origen</option>
                            <option value="warehouse_destination" {{ request('filter_field') == 'warehouse_destination' ? 'selected' : '' }}>Almacén de destino</option>
                            <option value="serial_number" {{ request('filter_field') == 'serial_number' ? 'selected' : '' }}>Número de serial</option>
                        </select>
                    </div>

                    <!-- Input dinámico -->
                    <div class="col-md-2 mt-1 mb-1">
                        <label for="start_date" class="form-label">Valor</label>
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
                        <form method="GET" action="{{ route('movements.history') }}" class="form-inline">
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
                        <a href="{{ route('movements.history') }}" class="btn btn-secondary" title="Limpiar filtros">
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

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th>Fecha del movimiento</th>
                    <th>Tipo del movimiento</th>
                    <th>Almacén de origen</th>
                    <th>Almacén de destino</th>
                    <th>Material</th>
                    <th>Cantidad</th>
                    <th>Unidad de medida</th>
                    <th>Serial</th>
                    <th>Motivo</th>
                    <th>Realizado por</th>
                </tr>
                @foreach($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at }}</td>
                        <td>{{ $movement->type }}</td>
                        <td>{{ $movement->warehouseOrigin->description ?? 'N/A'}}</td>
                        <td>{{ $movement->warehouseDestination->description ?? 'N/A'}}</td>
                        <td>{{ $movement->material->name }}</td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->unit_of_measurement }}</td>
                        <td>{{ $movement->serial_number ?? 'N/A' }}</td>
                        <td>{{ $movement->reason}}</td>
                        <td>{{ $movement->user->name ?? 'N/A' }} {{ $movement->user->last_name ?? 'N/A' }}</td>

                    </tr>
                @endforeach

            </table>

        </div>
    </div>
    <div class="text-center">
        {{ $movements->links() }}
    </div>
@endsection
@section('js')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterField = document.getElementById('filterField');
            const filterInput = document.getElementById('filterInput');

            filterField.addEventListener('change', () => {
                const selectedField = filterField.value;


                switch (selectedField) {
                    case 'type':
                        filterInput.placeholder = 'Tipo de movimiento';
                        filterInput.type = 'text';
                        break;
                    case 'warehouse_origin':
                        filterInput.placeholder = 'Almacén de origen';
                        filterInput.type = 'text';
                        break;
                    case 'warehouse_destination':
                        filterInput.placeholder = 'Almacén de destino';
                        filterInput.type = 'text';
                        break;
                    case 'serial_number':
                        filterInput.placeholder = 'Serial';
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
