<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th>ID CLIENTE</th>
            <th>ID CLIENTE</th>
            <th>ID PROCESO</th>
            <th>NOMBRE</th>
            <th>EMPRESA</th>
            <th>TRAMITE</th>
        </tr>
        <tr>
            <td>{{$cliente->id}}</td>
            <td>{{$cliente->id}}</td>
            <td>{{-- {{$cliente->id}} --}}</td>
            <td>{{$cliente->nombres}} {{$cliente->apepaterno}} {{$cliente->apematerno}}</td>
            <td>{{$cliente->empresa}}</td>
            <td>{{-- {{$cliente->id}} --}}</td>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th>CIUDAD</th>
            <th>CI</th>
            <th>CELULAR</th>
            <th>AFP</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>{{$cliente->lugarnacimiento}}</td>
            <td>{{$cliente->ci}} {{$cliente->ciexp}}</td>
            <td>{{$cliente->celular}}</td>
            <td>{{$cliente->afp}}</td>
        </tr>
    </table>
</body>
</html>
