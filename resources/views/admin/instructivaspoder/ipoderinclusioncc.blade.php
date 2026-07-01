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
        h1 {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: center;
            text-decoration: underline;
        }
        h3 {
            font-weight: 400;
            font-size: 15px;
            font-family: Arial, sans-serif;
            text-align: justify;
            line-height: 1.5;
        }
        h4 {
            font-size: 15px;
            font-family: Arial, sans-serif;
            text-align: justify;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <main>
        <h1>INSTRUCTIVA DE PODER</h1>
        <h3><strong>QUE OTORGA:</strong> El Sr/a. {{$cliente->nombrecompleto}} con C.I. {{$cliente->ci}} {{$cliente->ciexp}}, 
            {{ $estadoCivil }}, mayor de edad, Ocupación {{$cliente->ocupacion}}, con domicilio en {{$cliente->domicilio}} - {{$cliente->ciudadresidencia}}
             y hábil por derecho. <br>
            <strong>A FAVOR DE:</strong>Al Sr. FABRICIO ORLANDO PRADO PARRADO con C.I. 5505371, mayor de edad con domicilio en la ciudad de SANTA CRUZ y hábil por derecho, 
            y/o al Sra. DENISSE MAUREN LOPEZ FLORES Con C.I. No. 5211568, mayor de edad, con domicilio en la ciudad de SANTA CRUZ y hábil por derecho; 
            @if ($sucursal === 'COCHABAMBA')
            y/o al Sr. EUDAL AGUIRRE RODRIGUEZ Con C.I. No. 10360406, mayor de edad, con domicilio en la ciudad de SANTA CRUZ y hábil por derecho; 
            @endif
            @foreach($personal as $persona)
                y/o al Sr/a. {{ $persona->razonsocial }} con C.I. {{ $persona->ci }} {{ $persona->ciexp }}, mayor de edad, con domicilio en la ciudad de {{ $persona->ciudad }} y hábil por derecho{{ !$loop->last ? ';' : '.' }}
            @endforeach
            <br>
            <strong>OBJETO:</strong> Para que en nombre y representación de su persona acciones y derechos, uno Indistintamente de otro, con facultades de 
            Apersonamiento de manera individual y/o colectiva, ante las oficinas de GESTORA PÚBLICA DE LA SEGURIDAD SOCIAL DE LARGO PLAZO, para Inicio, 
            Seguimiento y Conclusión de Tramite de <strong>INCLUSION DE COMPENZACION DE COTIZACIONES MENSUAL Y/O GLOBAL</strong>. Iniciar el Trámite de la Inclusión de la 
            Compensación de Cotizaciones mensual y/o global, Firmar el formulario de la Inclusión de la Compensación de Cotizaciones mensual y/o global 
            firmando la solicitud de corresponda, Recibir la notificación del Anexo/Adenda de Prestaciones y Pagos del Sistema Integral de Pensiones, 
            Solicitar el Estado de Ahorro Previsional, Revisar, Verificar, Solicitar regularización de aportes si corresponde, Firmar el Certificado de 
            Verificación del Estado de Ahorro Previsional, así como, el Formulario de conformidad de aportes si en caso corresponde, firmar los formularios 
            y/o solicitudes que correspondan hasta su culminación. Asimismo, solicitar, consultar, verificar y gestionar la habilitación de pago y realizar 
            gestiones y seguimiento del mismo. Más Poder para apersonamiento ante las oficinas de la Autoridad de Fiscalización y Control de Pensiones y 
            Seguros APS para realizar toda clase de gestiones relacionadas con el trámite, pudiendo solicitar información verbal y escrita, Facultad para 
            llenar, suscribir y presentar formularios físicos o digitales, incluidos aquellos correspondientes al Buzón de Reclamos y Sugerencias, presentar 
            cartas de reclamo, memoriales, solicitudes, peticiones, reclamos administrativos y cualquier otra actuación necesaria, realizar seguimiento a 
            reclamos y solicitudes, notificarse y recoger respuestas, informes, notas y cualquier otra documentación relacionada con el trámite. Del poder 
            conferente, sin que, por no estar expresamente consignado en el presente, sea dado por insuficiente, aclarándose que todas las cláusulas de 
            este poder son enunciativas y no limitativas, facultando a los apoderados a otorgar poder en favor de terceros o ser sustituidos total y/o 
            parcialmente del presente mandato, en caso de viaje, enfermedad, ausencia o dejación de cargo.
            </h3>
            <br>
            <h3 style="text-align: center;">{{ $cliente->sucursal }} - {{ $fechaactual }}</h3>
    </main>
</body>
</html>
