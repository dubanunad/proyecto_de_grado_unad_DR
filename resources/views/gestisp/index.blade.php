@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')

    <div class="card mt-3">
        <div class="card-head p-3 text-center">
            <p>Hola, <strong>{{ Auth::user()->name }} {{ Auth::user()->last_name }}</strong> Bienvenido a
                <img src="{{ asset('img/Logo-gestisp-solo-texto.png') }}" alt="GestISP" width="80px">. Estás en la empresa <strong>{{ $branch->name }}</strong> Tu Rol es {{ $rol->name }}</p>
        </div>
        <div class="card-body">
            <div class="text-center p-4">
                <img src="{{ asset('storage/'.$branch->image) }}" alt="Logo de la sucursal" width="200px">
            </div>
        </div>
    </div>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Bienvenido a gestISP, una nueva revolución en gestión integral del ISP!"); </script>
@stop
