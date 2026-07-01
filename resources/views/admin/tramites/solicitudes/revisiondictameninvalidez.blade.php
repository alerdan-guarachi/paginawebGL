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
        <div class="tipo5"><strong>REF.- SOLICITUD DE REVISIÓN DE DICTAMEN DE INVALIDEZ @if(!empty($nrodictamen))
            N.º {{ $nrodictamen }}
        @endif</strong></div>

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
        @if(!empty($tituloop1))
            <div class="tipo6">
                <strong>{!! $tituloop1 !!}</strong>
            </div>
        @endif
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
        @if(!empty($tituloop1))
            <div class="tipo6">
                <strong>{!! $tituloop2 !!}</strong>
            </div>
        @endif
        @if(!empty($opcionesdos) && count($opcionesdos) > 0)
            <table class="tablesinborde" style="margin-top: -5px;">
                <tbody>
                    @foreach ($opcionesdos as $opciondos)
                        <tr>
                            <td class="bullet"></td>
                            <td>{{ $opciondos['opciondos'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <div class="tipo6">
            Por todo lo expuesto, se evidencia que @if ($sexo === 'masculino')el afiliado @elseif ($sexo === 'femenino')la afiliada @endif 
            presenta múltiples patologías crónicas, degenerativas y progresivas, las cuales generan un 
            deterioro significativo tanto en el ámbito físico como emocional, afectando su movilidad, su capacidad funcional y, en consecuencia, su 
            capacidad de generar ingresos mediante actividad laboral. En ese sentido, manifiesto mi desacuerdo con el Dictamen emitido por el Tribunal 
            Médico Calificador, el cual establece una pérdida de capacidad laboral del <strong>{{$dicporcentajereg}}</strong> de<strong>
                @switch($dicorigenreg)
                    @case('RIESGO COMÚN')
                        Origen Común por Enfermedad,
                        @break

                    @case('RIESGO LABORAL')
                        Origen Laboral por Enfermedad,
                        @break

                    @case('RIESGO PROFESIONAL')
                        Riesgo Profesional por Accidente,
                        @break

                    @default
                        <span class="textoedita">RIESGO FALTANTE</span>
                @endswitch
            </strong>
            porcentaje que no refleja adecuadamente la totalidad de las afecciones médicas ni el impacto real en la capacidad laboral  
            @if ($sexo === 'masculino')del asegurado.@elseif ($sexo === 'femenino')de la asegurada.@endif Asimismo, corresponde considerar 
            lo establecido en la Ley de Pensiones N° 065, <strong>Artículo 70 (Calificación)</strong>, que dispone que la calificación del grado, origen y causa de la 
            invalidez debe realizarse de forma integral, conforme al Manual Único de Calificación compuesto por el Manual de Normas de Evaluación y 
            Calificación del Grado de Invalidez y la Lista de Enfermedades Profesionales. En consecuencia, solicito respetuosamente que el caso sea 
            revisado, realizando una evaluación integral y exhaustiva del expediente médico, considerando la totalidad de los diagnósticos y antecedentes 
            clínicos presentados, a fin de que la calificación del grado de invalidez refleje de manera justa y objetiva la real condición de salud del 
            afiliado.
        </div>
        <div class="tipo6">
            <strong>POR TANTO</strong>, solicito se admita la presente solicitud y se disponga la Revisión del Dictamen 
            N° {!! $nrodictamen ?? '<span class="textoedita">NRO. DICTAMEN</span>' !!}, conforme a la normativa vigente.
        </div>
        @if(!empty($txtcomple2))
            <div class="tipo6">
                {!! $txtcomple2 !!}
            </div>
        @endif
        @if(!empty($especialistas) && count($especialistas) > 0)
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
                    @foreach ($especialistas as $especialista)
                        <tr>
                            <td>{{ $especialista['especialista'] }}</td>
                            <td>{{ $especialista['detalle'] }}</td>
                            <td class="tipo10">{{ $especialista['cantidad'] }} Pag.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if(!empty($txtcomple3))
            <div class="tipo6">
                {!! $txtcomple3 !!}
            </div>
        @endif
        <div class="tipo6">
        Sin otro particular, agradezco de antemano la atención brindada y quedo a la espera de una respuesta favorable.
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
