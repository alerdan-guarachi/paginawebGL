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
        <div class="tipo5"><strong>REF: SOLICITUD DE INCLUSIÓN DE INFORMES MÉDICOS</strong></div>
        <div class="tipo2">Distinguido Doctor. -</div>
        <div class="tipo6">
        Yo, el Sr. <strong>{{$cliente->nombrecompleto}}</strong> con Matricula Nro. <strong>{{ $matricula }}</strong>, con C.I. 
        <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>. 
        Por medio de la presente, tengo a bien a dirigirme a su persona con la finalidad de solicitar la Inclusión de 
        <strong>Certificados Médicos de Especialidades más Informes de Estudios</strong> a mi <strong>HISTORIA CLINICA</strong>. 
        Mismos que me hice atender de manera particular, por Problemas de mi Salud.
        </div>
        <div class="tipo6">
        Adjunto:
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
                            <td class="tipo10">{{ $especialista['cantidad'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3">No hay datos disponibles</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="tipo6">
        Sin otro particular, me despido no sin antes deseándoles éxito en sus actividades cotidianas.
        </div>
        <div class="tipo6">
        Atte.<br><br>
        </div>
        <div class="tipo7" style="margin-top: 80px;"><strong>{{$cliente->nombrecompleto}}</div>
        <div class="tipo8">C.I.: {{$cliente->ci}}</div>
        <div class="tipo8">Teléfono: {{ substr($cliente->celular, 3) }}</div>
    </main>
</body>
</html>
