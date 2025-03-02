@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <div class="card p-3">
        <h2>ADMINISTRAR ROLES</h2>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" class="form-control" id="name" name='name' placeholder="Nombre del rol"
                           value="{{ old('name') }}">

                    @error('name')
                    <span class="alert-red">
                    <span>*{{ $message }}</span>
                </span>
                    @enderror

                </div>
                <h3>Lista de permisos</h3>
                @foreach($permmisions as $permmision)
                    <div>
                        <label>
                            <input type="checkbox" name="permissions[]" id="" value="{{$permmision->id}}" class="mr-1">{{ $permmision->description }}

                        </label>
                    </div>
                @endforeach

                <input type="submit" value="Crear rol" class="btn btn-primary">
            </form>
        </div>
@endsection
