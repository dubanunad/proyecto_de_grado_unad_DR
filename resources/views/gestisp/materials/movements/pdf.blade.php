<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Movimientos</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="header">
    <h2>Historial de Movimientos de Almacén</h2>
</div>
<table>
    <thead>
    <tr>
        <th>Fecha del movimiento</th>
        <th>Tipo del movimiento</th>
        <th>Almacén de origen</th>
        <th>Almacén de destino</th>
        <th>Material</th>
        <th>Cantidad</th>
        <th>Unidad de medida</th>
        <th>Serial</th>
        <th>Motivo</th>
        <th>Realizado por</th>
    </tr>
    </thead>
    <tbody>
    @foreach($movements as $movement)
        <tr>
            <td>{{ $movement->created_at }}</td>
            <td>{{ $movement->type }}</td>
            <td>{{ $movement->warehouseOrigin->description ?? 'N/A'}}</td>
            <td>{{ $movement->warehouseDestination->description ?? 'N/A'}}</td>
            <td>{{ $movement->material->name }}</td>
            <td>{{ $movement->quantity }}</td>
            <td>{{ $movement->unit_of_measurement }}</td>
            <td>{{ $movement->serial_number ?? 'N/A' }}</td>
            <td>{{ $movement->reason }}</td>
            <td>{{ $movement->user->name ?? 'N/A' }} {{ $movement->user->last_name ?? 'N/A' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
