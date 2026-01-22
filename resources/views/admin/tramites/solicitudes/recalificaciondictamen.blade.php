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
        <div class="tipo5"><strong>REF.- SOLICITUD DE RECALIFICACIÓN DE DICTAMEN</strong></div>
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
        <div class="tipo6">
        Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{ $nombre }}</strong>, 
        con C.I. <strong>{{ $ci }}{{ $ciexp }}</strong>. En Calidad de Apoderado con N.º de poder <strong>{{ $numeropoder }}</strong>, 
        {{ $afiliadoTexto }} <strong>{{$cliente->nombrecompleto}}</strong> con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>, con CUA N.º <strong>{{$cliente->nuacua}}</strong>.
        Por medio de la presente me dirijo a su Institución, con la Finalidad de solicitar la <strong>RECALIFICACIÓN DE DICTAMEN</strong>
        de la <strong>{!! $entidadcalificante ?? '<span class="textoedita">ENTIDAD CALIFICANTE</span>' !!}</strong> N° <strong>{!! $nrodictamen ?? '<span class="textoedita">NRO. DICTAMEN</span>' !!}</strong>
        de fecha <strong>{!! $fechatramite ?? '<span class="textoedita">FECHA TRÁMITE</span>' !!}</strong> con un Porcentaje del <strong>{!! $porcentajedictamen ?? '<span class="textoedita">PORCENTAJE DICTAMEN</span>' !!}</strong> 
        de la Perdida de la Capacidad laboral de Origen <strong>{!! $origendictamen ?? '<span class="textoedita">ORIGEN DICTAMEN</span>' !!}</strong> por 
        <strong>{!! $tipoorigen ?? '<span class="textoedita">TIPO ORIGEN</span>' !!}</strong> notificado en 
        <strong>{!! $notificadoen ?? '<span class="textoedita">NOTIFICADO EN</span>' !!}</strong> en fecha 
        <strong>{!! $fechanotificacion ?? '<span class="textoedita">FECHA NOTIFICACIÓN</span>' !!}</strong>.
        </div>

        <div class="tipo6">
        Si más que decir, me despido Cordialmente.
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
