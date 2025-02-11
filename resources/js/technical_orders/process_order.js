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
        <button type="button" class="btn btn-danger btn-sm float-right mt-2 remove-entry">
            <i class="fas fa-trash"></i> Eliminar
        </button>
    </div>
`);

    // Agregar el nuevo campo al contenedor
    container.append(newEntry);

    // Re-inicializar Select2 en el nuevo select agregado
    newEntry.find('.material-select').select2({
        placeholder: "Seleccione un material",
        allowClear: true,
        width: '100%'
    });

    newEntry.find('.serial-number-select').select2({
        placeholder: "Seleccione un número de serie",
        allowClear: true,
        width: '100%'
    });

    // Configurar evento de cambio para el nuevo campo
    newEntry.find('.material-select').on('change', function() {
        handleMaterialChange($(this));
    });

    // Configurar evento de eliminación
    newEntry.find('.remove-entry').click(function() {
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
    $(document).on('change', '.material-select', function() {
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
