<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .content {
            margin: 20px;
        }
    </style>
</head>
<body>

    <div class="content" style="text-align: left; margin-top: 50px;">
        <p style="text-align: right;">{{ $ciudad }}, 
            {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
        </p>
        
        <p>Señores:</p>
        <p style="margin-top: 10px;"><strong>{{ $opcion }}</strong></p>
        <p style="margin-bottom: 40px;">Presente.-</p>

        <p style="text-align: center; margin-bottom: 40px;"><strong><u>REF: SOLICITUD DE POLIZAS</u></strong></p>
        <p>Mediante la presente carta reciba usted mis más cordiales saludos, deseándole éxitos en sus labores.</p>
        <p>Por la presente realizo la Solicitud de:</p>
        <p style="margin-left: 40px; margin-bottom:1px;">1.	Pólizas Generales</p>
        <p style="margin-left: 40px; margin-top:1px; margin-bottom:1px;">2.	Declaración de Salud </p>
        <p style="margin-left: 40px; margin-top:1px;">3.	Póliza de Seguro de Desgravamen</p>
        <p>Aclaro que el tomador del Seguro es el Banco, por lo mismo solicito las gestiones ante la Compañía Aseguradora.</p>
        <p>Agradeciendo su atención.</p>
        
        <p style="text-align: center; margin-top: 90px;">{{ $clienteuno }}</p>
        <p style="text-align: center; margin-top: -10px;">CI: {{ $clienteunoci }}</p>

        @if (!empty($clientedos))
            <p style="text-align: center; margin-top: 90px;">{{ $clientedos }}</p>
            <p style="text-align: center; margin-top: -10px;">CI: {{ $clientedosci }}</p>
        @endif

    </div>

</body>
</html>
