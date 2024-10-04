<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Checklist del Cliente</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        h2 {
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Para la tabla de Evaluaciones Médicas Técnicas (50% del ancho) */
        .table-container-left {
            width: 50%;
            margin-bottom: 15px;
            float: left;
            text-align: left;
        }

        /* Título centrado dentro del 50% izquierdo de la página */
        .titulo-evaluaciones {
            display: flex;
            justify-content: center; /* Centra el contenido horizontalmente */
            align-items: center;     /* Alinea el contenido verticalmente */
            width: 50%;              /* Aseguramos que el bloque sea solo del 50% */
            margin-left: 0;           /* Evitamos que el título se desplace innecesariamente */
            margin-bottom: 10px;
            text-align: center;
        }

        .table-container {
            width: 100%;
            margin-bottom: 15px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }

        /* Ajuste específico para las celdas de letras (E, I, S, R) */
        th.letras,
        td.letras {
            width: 15px; /* Espacio justo para la letra */
            padding: 4px;
        }

        /* Celdas para marcar debajo */
        td.marcado {
            height: 12px;
            padding: 7px;
        }

        /* Limpiar el float después de la tabla de Evaluaciones Médicas Técnicas */
        .clearfix {
            clear: both;
        }
    </style>
</head>

<body>

    <!-- Evaluaciones Médicas Técnicas (ocupa solo el 50% de la página) -->
    {{-- <div class="titulo-evaluaciones">
        <h2>{{ $tituloEvaluaciones }}</h2>
    </div>
    <div class="table-container-left">
        <table>
            <thead>
                <tr>
                    @foreach ($estudiosFijos as $estudio)
                        <th colspan="2">{{ $estudio }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($estudiosFijos as $estudio)
                        <th class="letras">E</th>
                        <th class="letras">I</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach ($estudiosFijos as $estudio)
                        <td class="marcado"></td> <!-- Columna E -->
                        <td class="marcado"></td> <!-- Columna I -->
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div> --}}

    @if ($evaluacionesAsociados->isNotEmpty())
        <div class="titulo-evaluaciones">
            <h2>{{ $tituloEvaluaciones }}</h2>
        </div>
        @foreach ($evaluacionesAsociados as $evaluacionesFila)
            <div class="table-container-left">
                <table>
                    <thead>
                        <tr>
                            @foreach ($evaluacionesFila as $evaluacion)
                                <th colspan="2">{{ $evaluacion }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($evaluacionesFila as $evaluacion)
                                <th class="letras">E</th>
                                <th class="letras">I</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach ($evaluacionesFila as $evaluacion)
                                <td class="marcado"></td> <!-- Columna S -->
                                <td class="marcado"></td> <!-- Columna I -->
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
    
    <div class="clearfix"></div> <!-- Limpiar el float -->

    <!-- Solicitud de Interconsultas (Especialidades) -->
    @if ($especialidadesAsociadas->isNotEmpty())
        <h2>{{ $tituloEspecialidades }}</h2>
        @foreach ($especialidadesAsociadas as $especialidadesFila)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            @foreach ($especialidadesFila as $especialidad)
                                <th colspan="2">{{ $especialidad }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($especialidadesFila as $especialidad)
                                <th class="letras">S</th>
                                <th class="letras">I</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach ($especialidadesFila as $especialidad)
                                <td class="marcado"></td> <!-- Columna S -->
                                <td class="marcado"></td> <!-- Columna I -->
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

    <!-- Solicitud de Estudios Complementarios (Estudios) -->
    @if ($estudiosAsociados->isNotEmpty())
        <h2>{{ $tituloComplementarios }}</h2>
        @foreach ($estudiosAsociados as $estudiosFila)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            @foreach ($estudiosFila as $estudio)
                                <th colspan="2">{{ $estudio }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($estudiosFila as $estudio)
                                <th class="letras">S</th>
                                <th class="letras">R</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach ($estudiosFila as $estudio)
                                <td class="marcado"></td> <!-- Columna S -->
                                <td class="marcado"></td> <!-- Columna R -->
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

</body>

</html>