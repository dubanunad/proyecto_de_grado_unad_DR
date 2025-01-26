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
            <div class="form-group">
                <label for="type">Tipo de Movimiento</label>
                <select name="type" id="type" class="form-control" required>
                    <option value="">Seleccione...</option>
                    <option value="Entrada">Entrada</option>
                    <option value="Salida">Salida</option>
                    <option value="Transferencia">Transferencia</option>
                </select>
            </div>

            <div id="materials-container">
                <div class="material-entry">
                    <div class="form-group">
                        <label>Material</label>
                        <select name="materials[0][material_id]" class="form-control material-select select2" required>
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
                        <input type="number" name="materials[0][quantity]" class="form-control quantity-input" required min="1">
                    </div>

                    <div class="form-group">
                        <label>Unidad de Medida</label>
                        <select class="form-control" name="materials[0][unit_of_measurement]" required>
                            <option value="">Seleccione una unidad</option>
                            <option value="Unidades">Unidades</option>
                            <option value="Metros">Metros</option>
                            <option value="Litros">Litros</option>
                            <option value="Paquetes">Paquetes</option>
                        </select>
                    </div>

                    <div class="serial-numbers-container" style="display:none;"></div>
                </div>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-primary" id="add-material-btn">
                    <i class="fas fa-plus"></i> Agregar Material
                </button>
            </div>

            <div class="form-group" id="warehouse-origin-group" style="display: none;">
                <label for="warehouse_origin_id">Almacén de Origen</label>
                <select name="warehouse_origin_id" id="warehouse_origin_id" class="form-control">
                    <option value="">Seleccione...</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->description }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" id="warehouse-destination-group" style="display: none;">
                <label for="warehouse_destination_id">Almacén de Destino</label>
                <select name="warehouse_destination_id" id="warehouse_destination_id" class="form-control">
                    <option value="">Seleccione...</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->description }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">Registrar Movimiento</button>
        </form>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            let materialIndex = 1;

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
            const materialsContainer = $('#materials-container');
            const addMaterialBtn = $('#add-material-btn');

            // Manejar visibilidad de almacenes
            typeSelect.on('change', function() {
                const selectedType = $(this).val();
                warehouseOriginGroup.hide();
                warehouseDestinationGroup.hide();

                switch(selectedType) {
                    case 'Entrada':
                        warehouseDestinationGroup.show();
                        break;
                    case 'Salida':
                        warehouseOriginGroup.show();
                        break;
                    case 'Transferencia':
                        warehouseOriginGroup.show();
                        warehouseDestinationGroup.show();
                        break;
                }
            });

            // Agregar material
            addMaterialBtn.on('click', function() {
                const newMaterialEntry = `
                <div class="material-entry">
                    <div class="form-group">
                        <label>Material</label>
                        <select name="materials[${materialIndex}][material_id]" class="form-control material-select select2" required>
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
                <input type="number" name="materials[${materialIndex}][quantity]" class="form-control quantity-input" required min="1">
                    </div>

                    <div class="form-group">
                        <label>Unidad de Medida</label>
                        <select class="form-control" name="materials[${materialIndex}][unit_of_measurement]" required>
                            <option value="">Seleccione una unidad</option>
                            <option value="Unidades">Unidades</option>
                            <option value="Metros">Metros</option>
                            <option value="Litros">Litros</option>
                            <option value="Paquetes">Paquetes</option>
                        </select>
                    </div>

                    <div class="serial-numbers-container" style="display:none;"></div>
                </div>
            `;
                materialsContainer.append(newMaterialEntry);
                initSelect2();
                bindQuantityAndSerialEvents();
                materialIndex++;
            });

            // Gestionar número de serie y cantidad
            function bindQuantityAndSerialEvents() {
                $('.material-select').off('change').on('change', function() {
                    const materialEntry = $(this).closest('.material-entry');
                    const isEquipment = $(this).find('option:selected').data('is-equipment') === 1;
                    const serialContainer = materialEntry.find('.serial-numbers-container');

                    if (isEquipment) {
                        serialContainer.show();
                    } else {
                        serialContainer.hide().empty();
                    }
                });

                $('.quantity-input').off('change').on('change', function() {
                    const materialEntry = $(this).closest('.material-entry');
                    const quantity = $(this).val();
                    const isEquipment = materialEntry.find('.material-select option:selected').data('is-equipment') === 1;
                    const serialContainer = materialEntry.find('.serial-numbers-container');

                    if (isEquipment) {
                        serialContainer.empty();
                        for (let i = 0; i < quantity; i++) {
                            serialContainer.append(`
                            <div class="form-group">
                                <label>Número de Serie #${i + 1}</label>
                                <input type="text" name="materials[${materialEntry.index()}][serial_numbers][]"
                                       class="form-control" required>
                            </div>
                        `);
                        }
                    } else {
                        serialContainer.hide().empty();
                    }
                });
            }
            bindQuantityAndSerialEvents();

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
