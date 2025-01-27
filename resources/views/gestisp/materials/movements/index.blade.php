@extends('adminlte::page')

@section('title', 'Registrar Movimiento de Material')

@section('content_header')
    <div class="card p-3">
        <h2>REGISTRAR MOVIMIENTO DE MATERIAL</h2>
    </div>
@endsection

@section('content')
    @if(session('success-create'))
        <div class="alert alert-info">
            {{ session('success-create') }}
        </div>
    @elseif(session('success-update'))
        <div class="alert alert-info">
            {{ session('success-update') }}
        </div>
    @elseif(session('success-delete'))
        <div class="alert alert-info">
            {{ session('success-delete') }}
        </div>
    @endif

    <div class="card p-3">
        <form action="{{ route('movements.store') }}" method="POST" id="movementForm">
            @csrf

            <div class="row">
                <div class="form-group col-md-4">
                    <label for="type">Tipo de Movimiento</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Entrada">Entrada</option>
                        <option value="Salida">Salida</option>
                        <option value="Transferencia">Transferencia</option>
                    </select>
                </div>

                <div class="form-group col-md-4" id="warehouse-origin-group" style="display: none;">
                    <label for="warehouse_origin_id">Almacén de Origen</label>
                    <select name="warehouse_origin_id" id="warehouse_origin_id" class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->description }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-4" id="warehouse-destination-group" style="display: none;">
                    <label for="warehouse_destination_id">Almacén de Destino</label>
                    <select name="warehouse_destination_id" id="warehouse_destination_id" class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->description }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-12">
                    <label for="reason">Motivo del movimiento</label>
                    <select name="reason" id="reason" class="form-control" required>
                        <option value="">Seleccione un motivo</option>
                        <option value="Compra" class="option-Entrada">Entrada por Compra de materiales</option>
                        <option value="Inicial" class="option-Entrada">Entrada por Inventario inicial</option>
                        <option value="Devolucion" class="option-Entrada">Entrada por Devolución de materiales</option>
                        <option value="Deterioro" class="option-Salida">Salida por deterioro</option>
                        <option value="Venta" class="option-Salida">Salida por venta</option>
                        <option value="Orden" class="option-Salida">Salida por orden técnica</option>
                        <option value="Transferencia" class="option-Transferencia">Transferencia entre almacenes</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-primary" id="open-modal-btn">
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
            </div>

            <button type="submit" class="btn btn-success">Registrar Movimiento</button>
        </form>
    </div>

    <!-- Modal para agregar material -->
    <div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
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
                    </div>

                    <div class="form-group">
                        <label>Cantidad</label>
                        <input type="number" id="modal-quantity" class="form-control quantity-input" required min="1">
                    </div>

                    <div class="form-group">
                        <label>Unidad de Medida</label>
                        <select id="modal-unit-of-measurement" class="form-control" required>
                            <option value="">Seleccione una unidad</option>
                            <option value="Unidades">Unidades</option>
                            <option value="Metros">Metros</option>
                            <option value="Litros">Litros</option>
                            <option value="Paquetes">Paquetes</option>
                        </select>
                    </div>

                    <div id="modal-serial-numbers-container" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="add-material-modal-btn">Agregar Material</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            let materialIndex = 0;

            // Inicializar Select2
            function initSelect2() {
                $('.material-select').select2({
                    placeholder: "Buscar material",
                    allowClear: true,
                    templateResult: formatMaterial
                });
            }
            initSelect2();

            // Elementos del DOM
            const typeSelect = $('#type');
            const warehouseOriginGroup = $('#warehouse-origin-group');
            const warehouseDestinationGroup = $('#warehouse-destination-group');
            const materialsTable = $('#materials-table tbody');
            const openModalBtn = $('#open-modal-btn');
            const addMaterialModalBtn = $('#add-material-modal-btn');
            const reasonSelect = $('#reason');

            // Manejar visibilidad de almacenes y opciones de motivo
            typeSelect.on('change', function() {
                const selectedType = $(this).val();
                warehouseOriginGroup.hide();
                warehouseDestinationGroup.hide();
                reasonSelect.find('option').hide().prop('disabled', true);
                reasonSelect.find('option[value=""]').show().prop('disabled', false);

                switch(selectedType) {
                    case 'Entrada':
                        warehouseDestinationGroup.show();
                        reasonSelect.find('.option-Entrada').show().prop('disabled', false);
                        break;
                    case 'Salida':
                        warehouseOriginGroup.show();
                        reasonSelect.find('.option-Salida').show().prop('disabled', false);
                        break;
                    case 'Transferencia':
                        warehouseOriginGroup.show();
                        warehouseDestinationGroup.show();
                        reasonSelect.find('.option-Transferencia').show().prop('disabled', false);
                        break;
                }
            });

            // Abrir modal de agregar material
            openModalBtn.on('click', function() {
                $('#materialModal').modal('show');
            });

            // Agregar material desde el modal
            addMaterialModalBtn.on('click', function() {
                const materialId = $('#modal-material-select').val();
                const materialName = $('#modal-material-select option:selected').data('name');
                const quantity = $('#modal-quantity').val();
                const unitOfMeasurement = $('#modal-unit-of-measurement').val();
                const isEquipment = $('#modal-material-select option:selected').data('is-equipment') === 1;
                const serialNumbersContainer = $('#modal-serial-numbers-container');
                const serialNumbers = [];

                if (isEquipment) {
                    serialNumbersContainer.find('input').each(function() {
                        serialNumbers.push($(this).val());
                    });
                }

                // Añadir fila a la tabla
                const newRow = `
                    <tr data-index="${materialIndex}">
                        <td>
                            <input type="hidden" name="materials[${materialIndex}][material_id]" value="${materialId}">
                            ${materialName}
                        </td>
                        <td>
                            <input type="hidden" name="materials[${materialIndex}][quantity]" value="${quantity}">
                            ${quantity}
                        </td>
                        <td>
                            <input type="hidden" name="materials[${materialIndex}][unit_of_measurement]" value="${unitOfMeasurement}">
                            ${unitOfMeasurement}
                        </td>
                        <td>
                            ${isEquipment ? serialNumbers.join(', ') : 'N/A'}
                            ${isEquipment ? serialNumbers.map((sn, i) => `<input type="hidden" name="materials[${materialIndex}][serial_numbers][${i}]" value="${sn}">`).join('') : ''}
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-material-btn">Eliminar</button>
                        </td>
                    </tr>
                `;

                materialsTable.append(newRow);
                materialIndex++;
                $('#materialModal').modal('hide');
                $('#materialModal').find('input, select').val('');
                serialNumbersContainer.hide().empty();
            });

            // Eliminar material de la tabla
            materialsTable.on('click', '.remove-material-btn', function() {
                $(this).closest('tr').remove();
            });

            // Mostrar campos de número de serie si es equipo
            $('#modal-material-select').on('change', function() {
                const isEquipment = $(this).find('option:selected').data('is-equipment') === 1;
                const serialNumbersContainer = $('#modal-serial-numbers-container');

                if (isEquipment) {
                    serialNumbersContainer.show();
                } else {
                    serialNumbersContainer.hide().empty();
                }
            });

            // Generar campos de número de serie basados en la cantidad
            $('#modal-quantity').on('change', function() {
                const quantity = $(this).val();
                const isEquipment = $('#modal-material-select option:selected').data('is-equipment') === 1;
                const serialNumbersContainer = $('#modal-serial-numbers-container');

                if (isEquipment) {
                    serialNumbersContainer.empty();
                    for (let i = 0; i < quantity; i++) {
                        serialNumbersContainer.append(`
                            <div class="form-group">
                                <label>Número de Serie #${i + 1}</label>
                                <input type="text" class="form-control serial-number-input" required>
                            </div>
                        `);
                    }
                } else {
                    serialNumbersContainer.hide().empty();
                }
            });

            // Formato de material en Select2
            function formatMaterial(material) {
                if (!material.id) return material.text;
                return $(`
                    <span>
                        ${material.text}
                        ${material.element.getAttribute('data-is-equipment') === '1' ?
                    '<small class="text-muted">(Equipo)</small>' :
                    '<small class="text-muted">(Material)</small>'}
                    </span>
                `);
            }
        });
    </script>
@endsection
