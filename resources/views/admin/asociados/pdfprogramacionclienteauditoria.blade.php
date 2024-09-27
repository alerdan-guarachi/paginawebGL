<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: 8.5in 11in;
            margin: 0;
        }
        body {
            margin: 4cm 1cm 3.5cm 1cm;
            font-family: "Segoe UI";
            background: transparent;
        }
        .page-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ public_path('membrete/membreteori.jpg') }}'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
        }
        main {
            text-align: center;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        h1 {
            color: #94c93b; 
            font-weight: 900;
            font-size: 25px;
            margin-bottom: 0px;
        }
        h2 {
            color: #94c93b; 
            font-weight: 900;
            font-size: 30px;
            margin-top: 5px;
        }
        p {
            margin-top: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="page-background"></div>
    <main>
        <h1>Programación para el {{$fechabateria}} de</h1>
        <h2>{{$clienteauditoria->nombrecompleto}}</h2>
    
        <table>
            <thead>
                <tr>
                    <th>Acción</th>
                    <th>Proveedor</th>
                    <th>Fecha asignada</th>
                    <th>Hora asignada</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accionesDisponibles as $accionesDisponible)
                    <tr>
                        <td>{{$accionesDisponible->accionnombre}}</td>
                        <td>{{$accionesDisponible->proveedornombre}}</td>
                        <td>{{$accionesDisponible->fechaasignada}}</td>
                        <td>{{$accionesDisponible->horadesde}} - {{$accionesDisponible->horahasta}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
