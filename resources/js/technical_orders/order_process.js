$(document).ready(function () {
    // Inicializar Select2
    $('.material-select').select2({
        placeholder: "Seleccione un material",
        allowClear: true
    });

    // Variables para almacenar los materiales seleccionados
    let selectedMaterials = [];

    // Abrir el modal para agregar material
    $('#open-modal-btn').on('click', function () {
        $('#materialModal').modal('show');
    });

    // Manejar la selección de material en el modal
    $('#modal-material-select').on('change', function () {
        let materialId = $(this).val();
        let materialName = $(this).find('option:selected').data('name');
        let isEquipment = $(this).find('option:selected').data('is-equipment');
        let warehouseId = $('#materialModal').data('warehouse-id'); // Obtener el ID del almacén desde el modal

        // Ocultar o mostrar el campo de números de serie según el tipo de material
        if (isEquipment) {
            $('#modal-serial-numbers-container').show();
            loadSerialNumbers(warehouseId, materialId); // Pasar warehouseId y materialId
        } else {
            $('#modal-serial-numbers-container').hide();
        }

        // Cargar la cantidad disponible
        loadAvailableQuantity(warehouseId, materialId); // Pasar warehouseId y materialId
    });

    // Cargar números de serie disponibles para el material seleccionado
    function loadSerialNumbers(warehouseId, materialId) {
        $.ajax({
            url: `/public/inventories/${warehouseId}/materials/${materialId}/serial-numbers`,
            method: 'GET',
            success: function (response) {
                let serialNumberSelect = $('#serial-number-select');
                serialNumberSelect.empty();

                // Agregar opciones de números de serie
                response.forEach(function (serialNumber) {
                    serialNumberSelect.append(`<option value="${serialNumber}">${serialNumber}</option>`);
                });

                // Activar Select2 para mejor experiencia de usuario
                serialNumberSelect.select2({
                    placeholder: "Seleccione un número de serie",
                    allowClear: true
                });
            },
            error: function (error) {
                console.error('Error al cargar los números de serie:', error);
            }
        });
    }

    // Cargar la cantidad disponible del material
    function loadAvailableQuantity(warehouseId, materialId) {
        $.ajax({
            url: `/public/inventories/${warehouseId}/materials/${materialId}/quantity`,
            method: 'GET',
            success: function (response) {
                // La respuesta es un objeto con la clave 'quantity'
                let availableQuantity = response.quantity;
                $('#available-quantity').text(availableQuantity);
                $('#available-quantity-text').show();
            },
            error: function (error) {
                console.error('Error al cargar la cantidad disponible:', error);
            }
        });
    }

    // Agregar material a la tabla
    $('#add-material-modal-btn').on('click', function () {
        let materialId = $('#modal-material-select').val();
        let materialName = $('#modal-material-select').find('option:selected').text();
        let quantity = $('#modal-quantity').val();
        let unitOfMeasurement = $('#modal-unit-of-measurement').val();
        let serialNumbers = $('#serial-number-select').val();

        if (!materialId || !quantity || !unitOfMeasurement) {
            Swal.fire('Error', 'Por favor, complete todos los campos.', 'error');
            return;
        }

        // Validar que la cantidad no exceda el stock disponible
        let availableQuantity = parseInt($('#available-quantity').text());
        if (quantity > availableQuantity) {
            Swal.fire('Error', 'La cantidad seleccionada excede el stock disponible.', 'error');
            return;
        }

        // Agregar el material a la lista de materiales seleccionados
        selectedMaterials.push({
            materialId: materialId,
            materialName: materialName,
            quantity: quantity,
            unitOfMeasurement: unitOfMeasurement,
            serialNumbers: serialNumbers
        });

        // Actualizar la tabla
        updateMaterialsTable();

        // Limpiar el modal y cerrarlo
        $('#modal-material-select').val('').trigger('change');
        $('#modal-quantity').val('');
        $('#modal-unit-of-measurement').val('');
        $('#serial-number-select').val('');
        $('#materialModal').modal('hide');
    });

    // Actualizar la tabla de materiales
    function updateMaterialsTable() {
        let tableBody = $('#materials-table tbody');
        tableBody.empty();

        selectedMaterials.forEach(function (material, index) {
            let serialNumbersText = material.serialNumbers ? material.serialNumbers.join(', ') : 'N/A';

            tableBody.append(`
                <tr>
                    <td>${material.materialName}</td>
                    <td>${material.quantity}</td>
                    <td>${material.unitOfMeasurement}</td>
                    <td>${serialNumbersText}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-material-btn" data-index="${index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    // Eliminar material de la tabla
    $('#materials-table').on('click', '.remove-material-btn', function () {
        let index = $(this).data('index');
        selectedMaterials.splice(index, 1);
        updateMaterialsTable();
    });

    // Enviar el formulario con los materiales seleccionados
    $('form').on('submit', function (e) {
        e.preventDefault();

        // Agregar los materiales seleccionados al formulario
        selectedMaterials.forEach(function (material, index) {
            $('<input>').attr({
                type: 'hidden',
                name: `material_id[${index}]`,
                value: material.materialId
            }).appendTo('form');

            $('<input>').attr({
                type: 'hidden',
                name: `quantity[${index}]`,
                value: material.quantity
            }).appendTo('form');

            if (material.serialNumbers) {
                $('<input>').attr({
                    type: 'hidden',
                    name: `serial_number[${index}]`,
                    value: material.serialNumbers.join(',')
                }).appendTo('form');
            }
        });

        // Enviar el formulario
        this.submit();
    });
});
