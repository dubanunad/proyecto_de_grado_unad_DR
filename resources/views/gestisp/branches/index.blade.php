@extends('adminlte::page')

@section('title', 'Sucursales')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between">

            <div class="col-md-6">
                <h2>ADMINISTRAR SUCURSALES</h2>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a class="btn btn-primary" href="{{ route('branches.create') }}">Crear sucursal <i class="fas fa-plus-circle"></i></a>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIT</th>
                        <th>NOMBRE</th>
                        <th>MUNICIPIO</th>
                        <th>DEPARTAMENTO</th>
                        <th>DIRECCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branches as $branch)
                    <tr>
                        <td>{{ $branch->nit }}</td>
                        <td>{{ $branch->name }}</td>
                        <td>{{ $branch->municipality }}</td>
                        <td>{{ $branch->department }}</td>
                        <td>{{ $branch->address }}</td>
                        <td class="text-right"><a class="btn btn-success" href="{{ route('branches.show', $branch) }}" title="Ver más"><i class="far fa-eye"></i> Ver más</a></td>
                        <td class="text-left"><a class="btn btn-warning" href="{{ route('branches.edit', $branch) }}" title="Editar"><i class="fas fa-pencil-alt"></i> Modificar</a></td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
@endsection
