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
            font-size: 16px;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: center;
            text-decoration: underline;
        }

        h3 {
            font-weight: 400;
            font-size: 16px;
            font-family: Arial, sans-serif;
            text-align: justify;
            line-height: 1.5; /* Ajusta el interlineado */
        }
        h4 {
            font-size: 16px;
            font-family: Arial, sans-serif;
            text-align: justify;
            line-height: 1.5;
        }

    </style>
</head>
<body>
    <main>
        <h1>INSTRUCTIVA DE PODER</h1>
        <h3><strong>QUE OTORGA:</strong> El Sr/a. {{$clienteauditoria->nombrecompleto}} con C.I. {{$clienteauditoria->ci}} {{$clienteauditoria->ciexp}}, 
            {{$clienteauditoria->estadocivil}}, mayor de edad, Ocupación {{$clienteauditoria->ocupacion}}, con domicilio en {{$clienteauditoria->domicilio}} - {{$clienteauditoria->ciudadresidencia}}
             y hábil por derecho. <br>
            <strong>A FAVOR DE:</strong>Al Sr. FABRICIO ORLANDO PRADO PARRADO con C.I. 5505371, mayor de edad con domicilio en la ciudad de Cochabamba y hábil por derecho, 
            y/o al Sra. DENISSE MAUREN LOPEZ FLORES Con C.I. No. 5211568, mayor de edad, con domicilio en la ciudad de Cochabamba y hábil por derecho; 
            @foreach($personal as $persona)
            y/o al Sr/a. {{$persona->razonsocial}} con C.I. {{$persona->ci}} {{$persona->ciexp}}, mayor de edad, con domicilio en la ciudad de {{$persona->sucursal}} y hábil por derecho
            @if (!$loop->last)
                    ;
                @else
                    .
                @endif
            @endforeach<br>
            <strong>OBJETO:</strong>Para que en nombre y representación de su persona acciones y derechos, uno Indistintamente de 
            otro, con facultades de Apersonamiento de manera individual y/o colectiva, ante las oficinas de la Gestora Pública de 
            la Seguridad Social de Largo Plazo para Inicio, Continuidad, Seguimiento y Conclusión de 
            <strong>TRAMITE DE PENSIÓN POR INVALIDEZ,</strong> Solicitar y Firmar el formulario de actualización de datos, 
            Iniciar Tramite de Pensión por Invalidez firmando la solicitud correspondiente, solicitar estado de ahorro 
            previsional, Solicitar Revisar y Conciliar el estado de ahorro previsional, Notificarse con requerimientos del 
            Tribunal Medico Calificador TMC, desistir de la solicitud antes de la emisión del dictamen correspondiente, recibir 
            notificaciones del dictamen de invalidez, solicitar revisión del dictamen de invalidez, recibir la notificación de 
            la declaración de pensión o la notificación del rechazo según corresponda, solicitar la recalificación del dictamen 
            de invalidez, presentar una segunda solicitud, en suma solicitar, recibir, entregar y firmar todo tipo de documentos 
            inherentes al trámite de pensión por invalidez hasta su culminación, Solicitud de copias legalizadas y simples de 
            cualquier ámbito con relación a dicho trámite, sin que por falta de cláusula expresa alguna, deje de surtir sus 
            efectos, ni se alegue falta de personería en los apoderados. Más Poder, para recabar el estado de Cuentas, firmar 
            el formulario de Solicitud de pensión por invalidez, formulario de historia ocupacional, firmar formulario de 
            declaración de derechohabientes, firmar formulario de actualización de datos y estado, asistir a reuniones con 
            facultades de decisión, memoriales, cartas notariales, inscripciones, autorizaciones y cualquier otro documento que 
            sea necesario, hasta la culminación del trámite de pensión por invalidez en Gestora Pública de la Seguridad Social 
            de Largo Plazo. Más Poder para sustituir total o individual el presente poder, y en caso de ser necesario otorgar 
            poder a favor de terceros u otras personas para la continuación y conclusión del presente trámite, todo con el fin 
            de solicitar la pensión por invalidez del poder conferente, sin que por no estar expresamente consignado en el 
            presente, sea dado por insuficiente, aclarándose que todas las facultades de este poder son enunciativas y no 
            limitativas, facultando a los apoderados a otorgar poder en favor de terceros o ser sustituidos total y/o 
            parcialmente del presente mandato, en caso de viaje, enfermedad, ausencia o dejación de cargo.
            </h3>
    </main>
</body>
</html>
