<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario - {{ strtoupper($warehouse->description) }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
<h2 class="text-center">Inventario de {{ strtoupper($warehouse->description) }}</h2>

<table>
    <thead>
    <tr>
        <th>Material</th>
        <th>Cantidad</th>
        <th>Unidad</th>
        <th>SNs</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($inventoriesData as $inventory)
        <tr>
            <td>{{ strtoupper($inventory['material']) }}</td>
            <td class="text-center">{{ $inventory['quantity'] }}</td>
            <td class="text-center">{{ strtoupper($inventory['unit_of_measurement']) }}</td>
            <td>{{ $inventory['sns'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>
<p><strong>Usuario:</strong> {{ Auth::user()->name }} {{ Auth::user()->last_name }}</p>
</body>
</html>
