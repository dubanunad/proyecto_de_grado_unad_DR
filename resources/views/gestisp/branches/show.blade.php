@extends('adminlte::page')

@section('title', 'Detalles de Sucursal')

@section('content')
    <div class="card mt-3">
        <div class="card-header">Detalles de Sucursal</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img width="200px" src="{{ asset('/storage/'.$branch->image) }}" alt="">
                </div>
                <div class="col-md-8">
                    <p><strong>Nit:</strong> {{ $branch->nit }}</p>
                    <p><strong>Nombre:</strong> {{ $branch->name }}</p>
                    <p><strong>País:</strong> {{ $branch->country }}</p>
                    <p><strong>Departamento:</strong> {{ $branch->department }}</p>
                    <p><strong>Municipio:</strong> {{ $branch->municipality }}</p>
                    <p><strong>Dirección:</strong> {{ $branch->address }}</p>
                    <p><strong>Teléfono:</strong> {{ $branch->number_phone }}</p>
                    <p><strong>Teléfono Adicional:</strong> {{ $branch->additional_number }}</p>
                    <p><strong>Precio de Traslado:</strong> {{ $branch->moving_price }}</p>
                    <p><strong>Precio de Reconexión:</strong> {{ $branch->reconnection_price }}</p>
                    <p><strong>Mensaje Personalizado:</strong> {{ $branch->message_custom_invoice }}</p>
                    <p><strong>Observaciones:</strong> {{ $branch->observation }}</p>
                    <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirmDelete();">Eliminar</button>
                    </form>
                </div>
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
