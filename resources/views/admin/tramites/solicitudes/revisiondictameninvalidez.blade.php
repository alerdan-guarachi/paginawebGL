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
        <div class="tipo3"><strong>Gestora Pública de la Seguridad Social de Largo Plazo</strong></div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5"><strong>REF.- SOLICITUD DE REVISIÓN DE DICTAMEN DE INVALIDEZ</strong></div>

        <div class="tipo2">De mi mayor consideración:</div>
        <div class="tipo6">
            @if ($emisor === 'APODERADO')
                Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{ $nombre }}</strong>, 
                con C.I. <strong>{{ $ci }}{{ $ciexp }}</strong>. En Calidad de Apoderado con N.º de poder <strong>{{ $numeropoder }}</strong>, 
                {{ $afiliadoTexto }} <strong>{{$cliente->nombrecompleto}}</strong> con CUA N.º <strong>{{$cliente->nuacua}}</strong>, con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>,
            @elseif ($emisor === 'CLIENTE')
                Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{$cliente->nombrecompleto}}</strong>, 
                con CUA N.º <strong>{{$cliente->nuacua}}</strong>, con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>, 
            @endif
        </div>
        <div class="tipo6">
            Me dirijo a ustedes al amparo del artículo 157 del Decreto Supremo N.º 0822, con el objeto de solicitar la 
            <strong>Revisión del Dictamen por grado de Invalidez</strong>, emitido por el <strong>Tribunal Médico Calificador 
            (TMC)</strong> mediante Dictamen <strong>N.º {!! $nrodictamen ?? '<span class="textoedita">NRO. DICTAMEN</span>' !!}</strong>, 
            con fecha <strong>{!! $fechacontrato ?? '<span class="textoedita">FECHA DOCUMENTO</span>' !!}</strong>, correspondiente a la solicitud de 
            pensión por invalidez del mencionado afiliado.
        </div>
        <div class="tipo6">
            {!! $txtcomple1 ?? '<span class="textoedita">TEXTO COMPLEMENTARIO 1</span>' !!}
        </div>
        <div class="tipo6">
            <strong>{!! $tituloop1 ?? '<span class="textoedita">TITULO OPCIONES 1</span>' !!}</strong>
        </div>
        <table class="tablesinborde" style="margin-top: -5px;">
            <tbody>
                @if(count($opcionesuno) > 0)
                    @foreach ($opcionesuno as $opcionuno)
                        <tr>
                            <td class="bullet"></td>
                            <td>{{ $opcionuno['opcionuno'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2" class="textoedita">NO HAY REGISTROS AGREGADOS</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="tipo6">
            <strong>{!! $tituloop2 ?? '<span class="textoedita">TITULO OPCIONES 2</span>' !!}</strong>
        </div>
        <table class="tablesinborde" style="margin-top: -5px;">
            <tbody>
                @if(count($opcionesdos) > 0)
                    @foreach ($opcionesdos as $opciondos)
                        <tr>
                            <td class="bullet"></td>
                            <td>{{ $opciondos['opciondos'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2" class="textoedita">NO HAY REGISTROS AGREGADOS</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="tipo6">
            Conforme al <strong>artículo 70 de la Ley de Pensiones N.º 065</strong> establece que la calificación del grado, origen, 
            causa y fecha de invalidez debe ser realizada por profesionales médicos habilitados por el Organismo de 
            Fiscalización, y dicha evaluación <strong>debe ser integral</strong>, conforme al <strong>Manual Único de Calificación</strong>, 
            el cual comprende el Manual de Normas de Evaluación y la Lista de Enfermedades Profesionales.
        </div>
        <div class="tipo6">
            {!! $txtcomple2 ?? '<span class="textoedita">TEXTO COMPLEMENTARIO 2</span>' !!}
        </div>
        <div class="tipo6">
            <strong>Documentación Adjunta:</strong>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="tipo10">ESPECIALIDAD</th>
                    <th class="tipo10">DETALLE</th>
                    <th class="tipo10">CANTIDAD</th>
                </tr>
            </thead>
            <tbody>
                @if(count($especialistas) > 0)
                    @foreach ($especialistas as $especialista)
                        <tr>
                            <td>{{ $especialista['especialista'] }}</td>
                            <td>{{ $especialista['detalle'] }}</td>
                            <td class="tipo10">{{ $especialista['cantidad'] }} Pag.</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="textoedita">NINGUNA ESPECIALIDAD AGREGADA</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="tipo6">
            {!! $txtcomple3 ?? '<span class="textoedita">TEXTO COMPLEMENTARIO 3</span>' !!}
        </div>
        <div class="tipo6">
        Agradezco de antemano su atención y colaboración, y quedo a la espera de una respuesta favorable.
        </div>
        <div class="tipo6">
        Atte.
        </div>
        <div class="tipo7" style="margin-top: 60px;"><strong>{{ $nombre }}</strong></div>
        <div class="tipo8">C.I.: {{ $ci }} {{ $ciexp }}</div>
        @if ($emisor === 'APODERADO')
            <div class="tipo7"><strong>APODERADO LEGAL</strong></div>
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
