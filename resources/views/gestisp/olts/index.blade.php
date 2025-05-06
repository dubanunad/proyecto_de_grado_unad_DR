@extends('adminlte::page')
@section('title', 'OLTs')
@section('content_header')
    <div class="card p-3">
        <h2>ADMINISTRAR OLT´S</h2>
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
        <div class="card-header d-flex justify-content-between">
            <h3 class="card-title">Lista de OLTs</h3>
            <div>
                <a class="btn btn-primary" href="{{ route('olts.create') }}">Agregar OLT <i class="fas fa-plus-circle"></i></a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Dirección IP</th>
                        <th>Estado</th>
                        <th>Temperatura</th>
                        <th>Uptime</th>
                        <th>ONUs</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($olts as $olt)
                        <tr>
                            <td>{{ $olt->name }}</td>
                            <td>{{ $olt->ip_address }}</td>
                            <td>
                                @if($olt->status_text === 'Conectado')
                                    <span class="badge bg-success">{{ $olt->status_text }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $olt->status_text }}</span>
                                @endif
                            </td>
                            <td><i class="fas fa-thermometer-half"></i> {{ $olt->temperature ?? 'N/A' }}</td>
                            <td><i class="fas fa-clock"></i> {{ $olt->uptime ?? 'N/A' }}</td>
                            <td>
                                <a href="" class="btn btn-sm btn-info">
                                    <i class="fas fa-network-wired"></i> ONUs
                                    <span class="badge bg-white text-primary"></span>
                                </a>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('olts.edit', $olt) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="if(confirm('¿Está seguro de eliminar esta OLT?')) document.getElementById('delete-form-{{ $olt->id }}').submit()">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $olt->id }}" action="{{ route('olts.destroy', $olt) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $olts->links() }}
        </div>
    </div>
@endsection
