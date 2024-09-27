@extends('adminlte::page')

@section('content_header')
<h1>REQUISITOS POR SERVICIO</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
@stop

@section('content')
@if (session('info'))
    <div id="alert-info" class="alert alert-success">
        <strong>{{ session('info') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 5000);
    </script>
@endif
<div class="card">
    <style>
        .table-striped tbody tr:nth-child(odd) {
            background-color: #f9f9f9; /* Color de fondo para filas impares en la tabla principal */
        }
        .table td {
            line-height: 1.2; /* Ajusta este valor para el interlineado */
        }
        /* Estilo para las filas impares en el modal */
        .modal .table-striped tbody tr:nth-child(odd) {
            background-color: #f1f1f1; /* Cambia este color según tu diseño para el modal */
        }
    </style>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Servicio</th>
                    <th>Servicio</th>
                    <th>Requisitos</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedServicios as $servicionombre => $servicios)
                    <tr>
                        <td>{{ $servicios[0]->servicioid }}</td>
                        <td>{{ $servicionombre }}</td>
                        <td>
                            <abbr title="VER REQUISITOS">
                                <button type="button" class="btn btn-ver fas fa-eye" data-toggle="modal" data-target="#modal{{ $servicios[0]->servicioid }}"></button>
                            </abbr>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @foreach ($groupedServicios as $servicionombre => $servicios)
            <!-- Modal -->
            <div class="modal fade" id="modal{{ $servicios[0]->servicioid }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $servicios[0]->servicioid }}" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="modalLabel{{ $servicios[0]->servicioid }}">{{ $servicionombre }}</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Importancia</th>
                                        <th>Requisito</th>
                                        <th>Requisito Detallado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($servicios->sortBy(['importancia', 'requisito']) as $servicio)
                                        <tr>
                                            <td>{{ $servicio->id }}</td>
                                            <td>{{ $servicio->importancia }}</td>
                                            <td>{{ $servicio->requisito }}</td>
                                            <td>{{ $servicio->requisitodetallado }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@stop