@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<h1 style="margin-right: -80px; margin-left: -80px;">MODELOS DE CARTAS Y RECLAMOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
@stop

@section('content')
<div class="card" style="margin-right: -80px; margin-left: -80px;">
    <div class="card-body">
        <nav class="navbar float-right">
            <form class="form-inline">
                <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Carta / Reclamo" aria-label="Search">
                <button class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
            </form>
        </nav>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo de Carta</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mdoelocartasreclamos as $modelocartasreclamo)
                        <tr>
                            <td>{{ $modelocartasreclamo->id }}</td>
                            <td>{{ $modelocartasreclamo->tipocarta }}</td>
                            <td>
                                @if ($modelocartasreclamo->estado == 'ACTIVO')
                                    <span class="badge badge-success">{{ $modelocartasreclamo->estado }}</span>
                                @else
                                    <span class="badge badge-danger">{{ $modelocartasreclamo->estado }}</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-editar" data-toggle="modal" data-target="#modalVista_{{ $modelocartasreclamo->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalVista_{{ $modelocartasreclamo->id }}" tabindex="-1" role="dialog" aria-labelledby="modalVistaLabel_{{ $modelocartasreclamo->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" style="color: black; font-weight: bold;" id="modalVistaLabel_{{ $modelocartasreclamo->id }}">MODELO DE {{ $modelocartasreclamo->tipocarta }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="pdf-container">
                                            @include($modelocartasreclamo->document)
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                            .pdf-container {
                                height: 600px;
                                overflow: auto;
                                border: 1px solid #ddd;
                                border-radius: 5px;
                                background-color: #ffffff;
                                padding: 20px;
                                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                                position: relative;
                            }
                            .pdf-container::before {
                                content: "";
                                position: absolute;
                                top: 0;
                                left: 0;
                                right: 0;
                                bottom: 0;
                                opacity: 0.1;
                                pointer-events: none;
                            }
                        </style>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop