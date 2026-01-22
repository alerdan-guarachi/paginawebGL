<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORDEN DE COMPRA</title>
    <style>
        @page {
            size: 8.5in 11in;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 1cm 0.2cm 3.5cm 0.2cm;
            padding: 0;
            color: #000;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
            font-size: 12px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header img {
            width: 150px;
        }

        /* .header-right {
             text-align: right;
             font-size: 10px;
         } */
        h1 {
            text-align: center;
            font-size: 26px;
            margin-top: 0;
        }

        /* .company-info {
             text-align: left;
             font-size: 10px;
             margin-top: 10px;
         } */
        .client-personal {
            background-color: orange;
            color: white;
            padding: 5px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }
        .details {
            margin-top: 10px;
        }

        .details-header {
            background-color: green;
            color: white;
            text-align: center;
            padding: 5px;
            font-weight: bold;
            margin-top: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
            border: 1px solid black;
            /* Borde grueso para toda la tabla */
        }

        .table th,
        .table td {
            padding: 5px 8px; /* Espaciado entre filas similar a un margen inferior de 5px */
            text-align: center;
            /* Centrar el texto en cada celda */
            font-size: 10px;
        }

        .table th {
            background-color: #94c93b;
            color: white;
            border: 1px solid black;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            /* Sombra ligera */
        }

        .table td {
            border-left: 1px solid black;
            /* Bordes laterales gruesos entre columnas */
            border-right: 1px solid black;
            border-bottom: 1px solid lightgray;
            /* Bordes finos entre filas */
        }

        .table tr:last-child td {
            border-bottom: 1px solid black;
            /* Borde inferior grueso en la última fila */
        }

        .totals {
            text-align: right;
            margin-top: 10px;
            font-size: 14px;
        }

        .totals p {
            margin: 0;
            font-size: 12px;
        }

        .info-container {
            display: flex;
            justify-content: space-between;
            /* Espacia los elementos entre el inicio y el final del contenedor */
            margin-top: 10px;
            /* Ajusta el margen según sea necesario */
        }

        .company-info {
            text-align: left;
            /* Asegura que el texto esté alineado a la izquierda */
            font-size: 10px;
        }

        .header-right {
            text-align: right;
            /* Asegura que el texto esté alineado a la derecha */
            font-size: 10px;
            /* Mantiene el mismo tamaño de fuente que tenías */
            margin-top: -100px;
        }

        .custom-info-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 12px; /* Alinea los elementos en la parte superior */
            margin-bottom: -20px; /* Quita el margen inferior */
            padding-bottom: 35px; /* Mantiene el espaciado en la parte inferior */
        }

        .custom-company-info {
            font-size: 10px;
            text-align: left;
            flex: 1;
            padding-right: 20px;
        }

        .custom-header-right {
            font-size: 10px;
            text-align: right;
            align-self: flex-start;
            margin-top: -180px; /* Alinea este bloque en la parte superior */
            margin-bottom: 35px;
            /* Agrega el mismo margen inferior para igualar el espaciado */
        }

        .custom-company-info p,
        .custom-header-right p {
            margin: 0 0 5px 0;
        }

        th:last-child,
        td.precio {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <header style="margin-top: -25px;">
            <img src="{{ public_path('membrete/logogl.png') }}" alt="Logo">
            <h1 style="margin-top: -40px; text-align: right;">ORDEN DE COMPRA N° {{ $ordenId }}</h1>
        </header>

        <div class="info-container"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; margin-top: 25px;">
            <div class="company-info" style="flex: 1; margin-right: 20px;">
                <p style="margin: 0 0 5px 0;">NIT: 310634022</p>
                <p style="margin: 0 0 5px 0;">SANTA CRUZ: AV. RENE MORENO NRO 484 ESQ. ANA BARBA</p>
                <p style="margin: 0 0 5px 0;">COCHABAMBA: CALLE LANZA NRO 940 ENTRE AV. RAMON RIVERO Y ORURO</p>
                <p style="margin: 0;">TELÉFONO: 65045401 - 4507269 - 3259385</p>
            </div>

            <div class="header-right" style="text-align: right; flex: 1;">
                
                <p style="margin: 0; padding-bottom: 0;">TIPO DE TRANSACCIÓN:<span
                        style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">{{ $tipotransaccion ?? 'No especificado' }}</span>
                </p>
                
                <p style="margin: 0; padding-bottom: 0;">FORMA DE PAGO:<span
                        style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">{{ $formapago ?? 'No especificado' }}</span>
                </p>
                <p style="margin: 0; padding-bottom: 0;">FECHA DE COMPRA:<span
                    style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">{{ $fechacomprar ?? 'No especificado' }}</span>
                </p>
                <p style="margin: 0; padding-bottom: 0;">FECHA DE PAGO:<span
                        style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">{{ $fechapagar ?? 'No especificado' }}</span>
                </p>
            </div>
        </div>

        <div class="client-personal">
            <span style="margin-right: 520px;">PROVEEDOR</span>
        </div>

        <div class="custom-info-container">
            <div class="custom-company-info">
                <p>PROVEEDOR: {{ $proveedor->razonsocial ?? 'No disponible' }}</p>
                <p>TELÉFONO: {{ $proveedor->celular ?? 'No disponible' }}</p>
                <p>
                    CIUDAD: 
                    @if (!empty($proveedor->ciudad))
                        {{ $proveedor->ciudad }}@if (!empty($proveedor->ciudad2)), {{ $proveedor->ciudad2 }}@endif
                    @elseif (!empty($proveedor->ciudad2))
                        {{ $proveedor->ciudad2 }}
                    @else
                        No disponible
                    @endif
                </p>
                <p>BANCO: {{ $proveedor->banco ?? 'No disponible' }}</p>
                <p>NIT: {{ $proveedor->nit ?? 'No disponible' }}</p>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    {{-- <th>NRO.BANCO_ORIGEN</th> --}}
                    <th>DETALLE</th>
                    <th>CANTIDAD</th>
                    <th>PRECIO/UNID.</th>
                    <th>DESCUENTO/UNID.</th>
                    <th style="text-align: center;">TOTAL/UNID.</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($ordenesCompra) && $ordenesCompra->count())
                    @foreach ($ordenesCompra as $orden)
                        <tr>
                            <td>{{ $orden->detalle }}</td>
                            <td>{{ $orden->cantidad }}</td>
                            <td>{{ $orden->preciounitario }}</td>
                            <td>{{ $orden->descuentounitario }}</td>
                            <td>{{ $orden->totalunitario }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6">NO HAY REGISTROS DISPONIBLES</td>
                    </tr>
                @endif
            </tbody>
            
        </table>

        <div class="totals-container"
            style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 20px; page-break-inside: avoid;">
            <div class="company-info" style="width: 42.7%; padding: 5px; border: 2px solid #696969;">
                <div
                    style="border-bottom: 2px solid #696969; padding: 5px; background-color: #d3d3d3; text-align: left;">
                    <strong>OBSERVACIONES</strong>
                </div>
                {{ $observacion ?? 'SIN OBSERVACIONES' }}
            </div>

            <div class="totals" style="text-align: right; flex-grow: 1; margin-left: 20px;" style="margin-top: -300px;">
                <p style="margin: 0; padding-bottom: 0;"><strong>NETO:</strong><span
                        style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">{{ $subtotal ?? 0 }}</span>
                </p>
                <p style="margin: 0; padding-bottom: 0;"><strong>DESCUENTOS:</strong><span
                        style="border: 0.5px solid lightgray; border-bottom: 2px solid black; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">{{ $descuento ?? 0 }}</span>
                </p>
                <p style="margin: 0; padding-bottom: 0;"><strong>TOTAL:</strong><span
                        style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">{{ $montototal ?? 0 }}</span>
                </p>
                @if (!empty($saldo) && $saldo != 0)
                    <p style="margin: 0; padding-bottom: 0;">
                        <strong>SALDO:</strong>
                        <span style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">
                            {{ number_format($saldo, 2) }}
                        </span>
                    </p>
                @endif
            </div>
        </div>
        <p style="margin-top: 40px;"><strong>Encargado:</strong> {{ $usuarioregistro }}</p>
        {{-- <p><strong>Aprobado por:</strong> {{ $usuarioAutenticadonombre }}</p> --}}
    </div>
</body>
</html>