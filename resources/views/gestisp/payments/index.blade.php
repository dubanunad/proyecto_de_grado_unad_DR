@extends('adminlte::page')

@section('title', 'Pagos')

@section('content_header')
    <div class="card p-3">
        <h2>HISTORIAL DE PAGOS</h2>
    </div>
@endsection

@section('content')
    <!-- Formulario para filtrar -->
    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('payments.index') }}">
                <div class="row align-items-center">
                    <!-- Select para el campo a buscar -->
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Criterio</label>
                        <select id="filterField" class="form-control" name="filter_field">
                            <option value="client.identity_number" {{ request('filter_field') == 'client.identity_number' ? 'selected' : '' }}>Número de identidad</option>
                            <option value="client.name" {{ request('filter_field') == 'client.name' ? 'selected' : '' }}>Nombre</option>
                            <option value="client.last_name" {{ request('filter_field') == 'client.last_name' ? 'selected' : '' }}>Apellido</option>
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
                        <form method="GET" action="{{ route('payments.index') }}" class="form-inline">
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
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary" title="Limpiar filtros">
                            <i class="fas fa-times"></i> Limpiar
                        </a>

                        <a href="{{ route('payments.export-excel') }}" class="btn btn-success" title="Exportar todos los pagos a Excel">
                            <i class="fas fa-file-excel"></i>
                        </a>
                        <a href="{{ route('payments.export', [
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
                <tbody>
                <tr>
                    <th>ID</th>
                    <th>Identidad cliente</th>
                    <th>cliente</th>
                    <th>Monto</th>
                    <th>Fecha de pago</th>
                    <th>Cobrado por</th>
                </tr>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->invoice->contract->client->identity_number }}</td>
                        <td>{{ $payment->invoice->contract->client->name }} {{ $payment->invoice->contract->client->last_name }}</td>
                        <td>{{ $payment->amount }}</td>
                        <td>{{ $payment->payment_date }}</td>
                        <td>{{ $payment->user->name }} {{ $payment->user->last_name }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-center">
            {{ $payments->links() }}
        </div>
    </div>
@endsection

@section('js')

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
                    case 'client.name':
                        filterInput.placeholder = 'Nombre del cliente';
                        filterInput.type = 'text';
                        break;
                    case 'client.last_name':
                        filterInput.placeholder = 'Apellido del cliente';
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
