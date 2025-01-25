@extends('adminlte::page')

@section('title', 'Movimientos de caja')

@section('content_header')
    <div class="card p-3">
        <h2>MOVIMIENTOS DE CAJA</h2>
    </div>
@endsection

@section('content')
    <div class="row">

        <div class="card col-md-12 p-3">
            <div class="col-md-12 d-flex justify-content-end">
                <a href="{{ route('transactions.history') }}" class="btn btn-info ">Historial de movimientos</a>
            </div>

        </div>
        <div class="card col-md-12">
            <div class="card-body">
                <form id="transaction-form">
                    @csrf
                    <div class="form-group">
                        <label for="transaction_type">Tipo de Movimiento</label>
                        <select name="transaction_type" id="transaction_type" class="form-control" required>
                            <option value="Ingreso">Entrada</option>
                            <option value="Egreso">Salida</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Monto</label>
                        <input type="number" name="amount" id="amount" class="form-control" required min="0">
                    </div>
                    <div class="form-group">
                        <label for="payment_method">Método de Pago</label>
                        <select id="payment_method" name="payment_method" class="form-control" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea name="description" id="description" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar Movimiento</button>
                </form>
            </div>
        </div>



    </div>

    <script>
        document.getElementById('transaction-form').addEventListener('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            fetch('{{ route('transactions.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        alert(data.message);
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
@endsection
