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
        <div class="tipo2">Señor:</div>
        @if(!empty($nombremedico))
            <div class="tipo3"><strong>{{ $nombremedico }}</strong></div>
        @endif
        <div class="tipo3"><strong>Gestora Publica de la Seguridad Social</strong></div>
        <div class="tipo3"><strong>de Largo Plazo</strong></div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5"><strong>REF: SOLICITUD DE UNIFICACIÓN DE CUA. / NUA.</strong></div>

        <div class="tipo2" style="margin-top: -10px; margin-bottom: -10px;">Distinguidos Señores. -</div>

        <div class="tipo6">
            Por medio de la presente me dirijo a ustedes, para solicitar la unificación de los números de 
            <strong>CUA {{-- {!! $nrocua1 ?? '<span class="textoedita">NRO. CUA 1</span>' !!} --}}{{ $cliente->nuacua }}</strong> de 
            <strong>{!! $nombreafp1 ?? '<span class="textoedita">AFP 1</span>' !!}</strong> con 
            <strong>C.I. {{-- {!! $nroci1 ?? '<span class="textoedita">NRO. CI. 1</span>' !!} --}}{{ $cliente->ci }}</strong> y el 
            <strong>N° CUA {!! $nrocua2 ?? '<span class="textoedita">NRO. CUA 2</span>' !!}</strong> registrado en 
            <strong>{!! $nombreafp2 ?? '<span class="textoedita">AFP 2</span>' !!}</strong> con 
            <strong>C.I. {!! $nroci2 ?? '<span class="textoedita">NRO. CI. 2</span>' !!}</strong>
            {{ $texto1 }}. Adjunto el detalle:
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
                @if(count($unificacioncuas) > 0)
                    @foreach ($unificacioncuas as $unificacioncua)
                        <tr>
                            <td>{{ $unificacioncua['appaterno3'] }}</td>
                            <td>{{ $unificacioncua['apmaterno3'] }}</td>
                            <td>{{ $unificacioncua['primernombre3'] }}</td>
                            <td>{{ $unificacioncua['segundonombre3'] }}</td>
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
                    <th class="tipo10">FECHA NAC.</th>
                    <th class="tipo10">C.I.</th>
                    <th class="tipo10">CUA 1</th>
                    <th class="tipo10">CUA 2</th>
                </tr>
            </thead>
            <tbody>
                @if(count($unificacioncuas) > 0)
                    @foreach ($unificacioncuas as $unificacioncua)
                        <tr>
                            <td>{{ $unificacioncua['fechanacimiento3'] }}</td>
                            <td>{{ $unificacioncua['ci3'] }}</td>
                            <td>{{ $unificacioncua['cua3'] }}</td>
                            <td>{{ $unificacioncua['cuaotro3'] }}</td>
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
            Siendo que mis Datos son:
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
                @if(count($cambiounificacioncuas) > 0)
                    @foreach ($cambiounificacioncuas as $cambiounificacioncua)
                        <tr>
                            <td>{{ $cambiounificacioncua['appaterno4'] }}</td>
                            <td>{{ $cambiounificacioncua['apmaterno4'] }}</td>
                            <td>{{ $cambiounificacioncua['primernombre4'] }}</td>
                            <td>{{ $cambiounificacioncua['segundonombre4'] }}</td>
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
                    <th class="tipo10">FECHA NAC.</th>
                    <th class="tipo10">C.I.</th>
                    <th class="tipo10">CUA</th>
                    <th class="tipo10"></th>
                </tr>
            </thead>
            <tbody>
                @if(count($cambiounificacioncuas) > 0)
                    @foreach ($cambiounificacioncuas as $cambiounificacioncua)
                        <tr>
                            <td>{{ $cambiounificacioncua['fechanacimiento4'] }}</td>
                            <td>{{ $cambiounificacioncua['ci4'] }}</td>
                            <td>{{ $cambiounificacioncua['cua4'] }}</td>
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
            Y como estoy registrado en <strong>Gestora Publica de la Seguridad Social de Largo Plazo</strong>, por ello Solicito permanezca solo el 
            <strong>N°CUA {{-- {!! $nrocuaunificado ?? '<span class="textoedita">NRO. CUA UNIFICADO</span>' !!} --}}{{ $cliente->nuacua }} Gestora Publica de la Seguridad Social de Largo Plazo,</strong>
            con mi Numero de Carnet actual <strong>{{-- {!! $nrociunificado ?? '<span class="textoedita">NRO. CI UNIFICADO</span>' !!} --}} {{ $cliente->ci }}</strong>
        </div>
        <div class="tipo6">
            Favor anular el N° CUA. <strong>{!! $nrocua2 ?? '<span class="textoedita">NRO. CUA 2</span>' !!}</strong> y unificar 
            todos mis aportes al N° CUA. <strong>{{-- {!! $nrocua1 ?? '<span class="textoedita">NRO. CUA 1</span>' !!} --}}{{ $cliente->nuacua }}</strong> con mi nombre correcto:
        </div>

        <div class="tipo6">
            <strong>NOMBRES:</strong>{{$cliente->nombres}}<br>
            <strong>APELLIDO PATERNO:</strong>{{$cliente->apepaterno}}<br>
            <strong>APELLIDO MATERNO:</strong>{{$cliente->apematerno}}
        </div>

        <div class="tipo6" style="margin-top: -10px;">
        Sin más que decir me despido, no sin antes agradeciendo de antemano su colaboración, esperando su pronta respuesta.
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
