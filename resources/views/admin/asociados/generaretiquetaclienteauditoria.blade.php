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
            <th>NOMBRE</th>
            <th>ENT. GESTOR SALUD</th>
            <th>TRAMITE</th>
        </tr>
        <tr>
            <td>{{$clienteauditoria->id}}</td>
            <td>{{$clienteauditoria->nombrecompleto}}</td>
            <td>{{$clienteauditoria->entgestorsalud}}</td>
            <td>AUDITORÍA MÉDICA</td>
        </tr>
        <tr>
            <th>CIUDAD</th>
            <th>CI</th>
            <th>CELULAR</th>
            <th>MOTIVO DE CONSULTA</th>
        </tr>
        <tr>
            <td>{{$clienteauditoria->lugarresidencia}}</td>
            <td>{{$clienteauditoria->ci}}</td>
            <td>{{$clienteauditoria->celular}}</td>
            <td>{{$clienteauditoria->motivoconsulta}}</td>
        </tr>
    </table>
</body>
</html>
