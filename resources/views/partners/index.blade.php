@extends('adminlte::page')

@section('title', 'Listado de Partners')



@section('content_header')
<div class="d-flex justify-content-between align-items-center">
        <h4>Listado de Personas Registradas</h4>
        <a href="{{ route('partners.scanner') }}" class="btn btn-success">
            <i class="fas fa-qrcode"></i> Escanear QR / Registrar Asistencia
        </a>
        <a class="btn btn-sm float-right btn-primary" href="{{ route('assistances.index') }}">LISTA</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if ($partners->isEmpty())
                <div class="alert alert-info text-center">
                    No hay registros creados aún.
                </div>
            @else
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>C.I.</th>
                            <th>Categoría</th>
                            <th>QR</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($partners as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
                                <td>{{ $p->name }}</td>
                                <td>{{ $p->last_name }}</td>
                                <td>{{ $p->ci }}</td>
                                <td>{{ $p->category }}</td>
                                <td>
                                    @if ($p->code_qr && file_exists(public_path('code/' . $p->code_qr . '.png')))
                                        <img src="{{ asset('code/' . $p->code_qr . '.png') }}" width="80" height="80" alt="QR">
                                    @else
                                        <span class="text-muted">No generado</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($p->code_qr))
                                        <a href="{{ route('partners.show', ['codigo' => $p->code_qr]) }}" target="_blank" class="btn btn-primary btn-sm">
                                            Ver Detalle
                                        </a>
                                    @else
                                        <span class="text-muted">Sin QR</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    
@stop
