@extends('adminlte::page')

@section('title', 'Editar Material')

@section('content_header')
    <div class="card p-3">
        <h2>EDITAR MATERIAL</h2>
    </div>

@endsection

@section('content')
    <div class="card p-3">
        <div>
            <form class="" action="{{route('materials.update', $material)}}" method="post">
                @csrf
                @method('PUT')
                <div>
                    <label for="" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $material->name }}">
                </div>
                <div>
                    <label for="" class="form-label">Categoría</label>
                    <select class="form-control form-select" aria-label="Default select example" name="category_id" id="category_id" required>
                        <option>Seleccione una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $material->category_id == $category->id ? 'selected' : ''}}>
                                {{ $category->name }}

                            </option>
                        @endforeach
                    </select>
                </div>
                <label>¿Es equipo?</label>
                <div class="form-group">
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">NO</label>
                        <input class="form-check-input ml-2" type="radio" name='is_equipment' id="is_equipment" value="0" {{ ($material->is_equipment== 0) ? 'checked' : '' }}>
                    </div>

                    <div class="form-check form-check-inline">
                        <label class="form-check-label">SI</label>
                        <input class="form-check-input ml-2" type="radio" name='is_equipment' id="is_equipment" value="1" {{ ($material->is_equipment== 1) ? 'checked' : '' }}>
                    </div>

                    <span class="text-danger">
                    <span>*</span>
                </span>
                </div>
                <div class="text-center mt-3">
                    <input type="submit" class="btn btn-primary col-md-3" value="Guardar Cambios">
                </div>
            </form>

            <div class="text-center mt-3">
                <form action="{{ route('materials.destroy', $material) }}" method="post">
                    @method('DELETE')
                    @csrf
                    <input type="submit" class="btn btn-danger col-md-3" value="Eliminar Material" onclick="return confirmDelete();">
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
