<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DECLARACIONES HECHAS AL MEDICO EXAMINADOR</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }

        .ancho {
            width: 12.5%;
        }

        .firma-izquierda,
        .firma-derecha {
            width: 50%;
            text-align: center;
            padding-top: 50px;
        }

        .firma-derecha {
            border-left: 1px solid black;
        }

        .narrow {
            width: 50px;
        }

        /* Para centrar el contenido en la sección de firma */
        .firma-detalles th,
        .firma-detalles td {
            text-align: center;
        }

        .detalles-firma-table {
            width: 100%;
        }

        .detalles-firma-table td {
            width: 16.6%;
            /* Distribuir los campos uniformemente */
        }
    </style>
</head>

<body>
    <table>
        <!-- Encabezado centrado -->
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">DECLARACIONES HECHAS AL MEDICO EXAMINADOR</th>
        </tr>
        <!-- Regional - Ahora con colspan para que ocupe más espacio -->
        <tr>
            <th class="ancho">Regional</th>
            <td class="ancho" colspan="7">{{ $clientebanco->ciudad }}</td>
        </tr>
        <!-- Nombres y Código -->
        <tr>
            <th class="ancho" colspan="2">Nombres y Apellidos</th>
            <td class="ancho" colspan="4">{{ $clientebanco->nombrecompleto }}</td>
            <th class="ancho">Código</th>
            <td class="ancho">{{ $clientebanco->id }}</td>
        </tr>
        <!-- Género, Fecha de nacimiento, Edad, Lugar de nacimiento -->
        <tr>
            <th class="ancho">Género</th>
            <td class="ancho">{{ $clientebanco->genero }}</td>
            <th class="ancho">Fecha de nac.</th>
            <td class="ancho">{{ $clientebanco->fechanacimiento }}</td>
            <th class="ancho">Edad</th>
            <td class="ancho">{{ $clientebanco->edad }}</td>
            <th class="ancho">Lugar de nac.</th>
            <td class="ancho">{{ $clientebanco->ciudad }}</td>
        </tr>
        <!-- Residencia y Grado de instrucción -->
        <tr>
            <th class="ancho" colspan="2">Residencia</th>
            <td class="ancho" colspan="2">{{ $clientebanco->ciudad }}</td>
            <th class="ancho" colspan="2">Grado de instrucción</th>
            <td class="ancho" colspan="2">{{ $clientebanco->ocupacionprofesion }}</td>
        </tr>
        <!-- Estado civil y Teléfono -->
        <tr>
            <th class="ancho" colspan="2">Estado civil</th>
            <td class="ancho" colspan="2">{{ $clientebanco->estadocivil }}</td>
            <th class="ancho" colspan="2">Teléfono del paciente</th>
            <td class="ancho" colspan="2">{{ $clientebanco->celular }}</td>
        </tr>
        <!-- Cédula de identidad -->
        <tr>
            <th class="ancho" colspan="2">Cédula de identidad</th>
            <td class="ancho" colspan="6">{{ $clientebanco->ci }}</td>
        </tr>

        <!-- Sección modificada para que siga el estilo general de la tabla -->
        <tr>
            <th colspan="8" style="text-align: center; background: yellow; font-weight: bold;">POR FAVOR CONTESTE A
                SU MEJOR SABER Y ENTENDER</th>
        </tr>
        <tr>
            <th colspan="4">Nombre y dirección de su médico particular:</th>
            <td colspan="4">{{ $nombre_medico ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th colspan="4">Fecha y motivo de la consulta reciente:</th>
            <td colspan="4">{{ $fecha_consulta ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th colspan="4">Qué tratamiento o medicación se transcribió:</th>
            <td colspan="4">{{ $tratamiento_medico ?? 'N/A' }}</td>
        </tr>

        <!-- Mostrar todas las preguntas con respuestas -->
        @foreach ($preguntas as $pregunta)
            <tr>
                <th colspan="8">{{ $pregunta['pregunta_nombre'] }}</th>
            </tr>
            <tr>
                <td colspan="2">Respuesta:</td>
                <td colspan="6">{{ $pregunta['respuesta'] ?? 'N/A' }}</td>
            </tr>

            <!-- Si la respuesta es 'si' para la pregunta específica -->
            @if (isset($pregunta['respuesta']) && $pregunta['respuesta'] == 'si')
                <!-- Verificar si es la pregunta específica (ID 29) -->
                @if ($pregunta['pregunta_id'] == 29)
                    <tr>
                        <td colspan="2">Detalles completos:</td>
                        <td colspan="6">{{ $pregunta['detallescompletos'] ?? 'N/A' }}</td>
                    </tr>
                @else
                    <!-- Mostrar los datos adicionales de otras preguntas -->
                    <tr>
                        <td colspan="2">Diagnóstico:</td>
                        <td colspan="6">{{ $pregunta['diagnostico'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Fecha:</td>
                        <td colspan="6">{{ $pregunta['fecha'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Tiempo:</td>
                        <td colspan="6">{{ $pregunta['tiempo'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Grado de recuperación:</td>
                        <td colspan="6">{{ $pregunta['gradorecuperacion'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Médico:</td>
                        <td colspan="6">{{ $pregunta['medico'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Dirección del médico:</td>
                        <td colspan="6">{{ $pregunta['direccionmedico'] ?? 'N/A' }}</td>
                    </tr>

                    <!-- Verificar si hay datos adicionales (segundo formulario) -->
                    @if (isset($pregunta['diagnostico2']) ||
                            isset($pregunta['fecha2']) ||
                            isset($pregunta['tiempo2']) ||
                            isset($pregunta['gradorecuperacion2']))
                        <tr>
                            <td colspan="2">Diagnóstico 2:</td>
                            <td colspan="6">{{ $pregunta['diagnostico2'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Fecha 2:</td>
                            <td colspan="6">{{ $pregunta['fecha2'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Tiempo 2:</td>
                            <td colspan="6">{{ $pregunta['tiempo2'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Grado de recuperación 2:</td>
                            <td colspan="6">{{ $pregunta['gradorecuperacion2'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Médico 2:</td>
                            <td colspan="6">{{ $pregunta['medico2'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Dirección del médico 2:</td>
                            <td colspan="6">{{ $pregunta['direccionmedico2'] ?? 'N/A' }}</td>
                        </tr>
                    @endif
                @endif
            @endif
        @endforeach

        <!-- Estado de Salud de Familiares -->
        <tr>
            <th colspan="8" style="text-align: center; background: yellow; font-weight: bold;">ESTADO DE SALUD DE
                FAMILIARES</th>
        </tr>

        <!-- Información del Padre -->
        @if (isset($familiares['padre']))
            <tr>
                <th colspan="2">Padre - Estado de salud:</th>
                <td colspan="6">{{ $familiares['padre']['estado'] ?? 'N/A' }}</td>
            </tr>
            @if ($familiares['padre']['estado'] == 'vivo')
                <tr>
                    <th colspan="2">Edad vivo:</th>
                    <td colspan="6">{{ $familiares['padre']['edad_vivo'] ?? 'N/A' }}</td>
                </tr>
            @elseif ($familiares['padre']['estado'] == 'fallecido')
                <tr>
                    <th colspan="2">Edad al fallecer:</th>
                    <td colspan="6">{{ $familiares['padre']['edad_fallecer'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th colspan="2">Causa de fallecimiento:</th>
                    <td colspan="6">{{ $familiares['padre']['causa_fallecimiento'] ?? 'N/A' }}</td>
                </tr>
            @endif
            <tr>
                <th colspan="2">Observaciones / Enfermedades:</th>
                <td colspan="6">{{ $familiares['padre']['observaciones'] ?? 'N/A' }}</td>
            </tr>
        @endif

        <!-- Información de la Madre -->
        @if (isset($familiares['madre']))
            <tr>
                <th colspan="2">Madre - Estado de salud:</th>
                <td colspan="6">{{ $familiares['madre']['estado'] ?? 'N/A' }}</td>
            </tr>
            @if ($familiares['madre']['estado'] == 'vivo')
                <tr>
                    <th colspan="2">Edad vivo:</th>
                    <td colspan="6">{{ $familiares['madre']['edad_vivo'] ?? 'N/A' }}</td>
                </tr>
            @elseif ($familiares['madre']['estado'] == 'fallecido')
                <tr>
                    <th colspan="2">Edad al fallecer:</th>
                    <td colspan="6">{{ $familiares['madre']['edad_fallecer'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th colspan="2">Causa de fallecimiento:</th>
                    <td colspan="6">{{ $familiares['madre']['causa_fallecimiento'] ?? 'N/A' }}</td>
                </tr>
            @endif
            <tr>
                <th colspan="2">Observaciones / Enfermedades:</th>
                <td colspan="6">{{ $familiares['madre']['observaciones'] ?? 'N/A' }}</td>
            </tr>
        @endif

        <!-- Información de Hermanos -->
        @if (isset($familiares['hermanos']))
            <tr>
                <th colspan="2">Hermanos - Estado de salud:</th>
                <td colspan="6">{{ $familiares['hermanos']['estado'] ?? 'N/A' }}</td>
            </tr>
            @if ($familiares['hermanos']['estado'] == 'vivo')
                <tr>
                    <th colspan="2">Edad vivo:</th>
                    <td colspan="6">{{ $familiares['hermanos']['edad_vivo'] ?? 'N/A' }}</td>
                </tr>
            @elseif ($familiares['hermanos']['estado'] == 'fallecido')
                <tr>
                    <th colspan="2">Edad al fallecer:</th>
                    <td colspan="6">{{ $familiares['hermanos']['edad_fallecer'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th colspan="2">Causa de fallecimiento:</th>
                    <td colspan="6">{{ $familiares['hermanos']['causa_fallecimiento'] ?? 'N/A' }}</td>
                </tr>
            @endif
            <tr>
                <th colspan="2">Observaciones / Enfermedades:</th>
                <td colspan="6">{{ $familiares['hermanos']['observaciones'] ?? 'N/A' }}</td>
            </tr>
        @endif

        <!-- Información de N. Vivo -->
        @if (isset($familiares['n_vivo']))
            <tr>
                <th colspan="2">N. Vivo - Estado de salud:</th>
                <td colspan="6">{{ $familiares['n_vivo']['estado'] ?? 'N/A' }}</td>
            </tr>
            @if ($familiares['n_vivo']['estado'] == 'vivo')
                <tr>
                    <th colspan="2">Edad vivo:</th>
                    <td colspan="6">{{ $familiares['n_vivo']['edad_vivo'] ?? 'N/A' }}</td>
                </tr>
            @elseif ($familiares['n_vivo']['estado'] == 'fallecido')
                <tr>
                    <th colspan="2">Edad al fallecer:</th>
                    <td colspan="6">{{ $familiares['n_vivo']['edad_fallecer'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th colspan="2">Causa de fallecimiento:</th>
                    <td colspan="6">{{ $familiares['n_vivo']['causa_fallecimiento'] ?? 'N/A' }}</td>
                </tr>
            @endif
            <tr>
                <th colspan="2">Observaciones / Enfermedades:</th>
                <td colspan="6">{{ $familiares['n_vivo']['observaciones'] ?? 'N/A' }}</td>
            </tr>
        @endif

        <!-- Información de N. Muerto -->
        @if (isset($familiares['n_muerto']))
            <tr>
                <th colspan="2">N. Muerto - Estado de salud:</th>
                <td colspan="6">{{ $familiares['n_muerto']['estado'] ?? 'N/A' }}</td>
            </tr>
            @if ($familiares['n_muerto']['estado'] == 'vivo')
                <tr>
                    <th colspan="2">Edad vivo:</th>
                    <td colspan="6">{{ $familiares['n_muerto']['edad_vivo'] ?? 'N/A' }}</td>
                </tr>
            @elseif ($familiares['n_muerto']['estado'] == 'fallecido')
                <tr>
                    <th colspan="2">Edad al fallecer:</th>
                    <td colspan="6">{{ $familiares['n_muerto']['edad_fallecer'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th colspan="2">Causa de fallecimiento:</th>
                    <td colspan="6">{{ $familiares['n_muerto']['causa_fallecimiento'] ?? 'N/A' }}</td>
                </tr>
            @endif
            <tr>
                <th colspan="2">Observaciones / Enfermedades:</th>
                <td colspan="6">{{ $familiares['n_muerto']['observaciones'] ?? 'N/A' }}</td>
            </tr>
        @endif

        <!-- Sección de Estatura y Peso -->
        <tr>
            <th colspan="8" style="text-align: center; background: yellow; font-weight: bold;">ESTATURA Y PESO</th>
        </tr>
        <tr>
            <th colspan="4">Estatura:</th>
            <td colspan="4">{{ $estatura ?? 'N/A' }} m.</td>
        </tr>
        <tr>
            <th colspan="4">Peso:</th>
            <td colspan="4">{{ $peso ?? 'N/A' }} kg.</td>
        </tr>

        <!-- Sección de Firma -->
        <tr>
            <td colspan="8" style="text-align: center;">
                <p><strong>Confirmo que soy la persona arriba mencionada como Propuesto Asegurado y que las
                        declaraciones y respuestas precedentes, una vez debidamente comprobadas, son completas,
                        auténticas, correctamente transcritas y forman parte de la solicitud de Seguro sobre mi vida
                        hecha a Nacional Vida Seguros de Personas S.A.</strong></p>
                <p>Por la presente autorizo a todo médico, hospital, compañía de seguros u otra institución o persona
                    cualesquiera a que, dentro de los límites legales, faciliten a Nacional Vida Seguros de Personas
                    S.A. o su representante, información sobre mi estado de salud, historial médico y cualquier
                    hospitalización, recomendación, diagnóstico, tratamiento, enfermedad o dolencia. Una fotocopia de
                    esta autorización será asimismo válida.</p>
            </td>
        </tr>

        <!-- Sección de Detalles de Firma centrada y ajustada -->
        <tr class="firma-detalles">
            <th colspan="2">Firmada en:</th>
            <td colspan="6">{{ $lugar ?? '' }}</td>
        </tr>
        <tr class="firma-detalles">
            <td colspan="8">
                <table class="detalles-firma-table">
                    <tr>
                        <th>El:</th>
                        <td>{{ $dia ?? '' }}</td>
                        <th>De:</th>
                        <td>{{ $mes ?? '' }}</td>
                        <th>De 20:</th>
                        <td>{{ $anio ?? '' }}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Línea de firma izquierda y derecha con bordes asegurados -->
        <tr>
            <td class="firma-izquierda" colspan="4">
                @if(file_exists($medicoSignaturePath))
                    <img src="{{ $medicoSignaturePath }}" alt="Firma del Médico Examinador" style="max-width: 250px; max-height: 100px;">
                @else
                    <p></p>
                @endif
                <p>Firma del Médico Examinador</p>
            </td>
            <td class="firma-derecha" colspan="4">
                @if(file_exists($propuestoSignaturePath))
                    <img src="{{ $propuestoSignaturePath }}" alt="Firma del Propuesto Asegurado" style="max-width: 250px; max-height: 100px;">
                @else
                    <p></p>
                @endif
                <p>Firma del Propuesto Asegurado</p>
            </td>
        </tr>
    </table>
</body>

</html>