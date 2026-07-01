@extends('adminlte::page')
    
@section('content_header')

<h1>MODIFICACIONES DE ID PROG. DE CAJA</h1>
@stop 

@section('content')
@if (session('info'))
    <div id="alert-info" class="alert alert-success">
        <strong>{{ session('info') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 3000);
    </script>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <form method="GET" action="{{ route('admin.caja.modificaciones.modifregprogcaja') }}" class="mb-3">
                <div class="input-group">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="Buscar por Nro. Recibo"
                        value="{{ request('search') }}"
                    >
                    <button class="btn btn-secondary" type="submit">
                        Buscar
                    </button>
                </div>
            </form>
            <form method="POST" action="{{ route('admin.caja.modificaciones.modifregprogcaja.actualizar') }}">
                @csrf
                <table class="table table-striped table-bordered table-sm">
                    <thead class="table-secondary">
                        <tr>
                            <th>Selec.</th>
                            <th>ID_Reg_DetalleRecibo</th>
                            <th>Nro_Recibo</th>
                            <th style="background-color: rgb(241, 253, 242)">ID_Prog.</th>
                            <th>Cliente_ID</th>
                            <th>Cliente_Nombre</th>
                            <th>Prov_Atención</th>
                            <th>Detalle</th>
                            <th>Fecha_Atención</th>
                            <th>Tipo_Mov.</th>
                            <th>Tipo_Transac.</th>
                            <th>Subtotal</th>
                            <th>Descuento</th>
                            <th>Total</th>
                            <th>Saldo</th>
                            <th>Estado</th>
                            <th>Usuario_Registro</th>
                        </tr>
                    </thead>
                    @if (request()->filled('search') && $registros->isNotEmpty())
                        <tbody>
                            @foreach ($registros as $registro)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="seleccionados[]" value="{{ $registro->id }}" class="checkbox-seleccion">
                                    </td>
                                    <td>{{ $registro->id }}</td>
                                    <td>{{ $registro->reciboid }}</td>
                                    <td style="background-color: rgb(241, 253, 242)">{{ $registro->programacionid }}</td>
                                    <td>{{ $registro->clienteid }}</td>
                                    <td>{{ $registro->clientenombre }}</td>
                                    <td>{{ $registro->proveedoratencion }}</td>
                                    <td>{{ $registro->detalle }}</td>
                                    <td>{{ $registro->fechaatencion }}</td>
                                    <td>{{ $registro->tipomovimiento }}</td>
                                    <td>{{ $registro->tipotransaccion }}</td>
                                    <td>{{ $registro->subtotal }}</td>
                                    <td>{{ $registro->descuento }}</td>
                                    <td>{{ $registro->montototal }}</td>
                                    <td>{{ $registro->saldo }}</td>
                                    <td>{{ $registro->estado }}</td>
                                    <td>{{ $registro->usuarioregistronombre }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endif
                </table>
                @if (request()->filled('search') && $registros->isNotEmpty())
                    <div class="input-group mt-3">
                        <input
                            type="number"
                            id="nuevo_programacionid"
                            name="nuevo_programacionid"
                            class="form-control"
                            placeholder="Nuevo ID de Programación"
                            required
                        >

                        <button
                            type="submit"
                            id="btnModificar"
                            class="btn btn-success"
                            disabled>
                            MODIFICAR
                        </button>
                    </div>
                @endif
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    const btnModificar = document.getElementById('btnModificar');
                    const inputProgramacion = document.getElementById('nuevo_programacionid');

                    function validarFormulario() {

                        const seleccionado = document.querySelectorAll('.checkbox-seleccion:checked').length > 0;
                        const programacion = inputProgramacion.value.trim() !== '';

                        btnModificar.disabled = !(seleccionado && programacion);
                    }

                    // Cuando cambia cualquier checkbox
                    document.querySelectorAll('.checkbox-seleccion').forEach(chk => {
                        chk.addEventListener('change', validarFormulario);
                    });

                    // Cuando escribe el nuevo ID
                    inputProgramacion.addEventListener('input', validarFormulario);

                    validarFormulario();

                });
            </script>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {
        color:#000000; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
</style>
@stop