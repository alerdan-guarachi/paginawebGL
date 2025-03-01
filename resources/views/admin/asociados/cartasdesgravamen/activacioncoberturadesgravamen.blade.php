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
        <p style="margin-top: 10px;"><strong>{{ $nombreBanco }}</strong></p>
        <p style="margin-bottom: 40px;">Presente.-</p>

        <p style="margin-bottom: 40px;"><strong><u>{!! $ref_carta !!}</u></strong></p>
        <p>De mi consideración:</p>

        <div class="articulos" style="text-align: justify;">
            {!! $articulosTexto !!}
        </div>

        <p style="text-align: center; margin-top: 90px;">{{ $clienteauditorianombre }}</p>
        <p style="text-align: center; margin-top: -10px;">CI: {{ $ci }}</p>

        @if (!empty($clientedos))
            <p style="text-align: center; margin-top: 90px;">{{ $clientedos }}</p>
            <p style="text-align: center; margin-top: -10px;">CI: {{ $clientedosci }}</p>
        @endif

    </div>

</body>
</html>
