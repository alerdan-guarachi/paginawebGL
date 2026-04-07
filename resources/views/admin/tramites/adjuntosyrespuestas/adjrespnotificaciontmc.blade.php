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
        .tipo1, .tipo2, .tipo3, .tipo5, .tipo6, .tipo7, .tipo8, .tipo9, .tipo10 {
            font-size: {{ $fontSize ?? '15px' }};
        }
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
        <div class="tipo9" style="margin-top: -10px;">Presente. -</div>
        <div class="tipo5"><strong>REF.- ADJUNTO DOCUMENTACIÓN MEDICA RESPUESTA AL ACTA 
            @php
                $tramite = strtoupper($nombretramite);
            @endphp
            @if ($tramite === 'INVALIDEZ' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD')
                TMC
            @elseif ($tramite === 'APELACIÓN' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                TMR
            @else
                TMC
            @endif</strong></div>
        <div class="tipo5">
                            @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                                <strong>TRÁMITE DE PENSIÓN POR INVALIDEZ (RECALIFICACIÓN)</strong>
                            @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
                                <strong>TRÁMITE DE PENSIÓN POR INVALIDEZ</strong>
                            @else
                                <strong>TRÁMITE DE {{ $nombretramite }}</strong>
                            @endif</div>
        <div class="tipo2">Distinguidos Señores:</div>
        <div class="tipo6" style="margin-top: -10px;">
            Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{ $nombre }}</strong>, 
            con C.I. <strong>{{ $ci }}{{ $ciexp }}</strong>. En Calidad de Apoderado con N.º de poder <strong>{{ $numeropoder }}</strong>, 
            {{ $afiliadoTexto }} <strong>{{$cliente->nombrecompleto}}</strong> con CUA N.º <strong>{{$cliente->nuacua}}</strong>, con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>
        </div>
        <div class="tipo6">
            Me dirijo a su Institución con la finalidad de brindar respuesta al Acta del Tribunal Medico 
            @if ($tramite === 'INVALIDEZ' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD')
                Calificador (TMC) de la Entidad Encargada de Calificar (EEC)
            @elseif ($tramite === 'APELACIÓN' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                Revisor (TMR) de la Entidad Encargada de Revisar (EER)
            @else
                Calificador (TMC) de la Entidad Encargada de Calificar (EEC)
            @endif
            Habiendo sido notificado por su Institución Publica con 
            <strong>NOTA CITE {!! $notatecnicomedico ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
            <strong>{!! $fechanotatecnicomedico ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>. 
            Dentro del Trámite de @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                                <strong>PENSIÓN POR INVALIDEZ (RECALIFICACIÓN)</strong>
                            @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
                                <strong>PENSIÓN POR INVALIDEZ</strong>
                            @else
                                <strong>{{ $nombretramite }}</strong>
                            @endif
        </div>
        <div class="tipo6">
        En respuesta Adjunto:
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="tipo10">ESPECIALISTA/CENTRO MÉDICO</th>
                    <th class="tipo10">DETALLE</th>
                    <th class="tipo10">CANTIDAD</th>
                </tr>
            </thead>
            <tbody>
                @if(count($especialistas) > 0)
                    @foreach ($especialistas as $especialista)
                        <tr>
                            <td>{{ $especialista['especialista2'] }}</td>
                            <td>{{ $especialista['detalle2'] }}</td>
                            <td class="tipo10">{{ $especialista['cantidad2'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="textoedita">NO HAY REGISTROS AGREGADOS</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <div class="tipo6">
        Por último, deseo manifestarles mi más sentido agradecimiento por la ayuda y colaboración, esperando su pronta respuesta me despido cordialmente.
        </div>
        <div class="tipo6">
        Atte.<br><br>
        </div>
        <div class="tipo7" style="margin-top: 70px;"><strong>{{ $nombre }}</strong></div>
        <div class="tipo8">C.I. {{ $ci }} {{ $ciexp }}</div>
        <div class="tipo7"><strong>APODERADO</strong></div>
        <div class="tipo8">Teléfono: {{ $telefono }}</div>
    </main>
</body>
</html>
