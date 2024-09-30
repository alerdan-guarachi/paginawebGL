<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Checklist del Cliente</title>
    <style>
         @page {
            size: 1100pt 612pt;
            margin-right: 50px;
            margin-left: 50px;
        }
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            font-size: 13px;
        }
        .espacio {
            height: 20px;
        }
        h3 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h3 style="margin-bottom: -12px; margin-top:-15px;">DOCUMENTACIÓN A PRESENTAR</h3>
    <table>
        <tr>
            <th>CARNET DE IDENTIDAD</th>
            <th>CERTIFICADO DE NACIMIENTO</th>
        </tr>
        <tr>
            <td class="espacio"></td>
            <td class="espacio"></td>
        </tr>
    </table>

    <h3 style="margin-bottom: -12px;">DETALLES DE PÓLIZAS</h3>
    <table>
        <tr>
            <th style="width: 30%;">BANCO</th>
            <th>NRO. PÓLIZA GENERALES</th>
            <th>PÓLIZA GENERALES</th>
            <th>DECLARACIÓN DE SALUD</th>
            <th>NRO. PÓLIZA DESGRAV.</th>
            <th>PÓLIZA DESGRAVAMEN</th>
        </tr>
        @for ($i = 1; $i <= $numPolizas; $i++)
        <tr>
            <td>{{ $request->input('banco' . $i) }}</td>
            <td>{{ $request->input('nropolizageneral' . $i) }}</td>
            <td></td>
            <td></td>
            <td>{{ $request->input('nropolizadesgravamen' . $i) }}</td>
            <td></td>
        </tr>
        @endfor
    </table>
</body>
</html>
