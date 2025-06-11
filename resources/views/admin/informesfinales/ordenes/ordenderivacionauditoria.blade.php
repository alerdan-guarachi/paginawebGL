<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DERIVACION MÉDICA</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>DERIVACIONES MÉDICAS</h2>
    <p><strong>Nombre del Cliente:</strong> {{ $clienteauditorianombre }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Médico Derivante</th>
                <th>Estudio / Especialidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registros as $registro)
                <tr>
                    <td>{{ $registro->id }}</td>
                    <td>{{ $registro->medicoderivante }}</td>
                    <td>{{ $registro->accionnombre }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
