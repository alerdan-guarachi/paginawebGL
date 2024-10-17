<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: 8.5in 11in;
            margin: 0;
        }
        body {
            margin: 1cm 3cm 1cm 3cm;
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
        .tipo4 {
            font-size: 14px;
            margin-top: 15px;
            margin-bottom: 15px;
            font-family: Arial, sans-serif;
            text-align: justify;
            margin-left: 40px;
        }
        .tipo5 {
            font-size: 15px;
            font-weight: 1200;
            margin-top: 60px;
            margin-bottom: 15px;
            font-family: Arial, sans-serif;
            text-align: center;
            line-height: 0;
        }
        .tipo6 {
            font-size: 14px;
            margin-top: 15px;
            margin-bottom: 15px;
            font-family: Arial, sans-serif;
            text-align: justify;
        }
        .tipo8 { 
            font-size: 14px;
            margin-top: 5px; /* Reducido para subir el rectángulo */
            margin-bottom: 15px;
            font-family: Arial, sans-serif;
            text-align: justify;
        }
        .container {
            text-align: right; /* Alinear todo a la derecha */
            margin: 0; /* Sin margen alrededor del contenedor */
        }
        .rectangle {
            width: 170px; /* Ancho del rectángulo */
            height: 80px; /* Alto del rectángulo */
            border: 1px solid black; /* Bordes del rectángulo */
            display: inline-block; /* Mantener el tamaño del contenedor */
            margin-top: -100px; /* Ajustar para que esté más arriba */
        }
        .text9 {
            margin-top: -30px; /* Espacio entre el rectángulo y el texto */
            font-size: 9px;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            line-height: 0;
            margin-right: 22px;
        }
    </style>
</head>
<body>
    <main>
        <div class="tipo5">CONSENTIMIENTO INFORMADO PARA LA REALIZACIÓN DE EVALUACIONES</div>
        <div class="tipo5" style="margin-top: 20px;">Y ESTUDIOS MÉDICOS ADICIONALES</div>
        <div class="tipo6">
            El presente documento tiene como finalidad garantizar que el paciente este de acuerdo en someterse a las evaluaciones 
            y estudios necesarios que sean requeridos por los Especialistas tras una evaluación inicial. El paciente comprende 
            que la precisión del diagnóstico depende de la realización oportuna de dichos estudios y que los informes médicos 
            tienen una vigencia de 3 meses desde el inicio de la primera evaluación médica. Pasado este tiempo, los estudios 
            vencidos deberán repetirse para mantener la validez de la historia clínica.
        </div>
        <div class="tipo6">
            Declaración del Paciente:
        </div>
        <div class="tipo6">
            Yo,  {{$nombres}} declaro que he sido informado/a sobre la importancia de 
            cumplir con todas las Evaluaciones y Estudios Médicos requeridos por los Especialistas a los que sea derivado, con el 
            fin de agilizar mi proceso diagnóstico y evitar incidencias en la obtención de un diagnóstico definitivo.
        </div>
        <div class="tipo6">
            Entiendo que:
        </div>
        <div class="tipo4">
            1.	Después de la primera evaluación, estoy de acuerdo en realizar todas las Evaluaciones y Estudios necesarios para 
            garantizar la precisión de mi diagnóstico.
        </div>
        <div class="tipo4">
            2.	Los informes y resultados de las evaluaciones médicas y estudios complementarios tienen una validez de 3 meses 
            a partir de la fecha de la primera evaluación. Si alguno de estos informes vence sin haber completado el proceso, 
            estaré dispuesto/a de repetir aquellos estudios cuya vigencia haya caducado.
        </div>
        <div class="tipo4">
            3.	La no realización de estudios dentro del plazo indicado puede afectar la validez de mi Historia Clínica, 
            y entiendo que es mi responsabilidad completar estos estudios en tiempo y forma.
        </div>
        <div class="tipo6">
            Consentimiento para la Realización de Evaluaciones y Estudios:
        </div>
        <div class="tipo6">
            Confirmo que he comprendido la necesidad de cumplir con todas las Evaluaciones y Estudios adicionales que puedan 
            ser requeridos y acepto realizar los estudios en los plazos indicados. Asimismo, comprendo que los informes médicos 
            caducan a los 3 meses y que, si esto ocurre, deberé someterme nuevamente a las pruebas correspondientes para 
            asegurar la validez de mi Historia Clínica.
        </div>
        <div class="tipo6">
            He tenido la oportunidad de hacer preguntas y todas mis dudas han sido aclaradas.
        </div>
        <div class="tipo6">
            Firmo este documento en pleno uso de mis facultades y consciente de su contenido.
        </div><br>
        <div class="tipo8">
            Firma del Paciente: 
        </div>
        <div class="tipo8">
            C.I: {{$ci}}
        </div>
        <div class="tipo8">
            Fecha:
        </div>
        <div class="container">
            <div class="rectangle"></div>
            <div class="text9">
                <h2>PULGAR DERECHO</h2>
            </div>
        </div>
    </main>
</body>
</html>
