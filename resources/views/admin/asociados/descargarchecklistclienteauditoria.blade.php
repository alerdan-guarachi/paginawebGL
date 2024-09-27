<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF</title>
    <style>
        /* Estilos CSS para la orientación horizontal del PDF */
        @page {
            size: 1100pt 612pt; /* Ancho x Alto */
            margin-right: 50px; /* Elimina los márgenes predeterminados */
            margin-left: 50px;
        }

        /* Estilos CSS para el diseño de la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Asegura que todas las columnas tengan el mismo ancho */
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-size: 12px;
            width: 100%; /* Establece el ancho de las celdas */
        }
        caption {
            caption-side: top;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        /* Estilos adicionales */
        .title-row th {
            vertical-align: top; /* Alinear el texto de los títulos arriba */
        }
        .empty-cell {
            height: 30px; /* Espacio para marcar manualmente */
        }
    </style>
</head>
<body>
    <table>
        <caption>DOCUMENTACIÓN A PRESENTAR</caption>
        <tr class="title-row">
            @foreach($nombreDocumentos as $key => $nombre)
                @if(in_array($key, $documentosSeleccionados))
                    <th>{{ $nombre }}</th>
                @endif
            @endforeach
        </tr>
        <tr>
            @foreach($nombreDocumentos as $key => $nombre)
                @if(in_array($key, $documentosSeleccionados))
                    <td class="empty-cell">
                        <!-- Espacio para marcar manualmente -->
                    </td>
                @endif
            @endforeach
        </tr>
    </table>
</body>
</html>
{{-- <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF</title>
    <style>
        /* Estilos CSS para la orientación horizontal del PDF */
        @page {
            size: 1100pt 612pt; /* Ancho x Alto */
            margin-right: 50px; /* Elimina los márgenes predeterminados */
            margin-left: 50px
        }

        /* Estilos CSS para el diseño de la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }
        caption {
            caption-side: top;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        /* Estilos para los checkboxes */
        input[type="checkbox"] {
            transform: scale(1.8); /* Hacer los checkboxes más grandes */
        }
        /* Aumentar el espacio entre los checkboxes y su etiqueta */
        td input[type="checkbox"] {
            margin-right: 5px;
        }
        /* Estilos adicionales */
        .title-row th {
            vertical-align: top; /* Alinear el texto de los títulos arriba */
        }
    </style>
</head>
<body>
    <input type="hidden" name="documentosSeleccionados" id="documentosSeleccionadosInput">
    <table>
        <caption>DOCUMENTACIÓN A PRESENTAR</caption>
        <tr class="title-row">
            <th>PODER</th>
            <th>AVC/CARNET ASEGURADO</th>
            <th>CERTIFICADO NACIMIENTO ASEGURADO</th>
            <th>CARNET IDENTIDAD ASEGURADO</th>
            <th>CERTIFICADO DE MATRIMONIO</th>
            <th>CERTIFICADO NACIMIENTO CONYUGE</th>
            <th>CARNET IDENTIDAD CONYUGE</th>
            <th>CERTIFICADO NACIMIENTO HIJOS &lt; 25</th>
            <th>CARNET IDENTIDAD HIJOS &lt; 25</th>
            <th>DENUNCIA ENFERMEDAD ACCIDENTE</th>
            <th>CROQUIS DE DOMICILIO</th>
            <th>CONTRATO</th>
        </tr>
        <tr>
            <td>
                <input type="checkbox" name="documentos[]" value="poder" {{ in_array('poder', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" name="documentos[]" value="avc" {{ in_array('avc', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" name="documentos[]" value="certificado_nacimiento_asegurado" {{ in_array('certificado_nacimiento_asegurado', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" name="documentos[]" value="carnet_identidad_asegurado" {{ in_array('carnet_identidad_asegurado', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" name="documentos[]" value="certificado_matrimonio" {{ in_array('certificado_matrimonio', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" name="documentos[]" value="certificado_nacimiento_conyuge" {{ in_array('certificado_nacimiento_conyuge', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" name="documentos[]" value="carnet_identidad_conyuge" {{ in_array('carnet_identidad_conyuge', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" name="documentos[]" value="certificado_nacimiento_hijos" {{ in_array('certificado_nacimiento_hijos', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" name="documentos[]" value="carnet_identidad_hijos" {{ in_array('carnet_identidad_hijos', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" name="documentos[]" value="denuncia_enfermedad_accidente" {{ in_array('denuncia_enfermedad_accidente', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" name="documentos[]" value="croquis_domicilio" {{ in_array('croquis_domicilio', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" name="documentos[]" value="contrato" {{ in_array('contrato', $documentosSeleccionados) ? 'checked' : '' }}>
            </td>
        </tr>
    </table>
</body>
</html> --}}
