@extends('adminlte::page')

@section('title', 'Detalle del Partner')

@section('content_header')
    <h4>Información del Partner</h4>
@stop

@section('content')
    <div class="card mx-auto shadow-sm" style="max-width: 600px;">
        <div class="card-body text-center">

            <h5 class="mb-3 text-primary"><strong>{{ $persona->name }} {{ $persona->last_name }}</strong></h5>

            <table class="table table-bordered text-center">
                <tr>
                    <th style="width: 40%;">C.I.</th>
                    <td>{{ $persona->ci }}</td>
                </tr>
                <tr>
                    <th>Categoría</th>
                    <td>{{ $persona->category }}</td>
                </tr>
                <tr>
                    <th>Fecha de Registro</th>
                    <td>{{ $persona->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>

            @if ($persona->code_qr && file_exists(public_path('code/' . $persona->code_qr . '.png')))
                <div class="mt-3">
                    <p><strong>QR asignado:</strong></p>
                    <img src="{{ asset('code/' . $persona->code_qr . '.png') }}" alt="QR" width="150">
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('partners.index') }}" class="btn btn-secondary">
                    ← Volver al listado
                </a>
            </div>

        </div>
    </div>
@stop
