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
            margin-top: 0px;
            margin-bottom: 15px;
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
            margin-top: 15px;
            margin-bottom: 15px;
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
{{-- <body>
    <main>
        <div class="tipo1">Santa Cruz de la Sierra, {{ $fechaactual }}</div>
        <div class="tipo2">Señores:</div>
        <div class="tipo3">Lic. María Esther Cruz Lopez</div>
        <div class="tipo3">Directora Ejecutiva – APS</div>
        <div class="tipo3">Autoridad de Fiscalización y Control de Pensiones y Seguros</div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5">REF.-CARTA DE RECLAMO DE TRAMITE DE </div>
        <div class="tipo5">{{ $tipocartareclamo }}</div>
        <div class="tipo2">Distinguidos Licenciada:</div>
        <div class="tipo6">
        Yo, el Sr. {{$personal->nombrecompleto}}, con documento de Identidad {{ $personal->ci }}. En Calidad de Apoderado con N.º de poder {{ $numeropoder }}, del Afiliado {{$cliente->nombrecompleto}} con CUA N.º {{$cliente->nuacua}}, con C.I. {{$cliente->ci}} {{$cliente->ciexp}}.
        </div>
        <div class="tipo6">
        Me dirijo a su Autoridad con la finalidad de presentar mi Reclamo, ya que a la fecha no hay ninguna respuesta a mis solicitudes presentadas anteriormente en la Gestora Publica de la Seguridad Social de Largo Plazo. Dentro del Trámite de {{ $tipocartareclamo }}, 
        en fecha {{ $fechaingresotramite  }} se realiza el Ingreso del Tramite, en fecha {{ $fechafirmaeap  }} se firmó la Verificación del Estado de Ahorro Previsional (Extracto) con fecha de retorno para consultar el {{ $fechaeap30  }} (Sin respuesta) se presentó una carta, 
        Posteriormente en fechas {{ $fechaprimeracartasit  }}, {{ $fechasegundacartasit  }} y {{ $fechaterceracartasit  }} se Solicitó Información del Tramite, en fechas {{ $fechaprimeracartareclamo  }}, {{ $fechasegundacartareclamo  }} y {{ $fechaterceracartareclamo  }} se presentó cartas de reclamo. Hasta la fecha de hoy {{ $fechaactual }}
        no se tiene respuesta alguna ni pronunciamiento sobre el Trámite por parte de la Gestora Pública. Por lo que solicito a su AUTORIDAD de la Manera más Humana se pueda hacer un seguimiento y se dé una respuesta a la brevedad posible del presente caso ya que el Afiliado se 
        encuentra angustiado y Molesto por la demora y lamentable respuesta sobre el Trámite por parte de la Gestora Pública, ya que su único interés es poder acceder a una pensión.
        </div>
        <div class="tipo6">
        Adjunto. -
        </div>
        <div class="tipo6">
        Copia de Documentación de Respaldo del Trámite     Folio ({{ $folio }} Hjs.)
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
</body> --}}

