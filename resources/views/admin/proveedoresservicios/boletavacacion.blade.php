<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Vacaciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        td, th {
            padding: 6px 8px;
            border: 1px solid #ccc;
        }

        .encabezado {
            border: none;
        }

        .logo {
            width: 180px;
        }

        .titulo-principal {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            line-height: 1.4;
        }

        .nroboleta {
            text-align: right;
            font-weight: bold;
            font-size: 18px;
            color: red;
        }

        .subtitulo {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .firma-box {
            height: 100px;
            vertical-align: bottom;
        }

        .firma-line {
            border-top: 2px solid #000;
            width: 90%;
            margin: 0 auto;
            margin-top: 90px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    @php
        use Carbon\Carbon;
        $fechaFinal = Carbon::parse($vacacion->fechafinal);
        $diaSemana = $fechaFinal->copy()->addDay()->dayOfWeek;
        $periodoInicio = Carbon::parse($fechaingreso)->year;
        $periodoFin = now()->year;
    @endphp

    <!-- Encabezado con logo, título y N° de Boleta -->
    <table class="encabezado">
        <tr>
            <td style="border: none; width: 30%;">
                <img src="{{ public_path('img/logo.png') }}" class="logo" alt="Logo">
            </td>
            <td style="border: none; width: 45%;">
                <div class="titulo-principal">
                    EMPRESA GOOD LIFE S.R.L.<br>
                    SOLICITUD Y AUTORIZACIÓN DE VACACIONES
                </div>
            </td>
            <td style="border: none; width: 25%;" class="nroboleta">
                Nro. {{ $vacacion->nroboleta }}
            </td>
        </tr>
    </table>

    <!-- Datos del empleado -->
    <table>
        <tr class="subtitulo">
            <td colspan="4">DATOS DEL EMPLEADO</td>
        </tr>
        <tr>
            <td><strong>Nombre del Empleado:</strong></td>
            <td>{{ $vacacion->proveedornombre }}</td>
            <td><strong>Cargo:</strong></td>
            <td>{{ $cargo }}</td>
        </tr>
        <tr>
            <td><strong>N° de Empleado:</strong></td>
            <td>{{ $vacacion->proveedorid }}</td>
            <td><strong>Fecha de Solicitud:</strong></td>
            <td>{{ now()->format('d/m/Y') }}</td>
        </tr>
    </table>

    <!-- Detalle del descanso -->
    <table>
        <tr class="subtitulo">
            <td colspan="4">DETALLE DE SOLICITUD DE VACACIÓN</td>
        </tr>
        <tr>
            <td><strong>Fecha de Inicio:</strong></td>
            <td>{{ Carbon::parse($vacacion->fechainicial)->format('d/m/Y') }}</td>
            <td><strong>Fecha Final:</strong></td>
            <td>{{ Carbon::parse($vacacion->fechafinal)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Fecha de Ingreso:</strong></td>
            <td>{{ \Carbon\Carbon::parse($fechaingreso)->format('d/m/Y') }}</td>
            <td><strong>Años de Servicio:</strong></td>
            <td>{{ $aniosServicio }} {{ $aniosServicio == 1 ? 'año' : 'años' }}</td>
        </tr>
        <tr>
            <td><strong>Días que Corresponden:</strong></td>
            <td>{{ $diasQueCorresponden }}</td>
            <td><strong>Días Usados:</strong></td>
            <td>{{ $diasUsados }}</td>
        </tr>
        <tr>
            <td><strong>Días Solicitados:</strong></td>
            <td>{{ $vacacion->cantidaddias }}</td>
            <td><strong>Días Pendientes:</strong></td>
            <td>{{ $diasPendientes }}</td>
        </tr>
        <tr>
            <td><strong>Periodo a Disfrutar:</strong></td>
            <td>{{ $periodoInicio }} - {{ $periodoFin }}</td>
            <td><strong>Fecha a Presentarse a Trabajar:</strong></td>
            <td>{{ $fechaRetorno->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Observaciones:</strong></td>
            <td colspan="3">{{ $vacacion->observacion }}</td>
        </tr>
    </table>

    <!-- Declaración -->
    <p style="text-align: justify;">
        POR EL PRESENTE EXPRESO MI CONFORMIDAD DE SOLICITAR Y GOZAR MIS VACACIONES DE ACUERDO A LO QUE ESTABLECE EL ARTÍCULO 44 DE LA LEY GENERAL DEL TRABAJO, CONSIDERANDO LOS SIGUIENTES DATOS:
    </p>

    <!-- Fecha de ciudad -->
    <table>
        <tr>
            <td class="center" style="border: none;">
                Santa Cruz, {{ now()->format('d') }} de {{ now()->translatedFormat('F') }} de {{ now()->format('Y') }}
            </td>
        </tr>
    </table>

    <!-- Firmas -->
    <table>
        <tr class="firma-box">
            <td class="center">
                <div class="firma-line"></div>
                Firma de Conformidad del Empleado
            </td>
            <td class="center">
                <div class="firma-line"></div>
                Firma de Autorización del Gerente<br>
                del Área y/o Director
            </td>
            <td class="center">
                <div class="firma-line"></div>
                Vo. Bo. Recursos Humanos
            </td>
        </tr>
    </table>

</body>
</html>
