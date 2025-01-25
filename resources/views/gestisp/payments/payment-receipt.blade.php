<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de Pago</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-info {
            margin-bottom: 30px;
        }
        .receipt-details {
            width: 100%;
            margin-bottom: 20px;
        }
        .receipt-details td {
            padding: 5px;
        }
        .payment-info {
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
        }
        .amount {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>RECIBO DE PAGO</h1>
    <h2>{{ $company['name'] }}</h2>
</div>

<div class="company-info">
    <p>{{ $company['address'] }}<br>
        Tel: {{ $company['phone'] }}<br>
        Email: {{ $company['email'] }}</p>
</div>

<table class="receipt-details">
    <tr>
        <td><strong>Recibo #:</strong></td>
        <td>{{ $payment->id }}</td>
        <td><strong>Fecha:</strong></td>
        <td>{{ $payment->payment_date->format('d/m/Y H:i') }}</td>
    </tr>
    <tr>
        <td><strong>Cliente:</strong></td>
        <td>{{ $payment->invoice->contract->client->name }}</td>
        <td><strong>Documento:</strong></td>
        <td>{{ $payment->invoice->contract->client->identity_number }}</td>
    </tr>
    <tr>
        <td><strong>Factura #:</strong></td>
        <td>{{ $payment->invoice->id }}</td>
        <td><strong>Contrato #:</strong></td>
        <td>{{ $payment->invoice->contract_id }}</td>
    </tr>
</table>

<div class="payment-info">
    <h3>Detalles del Pago</h3>
    <table class="receipt-details">
        <tr>
            <td><strong>Monto Pagado:</strong></td>
            <td class="amount">$ {{ number_format($payment->amount, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Método de Pago:</strong></td>
            <td>{{ ucfirst($payment->payment_method) }}</td>
        </tr>
        @if($payment->reference_number)
            <tr>
                <td><strong># Referencia:</strong></td>
                <td>{{ $payment->reference_number }}</td>
            </tr>
        @endif
        @if($payment->notes)
            <tr>
                <td><strong>Notas:</strong></td>
                <td>{{ $payment->notes }}</td>
            </tr>
        @endif
    </table>
</div>

<div class="footer">
    <p>Este documento es un comprobante válido de pago.<br>
        Recibido por: {{ $payment->user->name }}<br>
        Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
</div>
</body>
</html>
