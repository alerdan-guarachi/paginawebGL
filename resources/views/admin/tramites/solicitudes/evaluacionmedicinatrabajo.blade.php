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
        <div class="tipo5"><strong>REF: SOLICITUD DE EVALUACION DE INVALIDEZ POR</strong></div>
        <div class="tipo5"><strong>MEDICINA DEL TRABAJO</strong></div>
        <div class="tipo2">Distinguido Doctor. -</div>
        <div class="tipo6">
        Yo, el Sr. <strong>{{$cliente->nombrecompleto}}</strong> con Matricula Nro. <strong>{{ $matricula }}</strong> con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>. 
        Mediante la presente tengo a Bien dirigirme a su Persona con la finalidad de solicitar que por los medios que corresponda 
        se realice la valoración correspondiente de mi Grado de Invalidez con Medicina del Trabajo, para la presentación ante la <strong>{{$cliente->afp}}</strong>.
        </div>
        <div class="tipo6">
        Adjunto:
        </div>
        <ul class="lista-documentos">
            <li>Copia Cédula de Identidad.</li>
            <li>Copia del Carnet del Seguro.</li>
            <li>Copia del Extracto de la Gestora Pública de Seguridad Social de Largo Plazo.</li>
            <li>Copia de la Solicitud de la Gestora Pública de Seguridad Social de Largo Plazo.</li>
        </ul>

        <div class="tipo6">
        Sin otro particular, me despido no sin antes agradecerle por su colaboración y deseándole éxito en sus actividades cotidianas.
        </div>
        <div class="tipo6">Atte.</div>

        <div class="tipo7" style="margin-top: 80px;"><strong>{{$cliente->nombrecompleto}}</div>
        <div class="tipo8">C.I.: {{$cliente->ci}} {{$cliente->ciexp}}</div>
        <div class="tipo8">Teléfono: {{ substr($cliente->celular, 3) }}</div>
    </main>
</body>
</html>
