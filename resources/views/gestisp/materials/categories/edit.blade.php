@extends('adminlte::page')

@section('title', 'Editar categoría')

@section('content_header')
    <div class="card p-3">
        <h2>EDITAR CATEGORÍA</h2>
    </div>

@endsection

@section('content')
    <div class="card p-3">
        <div>
            <form class="" action="{{route('categories.update', $category)}}" method="post">
                @csrf
                @method('PUT')
                <div>
                    <label for="" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}">
                </div>
                <div>
                    <label for="" class="form-label">Descripción</label>
                    <input type="text" class="form-control" id="description" name="description" value="{{ $category->description }}">
                </div>

                <div class="text-center mt-3">
                    <input type="submit" class="btn btn-primary col-md-3" value="Guardar Cambios">
                </div>
            </form>

            <div class="text-center mt-3">
                <form action="{{ route('categories.destroy', $category) }}" method="post">
                    @method('DELETE')
                    @csrf
                    <input type="submit" class="btn btn-danger col-md-3" value="Eliminar Categoría" onclick="return confirmDelete();">
                </form>
            </div>
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
