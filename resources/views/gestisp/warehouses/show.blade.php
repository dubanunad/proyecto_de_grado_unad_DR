@extends('adminlte::page')

@section('title', 'Almacenes')

@section('content_header')
    <div class="card p-3">
        <h2>INVENTARIO DE {{ strtoupper($warehouse->description) }}</h2>
    </div>
@endsection

@section('content')

    <div class="card">
        <div class="card-head p-3">
            <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('warehouse.pdf', $warehouse->id) }}" class="btn btn-danger" title="Descargar PDF">
                    <i class="fas fa-file-pdf"></i>
                </a>
            </div>

        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Artículo</th>
                    <th>Cantidad</th>
                    <th>Unidad de medida</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($inventoriesData as $inventoryData)
                    <tr>
                        <td>{{ $inventoryData['material']->name }}</td>
                        <td>{{ $inventoryData['quantity'] }}</td>
                        <td>{{ $inventoryData['unit_of_measurement'] }}</td>
                        <td>
                            @if($inventoryData['material']->is_equipment)
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-{{ $inventoryData['material']->id }}">
                                    Ver SNs
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="text-center">
        {{ $inventoriesData->links() }}
    </div>
    @foreach($inventoriesData as $inventoryData)
        @if($inventoryData['material']->is_equipment)
            <!-- Modal -->
            <div class="modal fade" id="modal-{{ $inventoryData['material']->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel-{{ $inventoryData['material']->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel-{{ $inventoryData['material']->id }}">SNs de {{ $inventoryData['material']->name }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-hover sns-table" id="sns-table-{{ $inventoryData['material']->id }}">
                                    <thead>
                                    <tr>
                                        <th>SN</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($inventoryData['sns'] as $sn)
                                        <tr>
                                            <td>{{ $sn }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Activar DataTables en todas las tablas con clase 'sns-table'
            $('.sns-table').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" // Traducción al español
                }
            });
        });
    </script>
@endsection
