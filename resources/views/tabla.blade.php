@extends('adminlte::page')

@section('title', 'Subir Excel')

@section('content_header')
    <h1>Subir Archivo Excel</h1>
@endsection


@section('content')
<body>
    <h1>Datos Extraídos</h1>
    <table border="1">
        <thead>
            <tr>
                <th>COLUMNA</th>
                <th>COLUMNA</th>
                <th>COLUMNA</th>
                <th>COLUMNA</th>
                <th>COLUMNA</th>
                <th>COLUMNA</th>
                <th>COLUMNA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datos as $fila)
                <tr>
                    @foreach ($fila as $dato)
                        <td>{{ $dato }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
@endsection
