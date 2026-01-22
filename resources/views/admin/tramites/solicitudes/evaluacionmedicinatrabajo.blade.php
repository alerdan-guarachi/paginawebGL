<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('css/prestacionescartas.css') }}">
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
        <div class="tipo5"><strong>REF: SOLICITUD DE EVALUACION DE {{ $nombretramite }} POR</strong></div>
        <div class="tipo5"><strong>MEDICINA DEL TRABAJO</strong></div>
        <div class="tipo2">Distinguido Doctor. -</div>
        <div class="tipo6">
        Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{$cliente->nombrecompleto}}</strong> con Matricula Nro. <strong>{{ $matricula }}</strong> con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>. 
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
