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
        .textoedita {
            color: #e81313;              
            font-style: italic;          
            text-decoration: underline; 
            font-weight: bold;
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
            con facultades de apersonamiento de manera individual y/o colectiva ante las oficinas de Gestora Pública de la 
            Seguridad Social de Largo Plazo, para Inicio, Continuidad, Seguimiento y Conclusión del 
            <strong>TRAMITE DE PENSION POR MUERTE {!! $tipopension ?? '<span class="textoedita">TIPO PENSIÓN</span>' !!},</strong> en el marco de la Ley 065 Solicitar Iniciar tramite de Pensión por Muerte firmando 
            la solicitud correspondiente, solicitar el estado de ahorro previsional, consolidar el estado de ahorro previsional, 
            recibir requerimientos del Tribunal Medico Calificador TMC, recibir las notificaciones del dictamen de muerte, 
            solicitar revisión del dictamen de muerte, recibir la notificación de la declaración de pensión o la notificación 
            del rechazo según corresponda, en suma solicitar, recibir, entregar, y firmar todo tipo de documentos inherentes 
            al trámite de pensión por muerte hasta su culminación. Sin que por falta de cláusula expresa alguna, deje de surtir 
            sus efectos, ni se alegue falta de personería en los apoderados. Iniciar con el TRAMITE DE MASA HEREDITARIA, 
            en el marco de la Ley 065 Solicitar Entregar documentos de acreditación de los herederos para su revisión, solicitar 
            el estado de ahorro previsional, consolidar el estado de ahorro previsional, Iniciar tramite de masa hereditaria 
            firmando la solicitud correspondiente, firmar la liquidación y acta de entrega de masa hereditaria 
            (pensiones no cobradas), firmar contrato de entrega de masa hereditaria (cuenta Individual), en suma solicitar, 
            recibir, entregar, y firmar todo tipo de documentos inherentes al trámite de masa hereditaria hasta su culminación. 
            Sin que por falta de cláusula expresa alguna, deje de surtir sus efectos, ni se alegue falta de personería en los 
            apoderados. Más Poder, para recabar Estado de Cuentas, firmar el formulario de Solicitud, solicitar y firmar el 
            formulario de actualización de datos del Afiliado Titular y Derechohabiente, firmar formulario de declaración de 
            derechohabientes, asistir a reuniones con facultades de decisión, memoriales, cartas notariales, inscripciones, 
            autorizaciones y cualquier otro documento que sea necesario, hasta la culminación del trámite en las oficinas de la 
            Gestora Publica de la Seguridad Social de Largo Plazo. Más Poder.- para solicitar copias legalizadas y simples de 
            cualquier ámbito con relación a dicho trámite y para transferir o sustituir el presente poder, y en caso de ser 
            necesario otorgar poder a favor de terceros u otras personas para la continuación y conclusión del presente trámite, 
            todo con el fin de solicitar <strong>TRAMITE DE PENSION POR MUERTE Y/O TRAMITE DE MASA HEREDITARIA,</strong> del poder conferente, 
            sin que por no estar expresamente consignado en el presente, sea dado por insuficiente, aclarándose que todas las 
            cláusulas de este poder son enunciativas y no limitativas, facultando a los apoderados a otorgar poder en favor de 
            terceros o ser sustituidos total y/o parcialmente del presente mandato, en caso de viaje, enfermedad, ausencia o 
            dejación de cargo.
            </h3>
            <br>
            <h3 style="text-align: center;">{{ $cliente->sucursal }} - {{ $fechaactual }}</h3>
    </main>
</body>
</html>
