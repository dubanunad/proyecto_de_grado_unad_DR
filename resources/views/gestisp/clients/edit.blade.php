@extends('adminlte::page')

@section('title', 'Crear cliente')

@section('content_header')
    <h2>Editar datos de cliente</h2>
@endsection


@section('content')

    @if(session('success-update'))
        <div>
            <p class="text-success">{{ session('success-update') }}</p>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('clients.update', $client) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="type_client" class="form-label">Tipo de cliente</label>
                        <select class="form-select form-control" aria-label="Default select example" id="type_client" name="type_client" disabled="disabled">
                            <option selected>{{ $client->type_client }}</option>
                        </select>
                    </div>

                    <div class="mb-3  col-md-6">
                        <label for="type_client" class="form-label">Tipo de documento</label>
                        <select class="form-select form-control" aria-label="Default select example" id="type_document" name="type_document" disabled="disabled">
                            <option selected>{{$client->type_document}}</option>
                        </select>
                    </div>


                    <div class="mb-3  col-md-6">
                        <label for="identity_number" class="form-label">Número de identidad</label>
                        <input type="text" class="form-control" id="identity_number" name="identity_number" value="{{ $client->identity_number }}" disabled="disabled">
                    </div>
                    <div class="mb-3  col-md-6">
                        <label for="name" class="form-label">Nombres</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $client->name }}" disabled="disabled">
                    </div>
                    <div class="mb-3  col-md-6">
                        <label for="last_name" class="form-label">Apellidos</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $client->last_name }}" disabled="disabled">
                    </div>
                    <div class="mb-3  col-md-6">
                        <label for="number_phone" class="form-label">Número de teléfono</label>
                        <input type="text" class="form-control" id="number_phone" name="number_phone" value="{{ $client->number_phone }}">
                    </div>
                    <div class="mb-3  col-md-6">
                        <label for="aditional_phone" class="form-label">Número de teléfono adicional</label>
                        <input type="text" class="form-control" id="aditional_phone" name="aditional_phone" value="{{ $client->aditional_phone }}">
                    </div>
                    <div class="mb-3  col-md-6">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $client->email }}">
                    </div>
                    <div class="col-12 text-center">
                        <input type="submit" value="Actualizar cliente" class="btn btn-primary">
                        <form action="{{ route('clients.destroy', $client) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="submit" value="Eliminar" class="btn btn-danger" onclick="return confirmDelete();">
                        </form>
                    </div>

                </div>



            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function confirmDelete() {
            return confirm('Esta es una acción drástica, todos los contratos vinculados al cliente se eliminarán, después de eliminar no habrá vuelta atrás, ¿está seguro?');
        }
    </script>
@endsection
