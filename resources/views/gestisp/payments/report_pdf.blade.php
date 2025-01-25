<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pagos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
<h2>Reporte de Pagos</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Identidad Cliente</th>
        <th>Cliente</th>
        <th>Monto</th>
        <th>Fecha de Pago</th>
        <th>Cobrado por</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($payments as $payment)
        <tr>
            <td>{{ $payment->id }}</td>
            <td>{{ $payment->invoice->contract->client->identity_number }}</td>
            <td>{{ $payment->invoice->contract->client->name }} {{ $payment->invoice->contract->client->last_name }}</td>
            <td>{{ $payment->amount }}</td>
            <td>{{ $payment->payment_date }}</td>
            <td>{{ $payment->user->name }} {{ $payment->user->last_name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
