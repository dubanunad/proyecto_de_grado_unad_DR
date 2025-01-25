<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Caja</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h1>Reporte de Caja</h1>
<p><strong>Usuario:</strong> {{ $cashRegister->user->name }}</p>
<p><strong>Fecha de Apertura:</strong> {{ $cashRegister->opened_at }}</p>
<p><strong>Fecha de Cierre:</strong> {{ $cashRegister->closed_at }}</p>
<p><strong>Monto Inicial:</strong> {{ $cashRegister->initial_amount }}</p>
<p><strong>Monto Final:</strong> {{ $cashRegister->final_amount }}</p>
<p><strong>Notas de Apertura:</strong> {{ $cashRegister->opening_notes }}</p>
<p><strong>Notas de Cierre:</strong> {{ $cashRegister->closing_notes }}</p>

<h2>Transacciones</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Tipo</th>
        <th>Monto</th>
        <th>Método de Pago</th>
        <th>Descripción</th>
        <th>Fecha</th>
    </tr>
    </thead>
    <tbody>
    @foreach($cashRegister->transactions as $transaction)
        <tr>
            <td>{{ $transaction->id }}</td>
            <td>{{ $transaction->transaction_type }}</td>
            <td>{{ $transaction->amount }}</td>
            <td>{{ $transaction->payment_method }}</td>
            <td>{{ $transaction->description }}</td>
            <td>{{ $transaction->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
