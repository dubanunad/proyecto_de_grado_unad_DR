@extends('adminlte::page')

@section('title', 'Planes')

@section('content_header')
    <div class="card p-3">
        <h2>ADMINISTRAR PLANES DE SERVICIO</h2>
    </div>
@endsection

@section('content')
    @if(session('success-create'))
        <div class="alert alert-success">
            {{ session('success-create') }}
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
        <div class="card-header d-flex justify-content-end">
            <a class="btn btn-primary" href="{{ route('plans.create') }}">Crear Plan <i class="fas fa-plus-circle"></i></a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($plans as $plan)
                    <tr>
                        <td>{{ $plan->name }}</td>
                        <td class="text-right"><a class="btn btn-warning" href="{{ route('plans.edit', $plan->id) }}" title="Editar Plan"><i class="fas fa-pencil-alt"></i> Modificar</a></a></td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
@endsection
