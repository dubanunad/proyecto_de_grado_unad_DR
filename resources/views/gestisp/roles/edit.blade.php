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
            <form method="POST" action="{{ route('roles.update', $role->id) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" class="form-control" id="name" name='name'
                           placeholder="Nombre del rol" value="{{ $role->name  }}">

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
                            <input type="checkbox" name="permissions[]" id="" value="{{ $permmision->id }}"
                                   {{ $role->hasPermissionTo($permmision->name) ? 'checked' : '' }}
                                   class="mr-1"> {{ $permmision->description }}

                        </label>
                    </div>
                @endforeach

                <input type="submit" value="Modificar rol" class="btn btn-primary">
            </form>
        </div>
        <div>
            <form action="{{ route('roles.destroy', $role) }}" method="POST" onclick="return confirmDelete();">
                @csrf
                @method('DELETE')
                <input type="submit" value="Eliminar" class="btn btn-danger m-3">
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function confirmDelete() {
            return confirm('Esta es una acción drástica, después de eliminar no habrá vuelta atrás, ¿está seguro?');
        }
    </script>
@endsection
