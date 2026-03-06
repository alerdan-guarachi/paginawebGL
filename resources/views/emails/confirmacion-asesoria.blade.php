<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif;">

    <p>Estimado(a) {{ $programacion->clientenombre }},</p>

    <p>
        Le informamos que su asesoría ha sido confirmada y
        <strong>sí se llevará a cabo en la fecha y hora programada</strong>.
    </p>

    <hr>

    <p><strong>Detalle de la asesoría:</strong></p>

    <ul>
        <li><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($programacion->fecha)->format('d/m/Y') }}</li>
        <li>
            <strong>Horario:</strong> 
            {{ \Carbon\Carbon::parse($programacion->horadesde)->format('H:i') }} 
            - 
            {{ \Carbon\Carbon::parse($programacion->horahasta)->format('H:i') }}
        </li>
        <li><strong>Ciudad:</strong> {{ $programacion->sucursal }}</li>
        <li><strong>Modalidad:</strong> PRESENCIAL</li>
        <li><strong>Motivo:</strong> {{ $programacion->motivo }}</li>
    </ul>

    <hr>

    <p>
        Le recomendamos presentarse con al menos 10 minutos de anticipación.
    </p>

    <p>
        Gracias por confiar en nosotros.
    </p>

    <p>
        Atentamente,<br>
        <strong>Asesorías Good Life</strong>
    </p>

</body>
</html>