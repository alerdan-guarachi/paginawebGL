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
        <div class="tipo5">REF: SOLICITUD DE HISTORIA CLINICA LEGALIZADA</div>
        <div class="tipo2">Distinguido Doctor. -</div>
        <div class="tipo6">
        Por medio de la presente, tengo a bien a dirigirme a ustedes con la finalidad de solicitar que se me pueda otorgar una copia LEGALIZADA de la HISTORIA CLINICA de mi Ente Gestor de Salud, Mas los Informes y Estudios médicos que me realice de manera particular, 
        mismos que se presentó en fecha {{ $fechainformeestudio }}, para que se pueda incluir a mi Historia Clínica de mi persona {{$cliente->nombrecompleto}} con Matricula Nro. {{ $matricula }}, con C. I. {{$cliente->ci}} {{$cliente->ciexp}} Y se me pueda entregar por la sección que corresponda, 
        mismos que son necesarios para dar por Finalizado mi trámite de Pensión por Invalidez. 
        </div>
        <div class="tipo6">
        Adjunto:
        </div>
        <table>
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
        <div class="tipo8">C.I.{{$cliente->ci}} {{$cliente->ciexp}}</div>
        <div class="tipo8">Teléfono: {{ substr($cliente->celular, 3) }}</div>
    </main>
</body>
</html>
