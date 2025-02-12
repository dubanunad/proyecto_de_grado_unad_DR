@extends('adminlte::page')

@section('title', 'Órdenes Técnicas')

@section('content_header')
    <div class="card p-3">
        <h2>VER Y PROCESAR ORDEN {{ $technicalOrder->id }}</h2>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="card p-3 mt-1 col-md-6">
            <h3>Datos del cliente</h3>
            <p><strong>Número de contrato:</strong> {{ $technicalOrder->contract->id }}</p>
            <p><strong>Identificación del cliente:</strong> {{ $technicalOrder->contract->client->identity_number }}</p>
            <p><strong>Nombre y apellido del cliente:</strong> {{ $technicalOrder->contract->client->name }} {{ $technicalOrder->contract->client->last_name }}</p>
            <p><strong>Teléfonos:</strong> {{ $technicalOrder->contract->client->number_phone }}, {{ $technicalOrder->contract->client->aditional_phone }}</p>
            <hr>
            <h3>Residencia</h3>
            <p><strong>Barrio:</strong> {{ $technicalOrder->contract->neighborhood }}</p>
            <p><strong>Dirección:</strong> {{ $technicalOrder->contract->address }}</p>
            <hr>
            <h3>Datos de la orden</h3>
            <p><strong>Tipo de orden:</strong> {{ $technicalOrder->type }}</p>
            <p><strong>Detalle de orden:</strong> {{ $technicalOrder->detail }}</p>
            <p><strong>Comentario inicial:</strong> {{ $technicalOrder->initial_comment }}</p>
            <p><strong>Creada el:</strong> {{ $technicalOrder->created_at}} <strong>Por: </strong>{{  $technicalOrder->createdBy->name }} {{  $technicalOrder->createdBy->last_name }}</p>
        </div>
        <div class="card mt-1 p-3 col-md-6">
            <h3>Procesamiento de orden</h3>
            <form action="{{ route('technicals_orders.process', $technicalOrder->id) }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="">Comentario del técnico</label>
                    <textarea class="form-control" type="text" name="observations_technical" required></textarea>
                </div>
                <div class="form-group">
                    <label for="">Comentario del usuario</label>
                    <textarea class="form-control" type="text" name="client_observation" required></textarea>
                </div>
                <div class="form-group">
                    <label for="">Solución aplicada</label>
                    <textarea class="form-control" type="text" name="solution" required></textarea>
                </div>
                <div class="form-group">
                    <label for="images">Selecciona imágenes:</label>
                    <input class="form-control" type="file" name="images[]" multiple accept="image/*">
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary col-md-3" id="open-modal-btn">
                        <i class="fas fa-plus"></i> Agregar Material
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="materials-table">
                        <thead>
                        <tr>
                            <th>Material</th>
                            <th>Cantidad</th>
                            <th>Unidad de Medida</th>
                            <th>Números de Serie</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Filas de materiales agregados dinámicamente -->
                        </tbody>
                    </table>
                    <input type="submit" value="Procesar orden" class="col-md-3 btn btn-success" onclick="return confirmProcess();">
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para agregar material -->
    <div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true" data-warehouse-id="{{ $warehouse->id }}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="materialModalLabel">Agregar Material</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Material</label>
                        <select id="modal-material-select" class="form-control material-select select2" required>
                            <option value="">Seleccione un material</option>
                            @foreach ($materials as $material)
                                <option value="{{ $material->id }}"
                                        data-is-equipment="{{ $material->is_equipment }}"
                                        data-name="{{ $material->name }}">
                                    {{ $material->name }}
                                </option>
                            @endforeach
                        </select>
                        <small id="available-quantity-text" class="text-info mt-1" style="display: none;">
                            Cantidad disponible: <span id="available-quantity">0</span>
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Cantidad</label>
                        <input type="number" id="modal-quantity" class="form-control quantity-input" required min="1">
                    </div>

                    <div class="form-group">
                        <label>Unidad de Medida</label>
                        <select id="modal-unit-of-measurement" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <option value="Unidades">Unidades</option>
                            <option value="Metros">Metros</option>
                            <option value="Litros">Litros</option>
                            <option value="Paquetes">Paquetes</option>
                        </select>
                    </div>



                    <div id="modal-serial-numbers-container" style="display:none;">
                        <label for="serial-number-select">Números de Serie Disponibles</label>
                        <select id="serial-number-select" class="form-control" multiple>
                            <!-- Números de serie disponibles se agregarán aquí -->
                        </select>
                        <ul id="serial-number-list" style="list-style-type: none; padding: 0; margin-top: 10px;">
                            <!-- Lista de números de serie disponibles se agregarán aquí -->
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="add-material-modal-btn">Agregar Material</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/resources/js/technical_orders/order_process.js"></script>
    <script>
        function confirmProcess() {
            return confirm('¿Está seguro de procesar la orden?');
        }
    </script>
@endsection

