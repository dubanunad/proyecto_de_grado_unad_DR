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
                                    <a href="{{ route('technicals_orders.show', $technical_order->id) }}" title="Ver y procesar" class="btn btn-success col-md-8 mt-2"><i class="fas fa-cogs"></i></a>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

