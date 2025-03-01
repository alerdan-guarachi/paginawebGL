<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .content {
            margin: 20px;
        }
    </style>
</head>
<body>

    <div class="content" style="text-align: left; margin-top: 50px;">
        <p style="text-align: right;">{{ $ciudad }}, 
            {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
        </p>
        
        <p>Señores:</p>
        <p style="margin-top: 10px;"><strong>{{ $nombreBanco }}</strong></p>
        <p style="margin-bottom: 40px;">Presente.-</p>

        <p style="margin-bottom: 40px;"><strong><u>REF: ACTIVACION DE SEGURO DESGRAVAMEN</u></strong></p>
        <p style="text-align: justify;">Por la presente me dirijo a Uds. Para saludarle primeramente y después realizar la entrega de la
            <strong>Determinación según Dictamen Nro. {{ $dictamen->nrodictamen }} emitido por El Medico Calificador Dra. María Angela Lozano Flores con Matricula Profesional L-655 en fecha {{ \Carbon\Carbon::parse($dictamen->fechadictamen)->format('d/m/Y') }} el que dictamina una Enfermedad del {{ $dictamen->porcentajeinvalidez }}.</strong></p>
        <p style="text-align: justify;">Créditos con Nro. {{ $creditos }} otorgado a mi persona, para fines consiguientes se adjunta fotocopia de carnet de identidad, original certificado de nacimiento. </p>
        
        <p>Adjuntando la siguiente documentación:</p>
        <div class="body" style="margin-top:-20px; font-size: 13px;"> 
            <ul>
                @foreach($documentosSeleccionados as $documento)
                    <li>{{ $documento->documento }} - {{ $documento->hojas }} {{ $documento->hojas > 1 ? 'hojas' : 'hoja' }}</li>
                @endforeach
        
                @foreach($accionesSeleccionadas as $accion)
                    @php
                        $tipoHoja = $accion->accion && (strpos(strtoupper($accion->accion), 'PLACA') !== false || strpos(strtoupper($accion->accion), 'TOMOGRAFIA') !== false || strpos(strtoupper($accion->accion), 'RESONANCIA') !== false) 
                                    ? ($accion->hojas > 1 ? 'placas' : 'placa') 
                                    : ($accion->hojas > 1 ? 'hojas' : 'hoja');
                    @endphp
                    <li>{{ $accion->accion }} - {{ $accion->hojas }} {{ $tipoHoja }}</li>
                @endforeach
            </ul>
        </div>
        
    
        <p style="text-align: center; margin-top: 90px;">{{ $clienteauditorianombre }}</p>
        <p style="text-align: center; margin-top: -10px;">CI: {{ $ci }}</p>

        @if (!empty($clientedos))
            <p style="text-align: center; margin-top: 90px;">{{ $clientedos }}</p>
            <p style="text-align: center; margin-top: -10px;">CI: {{ $clientedosci }}</p>
        @endif

    </div>

</body>
</html>
