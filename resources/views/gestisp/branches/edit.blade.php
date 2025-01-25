@extends('adminlte::page')

@section('title', 'Editar Sucursal')
@section('content_header')
    <div class="card p-3"><h2>EDITAR SUCURSAL</h2></div>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('branches.update', $branch->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="nit">Nit</label>
                        <input type="text" name="nit" class="form-control" value="{{ $branch->nit }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name">Nombre</label>
                        <input type="text" name="name" class="form-control" value="{{ $branch->name }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="country">País</label>
                        <input type="text" name="country" class="form-control" value="{{ $branch->country }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="department">Departamento</label>
                        <input type="text" name="department" class="form-control" value="{{ $branch->department }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="municipality">Municipio</label>
                        <input type="text" name="municipality" class="form-control" value="{{ $branch->municipality }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="address">Dirección</label>
                        <input type="text" name="address" class="form-control" value="{{ $branch->address }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="number_phone">Teléfono</label>
                        <input type="text" name="number_phone" class="form-control" value="{{ $branch->number_phone }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="additional_number">Teléfono Adicional</label>
                        <input type="text" name="additional_number" class="form-control" value="{{ $branch->additional_number }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="image">Cambiar Imagen</label>
                        <input type="file" name="image" class="form-control">
                        <div class="rounded mx-auto d-block text-center mt-2">
                            <img src="{{ asset('storage/'.$branch->image) }}" style="width: 250px">
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="moving_price">Precio de Traslado</label>
                        <input type="number" step="0.01" name="moving_price" class="form-control" value="{{ $branch->moving_price }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="reconnection_price">Precio de Reconexión</label>
                        <input type="number" step="0.01" name="reconnection_price" class="form-control" value="{{ $branch->reconnection_price }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="message_custom_invoice">Mensaje Personalizado</label>
                        <textarea name="message_custom_invoice" class="form-control">{{ $branch->message_custom_invoice }}</textarea>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="observation">Observaciones</label>
                        <textarea name="observation" class="form-control">{{ $branch->observation }}</textarea>
                    </div>

                    <div class="col-12 text-center">
                        <button  type="submit" class="btn btn-primary col-md-3">Actualizar</button>
                    </div>

                </div>

            </form>
        </div>
    </div>
@endsection
