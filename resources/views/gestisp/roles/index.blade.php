@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <div class="card p-3">
        <h2>ADMINISTRAR ROLES</h2>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header text-right">
            <a class="btn btn-primary" href="{{ route('roles.create') }}">Crear rol</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th>Rol</th>
                    <th class="text-right"></th>
                </tr>

                @foreach($roles as $rol)
                    <tr>
                        <td>{{ $rol->name }}</td>
                        <td class="text-right"><a class="btn btn-warning" href="{{ route('roles.edit', $rol) }}">Modificar Rol</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
