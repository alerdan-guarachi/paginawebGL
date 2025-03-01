<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>REQUISITOS</title>
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
            .document-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 70px;
            }
            .document-table td {
                vertical-align: top;
                width: 50%;
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
        
        <table class="document-table">
            <tr>
                <td>
                    <h2>DOCUMENTACIÓN A PRESENTAR</h2>
                    <ul>
                        @foreach($nombreDocumentos as $key => $nombre)
                            @if(in_array($key, $documentosSeleccionados))
                                <li style="font-size: 15px; text-align: left;">{{ $nombre }}</li>
                            @endif
                        @endforeach
                    </ul>
                </td>
                <td>
                    <h2>DOCUMENTACIÓN ADICIONAL</h2>
                    <ul>
                        @foreach($nombreDocumentos2 as $key => $nombre)
                            @if(in_array($key, $documentosSeleccionados2))
                                <li style="font-size: 15px; text-align: left;">{{ $nombre }}</li>
                            @endif
                        @endforeach
                    </ul>
                </td>
            </tr>
        </table>
    </body>
</html>

