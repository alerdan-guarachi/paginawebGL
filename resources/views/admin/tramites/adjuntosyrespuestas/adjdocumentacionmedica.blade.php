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
        <div class="tipo5"><strong>REF.- ADJUNTO DOCUMENTACIÓN MEDICA @if ($nivelprocedimiento === 'AUTO DE ADMISIÓN') PARA REVISION DE DICTAMEN @endif</strong></div>
        <div class="tipo5">@php
                                $tramite = strtoupper($nombretramite);
                            @endphp
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
            @if ($nivelprocedimiento === 'AUTO DE ADMISIÓN')
                Me dirijo a su Institución con la finalidad de adjuntar <strong>Documentación Médica</strong> en respaldo a la 
                Solicitud de Revisión de Dictamen del <strong>TCM N° {!! $nrosolrevdic2 ?? '<span class="textoedita">NRO. SOL. REV. DIC.</span>' !!}</strong> de fecha 
                {!! $fechasolrevdic2 ?? '<span class="textoedita">FECHA SOL. REV. DIC.</span>' !!}, 
                presentado en su Institución <strong>Gestora Publica de la Seguridad Social de Largo Plazo</strong>, en fecha 
                {!! $fechapressolrevdic2 ?? '<span class="textoedita">FECHA PRESENT. SOL. REV. DIC.</span>' !!} 
                dentro del Tramite de <strong>Pensión por Invalidez</strong>. Ya que en fecha de 
                {!! $fechaautoadmision2 ?? '<span class="textoedita">FECHA AUTO DE ADMISION</span>' !!} 
                se me Notifico con el <strong>AUTO DE ADMISION</strong> emitido por la Autoridad de Fiscalización y Control de 
                Pensiones y Seguros <strong>APS</strong>. Así mismo hago llegar dicha Documentación Medica, para que sean tomados 
                en cuenta dentro de la Revisión correspondiente por el Tribunal Medico Revisor. Solicito de la Manera más Humana 
                a su Institución Publica se remita y haga llegar dicha Documentación al área correspondiente TMR de la Autoridad 
                de Fiscalización y Control de Pensiones y Seguros APS.
            @else
                Me dirijo a su Institución con la finalidad de brindar respuesta a la <strong>{{ $nivelprocedimiento }}</strong> con 
                <strong>NOTA CITE {!! $notatecnicomedico ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                <strong>{!! $fechanotatecnicomedico ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>. 
                Dentro del Trámite de @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                                <strong>PENSIÓN POR INVALIDEZ (RECALIFICACIÓN).</strong>
                            @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
                                <strong>PENSIÓN POR INVALIDEZ.</strong>
                            @else
                                <strong>{{ $nombretramite }}.</strong>
                            @endif
            @endif
        </div>
        <div class="tipo6">
            @if ($nivelprocedimiento === 'AUTO DE ADMISIÓN')
                A continuación, el detalle de lo adjuntado.
            @else
                En respuesta Adjunto.
            @endif
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
                            <td style="text-align: left;">{{ $especialista['especialista2'] }}</td>
                            <td style="text-align: left;">{{ $especialista['detalle2'] }}</td>
                            <td class="tipo10" style="text-align: center;">{{ $especialista['cantidad2'] }}</td>
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
            @if ($nivelprocedimiento === 'AUTO DE ADMISIÓN')
                Sin más que decir me despido agradeciendo de antemano su ayuda y Colaboración, esperando su pronta respuesta.
            @else
                Por último, deseo manifestarles mi más sentido agradecimiento por la ayuda y colaboración, esperando su pronta respuesta me despido cordialmente.
            @endif
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
