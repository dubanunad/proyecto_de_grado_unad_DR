@extends('adminlte::page')

@section('title', 'Clientes')

@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{asset('/css/gestisp/styles.css')}}">
@endsection

@section('content_header')
    <h2>Listado de clientes</h2>
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
            <form method="GET" action="{{ route('clients.index') }}">
                <div class="row align-items-center">
                    <!-- Select para el campo a buscar -->
                    <div class="col-md-3">
                        <select id="filterField" class="form-control" name="filter_field">
                            <option value="name" {{ request('filter_field') == 'name' ? 'selected' : '' }}>Nombre</option>
                            <option value="type_client" {{ request('filter_field') == 'type_client' ? 'selected' : '' }}>Tipo de cliente</option>
                            <option value="identity_number" {{ request('filter_field') == 'identity_number' ? 'selected' : '' }}>Número de identidad</option>
                            <!-- Añade más opciones según tus campos -->
                        </select>
                    </div>

                    <!-- Input dinámico -->
                    <div class="col-md-3">
                        <input
                            type="text"
                            id="filterInput"
                            name="filter_value"
                            class="form-control"
                            placeholder="Ingrese un valor"
                            value="{{ request('filter_value') }}">
                    </div>

                    <!--Formulario de paginación-->
                    <div class="col-md-2">
                        <form method="GET" action="{{ route('clients.index') }}" class="form-inline">
                            <select name="per_page" id="per_page" class="form-control mr-2" onchange="this.form.submit()">
                                <option value="">Resultados por página</option>
                                <option value="8" {{ request('per_page') == 8 ? 'selected' : '' }}>8</option>
                                <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            </select>
                        </form>
                    </div>


                    <!-- Botones -->
                    <div class="col-md-4 text-right">
                        <button type="submit" class="btn btn-primary" title="Aplicar filtro">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary" title="Limpiar filtros">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#columnModal" title="Configurar columnas">
                            <i class="fas fa-check-square"></i>
                        </button>

                        <!--Modal-->

                        <div class="modal fade" id="columnModal" tabindex="-1" role="dialog" aria-labelledby="columnModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <!-- Eliminé la etiqueta <form> porque no es necesaria -->
                                    <div class="modal-header">
                                        <h5 class="modal-title">Seleccionar columnas</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_identity" checked>
                                            <label class="form-check-label" for="col_identity">Número de identidad</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="col_type" checked>
                                            <label class="form-check-label" for="col_type">Tipo de cliente</label>
                                        </div>
                                        <!-- Añade más opciones para las columnas -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="saveColumns">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Hasta aquí Modal-->

                        <a href="{{ route('clients.export') }}" class="btn btn-success" title="Exportar clientes a Excel">
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
                    <th>Número de identidad</th>
                    <th>Tipo de cliente</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Número de contrato</th>
                    <th>Servicio</th>
                    <th>Estado</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        <tr>
                            <td>{{ $client->identity_number }}</td>
                            <td>{{ $client->type_client }}</td>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->last_name }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <a href=""><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
    <div class="text-center mt-3">
        {{ $clients->links() }}
    </div>
@endsection

@section('js')
            <script>
                document.getElementById('saveColumns').addEventListener('click', () => {
                    const columns = document.querySelectorAll('.table thead th, .table tbody td');

                    document.querySelectorAll('.form-check-input').forEach((checkbox, index) => {
                        columns.forEach((col, colIndex) => {
                            if (colIndex % document.querySelectorAll('.table thead th').length === index) {
                                col.style.display = checkbox.checked ? '' : 'none';
                            }
                        });
                    });

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
                            case 'identity_number':
                                filterInput.placeholder = 'Número de identidad';
                                filterInput.type = 'number'; // Cambiar el tipo a "number" para este campo
                                break;
                            case 'type_client':
                                filterInput.placeholder = 'Tipo de cliente';
                                filterInput.type = 'text'; // Cambiar el tipo a "text"
                                break;
                            case 'name':
                                filterInput.placeholder = 'Nombre';
                                filterInput.type = 'text'; // Cambiar el tipo a "text"
                                break;
                            default:
                                filterInput.placeholder = 'Ingrese un valor';
                                filterInput.type = 'text';
                        }
                    });
                });
            </script>
@endsection
