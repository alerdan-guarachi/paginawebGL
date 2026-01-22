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
        <div class="tipo2">Señor(es):</div>
        <div class="tipo3"><strong>{{ $nombremedico }}</strong></div>
        <div class="tipo3"><strong>{{ $cargomedico }}</strong></div>
        <div class="tipo3"><strong>{{$cliente->aseguradora}}</strong></div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5"><strong>REF: SOLICITUD DE HISTORIA CLINICA LEGALIZADA</strong></div>
        <div class="tipo2">Distinguido Doctor. -</div>

        <div class="tipo6">
        Por medio de la presente, tengo a bien a dirigirme a ustedes con la finalidad de solicitar que se me pueda otorgar una 
        copia <strong>LEGALIZADA</strong> de la <strong>HISTORIA CLINICA</strong> de mi persona <strong>{{$cliente->nombrecompleto}}</strong>
        con Matricula Nro. <strong>{{ $matricula }}</strong> con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>. 
        Y se me pueda entregar por la sección que corresponda, mismos que son necesarios para mi trámite de
        <strong>
            @if (
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
            @endif
        </strong>

        </div>
        <div class="tipo6">
        Adjunto:
        </div>
        {{-- <table>
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
                        <td colspan="3">No hay datos disponibles</td>
                    </tr>
                @endif
            </tbody>
        </table> --}}

        <ul class="lista-documentos">
            <li>Carnet de Identidad (Fotocopia).</li>
            <li>Carnet de Asegurado (Fotocopia).</li>
        </ul>

        <div class="tipo6">
        Sin otro particular, me despido no sin antes deseándoles éxito en sus actividades cotidianas.
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
