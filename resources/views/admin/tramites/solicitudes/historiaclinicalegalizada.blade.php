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
            @if (strtoupper($nombretramite) === 'INVALIDEZ')
                PENSIÓN POR INVALIDEZ
            @else
                {{ $nombretramite }}
            @endif
        </strong>.

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
        <div class="tipo7" style="margin-top: 80px;"><strong>{{$cliente->nombrecompleto}}</div>
        <div class="tipo8">C.I.: {{$cliente->ci}} {{$cliente->ciexp}}</div>
        <div class="tipo8">Teléfono: {{ substr($cliente->celular, 3) }}</div>
    </main>
</body>
</html>