<body>
    <main>
        <div class="tipo1">Santa Cruz de la Sierra, {!! $fechaactual ?? '<strong><em style="color: green;">FECHA ACTUAL</em></strong>' !!}</div>
        <div class="tipo2">Señores:</div>
        <div class="tipo3">Lic. María Esther Cruz Lopez</div>
        <div class="tipo3">Directora Ejecutiva – APS</div>
        <div class="tipo3">Autoridad de Fiscalización y Control de Pensiones y Seguros</div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5">REF.- SEGUNDA CARTA DE RECLAMO DE TRAMITE DE </div>
        <div class="tipo5">{!! $tipocartareclamo ?? '<strong><em style="color: green;">TIPO CARTA/RECLAMO</em></strong>' !!}</div>
        <div class="tipo2">Distinguidos Licenciada:</div>
        <div class="tipo6">
            Yo, el Sr. {!! $personal->nombrecompleto ?? '<strong><em style="color: green;">PERSONAL/NOMBRE COMPLETO</em></strong>' !!}, 
            con documento de Identidad {!! $personal->ci ?? '<strong><em style="color: green;">PERSONAL/CI</em></strong>' !!}. 
            En Calidad de Apoderado con N.º de poder {!! $numeropoder ?? '<strong><em style="color: green;">NUMERO DE PODER</em></strong>' !!}, 
            del Afiliado {!! $cliente->nombrecompleto ?? '<strong><em style="color: green;">CLIENTE/NOMBRE COMPLETO</em></strong>' !!} 
            con CUA N.º {!! $cliente->nuacua ?? '<strong><em style="color: green;">CLIENTE/NUA-CUA</em></strong>' !!}, 
            con C.I. {!! $cliente->ci ?? '<strong><em style="color: green;">CLIENTE/CI</em></strong>' !!} {!! $cliente->ciexp ?? '<strong><em style="color: green;">CLIENTE/CI EXP</em></strong>' !!}.
        </div>
        <div class="tipo6">
            Me dirijo a su Autoridad con la finalidad de presentar mi Segundo Reclamo, ya que a la fecha no llega ninguna respuesta a mi solicitud presentada anteriormente 
            y por consiguiente a la fecha NO hay una respuesta Clara y Precisa a mis Solitudes presentadas anteriormente en la Gestora Publica de la Seguridad Social de Largo Plazo. 
            Dentro del Trámite de {!! $tipocartareclamo ?? '<strong><em style="color: green;">TIPO CARTA/RECLAMO</em></strong>' !!}, 
        </div>
        <div class="tipo6">
            Me dirijo a su Autoridad con la finalidad de presentar mi Reclamo, ya que a la fecha no hay ninguna respuesta a mis solicitudes presentadas anteriormente en la Gestora Publica de la Seguridad Social de Largo Plazo. Dentro del Trámite de {!! $tipocartareclamo ?? '<strong><em style="color: green;">TIPO CARTA/RECLAMO</em></strong>' !!}, 
            en fecha {!! $fechaingresotramite ?? '<strong><em style="color: green;">FECHA INGRESO DE TRAMITE</em></strong>' !!} se realiza el Ingreso del Tramite, en fecha {!! $fechafirmaeap ?? '<strong><em style="color: green;">FECHA FIRMA EAP</em></strong>' !!} se firmó la Verificación del Estado de Ahorro Previsional (Extracto) con fecha de retorno para consultar el {!! $fechaeap30 ?? '<strong><em style="color: green;">FECHA EAP</em></strong>' !!} (Sin respuesta) se presentó una carta, 
            posteriormente en fechas {!! $fechaprimeracartasit ?? '<strong><em style="color: green;">FECHA PRIMERA CARTA SIT</em></strong>' !!}, {!! $fechasegundacartasit ?? '<strong><em style="color: green;">FECHA SEGUNDA CARTA SIT</em></strong>' !!} y {!! $fechaterceracartasit ?? '<strong><em style="color: green;">FECHA TERCERA CARTA SIT</em></strong>' !!} se solicitó Información del Tramite, en fechas {!! $fechaprimeracartareclamo ?? '<strong><em style="color: green;">FECHA PRIMERA CARTA RECLAMO</em></strong>' !!}, {!! $fechasegundacartareclamo ?? '<strong><em style="color: green;">FECHA SEGUNDA CARTA RECLAMO</em></strong>' !!} y {!! $fechaterceracartareclamo ?? '<strong><em style="color: green;">FECHA TERCERA CARTA RECLAMO</em></strong>' !!} se presentaron cartas de reclamo. Hasta la fecha de hoy {!! $fechaactual ?? '<strong><em style="color: green;">FECHA ACTUAL</em></strong>' !!} no se tiene respuesta alguna ni pronunciamiento sobre el Trámite por parte de la Gestora Pública. Por lo que solicito a su AUTORIDAD de la manera más humana se pueda hacer un seguimiento y se dé una respuesta a la brevedad posible del presente caso ya que el Afiliado se 
            encuentra angustiado y molesto por la demora y lamentable respuesta sobre el Trámite por parte de la Gestora Pública, ya que su único interés es poder acceder a una pensión.
        </div>
        <div class="tipo6">
            Adjunto. -
        </div>
        <div class="tipo6">
            Copia de Documentación de Respaldo del Trámite Folio ({!! $folio ?? '<strong><em style="color: green;">FOLIO</em></strong>' !!} Hjs.)
        </div>
        <div class="tipo6">
            Sin más que decir y esperando su pronta respuesta me despido con las consideraciones más distinguidas.
        </div>
        <div class="tipo6">
            Atte.<br>
        </div>
        <div class="tipo7">{!! $personal->nombrecompleto ?? '<strong><em style="color: green;">PERSONAL/NOMBRE COMPLETO</em></strong>' !!}</div>
        <div class="tipo8">C.I.{!! $personal->ci ?? '<strong><em style="color: green;">PERSONAL/CI</em></strong>' !!}</div>
        <div class="tipo7">APODERADO</div>
        <div class="tipo8">Teléfono: {!! isset($cliente->celular) ? substr($cliente->celular, 3) : '<strong><em style="color: green;">CLIENTE/CELULAR</em></strong>' !!}</div>
    </main>
</body>

</html>
