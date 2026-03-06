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
        copia <strong>LEGALIZADA</strong> de la <strong>HISTORIA CLINICA</strong>
        @if(!is_null($fechainclusion))
            más los <strong>Informes y Estudios</strong> médicos que me realice de manera particular, 
            mismos que se presentó en fecha <strong>{{ $fechainclusion }}</strong>
            para que se pueda incluir a mi Historia Clínica
        @endif
        de mi persona <strong>{{$cliente->nombrecompleto}}</strong>
        con Matricula Nro. <strong>{{ $matricula }}</strong> con C.I. <strong>{{$cliente->ci}} {{ $cliente->ciexp }}</strong>. 
        Y se me pueda entregar por la sección que corresponda, mismos que son necesarios para mi trámite de
        @php
            $tramite = strtoupper($nombretramite);
        @endphp
        @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            <strong>PENSIÓN POR INVALIDEZ (RECALIFICACIÓN).</strong>
        @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
            <strong>PENSIÓN POR INVALIDEZ.</strong>
        @else
            <strong>{{ $nombretramite }}.</strong>
        @endif
        </div>
        <div class="tipo6">
        Adjunto:
        </div>

        <ul class="lista-documentos">
            <li>Carnet de Identidad (Fotocopia).</li>
            <li>Carnet de Asegurado (Fotocopia).</li>
            @if(!is_null($fechainclusion))
            <li>Carta de Recepción (Fotocopia).</li>
            @endif
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
