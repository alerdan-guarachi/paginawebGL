<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Inventario</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 75%;
            margin: 30px auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header img {
            width: 200px; /* Aumenté el tamaño del logo */

        }

        .header h3 {
            font-size: 22px; /* Reduje el tamaño del título */
            color: #2D3E50;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 14px;
            color: #7B8C99;
            margin: 5px 0;
        }

        .details {
            font-size: 15px;
            margin-bottom: 40px;
        }

        .details table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .details th, .details td {
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }

        .details th {
            background-color: #F9FAFB;
            color: #2D3E50;
            font-weight: bold;
        }

        .details td {
            color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <img src="{{ asset('img/logo.png') }}" alt="Logo">
        <h3>COMPROBANTE N° {{ $solicitud->id }}</h3>
        <p><strong>Ciudad:</strong> {{ $solicitud->sucursal }}</p>
        <p><strong>Fecha:</strong> {{ $fecha }}</p>
    </div>

    <div class="details">
        <table>
            <tr><th>Entregado por:</th><td>{{ $usuarioNombre }}</td></tr>
            <tr><th>Solicitante:</th><td>{{ $solicitud->usuariosolicitante }}</td></tr>
            <tr><th>Código del Producto:</th><td>{{ $solicitud->codigoproducto }}</td></tr>
            <tr><th>Producto Entregado:</th><td>{{ $solicitud->productoofertado }}</td></tr>
            <tr><th>Cantidad Entregada:</th><td>{{ $cantidadOfertada }} {{ $producto->unidadmedida }}</td></tr>
        </table>
    </div>
</div>

</body>
</html>
