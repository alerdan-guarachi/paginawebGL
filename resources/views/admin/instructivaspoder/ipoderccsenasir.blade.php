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
            <strong>A FAVOR DE:</strong>
            @foreach($personal as $persona)
                Al Sr/a. {{ $persona->razonsocial }} con C.I. {{ $persona->ci }} {{ $persona->ciexp }}, mayor de edad, con domicilio en la ciudad de {{ $persona->ciudad }} y hábil por derecho{{ !$loop->last ? ';' : '.' }}
            @endforeach
            <br>
            <strong>OBJETO:</strong> Para que en nombre y representación en su persona, realice el <strong>TRAMITE DE COMPENSACION DE COTIZACIONES, NOTIFICACION, 
            RECEPCION DEL CERTIFICADO DE COMPENSACION DE COTIZACIONES, PLANTEAR RECURSOS Y OTROS TRAMITES ADICIONALES QUE SE PUEDA REALIZAR PARA LA 
            CONCLUSION DEL TRAMITE, por ante las oficinas del SERVICIO NACIONAL DEL SISTEMA DE REPARTO (SENASIR)</strong> y sea a partir de periodo de que 
            correspondan.- Al efecto, sus incidencias y emergencias, le confiere las facultades generales del mandato y las especiales de presentar 
            memoriales y toda clase de documentos; apersonarse y realizar Trámites por ante el Servicio Nacional del Sistema de Reparto (SENASIR), 
            Ministerio de Economía y Finanzas Publicas, Tesoro General de la Nación, Caja Nacional de Salud, Gestora Publica de la Seguridad Social de 
            Largo Plazo, firmar en su representación documentos generados en el SENASIR, plantear quejas, reclamos, solicitar certificados, copias 
            legalizadas, testimonios, duplicados, llenar formularios y demás requisitos que sean necesarios. En suma, realizar cuanta gestión o tramite 
            sea necesario para el mejor cumplimiento del mandato, sin que por falta de clausula expresa alguna sea rechazada. -  Así dijo, lo otorgo y 
            firmo, en presencia de las testigos instrumentales, ciudadano(a)s:
            </h3>
            <br>
            <h3 style="text-align: center;">{{ $cliente->sucursal }} - {{ $fechaactual }}</h3>
    </main>
</body>
</html>
