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
        .tipo4 {
            font-size: 14.8px;
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
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            text-align: center;
            line-height: 0;
        }
        .tipo6 {
            font-size: 14.8px;
            margin-top: 15px;
            margin-bottom: 15px;
            font-family: Arial, sans-serif;
            text-align: justify;
        }
        .tipo8 { 
            font-size: 14.8px;
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
            font-size: 10px;
            margin-bottom: 30px;
            font-family: Arial, sans-serif;
            line-height: 0;
            margin-right: 30px;
        }
    </style>
</head>
<body>
    <main>
        <div class="tipo5">CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS</div>
        <div class="tipo6">
            Este consentimiento tiene como propósito asegurar que el paciente comprenda la importancia de brindar información 
            completa y veraz durante la Evaluación Médica Inicial, cuyo fin sea la correcta derivación a un especialista para 
            obtener un diagnóstico preciso. Esta evaluación no está destinada al tratamiento, sino a la identificación adecuada 
            de la condición del paciente para determinar el tipo de especialista que se requiere.
        </div>
        <div class="tipo6">
            Declaración del Paciente:
        </div>
        <div class="tipo6">
            Yo,  {{$nombres}} declaro que he sido informado/a por el equipo médico sobre la necesidad de proporcionar información exacta, 
            completa y sincera respecto a mi salud, mis antecedentes médicos y cualquier síntoma o condición que presente, 
            con el fin de facilitar una derivación correcta a un Especialista.
        </div>
        <div class="tipo6">
            Me comprometo a:
        </div>
        <div class="tipo4">
            1.	Informar con total sinceridad sobre cualquier síntoma, enfermedad o condición que padezca.
        </div>
        <div class="tipo4">
            2.	No omitir ni distorsionar información que pueda ser relevante para el proceso de evaluación y derivación.
        </div>
        <div class="tipo4">
            3.	Colaborar con el equipo médico en el proceso de evaluación proporcionando cualquier dato que se me solicite, incluso si considero que no es significativo.
        </div>
        <div class="tipo4">
            4.	Consultar cualquier duda sobre mi salud que pueda influir en la derivación.
        </div>
        <div class="tipo6">
            Entiendo que la evaluación a la que me someteré tiene como objetivo obtener un diagnóstico preliminar que permita 
            derivarme al especialista adecuado. También comprendo que la precisión de este diagnóstico depende en gran medida 
            de la información que proporcione durante la evaluación.
        </div>
        <div class="tipo6">
            Consentimiento para la Evaluación:
        </div>
        <div class="tipo6">
            Confirmo que he comprendido la importancia de comunicar toda la información relevante y veraz para una correcta 
            evaluación y derivación a un especialista. Entiendo que este proceso no implica tratamiento médico, sino una 
            evaluación preliminar para definir el especialista que evaluará mi condición en profundidad. 
        </div>
        <div class="tipo6">
            Firmo este documento en pleno uso de mis facultades y consciente de su contenido.
        </div><br><br><br>
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
                <h2>Pulgar Derecho</h2>
            </div>
        </div>
    </main>
</body>
</html>
