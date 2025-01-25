@extends('adminlte::page')

@section('title', 'Crear servicio')

@section('content_header')
    <div class="card p-3">
        <h2>CREAR SERVICIO</h2>
    </div>

@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="name">Nombre del servicio</label>
                    <input type="text" class="form-control" id="name" name='name'
                           placeholder="Ingrese el nombre del servicio" minlength="5" maxlength="255"
                           value="{{ old('name') }}">
                    @error('name')
                    <span class="text-danger">
                    <span>* {{ $message }}</span>
                </span>
                    @enderror
                </div>


                <div class="form-group">
                    <label>Precio base</label>
                    <input type="text" class="form-control" id="base_price" name='base_price'
                           placeholder="Ingrese el precio base del servicio"
                           value="{{ old('base_price') }}">

                    @error('base_price')
                    <span class="text-danger">
                    <span>* {{ $message }}</span>
                </span>
                    @enderror

                </div>

                <div class="form-group">
                    <label>Porcentaje IVA (si es el 19%, ingrese 0.19)</label>
                    <input type="text" class="form-control" id="tax_percentage" name='tax_percentage'
                           placeholder="Ingrese el porcentaje de IVA"
                           value="{{ old('tax_percentage') }}">

                    @error('tax_percentage')
                    <span class="text-danger">
                    <span>* {{ $message }}</span>
                </span>
                    @enderror

                </div>

                <div class="col-12 text-center">
                    <input type="submit" value="Agregar Servicio" class="btn btn-primary col-md-3">
                </div>



            </form>
        </div>
    </div>
@endsection


