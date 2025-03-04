@extends('adminlte::page')

@section('title', 'Planes')

@section('content_header')
    <div class="card p-3">
        <h2>ADMINISTRAR USUARIOS</h2>
    </div>
@endsection

@section('content')
    @if(session('success-create'))
        <div class="alert alert-success">
            {{ session('success-create') }}
        </div>
    @elseif(session('success-update'))
        <div class="alert alert-success">
            {{ session('success-update') }}
        </div>
    @elseif(session('success-delete'))
        <div class="alert alert-danger">
            {{ session('success-delete') }}
        </div>

    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-end">
            <a class="btn btn-primary" href="{{ route('users.create') }}">Crear usuario <i class="fas fa-plus-circle"></i></a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Identificación</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->identity_number}}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->number_phone }}</td>
                        <td class="text-right"><a class="btn btn-warning" href="{{ route('users.edit', $user) }}" title="Editar usuario"><i class="fas fa-pencil-alt"></i> Modificar</a></a></td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
        <div>
            {{ $users->links() }}
        </div>
    </div>
@endsection
