@extends('adminlte::page')
@section('title', 'OLTs')
@section('content_header')
    <div class="card p-3">
        <h2>ONT´s Pendientes por activación</h2>
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

                <select class="form-control" name="olt" id="olt">
                    <option value="">Seleccione una OLT</option>
                    @foreach($olts as $olt)
                        <option value="{{$olt->id}}">{{ $olt->name }}</option>
                    @endforeach
                </select>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Ubicación (F/S/P)</th>
                        <th>Econtrada el</th>
                        <th>Acción</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">

        </div>
    </div>

    <script>
        document.getElementById('olt').addEventListener('change', function () {
            const oltId = this.value;
            const tbody = document.querySelector('table tbody');
            tbody.innerHTML = '';

            if (!oltId) return;

            fetch(`/public/olts/${oltId}/onts-autofind`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-danger">${data.error}</td></tr>`;
                        return;
                    }

                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="5">No hay ONTs en autofind.</td></tr>`;
                        return;
                    }

                    data.forEach(ont => {
                        const row = `<tr>
                    <td>${ont.ont_sn}</td>
                    <td>${ont.vendor}</td>
                    <td>${ont.equipment_id}</td>
                    <td>${ont.fspon}</td>
                    <td>${ont.autofind_time}</td>
                    <td><a href="" title="Activar ONT" class="btn btn-success"><i class="fas fa-check-square"></i></a></td>
                </tr>`;
                        tbody.innerHTML += row;
                    });
                })
                .catch(error => {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-danger">Error al cargar ONTs: ${error}</td></tr>`;
                });
        });
    </script>
@endsection
