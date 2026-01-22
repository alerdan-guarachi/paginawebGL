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
        .tableinter td {
            padding: 2px 5px;
            line-height: 1;
        }
        .tableinter th {
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
        <div class="tipo2">Distinguidos Señores:</div>
        @if(!empty($nombremedico))
            <div class="tipo3"><strong>{{ $nombremedico }}</strong></div>
        @endif
        <div class="tipo3"><strong>Gestora Publica de la Seguridad Social</strong></div>
        <div class="tipo3"><strong>de Largo Plazo</strong></div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5"><strong>REF: SOLICITUD DE ACTUALIZACION DE DATOS - CAMBIO DE </strong></div>
        <div class="tipo5"><strong>NUMERO DE CARNET DE EXTRANJERO A PASAPORTE</strong></div>

        <div class="tipo2" style="margin-top: -10px; margin-bottom: -10px;">Distinguidos Señores. -</div>

        <div class="tipo6">
            @if ($emisor === 'APODERADO')
                Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{ $nombre }}</strong>, 
                con C.I. <strong>{{ $ci }}{{ $ciexp }}</strong>. En Calidad de Apoderado con Nº de poder especial Nº <strong>{{ $numeropoder }}</strong> (Extranjero), 
                {{ $afiliadoTexto }} <strong>{{$cliente->nombrecompleto}}</strong> Con Cedula de Identidad de Extranjero N° <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>, con CUA N.º <strong>{{$cliente->nuacua}}</strong>,
            @elseif ($emisor === 'CLIENTE')
                Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{$cliente->nombrecompleto}}</strong>, 
                Con Cedula de Identidad de Extranjero N° <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>, con CUA N.º <strong>{{$cliente->nuacua}}</strong>, 
            @endif
            Por medio de la presente me dirijo a ustedes, con la finalidad de Solicitar la Actualización de datos, 
            <strong>Cambio de Numero</strong> de Carnet de Extranjero a Pasaporte. Ya que el Afiliado esta registrado 
            en su Institución <strong>Gestora Publica de la Seguridad Social de Largo Plazo</strong> con el C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>, 
            (Expirado), y que Solicito el Cambio a <strong>PASAPORTE N° {!! $nropasaporte ?? '<span class="textoedita">NRO PASAPORTE</span>' !!}</strong>.
            Numero de Pasaporte Vigente y actual. Mismo que solicito para poder Iniciar mi Tramite de Pensión por Vejez en su Institución Pública.
        </div>
        <div class="tipo6" style="margin-top: -5px;">
            Solicitud de Cambio de Numero de Carnet de Extranjero con datos:
        </div>
        <table class="table tableinter" style="margin-top: -5px;">
            <thead>
                <tr>
                    <th class="tipo10">APE. PATERNO</th>
                    <th class="tipo10">APE. MATERNO</th>
                    <th class="tipo10">PRIMER NOMBRE</th>
                    <th class="tipo10">SEGUNDO NOMBRE</th>
                </tr>
            </thead>
            <tbody>
                @if(count($ceapasaportes) > 0)
                    @foreach ($ceapasaportes as $ceapasaporte)
                        <tr>
                            <td>{{ $ceapasaporte['appaterno'] }}</td>
                            <td>{{ $ceapasaporte['apmaterno'] }}</td>
                            <td>{{ $ceapasaporte['primernombre'] }}</td>
                            <td>{{ $ceapasaporte['segundonombre'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="textoedita">NINGÚN REGISTRO AGREGADO</td>
                    </tr>
                @endif
            </tbody>
            <thead>
                <tr>
                    <th class="tipo10">CUA N°</th>
                    <th class="tipo10">C.E.</th>
                    <th class="tipo10">PASAPORTE N°</th>
                    <th class="tipo10"></th>
                </tr>
            </thead>
            <tbody>
                @if(count($ceapasaportes) > 0)
                    @foreach ($ceapasaportes as $ceapasaporte)
                        <tr>
                            <td>{{ $ceapasaporte['cua'] }}</td>
                            <td>{{ $ceapasaporte['ce'] }}</td>
                            <td>{{ $ceapasaporte['pasaporte'] }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="textoedita">NINGÚN REGISTRO AGREGADO</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="tipo6" style="margin-top: -5px;">
            A Numero de Pasaporte con datos:
        </div>
        <table class="table tableinter" style="margin-top: -5px;">
            <thead>
                <tr>
                    <th class="tipo10">APE. PATERNO</th>
                    <th class="tipo10">APE. MATERNO</th>
                    <th class="tipo10">PRIMER NOMBRE</th>
                    <th class="tipo10">SEGUNDO NOMBRE</th>
                </tr>
            </thead>
            <tbody>
                @if(count($ceapasaportes2) > 0)
                    @foreach ($ceapasaportes2 as $ceapasaporte2)
                        <tr>
                            <td>{{ $ceapasaporte2['appaterno2'] }}</td>
                            <td>{{ $ceapasaporte2['apmaterno2'] }}</td>
                            <td>{{ $ceapasaporte2['primernombre2'] }}</td>
                            <td>{{ $ceapasaporte2['segundonombre2'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="textoedita">NINGÚN REGISTRO AGREGADO</td>
                    </tr>
                @endif
            </tbody>
            <thead>
                <tr>
                    <th class="tipo10">CUA N°</th>
                    <th class="tipo10">C.E.</th>
                    <th class="tipo10">PASAPORTE N°</th>
                    <th class="tipo10"></th>
                </tr>
            </thead>
            <tbody>
                @if(count($ceapasaportes2) > 0)
                    @foreach ($ceapasaportes2 as $ceapasaporte2)
                        <tr>
                            <td>{{ $ceapasaporte2['cua2'] }}</td>
                            <td>{{ $ceapasaporte2['ce2'] }}</td>
                            <td>{{ $ceapasaporte2['pasaporte2'] }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="textoedita">NINGÚN REGISTRO AGREGADO</td>
                    </tr>
                @endif
            </tbody>
        </table>

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
        <div class="tipo6" style="margin-top: -10px;">
        Sin más que decir me despido, no sin antes agradeciendo de antemano su colaboración, esperando su pronta respuesta a mi Solicitud.
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
