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
            <strong>OBJETO:</strong> Para que en representación de su persona acciones y derechos, uno Indistintamente de otro, 
            con facultades de Apersonamiento de manera individual y/o colectiva ante las oficinas de Gestora Pública de la 
            Seguridad Social de Largo Plazo, para Inicio, Continuidad, Seguimiento y Conclusión de 
            <strong>TRAMITE DE PRESTACIÓN DE VEJEZ, Y PRESTACIÓN SOLIDARIA DE VEJEZ,</strong> en el marco de lo establecido en 
            la  Ley 1732 y la Ley 065 Solicitar cálculos o proyecciones de Pensión de Vejez, Solicitar el Estado de Ahorro 
            Previsional, Solicitar el Certificado de Verificación del Estado de Ahorro Previsional, Iniciar Tramite de Pensión 
            de Vejez firmando la solicitud que corresponda, Firmar y Recibir la notificación con la Declaración de Pensión de 
            Vejez, Desistir de la Solicitud antes de la Notificación con la declaración de Pensión de Vejez, Firmar el formulario 
            de cesación o continuación de aportes al SIP, en suma solicitar, recibir, entregar y firmar todo tipo de documentos 
            inherentes al trámite de Pensión de Vejez hasta su Culminación. Sin que por falta de cláusula expresa alguna, deje 
            de surtir sus efectos, ni se alegue falta de personería en los apoderados. Más Poder, para recabar Estado de Cuentas, 
            firmar el formulario de Solicitud, firmar formulario de declaración de Derechohabientes, Solicitar y firmar el 
            formulario de Actualización de datos del Asegurado Indicando específicamente los datos que serán incluidos o 
            excluidos, Solicitar copias legalizadas y simples de cualquier ámbito con relación a dicho trámite, asistir a 
            reuniones con facultades de decisión, memoriales, cartas notariales, inscripciones, autorizaciones y cualquier 
            otro documento que sea necesario, hasta la culminación del trámite en las oficinas de la Gestora Publica de la 
            Seguridad Social de Largo Plazo. Más Poder.- para sustituir total o individual el presente poder, y en caso de ser 
            necesario otorgar poder a favor de terceros u otras personas para la continuación y conclusión del presente trámite, 
            todo con el fin de solicitar <strong>TRAMITE DE PRESTACIÓN DE VEJEZ, Y PRESTACIÓN SOLIDARIA DE VEJEZ,</strong> 
            Más Poder.- Para iniciar el trámite de RETIROS MÍNIMOS,RETIRO FINAL Y/O PAGO DE COMPENSACIÓN DE COTIZACIONES 
            MENSUAL Y GLOBAL firmando las solicitudes que correspondan hasta su culminación, firmar Formulario Solicitud de 
            verificación de Estado De Cuentas, recabar Estado de Cuentas y Certificado de Verificación de Estado de Cuentas y 
            firmar contrato de Retiros Mínimos, Retiro Final, pago de compensación de cotizaciones y firma de liquidación en la 
            Gestora Publica de la Seguridad Social de Largo Plazo. Del poder conferente, sin que, por no estar expresamente 
            consignado en el presente, sea dado por insuficiente, aclarándose que todas las cláusulas de este poder son 
            enunciativas y no limitativas, facultando a los apoderados a otorgar poder en favor de terceros o ser sustituidos 
            total y/o parcialmente del presente mandato, en caso de viaje, enfermedad, ausencia o dejación de cargo.
            </h3>
            <br>
            <h3 style="text-align: center;">{{ $cliente->sucursal }} - {{ $fechaactual }}</h3>
    </main>
</body>
</html>
