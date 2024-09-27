<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: 8.5in 11in;
            margin: 0;
        }
        body {
            margin: 1cm 2cm 1cm 2cm;
            background: transparent;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .tipo1 {
            font-size: 17px;
            margin-top: 30px;
            margin-bottom: 10px;
            font-family: Arial, sans-serif;
            text-align: right;
        }
        .tipo2 {
            font-size: 17px;
            margin-top: 10px;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            text-align: left;
        }
        .tipo9 {
            font-size: 17px;
            margin-top: 10px;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            text-align: left;
            text-decoration: underline;
        }
        .tipo3 {
            font-size: 17px;
            font-weight: 1200;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
            text-align: left;
            line-height: 0;
        }
        .tipo4 {
            font-size: 17px;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: left;
            text-decoration: underline;
            line-height: 0;
        }
        .tipo5 {
            font-size: 17px;
            font-weight: 1200;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: right;
            text-decoration: underline;
            line-height: 0;
        }
        .tipo6 {
            font-size: 17px;
            margin-top: 10px;
            margin-bottom: 10px;
            font-family: Arial, sans-serif;
            text-align: justify;
        }
        .tipo7 {
            font-size: 17px;
            font-weight: 1200;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: center;
            line-height: 0;
            margin-bottom: 20px;
        }
        .tipo8 {
            font-size: 17px;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: center;
            line-height: 0;
            margin-bottom: 20px;
        }
        .tipo10 {
            text-align: center;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            line-height: 0.5;
        }
        .table th {
            border: 1px solid #000;
            padding: 8px;
        }
        .table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .bullet {
            width: 20px; /* Ajusta el ancho según sea necesario */
            text-align: center;
        }
        .bullet::before {
            content: '•'; /* Punto como viñeta */
            color: #333; /* Color de la viñeta */
            font-size: 20px; /* Tamaño de la viñeta */
        }
    </style>
</head>
<body>
    <main>
        <div class="tipo1">Santa Cruz de la Sierra, {{ $fechaactual }}</div>
        <div class="tipo2">Señores:</div>
        <div class="tipo3">DR. HASSAN E. BAKRY R.</div>
        <div class="tipo3">Director del hospital Santa Cruz</div>
        <div class="tipo3">Caja petrolera de Salud</div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5">REF: SOLICITUD DE INCLUSION DE INFORMES MEDICOS</div>
        <div class="tipo2">Distinguido Doctor. -</div>
        <div class="tipo6">
        Yo, el Sr. {{$cliente->nombrecompleto}} con Matricula Nro. {{ $matricula }}, con C. I. {{$cliente->ci}} {{$cliente->ciexp}}. Por medio de la presente, tengo a bien a dirigirme a su persona con la finalidad de solicitar la Inclusión de Informes Médicos de Especialidades más Estudios a mi 
        HISTORIA CLINICA. Mismos que me hice atender de manera particular, por Problemas de mi Salud.
        </div>
        <div class="tipo6">
        Adjunto:
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="tipo10">ESPECIALISTA</th>
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
        <div class="tipo7">{{$cliente->nombrecompleto}}</div>
        <div class="tipo8">C.I.{{$cliente->ci}}</div>
        <div class="tipo8">Teléfono: {{ substr($cliente->celular, 3) }}</div>
    </main>
</body>
</html>
