<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('css/prestacionescartas.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/prestacionescartas.css') }}?v={{ filemtime(public_path('css/prestacionescartas.css')) }}"> --}}
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
        <div class="tipo5"><strong>REF.- SOLICITUD DE COPIA LEGALIZADA DE DICTAMEN</strong></div>
        <div class="tipo5"><strong>( @if (
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
        <div class="tipo6">
            @if ($emisor === 'APODERADO')
                Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{ $nombre }}</strong>, 
                con C.I. <strong>{{ $ci }}{{ $ciexp }}</strong>. En Calidad de Apoderado con N.º de poder <strong>{{ $numeropoder }}</strong>, 
                {{ $afiliadoTexto }} <strong>{{$cliente->nombrecompleto}}</strong> con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>, con CUA N.º <strong>{{$cliente->nuacua}}</strong>,
            @elseif ($emisor === 'CLIENTE')
                Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{$cliente->nombrecompleto}}</strong>, 
                con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>, con CUA N.º <strong>{{$cliente->nuacua}}</strong>, 
            @endif
            Me dirijo a su Institución con la finalidad de solicitar <strong>1 COPIA LEGALIZADA</strong> del 
            <strong>DICTAMEN de @if (
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
                    @endif N° {!! $nrodictamen ?? '<span class="textoedita">NRO. DICTAMEN</span>' !!}</strong> firmado en fecha <strong>{!! $fechacontrato ?? '<span class="textoedita">FECHA DOCUMENTO</span>' !!}</strong> 
                                                en {!! $firmadoen ?? '<span class="textoedita">ENTREGADO EN</span>' !!}.
        </div>

        <div class="tipo6">
        Sin otro particular me despido, esperando su pronta respuesta 
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
