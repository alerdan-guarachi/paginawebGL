<!DOCTYPE html>
<html>
<head>
<style>
    @page {
        size: 8.5in 11in;
        margin: 0;
        font-family: Arial, sans-serif;
        font-size: 11pt;
    }
    body {
        margin: 1cm 2cm 1cm 2cm;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 11pt;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
        line-height: 1.0;
    }
    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        font-family: Arial, sans-serif;
        font-size: 11pt;
    }
    .tipo1 {
        margin-top: 30px;
        margin-bottom: 10px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
        text-align: right;
    }
    .tipo2 {
        font-size: 17px;
        margin-top: 10px;
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
        text-align: left;
    }
    .tipo9 {
        margin-top: 10px;
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
        text-align: left;
        text-decoration: underline;
    }
    .tipo3 {
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
        text-align: left;
        line-height: 0;
    }
    .tipo4 {
        margin-bottom: 30px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
        text-align: left;
        text-decoration: underline;
        line-height: 0;
    }
    .tipo5 {
        margin-bottom: 30px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
        text-align: right;
        text-decoration: underline;
        line-height: 0;
    }
    .tipo6 {
        margin-top: 10px;
        margin-bottom: 10px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
        text-align: justify;
    }
    .tipo7 {
        margin-bottom: 30px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
        text-align: center;
        line-height: 0;
        margin-bottom: 20px;
    }
    .tipo8 {
        margin-bottom: 30px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
        text-align: center;
        line-height: 0;
        margin-bottom: 20px;
    }
    .tipo10 {
        text-align: center;
        font-family: Arial, sans-serif;
        font-size: 11pt;
    }
    .table th {
        border: 1px solid #000;
        padding: 8px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
    }
    .table td {
        border: 1px solid #000;
        padding: 8px;
        font-family: Arial, sans-serif;
        font-size: 11pt;
    }
</style>
</head>
<body>
    <main>
        <div class="tipo1">Santa Cruz de la Sierra, {{ $fechaactual }}</div>
        <div class="tipo2">Señores:</div>
        <div class="tipo3"><strong>Gestora Publica de la Seguridad Social</strong></div>
        <div class="tipo3"><strong>de Largo Plazo.</strong></div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5"><strong>REF.- ADJUNTO DOCUMENTACIÓN MEDICA RESPUESTA AL ACTA TCM</strong></div>
        <div class="tipo5"><strong>(TRÁMITE DE {{ $tipocartareclamo }})</strong></div>
        <div class="tipo2">Distinguidos Señores:</div>
        <div class="tipo6">
        Yo, el Sr. <strong>{{$personal->razonsocial}}</strong>, con documento de Identidad <strong>{{ $personal->ci }}</strong>. En Calidad de Apoderado con N.º de poder <strong>{{ $numeropoder }}</strong>, del Afiliado <strong>{{$cliente->nombrecompleto}}</strong> con CUA N.º <strong>{{$cliente->nuacua}}</strong>, con C.I. <strong>{{$cliente->ci}} {{$cliente->ciexp}}</strong>.
        </div>
        <div class="tipo6">
        Me dirijo a su Institución con la finalidad de brindar respuesta al Acta del Tribunal Medico Calificador (TCM) de la Entidad Encargada de Calificar (EEC) Habiendo sido notificado por su Institución Publica con NOTA CITE <strong>{{ $notatecnicomedico }}</strong> de fecha <strong>{{ $fechanotatecnicomedico }}</strong>. Dentro del Tramite de <strong>{{ $tipocartareclamo }}</strong>. 
        </div>
        <div class="tipo6">
        En respuesta Adjunto.
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
        Por último, deseo manifestarles mi más sentido agradecimiento por la ayuda y colaboración, esperando su pronta respuesta me despido cordialmente.
        </div>
        <div class="tipo6">
        Atte.<br><br><br><br><br>
        </div>
        <div class="tipo7"><strong>{{$personal->razonsocial}}</strong></div>
        <div class="tipo8">C.I.{{$personal->ci}}</div>
        <div class="tipo7"><strong>APODERADO</strong></div>
        <div class="tipo8">Teléfono: {{-- {{ substr($cliente->celular, 3) }} --}}{{ $personal->celular }}</div>
    </main>
</body>
</html>
