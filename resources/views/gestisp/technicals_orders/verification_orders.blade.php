@extends('adminlte::page')

@section('title', 'Órdenes Técnicas')

@section('content_header')
    <div class="card p-3">
        <h2>VERIFICAR ÓRDENES TÉCNICAS</h2>
    </div>
@endsection

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            <p>{{ session('success') }}</p>
        </div>
    @elseif(session('warning'))
        <div class="alert alert-warning">
            <p>{{ session('warning') }}</p>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            <p>{{ session('error') }}</p>
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
                        <th>Tipo de orden</th>
                        <th>Detalle</th>
                        <th>Comentario inicial</th>
                        <th>Estado</th>
                        <th>Fecha de creación</th>
                        <th>Técnico asignado</th>
                        <th></th>
                    </tr>
                    @foreach($technical_orders as $technical_order)
                        <tr>
                            <td>{{ $technical_order->id }}</td>
                            <td>{{ $technical_order->contract->id }}</td>
                            <td>{{ $technical_order->contract->client->name }} {{ $technical_order->contract->client->last_name }}</td>
                            <td>{{ $technical_order->type }}</td>
                            <td>{{ $technical_order->detail }}</td>
                            <td>{{ $technical_order->initial_comment }}</td>
                            <td>{{ $technical_order->status }}</td>
                            <td>{{ $technical_order->created_at }}</td>
                            <td>{{ $technical_order->assignedUser->name ?? 'N/A' }} {{ $technical_order->assignedUser->last_name ?? 'N/A' }}</td>
                            <td>
                                @if($technical_order->status === 'Pendiente')
                                    <button class="btn btn-info" data-toggle="modal" data-target="#assignOrderModal{{ $technical_order->id }}">
                                        Asignar Orden
                                    </button>
                                @elseif($technical_order->status === 'Asignada')
                                    <button class="btn btn-warning" data-toggle="modal" data-target="#assignOrderModal{{ $technical_order->id }}">
                                        Reasignar Orden
                                    </button>
                                @endif
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#detailModal{{ $technical_order->id }}">
                                    Ver detalles
                                </button>

                                <div class="modal fade" id="detailModal{{ $technical_order->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Detalles de orden {{ $technical_order->id }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="">
                                                    <div class="mt-2">
                                                        <p><strong>Datos del cliente</strong></p>
                                                    </div>
                                                    <div>Cliente: {{ $technical_order->contract->client->name }} {{ $technical_order->contract->client->last_name }}</div>
                                                    <div>Barrio y dirección: {{ $technical_order->contract->neighborhood }} {{ $technical_order->contract->address }}</div>
                                                    <div>Detalles de plan: {{ $technical_order->contract->plan->name }}</div>
                                                    <div class="mt-2">
                                                        <p><strong>Datos de orden</strong></p>
                                                    </div>
                                                    <div>Tipo de orden: {{ $technical_order->type }}</div>
                                                    <div>Detalle: {{ $technical_order->detail }}</div>
                                                    <div>Detalle: {{ $technical_order->detail }}</div>
                                                    <div>Comentario inicial: {{ $technical_order->initial_comment }}</div>
                                                    <div class="mt-2">
                                                        <p><strong>Datos de solución</strong></p>
                                                    </div>
                                                    <div>Observaciones técnicas: {{ $technical_order->observations_technical }}</div>
                                                    <div>Observaciones del cliente: {{ $technical_order->client_observation }}</div>
                                                    <div>Solución: {{ $technical_order->solution }}</div>
                                                    <div>Fecha de creación: {{ $technical_order->created_at }}</div>
                                                    <div>Última acción: {{ $technical_order->updated_at }}</div>
                                                    <div class="mt-2">
                                                        <p><strong>Fotos</strong></p>
                                                        <div class="card">
                                                            <div id="carouselExample" class="carousel slide">
                                                                <div class="carousel-inner">
                                                                    <div class="carousel-item active">
                                                                        <img src="https://i.blogs.es/351454/conector/650_1200.jpg" class="d-block w-100" alt="...">
                                                                    </div>
                                                                    <div class="carousel-item">
                                                                        <img src="https://www.adslzone.net/app/uploads-adslzone.net/2023/01/ONT-2.jpg" class="d-block w-100" alt="...">
                                                                    </div>
                                                                    <div class="carousel-item">
                                                                        <img src="..." class="d-block w-100" alt="...">
                                                                    </div>
                                                                </div>
                                                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                    <span class="visually-hidden">Previous</span>
                                                                </button>
                                                                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                    <span class="visually-hidden">Next</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <tr>
                                                            <th>Material</th>
                                                            <th>Cantidad</th>
                                                            <th>SN</th>
                                                        </tr>
                                                        @foreach($technical_order->materials as $material_to_order)
                                                            <td>{{ $material_to_order->material->name }}</td>
                                                            <td>{{ $material_to_order->quantity }}</td>
                                                            <td>{{ $material_to_order->serial_number }}</td>
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="p-3">
                                                <form action="{{ route('technical_order.verification_process', $technical_order) }}" method="post">
                                                    @csrf
                                                    @method('put')
                                                    <label for="verification_comment">Comentario de verificación</label>
                                                    <textarea class="form-control" name="verification_comment" id="verification_comment" required></textarea>
                                                    <div class="text-center mt-2">
                                                        <button type="submit" name="close_order" class="btn btn-success">CERRAR ORDEN</button>
                                                        <button type="submit" name="reject_order" class="btn btn-danger">RECHAZAR ORDEN</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-center">
            {{ $technical_orders->links() }}
        </div>
    </div>
@endsection
@section('js')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
