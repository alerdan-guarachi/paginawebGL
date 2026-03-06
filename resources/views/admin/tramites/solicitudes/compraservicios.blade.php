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
        <div class="tipo5"><strong>REF.:  SOLICITUD DE COMPRA DE SERVICIOS</strong></div>
        <div class="tipo5">
            @php
                $tramite = strtoupper($nombretramite);
            @endphp
            @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                <strong>TRÁMITE DE PENSIÓN POR INVALIDEZ (RECALIFICACIÓN)</strong>
            @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
                <strong>TRÁMITE DE PENSIÓN POR INVALIDEZ</strong>
            @else
                <strong>TRÁMITE DE {{ $nombretramite }}</strong>
            @endif
        </div>
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
            Dentro del Trámite de 
            @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                <strong>PENSIÓN POR INVALIDEZ (RECALIFICACIÓN)</strong>
            @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
                <strong>PENSIÓN POR INVALIDEZ</strong>
            @else
                <strong>{{ $nombretramite }}</strong>
            @endif
            en respuesta a la <strong>{{$nivelprocedimiento}}</strong> con NOTA <strong>{!! $notatecnicomedico ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> de fecha <strong>{{ $fechanotatecnicomedico }}</strong>, {!! $texto1 ?? '<span class="textoedita">TEXTO COMPLEMENTARIO</span>' !!}
            Solicito por favor autoricen con <strong>COMPRA DE SERVICIOS</strong> para dar cumplimiento al requerimiento, puesto 
            que el Afiliado <strong>NO</strong> se encuentra trabajando ya que dejo de Trabajar el 19/04/2024 Dependiente de la 
            Empresa <strong>{{ $empresacliente }}</strong> y por lo tanto no cuenta con Ente Gestor de Salud vigente. 
            Por lo que pido por favor se tome en consideración del caso, ya que el único interés {{ $afiliadoTexto }} es poder acceder a un beneficio.
        </div>
        <div class="tipo6">
        Adjunto:
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
