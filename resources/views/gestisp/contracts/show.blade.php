@extends('adminlte::page')

@section('title', 'Detalles de contrato')

@section('content_header')
    <h4 class="text-center">DETALLES DEL CONTRATO</h4>
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

    @if(session('success'))
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="successModalLabel">Éxito</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{ session('success') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
        <div class="row d-flex justify-content-center">

            <div class="card col-md-10">
                <div class="card-head d-flex justify-content-between p-3">
                    <p><strong>Número de contrato:</strong> {{$contract->id}}</p>
                    <p><strong>Estado:</strong>
                        <strong
                            @if($contract->status == 'Activo') class="text-success"
                            @else class="text-danger"
                            @endif
                        >
                            {{ $contract->status }}
                        </strong>
                    </p>
                </div>
            </div>
            <div class="card col-md-5 ml-md-1 mr-md-1">
                <div class="card-header row">
                    <div class="col-md-9 col-8">
                        <h3><i class="far fa-user"></i> Datos personales</h3>
                    </div>

                    <div class="col-4 col-md-3 text-right">
                        <a href="" class="btn btn-info"><i class="fas fa-edit"></i></a>
                    </div>
                </div>
                <div class="card-body row">
                    <p class="col-6"><strong>Número de Documento:</strong> {{ $contract->client->identity_number }}</p>
                    <p class="col-6"><strong>Nombre completo:</strong> {{ $contract->client->name }} {{ $contract->client->last_name }}</p>
                    <p class="col-6"><strong>Tipo de cliente:</strong> {{ $contract->client->type_client }}</p>
                    <p class="col-6"><strong>Teléfono:</strong> {{ $contract->client->number_phone }}</p>
                    <p class="col-6"><strong>Teléfono adicional:</strong> {{ $contract->client->aditional_phone }}</p>
                    <p class="col-6"><strong>Email:</strong> {{ $contract->client->email }}</p>
                    <p class="col-6"><strong>Fecha de nacimiento:</strong> {{ $contract->client->birthday }}</p>
                    <p class="col-6"><strong>Creado por:</strong> {{ $contract->client->user->name }}</p>
                </div>
            </div>

            <div class="card col-md-5 ml-md-1 mr-md-1">
                <div class="card-header row">

                    <div class="col-8 col-md-9">
                        <h3><i class="fas fa-map-marked-alt"></i> Datos de residencia</h3>
                    </div>
                    <div class="col-4 col-md-3 text-right">
                        <a href="" class="btn btn-info"><i class="fas fa-edit"></i></a>
                    </div>
                </div>
                <div class="card-body row">
                    <p class="col-6"><strong>Barrio:</strong> {{ $contract->neighborhood }}</p>
                    <p class="col-6"><strong>Dirección:</strong> {{ $contract->address }}</p>
                    <p class="col-6"><strong>Tipo de vivienda:</strong> {{ $contract->home_type }}</p>
                    <p class="col-6"><strong>Estrato social:</strong> {{ $contract->social_stratum }}</p>
                </div>
            </div>
            <div class="card col-md-5 ml-md-1 mr-md-1">
                <div class="card-header row">
                    <div class="col-8 col-md-9">
                        <h3><i class="fas fa-network-wired"></i> Datos del servicio</h3>
                    </div>

                    <div class="col-4 col-md-3 text-right">
                        <a href="" class="btn btn-info"><i class="fas fa-edit"></i></a>
                    </div>
                </div>

                <div class="card-body row">
                    <p class="col-6"><strong>Plan de servicio:</strong> {{ $contract->plan->name }}</p>
                    <p class="col-6"><strong>Clausula de permanencia:</strong> {{ $contract->permanence_clause }} Meses</p>
                </div>
            </div>
            <div class="card col-md-5 ml-md-1 mr-md-1">
                <div class="card-header row">
                    <div class="col-8 col-md-9">
                        <h3><i class="fas fa-cogs"></i> Datos técnicos</h3>
                    </div>

                    <div class="col-4 col-md-3 text-right">
                        <a href="" class="btn btn-info"><i class="fas fa-edit"></i></a>
                    </div>
                </div>
                <div class="card-body row">
                    <p class="col-6"><strong>NAP y puerto:</strong> {{ $contract->nap_port }}</p>
                    <p class="col-6"><strong>Serial del CPE:</strong> {{ $contract->cpe_sn }}</p>
                    <p class="col-6"><strong>Usuario PPPoE:</strong> {{ $contract->user_pppoe }}</p>
                    <p class="col-6"><strong>Contraseña PPPoE:</strong> {{ $contract->password_pppoe }}</p>
                    <p class="col-6"><strong>SSID del Wifi:</strong> {{ $contract->ssid_wifi }}</p>
                    <p class="col-6"><strong>Contraseña del Wifi:</strong> {{ $contract->password_wifi }}</p>
                    <p class="col-6"><strong>Contrato realizado por:</strong> {{ $contract->user->name }} {{ $contract->user->last_name }} </p>
                    <p class="col-6"><strong>Fecha de creación:</strong> {{ $contract->created_at }} </p>
                    <p class="col-6"><strong>Última actualización:</strong> {{ $contract->updated_at }} </p>

                </div>
            </div>

            <div class="card col-md-10">
                <div class="card-header">
                    <h3><i class="fas fa-location-arrow"></i> Acciones con el contrato</h3>
                </div>
                <div class="card-body text-center">
                    <a href="{{ route('technicals_orders.create', $contract) }}" class="btn btn-info mb-1 mt-1 col-8 col-md-3" title="Crear incidencia a contrato">Crear orden técnica</a>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-success mb-1 mt-1 col-8 col-md-3" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        Agregar Cargo Adicional
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">AGREGAR CARGO A CONTRATO</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('additionalCharges.store') }}">
                                        @csrf

                                        <input type="text" value="{{$contract->id}}" name="contract_id" hidden="hidden">

                                        <!-- Description -->
                                        <div class="form-group mb-3">
                                            <label for="description">Descripción</label>
                                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" required>
                                            @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Amount -->
                                        <div class="form-group mb-3">
                                            <label for="amount">Monto</label>
                                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required>
                                            @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>



                                        <!-- Submit Button -->
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Guardar</button>
                                        </div>

                                    </form>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="col-md-10 mt-2">

                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="contractTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="account-status-tab" data-toggle="tab" href="#account-status" role="tab" aria-controls="account-status" aria-selected="true">
                                    Estado de Cuenta
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="additional-charges-tab" data-toggle="tab" href="#additional-charges" role="tab" aria-controls="additional-charges" aria-selected="false">
                                    Cargos adicionales
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="operation-history-tab" data-toggle="tab" href="#operation-history" role="tab" aria-controls="operation-history" aria-selected="false">
                                    Historial de Operaciones
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="contract-comments-tab" data-toggle="tab" href="#contract-comments" role="tab" aria-controls="contract-comments" aria-selected="false">
                                    Comentarios sobre el Contrato
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="contractTabsContent">
                            <!-- Estado de Cuenta -->
                            <div class="tab-pane fade show active" id="account-status" role="tabpanel" aria-labelledby="account-status-tab">
                                <div class="mt-3">
                                    <h4>Estado de Cuenta</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <tbody>
                                                <tr>
                                                    <th>Factura</th>
                                                    <th>Mes</th>
                                                    <th>Saldo</th>
                                                    <th>Estado</th>
                                                </tr>

                                                    @foreach($invoices as $invoice)
                                                        <tr>
                                                        <td>{{ $invoice->id }}</td>
                                                        <td>{{ $invoice->billed_month_name }}</td>
                                                        <td>{{ $invoice->total }}</td>
                                                        <td>{{ $invoice->status }}</td>
                                                        </tr>
                                                    @endforeach

                                            </tbody>
                                        </table>
                                        <div>
                                            {{ $invoices->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cargos adicionales -->
                            <div class="tab-pane fade" id="additional-charges" role="tabpanel" aria-labelledby="additional-charges-tab">
                                <div class="mt-3">
                                    <h4>Cargos adicionales</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <tbody>
                                                <tr>
                                                    <th>Concepto</th>
                                                    <th>Valor</th>
                                                    <th>Estado</th>
                                                    <th>Fecha de creación</th>
                                                    <th>Creado por</th>
                                                </tr>
                                                @foreach($additionalCharges as $addionalChrage)
                                                    <tr>
                                                        <td>{{ $addionalChrage->description }}</td>
                                                        <td>{{ $addionalChrage->amount }}</td>
                                                        <td>{{ $addionalChrage->status }}</td>
                                                        <td>{{ $addionalChrage->created_at }}</td>
                                                        <td>{{ $addionalChrage->user->name }} {{ $addionalChrage->user->last_name }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Historial de Operaciones -->
                            <div class="tab-pane fade" id="operation-history" role="tabpanel" aria-labelledby="operation-history-tab">
                                <div class="mt-3">
                                    <h4>Historial de Operaciones</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <tr>
                                                <th>Número de orden</th>
                                                <th>Tipo de orden</th>
                                                <th>Detalle de orden</th>
                                                <th>Comentario inicial</th>
                                                <th>Técnico asignado</th>
                                                <th>Fecha de creación</th>
                                                <th>Creada por</th>
                                                <th>Estado</th>
                                                <th></th>
                                            </tr>
                                            @foreach($technicalOrders as $technicalOrder)
                                                <tr>
                                                    <td>{{ $technicalOrder->id }}</td>
                                                    <td>{{ $technicalOrder->type }}</td>
                                                    <td>{{ $technicalOrder->detail }}</td>
                                                    <td>{{ $technicalOrder->initial_comment }}</td>
                                                    <td>{{ $technicalOrder->assignedUser->name ?? 'N/A'}} {{ $technicalOrder->assignedUser->last_name ?? 'N/A'}}</td>
                                                    <td>{{ $technicalOrder->created_at }}</td>
                                                    <td>{{ $technicalOrder->createdBy->name }} {{ $technicalOrder->createdBy->last_name }}</td>
                                                    <td>{{ $technicalOrder->status }}</td>
                                                    <td>
                                                        <!-- Button trigger modal -->
                                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#detailModal{{ $technicalOrder->id }}">
                                                            Ver detalles
                                                        </button>

                                                        <div class="modal fade" id="detailModal{{ $technicalOrder->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Detalles de orden {{ $technicalOrder->id }}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="">
                                                                            <div class="mt-2">
                                                                                <p><strong>Datos del cliente</strong></p>
                                                                            </div>
                                                                            <div>Cliente: {{ $technicalOrder->contract->client->name }} {{ $technicalOrder->contract->client->last_name }}</div>
                                                                            <div>Barrio y dirección: {{ $technicalOrder->contract->neighborhood }} {{ $technicalOrder->contract->address }}</div>
                                                                            <div>Detalles de plan: {{ $technicalOrder->contract->plan->name }}</div>
                                                                            <div class="mt-2">
                                                                                <p><strong>Datos de orden</strong></p>
                                                                            </div>
                                                                            <div>Tipo de orden: {{ $technicalOrder->type }}</div>
                                                                            <div>Detalle: {{ $technicalOrder->detail }}</div>
                                                                            <div>Detalle: {{ $technicalOrder->detail }}</div>
                                                                            <div>Comentario inicial: {{ $technicalOrder->initial_comment }}</div>
                                                                            <div class="mt-2">
                                                                                <p><strong>Datos de solución</strong></p>
                                                                            </div>
                                                                            <div>Observaciones técnicas: {{ $technicalOrder->observations_technical }}</div>
                                                                            <div>Observaciones del cliente: {{ $technicalOrder->client_observation }}</div>
                                                                            <div>Solución: {{ $technicalOrder->solution }}</div>
                                                                            <div>Fecha de creación: {{ $technicalOrder->created_at }}</div>
                                                                            <div>Motivo de rechazo por el técnico: {{ $technicalOrder->rejection_reason ?? 'N/A' }}</div>
                                                                            <div>Última acción: {{ $technicalOrder->updated_at }}</div>
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
                                                                                @foreach($technicalOrder->materials as $material_to_order)
                                                                                    <td>{{ $material_to_order->material->name }}</td>
                                                                                    <td>{{ $material_to_order->quantity }}</td>
                                                                                    <td>{{ $material_to_order->serial_number }}</td>
                                                                                @endforeach
                                                                            </table>
                                                                        </div>
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
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Comentarios sobre el Contrato -->
                            <div class="tab-pane fade" id="contract-comments" role="tabpanel" aria-labelledby="contract-comments-tab">
                                <div class="mt-3">
                                    <h4>Comentarios sobre el Contrato</h4>
                                    <p>Aquí se pueden agregar o visualizar comentarios sobre el contrato.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar automáticamente el modal si existe un mensaje de éxito o error
        @if(session('success'))
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        @endif

        @if(session('error'))
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
        @endif
    </script>
@endsection
