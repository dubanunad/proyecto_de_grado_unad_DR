@extends('adminlte::page')

@section('title', 'Pagos')

@section('content_header')
    <div class="card p-3">
        <h2>BUSCAR CLIENTE O CONTRATO PARA COBRO</h2>
    </div>
@endsection

@section('content')

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulario para buscar -->
    <div class="card">
        <div class="card-header">
            <form method="POST" action="{{ route('payments.search') }}">
                @csrf
                <div class="row align-items-center">
                    <div class="col-md-4 mt-1 mb-1">
                        <input
                            type="text"
                            id="search_term"
                            name="search_term"
                            class="form-control"
                            placeholder="Número de documento o ID del contrato"
                            value="{{ request('search_term') }}">
                    </div>
                    <div class="col-md-2 mt-1 mb-1">
                        <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
                            <option value="">Resultados por página</option>
                            <option value="8" {{ request('per_page') == 8 ? 'selected' : '' }}>8</option>
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                    <div class="col-md-6 text-center text-md-right">
                        <button type="submit" class="btn btn-primary" title="Aplicar filtro">
                            <i class="fas fa-filter"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($invoices) && $invoices->isNotEmpty())
        <!-- Tabla para listar los pagos -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Factura #</th>
                        <th>Cliente</th>
                        <th>Documento</th>
                        <th>Contrato</th>
                        <th>Fecha Emisión</th>
                        <th>Fecha Vencimiento</th>
                        <th>Total</th>
                        <th>Pendiente</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->id }}</td>
                            <td>{{ $invoice->contract->client->name ?? 'N/A' }}</td>
                            <td>{{ $invoice->contract->client->identity_number ?? 'N/A' }}</td>
                            <td>{{ $invoice->contract_id }}</td>
                            <td>{{ $invoice->issue_date }}</td>
                            <td>{{ $invoice->due_date }}</td>
                            <td>{{ $invoice->total }}</td>
                            <td>{{ $invoice->getPendingAmount() }}</td>
                            <td>
                                @if($invoice->getPendingAmount() > 0)
                                    <button
                                        class="btn btn-success btn-sm"
                                        data-toggle="modal"
                                        data-target="#paymentModal"
                                        data-invoice-id="{{ $invoice->id }}"
                                        data-total="{{ $invoice->total }}"
                                        data-pending="{{ $invoice->getPendingAmount() }}">
                                        Pagar
                                    </button>
                                @else
                                    <span class="badge badge-success">Pagada</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-center mt-3">
            {{ $invoices->links() }}
        </div>
    @else
        <div class="alert alert-info mt-3">
            No se encontraron facturas pendientes.
        </div>
    @endif

    <!-- Modal para confirmar pago -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="paymentForm" method="POST" action="{{ route('payments.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Registrar Pago</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="invoice_id" name="invoice_id">
                        <div class="form-group">
                            <label for="amount">Monto a Pagar</label>
                            <input type="number" step="0.01" id="amount" name="amount" class="form-control" required>
                            <small class="text-muted">Pendiente: <span id="pending_amount"></span></small>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Método de Pago</label>
                            <select id="payment_method" name="payment_method" class="form-control" required>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Transferencia">Transferencia</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="reference_number">Número de Referencia</label>
                            <input type="text" id="reference_number" name="reference_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="notes">Notas</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Confirmar Pago</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Éxito de Pago -->
    <div class="modal fade" id="paymentSuccessModal" tabindex="-1" role="dialog" aria-labelledby="paymentSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="paymentSuccessModalLabel">Pago Registrado con Éxito</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                        <h4 class="mt-3">¡Pago procesado correctamente!</h4>
                    </div>
                    <div class="payment-details">
                        <p><strong>Monto pagado:</strong> $<span id="successAmount"></span></p>
                        <p><strong>Saldo pendiente:</strong> $<span id="newBalance"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="downloadPdf">
                        <i class="fas fa-download"></i> Descargar PDF
                    </button>
                    <button type="button" class="btn btn-info" id="printPdf">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Error de Pago -->
    <div class="modal fade" id="paymentErrorModal" tabindex="-1" role="dialog" aria-labelledby="paymentErrorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="paymentErrorModalLabel">Error al Procesar el Pago</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-times-circle text-danger" style="font-size: 48px;"></i>
                        <h4 class="mt-3">¡Ocurrió un error!</h4>
                    </div>
                    <div class="alert alert-danger" id="errorMessage"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        // Evento para cargar los datos en el modal de pago
        $('#paymentModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var invoiceId = button.data('invoice-id');
            var total = button.data('total');
            var pending = button.data('pending');

            var modal = $(this);
            modal.find('#invoice_id').val(invoiceId);
            modal.find('#amount').attr('max', pending).val(pending);
            modal.find('#pending_amount').text(pending);
        });

        // Manejar el envío del formulario de pago
        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();

            // Mostrar indicador de carga
            $(this).find('button[type="submit"]').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...'
            );

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    // Cerrar el modal de pago
                    $('#paymentModal').modal('hide');

                    if (response.success) {
                        // Actualizar los detalles en el modal de éxito
                        $('#successAmount').text(response.payment.amount);
                        $('#newBalance').text(response.new_balance);

                        // Guardar la URL del PDF
                        $('#downloadPdf, #printPdf').data('pdf-url', response.pdf_url);

                        // Mostrar el modal de éxito
                        $('#paymentSuccessModal').modal('show');

                        // Actualizar la tabla o recargar según el saldo
                        if (parseFloat(response.new_balance) === 0) {
                            // Recargar la página cuando se cierre el modal
                            $('#paymentSuccessModal').on('hidden.bs.modal', function() {
                                location.reload();
                            });
                        } else {
                            // Actualizar la fila en la tabla
                            var row = $('tr').find('td:contains(' + response.payment.invoice_id + ')').first().parent();
                            row.find('td:eq(7)').text(response.new_balance);
                        }
                    }
                },
                error: function(xhr) {
                    // Cerrar el modal de pago
                    $('#paymentModal').modal('hide');

                    // Mostrar el mensaje de error
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.error
                        ? xhr.responseJSON.error
                        : 'Ocurrió un error al procesar el pago.';

                    $('#errorMessage').text(errorMessage);
                    $('#paymentErrorModal').modal('show');
                },
                complete: function() {
                    // Restaurar el botón de submit
                    $('#paymentForm').find('button[type="submit"]').prop('disabled', false).html('Confirmar Pago');
                }
            });
        });

        // Manejar la descarga del PDF
        $('#downloadPdf').on('click', function() {
            var pdfUrl = $(this).data('pdf-url');
            window.open(pdfUrl, '_blank');
        });

        // Manejar la impresión del PDF
        $('#printPdf').on('click', function() {
            var pdfUrl = $(this).data('pdf-url');
            var printWindow = window.open(pdfUrl, '_blank');
            printWindow.onload = function() {
                printWindow.print();
            };
        });

        // Limpiar el formulario cuando se cierra el modal
        $('#paymentModal').on('hidden.bs.modal', function () {
            $('#paymentForm')[0].reset();
        });
    </script>
@endsection
