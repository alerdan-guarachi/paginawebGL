<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETIQUETA DE TRÁMITE DE {{$tramite}}</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 12px;
            background-color: #f0f0f0
        }
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th>ID CLIENTE</th>
            <th>NOMBRE</th>
            <th>EMPRESA</th>
            <th>TRAMITE</th>
        </tr>
        <tr>
            <td style="font-size: 25px;">{{$cliente->id}}</td>
            <td>{{$cliente->nombres}} {{$cliente->apepaterno}} {{$cliente->apematerno}}</td>
            <td>{{$cliente->empresa}}</td>
            <td>{{$idtramite}} - {{$tramite}}</td>
        </tr>
        <tr>
            <th>LUGAR_RESIDENCIA</th>
            <th>CI</th>
            <th>CELULAR</th>
            <th>ASEGURADORA</th>
        </tr>
        <tr>
            <td>{{$cliente->ciudadresidencia}}</td>
            <td>{{$cliente->ci}} {{$cliente->ciexp}}</td>
            <td>{{$cliente->celular}}</td>
            <td>{{$cliente->aseguradora}}</td>
        </tr>
    </table>
</body>
</html>
