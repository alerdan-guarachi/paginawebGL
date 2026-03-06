<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket Asesoría</title>

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <style>
        @page {
            margin: 5px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 5 5 5 5;
            padding: 5px;
        }

        .logo {
            text-align: center;
            margin-bottom: 4px;
        }

        .logo img {
            width: 200px;
        }

        .titulo {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 2px 0;
        }

        .subtitulo {
            text-align: center;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        .item {
            width: 100%;
            margin: 2px 0;
            overflow: hidden;
        }

        .clear {
            clear: both;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 6px;
        }
        .note {
            text-align: center;
            font-size: 9px;
        }
        .ticket-datetime {
            text-align: center;
            font-size: 9px;
            margin-top: -10px;
            margin-bottom: 15px;
        }
        h2 { 
            text-align: center; 
            margin: 6px 0 10px; 
            font-size: 13px; 
            color: #000000; 
        }
        .logo { 
            text-align: center; 
            margin-bottom: 8px; 
        } 
        .logo img { 
            max-width: 120px; 
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2px 0;
        }

        .item-table td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 11px;
        }

        .label {
            font-weight: bold;
            width: 35%;
        }

        .value {
            text-align: right;
            width: 65%;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
@php use Carbon\Carbon; @endphp

<div id="ticket">
    @php
        $path = public_path('img/logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    @endphp

    <div class="logo">
        <img src="{{ $base64 }}" alt="Logo">
    </div>
    <h2 style="margin-bottom: -7px; margin-top: -5px;">TICKET DE ASESORÍA</h2>
    <h2>N° {{ $ticket->id }}</h2>
    <div class="ticket-datetime">
        Generado el {{ Carbon::parse($ticket->created_at)->translatedFormat('d-m-Y') }} a las {{ Carbon::parse($ticket->created_at)->format('H:i') }}
    </div>
    <div class="divider"></div>
    <table class="item-table">
    <tr>
        <td class="label">Nombre:</td>
        <td class="value">{{ $ticket->clientenombre }}</td>
    </tr>
</table>
<div class="divider"></div>

<table class="item-table">
    <tr>
        <td class="label">Ciudad:</td>
        <td class="value">{{ $ticket->sucursal }}</td>
    </tr>
</table>
<div class="divider"></div>

<table class="item-table">
    <tr>
        <td class="label">Modalidad:</td>
        <td class="value">{{ $ticket->modalidad }}</td>
    </tr>
</table>
<div class="divider"></div>

<table class="item-table">
    <tr>
        <td class="label">Motivo:</td>
        <td class="value">{{ $ticket->motivo }}</td>
    </tr>
</table>
<div class="divider"></div>



<table class="item-table">
    <tr>
        <td class="label">Fecha:</td>
        <td class="value">{{ Carbon::parse($ticket->fecha)->format('d-m-Y') }}</td>
    </tr>
</table>
<div class="divider"></div>

<table class="item-table">
    <tr>
        <td class="label">Hora:</td>
        <td class="value">
            {{ substr($ticket->horadesde, 0, 5) }} - {{ substr($ticket->horahasta, 0, 5) }}
        </td>
    </tr>
</table>
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
</body>
</html>
