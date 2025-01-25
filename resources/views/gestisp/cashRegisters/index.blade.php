@extends('adminlte::page')

@section('title', 'Pagos')

@section('content_header')
    <div class="card p-3">
        <h2>GESTIÓN DE CAJA</h2>
    </div>
@endsection

@section('content')
    <div class="card p-3">
        <!-- Contenedor de la caja -->
        <div id="cash-register-status">
            <!-- Aquí mostraremos el estado de la caja -->
        </div>

        <!-- Botones para abrir y cerrar caja -->
        <div id="cash-register-actions" class="mt-4">
            <button id="open-cash-register" class="btn btn-success">Abrir Caja</button>
            <button id="close-cash-register" class="btn btn-danger" style="display:none;">Cerrar Caja</button>
        </div>

        <!-- Formulario para abrir/cerrar caja -->
        <div id="cash-register-form" class="mt-4" style="display:none;">
            <form id="cash-register-form-content">
                <div class="form-group">
                    <label for="initial_amount">Monto Inicial</label>
                    <input type="number" id="initial_amount" name="initial_amount" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="opening_notes">Notas de Apertura</label>
                    <textarea id="opening_notes" name="opening_notes" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Abrir Caja</button>
            </form>
        </div>

        <div id="close-form" style="display:none;">
            <form id="close-form-content">
                <div class="form-group">
                    <label for="final_amount">Monto Final</label>
                    <input type="number" id="final_amount" name="final_amount" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="closing_notes">Notas de Cierre</label>
                    <textarea id="closing_notes" name="closing_notes" class="form-control"></textarea>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para mostrar el PDF -->
    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Reporte de Caja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfIframe" src="" width="100%" height="500px"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <a id="downloadPdf" class="btn btn-primary" href="#" target="_blank">Guardar PDF</a>
                    <button id="printPdf" class="btn btn-info">Imprimir PDF</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('#open-cash-register').on('click', function() {
            if (confirm('¿Estás seguro de que deseas abrir la caja?')) {
                $('#cash-register-form').show();
            }
        });

        $('#close-cash-register').on('click', function() {
            if (confirm('¿Estás seguro de que deseas cerrar la caja?')) {
                $.ajax({
                    url: '{{ route('cash_register.close') }}',
                    method: 'POST',
                    data: {
                        final_amount: $('#final_amount').val(),
                        closing_notes: $('#closing_notes').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        getCashRegisterStatus();
                        $('#pdfIframe').attr('src', response.pdf_url);
                        $('#downloadPdf').attr('href', response.pdf_url);
                        $('#pdfModal').modal('show');
                    },
                    error: function(response) {
                        alert(response.responseJSON.error);
                    }
                });
            }
        });

        function getCashRegisterStatus() {
            $.ajax({
                url: '{{ route('cash_register.status') }}',
                method: 'GET',
                success: function(response) {
                    if (response.status === 'open') {
                        $('#cash-register-status').html(
                            '<div class="alert alert-success">Caja abierta. Saldo actual: <strong>' + response.expected_amount + '</strong>.</div>'
                        );
                        $('#open-cash-register').hide();
                        $('#close-cash-register').show();
                        $('#close-form').show();
                    } else {
                        $('#cash-register-status').html('<div class="alert alert-danger">Caja cerrada.</div>');
                        $('#open-cash-register').show();
                        $('#close-cash-register').hide();
                        $('#cash-register-form').hide();
                    }
                }
            });
        }

        $('#cash-register-form-content').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route('cash_register.open') }}',
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    getCashRegisterStatus();
                },
                error: function(response) {
                    alert(response.responseJSON.error);
                }
            });
        });

        $(document).ready(function() {
            getCashRegisterStatus();
        });

        $('#printPdf').on('click', function() {
            var pdfUrl = $('#pdfIframe').attr('src');
            var printWindow = window.open(pdfUrl, '_blank');
            printWindow.onload = function() {
                printWindow.print();
            };
        });
    </script>
@endsection
