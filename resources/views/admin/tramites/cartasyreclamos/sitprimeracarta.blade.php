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
    </style>
</head>
{{-- <body> 
    <main>
        <div class="tipo1">Santa Cruz de la Sierra, {{ $fechaactual ?? 'FECHA ACTUAL' }}</div>
        <div class="tipo2">Señores:</div>
        <div class="tipo3">Gestora Publica de la Seguridad Social</div>
        <div class="tipo3">de Largo Plazo.</div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5">REF.-SOLICITUD DE INFORMACIÓN DE TRÁMITE DE</div>
        <div class="tipo5">{{ $tipocartareclamo ?? 'TIPO CARTA/RECLAMO' }}</div>
        <div class="tipo2">Distinguidos Señores:</div>
        <div class="tipo6">
            Yo, el Sr. {{ $personal->nombrecompleto ?? 'PERSONAL/NOMBRE COMPLETO' }}, con documento de Identidad {{ $personal->ci ?? 'PERSONAL/CI' }}.
            En Calidad de Apoderado con N.º de poder {{ $numeropoder ?? 'CLIENTE/NUMERO DE PODER' }}, del Afiliado {{ $cliente->nombrecompleto ?? 'CLIENTE/NOMBRE COMPLETO' }} con CUA N.º {{ $cliente->nuacua ?? 'CLIENTE/ NUA-CUA' }}, con C.I. {{ $cliente->ci ?? 'CLIENTE/CI' }} {{ $cliente->ciexp ?? 'CLIENTE/CI EXP' }}.
        </div>
        <div class="tipo6">
            Me dirijo a su Institución con la finalidad de solicitar Información sobre el estado del Trámite de {{ $tipocartareclamo ?? 'TIPO CARTA/RECLAMO' }}. En fecha {{ $fechaingresotramite ?? 'FECHA INGRESO DE TRAMITE' }} se Ingresó el Trámite, y en fecha {{ $fechafirmaeap ?? 'FECHA FIRMA EAP' }} se firmó la Verificación del Estado de Ahorro Previsional 
            (Extracto), en donde indican, que se debe consultar sobre el Trámite en 30 Días. Hasta la fecha no se tiene respuesta alguna sobre el Trámite, por lo que solicito se haga un seguimiento y se dé una respuesta del presente caso.
        </div>
        <div class="tipo6">
            Sin más que decir y esperando su pronta respuesta me despido con las consideraciones más distinguidas.
        </div>
        <div class="tipo6">
            Atte.<br><br><br>
        </div>
        <div class="tipo7">{{ $personal->nombrecompleto ?? 'PERSONAL/NOMBRE COMPLETO' }}</div>
        <div class="tipo8">C.I.{{ $personal->ci ?? 'PERSONAL/CI' }}</div>
        <div class="tipo7">APODERADO</div>
        <div class="tipo8">Teléfono: {{ isset($cliente->celular) ? substr($cliente->celular, 3) : 'PERSONAL/CELULAR' }}</div>
    </main>
</body> --}}

<body>  
    <main>
        <div class="tipo1">Santa Cruz de la Sierra, {!! $fechaactual ?? '<strong><em style="color: green;">FECHA ACTUAL</em></strong>' !!}</div>
        <div class="tipo2">Señores:</div>
        <div class="tipo3">Gestora Publica de la Seguridad Social</div>
        <div class="tipo3">de Largo Plazo.</div>
        <div class="tipo9">Presente. -</div>
        <div class="tipo5">REF.-SOLICITUD DE INFORMACIÓN DE TRÁMITE DE</div>
        <div class="tipo5">{!! $tipocartareclamo ?? '<strong><em style="color: green;">TIPO CARTA/RECLAMO</em></strong>' !!}</div>
        <div class="tipo2">Distinguidos Señores:</div>
        <div class="tipo6">
            Yo, el Sr. {!! $personal->nombrecompleto ?? '<strong><em style="color: green;">PERSONAL/NOMBRECOMPLETO</em></strong>' !!}, con documento de Identidad {!! $personal->ci ?? '<strong><em style="color: green;">PERSONAL/CI</em></strong>' !!}.
            En Calidad de Apoderado con N.º de poder {!! $numeropoder ?? '<strong><em style="color: green;">CLIENTE/NUMEROPODER</em></strong>' !!}, del Afiliado {!! $cliente->nombrecompleto ?? '<strong><em style="color: green;">CLIENTE/NOMBRECOMPLETO</em></strong>' !!} con CUA N.º {!! $cliente->nuacua ?? '<strong><em style="color: green;">CLIENTE/NUA-CUA</em></strong>' !!}, con C.I. {!! $cliente->ci ?? '<strong><em style="color: green;">CLIENTE/CI</em></strong>' !!} {!! $cliente->ciexp ?? '<strong><em style="color: green;">CLIENTE/CIEXP</em></strong>' !!}.
        </div>
        <div class="tipo6">
            Me dirijo a su Institución con la finalidad de solicitar Información sobre el estado del Trámite de {!! $tipocartareclamo ?? '<strong><em style="color: green;">TIPO CARTA/RECLAMO</em></strong>' !!}. En fecha {!! $fechaingresotramite ?? '<strong><em style="color: green;">FECHA INGRESOTRAMITE</em></strong>' !!} se Ingresó el Trámite, y en fecha {!! $fechafirmaeap ?? '<strong><em style="color: green;">FECHA FIRMAEAP</em></strong>' !!} se firmó la Verificación del Estado de Ahorro Previsional 
            (Extracto), en donde indican, que se debe consultar sobre el Trámite en 30 Días. Hasta la fecha no se tiene respuesta alguna sobre el Trámite, por lo que solicito se haga un seguimiento y se dé una respuesta del presente caso.
        </div>
        <div class="tipo6">
            Sin más que decir y esperando su pronta respuesta me despido con las consideraciones más distinguidas.
        </div>
        <div class="tipo6">
            Atte.<br><br><br>
        </div>
        <div class="tipo7">{!! $personal->nombrecompleto ?? '<strong><em style="color: green;">PERSONAL/NOMBRECOMPLETO</em></strong>' !!}</div>
        <div class="tipo8">C.I.{!! $personal->ci ?? '<strong><em style="color: green;">PERSONAL/CI</em></strong>' !!}</div>
        <div class="tipo7">APODERADO</div>
        <div class="tipo8">Teléfono: {!! isset($cliente->celular) ? substr($cliente->celular, 3) : '<strong><em style="color: green;">PERSONAL/CELULAR</em></strong>' !!}</div>
    </main>
</body>

</html>
