@extends('adminlte::page')

@section('title', 'Busqueda de cliente')

@section('content_header')
    <div class="card p-3">
        <h2>BUSCAR UN CLIENTE</h2>
    </div>

@endsection

@section('content')

        <div class="row d-flex justify-content-center">
            <div class="card col-md-11">
                <form method="POST" action="{{ route('clients.search') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="col-md-4 pt-3">
                        <div class="input-group mb-3">
                            <input id="identity_number"  name="identity_number" type="text" class="form-control" placeholder="Introduce un número de documento" aria-label="Introduce un número de documento" aria-describedby="button-addon2">
                            <button class="btn btn-info" type="submit" id="button-addon2"><i class="fas fa-search-plus"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if (isset($client))
            <div class="row d-flex justify-content-center">
                <div class="card col-md-4 ml-md-1">
                    <div class="card-header">
                        <h3><i class="far fa-user"></i> Datos personales</h3>
                        <div class="col-12 d-flex justify-content-end">
                            <a href="{{ route('clients.edit', $client) }}" title="Editar información del usuario" class="btn btn-success mr-2"><i class="fas fa-edit"></i></a>
                            <a href="{{ route('contracts.create', $client) }}" title="Agregar contrato al usuario" class="btn btn-danger"><i class="fas fa-file-signature"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><strong>Número de Documento:</strong> {{ $client->identity_number }}</p>
                        <p><strong>Nombre completo:</strong> {{ $client->name }} {{ $client->last_name }}</p>
                        <p><strong>Tipo de cliente:</strong> {{ $client->type_client }}</p>
                        <p><strong>Teléfono:</strong> {{ $client->number_phone }}</p>
                        <p><strong>Teléfono adicional:</strong> {{ $client->aditional_phone }}</p>
                        <p><strong>Email:</strong> {{ $client->email }}</p>
                        <p><strong>Fecha de nacimiento:</strong> {{ $client->birthday }}</p>
                        <p><strong>Creado por:</strong> {{ $client->user->name }}</p>
                    </div>
                </div>

                <div class="card col-md-7 ml-md-2 mr-md-2">
                    <div class="card-header">
                        <h3><i class="fas fa-file-contract"></i> Contratos asociados al cliente</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <th><p>ID del contrato</p></th>
                                    <th><p>Barrio</p></th>
                                    <th><p>Dirección</p></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                @foreach($contracts as $contract)
                                    <tr>
                                        <td>{{ $contract->id }}</td>
                                        <td>{{ $contract->neighborhood }}</td>
                                        <td>{{ $contract->address }}</td>
                                        <td><a href="{{ route('contracts.show', $contract) }}" class="btn btn-info">Detalles</a></td>
                                        <td><a href="" class="btn btn-warning">Editar</a></td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                    </div>
                </div>

            </div>





    @endif
    @if (session('error'))
        <div class="card pt-3 pl-3 p3">
           <p class="text-danger">{{ session('error') }}</p>
        </div>
    @endif
@endsection

