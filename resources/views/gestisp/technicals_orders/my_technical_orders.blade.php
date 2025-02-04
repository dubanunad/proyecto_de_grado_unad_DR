@extends('adminlte::page')

@section('title', 'Órdenes Técnicas')

@section('content_header')
    <div class="card p-3">
        <h2>ORDENES TÉCNICAS DE {{ strtoupper(Auth::user()->name.' '.Auth::user()->last_name) }}</h2>
    </div>
@endsection

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="card">
        <div class="card-head p-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>Número de orden</th>
                            <th>Número de contrato</th>
                            <th>Cliente</th>
                            <th>Dirección</th>
                            <th>Tipo de orden</th>
                            <th>Detalle</th>
                            <th>Comentario inicial</th>
                            <th>Estado</th>
                            <th>Fecha de creación</th>
                            <th>Acciones</th>
                        </tr>
                        @foreach($technical_orders as $technical_order)
                            <tr>
                                <td>{{ $technical_order->id }}</td>
                                <td>{{ $technical_order->contract->id }}</td>
                                <td>{{ $technical_order->contract->client->name }} {{ $technical_order->contract->client->last_name }}</td>
                                <td>{{ $technical_order->contract->address }}</td>
                                <td>{{ $technical_order->type }}</td>
                                <td>{{ $technical_order->detail }}</td>
                                <td>{{ $technical_order->initial_comment }}</td>
                                <td>{{ $technical_order->status }}</td>
                                <td>{{ $technical_order->created_at }}</td>
                                <td>
                                    <button class="btn btn-danger mt-2 col-md-8" title="Rechazar orden" data-toggle="modal" data-target="#rejectOrderModal{{ $technical_order->id }}">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                    <button class="btn btn-success mt-2 col-md-8" title="Procesar orden" data-toggle="modal" data-target="#processOrderModal{{ $technical_order->id }}">
                                        <i class="fas fa-cogs"></i>
                                    </button>
                                </td>
                            </tr>


                            <!-- Modal para rechazar la orden -->
                            <div class="modal fade" id="rejectOrderModal{{ $technical_order->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Rechazar orden {{ $technical_order->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('technical_orders.reject', $technical_order) }}" method="post">
                                                @csrf
                                                @method('put')
                                                <label for="reason">Motivo del rechazo de la orden</label>
                                                <textarea name="reason" class="form-control" required></textarea>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Rechazar orden</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal para procesar la orden -->
                            <div class="modal fade" id="processOrderModal{{ $technical_order->id }}" tabindex="-1" role="dialog" aria-labelledby="processOrderModalLabel{{ $technical_order->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="processOrderModalLabel{{ $technical_order->id }}">
                                                Procesar Orden #{{ $technical_order->id }}
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="processOrderForm{{ $technical_order->id }}" action="{{ route('technicals_orders.process', $technical_order->id) }}" method="POST">
                                                @csrf
                                                <!-- Observaciones Técnicas -->
                                                <div class="form-group">
                                                    <label for="observations_technical">Observaciones Técnicas</label>
                                                    <textarea class="form-control" id="observations_technical" name="observations_technical" rows="3" required></textarea>
                                                </div>

                                                <!-- Observación del Cliente -->
                                                <div class="form-group">
                                                    <label for="client_observation">Observación del Cliente</label>
                                                    <textarea class="form-control" id="client_observation" name="client_observation" rows="3" required></textarea>
                                                </div>

                                                <!-- Solución -->
                                                <div class="form-group">
                                                    <label for="solution">Solución</label>
                                                    <textarea class="form-control" id="solution" name="solution" rows="3" required></textarea>
                                                </div>

                                                <!-- Contenedor de Materiales -->
                                                <div class="materials-container" id="materialsContainer{{ $technical_order->id }}">
                                                    <h6 class="mt-4 mb-3">Materiales Utilizados</h6>
                                                    <div class="material-entry border p-3 mb-3">
                                                        <!-- Select de Material -->
                                                        <div class="form-group">
                                                            <label>Material</label>
                                                            <select class="form-control material-select" name="material_id[]" style="width: 100%">
                                                                <option value="">Seleccione un material</option>
                                                                @foreach($materials as $material)
                                                                    <option value="{{ $material->id }}"
                                                                            data-type="{{ $material->is_equipment ? 'equipo' : 'material' }}"
                                                                            data-quantity="{{ $material->total_quantity }}">
                                                                        {{ $material->name }} (Disponible: {{ $material->total_quantity }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <!-- Input de Cantidad -->
                                                        <div class="form-group">
                                                            <label>Cantidad</label>
                                                            <input type="number" class="form-control quantity-input" name="quantity[]" min="1">
                                                        </div>

                                                        <!-- Select de Número de Serie (inicialmente oculto) -->
                                                        <div class="form-group serial-number-container" style="display: none;">
                                                            <label>Número de Serie</label>
                                                            <select class="form-control serial-number-select" name="serial_number[]" style="width: 100%">
                                                                <option value="">Seleccione un número de serie</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Botones del formulario -->
                                                <div class="form-group mt-3">
                                                    <button type="button" class="btn btn-secondary" onclick="addMaterialEntry({{ $technical_order->id }})">
                                                        <i class="fas fa-plus"></i> Agregar Material
                                                    </button>
                                                    <button type="submit" class="btn btn-primary float-right">
                                                        <i class="fas fa-save"></i> Guardar
                                                    </button>
                                                </div>
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
    </div>
@endsection

@push('js')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .material-entry {
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 15px;
            position: relative;
        }

        .material-entry .form-group {
            margin-bottom: 10px;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .modal-lg {
            max-width: 80% !important;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar Select2 en los selectores existentes
            initializeSelect2Elements();
            // Configurar los handlers iniciales
            setupMaterialChangeHandlers();
        });

        function initializeSelect2Elements() {
            $('.material-select').each(function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2({
                        placeholder: "Seleccione un material",
                        allowClear: true,
                        width: '100%'
                    });
                }
            });

            $('.serial-number-select').each(function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2({
                        placeholder: "Seleccione un número de serie",
                        allowClear: true,
                        width: '100%'
                    });
                }
            });
        }

        function addMaterialEntry(orderId) {
            const container = $(`#materialsContainer${orderId}`);

            // Crear un nuevo campo de material desde cero
            const newEntry = $(`
        <div class="material-entry border p-3 mb-3">
            <div class="form-group">
                <label>Material</label>
                <select class="form-control material-select" name="material_id[]" style="width: 100%">
                    <option value="">Seleccione un material</option>
                    @foreach($materials as $material)
            <option value="{{ $material->id }}"
                                data-type="{{ $material->is_equipment ? 'equipo' : 'material' }}"
                                data-quantity="{{ $material->total_quantity }}">
                            {{ $material->name }} (Disponible: {{ $material->total_quantity }})
                        </option>
                    @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Cantidad</label>
            <input type="number" class="form-control quantity-input" name="quantity[]" min="1">
        </div>
        <div class="form-group serial-number-container" style="display: none;">
            <label>Número de Serie</label>
            <select class="form-control serial-number-select" name="serial_number[]" style="width: 100%">
                <option value="">Seleccione un número de serie</option>
            </select>
        </div>
        <button type="button" class="btn btn-danger btn-sm float-right mt-2"><i class="fas fa-trash"></i> Eliminar</button>
    </div>
`);

            // Agregar el nuevo campo al contenedor
            container.append(newEntry);

            // Inicializar Select2 para el nuevo campo
            newEntry.find('.material-select').select2({
                placeholder: "Seleccione un material",
                allowClear: true,
                width: '100%'
            });

            // Configurar el evento de cambio para el nuevo campo
            newEntry.find('.material-select').on('change', function() {
                handleMaterialChange($(this));
            });

            // Configurar el evento de eliminación
            newEntry.find('.btn-danger').click(function() {
                $(this).closest('.material-entry').remove();
            });

            // Configurar la validación de cantidad
            newEntry.find('.quantity-input').on('input', function() {
                const materialEntry = $(this).closest('.material-entry');
                const materialSelect = materialEntry.find('.material-select');
                const selectedOption = materialSelect.find(':selected');
                const maxQuantity = selectedOption.data('quantity');

                if (this.value > maxQuantity) {
                    alert(`La cantidad no puede exceder ${maxQuantity}`);
                    this.value = maxQuantity;
                }
            });
        }

        function setupMaterialChangeHandlers() {
            $('.material-select').off('change').on('change', function() {
                handleMaterialChange($(this));
            });
        }

        function handleMaterialChange(materialSelect) {
            const materialEntry = materialSelect.closest('.material-entry');
            const serialNumberContainer = materialEntry.find('.serial-number-container');
            const serialNumberSelect = materialEntry.find('.serial-number-select');
            const selectedOption = materialSelect.find(':selected');
            const materialType = selectedOption.data('type');
            const materialId = selectedOption.val();

            // Obtener el input de cantidad
            const quantityInput = materialEntry.find('.quantity-input');
            const maxQuantity = selectedOption.data('quantity');

            // Actualizar el máximo permitido en el input de cantidad
            if (maxQuantity) {
                quantityInput.attr('max', maxQuantity);
            }

            if (materialType === 'equipo' && materialId) {
                // Cargar números de serie
                fetch(`/public/technicals_orders/get-serial-numbers/${materialId}`)
                    .then(response => response.json())
                    .then(serialNumbers => {
                        serialNumberSelect.empty()
                            .append('<option value="">Seleccione un número de serie</option>');

                        serialNumbers.forEach(sn => {
                            serialNumberSelect.append(
                                `<option value="${sn}">${sn}</option>`
                            );
                        });

                        serialNumberContainer.show();
                        serialNumberSelect.trigger('change');
                    });
            } else {
                serialNumberContainer.hide();
                serialNumberSelect.val('').trigger('change');
            }
        }
    </script>
@endpush
