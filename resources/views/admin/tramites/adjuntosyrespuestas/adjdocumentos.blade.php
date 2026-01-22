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
        <div class="tipo5"><strong>REF.- ADJUNTO DE DOCUMENTACIÓN</strong></div>
        <div class="tipo5"><strong>{!! $documentoadjunto ?? '<span class="textoedita">DOCUMENTO ADJUNTO</span>' !!}</strong></div>
        <div class="tipo2">Distinguidos Señores:</div>
        <div class="tipo6" style="margin-top: -10px;">
            Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{ $nombre }}</strong>, 
            con C.I. <strong>{{ $ci }}{{ $ciexp }}</strong>. En Calidad de Apoderado con N.º de poder <strong>{{ $numeropoder }}</strong>, 
            {{ $afiliadoTexto }} <strong>{{$cliente->nombrecompleto}}</strong> con CUA N.º <strong>{{$cliente->nuacua}}</strong>, con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>
        </div>
        <div class="tipo6">
            Dentro del Tramite de <strong>@if (
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
                    @endif</strong>, por medio de la presente me dirijo a ustedes, con la finalidad de 
            Adjuntar Documentación <strong>({!! $documentoadjunto ?? '<span class="textoedita">DOCUMENTO ADJUNTO</span>' !!})</strong> 
            en respuesta a la <strong>NOTA CITE {!! $notatecnicomedico ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
            <strong>{!! $fechanotatecnicomedico ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>, con Referencia 
            <strong>{!! $textocomplementario ?? '<span class="textoedita">TEXTO COMPLEMENTARIO</span>' !!}</strong>
        </div>
        <div class="tipo6">
            En respuesta Adjunto:
        </div>
        <div class="tipo6" style="margin-top: -15px;">
            <ul style="list-style-type: disc; padding-left: 20px;">
                <li>
                    {!! $documentoadjunto ?? '<span class="textoedita">DOCUMENTO ADJUNTO</span>' !!}
                </li>
            </ul>
        </div>
        
        <div class="tipo6">
        Sin más que decir me despido, no sin antes agradeciendo de antemano su colaboración, esperando su pronta respuesta a mi Solicitud.
        </div>
        <div class="tipo6">
        Atte.<br><br>
        </div>
        <div class="tipo7" style="margin-top: 70px;"><strong>{{ $nombre }}</strong></div>
        <div class="tipo8">C.I. {{ $ci }}{{ $ciexp }}</div>
        <div class="tipo7"><strong>APODERADO</strong></div>
        <div class="tipo8">Teléfono: {{ $telefono }}</div>
    </main>
</body>
</html>
