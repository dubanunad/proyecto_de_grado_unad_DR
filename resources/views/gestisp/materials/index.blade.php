@extends('adminlte::page')

@section('title', 'Materiales')

@section('content_header')
    <div class="card p-3">
        <h2>ADMINISTRAR MATERIALES</h2>
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
            <a href="{{ route('categories.index') }}" class="btn btn-warning mr-md-2">Categorías</a>
            <a href="{{ route('materials.create') }}" class="btn btn-primary">Nuevo material</a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <table-body>
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Es equipo</th>
                        <th></th>
                    </tr>
                    @foreach($materials as $material)
                        <tr>
                            <td>{{ $material->name }}</td>
                            <td>{{ $material->category->name }}</td>
                            <td>
                                <input type="checkbox" name="is_equipment" id="is_equipment" class="form-check-input ml-4"
                                       {{ $material->is_equipment ? 'checked="checked"' : '' }}
                                       disabled>
                            </td>
                            <td class="text-right"><a href="{{route('materials.edit', $material)}}" class="btn btn-warning">Editar</a></td>
                        </tr>

                    @endforeach
                </table-body>
            </table>
        </div>

        <div class="text-center">
            {{ $materials->links() }}
        </div>
    </div>
@endsection
