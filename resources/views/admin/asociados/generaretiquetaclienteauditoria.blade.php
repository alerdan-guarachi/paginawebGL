<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETIQUETA DE TRÁMITE DE AUDITORIA MÉDICA</title>
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
            <th>TRAMITE</th>
        </tr>
        <tr>
            <td style="font-size: 25px;">{{$clienteauditoria->id}}</td>
            <td>{{$clienteauditoria->nombrecompleto}}</td>
            <td>AUDITORÍA MÉDICA</td>
        </tr>
        <tr>
            <th>LUGAR DE RESIDENCIA</th>
            <th>CI</th>
            <th>CELULAR</th>
        </tr>
        <tr>
            <td>{{$clienteauditoria->lugarresidencia}}</td>
            <td>{{$clienteauditoria->ci}}</td>
            <td>{{$clienteauditoria->celular}}</td>
        </tr>
    </table>
</body>
</html>
