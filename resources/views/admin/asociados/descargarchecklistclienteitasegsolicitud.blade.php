<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF</title>
    <style>
        @page {
            size: 1100pt 612pt;
            margin-right: 50px;
            margin-left: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-size: 12px;
            width: 100%;
        }
        caption {
            caption-side: top;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .title-row th {
            vertical-align: top;
        }
        .empty-cell {
            height: 30px;
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
                    </td>
                @endif
            @endforeach
        </tr>
    </table>
    <table>
        <caption>DOCUMENTACIÓN ADICIONAL</caption>
        <tr class="title-row">
            @foreach($nombreDocumentos2 as $key => $nombre)
                @if(in_array($key, $documentosSeleccionados2))
                    <th>{{ $nombre }}</th>
                @endif
            @endforeach
        </tr>
        <tr>
            @foreach($nombreDocumentos2 as $key => $nombre)
                @if(in_array($key, $documentosSeleccionados2))
                    <td class="empty-cell">
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
        @page {
            size: 8.5in 11in;
            margin-right: 60px;
            margin-left: 60px;
        }
        .document-list {
            list-style-type: none;
            padding-left: 0;
        }
        .document-item {
            margin-bottom: 10px;
            font-size: 14px;
        }
        input[type="checkbox"] {
            transform: scale(1.5);
            margin-right: 10px;
            margin-bottom: 15px;
        }
        label {
            display: inline-block;
            vertical-align: middle;
            margin-bottom: 20px;
        }
        h3 {
            text-align: center;
        }
        h2 {
            text-align: center;
            margin-top: -10px;
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
    <h3>DOCUMENTACIÓN A PRESENTAR DE</h3>
    <h2>{{$cliente->nombrecompleto}}</h2>
    <ul class="document-list">
        @foreach ($documentosSeleccionados as $documento)
        <li>
            <input type="checkbox" name="{{ $documento }}" value="{{ $documento }}" checked>
            <label>{{ $nombreDocumentos[$documento] }}</label>
        </li>
        @endforeach
    </ul>
</body>
</html> --}}

