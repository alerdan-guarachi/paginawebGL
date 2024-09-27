<!DOCTYPE html>
<html>
<head>
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
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .tipo1 {
            font-size: 17px;
            margin-top: 30px;
            margin-bottom: 10px;
            font-family: Arial, sans-serif;
            text-align: right;
        }
        .tipo2 {
            font-size: 17px;
            margin-top: 10px;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            text-align: left;
        }
        .tipo9 {
            font-size: 17px;
            margin-top: 10px;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            text-align: left;
            text-decoration: underline;
        }
        .tipo3 {
            font-size: 17px;
            font-weight: 1200;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            text-align: left;
            line-height: 0;
        }
        .tipo4 {
            font-size: 17px;
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
            font-size: 17px;
            margin-top: 10px;
            margin-bottom: 10px;
            font-family: Arial, sans-serif;
            text-align: justify;
        }
        .tipo7 {
            font-size: 17px;
            font-weight: 1200;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: center;
            line-height: 0;
            margin-bottom: 20px;
        }
        .tipo8 {
            font-size: 17px;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: center;
            line-height: 0;
            margin-bottom: 20px;
        }
        .tipo10 {
            text-align: center;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            line-height: 0.5;
        }
        .table th {
            border: 1px solid #000;
            padding: 8px;
        }
        .table td {
            border: 1px solid #000;
            padding: 8px;
        }
    </style>
</head>
<body>
    <main>
        <div class="tipo1">Santa Cruz de la Sierra, {{ $fechaactual }}</div>
        <div class="tipo2">Señores:</div>
        <div class="tipo3">Gestora Publica de la Seguridad Social</div>
        <div class="tipo3">de Largo Plazo.</div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5">REF.:  SOLICITUD DE ACTUALIZACIÓN DE DATOS</div>
        <div class="tipo5">(TRÁMITE DE {{ $tipocartareclamo }})</div>
        <div class="tipo2">Distinguidos Señores:</div>
        <div class="tipo6">
        Yo, el Sr. {{$personal->nombrecompleto}}, con documento de Identidad {{ $personal->ci }}. En Calidad de Apoderado con N.º de poder {{ $numeropoder }}, del Afiliado {{$cliente->nombrecompleto}} con CUA N.º {{$cliente->nuacua}}, con C.I. {{$cliente->ci}} {{$cliente->ciexp}}.
        </div>
        <div class="tipo6">
        Mediante la presente me dirijo a ustedes para solicitar ACTUALIZACIÓN de datos personales de acuerdo con Cedula de Identidad, CAMBIAR {{ $cambioactualizacion }}, como figura en la Cedula de Identidad, por motivo de Tramite de {{ $tipocartareclamo }}.
        </div>
        <div class="tipo6">
        Requiero esta Actualización para no tener inconvenientes en un futuro cuando solicite trámites correspondientes, según la cantidad de aportes y/o saldos que se registre en la cuenta personal previsional.
        </div>
        <div class="tipo6">
        Espero ser atendido a la solicitud y sin más que decir me despido cordialmente.
        </div>
        <div class="tipo6">
        Atte.<br><br>
        </div>
        <div class="tipo7">{{$personal->nombrecompleto}}</div>
        <div class="tipo8">C.I.{{$personal->ci}}</div>
        <div class="tipo7">APODERADO</div>
        <div class="tipo8">Teléfono: {{ substr($cliente->celular, 3) }}</div>
    </main>
</body>
</html>
