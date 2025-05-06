@extends('adminlte::page')
@section('title', 'OLTs')
@section('content_header')
    <div class="card p-3">
        <h2>ONT´S AUTORIZADAS</h2>
    </div>
@endsection
@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('success-update'))
        <div class="alert alert-warning">
            {{ session('success-update') }}
        </div>
    @elseif(session('success-delete'))
        <div class="alert alert-danger">
            {{ session('success-delete') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Slot</th>
                        <th>Puerto</th>
                        <th>Onu Id</th>
                        <th>Service Port</th>
                        <th>Serial</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Potencia</th>
                        <th>Modelo</th>
                        <th>Vlan</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($onts as $ont)
                        <tr>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $onts->links() }}
        </div>
    </div>
@endsection
