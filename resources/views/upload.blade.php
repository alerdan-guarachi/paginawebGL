@extends('adminlte::page')

@section('title', 'Subir Excel')

@section('content_header')
    <h1>Subir Archivo Excel</h1>
@endsection

@section('content')
    <h1>Subir Archivo CSV</h1>

    <!-- Formulario para subir archivo -->
    <form action="{{ route('upload.excel') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="archivo" accept=".csv">
        <button type="submit">Subir</button>
    </form>

    <!-- Mostrar datos solo si existen -->
    @if (isset($datos) && count($datos) > 0)
        <h1>Datos Extraídos</h1>
        <table border="1">
            <thead>
                <tr>
                    @foreach ($encabezados as $encabezado)
                        <th>{{ $encabezado }}</th>
                    @endforeach
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
            <tfoot>
                <tr>
                    <td colspan="8"><strong>Total:</strong></td>
                    <td><strong>{{ number_format($total, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    @endif
@endsection