@extends('adminlte::page')

@section('title', 'Servicios')

@section('content_header')
    <div class="card p-3">
        <h2>ADMINISTRAR SERVICIOS</h2>
    </div>

@endsection

@section('content')
    @if(session('success-create'))
        <div class="alert alert-info">
            {{ session('success-create') }}
        </div>
    @elseif(session('success-update'))
        <div class="alert alert-info">
            {{ session('success-update') }}
        </div>
    @elseif(session('success-delete'))
        <div class="alert alert-info">
            {{ session('success-delete') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-end">
            <a class="btn btn-primary" href="{{ route('services.create') }}">Crear Servicio <i class="fas fa-plus-circle"></i></a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Precio Base</th>
                    <th>Porcentaje de impuesto (IVA)</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($services as $service)
                    <tr>
                        <td>{{ $service->name }}</td>
                        <td>{{ $service->base_price }}</td>
                        <td>{{ $service->tax_percentage }}</td>
                        <td class="text-right"><a class="btn btn-warning" href="{{ route('services.edit', $service) }}" title="Editar"><i class="fas fa-pencil-alt"></i> Modificar</a></a></td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
@endsection
