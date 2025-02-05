@extends('adminlte::page')

@section('title', 'Facturas')

@section('content')
    <div class="card  mt-3">
        <div class="card-head pt-3">
            <div class="row d-flex justify-content-between mb-4 pr-3">
                <div class="col-md-8">
                    <h2 class="ml-2 P3">LISTADO DE FACTURAS</h2>
                </div>
                <div class="col-md-2  text-center text-md-right mb-2">
                    <form action="{{ route('invoices.generate') }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas generar las facturas?');">
                        @csrf
                        <button type="submit" class="btn btn-primary col-8">Generar Facturas</button>
                    </form>
                </div>

                <div class="col-md-2 text-center text-md-left">
                    <a href="{{ route('invoices.generate_max_pdf') }}" id="generatePdfButton" class="btn btn-danger col-8" title="Generar PDF de facturas pendientes">
                        Generar PDF <i class="far fa-file-pdf"></i>
                    </a>
                </div>

            </div>

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
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Fecha de emisión</th>
                        <th>Fecha de vencimiento</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>

                    @foreach($invoices as $invoice)
                        <tr>
                            <td>{{$invoice->id}}</td>
                            <td>{{$invoice->contract->client->name}} {{$invoice->contract->client->last_name}}</td>
                            <td>{{$invoice->issue_date}}</td>
                            <td>{{$invoice->due_date}}</td>
                            <td>{{$invoice->total}}</td>
                            <td>{{$invoice->status}}</td>
                            <td><a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-info"><i class="far fa-eye"></i></a></td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="text-center">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Modal para notificar al usuario -->
    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">PDF de Facturas Pendientes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <embed id="pdfViewer" src="" width="100%" height="500px" type="application/pdf">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Deshabilitar el botón y cambiar su texto mientras se genera el PDF
        document.querySelector('form').addEventListener('submit', function() {
            const button = this.querySelector('button');
            button.disabled = true;
            button.textContent = 'Generando...';
        });

        // Iniciar la verificación del PDF solo después de hacer clic en el botón "Generar PDF"
        $(document).ready(function() {
            $('#generatePdfButton').on('click', function(e) {
                e.preventDefault(); // Evitar que el enlace redirija inmediatamente

                // Mostrar un mensaje de carga
                console.log("Iniciando proceso de generación de PDF...");

                // Realizar la solicitud para generar el PDF
                $.ajax({
                    url: $(this).attr('href'), // Usar la URL del enlace
                    method: 'GET',
                    success: function(response) {
                        console.log("PDF generado. Iniciando verificación del estado...");

                        // Iniciar la verificación del estado del PDF
                        checkPdfStatus();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al generar el PDF:', error);
                        console.log("Código de estado HTTP:", xhr.status);
                        console.log("Respuesta del servidor:", xhr.responseText);
                    }
                });
            });

            // Función para verificar el estado del PDF
            function checkPdfStatus() {
                console.log("Realizando solicitud AJAX para verificar el estado del PDF...");

                $.ajax({
                    url: "{{ route('invoices.check-pdf-status') }}", // Usar la ruta definida
                    method: 'GET',
                    success: function(response) {
                        console.log("Respuesta recibida:", response);

                        if (response.pdfPath) {
                            console.log("PDF listo. Ruta del PDF:", response.pdfPath);

                            // Mostrar el modal con el PDF
                            $('#pdfViewer').attr('src', response.pdfPath + '?t=' + response.timestamp);
                            $('#downloadLink').attr('href', response.pdfPath + '?t=' + response.timestamp);
                            $('#pdfModal').modal({
                                show: true,
                                backdrop: 'static'
                            });

                            console.log("Modal disparado correctamente.");
                        } else {
                            console.log("PDF no está listo. Reintentando en 5 segundos...");

                            // Reintentar después de 5 segundos
                            setTimeout(checkPdfStatus, 5000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al verificar el estado del PDF:', error);
                        console.log("Código de estado HTTP:", xhr.status);
                        console.log("Respuesta del servidor:", xhr.responseText);

                        // Reintentar después de 5 segundos en caso de error
                        setTimeout(checkPdfStatus, 5000);
                    }
                });
            }
        });
    </script>
@endsection
