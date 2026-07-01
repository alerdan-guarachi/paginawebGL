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
        <h3><strong>QUE OTORGA:</strong> El Sr/a. {{$cliente->nombrecompleto}} con C.I. {{$cliente->ci}}{{$cliente->ciexp}}, 
            {{ $estadoCivil }}, mayor de edad, Ocupación {{$cliente->ocupacion}}, con domicilio en {{$cliente->domicilio}} - {{$cliente->ciudadresidencia}}
             y hábil por derecho. <br>
            <strong>A FAVOR DE:</strong> Al Sr. FABRICIO ORLANDO PRADO PARRADO con C.I. 5505371, mayor de edad con domicilio en la ciudad de SANTA CRUZ y hábil por derecho, 
            y/o al Sra. DENISSE MAUREN LOPEZ FLORES Con C.I. No. 5211568, mayor de edad, con domicilio en la ciudad de SANTA CRUZ y hábil por derecho; 
            @if ($sucursal === 'COCHABAMBA')
            y/o al Sr. EUDAL AGUIRRE RODRIGUEZ Con C.I. No. 10360406, mayor de edad, con domicilio en la ciudad de SANTA CRUZ y hábil por derecho; 
            @endif
            @foreach($personal as $persona)
                y/o al Sr/a. {{ $persona->razonsocial }} con C.I. {{ $persona->ci }}{{ $persona->ciexp }}, mayor de edad, con domicilio en la ciudad de {{ $persona->ciudad }} y hábil por derecho{{ !$loop->last ? ';' : '.' }}
            @endforeach
            <br>
            <strong>OBJETO:</strong> Para que en representación de su persona acciones y derechos, uno indistintamente de otro, con facultades de 
            Apersonamiento, ante las oficinas de la GESTORA PÚBLICA DE LA SEGURIDAD SOCIAL DE LARGO PLAZO para Inicio, Continuidad, Seguimiento y 
            Conclusión de <strong>TRÁMITE DE PENSIÓN POR INVALIDEZ,</strong> Solicitar, Suscribir , Firmar el formulario de actualización de datos, 
            Iniciar Tramite de Pensión por Invalidez firmando la solicitud correspondiente, Solicitar el Estado de Ahorro Previsional, Revisar, Verificar, 
            Solicitar regularización de Aportes si en caso corresponde y Firmar el Certificado de Verificación del Estado de Ahorro Previsional, así como, 
            el Formulario de conformidad de aportes si corresponde, Notificarse con requerimientos del Tribunal Medico Calificador TMC, desistir de la 
            solicitud antes de la emisión del Dictamen correspondiente, recibir notificaciones del Dictamen de Invalidez, Solicitar Revisión del Dictamen 
            de Invalidez, recibir la notificación de la Declaración de Pensión o la notificación del rechazo según corresponda, Solicitar la Recalificación 
            del Dictamen, presentar una segunda solicitud, en suma solicitar, recibir, entregar y firmar todo tipo de documentos inherentes al Trámite de 
            Pensión por Invalidez hasta su culminación, Solicitud de copias legalizadas y simples de cualquier ámbito con relación a dicho trámite, sin que 
            por falta de cláusula expresa alguna, deje de surtir sus efectos, ni se alegue falta de personería en los apoderados. Más Poder, para recabar el 
            estado de Cuentas, firmar el formulario de Solicitud de pensión por invalidez, formulario de historia ocupacional, firmar formulario de 
            declaración de derechohabientes, firmar formulario de actualización de datos y estado, asistir a reuniones con facultades de decisión, memoriales, 
            cartas notariales, inscripciones, autorizaciones y cualquier otro documento que sea necesario, hasta la culminación del Trámite de Pensión por 
            Invalidez en Gestora Pública de la Seguridad Social de Largo Plazo. Asimismo, y en caso de corresponder conforme al porcentaje de Invalidez 
            determinado solicitar, consultar, verificar y gestionar la habilitación de pago de la Pensión por Invalidez. Más Poder para apersonamiento ante 
            las oficinas de la Autoridad de Fiscalización y Control de Pensiones y Seguros APS para realizar toda clase de gestiones relacionadas con el 
            Trámite de Pensión por Invalidez, pudiendo solicitar información verbal y escrita, Facultad para llenar, suscribir y presentar formularios 
            físicos o digitales, incluidos aquellos correspondientes al Buzón de Reclamos y Sugerencias, presentar cartas de reclamo, memoriales, solicitudes, 
            peticiones, reclamos administrativos y cualquier otra actuación necesaria, realizar seguimiento a reclamos y solicitudes, notificarse y recoger 
            respuestas, informes, resoluciones administrativas, notas y cualquier otra documentación relacionada con el Trámite. Mas Poder para sustituir 
            total o individual el presente poder, y en caso de ser necesario otorgar poder a favor de terceros u otras personas para la continuación y 
            conclusión del presente trámite, todo con el fin de solicitar la pensión por invalidez, del poder conferente, sin que por no estar expresamente 
            consignado en el presente, sea dado por insuficiente, aclarándose que todas las facultades de este poder son enunciativas y no limitativas, 
            facultando a los apoderados a otorgar poder en favor de terceros o ser sustituidos total y/o parcialmente del presente mandato, en caso de viaje, 
            enfermedad, ausencia o dejación de cargo.
            </h3>
            <br>
            <h3 style="text-align: center;">{{ $cliente->sucursal }} - {{ $fechaactual }}</h3>
    </main>
</body>
</html>
