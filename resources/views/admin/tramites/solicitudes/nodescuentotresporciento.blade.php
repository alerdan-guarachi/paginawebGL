<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('css/prestacionescartas.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/prestacionescartas.css') }}?v={{ filemtime(public_path('css/prestacionescartas.css')) }}"> --}}
    <style>
        table td {
            padding: 2px 5px;
            line-height: 1.2;
        }
        .tableinter td {
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
        <div class="tipo2">Señor(es):</div>
        <div class="tipo3"><strong>{{ $nombremedico }}</strong></div>
        <div class="tipo3"><strong>{{ $cargomedico }}</strong></div>
        <div class="tipo3"><strong>Gestora Publica de la Seguridad Social de Largo Plazo</strong></div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5"><strong>REF: SUSPENSION DESCUENTO 3% CAJA DE SALUD PARA</strong></div>
        <div class="tipo5">
            @php
                $tramite = strtoupper($nombretramite);
            @endphp
            @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                <strong>PENSIÓN POR INVALIDEZ (RECALIFICACIÓN)</strong>
            @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
                <strong>PENSIÓN POR INVALIDEZ</strong>
            @else
                <strong>{{ $nombretramite }}</strong>
            @endif
        </div>
        <div class="tipo2">Distinguido Doctor. -</div>
        <div class="tipo6" style="margin-top: -15px;">
        Por medio de la presente Solicito a ustedes tomar en cuenta mis datos para que <strong>NO</strong> me descuenten el 3% para el 
        Seguro de Salud de mi 
        @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            <strong>PENSIÓN POR INVALIDEZ (RECALIFICACIÓN)</strong>
        @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
            <strong>PENSIÓN POR INVALIDEZ</strong>
        @else
            <strong>{{ $nombretramite }}</strong>
        @endif
        ya que me encuentro Afiliado por la Empresa <strong>{{ $empresacliente }}</strong>, Quienes hacen los aportes a la
        <strong>{{$cliente->aseguradora}}</strong>.
        </div>
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
        Sin otro particular me despido de ustedes con las consideraciones más distinguidas.
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
