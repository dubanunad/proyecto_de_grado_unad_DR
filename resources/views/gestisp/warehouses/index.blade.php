@extends('adminlte::page')

@section('title', 'Almacenes')

@section('content_header')
    <div class="card p-3">
        <h2>ADMINISTRAR ALMACENES</h2>
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

    <div class="card p-3">
        <div class="d-flex justify-content-end">
            <a href="{{ route('warehouses.create') }}" class="btn btn-primary">Nuevo almacén</a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <table-body>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario vinculado</th>
                        <th></th>
                    </tr>
                    @foreach($warehouses as $warehouse)
                        <tr>
                            <td>{{ $warehouse->description }}</td>
                            <td>{{ $warehouse->user->name ?? 'N/A'}} {{ $warehouse->user->last_name ?? 'N/A'}}</td>
                            <td></td>
                            <td class="text-right">
                                <a class="btn btn-primary" href="{{ route('warehouses.show', $warehouse) }}">Ver inventario</a>
                                <a href="{{route('warehouses.edit', $warehouse)}}" class="btn btn-warning">Editar</a>
                            </td>
                        </tr>

                    @endforeach
                </table-body>
            </table>
        </div>

        <div class="text-center">
            {{ $warehouses->links() }}
        </div>
    </div>
@endsection
