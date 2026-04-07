<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Créditos</title>
    <style>
        @page {
            size: 8.5in 11in;
            margin: 0;
        }
        body {
            margin: 1cm 2cm 1cm 2cm;
            background: transparent;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        
        .tipo1 {
            font-size: 15px;
            margin-top: 30px;
            margin-bottom: 10px;
            font-family: Arial, sans-serif;
            text-align: right;
        }
        .tipo2 {
            font-size: 15px;
            margin-top: 10px;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            text-align: left;
        }
        .tipo9 {
            font-size: 15px;
            margin-top: 10px;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            text-align: left;
            text-decoration: underline;
        }
        .tipo3 {
            font-size: 15px;
            font-weight: 1200;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            text-align: left;
            line-height: 0;
        }
        .tipo4 {
            font-size: 15px;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: left;
            text-decoration: underline;
            line-height: 0;
        }
        .tipo5 {
            font-size: 17px;
            font-weight: 1200;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: right;
            text-decoration: underline;
            line-height: 0;
        }
        .tipo6 {
            font-size: 14px;
            margin-top: 5px;
            margin-bottom: 5px;
            font-family: Arial, sans-serif;
            text-align: justify;
        }
        .tipo7 {
            font-size: 15px;
            font-weight: 1200;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: center;
            line-height: 0;
            margin-bottom: 20px;
        }
        .tipo8 {
            font-size: 15px;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: center;
            line-height: 0;
            margin-bottom: 20px;
        }
        .tipo9 {
            font-size: 15px;
            margin-top: 10px;
            margin-bottom: 5px;
            font-weight: 1200;
            font-family: Arial, sans-serif;
            text-align: left;
            text-decoration: underline;
        }
        .tipo10 {
            font-size: 12.5px;
            margin-top: 5px;
            margin-left: -5px;
            margin-bottom: 5px;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <main style="margin-top: 60px;">
        <div class="tipo3">CONSULTORA DE PENSIONES Y PREVISIÓN SOCIAL</div>
        <div class="tipo3">GOOD LIFE S.R.L.</div>
        <div class="tipo3">{{ $creditos[0]['SucursalCliente'] }}, {{ $fechaactual }}</div>

        <div class="tipo5">CARTA DE CRÉDITO N° {{ $nrocredito }}</div>
        <div class="tipo5">Ref.: Forma de Pago y Condiciones</div>

        <div class="tipo2">Señor(a)</div>
        <div class="tipo3">{{ $creditos[0]['clienteNombre'] }}</div>
        <div class="tipo3">CI: {{ $creditos[0]['clienteCI'] }}</div>

        <div class="tipo2">Estimado(a) Sr(a):</div>

        <div class="tipo6">
            La Consultora de Pensiones y Previsión Social <strong>GOOD LIFE S.R.L.</strong>, en mutuo acuerdo con usted, estipula la siguiente 
            forma de pago por concepto de <strong>{{ $detalles }}</strong> correspondiente al trámite de <strong>{{ $tramites }}</strong>, por un total de Bs {{ number_format($montocuotaTotal, 2, ',', '.') }}.
        </div>

        <div class="tipo9">Detalle de pagos.-</div>

        <!-- Tabla con bordes -->
<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px;">
    <thead>
        <tr>
            {{-- <th style="border: 1px solid black; padding: 3px;">ID Crédito</th> --}}
            <th style="border: 1px solid black; padding: 3px;">Fecha de Crédito</th>
            <th style="border: 1px solid black; padding: 3px;">Monto Cuota</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($creditos as $credito)
            <tr>
                {{-- <td style="border: 1px solid black; padding: 3px;">{{ $credito['id'] }}</td> --}}
                <td style="border: 1px solid black; padding: 3px;">{{ $credito['fechacredito'] }}</td>
                <td style="border: 1px solid black; padding: 3px;">{{ $credito['montocuota'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

        <div class="tipo6">
            Usted se compromete a cumplir con los pagos en las fechas establecidas. En caso de incumplimiento, se aplicará una penalidad del <strong>10% de interés</strong> sobre el saldo adeudado.
        </div>
        <div class="tipo6">
            Adicionalmente, se le informará con <strong>un día de anticipación</strong> antes del vencimiento de su línea de crédito.
        </div>
        <div class="tipo6">
            La <strong>única persona autorizada</strong> para realizar el cobro es <strong>ADRIAN DAVID POMA CUELLAR</strong>, quien puede ser contactado al siguiente número: <strong>722-22963</strong>.
        </div>
        <div class="tipo6">
            Agradecemos su atención y compromiso con las condiciones establecidas.
        </div>
        <div class="tipo6">
            Atentamente,
        </div>
        <!-- Tabla sin bordes excepto para huellas -->
        <table width="100%" style="border-collapse: collapse; margin-top: 10px;"> 
            <tr>
                <td width="50%" style="vertical-align: top; text-align: left;">
                    <div class="tipo10"><strong>CONSULTORES</strong></div>
                    <div class="tipo10">LIC. JHOSELINE EVA VELASQUEZ ESCOBAR</div>
                    <div class="tipo10"><strong>Jefe Administrativa Y Financiera</strong></div>
                    <div class="tipo10"><strong>GOOD LIFE S.R.L.</strong></div>
                </td>
                <!-- Sección del interesado -->
                <td width="50%" style="text-align: right;">
                    <strong>Descripción:</strong> __________________________________________
                    <br><br>
                    <table style="border: none;">
                        <tr>
                            <td><strong>C.I.</strong> ____________________</td>
                            <td><strong>Exp.</strong> ____________________</td>
                        </tr>
                    </table>
                    <br>
                    <table width="100%" style="vertical-align: top; text-align: center; border: none;">
                        <tr>
                            <td style="border: 1px solid black; width: 100px; height: 80px;"></td>
                            <td style="border: 1px solid black; width: 100px; height: 80px;"></td>
                        </tr>
                        <tr>
                            <td><strong>PULGAR IZQUIERDO</strong></td>
                            <td><strong>PULGAR DERECHO</strong></td>
                        </tr>
                    </table>
                </td>

                <!-- Sección del consultor -->
                
            </tr>
        </table>
        
    </main>
</body>
</html>
