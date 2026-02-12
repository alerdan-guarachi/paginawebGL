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
            <strong>OBJETO:</strong> Para que en representación de su persona acciones y derechos, uno Indistintamente de otro, con facultades de apersonamiento 
            de manera individual y/o colectiva ante oficinas de la <strong>GESTORA PÚBLICA DE LA SEGURIDAD SOCIAL DE LARGO PLAZO,</strong>
            para Inicio, Continuidad, Seguimiento y Conclusión del
            <strong>TRAMITE DE MASA HEREDITARIA
                @if($generofallecido == 'MASCULINO')
                    DEL ASEGURADO FALLECIDO
                @elseif($generofallecido == 'FEMENINO')
                    DE LA ASEGURADA FALLECIDA
                @else
                    DE LA PERSONA FALLECIDA
                @endif
                {!! $nombrefallecido ?? '<span class="textoedita">Nombre Fallecido</span>' !!} 
                CON C.I. N° {!! $cifallecido ?? '<span class="textoedita">CI Fallecido</span>' !!},
            </strong>en el marco de la Ley 065, Solicitar Entregar documentos de acreditación de los herederos para su revisión, Solicitar el Estado de Ahorro 
            Previsional (EAP), Revisar, verificar, solicitar regularización de aportes (si corresponde) y firmar el certificado de verificación del EAP, 
            así como, el formulario de conformidad de aportes (si corresponde), Iniciar Tramite de Masa Hereditaria firmando la solicitud correspondiente, 
            firmar la liquidación y acta de entrega de Masa Hereditaria (pensiones no cobradas), firmar contrato de entrega de masa hereditaria 
            (Cuenta personal previsional), en suma solicitar, recibir, entregar, y firmar todo tipo de documentos inherentes al trámite de masa hereditaria 
            desde el inicio hasta su culminación . Sin que por falta de cláusula expresa alguna, deje de surtir sus efectos, ni se alegue falta de personería 
            en los apoderados. Más Poder, para recabar Estado de Cuentas, firmar el formulario de Solicitud, firmar formulario de declaración de 
            derechohabientes, asistir a reuniones con facultades de decisión, memoriales, cartas notariales, inscripciones, autorizaciones y cualquier otro 
            documento que sea necesario, hasta la culminación del trámite, Autorización para la firma del formulario de Actualización de datos del Asegurado 
            fallecido, Autorización para la actualización de datos de los herederos (cuando corresponda), Desistir de la solicitud antes del contrato, 
            Facultad para la firma del formulario de Habilitación/Reposición de Pagos Suspendidos y/o Revertidos, así mismo, poder para firmar contratos y 
            actas, recojo de cheques en representación del afiliado titular o el derechohabiente en oficinas de la Gestora Publica de la Seguridad Social de 
            Largo Plazo. Más Poder.- para transferir o sustituir el presente poder, y en caso de ser necesario otorgar poder a favor de terceros u otras 
            personas para la continuación y conclusión del presente trámite, todo con el fin de solicitar <strong>TRAMITE DE MASA HEREDITARIA,</strong> 
            del poder conferente, sin que por no estar expresamente consignado en el presente, sea dado por insuficiente, aclarándose que todas las cláusulas 
            de este poder son enunciativas y no limitativas, facultando a los apoderados a otorgar poder en favor de terceros o ser sustituidos total y/o 
            parcialmente del presente mandato, en caso de viaje, enfermedad, ausencia o dejación de cargo.
            </h3>
            <br>
            <h3 style="text-align: center;">{{ $cliente->sucursal }} - {{ $fechaactual }}</h3>
    </main>
</body>
</html>
