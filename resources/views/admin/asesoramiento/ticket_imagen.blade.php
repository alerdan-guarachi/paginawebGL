<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket Asesoría</title>

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <style>
        body {
            background: #e9ecef;
            font-family: 'Arial', sans-serif;
        }

        #ticket {
            width: 300px;
            background: #ffffff;
            /* border-radius: 14px; */
            padding: 16px;
            margin: 40px auto;
            font-size: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            /* border: 2px dashed #2c3e50; */
        }

        .logo {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo img {
            max-width: 120px;
        }

        h2 {
            text-align: center;
            margin: 6px 0 10px;
            font-size: 16px;
            color: #000000;
        }

        .divider {
            border-top: 1px dashed #aaa;
            margin: 5px 0;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .value {
            color: #111;
            text-align: right;
            max-width: 150px;
        }

        .highlight {
            background: #f1f3f5;
            border-radius: 6px;
            padding: 6px;
            margin: 8px 0;
        }

        .footer {
            margin-top: 10px;
            font-size: 11px;
            text-align: center;
            color: #555;
        }

        .note {
            margin-top: 6px;
            font-size: 10px;
            text-align: center;
            color: #777;
        }
        .ticket-datetime {
            text-align: center;
            font-size: 11px;
            color: #666;
            margin-top: -4px;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>
@php use Carbon\Carbon; @endphp

<div id="ticket">
    <div class="logo">
        <img src="{{ asset('img/logo.png') }}" alt="Logo">
    </div>
    <h2 style="margin-bottom: -7px;">TICKET DE ASESORÍA</h2>
    <h2>N° {{ $ticket->id }}</h2>
    <div class="ticket-datetime">
        Generado el {{ Carbon::parse($ticket->created_at)->translatedFormat('d-m-Y') }} a las {{ Carbon::parse($ticket->created_at)->format('H:i') }}
    </div>
    <div class="divider"></div>
    <div class="item">
        <span class="label">Nombre:</span>
        <span class="value">{{ $ticket->clientenombre }}</span>
    </div>
    <div class="divider"></div>
    <div class="item">
        <span class="label">Celular:</span>
        <span class="value">{{ $ticket->celular }}</span>
    </div>
    <div class="divider"></div>
    <div class="item">
        <span class="label">Motivo:</span>
        <span class="value">{{ $ticket->motivo }}</span>
    </div>
    <div class="divider"></div>
    <div class="item">
        <span class="label">Sucursal:</span>
        <span class="value">{{ $ticket->sucursal }}</span>
    </div>
    <div class="divider"></div>
    <div class="item">
        <span class="label">Fecha:</span>
        <span class="value">{{ Carbon::parse($ticket->fecha)->format('d-m-Y') }}</span>
    </div><div class="divider"></div>
    <div class="item">
        <span class="label">Horario:</span>
        <span class="value">{{ substr($ticket->horadesde, 0, 5) }} - {{ substr($ticket->horahasta, 0, 5) }}</span>
    </div>
    <div class="divider"></div>
    
    @if(strtoupper($ticket->sucursal) === 'SANTA CRUZ')
        <div class="note" style="font-size: 11px;">
            Ubicación: Entre Av. Ana Barba y Calle René Moreno, N° 484
        </div>
        <div class="divider"></div>
    @endif
    <div class="footer">
        Presente este ticket el día de su asesoría
    </div>
    <div class="note">
        --- Asistir 10 minutos antes ---
    </div>
</div>

<script>
    window.onload = function () {
        html2canvas(document.getElementById('ticket'), {
            scale: 2
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'ASESORIA_{{ $ticket->fecha }}_{{ $ticket->horadesde }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    };
</script>

</body>
</html>
