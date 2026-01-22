<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('css/prestacionescartas.css') }}">
    <style>
        body {
            margin: {{ $marginSize ?? '1.5cm 3cm 1.5cm 3cm' }};
            background: transparent;
        }
        main {
            font-size: {{ $fontSize ?? '15px' }};
        }
        .tipo1, .tipo2, .tipo3, .tipo5, .tipo6, .tipo7, .tipo8, .tipo9, .tipo10, .tipo55 {
            font-size: {{ $fontSize ?? '15px' }};
        }
        table {
            margin-left: -40px;
        }
        .tipo55 {
            font-size: 15px;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: right;
            text-decoration: underline;
            line-height: 1.2;      /* espacio vertical razonable */
            word-wrap: break-word;  /* fuerza salto de línea si es necesario */
            overflow-wrap: break-word; /* compatibilidad con navegadores modernos */
        }

    </style>
</head>
<body>
    <main>
        <div class="tipo1">
            @if ($cliente->sucursal === 'SANTA CRUZ')
                Santa Cruz de la Sierra, {{ $fechaactual }}
            @elseif ($cliente->sucursal === 'COCHABAMBA')
                Cochabamba, {{ $fechaactual }}
            @else
                {{ $cliente->sucursal }}, {{ $fechaactual }}
            @endif
        </div>
        <div class="tipo2">Señores:</div>
        @if(!empty($nombremedico))
            <div class="tipo3"><strong>{{ $nombremedico }}</strong></div>
        @endif
        @if(!empty($cargomedico))
            <div class="tipo3"><strong>{{ $cargomedico }}</strong></div>
        @endif
        {{-- <div class="tipo3"><strong>Gestora Publica de la Seguridad Social de Largo Plazo</strong></div> --}}
        <div class="tipo9" style="margin-top: -10px;">Presente. -</div>
        <div class="tipo55"><strong>REF.- {!! $tipoPdf ?? '<span class="textoedita">TIPO MISIVA</span>' !!}</strong></div>
        @if ($mostrarencabezado === 'SI')
            @if ($emisor === 'APODERADO')
                <div class="tipo2" style="margin-bottom: -5px;">Distinguidos Señores:</div>
            @else
                <div class="tipo2" style="margin-bottom: -5px;">De mi Consideración:</div>
            @endif
            <div class="tipo6">
                @if ($emisor === 'APODERADO')
                    Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{ $nombre }}</strong>, 
                    con C.I. <strong>{{ $ci }}{{ $ciexp }}</strong>. En Calidad de Apoderado con N.º de poder <strong>{{ $numeropoder }}</strong>, 
                    {{ $afiliadoTexto }} <strong>{{$cliente->nombrecompleto}}</strong> con CUA N.º <strong>{{$cliente->nuacua}}</strong>, con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>,
                @elseif ($emisor === 'CLIENTE')
                    Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{$cliente->nombrecompleto}}</strong>, 
                    con CUA N.º <strong>{{$cliente->nuacua}}</strong>, con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}.</strong>
                @endif
            </div>
        @endif
        <div class="tipo6">
            {!! $contenidoLibre !!}
        </div>
        <div class="tipo6">
        Atte.<br><br>
        </div>
        <div class="tipo7" style="margin-top: 60px;"><strong>{{ $nombre }}</strong></div>
        <div class="tipo8">C.I.: {{ $ci }} {{ $ciexp }}</div>
        @if ($emisor === 'APODERADO')
            <div class="tipo7"><strong>APODERADO</strong></div>
        @endif
        <div class="tipo8">
            Teléfono: {{ $telefono }}
            @if ($emisor === 'APODERADO')
                - {{ Str::startsWith($cliente->celular, '591') ? substr($cliente->celular, 3) : $cliente->celular }}
            @endif
        </div>
    </main>
</body>
</html>
