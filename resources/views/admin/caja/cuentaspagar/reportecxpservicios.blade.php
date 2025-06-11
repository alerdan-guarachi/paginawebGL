<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cuentas Pendientes - {{ $fecha }}</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h3 style="text-align: center;">CUENTAS POR PAGAR PENDIENTES DE: {{ $fecha }}</h3>
    <table>
        <thead>
            <tr>
                <th>ID Reg.</th>
                <th>Proveedor</th>
                <th>Tipo Orden</th>
                <th>Orden ID</th>
                <th>Detalle</th>
                {{-- <th>Fecha Pagar</th> --}}
                <th>N.Cuenta Origen</th>
                {{-- <th>Cant.</th> --}}
                <th>Subto.</th>
                <th>Desc.</th>
                <th>Total</th>
                {{-- <th>Estado</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($cuentas as $pendiente)
                <tr>
                    <td>{{ $pendiente->id }}</td>
                    <td>{{ $pendiente->proveedornombre }}</td>
                    <td>{{ $pendiente->tipoorden }}</td>
                    <td>{{ $pendiente->ordenid ?? 0 }}</td>
                    <td>{{ $pendiente->detalleproducto }}</td>
                    {{-- <td>{{ $pendiente->fechaasignada }}</td> --}}
                    <td>{{ $pendiente->nrobancoorigen ?? 0 }}</td>
                    {{-- <td>{{ $pendiente->cantidad ?? 0 }}</td> --}}
                    <td>{{ $pendiente->subtotal }}</td>
                    <td>{{ $pendiente->descuento }}</td>
                    <td>{{ $pendiente->montototal }}</td>
                    {{-- <td>{{ $pendiente->estado }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
