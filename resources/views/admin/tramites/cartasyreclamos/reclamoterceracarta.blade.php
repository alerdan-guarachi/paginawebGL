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
            margin-top: 30px;
            margin-bottom: 30px;
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
        .tipo9 {
            font-size: 17px;
            margin-top: 30px;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: left;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main>
        <div class="tipo1">Santa Cruz de la Sierra, {{ $fechaactual }}</div>
        <div class="tipo2">Señores:</div>
        <div class="tipo3">Gestora Publica de la Seguridad Social</div>
        <div class="tipo3">de Largo Plazo.</div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5">REF.-TERCERA CARTA DE RECLAMO DE TRÁMITE DE</div>
        <div class="tipo5">{{ $tipocartareclamo }}</div>
        <div class="tipo2">Distinguidos Señores:</div>
        <div class="tipo6">
        Yo, el Sr. {{$personal->nombrecompleto}}, con documento de Identidad {{ $personal->ci }}. En Calidad de Apoderado con N.º de poder {{ $numeropoder }}, del Afiliado {{$cliente->nombrecompleto}} con CUA N.º {{$cliente->nuacua}}, con C.I. {{$cliente->ci}} {{$cliente->ciexp}}.
        </div>
        <div class="tipo6">
        Me dirijo a su Institución con la finalidad de presentar mi Tercer Reclamo, ya que a la fecha no hubo ninguna respuesta ni pronunciamiento a mis Solicitudes presentadas anteriormente. Dentro del Trámite de {{ $tipocartareclamo }}. Iniciado en su Institución 
        Publica en fecha {{ $fechaingresotramite  }}, y que en fecha {{ $fechafirmaeap  }} se firmó la Verificación del Estado de Ahorro Previsional (Extracto). Posteriormente en fechas {{ $fechaprimeracartasit  }}, {{ $fechasegundacartasit  }} y {{ $fechaterceracartasit  }} se Solicitó Información del Tramite, en fechas 
        {{ $fechaprimeracartareclamo  }} y {{ $fechasegundacartareclamo  }} se Presentó Carta de Reclamo. Hasta la fecha de hoy {{ $fechaactual }} no se tiene ninguna respuesta ni pronunciamiento por parte de su Institución Pública. Solicito y Reitero de la manera más humana se dé una respuesta a la brevedad posible 
        del presente caso, ya que el Afiliado se encuentra delicado de salud, además Angustiado y Molesto por la Demora y lamentable desempeño y respuesta sobre el Trámite por parte de su Institución Pública, ya que el único interés del Afiliado es poder acceder a una pensión.
        </div>
        <div class="tipo6">
        Sin más que decir y esperando su pronta respuesta me despido con las consideraciones más distinguidas.
        </div>
        <div class="tipo6">
        Atte.<br>
        </div>
        <div class="tipo7">{{$personal->nombrecompleto}}</div>
        <div class="tipo8">C.I.{{$personal->ci}}</div>
        <div class="tipo7">APODERADO</div>
        <div class="tipo8">Teléfono: {{ substr($cliente->celular, 3) }}</div>
    </main>
</body>
</html>
