<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('css/prestacionescartas.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/prestacionescartas.css') }}?v={{ filemtime(public_path('css/prestacionescartas.css')) }}"> --}}
    <style>
        table td {
            padding: 2px 5px;
            line-height: 1.2;
        }
        .tableinter td {
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
        <div class="tipo3"><strong>{{$cliente->empresa}}</strong></div>
        <div class="tipo3"><strong>Recursos Humanos</strong></div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5"><strong>REF: SOLICITUD DE INFORME DEL EMPLEADOR</strong></div>
        <div class="tipo2">Distinguidos Señores. -</div>
        <div class="tipo6">
        Mediante la presente Nota. Yo, el Sr. <strong>{{$cliente->nombrecompleto}}</strong> con C.I. <strong>{{$cliente->ci}}{{$cliente->ciexp}}</strong>, 
        con CUA:<strong>{{$cliente->nuacua}}</strong>.
        </div>
        <div class="tipo6">
        Me dirijo a su Institución con la Finalidad de solicitar de la manera más Humana su colaboración, para poder brindar respuesta habiendo 
        sido notificado por la Gestora Publica mediante la NOTA CITE <strong>{!! $notatecnicomedico ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> de fecha <strong>{{ $fechanotatecnicomedico }}</strong>. 
        En donde solicitan <strong>INFORME DEL EMPLEADOR</strong> <strong>({{$cliente->empresa}})</strong> la siguiente Información:
        </div>
        <table class="tablesinborde" style="margin-top: -5px;">
            <tbody>
                @if(count($informaciones) > 0)
                    @foreach ($informaciones as $informacion)
                        <tr>
                            <td class="bullet"></td>
                            <td>{{ $informacion['informacion'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2" class="textoedita">NO HAY REGISTROS AGREGADOS</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="tipo6" style="margin-top: -20px;">
        El mismo es necesario para dar Finalizado mi <strong>TRAMITE DE @if (
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
                    @endif</strong>
        Ante las oficinas de la <strong>GESTORA PÚBLICA DE LA SEGURIDAD SOCIAL DE LARGO PLAZO</strong>.
        </div>
        <div class="tipo6">
        Adjunto:
        </div>
        <table class="tablesinborde tableinter" style="margin-top: -5px;">
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
        <div class="tipo6" style="margin-top: -20px;">
        Sin otro particular, me despido no sin antes deseándoles éxito en sus actividades cotidianas, esperando su pronta respuesta.
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
