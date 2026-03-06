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
        table.tabla-centrada {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .tabla-centrada th,
        .tabla-centrada td {
            text-align: center !important;
            vertical-align: middle !important;
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
        <div class="tipo5"><strong>REF.- SOLICITUD DE PAGO DE ABONO EN CUENTA DE</strong></div>
        <div class="tipo5">
            @php
                $tramite = strtoupper($nombretramite);
            @endphp
            @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                <strong>PENSIÓN POR INVALIDEZ (RECALIFICACIÓN)</strong>
            @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
                <strong>PENSIÓN POR INVALIDEZ</strong>
            @else
                <strong>{{ $nombretramite }}</strong>
            @endif
        </div>
        <div class="tipo2">Distinguidos Señores:</div>
        <div class="tipo6">
            @if ($emisor === 'APODERADO')
                Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{ $nombre }}</strong>, 
                con C.I. <strong>{{ $ci }}{{ $ciexp }}</strong>. En Calidad de Apoderado con N.º de poder <strong>{{ $numeropoder }}</strong>, 
                del cónyuge {{-- {{ $nombretramite }} --}}, {{ $afiliadoTexto }} <strong>{{$cliente->nombrecompleto}}</strong> con CUA N.º <strong>{{$cliente->nuacua}}</strong>, con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>.
            @elseif ($emisor === 'CLIENTE')
                Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{$cliente->nombrecompleto}}</strong>, 
                con CUA N.º <strong>{{$cliente->nuacua}}</strong>, con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>.
            @endif
        </div>
        <div class="tipo6">
            Me dirijo a su institución de la finalidad de presentar un RECLAMO sobre el estado de trámite de MASA HEREDITARIA, 
            mismo que se dio inicio en fecha {!! $fechainiciotramite ?? '<span class="textoedita">FECHA INICIO TRÁMITE</span>' !!} 
            y que se realizó la verificación de EAP (Estado de Ahorro Previsional) en fecha 
            {!! $fechafirmaeap2 ?? '<span class="textoedita">FECHA FIRMA EAP</span>' !!}.
            .
        </div>
        <div class="tipo6">
            Los herederos declaran que en fecha {!! $fechaformulario ?? '<span class="textoedita">FECHA FORMULARIO</span>' !!}, 
            han llenado, suscrito y presentado el Formulario de MASA HEREDITARIA, junto con toda la documentación entregada 
            por los herederos, de acuerdo con lo establecido por la RESOLUCIÓN ADMINISTRATIVA SPVS Nº 
            {!! $nroresolucion ?? '<span class="textoedita">NRO. RESOLUCIÓN</span>' !!} de fecha 
            {!! $fecharesolucion ?? '<span class="textoedita">FECHA RESOLUCIÓN</span>' !!}.
        </div>
        <div class="tipo6">
            Las pensiones no cobradas por le fallecido sujetas a entrega bajo la modalidad de MASA HEREDITARIA correspondes al detalle siguiente:
        </div>
        <table class="table tabla-centrada">
            <thead>
                <tr>
                    <th class="tipo10">PRESTACIÓN O MODALIDAD</th>
                    <th class="tipo10">PERIODOS NO COBRADOS</th>
                </tr>
            </thead>
            <tbody>
                @if(count($prestaciones) > 0)
                    @foreach ($prestaciones as $prestacion)
                        <tr>
                            <td>{{ $prestacion['prestacion'] }}</td>
                            <td>{{ $prestacion['periodo'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="textoedita">NINGUNA PRESTACIÓN AGREGADA</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="tipo6">
        Sin más que decir me despido agradeciendo de antemano por su ayuda y colaboración esperando su pronta respuesta.
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
