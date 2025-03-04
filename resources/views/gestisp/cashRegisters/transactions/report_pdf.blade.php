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
<h1>Reporte de movimientos de caja</h1>

<h2>Movimientos</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Tipo</th>
        <th>Monto</th>
        <th>Método de Pago</th>
        <th>Descripción</th>
        <th>Fecha</th>
        <th>Usuario</th>
    </tr>
    </thead>
    <tbody>
    @foreach($transactions as $transaction)
        <tr>
            <td>{{ $transaction->id }}</td>
            <td>{{ $transaction->transaction_type }}</td>
            <td>{{ $transaction->amount }}</td>
            <td>{{ $transaction->payment_method }}</td>
            <td>{{ $transaction->description }}</td>
            <td>{{ $transaction->created_at }}</td>
            <td>{{ $transaction->user->name }} {{ $transaction->user->last_name }}  </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
