<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Cuentas por Pagar</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table, .table th, .table td { border: 1px solid black; }
        .table th, .table td { padding: 8px; text-align: center; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="title">Reporte de Cuentas por Pagar</div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Proveedor</th>
                {{-- <th>ID Cliente</th> --}}
                <th>Cliente</th>
                <th>Fecha Batería</th>
                <th>Estudio/Especialidad</th>
                <th>Pago</th>
                <th>Informe</th>
                <th>Factura</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($result as $item)
                @foreach ($item['acciones'] as $accion)
                    @if (!is_null($accion['informedocumentacion']) && is_null($accion['pagoservicioinforme']))
                        <tr>
                            <td>{{ $item['proveedorasignado'] }}</td>
                            {{-- <td>{{ $accion['id'] }}</td> --}}
                            <td>{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
                            <td>{{ $accion['fechabateria'] }}</td>
                            <td>{{ $accion['accionnombre'] }}</td>
                            <td>{{ $accion['preciocompra'] }}</td>
                            <td>{{ $accion['informedocumentacion'] ?? 'Pendiente' }}</td>
                            <td>{{ $accion['nrofacturaprog'] ?? 'Pendiente' }}</td>
                        </tr>
                    @endif
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
