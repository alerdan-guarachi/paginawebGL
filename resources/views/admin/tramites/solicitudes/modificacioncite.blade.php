<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('css/prestacionescartas.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/prestacionescartas.css') }}?v={{ filemtime(public_path('css/prestacionescartas.css')) }}"> --}}
    <style>
        table td {
            padding: 2px 5px;
            line-height: 1;
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
        <div class="tipo3"><strong>Gestora Publica de la Seguridad Social</strong></div>
        <div class="tipo3"><strong>de Largo Plazo</strong></div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5"><strong>REF.:  SOLICITUD DE MODIFICACIÓN DE CITE</strong></div>
        <div class="tipo5"><strong>( TRÁMITE DE @if (
                        strtoupper($nombretramite) === 'INVALIDEZ' ||
                        strtoupper($nombretramite) === 'APELACIÓN' ||
                        strtoupper($nombretramite) === 'SEGUNDA SOLICITUD' ||
                        strtoupper($nombretramite) === 'APELACIÓN SEGUNDA SOLICITUD' ||
                        strtoupper($nombretramite) === 'TERCERA SOLICITUD' ||
                        strtoupper($nombretramite) === 'APELACIÓN TERCERA SOLICITUD' ||
                        strtoupper($nombretramite) === 'RECALIFICACIÓN' ||
                        strtoupper($nombretramite) === 'APELACIÓN DE RECALIFICACIÓN' ||
                        strtoupper($nombretramite) === 'RECALIFICACIÓN SEGUNDA SOLICITUD' ||
                        strtoupper($nombretramite) === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD'
                    )
                        PENSIÓN POR INVALIDEZ
                    @else
                        {{ $nombretramite }}
                    @endif )</strong></div>
        <div class="tipo2">Distinguidos Señores:</div>
        @if ($emisor === 'APODERADO')
            <div class="tipo6">
            Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{ $nombre }}</strong>, 
            con C.I. <strong>{{ $ci }}{{ $ciexp }}</strong>. En Calidad de Apoderado con N.º de poder <strong>{{ $numeropoder }}</strong>, 
            {{ $afiliadoTexto }} <strong>{{$cliente->nombrecompleto}}</strong> con CUA N.º <strong>{{$cliente->nuacua}}</strong>, con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>.
            </div>
        @elseif ($emisor === 'CLIENTE')
            <div class="tipo6">
            Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{$cliente->nombrecompleto}}</strong>, 
            con CUA N.º <strong>{{$cliente->nuacua}}</strong>, con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>.
            </div>
        @endif
        <div class="tipo6">
            Me permito dirigirme a su Institución con el fin de solicitar se pueda modificar la <strong>SOLICITUD DE INFORMACIÓN {!! $solicitudmodificar ?? '<span class="textoedita">SOLICITUD MODIFICAR</span>' !!}</strong> bajo NOTA CITE <strong>{{ $notatecnicomedico }}</strong> de fecha <strong>{{ $fechanotatecnicomedico }}</strong>, 
            ya que esta dirigido a la {!! $campodirigidoa ?? '<span class="textoedita">DIRIGIDO A: CAJA/EMPRESA</span>' !!} puesto que el 
            afiliado {!! $campoestadolab ?? '<span class="textoedita">ESTADO LABORAL: ASEGURADO/TRABAJANDO/TRABAJÓ</span>' !!} en la 
            {!! $campoafiliadoa ?? '<span class="textoedita">AFILIADO A: CAJA/EMPRESA</span>' !!} {!! $texto1 ?? '<span class="textoedita">TEXTO COMPLEMENTARIO</span>' !!} 
            Requiero dicha modificación para dar curso y respuesta a la solicitud del Tribunal Medico Calificador dentro del tramite de Pensión por Invalidez.
        </div>
        <div class="tipo6">
        En respuesta Adjunto:
        </div>
        <table class="tablesinborde">
            <tbody>
                @if(count($adjuntos) > 0)
                    @foreach ($adjuntos as $adjunto)
                        <tr>
                            <td class="bullet"></td>
                            <td>{{ $adjunto['requerimiento'] }}</td>
                            <td>{{ $adjunto['tipo'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="textoedita">NO HAY ADJUNTOS AGREGADOS</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="tipo6">
        Sin más que decir me despido cordialmente agradeciendo de antemano su ayuda y colaboración esperando su pronta respuesta.
        </div>
        <div class="tipo6">
        Atte.
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
